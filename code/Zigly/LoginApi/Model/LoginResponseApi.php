<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_LoginApi
 */
declare(strict_types=1);

namespace Zigly\LoginApi\Model;

use Zigly\LoginApi\Api\Data\LoginResponseInterface;

class LoginResponseApi extends \Magento\Framework\Api\AbstractExtensibleObject implements LoginResponseInterface
{

    /**
     * Get mobileToken
     * @return string|null
     */
    public function getMobileToken()
    {
        return $this->_get(self::MOBILE_TOKEN);
    }

    /**
     * Set mobileToken
     * @param string $mobileToken
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     */
    public function setMobileToken($mobileToken)
    {
        return $this->setData(self::MOBILE_TOKEN, $mobileToken);
    }

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
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get token
     * @return string|null
     */
    public function getToken()
    {
        return $this->_get(self::TOKEN);
    }

    /**
     * Set token
     * @param string $token
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     */
    public function setToken($token)
    {
        return $this->setData(self::TOKEN, $token);
    }

    /**
     * Get isCustomer
     * @return string|null
     */
    public function getIsCustomer()
    {
        return $this->_get(self::IS_CUSTOMER);
    }

    /**
     * Set isCustomer
     * @param string $isCustomer
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     */
    public function setIsCustomer($isCustomer)
    {
        return $this->setData(self::IS_CUSTOMER, $isCustomer);
    }

    /**
     * Get message
     * @return string|null
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * Set message
     * @param string $message
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

     /**
     * Get photo path
     * @return string|null
     */
    public function getPhotoPath()
    {
        return $this->_get(self::PHOTO_PATH);
    }

    /**
     * Set photo path
     * @param string $photoPath
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     */
    public function setPhotoPath($photoPath)
    {
        return $this->setData(self::PHOTO_PATH, $photoPath);
    }
}