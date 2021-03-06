<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_MagentoLocalized
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */

class IntegerNet_MagentoLocalized_Model_Setup
{
    protected $_storeLocales = array();
    protected $_messages = array();

    /**
     * @param array $params
     */
    public function setup($params)
    {
        if (!Mage::getStoreConfig('magento_localized/is_initialized')) {

            $this->_deactivateCache();

            if (isset($params['auto_install'])) {
                Mage::getSingleton('magento_localized/installer')->installEditionModules($params['edition']);

                if (isset($params['ebay_edition']) && ($ebayEdition = $params['ebay_edition']) && $ebayEdition == 1) {
                    Mage::getSingleton('magento_localized/installer')->installEbayEditionModules();
                }

                Mage::reset();
                Mage::app('admin', 'store');
                Mage_Core_Model_Resource_Setup::applyAllUpdates();
            }
        }

        $this->_installModules($params);

        $this->_updateConfigData($params);
        Mage::dispatchEvent('magento_localized_form_save', array('params' => $params));

        $this->_createStores($params);

        if (!Mage::getStoreConfig('magento_localized/is_initialized')) {

            $this->_markNotificationsAsRead();

            $this->_runMageSetup();

            $this->_reindexAll();

            $this->_addMessage(Mage::helper('magento_localized')->__('Magento was prepared successfully. Please log out and log in again in order to access the newly installed modules.'));

            // Set a config flag to indicate that the setup has been initialized.
            $this->_setConfigData('magento_localized/is_initialized', 1);

            $this->_setConfigData('customer/integernet_removecustomeraccountlinks/items', 'recurring_profiles,billing_agreements,tags,OAuth Customer Tokens');

        } else {

            $this->_addMessage(Mage::helper('magento_localized')->__('Your data was saved successfully.'));
        }
    }

    protected function _installModules($params)
    {
        $installer = Mage::getSingleton('magento_localized/installer');

        foreach((array)Mage::getStoreConfig('magento_localized/modules') as $moduleIdentifier => $module) {

            if (
                $module['is_required']
                || (isset($params['modules_default']) && $params['modules_default'] == 1 && $module['is_default'])
                || ($module['is_available'] && isset($params['module'][$moduleIdentifier]) && $params['module'][$moduleIdentifier] == 1)
            ) {
                $installer->installPackageByName($module['code']);
            }
        }
    }

    /**
     * Update configuration based on form entries
     */
    protected function _updateConfigData($params)
    {
        if (!isset($params['field'])) {
            return;
        }

        $fieldData = $params['field'];
        $defaultLanguageCode = Mage::getStoreConfig('magento_localized/default_language');
        if (is_array($fieldData)) {
            foreach ($fieldData as $key => $value) {
                $fieldCode = implode('/', explode('__', $key));
                $this->_setConfigData($fieldCode, $value);
            }
            $this->_setConfigData('general/store_information/name', $fieldData['general__imprint__shop_name']);
            $this->_setConfigData('general/store_information/phone', $fieldData['general__imprint__telephone']);
            if (isset($fieldData['general__imprint__vat_id'])) {
                $this->_setConfigData('general/store_information/merchant_vat_number', $fieldData['general__imprint__vat_id']);
            }
            $this->_setConfigData('general/store_information/address', $this->_getAddress($fieldData));
            $this->_setConfigData('sales/identity/address', $this->_getAddress($fieldData));
            $this->_setConfigData('design/head/default_title', $fieldData['general__imprint__shop_name']);
            $this->_setConfigData('design/head/title_suffix', ' - ' . $fieldData['general__imprint__shop_name']);
            $this->_setConfigData('design/head/default_description', $fieldData['general__imprint__shop_name']);
            $this->_setConfigData('design/head/default_keywords', $fieldData['general__imprint__shop_name']);
            $this->_setConfigData('design/header/logo_alt', $fieldData['general__imprint__shop_name']);
            $this->_setConfigData('design/footer/copyright', '&copy; ' . date('Y') . ' ' . $fieldData['general__imprint__company_first']);
            $this->_setConfigData('trans_email/ident_general/name', $fieldData['general__imprint__shop_name']);
            $this->_setConfigData('trans_email/ident_general/email', $fieldData['general__imprint__email']);
            $this->_setConfigData('trans_email/ident_sales/name', $fieldData['general__imprint__shop_name']);
            $this->_setConfigData('trans_email/ident_sales/email', $fieldData['general__imprint__email']);
            $this->_setConfigData('trans_email/ident_support/name', $fieldData['general__imprint__shop_name']);
            $this->_setConfigData('trans_email/ident_support/email', $fieldData['general__imprint__email']);
            $this->_setConfigData('trans_email/ident_custom1/name', $fieldData['general__imprint__shop_name']);
            $this->_setConfigData('trans_email/ident_custom1/email', $fieldData['general__imprint__email']);
            $this->_setConfigData('trans_email/ident_custom2/name', $fieldData['general__imprint__shop_name']);
            $this->_setConfigData('trans_email/ident_custom2/email', $fieldData['general__imprint__email']);
            $this->_setConfigData('contacts/email/recipient_email', $fieldData['general__imprint__email']);
            $this->_setConfigData('sales_email/order/copy_to', $fieldData['general__imprint__email']);
            $this->_setConfigData('sales_pdf/firegento_pdf/sender_address_bar', $this->_getAddress($fieldData, ' - '));
            $this->_setConfigData('checkout/payment_failed/copy_to', $fieldData['general__imprint__email']);
            $this->_setConfigData('shipping/origin/postcode', $fieldData['general__imprint__zip']);
            $this->_setConfigData('shipping/origin/city', $fieldData['general__imprint__city']);
            $this->_setConfigData('shipping/origin/street_line1', $fieldData['general__imprint__street']);
            $this->_setConfigData('payment/banktransfer/active', 0);
            if (isset($fieldData['general__imprint__bank_account_owner'])
                && isset($fieldData['general__imprint__bank_account'])
                && isset($fieldData['general__imprint__bank_code_number'])
                && isset($fieldData['general__imprint__iban'])
                && isset($fieldData['general__imprint__swift'])
            ) {
                $this->_setConfigData('payment/banktransfer/instructions', Mage::helper('magento_localized')->__(
                    'After completion of this order, please transfer the order amount to: %s, Account %s, Bank number %s, %s',
                    $fieldData['general__imprint__bank_account_owner'],
                    $fieldData['general__imprint__bank_account'],
                    $fieldData['general__imprint__bank_code_number'],
                    $fieldData['general__imprint__bank_name']
                ));

                $this->_setConfigData('debitpayment/bankaccount/account_owner', $fieldData['general__imprint__bank_account_owner']);
                $this->_setConfigData('debitpayment/bankaccount/routing_number', $fieldData['general__imprint__bank_code_number']);
                $this->_setConfigData('debitpayment/bankaccount/account_number', $fieldData['general__imprint__bank_account']);

                if (isset($fieldData['general__imprint__iban'])
                    && isset($fieldData['general__imprint__swift'])
                ) {
                    $this->_setConfigData('payment/banktransfer/instructions', Mage::helper('magento_localized')->__(
                        'After completion of this order, please transfer the order amount to: %s, Account %s, Bank number %s, %s (IBAN %s, SWIFT %s)',
                        $fieldData['general__imprint__bank_account_owner'],
                        $fieldData['general__imprint__bank_account'],
                        $fieldData['general__imprint__bank_code_number'],
                        $fieldData['general__imprint__bank_name'],
                        $fieldData['general__imprint__iban'],
                        $fieldData['general__imprint__swift']
                    ));

                }
            }


            $defaultStore = Mage::app()->getDefaultStoreView();
            if ($defaultStore->getId()) {
                $defaultStore
                    ->setName(Mage::helper('magento_localized')->__(Mage::getStoreConfig('magento_localized/languages/' . $defaultLanguageCode . '/name')))
                    ->setCode(current(explode('_', $defaultLanguageCode)))
                    ->save();
            }
            $defaultStoreGroup = $defaultStore->getGroup();
            if ($defaultStoreGroup->getId()) {
                $defaultStoreGroup
                    ->setName($fieldData['general__imprint__shop_name'])
                    ->save();
            }
            $defaultWebsite = $defaultStore->getWebsite();
            if ($defaultWebsite->getId()) {
                $defaultWebsite
                    ->setName($fieldData['general__imprint__shop_name'])
                    ->save();
            }
            $defaultCategory = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToFilter('name', 'Default Category')
                ->getFirstItem();
            if ($defaultCategory->getId()) {
                $defaultCategory->setName(Mage::helper('magento_localized')->__('Default Category'))->save();
            }
        }

        if (isset($params['paypal_email'])) {
            $payPalEmailAddress = $params['paypal_email'];
            if ($payPalEmailAddress) {
                $this->_setConfigData('paypal/general/business_account', $payPalEmailAddress);
                $this->_setConfigData('payment/paypal_standard/active', 1);
                $this->_setConfigData('payment/paypal_standard/title', 'PayPal');
                $this->_setConfigData('payment/paypal_standard/sort_order', 10);
            }
        }
        
        $feedUrl = Mage::getStoreConfig('magento_localized/feed_url_prefix')
            . Mage::getStoreConfig('magento_localized/module_code')
            . '_'
            . $fieldData['admin__magento_localized__datatransfer']
            . '.xml';
        $this->_setConfigData('ikonoshirt/custom_rss_feeds/feeds/magento_localized', $feedUrl);
        $this->_setConfigData('magento_localized/newsfeed_url', $feedUrl);
        $this->_setConfigData('system/adminnotification/feed_url', $feedUrl);

        Mage::getSingleton('magento_localized/installer')->installPackageByName('ikonoshirt/customadminnotifications');


        $this->_setConfigData('general/region/state_required', '');
        $this->_setConfigData('general/region/display_all', 0);
        $this->_setConfigData('admin/startup/page', 'dashboard');

        $this->_storeLocales['default'] = $defaultLanguageCode;
        $this->_storeLocales[Mage::app()->getDefaultStoreView()->getId()] = '';

        if (!Mage::getStoreConfig('magento_localized/is_initialized')) {

            $this->_setConfigData('magento_localized/installation_id', md5(Mage::getBaseUrl()));

            date_default_timezone_set(Mage::getStoreConfig('general/locale/timezone'));
            $installationDate = new Zend_Date();
            $this->_setConfigData('magento_localized/installation_date', $installationDate->get(Zend_Date::ISO_8601));

            $distributorFilename = Mage::getModuleDir('etc', 'IntegerNet_MagentoLocalized') . DS . 'distributor.txt';
            if (file_exists($distributorFilename)) {
                $this->_setConfigData('magento_localized/distributor', file_get_contents($distributorFilename));
            }
        }
    }

    /**
     * Return formatted company address
     *
     * @param array $fieldData
     * @param string $seperator
     * @return string
     */
    protected function _getAddress($fieldData, $seperator = "\n")
    {
        $address = ($fieldData['general__imprint__company_first'] ? $fieldData['general__imprint__company_first'] . $seperator : '');
        $address .= ($fieldData['general__imprint__company_second'] ? $fieldData['general__imprint__company_second'] . $seperator : '');
        $address .= ($fieldData['general__imprint__street'] ? $fieldData['general__imprint__street'] . $seperator : '');
        $address .= ($fieldData['general__imprint__zip'] ? $fieldData['general__imprint__zip'] . ' ' : '');
        $address .= ($fieldData['general__imprint__city'] ? $fieldData['general__imprint__city'] : '');
        return $address;
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
        Mage::getModel('eav/entity_setup', 'core_setup')->setConfigData($key, $value, $scope, $scopeId);
    }

    /**
     * Create Stores with different languages
     */
    public function _createStores($params)
    {
        if (!isset($params['language']) || !is_array($params['language'])) {
            return;
        }

        $languageData = $params['language'];
        $i = 0;
        $storeCreated = false;
        foreach ($languageData as $localeCode => $value) {
            if ($value != 1) {
                continue;
            }

            $storeCreated = $this->_createStore($localeCode, $i++) || $storeCreated;
        }

        if ($storeCreated) {
            Mage::app()->reinitStores();
            $this->_setConfigData('web/url/use_store', 1);
        }
    }

    /**
     * @param string $localeCode (i.e. en_US or fr_FR)
     * @param int $sortOrder
     * @return boolean
     */
    protected function _createStore($localeCode, $sortOrder)
    {
        $languageCode = current(explode('_', $localeCode));
        /** @var $store Mage_Core_Model_Store */
        $store = Mage::getModel('core/store');
        $store->load($languageCode, 'code');
        if ($store->getId()) {
            $this->_addMessage(Mage::helper('magento_localized')->__('Store "%s" already exists.', $languageCode), 'notice');
            return false;
        }
        $store
            ->setCode($languageCode)
            ->setName(Mage::helper('magento_localized')->__(Mage::getStoreConfig('magento_localized/languages/' . $localeCode . '/name')))
            ->setWebsiteId(Mage::app()->getDefaultStoreView()->getWebsiteId())
            ->setGroupId(Mage::app()->getDefaultStoreView()->getGroupId())
            ->setIsActive(1)
            ->setSortOrder($sortOrder)
            ->save();

        $this->_storeLocales[$store->getId()] = $localeCode;

        $this->_setConfigData('general/locale/code', $localeCode, 'stores', $store->getId());
        $this->_setConfigData('general/locale/code_fallback', $localeCode, 'stores', $store->getId());

        $installer = Mage::getSingleton('magento_localized/installer');
        $localePackageName = Mage::getStoreConfig('magento_localized/languages/' . $localeCode . '/code');
        if ($localePackageName) {
            $installer->installPackageByName($localePackageName);
        }

        return true;
    }

    /**
     * Reindex all indices
     */
    protected function _reindexAll()
    {
        $processCollection = Mage::getModel('index/process')->getCollection();

        foreach ($processCollection as $process) {
            /* @var $process Mage_Index_Model_Process */
            $process->reindexAll();
        }
    }

    /**
     * Mark all notifications as read
     */
    protected function _markNotificationsAsRead()
    {
        $notificationCollection = Mage::getModel('adminnotification/inbox')->getCollection();
        foreach ($notificationCollection as $notification) {
            /* @var $notification Mage_AdminNotification_Model_Inbox */
            if (!$notification->getIsRead()) {
                $notification->setIsRead(1)
                    ->save();
            }
        }
    }

    /**
     * Run Setup of MageSetup as if the form had been filled and submitted
     */
    protected function _runMageSetup()
    {
        $emailStoreLocales = $this->_storeLocales;
        // workaround as spanisch language doesn't contain emails
        foreach($emailStoreLocales as $identifier => $localeCode) {
            if ($localeCode == 'es_ES') {
                $emailStoreLocales[$identifier] = 'en_US';
            }
        }
        Mage::getSingleton('magesetup/setup')->setup(array(
            'country' => strtolower(Mage::getStoreConfig('magento_localized/country')),
            'cms_locale' => $this->_storeLocales,
            'email_locale' => $emailStoreLocales,
        ));
    }

    /**
     * Deactivate all caches
     */
    protected  function _deactivateCache()
    {
        /* @var $cache Mage_Core_Model_Cache */
        $cache = Mage::getModel('core/cache');

        /* @var $options array */
        $options = $cache->canUse(null);

        $newOptions = array();
        foreach ($options as $option => $value) {
            $newOptions[$option] = 0;
        }

        $cache->saveOptions($newOptions);
    }
    
    protected function _addMessage($message, $type='success')
    {
        $this->_messages[$type][] = $message;    
    }
    
    public function getSuccessMessages()
    {
        if (isset($this->_messages['success'])) {
            return $this->_messages['success'];
        }
        return array();
    }
    
    public function getNoticeMessages()
    {
        if (isset($this->_messages['notice'])) {
            return $this->_messages['notice'];
        }
        return array();
    }
    
    public function getWarningMessages()
    {
        if (isset($this->_messages['warning'])) {
            return $this->_messages['warning'];
        }
        return array();
    }
    
    public function getErrorMessages()
    {
        if (isset($this->_messages['error'])) {
            return $this->_messages['error'];
        }
        return array();
    }
}