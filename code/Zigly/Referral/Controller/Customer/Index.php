<?php
namespace Zigly\Referral\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Zigly\Referral\Helper\Data as ReferralHelper;
use Magento\Framework\Exception\NotFoundException;
use Magento\Customer\Model\Session as CustomerSession;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var ReferralHelper
     */
    protected $referralHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ReferralHelper $referralHelper
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        ReferralHelper $referralHelper,
        PageFactory $resultPageFactory,
        CustomerSession $customerSession
    ) {
        parent::__construct($context);
        $this->referralHelper = $referralHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->referralHelper->isEnabled()) {
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Refer a friend'));

            return $resultPage;
        }
        throw new NotFoundException(__('noroute'));
    }
}
