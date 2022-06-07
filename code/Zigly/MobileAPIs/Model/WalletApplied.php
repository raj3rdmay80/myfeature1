<?php 



namespace Zigly\MobileAPIs\Model;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Quote\Model\QuoteFactory;
use Webkul\MobikulCore\Helper\Data as HelperData;
use Zigly\Wallet\Model\WalletFactory;
use Webkul\MobikulCore\Model\OauthTokenFactory as TokenModelFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Serialize\SerializerInterface;


class WalletApplied {

	private $helperEmail;
	private $tokenModelFactory;
	protected $customerFactory;
	protected $request;
	/**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

	public function __construct(
		JsonFactory $resultJsonFactory,
		WalletFactory $walletFactory,
		\Magento\Framework\Webapi\Rest\Request $request,
		\Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory,
		CartRepositoryInterface $quoteRepository,
		QuoteFactory $QuoteFactory,
		CustomerFactory $customerFactory,
		SerializerInterface $serializer,
        TokenModelFactory $tokenModelFactory,
		HelperData $helper
    ) {
		$this->resultJsonFactory = $resultJsonFactory;
		$this->walletFactory = $walletFactory;
		$this->request = $request;
		$this->customerResourceFactory = $customerResourceFactory;
		$this->QuoteFactory = $QuoteFactory;
        $this->quoteRepository = $quoteRepository;
		$this->customerFactory = $customerFactory;
		$this->serializer = $serializer;
		$this->tokenModelFactory = $tokenModelFactory;
		$this->helper = $helper;
    }
	 /**
     * Customer walletDetails
     *
     * @return array
     */
    public function iswalletapplied()
    {
		$requestdata = $this->request->getParams();
		$response = array();
		$resultJson = $this->resultJsonFactory->create();
		try{
			if(isset($requestdata['customer_token']) && isset($requestdata['is_checked']) ){
				$customerToken = $requestdata['customer_token'];
				$customerData = $this->tokenModelFactory->create()->load($customerToken,'token');
				$customerId = $customerData['customer_id'];
				//$this->tokenModelFactory->create()->loadByToken($customerToken)->getCustomerId();
				if(isset($customerId) && $customerToken != ""){
					$quote = $this->helper->getCustomerQuote($customerId); 
					$quote = $this->QuoteFactory->create()->load($quote->getId());
	
					//$quote = $this->quoteRepository->get($quote->getId());
					$quote->getShippingAddress()->setCollectShippingRates(true);

					if (!empty($quote->getZwallet())) {
						$zwallet = $this->serializer->unserialize($quote->getZwallet());
					} else {
						$zwallet = [];
					}


					// $customer = $this->customerSession->create()->getCustomer();
					// $totalBalance = is_null($customer->getWalletBalance()) ? "0" : $customer->getWalletBalance();

					if (isset($requestdata['is_checked']) && $requestdata['is_checked'] == 'true') {
						$zwallet['applied'] = true;
					} elseif (isset($requestdata['is_checked']) && $requestdata['is_checked'] == 'false') {
						$zwallet['applied'] = false;
					}
					$zwallet = $this->serializer->serialize($zwallet);
					$quote->setZwallet($zwallet);
					$quote->setDataChanges(true);
					$quote->collectTotals();
					$quote->save();	

					$response['success'] = true;
					$response['cart_details'] = $quote->getData();
					$response['cart_details']['zwallet'] =  $this->serializer->unserialize($quote->getZwallet());
					$response['message'] = "Successfully applied.";
				}else{
				$response = ['success' => false,'message' => 'No customer found'];
				}
			}else{
				$response = ['success' => false,'message' => 'Incorrect authentication token'];
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
