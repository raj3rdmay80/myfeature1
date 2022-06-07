<?php
declare(strict_types=1);

namespace Zigly\Mobilehome\Api\Data;

interface MobilehomeInterface
{

    const NAME = 'name';
    const MOBILEHOME_ID = 'mobilehome_id';

    /**
     * Get mobilehome_id
     * @return string|null
     */
    public function getMobilehomeId();

    /**
     * Set mobilehome_id
     * @param string $mobilehomeId
     * @return \Zigly\Mobilehome\Mobilehome\Api\Data\MobilehomeInterface
     */
    public function setMobilehomeId($mobilehomeId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Zigly\Mobilehome\Mobilehome\Api\Data\MobilehomeInterface
     */
    public function setName($name);
}

