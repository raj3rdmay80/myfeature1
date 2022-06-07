<?php 
namespace Zigly\MobileAPIs\Api;
 
 
interface BookingCancelStatusInterface {

    /**
     * Cancels a specified order.
     *
     * @param int $id.
	 * @param string $cancelMessage (optional)
	 * @param string $updateStatus
     * @return array
     */
    public function bookingCancel($id, $updateStatus, $cancelReason = null);
	
	/**
     * Reason for cancelling order.
     *
	 * @param string $storeid (optional)
     * @return array
     */
    public function cancelReason($storeid = null);
 
}
