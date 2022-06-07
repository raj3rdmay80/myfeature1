<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */

declare(strict_types=1);

namespace Amasty\JetTheme\Controller\Adminhtml\PaymentLink;

use Amasty\JetTheme\Api\PaymentLinkRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    const ADMIN_RESOURCE = 'Amasty_JetTheme::manage_payment_links';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var PaymentLinkRepositoryInterface
     */
    private $paymentLinkRepository;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PaymentLinkRepositoryInterface $paymentLinkRepository
    ) {

        $this->resultPageFactory = $resultPageFactory;
        $this->paymentLinkRepository = $paymentLinkRepository;
        parent::__construct($context);
    }

    /**
     * Edit action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $id = $this->getRequest()->getParam('id', null);

        if ($id) {
            try {
                $model = $this->paymentLinkRepository->get((int)$id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This Payment Method Icon no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Payment link') : __('New Payment Method Icon'),
            $id ? __('Edit Payment link') : __('New Payment Method Icon')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Payment Method Icon'));
        $resultPage->getConfig()->getTitle()->prepend(
            $id ?
                __('Edit Payment Method Icon "%1" (ID: %2)', $model->getTitle(), $model->getId())
                : __('New Payment Method Icon')
        );

        return $resultPage;
    }
}
