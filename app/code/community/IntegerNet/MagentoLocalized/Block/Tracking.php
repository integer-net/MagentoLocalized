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
class IntegerNet_MagentoLocalized_Block_Tracking extends Mage_Adminhtml_Block_Template
{
    const DAYS_BETWEEN_TRACKING_REQUESTS = 30;

    /**
     * Generate Tracking URL including encoded parameters
     *
     * @return string
     */
    public function getTrackingUrl()
    {
        $params = array();
        switch (Mage::getStoreConfig('admin/magento_localized/datatransfer')) {

            case IntegerNet_MagentoLocalized_Model_Source_Datatransfer::DATATRANSFER_ADVANCED:

                $params['server_ip'] = Mage::app()->getRequest()->getServer('SERVER_ADDR');
                $params['installation_date'] = Mage::getStoreConfig('magento_localized/installation_date');
                $params['installation_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                $params['magento_version'] = (string)Mage::getVersion();
                $params['shop_name'] = Mage::getStoreConfig('general/imprint/shop_name');
                $params['company_first'] = Mage::getStoreConfig('general/imprint/company_first');
                $params['company_second'] = Mage::getStoreConfig('general/imprint/company_second');
                $params['street'] = Mage::getStoreConfig('general/imprint/street');
                $params['zip'] = Mage::getStoreConfig('general/imprint/zip');
                $params['city'] = Mage::getStoreConfig('general/imprint/city');
                $params['country'] = Mage::getStoreConfig('general/imprint/country');
                $params['telephone'] = Mage::getStoreConfig('general/imprint/telephone');
                $params['fax'] = Mage::getStoreConfig('general/imprint/fax');
                $params['email'] = Mage::getStoreConfig('general/imprint/email');
                $params['web'] = Mage::getStoreConfig('general/imprint/web');
                $params['ceo'] = Mage::getStoreConfig('general/imprint/ceo');
                $params['owner'] = Mage::getStoreConfig('general/imprint/owner');
                // fallthrough intended

            case IntegerNet_MagentoLocalized_Model_Source_Datatransfer::DATATRANSFER_BASIC:

                $params['installation_id'] = Mage::getStoreConfig('magento_localized/installation_id'); // Anonymized, not decryptable
                $params['distributor'] = Mage::getStoreConfig('magento_localized/distributor');
                $params['package_type'] = Mage::getStoreConfig('magento_localized/module_title');
                $params['gsc_version'] = (string)Mage::app()->getConfig()->getNode('modules/IntegerNet_MagentoLocalized/version');
                $params['transfer_type'] = Mage::getStoreConfig('admin/magento_localized/datatransfer');
        }

        // store current date in database
        $transferDate = new Zend_Date();
        $this->_setConfigData('magento_localized/transfer_date', $transferDate->get(Zend_Date::ISO_8601));

        return Mage::getStoreConfig('magento_localized/tracking_url') . '?data=' . base64_encode(serialize($params));
    }

    /**
     * Check if data transfer is activated and if enough time has passed since the last request
     *
     * @return bool
     */
    public function showTracking()
    {
        if (!Mage::getStoreConfig('magento_localized/installation_id')) {
            return false;
        }

        if (Mage::getStoreConfig('admin/magento_localized/datatransfer') == IntegerNet_MagentoLocalized_Model_Source_Datatransfer::DATATRANSFER_NONE) {
            return false;
        }

        $lastTransferDate = Mage::getStoreConfig('magento_localized/transfer_date');
        if (!$lastTransferDate) {
            return true;
        }

        date_default_timezone_set(Mage::getStoreConfig('general/locale/timezone'));
        $lastTransferDateObject = new Zend_Date();
        $lastTransferDateObject->set($lastTransferDate, Zend_Date::ISO_8601);

        return ($lastTransferDateObject->add(self::DAYS_BETWEEN_TRACKING_REQUESTS, Zend_Date::DAY)->isEarlier(new Zend_Date()));
    }

    /**
     * Set configuration data
     *
     * @param string $key
     * @param string|int $value
     */
    protected function _setConfigData($key, $value)
    {
        Mage::getModel('eav/entity_setup', 'core_setup')->setConfigData($key, $value);
    }
}
