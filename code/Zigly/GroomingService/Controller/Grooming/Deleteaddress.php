<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */

namespace Zigly\GroomingService\Controller\Grooming;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Delete customer address controller action.
 */
class Deleteaddress extends \Magento\Customer\Controller\Address implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * @inheritdoc
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $response = [
            'errors' => true,
            'message' => ''
        ];
        $resultJsonFactory = $this->_objectManager->create('Magento\Framework\Controller\Result\JsonFactory');
        $resultJson = $resultJsonFactory->create();

        $addressId = $this->getRequest()->getParam('id', false);
        // && $this->_formKeyValidator->validate($this->getRequest())

        if ($addressId ) {
            try {
                $address = $this->_addressRepository->getById($addressId);
                if ($address->getCustomerId() === $this->_getSession()->getCustomerId()) {
                    $this->_addressRepository->deleteById($addressId);
                    $response = [
                        'errors' => false,
                        'message' => __('You deleted the address.')
                    ];
                } else {
                    $response['message'] = __('We can\'t delete the address right now.');
                }
            } catch (\Exception $other) {
                    $response['message'] = __('We can\'t delete the address right now.');
                    $response['other'] = $other;
            }
        }
        return $resultJson->setData($response);
    }
}
