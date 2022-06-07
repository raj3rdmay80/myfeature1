<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\SocialLink;

use Amasty\JetTheme\Api\Data\SocialLinkInterface;
use Amasty\JetTheme\Api\Data\SocialLinkInterfaceFactory;
use Amasty\JetTheme\Api\Data\SocialLinkSearchResultsInterfaceFactory;
use Amasty\JetTheme\Api\SocialLinkRepositoryInterface;
use Amasty\JetTheme\Model\SocialLink\ResourceModel\SocialLink as ResourceSocialLink;
use Amasty\JetTheme\Model\SocialLink\ResourceModel\SocialLink\CollectionFactory as SocialLinkCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class SocialLinkRepository implements SocialLinkRepositoryInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var SocialLinkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var SocialLinkInterfaceFactory
     */
    private $socialLinkFactory;

    /**
     * @var ResourceSocialLink
     */
    private $resource;

    /**
     * @var SocialLinkCollectionFactory
     */
    private $socialLinkCollectionFactory;

    /**
     * @var SocialLinkInterface[]
     */
    private $loadedData = [];

    public function __construct(
        ResourceSocialLink $resource,
        SocialLinkInterfaceFactory $socialLinkFactory,
        SocialLinkCollectionFactory $socialLinkCollectionFactory,
        SocialLinkSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->socialLinkFactory = $socialLinkFactory;
        $this->socialLinkCollectionFactory = $socialLinkCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(SocialLinkInterface $socialLink): SocialLinkInterface
    {
        try {
            $this->resource->save($socialLink);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the socialLink: %1',
                $exception->getMessage()
            ));
        }

        return $socialLink;
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $socialLinkId): SocialLinkInterface
    {
        if (!isset($this->loadedData[$socialLinkId])) {
            $socialLinkModel = $this->socialLinkFactory->create();
            $this->resource->load($socialLinkModel, $socialLinkId);
            $this->loadedData[$socialLinkId] = $socialLinkModel;
            if (!$socialLinkModel->getId()) {
                throw new NoSuchEntityException(__('SocialLink with id "%1" does not exist.', $socialLinkId));
            }
        }

        return $this->loadedData[$socialLinkId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->socialLinkCollectionFactory->create();
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
    public function delete(SocialLinkInterface $socialLink): bool
    {
        try {
            $socialLinkId = $socialLink->getId();
            $this->resource->delete($socialLink);
            if (isset($this->loadedData[$socialLinkId])) {
                unset($this->loadedData[$socialLinkId]);
            }
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the SocialLink: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById(int $socialLinkId): bool
    {
        return $this->delete($this->get($socialLinkId));
    }
}
