<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\PaymentLink;

use Amasty\JetTheme\Api\Data\PaymentLinkInterface;
use Amasty\JetTheme\Api\Data\PaymentLinkInterfaceFactory;
use Amasty\JetTheme\Api\Data\PaymentLinkSearchResultsInterfaceFactory;
use Amasty\JetTheme\Api\PaymentLinkRepositoryInterface;
use Amasty\JetTheme\Model\PaymentLink\ResourceModel\PaymentLink as ResourcePaymentLink;
use Amasty\JetTheme\Model\PaymentLink\ResourceModel\PaymentLink\CollectionFactory as PaymentLinkCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class PaymentLinkRepository implements PaymentLinkRepositoryInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var PaymentLinkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var PaymentLinkInterfaceFactory
     */
    private $paymentLinkFactory;

    /**
     * @var ResourcePaymentLink
     */
    private $resource;

    /**
     * @var PaymentLinkCollectionFactory
     */
    private $paymentLinkCollectionFactory;

    /**
     * @var PaymentLinkInterface[]
     */
    private $loadedData = [];

    public function __construct(
        ResourcePaymentLink $resource,
        PaymentLinkInterfaceFactory $paymentLinkFactory,
        PaymentLinkCollectionFactory $paymentLinkCollectionFactory,
        PaymentLinkSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->paymentLinkFactory = $paymentLinkFactory;
        $this->paymentLinkCollectionFactory = $paymentLinkCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(PaymentLinkInterface $paymentLink): PaymentLinkInterface
    {
        try {
            $this->resource->save($paymentLink);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the paymentLink: %1',
                $exception->getMessage()
            ));
        }

        return $paymentLink;
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $paymentLinkId): PaymentLinkInterface
    {
        if (!isset($this->loadedData[$paymentLinkId])) {
            $paymentLinkModel = $this->paymentLinkFactory->create();
            $this->resource->load($paymentLinkModel, $paymentLinkId);
            $this->loadedData[$paymentLinkId] = $paymentLinkModel;
            if (!$paymentLinkModel->getId()) {
                throw new NoSuchEntityException(__('PaymentLink with id "%1" does not exist.', $paymentLinkId));
            }
        }

        return $this->loadedData[$paymentLinkId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->paymentLinkCollectionFactory->create();
        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = $collection->getItems();

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(PaymentLinkInterface $paymentLink): bool
    {
        try {
            $paymentLinkId = $paymentLink->getId();
            $this->resource->delete($paymentLink);
            if (isset($this->loadedData[$paymentLinkId])) {
                unset($this->loadedData[$paymentLinkId]);
            }
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the PaymentLink: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById(int $paymentLinkId): bool
    {
        return $this->delete($this->get($paymentLinkId));
    }
}
