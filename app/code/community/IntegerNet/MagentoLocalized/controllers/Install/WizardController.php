<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Installation wizard controller
 */
require_once('Mage/Install/controllers/WizardController.php');
class IntegerNet_MagentoLocalized_Install_WizardController extends Mage_Install_WizardController
{
    /**
     * Process begin step POST data
     */
    public function beginPostAction()
    {
        $this->_checkIfInstalled();

        $agree = $this->getRequest()->getPost('agree');
        if ($agree && $step = $this->_getWizard()->getStepByName('begin')) {
            $this->getResponse()->setRedirect($step->getNextUrl());

            try {
                Mage::getSingleton('magento_localized/installer')->installEditionModules($this->getRequest()->getPost('edition'));
            } catch (Exception $e) {
                Mage::getSingleton('install/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($step->getUrl());
            }
        }
        else {
            $this->_redirect('install');
        }
    }
}
