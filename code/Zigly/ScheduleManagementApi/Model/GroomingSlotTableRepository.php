<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zigly\ScheduleManagementApi\Api\Data\GroomingSlotTableInterface;
use Zigly\ScheduleManagementApi\Api\Data\GroomingSlotTableInterfaceFactory;
use Zigly\ScheduleManagementApi\Api\Data\GroomingSlotTableSearchResultsInterfaceFactory;
use Zigly\ScheduleManagementApi\Api\GroomingSlotTableRepositoryInterface;
use Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingSlotTable as ResourceGroomingSlotTable;
use Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingSlotTable\CollectionFactory as GroomingSlotTableCollectionFactory;

class GroomingSlotTableRepository implements GroomingSlotTableRepositoryInterface
{

    /**
     * @var ResourceGroomingSlotTable
     */
    protected $resource;

    /**
     * @var GroomingSlotTableInterfaceFactory
     */
    protected $groomingSlotTableFactory;

    /**
     * @var GroomingSlotTableCollectionFactory
     */
    protected $groomingSlotTableCollectionFactory;

    /**
     * @var GroomingSlotTable
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;


    /**
     * @param ResourceGroomingSlotTable $resource
     * @param GroomingSlotTableInterfaceFactory $groomingSlotTableFactory
     * @param GroomingSlotTableCollectionFactory $groomingSlotTableCollectionFactory
     * @param GroomingSlotTableSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceGroomingSlotTable $resource,
        GroomingSlotTableInterfaceFactory $groomingSlotTableFactory,
        GroomingSlotTableCollectionFactory $groomingSlotTableCollectionFactory,
        GroomingSlotTableSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->groomingSlotTableFactory = $groomingSlotTableFactory;
        $this->groomingSlotTableCollectionFactory = $groomingSlotTableCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(
        GroomingSlotTableInterface $groomingSlotTable
    ) {
        try {
            $this->resource->save($groomingSlotTable);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the groomingSlotTable: %1',
                $exception->getMessage()
            ));
        }
        return $groomingSlotTable;
    }

    /**
     * @inheritDoc
     */
    public function get($groomingSlotTableId)
    {
        $groomingSlotTable = $this->groomingSlotTableFactory->create();
        $this->resource->load($groomingSlotTable, $groomingSlotTableId);
        if (!$groomingSlotTable->getId()) {
            throw new NoSuchEntityException(__('GroomingSlotTable with id "%1" does not exist.', $groomingSlotTableId));
        }
        return $groomingSlotTable;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->groomingSlotTableCollectionFactory->create();
        
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
    public function delete(
        GroomingSlotTableInterface $groomingSlotTable
    ) {
        try {
            $groomingSlotTableModel = $this->groomingSlotTableFactory->create();
            $this->resource->load($groomingSlotTableModel, $groomingSlotTable->getGroomingslottableId());
            $this->resource->delete($groomingSlotTableModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the GroomingSlotTable: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($groomingSlotTableId)
    {
        return $this->delete($this->get($groomingSlotTableId));
    }
}

