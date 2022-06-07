<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Grooming;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Zigly\GroomingService\Model\Session;
use Magento\Customer\Model\AddressFactory;

class Review extends \Magento\Framework\App\Action\Action
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
     * @var Session
     */
    protected $groomingSession;

    /**
     * @var AddressFactory
     */
    protected $address;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param AddressFactory $address
     * @param JsonFactory $jsonResultFactory
     * @param PageFactory $resultPageFactory
     * @param Session $groomingSession
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        PageFactory $resultPageFactory,
        AddressFactory $address,
        Session $groomingSession
    ) {
        $this->address = $address;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->groomingSession = $groomingSession;
        parent::__construct($context);
    }

    /**
     * Execute save action
     * @return void
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $result = $this->jsonResultFactory->create();
        $resultPage = $this->resultPageFactory->create();
        $address = '';
        $groomSession = $this->groomingSession->getGroomService();
        if (!empty($post['address_id'])) {
            $groomSession['address_id'] = $post['address_id'];
            $this->groomingSession->setGroomService($groomSession);
        }
        if (!empty($groomSession['address_id'])) {
            $shippingAddress = $this->address->create()->load($groomSession['address_id']);
            $street = $shippingAddress->getStreet();
            $address = $street['0'];
            if (isset($street['1'])) {
                $address .= ", ".$street['1'];
            }
        }

        $block = $resultPage->getLayout()
                ->createBlock('Zigly\GroomingService\Block\Grooming\Review')
                ->setTemplate('Zigly_GroomingService::grooming/review.phtml')
                ->setData('petData', $post)
                ->toHtml();

        $result->setData(['output' => $block, 'address' => $address]);

        return $result;
    }
}