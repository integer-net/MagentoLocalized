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
        return array(
            array(
                'type' => 'text',
                'name' => 'general__imprint__shop_name',
                'label' => Mage::helper('magento_localized')->__('Shop Name'),
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__company_first',
                'label' => Mage::helper('magento_localized')->__('Company 1'),
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__company_second',
                'label' => Mage::helper('magento_localized')->__('Company 2'),
                'optional' => true,
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__street',
                'label' => Mage::helper('magento_localized')->__('Street'),
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__zip',
                'label' => Mage::helper('magento_localized')->__('Zip'),
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__city',
                'label' => Mage::helper('magento_localized')->__('City'),
            ),
            array(
                'type' => 'select',
                'source_model' => 'adminhtml/system_config_source_country',
                'name' => 'general__imprint__country',
                'label' => Mage::helper('adminhtml')->__('Country'),
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__telephone',
                'label' => Mage::helper('magento_localized')->__('Telephone'),
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__fax',
                'label' => Mage::helper('magento_localized')->__('Fax'),
                'optional' => true,
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__email',
                'label' => Mage::helper('magento_localized')->__('E-Mail'),
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__web',
                'label' => Mage::helper('magento_localized')->__('Website'),
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__vat_id',
                'label' => Mage::helper('magento_localized')->__('VAT-ID'),
                'optional' => true,
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__tax_number',
                'label' => Mage::helper('magento_localized')->__('Tax number'),
                'optional' => true,
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__court',
                'label' => Mage::helper('magento_localized')->__('Register court'),
                'optional' => true,
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__financial_office',
                'label' => Mage::helper('magento_localized')->__('Financial office'),
                'optional' => true,
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__ceo',
                'label' => Mage::helper('magento_localized')->__('CEO'),
                'optional' => true,
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__owner',
                'label' => Mage::helper('magento_localized')->__('Owner'),
                'optional' => true,
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__register_number',
                'label' => Mage::helper('magento_localized')->__('Register number'),
                'optional' => true,
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__bank_account_owner',
                'label' => Mage::helper('magento_localized')->__('Account owner'),
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__bank_account',
                'label' => Mage::helper('magento_localized')->__('Account'),
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__bank_code_number',
                'label' => Mage::helper('magento_localized')->__('Bank number'),
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__bank_name',
                'label' => Mage::helper('magento_localized')->__('Bank name'),
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__swift',
                'label' => Mage::helper('magento_localized')->__('SWIFT'),
            ),
            array(
                'type' => 'text',
                'name' => 'general__imprint__iban',
                'label' => Mage::helper('magento_localized')->__('IBAN'),
            ),
        );
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
