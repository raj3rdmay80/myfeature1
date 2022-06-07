<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_LoginApi
 */
declare(strict_types=1);

namespace Zigly\LoginApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface LoginRepositoryInterface
{

    /**
     * Send Login Otp
     * @param string $username
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendOtp($username);

    /**
     * Resend Login Otp
     * @param string $username
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resendOtp($username);

    /**
     * Login Otp and generate token
     * @param string $username
     * @param string $otp
     * @param string $type
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function customerLogin($username, $otp, $type);

    /**
     * Create customer and generate token
     * @param mixed $customer
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createCustomer($customer);

    /**
     * revoke customer login
     * @param int $customerId
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function revokeCustomerAccessToken($customerId);
}