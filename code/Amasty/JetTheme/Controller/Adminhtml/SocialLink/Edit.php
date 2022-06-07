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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    const ADMIN_RESOURCE = 'Amasty_JetTheme::manage_social_links';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var SocialLinkRepositoryInterface
     */
    private $socialLinkRepository;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        SocialLinkRepositoryInterface $socialLinkRepository
    ) {

        $this->resultPageFactory = $resultPageFactory;
        $this->socialLinkRepository = $socialLinkRepository;
        parent::__construct($context);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id', null);

        if ($id) {
            try {
                $model = $this->socialLinkRepository->get((int)$id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This Social link no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Social link') : __('New Social link'),
            $id ? __('Edit Social link') : __('New Social link')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Social links'));
        $resultPage->getConfig()->getTitle()->prepend(
            $id ?
                __('Edit Social link "%1" (ID: %2)', $model->getTitle(), $model->getId())
                : __('New Social link')
        );

        return $resultPage;
    }
}
