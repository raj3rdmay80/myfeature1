<?php
/**
 * Copyright (C) 2021  Zigly
 * @package  Zigly_ServicePay
 */

namespace Zigly\ServicePay\Model;

use Magento\Framework\Exception\LocalizedException;

/**
 * Pay In Store payment method model
 */
class PaymentPending extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
    * Payment code
    *
    * @var string
    */
    protected $_code = 'services_razorpay';

    /**
    * Availability option
    *
    * @var bool
    */
    protected $_isOffline = true;

    /**
     * Assign corresponding data
     *
     * @param \Magento\Framework\DataObject|mixed $data
     * @return $this
     * @throws LocalizedException
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);
        $infoInstance = $this->getInfoInstance();
        $additionalData = $data->getData()['additional_data'];
        if (isset($additionalData['app_number'])) {
            $infoInstance->setAdditionalInformation('app_number', $additionalData['app_number']);
        }
        return $this;
    }
}