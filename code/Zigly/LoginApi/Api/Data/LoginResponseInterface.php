<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_LoginApi
 */
declare(strict_types=1);

namespace Zigly\LoginApi\Api\Data;

interface LoginResponseInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const TOKEN = 'token';
    const IS_CUSTOMER = 'is_customer';
    const STATUS = 'status';
    const MESSAGE = 'message';
    const MOBILE_TOKEN = 'mobile_token';
    const PHOTO_PATH = 'photo_path';

    /**
     * Get mobileToken
     * @return string|null
     */
    public function getMobileToken();

    /**
     * Set mobileToken
     * @param string $mobileToken
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     */
    public function setMobileToken($mobileToken);

    /**
     * Get token
     * @return string|null
     */
    public function getToken();

    /**
     * Set token
     * @param string $token
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     */
    public function setToken($token);

    /**
     * Get isCustomer
     * @return string|null
     */
    public function getIsCustomer();

    /**
     * Set isCustomer
     * @param string $isCustomer
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     */
    public function setIsCustomer($isCustomer);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     */
    public function setStatus($status);

    /**
     * Get message
     * @return string|null
     */
    public function getMessage();

    /**
     * Set message
     * @param string $message
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     */
    public function setMessage($photoPath);

     /**
     * Get photoPath
     * @return string|null
     */
    public function getPhotoPath();

    /**
     * Set photoPath
     * @param string $photoPath
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     */
    public function setPhotoPath($photoPath);
}