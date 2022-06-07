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
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Psr\Log\LoggerInterface;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Amasty_JetTheme::manage_payment_links';

    /**
     * @var PaymentLinkRepositoryInterface
     */
    private $paymentLinkRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        PaymentLinkRepositoryInterface $paymentLinkRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->paymentLinkRepository = $paymentLinkRepository;
        $this->logger = $logger;
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->paymentLinkRepository->deleteById((int)$id);
                $this->messageManager->addSuccessMessage(__('Payment Link have been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);

                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Payment Link to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
