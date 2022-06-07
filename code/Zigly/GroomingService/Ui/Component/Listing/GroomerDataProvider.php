<?php

declare(strict_types=1);

namespace Zigly\GroomingService\Ui\Component\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Zigly\Groomer\Model\ResourceModel\Groomer\CollectionFactory as GroomerCollection;

/**
 * Class CustomDataProvider
 */
class GroomerDataProvider extends DataProvider
{

    /**
     * Total source count.
     *
     * @var int
     */
    private $sourceCount;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     * @param GroomerCollection $groomerCollection
     * @SuppressWarnings(PHPMD.ExcessiveParameterList) All parameters are needed for backward compatibility
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = [],
        GroomerCollection $groomerCollection
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->groomerCollection = $groomerCollection;
    }
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $groomers = $this->groomerCollection->create()->addFieldToFilter('professional_role', 2);
        return [
            'items' => $groomers->getData(),
            'totalRecords' => $groomers->count()
        ];
    }
}
