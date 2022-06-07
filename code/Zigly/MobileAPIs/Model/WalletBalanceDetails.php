<?php 



namespace Zigly\MobileAPIs\Model;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\CustomerFactory;
use Zigly\MobileAPIs\Helper\Email;
use Zigly\Wallet\Model\ResourceModel\Wallet\CollectionFactory;
use Zigly\Wallet\Model\WalletFactory;
use Webkul\MobikulCore\Model\OauthTokenFactory as TokenModelFactory;


class WalletBalanceDetails {

	private $helperEmail;
	private $tokenModelFactory;
	protected $collectionFactory;
	protected $customerFactory;
	protected $request;

	public function __construct(
		JsonFactory $resultJsonFactory,
		CollectionFactory $collectionFactory,
		WalletFactory $walletFactory,
		\Magento\Framework\Webapi\Rest\Request $request,
		\Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		CustomerFactory $customerFactory,
        TokenModelFactory $tokenModelFactory
    ) {
		$this->resultJsonFactory = $resultJsonFactory;
		$this->collectionFactory = $collectionFactory;
		$this->walletFactory = $walletFactory;
		$this->request = $request;
		$this->customerResourceFactory = $customerResourceFactory;
		$this->scopeConfig = $scopeConfig;
		$this->customerFactory = $customerFactory;
		$this->tokenModelFactory = $tokenModelFactory;
    }
	 /**
     * Customer walletDetails
     *
     * @return array
     */
    public function walletDetails()
    {
		$requestdata = $this->request->getParams();
		$customerData = $this->tokenModelFactory->create()->load($requestdata['customer_token'],'token');
		$response = array();
		$resultJson = $this->resultJsonFactory->create();
		try{
			if(isset($customerData)){
				$customerId = $customerData['customer_id'];
				if($customerId){
					$customer = $this->customerFactory->create()->load($customerId);
					if($customer->getId()){
						$transactionCollection = $this->getPastTransactions($customer);
						$totalBalance = $this->getTotalBalance($customer);
						$transactionHistory = $transactionCollection->getData();
						if($transactionCollection->getData()){
							$response = ['success' => true,'message' => 'Wallet Details Sent Successfully','Total Balance' => $totalBalance,'Transaction History' => $transactionHistory ];
						}
						else {
							$response = ['success' => false,'message' => 'Wallet Details Not Sent Successfully'];
						}
					}else{
						$response = ['success' => false,'message' => 'Customer does not exist'];
					}
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
	/**
     * Customer walletStatus.
     *
     * @return array
     */
	public function walletStatus(){
		$enable = $this->scopeConfig->getValue(
            'wallet/general/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		if($enable){
			$response = ['success' => true,'status' => true];
		}else{
			$response = ['success' => true,'status' => false];
		}
		return json_encode($response);
	}	
	    /*
    * get past transaction
    */
    public function getPastTransactions($customer)
    {
		$customerId = $customer->getId();
        $transactionCollection = [];
        if ($customerId) {
            //$page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
            //$pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 10;
            $transactionCollection = $this->collectionFactory->create()->addFieldToFilter('customer_id', ['in' => $customerId])->addFieldToFilter('visibility', ['eq' => 1])->setOrder('wallet_id', 'DESC');//->setPageSize($pageSize)->setCurPage($page);
        }
        return $transactionCollection;
    }

    /*
    * get total balance
    */
    public function getTotalBalance($customer)
    {
        $totalBalance = $customer->getWalletBalance();
        $totalBalance = is_null($totalBalance) ? "0" : $totalBalance;
		
        return $totalBalance;
    }
	/**
     * Customer addMoney.
	 *
     * @return array
     */
	public function addMoney()
	{
		$requestdata = $this->request->getParams();
		$customerData = $this->tokenModelFactory->create()->load($requestdata['customer_token'],'token');
		$amount = $requestdata['amount']; 
		$transactionType = $requestdata['transactionType'];
		$comment = $requestdata['comment'];
		$amountData = $amount;
		$result = $this->resultJsonFactory->create();
		//if ($this->authorization->isAllowed('Zigly_Wallet::Wallet_Add')) {
			try{
				if(isset($customerData)){
					$customerId = $customerData['customer_id']; 
					$model = $this->walletFactory->create();
					$data['comment'] = $comment;
					$data['amount'] = $amountData;
					if($transactionType == 'Credit'){
						$data['flag'] = 1;
					}else{
						$data['flag'] = 0;
					}
					$data['performed_by'] = "admin";
					$data['visibility'] = 1;
					$data['customer_id'] = $customerId;
					$model->setData($data);
					if ($model) {
						$customer = $this->customerFactory->create()->load($customerId);
						$customerData = $customer->getDataModel();
						$totalBalance = is_null($customer->getWalletBalance()) ? "0" : $customer->getWalletBalance();
						if ($model->getFlag() == 1) {
							$balance = $model->getAmount() + $totalBalance;
						} else {
							if($totalBalance >= $model->getAmount()){
								$amount = $totalBalance - $model->getAmount();
								$balance = ($amount <= 0) ? 0 : $amount;
								$balances = ($model->getAmount() >= $totalBalance) ? $totalBalance : $balance;
								$model->setAmount($balances);
							}
						}
						$model->save();
						$customerData->setCustomAttribute('wallet_balance',$balance);
						$customer->updateData($customerData);
						$customerResource = $this->customerResourceFactory->create();
						$customerResource->saveAttribute($customer, 'wallet_balance');
						$responseData['success'] = true;
						$responseData['message'] = 'Recharged Successfully';
					}else{
						$responseData = [
							'success' => false,
							'message' => 'Something went wrong. Please reload and try again'
						];
					}
				}else{
					$response = ['success' => false,'message' => 'Incorrect authentication token'];
				}
			} catch (NoSuchEntityException $e) {
				$responseData['message'] = $e->getMessage();
				$responseData['status'] = false;
			} catch (InputException $e) {
				$responseData['message'] = $e->getMessage();
				$responseData['status'] = false;
			}
		//}
		$result->setData($responseData);
		return json_encode($responseData);
	}
}
