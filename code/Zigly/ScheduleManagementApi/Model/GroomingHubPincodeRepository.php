<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zigly\ScheduleManagementApi\Api\Data\GroomingHubPincodeInterface;
use Zigly\ScheduleManagementApi\Api\Data\GroomingHubPincodeInterfaceFactory;
use Zigly\ScheduleManagementApi\Api\Data\GroomingHubPincodeSearchResultsInterfaceFactory;
use Zigly\ScheduleManagementApi\Api\GroomingHubPincodeRepositoryInterface;
use Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHubPincode as ResourceGroomingHubPincode;
use Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHubPincode\CollectionFactory as GroomingHubPincodeCollectionFactory;

class GroomingHubPincodeRepository implements GroomingHubPincodeRepositoryInterface
{

    /**
     * @var ResourceGroomingHubPincode
     */
    protected $resource;

    /**
     * @var GroomingHubPincodeInterfaceFactory
     */
    protected $groomingHubPincodeFactory;

    /**
     * @var GroomingHubPincodeCollectionFactory
     */
    protected $groomingHubPincodeCollectionFactory;

    /**
     * @var GroomingHubPincode
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;


    /**
     * @param ResourceGroomingHubPincode $resource
     * @param GroomingHubPincodeInterfaceFactory $groomingHubPincodeFactory
     * @param GroomingHubPincodeCollectionFactory $groomingHubPincodeCollectionFactory
     * @param GroomingHubPincodeSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceGroomingHubPincode $resource,
        GroomingHubPincodeInterfaceFactory $groomingHubPincodeFactory,
        GroomingHubPincodeCollectionFactory $groomingHubPincodeCollectionFactory,
        GroomingHubPincodeSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->groomingHubPincodeFactory = $groomingHubPincodeFactory;
        $this->groomingHubPincodeCollectionFactory = $groomingHubPincodeCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(
        GroomingHubPincodeInterface $groomingHubPincode
    ) {
        try {
            $this->resource->save($groomingHubPincode);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the groomingHubPincode: %1',
                $exception->getMessage()
            ));
        }
        return $groomingHubPincode;
    }

    /**
     * @inheritDoc
     */
    public function get($groomingHubPincodeId)
    {
        $groomingHubPincode = $this->groomingHubPincodeFactory->create();
        $this->resource->load($groomingHubPincode, $groomingHubPincodeId);
        if (!$groomingHubPincode->getId()) {
            throw new NoSuchEntityException(__('GroomingHubPincode with id "%1" does not exist.', $groomingHubPincodeId));
        }
        return $groomingHubPincode;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->groomingHubPincodeCollectionFactory->create();
        
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
        GroomingHubPincodeInterface $groomingHubPincode
    ) {
        try {
            $groomingHubPincodeModel = $this->groomingHubPincodeFactory->create();
            $this->resource->load($groomingHubPincodeModel, $groomingHubPincode->getGroominghubpincodeId());
            $this->resource->delete($groomingHubPincodeModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Grooming Hub Pincode: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($groomingHubPincodeId)
    {
        return $this->delete($this->get($groomingHubPincodeId));
    }
}

