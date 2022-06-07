<?php
/**
 * Copyright © 2022 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Groomingapi\Api;

interface GetBreedsManagementInterface
{

    /**
     * GET for getPets api
     * @param string $search
    * @return \Zigly\Groomingapi\Api\Data\GetPetsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBreeds($search);
}

