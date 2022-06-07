<?php 

namespace Webkul\MobikulApi\Controller\Checkout;

use Magento\Quote\Model\QuoteFactory;
use Webkul\MobikulCore\Helper\Data as HelperData;
use Webkul\MobikulCore\Model\OauthTokenFactory as TokenModelFactory;
use Magento\Quote\Model\QuoteRepository;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\AddressFactory;

class Applywallet extends \Magento\Framework\App\Action\Action 
{

	private $helperEmail;
	private $tokenModelFactory;

	/**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

	public function __construct(
	    Context $context,
		QuoteRepository $quoteRepository,
		QuoteFactory $QuoteFactory,
		SerializerInterface $serializer,
        TokenModelFactory $tokenModelFactory,
		HelperData $helper,
		CustomerFactory $customerFactory,
		AddressFactory $addressFactory
    ) {
		$this->QuoteFactory = $QuoteFactory;
        $this->quoteRepository = $quoteRepository;
		$this->serializer = $serializer;
		$this->tokenModelFactory = $tokenModelFactory;
		$this->helper = $helper;
		$this->customerFactory = $customerFactory;
		$this->addressFactory = $addressFactory;
		parent::__construct($context);

    }
	 /**
     * Customer walletDetails
     *
     * @return array
     */
    public function execute()
    {
		$requestdata = $this->getRequest()->getParams();
		$response = array();
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
		try{
			if(isset($requestdata['customer_token']) && isset($requestdata['is_checked']) ){
				$customerToken = $requestdata['customer_token'];
				$customerData = $this->tokenModelFactory->create()->load($customerToken,'token');
				$customerId = $customerData['customer_id'];
				//$this->tokenModelFactory->create()->loadByToken($customerToken)->getCustomerId();
				if(isset($customerId) && $customerToken != ""){
					$_quote = $this->helper->getCustomerQuote($customerId); 
					//$quote = $this->QuoteFactory->create()->load($quote->getId());
					$quote = $this->quoteRepository->get($_quote->getId());
					$quote->getShippingAddress()->setCollectShippingRates(true);

					if (!empty($quote->getZwallet())) {
						$zwallet = $this->serializer->unserialize($quote->getZwallet());
					} else {
						$zwallet = [];
					}
					// $customer = $this->customerFactory->create()->load($customerId);
					// $shippingAddressId = $customer->getDefaultShipping();
					// $shippingAddress = $this->addressFactory->create()->load($shippingAddressId);
					// $shippingAddress->save();

					// $customer = $this->customerSession->create()->getCustomer();
					// $totalBalance = is_null($customer->getWalletBalance()) ? "0" : $customer->getWalletBalance();

					if (isset($requestdata['is_checked']) && $requestdata['is_checked'] == 'true') {
						$zwallet['applied'] = true;
					} elseif (isset($requestdata['is_checked']) && $requestdata['is_checked'] == 'false') {
						$zwallet['applied'] = false;
					}
					$zwallet = $this->serializer->serialize($zwallet);
					$quote->setZwallet($zwallet);
					$quote->setCgstCharge(floatval($quote->getCgstCharge()));
					$quote->setSgstCharge(floatval($quote->getSgstCharge()));
					$quote->setIgstCharge(floatval($quote->getIgstCharge()));
					$quote->setDataChanges(true);
					$quote->collectTotals();
					//$quote->save();	
           			$this->quoteRepository->save($quote);
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
		return $resultJson;
    }
}
