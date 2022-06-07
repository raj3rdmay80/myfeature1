<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Api\Data;

interface GroomingHubInterface
{

    const HUB_ID = 'Hub_Id';
    const GROOMINGHUB_ID = 'groominghub_id';

    /**
     * Get groominghub_id
     * @return string|null
     */
    public function getGroominghubId();

    /**
     * Set groominghub_id
     * @param string $groominghubId
     * @return \Zigly\ScheduleManagementApi\GroomingHub\Api\Data\GroomingHubInterface
     */
    public function setGroominghubId($groominghubId);

    /**
     * Get Hub_Id
     * @return string|null
     */
    public function getHubId();

    /**
     * Set Hub_Id
     * @param string $hubId
     * @return \Zigly\ScheduleManagementApi\GroomingHub\Api\Data\GroomingHubInterface
     */
    public function setHubId($hubId);
}

