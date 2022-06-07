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

class BookingStatus {

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
        $this->professionalFactory = $professionalFactory;
        $this->collectionFactory = $collectionFactory;
        $this->scheduleManagementModelFactory = $scheduleManagementModelFactory;
    }
	 /**
     * Booking bookingCancel
     *
     * @param int $id
 	 * @param string $cancelReason (optional)
	 * @param string $updateStatus
     * @return array
     */
    public function bookingCancel($id, $updateStatus, $cancelReason = null)
    {
		$response = array();
		$resultJson = $this->resultJsonFactory->create();
		try{
			$booking = $this->groomingFactory->create()->load($id);
			$callStatus = ['Scheduled', 'Rescheduled by Admin', 'Rescheduled by Customer', 'Rescheduled by Professional'];
			if (in_array($booking->getBookingStatus(), $callStatus)) {
				$booking->setBookingStatus($updateStatus);
				$booking->setComment($cancelReason);
				$booking->save();
				$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
				$bitly = $this->scopeConfig->getValue('groomingservice/feedback/feedback_bitly_url', $storeScope);
				$professional = $this->professionalFactory->create()->load($booking->getGroomerId());
				$cancelVetBook['pet_name'] = $booking->getPetName();
				$cancelVetBook['url'] = !empty($bitly) ? $bitly : '';
				$cancelVetBook['mobileNo'] = $booking->getPhoneNo();
				$cancelVetBook['vet'] = $professional->getName();
				$cancelVetBook['date'] = $booking->getScheduledDate();
				if ($booking->getBookingStatus() == 'Cancelled by Professional') {
					$this->notifyVet->sendCancelSms($cancelVetBook);
				} elseif ($booking->getBookingStatus() == 'Cancelled by Customer') {
					$customerCancelVetBook['pet_name'] = $booking->getPetName();
					$customerCancelVetBook['date'] = $booking->getScheduledDate();
					$customerCancelVetBook['mobileNo'] = $professional->getPhoneNumber();
					$this->notifyVet->sendCancelVetSms($customerCancelVetBook);
				}
				$reschedule = $this->collectionFactory->create()->addFieldToFilter('booking_id', $booking->getEntityId());
                if (count($reschedule->getData())){
                    $rescheduleModel = $this->scheduleManagementModelFactory->create();
                    $rescheduleModel->load($reschedule->getData()[0]['schedulemanagement_id']);
                    $rescheduleModel->setBookingId(0);
                    $rescheduleModel->save();
                }
				$response = ['success' => true,'message' => 'Booking cancelled successfully'];
			}
			else{
				$response = ['success' => false,'message' => "Booking cannot be cancelled"];
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
	/**
     * Reason for cancelling order.
     *
	 * @param string $storeid (optional)
     * @return array
     */
	public function bookingcancelReason($storeid = null)
    {
		$response = array();
		$resultJson = $this->resultJsonFactory->create();
		try{
			$value = $this->scopeConfig->getValue('order/order_cancel_reason_config/cancelreasons', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			if (empty($value)) {
				 $response['message'] = "No reason found";
				$response['status'] = false;
			}
			else{
			    if ($this->isSerialized($value)) {
					$unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Unserialize\Unserialize::class);
				} else {
					$unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Serialize\Serializer\Json::class);
				}
				$data = $unserializer->unserialize($value);
				$reason = [];
				foreach ($data as $key => $reas) {
					$reason[] = $reas['cancelreason'];
				}
				$response = ['success' => true,'reason' => $reason];
			}
		} catch (NoSuchEntityException $e) {
			$response['message'] = $e->getMessage();
			$response['status'] = false;
        } catch (InputException $e) {
            $response['message'] = $e->getMessage();
			$response['status'] = false;
        }
		$resultJson->setData($response);
		return json_encode($response);;
    }

        /**
     * Check if value is a serialized string
     *
     * @param string $value
     * @return boolean
     */
    private function isSerialized($value)
    {
        return (boolean) preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }
}
