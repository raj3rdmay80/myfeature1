<?php 

namespace Zigly\MobileAPIs\Model;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\CustomerFactory;
use Zigly\MobileAPIs\Helper\Email;
use Zigly\GroomingService\Model\GroomingFactory;
use Zigly\Groomer\Model\GroomerFactory as ProfessionalFactory;

class PrescribedMail {

	private $helperEmail;
	
	public function __construct(
		JsonFactory $resultJsonFactory,
        ScopeConfigInterface $scopeConfig,
		CustomerFactory $customerFactory,
		Email $helperEmail,
		GroomingFactory $groomFactory,
		ProfessionalFactory $professionalFactory,
		\Magento\Framework\Webapi\Rest\Request $request
    ) {
		$this->resultJsonFactory = $resultJsonFactory;
		$this->scopeConfig = $scopeConfig;
		$this->customerFactory = $customerFactory;
		$this->helperEmail = $helperEmail;
		$this->groomFactory = $groomFactory;
		$this->professionalFactory = $professionalFactory;
		$this->request = $request;
    }
	 /**
     * Customer sendMail
     *
     * @return array
     */
    public function sendMail()
    {
		$requestdata = $this->request->getParams();
		$requestfiledata = $this->request->getFiles(); 
		$response = array();
		$resultJson = $this->resultJsonFactory->create();
		try{
			if(isset($requestdata['customerId']) && isset($requestfiledata['prescription'])){
				$customerData = $this->customerFactory->create()->load($requestdata['customerId']);
				$customerEmail = $customerData->getEmail();
				$customerName = $customerData->getFirstname().' '.$customerData->getLastname();
				$mailResponse = $this->helperEmail->sendEmail($customerEmail, $customerName, $requestfiledata);
				$custSmsResponse = '';
				if(isset($requestdata['serviceId']) && !empty($requestdata['serviceId'])){
					/*Send prescription sms*/
					$groomData = $this->groomFactory->create()->load($requestdata['serviceId']);
					$vetId = $groomData->getGroomerId();
					$professionalData = $this->professionalFactory->create()->load($vetId);
	                $vetCustomerBook['appointment_date'] = $groomData->getScheduledDate();
	                $vetCustomerBook['appointment_time'] = $groomData->getScheduledTime();
	                $vetCustomerBook['vet_name'] = $professionalData->getName();
	                $vetCustomerBook['mobileNo'] = $customerData->getPhoneNumber();
	                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
	                $bitly = $this->scopeConfig->getValue('groomingservice/feedback/feedback_bitly_url', $storeScope);
	                $vetCustomerBook['url'] = !empty($bitly) ? $bitly : '';
	                $custSmsResponse = $this->helperEmail->sendSms($vetCustomerBook);
	                /*Send prescription sms*/
	            }
				if ($mailResponse && empty($custSmsResponse)){
					$response = ['success' => true,'message' => 'Prescription mail sent successfully'];
				} elseif ($mailResponse && !empty($custSmsResponse)){
					$response = ['success' => true,'message' => 'Prescription mail and sms sent successfully'];
				} else {
					$response = ['success' => false,'message' => 'Mail and Sms not sent'];
				}
			} else {
				$response = ['success' => false,'message' => 'Send customer ID/Prescription'];
			}
				
		} catch (NoSuchEntityException $e) {
			$response['message'] = $e->getMessage();
			$response['status'] = false;
        } catch (InputException $e) {
            $response['message'] = $e->getMessage();
			$response['status'] = false;
        }
		$resultJson->setData($response);
		return json_encode($response);
    }
	
}