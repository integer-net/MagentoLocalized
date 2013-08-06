<?php
class IntegerNet_MagentoLocalized_Block_Frontend_Copyright extends Mage_Core_Block_Template
{
    public function getMagentoDEUrl()
    {
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        if (strpos($localeCode, 'de_') === 0) {
            return Mage::getStoreConfig('magento_localized/magentode_url_de');
        } else {
            return Mage::getStoreConfig('magento_localized/magentode_url_en');
        }
    }

    public function getAppFactoryUrl()
    {
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        if (strpos($localeCode, 'de_') === 0) {
            return Mage::getStoreConfig('magento_localized/appfactory_url_de');
        } else {
            return Mage::getStoreConfig('magento_localized/appfactory_url_en');
        }
    }

    public function getIntegerNetUrl()
    {
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        if (strpos($localeCode, 'de_') === 0) {
            return Mage::getStoreConfig('magento_localized/integernet_url_de');
        } else {
            return Mage::getStoreConfig('magento_localized/integernet_url_en');
        }
    }

    public function getLimeSodaUrl()
    {
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        if (strpos($localeCode, 'de_') === 0) {
            return Mage::getStoreConfig('magento_localized/limesoda_url_de');
        } else {
            return Mage::getStoreConfig('magento_localized/limesoda_url_en');
        }
    }

    public function getOpenstreamUrl()
    {
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        if (strpos($localeCode, 'de_') === 0) {
            return Mage::getStoreConfig('magento_localized/openstream_url_de');
        } else {
            return Mage::getStoreConfig('magento_localized/openstream_url_en');
        }
    }
}