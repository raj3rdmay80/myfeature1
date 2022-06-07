<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Ui\Component\Listing\Column;

class GroomingActions extends \Magento\Ui\Component\Listing\Columns\Column
{

    const URL_PATH_DETAILS = 'zigly_groomingservice/grooming/view';
    protected $urlBuilder;
    const URL_PATH_DELETE = 'zigly_groomingservice/grooming/delete';
    const URL_PATH_EDIT = 'zigly_groomingservice/grooming/edit';

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
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
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {
                    if ($item['booking_type'] == 1) {
                        $item[$this->getData('name')] = [
                            'edit' => [
                                'href' => $this->urlBuilder->getUrl(
                                    static::URL_PATH_DETAILS,
                                    [
                                        'entity_id' => $item['entity_id']
                                    ]
                                ),
                                'label' => __('View')
                            ]
                        ];
                    }
                }
            }
        }
        
        return $dataSource;
    }
}

