<?php 
namespace Zigly\VideoIntegrate\Api;
 
 
interface VideoIntegrateInterface {


	/**
	 * GET for getvettoken api
	 * @param int $bookingid
	 * @return mixed
	 */
	
	public function getvettoken($bookingid);
}
