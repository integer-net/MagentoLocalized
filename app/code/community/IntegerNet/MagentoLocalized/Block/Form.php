<?php
/**
 * Localized Magento Editions
 *
 * @category   IntegerNet
 * @package    IntegerNet_MagentoLocalized
 * @copyright  Copyright (c) 2013 integer_net GmbH (http://www.integer-net.de/)
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

    public function getModulesData()
    {
        $modulesData = array();
        foreach(Mage::getStoreConfig('magento_localized/modules') as $moduleIdentifier => $module) {
            $modulesData[$moduleIdentifier] = (array) $module;
        }
        return $modulesData;
    }

    public function isInitialized()
    {
        return Mage::getStoreConfigFlag('magento_localized/is_initialized');
    }
}
