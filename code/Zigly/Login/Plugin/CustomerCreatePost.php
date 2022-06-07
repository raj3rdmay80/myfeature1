<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Plugin;

use Magento\Customer\Controller\Account\CreatePost;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CusCollectFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\PhpEnvironment\Response;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Mageplaza\CustomerApproval\Helper\Data as HelperData;
use Mageplaza\CustomerApproval\Model\Config\Source\AttributeOptions;
use Mageplaza\CustomerApproval\Model\Config\Source\TypeAction;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class CustomerCreatePost
 *
 * @package Mageplaza\CustomerApproval\Plugin
 */
class CustomerCreatePost
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ResponseFactory
     */
    private $response;

    /**
     * @var CusCollectFactory
     */
    protected $cusCollectFactory;

    /**
     * CustomerCreatePost constructor.
     *
     * @param HelperData $helperData
     * @param ManagerInterface $messageManager
     * @param RedirectFactory $resultRedirectFactory
     * @param RedirectInterface $redirect
     * @param Session $customerSession
     * @param ResponseFactory $responseFactory
     * @param CusCollectFactory $cusCollectFactory
     */
    public function __construct(
        HelperData $helperData,
        ManagerInterface $messageManager,
        RedirectFactory $resultRedirectFactory,
        RedirectInterface $redirect,
        Session $customerSession,
        ResponseFactory $responseFactory,
        CusCollectFactory $cusCollectFactory
    ) {
        $this->helperData = $helperData;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->redirect = $redirect;
        $this->customerSession = $customerSession;
        $this->response = $responseFactory;
        $this->cusCollectFactory = $cusCollectFactory;
    }

    /**
     * @param CreatePost $createPost
     *
     * @return mixed
     * @throws FailureToSendException
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforeExecute(CreatePost $createPost)
    {
        $request = $createPost->getRequest();
        $phoneNumberPost = $request->getParam('phone_number');

        if (!isset($phoneNumberPost) || empty($phoneNumberPost) || !is_numeric($phoneNumberPost) || strlen($phoneNumberPost) > 10 || strlen($phoneNumberPost) < 10)
        {
            throw new CouldNotSaveException(__('Please enter valid phone number.'));
        }

        return $createPost;
    }
}
