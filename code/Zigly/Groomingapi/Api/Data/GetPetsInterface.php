<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingAPI
 */
declare(strict_types=1);

namespace Zigly\Groomingapi\Api\Data;

interface GetPetsInterface
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
     * @return \Zigly\Groomingapi\Api\Data\GetPetsInterface
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
     * @return \Zigly\Groomingapi\Api\Data\GetPetsInterface
    */
    public function setMessage($message);
}