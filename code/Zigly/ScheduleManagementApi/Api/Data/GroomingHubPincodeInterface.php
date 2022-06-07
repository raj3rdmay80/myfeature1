<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Api\Data;

interface GroomingHubPincodeInterface
{

    const PINCODEID = 'PincodeId';
    const GROOMINGHUBPINCODE_ID = 'groominghubpincode_id';

    /**
     * Get groominghubpincode_id
     * @return string|null
     */
    public function getGroominghubpincodeId();

    /**
     * Set groominghubpincode_id
     * @param string $groominghubpincodeId
     * @return \Zigly\ScheduleManagementApi\GroomingHubPincode\Api\Data\GroomingHubPincodeInterface
     */
    public function setGroominghubpincodeId($groominghubpincodeId);

    /**
     * Get PincodeId
     * @return string|null
     */
    public function getPincodeId();

    /**
     * Set PincodeId
     * @param string $pincodeId
     * @return \Zigly\ScheduleManagementApi\GroomingHubPincode\Api\Data\GroomingHubPincodeInterface
     */
    public function setPincodeId($pincodeId);
}

