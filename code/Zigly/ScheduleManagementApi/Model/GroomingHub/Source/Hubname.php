<?php
namespace Zigly\ScheduleManagementApi\Model\GroomingHub\Source;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Zigly\ScheduleManagementApi\Api\GroomingHubRepositoryInterface;
use Zigly\ScheduleManagementApi\Api\Data\GroomingHubInterfaceFactory;
use Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHub\CollectionFactory as HuboptionCollectionFactory;

class Hubname extends Column
{
    protected $userFactory;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     * @param HuboptionCollectionFactory $HuboptionCollectionFactory
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        GroomingHubInterfaceFactory $groomingHubFactory,
        GroomingHubRepositoryInterface $groomingHubRepositoryInterface,
        array $components = [],
        array $data = [],
        HuboptionCollectionFactory $HuboptionCollectionFactory
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->HuboptionCollectionFactory = $HuboptionCollectionFactory;
        $this->groomingHubFactory = $groomingHubFactory;
        $this->groomingHubRepositoryInterface = $groomingHubRepositoryInterface;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item[$fieldName] != '') {
                    $hubname = $this->getHubName($item[$fieldName]);
                    $item[$fieldName] = $hubname;
                }
            }
        }
        return $dataSource;
    }

    /**
     * @param $hubId
     * @return string
     */
    private function getHubName($hubId)
    {

        $groomingHub = $this->groomingHubFactory->create()->load($hubId);
        return $groomingHub->getHubName();


    }
}