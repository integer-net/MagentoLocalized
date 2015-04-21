<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_MagentoLocalized
 * @copyright  Copyright (c) 2014 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */ 
class IntegerNet_MagentoLocalized_Model_AdminNotification_Survey extends Mage_AdminNotification_Model_Survey
{
    /**
     * Return survey url
     *
     * @return string
     */
    public static function getSurveyUrl()
    {
        $localeCode = Mage::getStoreConfig('general/locale/code');
        if (!$localeCode) {
            $localeCode = Mage::getSingleton('install/session')->getLocale();
            if (!$localeCode) {
                $localeData = Mage::getSingleton('install/session')->getLocaleData();
                $localeCode = $localeData['locale'];
                if (!$localeCode) {
                    $localeCode = Mage::getStoreConfig('magento_localized/default_language');
                }
            }
        }
        $languageCode = current(explode('_', $localeCode));

        $surveyUrl = Mage::getStoreConfig('magento_localized/survey_url/' . $languageCode);

        if ($surveyUrl) {
            if (Mage::app()->getRequest()->isSecure()) {
                $surveyUrl = str_replace('http://', 'https://', $surveyUrl);
            }
            return $surveyUrl;
        }

        return Mage::getStoreConfig('magento_localized/survey_url/default');
    }
}