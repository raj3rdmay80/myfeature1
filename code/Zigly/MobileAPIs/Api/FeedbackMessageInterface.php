<?php 

namespace Zigly\MobileAPIs\Api;

interface FeedbackMessageInterface {
    
	 /**
     * Customer feedbackMessage
     *
     * @param int $id
     * @return array
     */
    public function feedbackMessage($id);
}