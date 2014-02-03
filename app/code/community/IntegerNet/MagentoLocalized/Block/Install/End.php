<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_MagentoLocalized
 * @copyright  Copyright (c) 2014 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */ 
class IntegerNet_MagentoLocalized_Block_Install_End extends Mage_Install_Block_End
{
    /**
     * Return url for iframe source
     *
     * @return string
     */
    public function getIframeSourceUrl()
    {
        if (!IntegerNet_MagentoLocalized_Model_AdminNotification_Survey::isSurveyUrlValid()
            || Mage::getSingleton('install/installer')->getHideIframe()) {
            return null;
        }
        return IntegerNet_MagentoLocalized_Model_AdminNotification_Survey::getSurveyUrl();
    }
}