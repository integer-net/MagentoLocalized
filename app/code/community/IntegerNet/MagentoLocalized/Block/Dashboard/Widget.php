<?php

class IntegerNet_MagentoLocalized_Block_Dashboard_Widget extends Mage_Adminhtml_Block_Template
{
    public function displayWidget()
    {
        return Mage::getStoreConfigFlag('admin/magento_localized/display_dashboard_block');
    }

    public function getIframeUrl()
    {
        $iframeUrl = Mage::getStoreConfig('magento_localized/iframe_url_prefix')
            . $this->_getLanguageUrlPart()
            . '/dashboard-widget'
            . Mage::getStoreConfig('magento_localized/iframe_url_suffix');

        return $iframeUrl;
    }

    /**
     * @return string
     */
    protected function _getLanguageUrlPart()
    {
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        if (strpos($localeCode, 'de_') === 0) {
            return 'de';
        } else {
            return 'en';
        }
    }
}