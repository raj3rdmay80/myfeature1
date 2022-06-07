<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProductApi
 */
declare(strict_types=1);

namespace Zigly\ProductApi\Model;

use Zigly\ProductApi\Api\Data\PincodeInterface;

class PincodeResponceApi extends \Magento\Framework\Api\AbstractExtensibleObject implements PincodeInterface
{

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return string
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

     /**
     * Get message
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * Set message
     * @param string $message
     * @return string
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

}