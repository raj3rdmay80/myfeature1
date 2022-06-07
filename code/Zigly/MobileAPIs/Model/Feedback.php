<?php 

namespace Zigly\MobileAPIs\Model;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Zigly\GroomingService\Model\GroomingFactory;
use Zigly\VetConsulting\Helper\NotifyVet;
use Zigly\ScheduleManagement\Model\ScheduleManagementFactory;
use Zigly\ScheduleManagement\Model\ResourceModel\ScheduleManagement\CollectionFactory;
use Zigly\Groomer\Model\GroomerFactory as ProfessionalFactory;

class Feedback {
	
	protected $orderManagement;
	protected $orderFactory;

	public function __construct(
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		JsonFactory $resultJsonFactory,
		GroomingFactory $groomingFactory,
		OrderManagementInterface $orderManagement,
        ScopeConfigInterface $scopeConfig,
		CollectionFactory $collectionFactory,
		NotifyVet $notifyVet,
        ProfessionalFactory $professionalFactory,
        ScheduleManagementFactory $scheduleManagementModelFactory
    ) {
		$this->orderRepository = $orderRepository;
		$this->groomingFactory = $groomingFactory;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->orderManagement = $orderManagement;
		$this->scopeConfig = $scopeConfig;
		$this->notifyVet = $notifyVet;
        $this->collectionFactory = $collectionFactory;
        $this->scheduleManagementModelFactory = $scheduleManagementModelFactory;
        $this->professionalFactory = $professionalFactory;
    }
	 /**
     * Customer feedbackMessage
     *
     * @param int $id
     * @return array
     */
    public function feedbackMessage($id)
    {
		$response = array();
		$resultJson = $this->resultJsonFactory->create();
		try{
			$booking = $this->groomingFactory->create()->load($id);
			if (($booking->getBookingStatus() == 'Completed') || ($booking->getBookingStatus() == 'Cancelled by Professional')|| ($booking->getBookingStatus() == 'Cancelled by Customer')) {
				$professional = $this->professionalFactory->create()->load($booking->getGroomerId());
				$booking = $this->groomingFactory->create()->load($id);
				$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
				$bitly = $this->scopeConfig->getValue('groomingservice/feedback/feedback_bitly_url', $storeScope);
				$consultVar['mobileNo'] = $booking->getPhoneNo();
				$consultVar['url'] = !empty($bitly) ? $bitly : '';
				$cancelVetBook['pet_name'] = $booking->getPetName();
				$cancelVetBook['url'] = !empty($bitly) ? $bitly : '';
				$cancelVetBook['mobileNo'] = $booking->getPhoneNo();
				$cancelVetBook['vet'] = $professional->getName();
				$cancelVetBook['date'] = $booking->getScheduledDate();
				if ($booking->getBookingStatus() == 'Cancelled by Professional') {
					if($this->notifyVet->sendCancelSms($cancelVetBook)){
						$this->reschedule($booking->getEntityId());
						$response = ['success' => true,'message' => 'Message sent successfully'];
					}
				} elseif ($booking->getBookingStatus() == 'Cancelled by Customer') {
					$customerCancelVetBook['pet_name'] = $booking->getPetName();
					$customerCancelVetBook['date'] = $booking->getScheduledDate();
					$customerCancelVetBook['mobileNo'] = $professional->getPhoneNumber();
					if($this->notifyVet->sendCancelVetSms($customerCancelVetBook)){
						$this->reschedule($booking->getEntityId());
						$response = ['success' => true,'message' => 'Message sent successfully'];
					}
				} elseif ($booking->getBookingStatus() == 'Completed') {
					if($this->notifyVet->sendFeedbackConsultation($consultVar)){
						$response = ['success' => true,'message' => 'Message sent successfully'];
					}
				}
			}
			else{
				$response = ['success' => false,'message' => "Booking is not Complete or Cancelled by Professional"];
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

    public function reschedule($bookingId)
    {
    	$reschedule = $this->collectionFactory->create()->addFieldToFilter('booking_id', $bookingId);
        if (count($reschedule->getData())){
            $rescheduleModel = $this->scheduleManagementModelFactory->create();
            $rescheduleModel->load($reschedule->getData()[0]['schedulemanagement_id']);
            $rescheduleModel->setBookingId(0);
            $rescheduleModel->save();
            return true;
        }
    }
}
