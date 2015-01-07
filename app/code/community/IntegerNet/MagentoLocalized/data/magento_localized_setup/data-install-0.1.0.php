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

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

if(Mage::helper('core')->isModuleEnabled('poll')) {
    /** @var $pollCollection Mage_Poll_Model_Resource_Poll_Collection */
    $pollCollection = Mage::getModel('poll/poll')->getCollection();
    foreach($pollCollection as $poll) {
        /** @var $poll Mage_Poll_Model_Poll */
        $poll->delete();
    }
}


$installer->endSetup();