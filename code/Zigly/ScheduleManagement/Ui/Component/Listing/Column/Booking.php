<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Booking extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param array $data
     * @param array $components
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param GroomingFactory $groomingFactory
     * @param UiComponentFactory $uiComponentFactory
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        GroomingFactory $groomingFactory,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->groomingFactory = $groomingFactory;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
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
                if (isset($item[$fieldName])) {
                    $service = $this->groomingFactory->create()->load($item[$fieldName]);
                    if ($service->getBookingType() == 2) {
                        $html = "<a  href='" . $this->context->getUrl('zigly_vetconsulting/vet/view', ['entity_id' => $item[$fieldName]]) . "'>";
                    } elseif (empty($service->getEntityId())) {
                        $html = "<a  href='javascript:;'>";
                    } else {
                        $html = "<a  href='" . $this->context->getUrl('zigly_groomingservice/grooming/view', ['entity_id' => $item[$fieldName]]) . "'>";
                    }
                    $html .= $item[$fieldName];
                    $html .= "</a>";
                    $item[$fieldName] = $html;
                }
            }
        }
        return $dataSource;
    }
}