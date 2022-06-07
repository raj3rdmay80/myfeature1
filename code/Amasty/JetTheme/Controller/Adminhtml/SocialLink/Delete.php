<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Controller\Adminhtml\SocialLink;

use Amasty\JetTheme\Api\SocialLinkRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Psr\Log\LoggerInterface;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Amasty_JetTheme::manage_social_links';

    /**
     * @var SocialLinkRepositoryInterface
     */
    private $socialLinkRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        SocialLinkRepositoryInterface $socialLinkRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->socialLinkRepository = $socialLinkRepository;
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
                $this->socialLinkRepository->deleteById((int)$id);
                $this->messageManager->addSuccessMessage(__('Social Link have been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);

                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Social Link to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
