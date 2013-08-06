<?php

class IntegerNet_MagentoLocalized_Block_Form extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('magento_localized/form.phtml');
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
                'label' => $this->__($fieldData['label']),
                'optional' => isset($fieldData['optional']) ? (boolean)$fieldData['optional'] : null,
            );
        }
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
}
