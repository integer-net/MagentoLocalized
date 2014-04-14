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
class IntegerNet_MagentoLocalized_Model_Observer
{
    /**
     * Store installed modules data to database
     *
     * @param Varien_Event_Observer $observer
     * @event controller_action_predispatch_install_wizard_end
     */
    public function predispatchInstallWizardEnd($observer)
    {
        $installConfigData = Mage::getSingleton('install/session')->getInstallConfigData();
        if (is_array($installConfigData)) {
            foreach($installConfigData as $path => $value) {
                $this->_setConfigData($path, $value);
            }
        }
        $this->predispatchInstallWizard($observer);
    }

    /**
     * Set theme to "magento_localized" with fallback to "default"
     *
     * @param Varien_Event_Observer $observer
     * @event controller_action_predispatch_install_wizard_begin
     * @event controller_action_predispatch_install_wizard_locale
     * @event controller_action_predispatch_install_wizard_config
     * @event controller_action_predispatch_install_wizard_administrator
     */
    public function predispatchInstallWizard($observer)
    {
        Mage::getDesign()->setTheme('magento_localized');
        Mage::getSingleton('install/session')->setTimezone(Mage::getStoreConfig('magento_localized/timezone'));
        Mage::getSingleton('install/session')->setCurrency(Mage::getStoreConfig('magento_localized/currency'));
        Mage::getSingleton('install/session')->setInstallGuideUrl($this->_getInstallGuideUrl());
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
        if (!Mage::getSingleton('install/session')->getIsCountrySelected() && $edition = $this->_getEdition()) {

            try {
                Mage::getSingleton('magento_localized/installer')->installEditionModules($edition);
                Mage::getSingleton('install/session')->setIsCountrySelected(1);

                $controller = $observer->getControllerAction();
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/*'));
                $controller->getResponse()->sendResponse();
                $controller->getRequest()->setDispatched(true);

            } catch (Exception $e) {
                Mage::getSingleton('install/session')->addError(Mage::helper('magento_localized')->__('An error occured. Please provide write access to the whole Magento directory and all subdirectories.'));
                Mage::getSingleton('install/session')->addError($e->getMessage());
            }
        }

        Mage::getDesign()->setTheme('magento_localized');
        Mage::getSingleton('install/session')->setTimezone(Mage::getStoreConfig('magento_localized/timezone'));
        Mage::getSingleton('install/session')->setCurrency(Mage::getStoreConfig('magento_localized/currency'));
        Mage::getSingleton('install/session')->setInstallGuideUrl($this->_getInstallGuideUrl());
    }

    /**
     * @return string
     */
    protected function _getEdition()
    {
        foreach((array)Mage::getSingleton('install/config')->getNode('magento_localized/editions') as $editionCode => $editionData) {
            if (file_exists(Mage::getModuleDir('etc', 'IntegerNet_MagentoLocalized') . DS . 'magento-ebay-' . $editionCode . '.txt')) {
                return 'ebay-' . $editionCode;
            }
            if (file_exists(Mage::getModuleDir('etc', 'IntegerNet_MagentoLocalized') . DS . 'magento-' . $editionCode . '.txt')) {
                return 'ebay-' . $editionCode;
            }
        }

        return '';
    }

    /**
     * Get language dependant URL of magento_localized logo
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
                ->createBlock('core/template', 'copyright')
                ->setTemplate('magento_localized/copyright.phtml')
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

    public function onAdminUserLoginSuccess(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfig('magento_localized/is_initialized')) {
            Mage::getSingleton('magento_localized/installer')->updateInstalledModules();
        }
    }

    /**
     * Set configuration data
     *
     * @param string $key
     * @param string|int $value
     * @param string $scope
     * @param int $scopeId
     */
    protected function _setConfigData($key, $value, $scope = 'default', $scopeId = 0)
    {
        $setup = Mage::getModel('eav/entity_setup', 'core_setup');
        if ($setup->getConnection()) {
            $setup->setConfigData($key, $value, $scope, $scopeId);
        }
    }

    /**
     * @return mixed
     */
    protected function _getInstallGuideUrl()
    {
        $localeCode = Mage::getSingleton('install/session')->getLocale();
        if (!$localeCode) {
            $localeData = Mage::getSingleton('install/session')->getLocaleData();
            $localeCode = $localeData['locale'];
            if (!$localeCode) {
                $localeCode = Mage::getStoreConfig('magento_localized/default_language');
            }
        }
        $languageCode = current(explode('_', $localeCode));

        $installGuideUrl = Mage::getStoreConfig('magento_localized/install_guide_url/' . $languageCode);

        if ($installGuideUrl) {
            return $installGuideUrl;
        }

        return Mage::getStoreConfig('magento_localized/install_guide_url/default');
    }
}