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
        return $this->getSkinUrl(Mage::getStoreConfig('magento_localized/logo_filename'));
    }

    /**
     * @return string
     */
    public function getLogoAlt()
    {
        return Mage::helper('magento_localized')->__(Mage::getStoreConfig('magento_localized/module_title'));
    }
}
