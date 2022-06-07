<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Groomingapi
 */
declare(strict_types=1);

namespace Zigly\Groomingapi\Model;

use Zigly\Groomingapi\Api\Data\GetPetsInterface;

class GetPlanResponse extends \Magento\Framework\Api\AbstractExtensibleObject implements GetPetsInterface
{

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return string
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

     /**
     * Get message
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * Set message
     * @param string $message
     * @return string
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

}