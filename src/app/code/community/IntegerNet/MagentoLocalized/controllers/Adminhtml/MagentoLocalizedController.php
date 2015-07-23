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

class IntegerNet_MagentoLocalized_Adminhtml_MagentoLocalizedController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        /** @var $helper IntegerNet_MagentoLocalized_Helper_Data */
        $helper = Mage::helper('magento_localized');

        $this->_title($helper->__(Mage::getStoreConfig('magento_localized/module_title')))
            ->_title($helper->__('Dashboard'));

        $this->loadLayout()
            ->_setActiveMenu('magento_localized/dashboard')
            ->_addBreadcrumb($helper->__('Dashboard'), $helper->__('Dashboard'));

        $iframeUrl = Mage::getStoreConfig('magento_localized/iframe_url_prefix')
            . $this->_getLanguageUrlPart()
            . '/dashboard'
            . Mage::getStoreConfig('magento_localized/iframe_url_suffix');

        $this->getLayout()
            ->getBlock('content')
            ->append(
                $this->getLayout()
                    ->createBlock('adminhtml/template')
                    ->setTemplate('magento_localized/iframe.phtml')
                    ->setIframeUrl($iframeUrl)
            );

        $this->renderLayout();
    }

    public function partnerAction()
    {
        /** @var $helper IntegerNet_MagentoLocalized_Helper_Data */
        $helper = Mage::helper('magento_localized');

        $this->_title($helper->__(Mage::getStoreConfig('magento_localized/module_title')))
            ->_title($helper->__('Partners'));

        $this->loadLayout()
            ->_setActiveMenu('magento_localized/partner')
            ->_addBreadcrumb($helper->__('Partners'), $helper->__('Partners'));

        $iframeUrl = Mage::getStoreConfig('magento_localized/iframe_url_prefix')
            . $this->_getLanguageUrlPart()
            . '/exclusive-partners'
            . Mage::getStoreConfig('magento_localized/iframe_url_suffix');
        $this->getLayout()
            ->getBlock('content')
            ->append(
                $this->getLayout()
                    ->createBlock('adminhtml/template')
                    ->setTemplate('magento_localized/iframe.phtml')
                    ->setIframeUrl($iframeUrl)
            );

        $this->renderLayout();
    }

    public function supportAction()
    {
        /** @var $helper IntegerNet_MagentoLocalized_Helper_Data */
        $helper = Mage::helper('magento_localized');

        $this->_title($helper->__(Mage::getStoreConfig('magento_localized/module_title')))
            ->_title($helper->__('Support Center'));

        $this->loadLayout()
            ->_setActiveMenu('magento_localized/support')
            ->_addBreadcrumb($helper->__('Support Center'), $helper->__('Support Center'));

        $iframeUrl = Mage::getStoreConfig('magento_localized/iframe_url_prefix')
            . $this->_getLanguageUrlPart()
            . '/support-center'
            . Mage::getStoreConfig('magento_localized/iframe_url_suffix');
        $this->getLayout()
            ->getBlock('content')
            ->append(
                $this->getLayout()
                    ->createBlock('adminhtml/template')
                    ->setTemplate('magento_localized/iframe.phtml')
                    ->setIframeUrl($iframeUrl)
            );

        $this->renderLayout();
    }

    public function suggestionsAction()
    {
        /** @var $helper IntegerNet_MagentoLocalized_Helper_Data */
        $helper = Mage::helper('magento_localized');

        $this->_title($helper->__(Mage::getStoreConfig('magento_localized/module_title')))
            ->_title($helper->__('Suggestions'));

        $this->loadLayout()
            ->_setActiveMenu('magento_localized/dashboard/suggestions')
            ->_addBreadcrumb($helper->__('Suggestions'), $helper->__('Suggestions'));

        $iframeUrl = Mage::getStoreConfig('magento_localized/iframe_url_prefix')
            . $this->_getLanguageUrlPart()
            . '/suggestions'
            . Mage::getStoreConfig('magento_localized/iframe_url_suffix');
        $this->getLayout()
            ->getBlock('content')
            ->append(
                $this->getLayout()
                    ->createBlock('adminhtml/template')
                    ->setTemplate('magento_localized/iframe.phtml')
                    ->setIframeUrl($iframeUrl)
            );

        $this->renderLayout();
    }

    /**
     * Basic action: setup form
     *
     * @return void
     */
    public function formAction()
    {
        /** @var $helper IntegerNet_MagentoLocalized_Helper_Data */
        $helper = Mage::helper('magento_localized');

        $this->_title($helper->__('System'))
            ->_title($helper->__(Mage::getStoreConfig('magento_localized/module_title')));

        if (Mage::getStoreConfigFlag('admin/magento_localized/display_menu')) {
            $this->loadLayout()
                ->_addBreadcrumb($helper->__(Mage::getStoreConfig('magento_localized/module_title')), $helper->__(Mage::getStoreConfig('magento_localized/module_title')))
                ->_setActiveMenu('magento_localized/configuration');
        } else {
            $this->loadLayout()
                ->_addBreadcrumb($helper->__(Mage::getStoreConfig('magento_localized/module_title')), $helper->__(Mage::getStoreConfig('magento_localized/module_title')))
                ->_setActiveMenu('system/magento_localized');
        }

        $this->renderLayout();
    }

    /**
     * Basic action: setup save action
     *
     * @return void
     */
    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {
            
            $params = $this->getRequest()->getParams();

            /** @var $setup IntegerNet_MagentoLocalized_Model_Setup */
            $setup = Mage::getSingleton('magento_localized/setup');
            $setup->setup($params);

            foreach($setup->getErrorMessages() as $message) {
                $this->_getSession()->addError($message);
            }
            foreach($setup->getWarningMessages() as $message) {
                $this->_getSession()->addWarning($message);
            }
            foreach($setup->getNoticeMessages() as $message) {
                $this->_getSession()->addNotice($message);
            }
            foreach($setup->getSuccessMessages() as $message) {
                $this->_getSession()->addSuccess($message);
            }
        }

        $this->_redirect('');
    }

    public function newsAction()
    {
        $this->_forward('index', 'notification');
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

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/magento_localized') ||  Mage::getSingleton('admin/session')->isAllowed('magento_localized');
    }
}
