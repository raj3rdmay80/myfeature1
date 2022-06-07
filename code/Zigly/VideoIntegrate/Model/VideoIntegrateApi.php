<?php 
namespace Zigly\VideoIntegrate\Model;
 
use Zigly\VideoIntegrate\Model\TwilioDataFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Zigly\GroomingService\Model\GroomingFactory;
use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\ClientToken;
use Twilio\Jwt\Grants\VideoGrant;

class VideoIntegrateApi {

	public function __construct(
		TwilioDataFactory $twilioDataFactory,
		JsonFactory $resultJsonFactory,
		GroomingFactory $groomingFactory		
    ) {
		$this->twilioDataFactory = $twilioDataFactory;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->groomingFactory = $groomingFactory;
    }
	/**
	 * {@inheritdoc}
	 */
	public function getvettoken($bookingid)
	{
		$response = array();
		$resultJson = $this->resultJsonFactory->create();
		$TWILIO_ACCOUNT_SID = 'AC846889db970665a9365bbd6dbcc9449b';
		$TWILIO_API_KEY = 'SK8619bec36089099af92be140c8502292';
		$TWILIO_API_SECRET = 'LXNqiKEnxtqbXzcHGHlgWvJvIVJLILwM';

		if($bookingid){

			$model = $this->twilioDataFactory->create()->load($bookingid,'order_id');
			if(!empty($model->getData())){
				$identity =  rand().$model->getIdentityVet();
				
				$token = new AccessToken($TWILIO_ACCOUNT_SID, $TWILIO_API_KEY, $TWILIO_API_SECRET, 1800, $identity);
				$videoGrant = new VideoGrant();
				$videoGrant->setRoom($model->getRoomName());
				$token->addGrant($videoGrant);
				$videotoken = $token->toJWT();
				$model->setTokenVet($videotoken);
				$model->save();
				$response = ['token'=>$videotoken,'room'=>$model->getRoomName(),'id'=>$identity,
				'success' => true,'message' => 'Token found successfully'];
			}else{
				$twilioDataModel = $this->twilioDataFactory->create();
				$groomOrder = $this->groomingFactory->create()->load($bookingid);

				$identity =  rand().$groomOrder->getGroomerId();
				$twilioClient = new Client($TWILIO_API_KEY, $TWILIO_API_SECRET);
				$roomName = $this->createRoom($twilioClient, $groomOrder);
				$token = new AccessToken($TWILIO_ACCOUNT_SID, $TWILIO_API_KEY, $TWILIO_API_SECRET, 1800, $identity);
				$videoGrant = new VideoGrant();
				$videoGrant->setRoom($roomName);
				$token->addGrant($videoGrant);
				$videotoken = $token->toJWT();
				$twilioDataModel->setData('token_vet',$videotoken);
				$twilioDataModel->setData('identity_vet', $identity);
				$twilioDataModel->setData('room_name',$roomName);
				$twilioDataModel->setData('order_id',$groomOrder->getId());
				$twilioDataModel->setData('status', 0);
				$twilioDataModel->save();
				$response = ['token'=>$videotoken,'room'=>$roomName,'id'=>$identity,
				'success' => true,'message' => 'Token found successfully'];
			}
		}
		else{
			$response = ['success' => false,'message' => 'Enter a Booking Id'];
		}		 
		 $resultJson->setData($response);
		 return json_encode($response);	
	}
	
	public function createRoom($twilioClient, $groomOrder ){
		$roomName = 'TwilioRoom'.rand().$groomOrder->getId();
		$room = $twilioClient->video->rooms->create([
				'uniqueName' => $roomName,
				'type' => 'peer-to-peer',
				'enableTurn' => false,
				'Duration'   => 1800,
				'maxParticipants'  => 2,
				'maxParticipantDuration'=> 1800
			]);
		return $roomName;
	}
}
