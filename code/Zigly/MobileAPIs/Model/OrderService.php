<?php 
namespace Zigly\MobileAPIs\Model;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\SerializerInterface;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Webkul\MobikulCore\Model\OauthToken;

class OrderService {

	protected $orderManagement;

	protected $orderFactory;

	public function __construct(
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		JsonFactory $resultJsonFactory,
		OrderFactory $orderFactory,
		OrderManagementInterface $orderManagement,
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        WalletFactory $walletFactory,
        Customer $customer,
        CustomerFactory $customerFactory,
        OauthToken $mobiToken
    ) {
		$this->orderRepository = $orderRepository;
		$this->orderFactory = $orderFactory;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->orderManagement = $orderManagement;
		$this->scopeConfig = $scopeConfig;
		$this->serializer = $serializer;
        $this->walletFactory = $walletFactory;
        $this->customer = $customer;
        $this->customerFactory = $customerFactory;
        $this->mobiToken = $mobiToken;
    }

	 /**
     * Order orderCancel
     *
     * @param int $id
 	 * @param string $token
 	 * @param string $cancelMessage (optional)
     * @return array
     */
    public function orderCancel($id, $token, $cancelMessage = null)
    {
		$response = array();
		$resultJson = $this->resultJsonFactory->create();
		try{
			if (empty($token)) {
				$response['status'] = false;
	            $response['message'] = "Token is required";
				$resultJson->setData($response);
				return json_encode($response);
	        }
	        $customerId = $this->getCustomerByToken($token);
	        if (empty($customerId)) {
				$response['status'] = false;
	            $response['message'] = "Invalid token";
				$resultJson->setData($response);
				return json_encode($response);
	        }
			//$order = $this->orderRepository->get($id);
			$order = $this->orderFactory->create()->load($id);
			if ($order->getStatus() != 'canceled') {
				if (!empty($order->getZwallet())){
					$zWallet = $this->serializer->unserialize($order->getZwallet());
					if ($zWallet['applied'] == true) { 
                        $total = $zWallet['spend_amount'];
						$model = $this->walletFactory->create();
                        $data['comment'] = "Order ".$order->getIncrementId()." Cancellation Refund";
                        $data['amount'] = $total;
                        $data['flag'] = 1;
                        $data['performed_by'] = "customer";
                        $data['visibility'] = 1;
                        $data['customer_id'] = $order->getCustomerId();
                        $model->setData($data);
                        $model->save();
                        $customerModel = $this->customer->load($order->getCustomerId());
                        $customerData = $customerModel->getDataModel();
                        $totalBalance = is_null($customerModel->getWalletBalance()) ? "0" : $customerModel->getWalletBalance();
                        $balance = $totalBalance + $total;
                        $customerData->setCustomAttribute('wallet_balance',$balance);
                        $customerModel->updateData($customerData);
                        $customerResource = $this->customerFactory->create();
                        $customerResource->saveAttribute($customerModel, 'wallet_balance');
					}
				}
				//$this->orderManagement->cancel($order->getId());
				$order->setData('state', "canceled");
				$order->setStatus("canceled");
				$history = $order->addStatusHistoryComment("Order is cancelled by Api ".$cancelMessage, false);
				$history->setIsCustomerNotified(true);
				$order->save();
				$response = ['success' => true,'message' => 'Order cancelled successfully'];
			} else{
				$response = ['success' => false,'message' => "Order cannot be cancelled"];
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
 	 * @param string $token
	 * @param string $storeid (optional)
     * @return array
     */
	public function cancelReason($token, $storeid = null)
    {
		//echo "fdfdf"; exit;
		$response = array();
		$resultJson = $this->resultJsonFactory->create();
		try{
			if (empty($token)) {
				$response['status'] = false;
	            $response['message'] = "Token is required";
				$resultJson->setData($response);
				return json_encode($response);
	        }
	        $customerId = $this->getCustomerByToken($token);
	        if (empty($customerId)) {
				$response['status'] = false;
	            $response['message'] = "Invalid token";
				$resultJson->setData($response);
				return json_encode($response);
	        }
			$value = $this->scopeConfig->getValue('order/order_cancel_reason_config/cancelreasons', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			if (empty($value)) {
				$response['message'] = "No reason found";
				$response['status'] = false;
			} else{
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
		return json_encode($response);
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

    /**
     * Function to get customer id by provided token
     *
     * @param string $token
     * @return integer
     */
    public function getCustomerByToken($token)
    {
    	$token = $this->mobiToken->loadByToken($token)->getCustomerId();
    	return $token;
    }
}