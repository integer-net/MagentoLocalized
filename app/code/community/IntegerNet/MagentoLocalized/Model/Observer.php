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
class IntegerNet_MagentoLocalized_Model_Observer
{
    /**
     * Set theme to "magento_localized" with fallback to "default"
     *
     * @param Varien_Event_Observer $observer
     * @event controller_action_predispatch_install_wizard_begin
     * @event controller_action_predispatch_install_wizard_locale
     * @event controller_action_predispatch_install_wizard_config
     * @event controller_action_predispatch_install_wizard_administrator
     * @event controller_action_predispatch_install_wizard_end
     */
    public function predispatchInstallWizard($observer)
    {
        Mage::getDesign()->setTheme('magento_localized');
    }

    /**
     * Set default Timezone and Currency
     *
     * @param Varien_Event_Observer $observer
     * @event controller_action_predispatch_install_wizard_index
     * @event controller_action_predispatch_install_index_index
     */
    public function predispatchInstallStart($observer)
    {
        Mage::getSingleton('install/session')->setTimezone(Mage::getStoreConfig('magento_localized/timezone'));
        Mage::getSingleton('install/session')->setCurrency(Mage::getStoreConfig('magento_localized/currency'));
    }

    /**
     * Get language dependant URL of germanstoreconfig logo
     *
     * @param Mage_Adminhtml_Block_Page_Header $block
     * @return string
     */
    protected function _getLogoUrl($block)
    {
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        if (strpos($localeCode, 'de_') === 0) {
            return $block->getSkinUrl('images/magento-edition-deutschland.gif');
        } else {
            return $block->getSkinUrl('images/magento-edition-deutschland.gif');
        }
    }

    /**
     * Add copyright notice to end of imprint cms page
     *
     * @param Varien_Event_Observer $observer
     * @event cms_page_load_after
     */
    public function afterLoadCmsPage(Varien_Event_Observer $observer)
    {
        /** @var $page Mage_Cms_Model_Page */
        $page = $observer->getObject();
        if ($page->getIdentifier() == 'impressum' && Mage::getStoreConfigFlag('general/imprint/display_copyright')) {
            $copyrightHtml = Mage::app()->getLayout()
                ->createBlock('germanstoreconfig/frontend_copyright', 'copyright')
                ->setTemplate('germanstoreconfig/copyright.phtml')
                ->toHtml();
            $page->setContent($page->getContent() . $copyrightHtml);
        }
    }

    /**
     * Add additional text to checkout review page if "cash on delivery" payment method is selected
     *
     * @param Varien_Event_Observer $observer
     * @event checkout_additional_information
     */
    public function addCheckoutAdditionalInformation(Varien_Event_Observer $observer)
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote->getPayment()->getMethod() == 'cashondelivery') {

            $customText = $quote->getPayment()->getMethodInstance()->getCustomText();

            if ($customText) {
                $additionalObject = $observer->getAdditional();
                $text = (string)$additionalObject->getText();

                if ($text) {
                    $text .= '<br />';
                }

                $text .= $customText;
                $additionalObject->setText($text);
            }
        }
    }
}