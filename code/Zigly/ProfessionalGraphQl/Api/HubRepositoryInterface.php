<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProfessionalGraphQl
 */
declare(strict_types=1);

namespace Zigly\ProfessionalGraphQl\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface HubRepositoryInterface
{

    /**
     * Get Hub list
     * @param string $token
     * @return \Zigly\Hub\Api\Data\HubInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getHubList($token);
}

