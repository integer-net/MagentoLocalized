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
class IntegerNet_MagentoLocalized_Block_Form extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTitle(Mage::helper('magento_localized') ->__(Mage::getStoreConfig('magento_localized/module_title')));
    }

    /**
     * Retrieve the POST URL for the form
     *
     * @return string URL
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/save');
    }

    /**
     * @return array
     */
    public function getInputFields()
    {
        $inputFields = array();
        foreach(Mage::getStoreConfig('magento_localized/form_fields') as $fieldCode => $fieldData) {
            $inputFields[$fieldData['sorting']] = array(
                'type' => $fieldData['type'],
                'source_model' => isset($fieldData['source_model']) ? $fieldData['source_model'] : null,
                'name' => $fieldCode,
                'label' => Mage::helper('magesetup')->__($fieldData['label']),
                'optional' => isset($fieldData['optional']) ? (boolean)$fieldData['optional'] : null,
            );
        }

        ksort($inputFields);
        return $inputFields;
    }

    /**
     * @param string $fieldname
     * @return mixed
     */
    public function getValue($fieldname)
    {
        $fieldCode = implode('/', explode('__', $fieldname));

        return Mage::getStoreConfig($fieldCode);
    }

    /**
     * @return array
     */
    public function getAdditionalLanguages()
    {
        return Mage::getStoreConfig('magento_localized/available_languages');
    }

    /**
     * @return array
     */
    public function getModulesData()
    {
        $modulesData = array();
        foreach(Mage::getStoreConfig('magento_localized/modules') as $moduleIdentifier => $module) {
            $modulesData[$moduleIdentifier] = (array) $module;
        }
        return $modulesData;
    }

    public function isModuleInstalled($packageName)
    {
        return (boolean)Mage::getStoreConfig('magento_localized/installed_modules/' . strtolower($packageName));
    }

    /**
     * @return bool
     */
    public function isInitialized()
    {
        return Mage::getStoreConfigFlag('magento_localized/is_initialized');
    }

    /**
     * Create buttonn and return its html
     *
     * @param string $label
     * @param string $onclick
     * @param string $class
     * @param string $id
     * @return string
     */
    public function getButtonHtml($label, $onclick, $class='', $id=null) {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => $label,
                'title'     => strip_tags(str_replace('<br />', ' - ', $label)),
                'onclick'   => $onclick,
                'class'     => $class,
                'type'      => 'button',
                'id'        => $id,
            ))
            ->toHtml();
    }

    /**
     * @return string
     */
    public function getPrivacyLinkUrl()
    {
        $url = Mage::getStoreConfig('magento_localized/iframe_url_prefix')
            . $this->_getLanguageUrlPart()
            . '/privacy'
            . Mage::getStoreConfig('magento_localized/iframe_url_suffix');

        return $url;
    }

    /**
     * @return string
     */
    protected function _getLanguageUrlPart()
    {
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        if (strpos($localeCode, Mage::getStoreConfig('magento_localized/iframe_main_language_code')) === 0) {
            return Mage::getStoreConfig('magento_localized/iframe_main_language_code');
        } else {
            return Mage::getStoreConfig('magento_localized/iframe_fallback_language_code');
        }
    }
}
