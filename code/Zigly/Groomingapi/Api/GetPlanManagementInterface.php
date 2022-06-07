<?php
/**
 * Copyright © 2022 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Groomingapi\Api;

interface GetPlanManagementInterface
{

    /**
     * GET for getPlan api
     * @param string $type 
     * @param string $cities
     * @param string $species_type
     * @return \Zigly\Groomingapi\Api\Data\GetPetsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGetPlan($type,$cities,$species_type);
}

