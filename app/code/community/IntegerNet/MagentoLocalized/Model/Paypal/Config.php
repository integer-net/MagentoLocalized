<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_MagentoLocalized
 * @copyright  Copyright (c) 2014 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */ 
class IntegerNet_MagentoLocalized_Model_Paypal_Config extends Mage_Paypal_Model_Config 
{
    /**
     * Check wheter specified country code is supported by build notation codes for specific countries
     *
     * @param $code
     * @return string|null
     */
    private function _matchBnCountryCode($code)
    {
        switch ($code) {
            // GB == UK
            case 'GB':
                return 'UK';
            // Australia, Austria, Belgium, Canada, China, France, Germany, Hong Kong, Italy
            case 'AU': case 'AT': case 'BE': case 'CA': case 'CN': case 'FR': case 'DE': case 'HK': case 'IT':
            // Japan, Mexico, Netherlands, Poland, Singapore, Spain, Switzerland, United Kingdom, United States
            case 'JP': case 'MX': case 'NL': case 'PL': case 'SG': case 'ES': case 'CH': case 'UK': case 'US':
            // Russia
            case 'RU':
                return $code;
        }
    }
    
    /**
     * Modify paypal button tracking code
     *
     * @param string $countryCode ISO 3166-1
     * @return string
     */
    public function getBuildNotationCode($countryCode = null)
    {
        if (null === $countryCode) {
            $countryCode = $this->_matchBnCountryCode($this->getMerchantCountry());
        }
        return sprintf('MagentoCommerce_Cart_%sEdition', $countryCode);
    }
}