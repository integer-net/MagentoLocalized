<?php
/**
 * Localized Magento Editions
 *
 * @category   IntegerNet
 * @package    IntegerNet_MagentoLocalized
 * @copyright  Copyright (c) 2013 integer_net GmbH (http://www.integer-net.de/)
 * @license    http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */

class IntegerNet_MagentoLocalized_Model_Installer
{
    protected $_composerConfiguration = array();

    public function __construct()
    {
        $this->_autoloadMagentoComposerClass('\MagentoHackathon\Composer\Magento\Deploystrategy\DeploystrategyAbstract');
        $this->_autoloadMagentoComposerClass('\MagentoHackathon\Composer\Magento\Deploystrategy\Copy');
        $this->_autoloadMagentoComposerClass('\MagentoHackathon\Composer\Magento\Parser');
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

        Mage::app()->cleanCache();
        Mage::getSingleton('install/session')->setTimezone(Mage::getStoreConfig('magento_localized/timezone'));
        Mage::getSingleton('install/session')->setCurrency(Mage::getStoreConfig('magento_localized/currency'));

    }

    /**
     * @param string $localePackageName
     */
    public function installPackageByName($localePackageName)
    {
        $composerConfiguration = $this->_getComposerConfiguration();

        foreach ($composerConfiguration['packages'] as $packageConfiguration) {
            if (strtolower($packageConfiguration['name']) == strtolower($localePackageName)) {
                $this->installPackageByConfiguration($packageConfiguration);
            }
        }
    }

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
        $filename = $baseMagentoComposerPath . str_replace('/', DS, $className) . '.php';
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
}