<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProfessionalGraphQl
 */
declare(strict_types=1);

namespace Zigly\ProfessionalGraphQl\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ProfessionalRepositoryInterface
{

    /**
     * Update Professional
     * @param mixed $professional
     * @param string $token
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update($professional, $token);
}

