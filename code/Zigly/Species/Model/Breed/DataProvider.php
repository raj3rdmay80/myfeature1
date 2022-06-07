<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Species\Model\Breed;

use Magento\Framework\App\Request\DataPersistorInterface;
use Zigly\Species\Model\ResourceModel\Breed\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $loadedData;

    protected $dataPersistor;

    protected $collection;

    /* @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->_storeManager = $storeManager;
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
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        foreach ($items as $model) {
            $data = $model->getData();
            if (isset($data['image'])) {
                $name = $data['image'];
                unset($data['image']);
                $data['image'][0] = [
                    'name' => $name,
                    'url' => $mediaUrl.'breed/feature/'.$name
                ];
            }
            $this->loadedData[$model->getId()] = $data;
        }
        $data = $this->dataPersistor->get('zigly_species_breed');
        
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('zigly_species_breed');
        }
        
        return $this->loadedData;
    }
}

