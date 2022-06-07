<?php
/**
 * Copyright © 2022 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Groomingapi\Api;

interface UserLocationManagementInterface
{

    /**
     * GET for userLocation api
     * @param string $pincode_city
     * @return string
     */
    public function getUserLocation($pincode_city);
}

