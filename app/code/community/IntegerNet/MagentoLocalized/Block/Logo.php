<?php

class IntegerNet_MagentoLocalized_Block_Logo extends Mage_Adminhtml_Block_Template
{
    /**
     * @return bool
     */
    public function isActive()
    {
        return Mage::getStoreConfigFlag('admin/magento_localized/display_logo');
    }

    /**
     * @return string
     */
    public function getLinkUrl()
    {
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        if (strpos($localeCode, 'de_') === 0) {
            return Mage::getStoreConfig('magento_localized/magentode_url_de');
        } else {
            return Mage::getStoreConfig('magento_localized/magentode_url_en');
        }
    }

    /**
     * Get language dependant URL of magento_localized logo
     *
     * @return string
     */
    public function getLogoUrl()
    {
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        if (strpos($localeCode, 'de_') === 0) {
            return $this->getSkinUrl('magento_localized/logo-magento_localized-de.png');
        } else {
            return $this->getSkinUrl('magento_localized/logo-magento_localized-de.png');
        }
    }

    /**
     * @return string
     */
    public function getLogoAlt()
    {
        return Mage::helper('magento_localized')->__(Mage::getStoreConfig('magento_localized/module_title'));
    }
}
