<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\Plan\Model\Plan;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Zigly\Plan\Model\ResourceModel\Plan\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $loadedData;
    protected $dataPersistor;

    protected $collection;

    /*
     @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->storeManager = $storeManager;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        foreach ($items as $model) {
            $data = $model->getData();
            if (isset($data['plan_image'])) {
                $name = $data['plan_image'];
                unset($data['plan_image']);
                $data['plan_image'][0] = [
                    'name' => $name,
                    'url' => $mediaUrl.'plan/feature/'.$name
                ];
            }
            if (isset($data['banner_image'])) {
                $name = $data['banner_image'];
                unset($data['banner_image']);
                $data['banner_image'][0] = [
                    'name' => $name,
                    'url' => $mediaUrl.'plan/feature/'.$name
                ];
            }
            if (isset($data['applicable_cities'])) {
                $cities = explode(',',$data['applicable_cities']);
                $data['applicable_cities'] = $cities;
            }
            if (isset($data['activity'])) {
                $activity = explode(',',$data['activity']);
                $data['activity'] = $activity;
            }
            $this->loadedData[$model->getId()] = $data;
        }
        $data = $this->dataPersistor->get('zigly_plan_plan');
        
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('zigly_plan_plan');
        }
        
        return $this->loadedData;
    }
}

