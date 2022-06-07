<?php 
namespace Zigly\VideoIntegrate\Model\Suggest\SuggestFragrance;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection{
	public function _construct(){
		$this->_init("Zigly\VideoIntegrate\Model\TwilioData","Zigly\VideoIntegrate\Model\Video\TwilioData");
	}
}
