<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProductApi
 */
declare(strict_types=1);

namespace Zigly\ProductApi\Api;

interface PincodeRepositoryInterface
{
   /**
     * Pincode Api
     * @param string $data
     * @return \Zigly\ProductApi\Api\Data\PincodeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
     public function pincodeApi();
}