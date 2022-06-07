<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_PincodeChecker
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\PincodeChecker\Controller\Adminhtml\Pincodechecker;

use Magento\Backend\App\Action;

/**
 * Class Importpost
 * @package Ced\PincodeChecker\Controller\Adminhtml\Pincodechecker
 */
class Importpost extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\PincodeChecker\Helper\Data
     */
    protected $_pincodehelper;

    protected $messageManager;

    /**
     * @var \Ced\PincodeChecker\Model\ResourceModel\Pincode
     */
    protected $pincode;

    /**
     * Importpost constructor.
     * @param Action\Context $context
     * @param \Ced\PincodeChecker\Helper\Data $_pincodehelper
     * @param \Ced\PincodeChecker\Model\ResourceModel\Pincode $pincode
     */
    public function __construct(
        Action\Context $context,
        \Ced\PincodeChecker\Helper\Data $_pincodehelper,
        \Ced\PincodeChecker\Model\ResourceModel\Pincode $pincode
    )
    {
        $this->_pincodehelper = $_pincodehelper;
        $this->pincode = $pincode;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->_pincodehelper->isPinCodeCheckerEnabled())
            return $this->_redirect('*/*/');


        if (!empty($_FILES['import_csv']['name'])) {
            if (isset($_FILES['import_csv']['name'])) { 
                $this->_importCsv($_FILES['import_csv']);
            }
        }

    }

    /**
     * @param $csvFile
     */
    protected function _importCsv($csvFile)
    {

        try {
            $columns = array('website_id', 'vendor_id', 'zipcode', 'can_cod', 'can_ship', 'days_to_deliver');

            $import = $this->pincode->importCsv($csvFile, $columns);

            if ($import->_importErrors) {
                $error = __('File has not been imported. See the following list of errors: %s', implode(" \n", $import->_importErrors));
                $this->messageManager->addErrorMessage($error);
            }

            $this->messageManager->addSuccessMessage(__('Zipcode CSV Imported Successfully, and empty rows will be skipped.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Importing process of zipcodes stopped due to incorrect data in the CSV. Please check the CSV data entered.')
            );
        }
        $this->_redirect('*/*/index');
    }

}
