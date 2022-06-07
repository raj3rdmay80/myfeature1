<?php

namespace Zigly\VideoIntegrate\Observer;

use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\ClientToken;

use Twilio\Jwt\Grants\VideoGrant;
use Zigly\VideoIntegrate\Model\TwilioDataFactory;
use Zigly\GroomingService\Model\GroomingFactory;

class TwilioObserver implements \Magento\Framework\Event\ObserverInterface
{

	protected $twilioDataFactory;
	protected $_messageManager;
	protected $groomingFactory;

    public function __construct(
		TwilioDataFactory $twilioDataFactory,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		GroomingFactory $groomingFactory
    ) {
		$this->twilioDataFactory = $twilioDataFactory;
		$this->groomingFactory = $groomingFactory;
		$this->_messageManager = $messageManager;
    }
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$groomerData = $observer->getData('groomer_data');
		$groomOrder = $this->groomingFactory->create()->load($groomerData->getGroomerId());
		$model = $this->twilioDataFactory->create();

		$response = array();
		if($groomOrder->getCenter() == 'Insta Consult' || $groomOrder->getCenter() == 'Video Appointment'){
			try{
				$TWILIO_ACCOUNT_SID = 'AC846889db970665a9365bbd6dbcc9449b';
				$TWILIO_API_KEY = 'SK8619bec36089099af92be140c8502292';
				$TWILIO_API_SECRET = 'LXNqiKEnxtqbXzcHGHlgWvJvIVJLILwM';
				
				$twilioClient = new Client($TWILIO_API_KEY, $TWILIO_API_SECRET);
				$roomName = $this->createRoom($twilioClient, $groomOrder);
				$vetsData = $this->vetTokenCreate($TWILIO_ACCOUNT_SID, $TWILIO_API_KEY, $TWILIO_API_SECRET, $groomOrder, $roomName);
				//$customersData = $this->customerTokenCreate($TWILIO_ACCOUNT_SID, $TWILIO_API_KEY, $TWILIO_API_SECRET, $groomOrder, $roomName);
				$model->setData('token_vet',$vetsData['vet_token']);
				//$model->setData('token_customer',$customersData['customer_token']);
				$model->setData('identity_vet', $vetsData['vet_identity']);
				//$model->setData('identity_customer',$customersData['customer_identity']);
				$model->setData('room_name',$roomName);
				$model->setData('order_id',$groomOrder->getId());
				$model->setData('customer_id',$groomOrder->getCustomerId());
				$model->setData('status', 0);
				$model->save();
		 	}catch(Exception $e){
				$this->_messageManager->addError($e->getMessage());
			}
		}

		return $this;
	}
	public function createRoom($twilioClient, $groomOrder ){
		$roomName = 'TwilioRoom'.rand().$groomOrder->getId();
		$room = $twilioClient->video->rooms->create([
				'uniqueName' => $roomName,
				'type' => 'peer-to-peer',
				'enableTurn' => false,
				'duration'   => 1800,
				'maxParticipants'  => 2,
				
			]);
		return $roomName;
	}
	public function vetTokenCreate($TWILIO_ACCOUNT_SID, $TWILIO_API_KEY, $TWILIO_API_SECRET, $groomOrder, $roomName){
		$videoGrant = new VideoGrant();

		$vet_id = $groomOrder->getId().$groomOrder->getGroomerId();
		$vet_token = new AccessToken($TWILIO_ACCOUNT_SID, $TWILIO_API_KEY, $TWILIO_API_SECRET, 1800, $vet_id);
		$videoGrant->setRoom($roomName);
		$vet_token->addGrant($videoGrant);
		$vetsData = ['vet_identity' => $vet_id, 'vet_token' => $vet_token->toJWT()];
		return $vetsData;
	}
	public function customerTokenCreate($TWILIO_ACCOUNT_SID, $TWILIO_API_KEY, $TWILIO_API_SECRET, $groomOrder, $roomName){
		$videoGrant = new VideoGrant();

		$customer_id = $groomOrder->getId().$groomOrder->getCustomerId();
		$customer_token = new AccessToken($TWILIO_ACCOUNT_SID, $TWILIO_API_KEY, $TWILIO_API_SECRET, 1800, $customer_id);
		$videoGrant->setRoom($roomName);
		$customer_token->addGrant($videoGrant);
		$customersData = ['customer_identity' => $customer_id, 'customer_token' => $customer_token->toJWT()];
		return $customersData;
	}
}
