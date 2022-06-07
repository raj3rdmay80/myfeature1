<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProfessionalGraphQl
 */
declare(strict_types=1);

namespace Zigly\ProfessionalGraphQl\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ProfessionalLoginRepositoryInterface
{

    /**
     * Send Login Otp
     * @param string $phone_number
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendOtp($phone_number);

    /**
     * Resend Login Otp
     * @param string $phone_number
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resendOtp($phone_number);

    /**
     * Generate professional token
     * @param string $phone_number
     * @param string $otp
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateToken($phone_number, $otp);

    /**
     * Professional logout
     * @param string $token
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function logout($token);

    /**
     * Professional listing
     * @param string $token
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function list($token);

}

