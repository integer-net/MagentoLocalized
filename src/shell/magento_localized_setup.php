<?php

require_once 'abstract.php';

class IntegerNet_MagentoLocalized_Shell_Setup extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        if ($this->getArg('file')) {
            try {
                $filename = $this->_getFilename();
                $fileContents = file_get_contents($filename);
                $params = Zend_Json::decode($fileContents);

                /** @var $setup IntegerNet_MagentoLocalized_Model_Setup */
                $setup = Mage::getSingleton('magento_localized/setup');
                $setup->setup($params);
                
                foreach($setup->getErrorMessages() as $message) {
                    echo 'Error: ' . $message . PHP_EOL;
                }
                foreach($setup->getWarningMessages() as $message) {
                    echo 'Warning: ' . $message . PHP_EOL;
                }
                foreach($setup->getNoticeMessages() as $message) {
                    echo 'Notice: ' . $message . PHP_EOL;
                }
                foreach($setup->getSuccessMessages() as $message) {
                    echo 'Success: ' . $message . PHP_EOL;
                }
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage() . PHP_EOL;
                Mage::logException($e);
            }
                        
        } else {
            echo $this->usageHelp();
        }
    }

    /**
     * @return string
     */
    protected function _getFilename()
    {
        $filename = $this->getArg('file');

        if (!is_file($filename)) {
            Mage::throwException('File "' . $filename . '" does not exist.');
        }

        if (!is_readable($filename)) {
            Mage::throwException('File "' . $filename . '" is not readable.');
        }

        if (!filesize($filename)) {
            Mage::throwException('File "' . $filename . '" seems to be empty.');
        }

        return $filename;
    }


    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f magento_localized_setup.php -- [options]
        php -f magento_localized_setup.php -- --file ../var/magento_localized_config.json

  --file <filepath> Path to file with config data as json
  help              This help

USAGE;
    }
}

$shell = new IntegerNet_MagentoLocalized_Shell_Setup();
$shell->run();
