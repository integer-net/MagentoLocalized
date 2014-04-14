<?php
/**
 * Localized Magento Editions
 *
 * @category   IntegerNet
 * @package    IntegerNet_MagentoLocalized
 * @copyright  Copyright (c) 2014 integer_net GmbH (http://www.integer-net.de/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */

class IntegerNet_MagentoLocalized_Model_Installer
{
    protected $_composerConfiguration = array();

    public function __construct()
    {
        $this->_autoloadMagentoComposerClass('\MagentoHackathon\Composer\Magento\Parser');
        $this->_autoloadMagentoComposerClass('\MagentoHackathon\Composer\Magento\PathTranslationParser');
        $this->_autoloadMagentoComposerClass('\MagentoHackathon\Composer\Magento\Deploystrategy\DeploystrategyAbstract');
        $this->_autoloadMagentoComposerClass('\MagentoHackathon\Composer\Magento\Deploystrategy\Copy');
        $this->_autoloadMagentoComposerClass('\MagentoHackathon\Composer\Magento\Parser');
        $this->_autoloadMagentoComposerClass('\MagentoHackathon\Composer\Magento\MapParser');
        $this->_autoloadMagentoComposerClass('\MagentoHackathon\Composer\Magento\PackageXmlParser');
        $this->_autoloadMagentoComposerClass('\MagentoHackathon\Composer\Magento\ModmanParser');
    }

    /**
     * install main localization module and locale package
     *
     * @param string $editionCode i.e. de, at, ch, ru
     */
    public function installEditionModules($editionCode)
    {
        if (strpos($editionCode, 'ebay') === 0) {
            $ebayPackageName = Mage::getSingleton('install/config')->getNode('magento_localized/ebay_edition/module_package');
            $this->installPackageByName($ebayPackageName);
            $editionCode = substr($editionCode, 5);
        }

        $localizedModulePackageName = Mage::getSingleton('install/config')->getNode('magento_localized/editions/' . $editionCode . '/module_package');
        if ($localizedModulePackageName) {
            $this->installPackageByName($localizedModulePackageName);
        } else {
            Mage::throwException(Mage::helper('magento_localized')->__('Localized Package for code "%s" not set', $editionCode));
        }

        $localePackageName = Mage::getSingleton('install/config')->getNode('magento_localized/editions/' . $editionCode . '/locale_package');
        if ($localePackageName) {
            $this->installPackageByName($localePackageName);
        }

        $localeFallbackPackageName = Mage::getSingleton('install/config')->getNode('magento_localized/editions/' . $editionCode . '/locale_fallback_package');
        if ($localeFallbackPackageName) {
            $this->installPackageByName('magento-hackathon/localefallback');
            $this->installPackageByName($localeFallbackPackageName);
        }

        $this->installPackageByName('firegento/magesetup');

        $this->_cleanCache();
    }

    public function updateInstalledModules()
    {
        foreach(Mage::getStoreConfig('magento_localized/installed_modules') as $namespace => $moduleConfig) {
            foreach($moduleConfig as $moduleName => $reference) {
                $packageName= $namespace . '/' . $moduleName;
                if ($this->installPackageByName($packageName)) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('magento_localized')->__('Successfully updated module %s.', $packageName)
                    );
                    Mage::log(Mage::helper('magento_localized')->__('Successfully updated module %s.', $packageName));
                }
            }
        }
    }

    public function installEbayEditionModules()
    {
        $this->installPackageByName(Mage::getSingleton('install/config')->getNode('magento_localized/ebay_edition/module_package'));

        $this->_cleanCache();
    }

    /**
     * Install / update a package by its identifier
     *
     * @param string $packageName
     * @param boolean $force
     * @return boolean
     */
    public function installPackageByName($packageName, $force = false)
    {
        $composerConfiguration = $this->_getComposerConfiguration();

        foreach ($composerConfiguration['packages'] as $packageConfiguration) {
            if (strtolower($packageConfiguration['name']) == strtolower($packageName)) {
                if (isset($packageConfiguration['source']['reference']) && !is_null($packageConfiguration['source']['reference'])) {
                    $reference = $packageConfiguration['source']['reference'];
                } else if (isset($packageConfiguration['dist']['reference']) && !is_null($packageConfiguration['dist']['reference'])) {
                    $reference = $packageConfiguration['dist']['reference'];
                } else {
                    $reference = $packageConfiguration['version'];
                }

                if (!$force && $reference == Mage::getStoreConfig('magento_localized/installed_modules/' . strtolower($packageName))) {
                    // same version as before
                    return false;
                }

                $this->installPackageByConfiguration($packageConfiguration);

                $this->_setConfigData('magento_localized/installed_modules/' . strtolower($packageName), $reference);
                return true;
            }
        }
    }

    /**
     * @param array $packageConfiguration
     */
    public function installPackageByConfiguration($packageConfiguration)
    {
        $sourceDir = $this->getSourceDir($packageConfiguration);
        $targetDir = Mage::getBaseDir();
        $strategy = new \MagentoHackathon\Composer\Magento\Deploystrategy\Copy($sourceDir, $targetDir);
        $strategy->setIsForced(true);
        $strategy->setMappings($this->getParser($packageConfiguration)->getMappings());
        $strategy->deploy();
    }

    protected function _autoloadMagentoComposerClass($className)
    {
        $baseMagentoComposerPath = Mage::getBaseDir() . DS . 'vendor' . DS . 'magento-hackathon' . DS . 'magento-composer-installer' . DS . 'src';
        $filename = $baseMagentoComposerPath . str_replace('\\', DS, $className) . '.php';
        require_once($filename);
    }

    /**
     * Returns a parser for the vendor dir
     *
     * @param array $packageConfiguration
     * @return MagentoHackathon\Composer\Magento\Parser
     * @throws Mage_Core_Exception
     */
    public function getParser($packageConfiguration)
    {
        $extra = (isset($packageConfiguration['extra']) ? $packageConfiguration['extra'] : array());

        if (isset($extra['map'])) {
            $parser = new MagentoHackathon\Composer\Magento\MapParser($extra['map']);
            return $parser;
        } elseif (isset($extra['package-xml'])) {
            $parser = new MagentoHackathon\Composer\Magento\PackageXmlParser($this->getSourceDir($packageConfiguration), $extra['package-xml']);
            return $parser;
        } elseif (file_exists($this->getSourceDir($packageConfiguration) . DS . 'modman')) {
            $parser = new MagentoHackathon\Composer\Magento\ModmanParser($this->getSourceDir($packageConfiguration));
            return $parser;
        } else {
            Mage::throwException('Unable to find deploy strategy for module: no known mapping');
        }
    }

    public function getSourceDir($packageConfiguration)
    {
        return Mage::getBaseDir() . DS . 'vendor' . DS . $packageConfiguration['name'];
    }

    /**
     * @return array
     */
    protected function _getComposerConfiguration()
    {
        if (!sizeof($this->_composerConfiguration)) {
            $this->_composerConfiguration = Zend_Json::decode(file_get_contents(Mage::getBaseDir() . DS . 'composer.lock'));
        }

        return $this->_composerConfiguration;
    }

    /**
     * Set configuration data
     *
     * @param string $key
     * @param string|int $value
     * @param string $scope
     * @param int $scopeId
     */
    protected function _setConfigData($key, $value, $scope = 'default', $scopeId = 0)
    {
        $setup = Mage::getModel('eav/entity_setup', 'core_setup');
        if ($setup->getConnection()) {
            $setup->setConfigData($key, $value, $scope, $scopeId);
        } else {
            // store config data temporarily in session until a database connection exists
            $installConfigData = Mage::getSingleton('install/session')->getInstallConfigData();
            if (!is_array($installConfigData)) {
                $installConfigData = array();
            }
            $installConfigData[$key] = $value;
            Mage::getSingleton('install/session')->setInstallConfigData($installConfigData);
        }
    }

    protected function _cleanCache()
    {
        Mage::app()->cleanCache();
    }
}