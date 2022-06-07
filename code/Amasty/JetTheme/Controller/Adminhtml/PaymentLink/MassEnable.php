<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Controller\Adminhtml\PaymentLink;

use Amasty\JetTheme\Api\Data\PaymentLinkInterface;
use Amasty\JetTheme\Api\PaymentLinkRepositoryInterface;
use Amasty\JetTheme\Model\OptionSource\Status;
use Amasty\JetTheme\Model\PaymentLink\ResourceModel\PaymentLink\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

class MassEnable extends Action
{
    const ADMIN_RESOURCE = 'Amasty_JetTheme::manage_payment_links';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PaymentLinkRepositoryInterface
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
        PaymentLinkRepositoryInterface $repository,
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
                /** @var PaymentLinkInterface $model */
                foreach ($collection->getItems() as $model) {
                    try {
                        $model->setStatus(Status::STATUS_ACTIVE);
                        $this->repository->save($model);
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
        return __('We can\'t enable item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return Phrase
     */
    private function getSuccessMessage(int $collectionSize = 0): Phrase
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been enabled.', $collectionSize);
        }

        return __('No records have been changed.');
    }
}
