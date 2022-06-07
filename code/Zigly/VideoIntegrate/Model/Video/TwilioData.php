<?php 
namespace Zigly\VideoIntegrate\Model\Video;
class TwilioData extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb{
	 public function _construct(){
	 $this->_init("twilio_data","entity_id");
 }
}
