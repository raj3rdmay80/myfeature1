<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
namespace Zigly\VideoIntegrate\Controller\Video;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\OrderFactory;
use Zigly\VideoIntegrate\Model\TwilioDataFactory;
use Zigly\GroomingService\Model\GroomingFactory;

class Joinroom extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
	
	protected $orderFactory;
	
	protected $groomingFactory;

	protected $twilioDataFactory;
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
		TwilioDataFactory $twilioDataFactory,
        PageFactory $resultPageFactory,
		OrderFactory $orderFactory,
		GroomingFactory $groomingFactory,
		\Magento\Framework\View\LayoutInterface $layout

    ) {
		$this->orderFactory = $orderFactory;
		$this->twilioDataFactory = $twilioDataFactory;
        $this->resultPageFactory = $resultPageFactory;
		$this->groomingFactory = $groomingFactory;
		$this->_layout = $layout;
        parent::__construct($context);
    }
	
	/**
	* @return json object
	*/
    public function execute()
    {
		$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
		$groomerId = $this->getRequest()->getParam('grooming_id');
		$groomOrder = $this->groomingFactory->create()->load($groomerId);
		$model = $this->twilioDataFactory->create()->load($groomerId,'order_id');
		$roomName = $model->getRoomName();		
		$response = array();
		try{
			if(!empty($model->getData()) && $roomName != ""){
				//if(!$model->getTokenCustomer() && !$model->getIdentityCustomer()){
					$TWILIO_ACCOUNT_SID = 'AC846889db970665a9365bbd6dbcc9449b';
					$TWILIO_API_KEY = 'SK8619bec36089099af92be140c8502292';
					$TWILIO_API_SECRET = 'LXNqiKEnxtqbXzcHGHlgWvJvIVJLILwM';
					$identity =  $groomOrder->getId().$groomOrder->getCustomerId();
					
					$token = new AccessToken($TWILIO_ACCOUNT_SID, $TWILIO_API_KEY, $TWILIO_API_SECRET, 14400, $identity);
					$videoGrant = new VideoGrant();
					$videoGrant->setRoom($roomName);
					$token->addGrant($videoGrant);
					$model->setData('token_customer',$token->toJWT());
					$model->setData('identity_customer',$identity);
					$model->save();
					$response['id'] = $identity;
					$response['token'] = $token->toJWT();
					$response['status'] = true;
					$response['roomname'] = $roomName;
				/*}else{
					$response['id'] = $model->getIdentityCustomer();
					$response['token'] = $model->getTokenCustomer();
					$response['status'] = true;
					$response['roomname'] = $roomName;
				}*/
			}else{
				$response['status'] = false;
				$response['message'] = 'Room not created';
			}
				
	 	}catch(Exception $e){
			$response['message'] = $e->getMessage();
			$response['status'] = false;
		}
		$resultJson->setData($response);
     	return $resultJson;
    }
}
