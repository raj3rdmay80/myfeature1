<?php 
namespace Zigly\MobileAPIs\Api;
 
 
interface OrderManagementInterface {

    /**
     * Cancels a specified order.
     *
     * @param int $id.
     * @param string $token
     * @param string $cancelMessage (optional)
     * @return array
     */
    public function orderCancel($id, $token, $cancelMessage = null);
    
    /**
     * Reason for cancelling order.
     *
     * @param string $token
     * @param string $storeid (optional)
     * @return array
     */
    public function cancelReason($token, $storeid = null);
  
}
