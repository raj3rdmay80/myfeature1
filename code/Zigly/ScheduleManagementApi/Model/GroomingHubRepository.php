<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zigly\ScheduleManagementApi\Api\Data\GroomingHubInterface;
use Zigly\ScheduleManagementApi\Api\Data\GroomingHubInterfaceFactory;
use Zigly\ScheduleManagementApi\Api\Data\GroomingHubSearchResultsInterfaceFactory;
use Zigly\ScheduleManagementApi\Api\GroomingHubRepositoryInterface;
use Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHub as ResourceGroomingHub;
use Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHub\CollectionFactory as GroomingHubCollectionFactory;

class GroomingHubRepository implements GroomingHubRepositoryInterface
{

    /**
     * @var ResourceGroomingHub
     */
    protected $resource;

    /**
     * @var GroomingHubInterfaceFactory
     */
    protected $groomingHubFactory;

    /**
     * @var GroomingHubCollectionFactory
     */
    protected $groomingHubCollectionFactory;

    /**
     * @var GroomingHub
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;


    /**
     * @param ResourceGroomingHub $resource
     * @param GroomingHubInterfaceFactory $groomingHubFactory
     * @param GroomingHubCollectionFactory $groomingHubCollectionFactory
     * @param GroomingHubSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceGroomingHub $resource,
        GroomingHubInterfaceFactory $groomingHubFactory,
        GroomingHubCollectionFactory $groomingHubCollectionFactory,
        GroomingHubSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->groomingHubFactory = $groomingHubFactory;
        $this->groomingHubCollectionFactory = $groomingHubCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(GroomingHubInterface $groomingHub)
    {
        try {
            $this->resource->save($groomingHub);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the groomingHub: %1',
                $exception->getMessage()
            ));
        }
        return $groomingHub;
    }

    /**
     * @inheritDoc
     */
    public function get($groomingHubId)
    {
        $groomingHub = $this->groomingHubFactory->create();
        $this->resource->load($groomingHub, $groomingHubId);
        if (!$groomingHub->getId()) {
            throw new NoSuchEntityException(__('GroomingHub with id "%1" does not exist.', $groomingHubId));
        }
        return $groomingHub;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->groomingHubCollectionFactory->create();
        
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
    public function delete(GroomingHubInterface $groomingHub)
    {
        try {
            $groomingHubModel = $this->groomingHubFactory->create();
            $this->resource->load($groomingHubModel, $groomingHub->getGroominghubId());
            $this->resource->delete($groomingHubModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the GroomingHub: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($groomingHubId)
    {
        return $this->delete($this->get($groomingHubId));
    }
}

