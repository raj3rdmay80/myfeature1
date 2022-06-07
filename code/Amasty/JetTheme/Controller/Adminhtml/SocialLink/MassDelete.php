<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Controller\Adminhtml\SocialLink;

use Amasty\JetTheme\Api\SocialLinkRepositoryInterface;
use Amasty\JetTheme\Model\SocialLink\ResourceModel\SocialLink\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

class MassDelete extends Action
{
    const ADMIN_RESOURCE = 'Amasty_JetTheme::manage_social_links';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SocialLinkRepositoryInterface
     */
    private $repository;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        SocialLinkRepositoryInterface $repository,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute(): ResultInterface
    {
        $collection = $this->collectionFactory->create();
        $collection = $this->filter->getCollection($collection);
        $collectionSize = $collection->getSize();
        if ($collectionSize) {
            try {
                $updatedCount = 0;
                foreach ($collection->getItems() as $model) {
                    try {
                        $this->repository->delete($model);
                        $updatedCount++;
                    } catch (LocalizedException $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    }
                }

                $this->messageManager->addSuccessMessage($this->getSuccessMessage($updatedCount));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($this->getErrorMessage());
                $this->logger->critical($e);
            }
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
    }

    /**
     * @return Phrase
     */
    private function getErrorMessage(): Phrase
    {
        return __('We can\'t delete item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return Phrase
     */
    private function getSuccessMessage(int $collectionSize = 0): Phrase
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been deleted.', $collectionSize);
        }

        return __('No records have been changed.');
    }
}
