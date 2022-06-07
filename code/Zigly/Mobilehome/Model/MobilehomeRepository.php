<?php
declare(strict_types=1);

namespace Zigly\Mobilehome\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zigly\Mobilehome\Api\Data\MobilehomeInterface;
use Zigly\Mobilehome\Api\Data\MobilehomeInterfaceFactory;
use Zigly\Mobilehome\Api\Data\MobilehomeSearchResultsInterfaceFactory;
use Zigly\Mobilehome\Api\MobilehomeRepositoryInterface;
use Zigly\Mobilehome\Model\ResourceModel\Mobilehome as ResourceMobilehome;
use Zigly\Mobilehome\Model\ResourceModel\Mobilehome\CollectionFactory as MobilehomeCollectionFactory;

class MobilehomeRepository implements MobilehomeRepositoryInterface
{

    /**
     * @var ResourceMobilehome
     */
    protected $resource;

    /**
     * @var MobilehomeInterfaceFactory
     */
    protected $mobilehomeFactory;

    /**
     * @var MobilehomeCollectionFactory
     */
    protected $mobilehomeCollectionFactory;

    /**
     * @var Mobilehome
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;


    /**
     * @param ResourceMobilehome $resource
     * @param MobilehomeInterfaceFactory $mobilehomeFactory
     * @param MobilehomeCollectionFactory $mobilehomeCollectionFactory
     * @param MobilehomeSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceMobilehome $resource,
        MobilehomeInterfaceFactory $mobilehomeFactory,
        MobilehomeCollectionFactory $mobilehomeCollectionFactory,
        MobilehomeSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->mobilehomeFactory = $mobilehomeFactory;
        $this->mobilehomeCollectionFactory = $mobilehomeCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(MobilehomeInterface $mobilehome)
    {
        try {
            $this->resource->save($mobilehome);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the mobilehome: %1',
                $exception->getMessage()
            ));
        }
        return $mobilehome;
    }

    /**
     * @inheritDoc
     */
    public function get($mobilehomeId)
    {
        $mobilehome = $this->mobilehomeFactory->create();
        $this->resource->load($mobilehome, $mobilehomeId);
        if (!$mobilehome->getId()) {
            throw new NoSuchEntityException(__('mobilehome with id "%1" does not exist.', $mobilehomeId));
        }
        return $mobilehome;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->mobilehomeCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(MobilehomeInterface $mobilehome)
    {
        try {
            $mobilehomeModel = $this->mobilehomeFactory->create();
            $this->resource->load($mobilehomeModel, $mobilehome->getMobilehomeId());
            $this->resource->delete($mobilehomeModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the mobilehome: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($mobilehomeId)
    {
        return $this->delete($this->get($mobilehomeId));
    }
}

