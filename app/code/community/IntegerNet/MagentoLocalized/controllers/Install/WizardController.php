<?php
/**
 * Localized Magento Editions
 *
 * @category   IntegerNet
 * @package    IntegerNet_MagentoLocalized
 * @copyright  Copyright (c) 2013 integer_net GmbH (http://www.integer-net.de/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Andreas von Studnitz <avs@integer-net.de>
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
