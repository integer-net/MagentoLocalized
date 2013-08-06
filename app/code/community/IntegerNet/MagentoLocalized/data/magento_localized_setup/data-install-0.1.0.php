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

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

/** @var $pollCollection Mage_Poll_Model_Resource_Poll_Collection */
$pollCollection = Mage::getModel('poll/poll')->getCollection();
foreach($pollCollection as $poll) {
    /** @var $poll Mage_Poll_Model_Poll */
    $poll->delete();
}

$installer->endSetup();