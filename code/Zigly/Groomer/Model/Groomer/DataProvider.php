<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Groomer
 */
declare(strict_types=1);

namespace Zigly\Groomer\Model\Groomer;

use Magento\Framework\App\Request\DataPersistorInterface;
use Zigly\Groomer\Model\ResourceModel\Groomer\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

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
     * @param DataPersistorInterface $dataPersistor
     * @param StoreManagerInterface $storeManager
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
            if (isset($data['profile_image'])) {
                $name = $data['profile_image'];
                unset($data['profile_image']);
                $data['profile_image'][0] = [
                    'name' => $name,
                    'url' => $mediaUrl.'groomer/feature/'.$name
                ];
            }
            if (isset($data['bd_profile_image'])) {
                $name = $data['bd_profile_image'];
                unset($data['bd_profile_image']);
                $data['bd_profile_image'][0] = [
                    'name' => $name,
                    'url' => $mediaUrl.'groomer/feature/'.$name
                ];
            }
            if (isset($data['aadhar_img'])) {
                $name = $data['aadhar_img'];
                unset($data['aadhar_img']);
                $data['aadhar_img'][0] = [
                    'name' => $name,
                    'url' => $mediaUrl.'groomer/feature/'.$name
                ];
            }
            if (isset($data['aadhar_img_back'])) {
                $name = $data['aadhar_img_back'];
                unset($data['aadhar_img_back']);
                $data['aadhar_img_back'][0] = [
                    'name' => $name,
                    'url' => $mediaUrl.'groomer/feature/'.$name
                ];
            }
            if (isset($data['pan_img'])) {
                $name = $data['pan_img'];
                unset($data['pan_img']);
                $data['pan_img'][0] = [
                    'name' => $name,
                    'url' => $mediaUrl.'groomer/feature/'.$name
                ];
            }
            if (isset($data['cheque_image'])) {
                $name = $data['cheque_image'];
                unset($data['cheque_image']);
                $data['cheque_image'][0] = [
                    'name' => $name,
                    'url' => $mediaUrl.'groomer/feature/'.$name
                ];
            }
            if (isset($data['upload_certificate'])) {
                $name = $data['upload_certificate'];
                unset($data['upload_certificate']);
                $data['upload_certificate'][0] = [
                    'name' => $name,
                    'url' => $mediaUrl.'groomer/feature/'.$name
                ];
            }
            /*if (isset($data['select_specialisation'])) {
                $specialisation = explode(',',$data['select_specialisation']);
                $data['select_specialisation'] = $specialisation;
            }*/
            if (isset($data['select_facilities'])) {
                $facilities = explode(',',$data['select_facilities']);
                $data['select_facilities'] = $facilities;
            }
            $this->loadedData[$model->getId()] = $data;
        }
        $data = $this->dataPersistor->get('zigly_groomer_groomer');
        
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('zigly_groomer_groomer');
        }
        
        return $this->loadedData;
    }
}

