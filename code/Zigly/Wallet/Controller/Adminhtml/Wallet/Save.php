<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Controller\Adminhtml\Wallet;

use Magento\Customer\Model\Customer;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\AuthorizationInterface;

class Save extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /** 
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request 
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * Constructor
     *
     * @param Context  $context
     * @param Customer $customer
     * @param WalletFactory $walletFactory
     * @param JsonFactory $jsonResultFactory
     * @param CustomerFactory $customerFactory
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        Context $context,
        Customer $customer,
        WalletFactory $walletFactory,
        JsonFactory $jsonResultFactory,
        CustomerFactory $customerFactory,
        AuthorizationInterface $authorization
    ) {
        $this->customer = $customer;
        $this->walletFactory = $walletFactory;
        $this->customerFactory = $customerFactory;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->authorization = $authorization;
        parent::__construct($context);
    }

    /**
     * Execute save action
     * @return void
     */
    public function execute()
    {
        $amountData = $this->getRequest()->getPostValue();
        $result = $this->jsonResultFactory->create();
        $responseData = [
            'success' => false,
            'message' => 'Something went wrong. Please reload and try again'
        ];
        if ($this->authorization->isAllowed('Zigly_Wallet::Wallet_Add')) {
            try {
                $model = $this->walletFactory->create();
                $data['comment'] = $amountData['comment'];
                $data['amount'] = $amountData['amount'];
                $data['flag'] = $amountData['flag'];
                $data['performed_by'] = "admin";
                $data['visibility'] = 1;
                $data['customer_id'] = $amountData['customerid'];
                $model->setData($data);
                if ($model) {
                    $customerId = $amountData['customerid'];
                    $customer = $this->customer->load($customerId);
                    $customerData = $customer->getDataModel();
                    $totalBalance = is_null($customer->getWalletBalance()) ? "0" : $customer->getWalletBalance();
                    if ($model->getFlag() == 1) {
                        $balance = $model->getAmount() + $totalBalance;
                    } else {
                        $amount = $totalBalance - $model->getAmount();
                        $balance = ($amount <= 0) ? 0 : $amount;
                        $balances = ($model->getAmount() >= $totalBalance) ? $totalBalance : $balance;
                        $model->setAmount($balances);
                    }
                    $model->save();
                    $customerData->setCustomAttribute('wallet_balance',$balance);
                    $customer->updateData($customerData);
                    $customerResource = $this->customerFactory->create();
                    $customerResource->saveAttribute($customer, 'wallet_balance');
                    $responseData['success'] = true;
                    $responseData['message'] = 'Recharged Successfully';
                }
            } catch (\Exception $e) {
                $responseData['trace'] = $e->getMessage();
            }
        }
        $result->setData($responseData);
        return $result;
    }
}