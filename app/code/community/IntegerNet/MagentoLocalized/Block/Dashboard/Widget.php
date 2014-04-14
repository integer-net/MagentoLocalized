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
        if (strpos($localeCode, Mage::getStoreConfig('magento_localized/iframe_main_language_code')) === 0) {
            return Mage::getStoreConfig('magento_localized/iframe_main_language_code');
        } else {
            return Mage::getStoreConfig('magento_localized/iframe_fallback_language_code');
        }
    }
}