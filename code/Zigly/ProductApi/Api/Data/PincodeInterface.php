<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProductApi
 */
declare(strict_types=1);

namespace Zigly\ProductApi\Api\Data;

interface PincodeInterface
{
    const MESSAGE = 'message';
    const STATUS = 'status';

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus();

   /**
     * Set status
     * @param string $status
     * @return \Zigly\ProductApi\Api\Data\PincodeInterface
     */
    public function setStatus($status);

    /**
     * Get message
     *
     * @return string|null
     */
    public function getMessage();

   /**
     * Set message
     * @param string $message
     * @return \Zigly\ProductApi\Api\Data\PincodeInterface
     */
    public function setMessage($message);
}