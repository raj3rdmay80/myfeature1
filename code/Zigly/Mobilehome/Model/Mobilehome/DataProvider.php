<?php
declare(strict_types=1);

namespace Zigly\Mobilehome\Model\Mobilehome;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Zigly\Mobilehome\Model\ResourceModel\Mobilehome\CollectionFactory;

class DataProvider extends AbstractDataProvider
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
        \Magento\Framework\UrlInterface $urlInterface,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->storeManager = $storeManager;
        $this->dataPersistor = $dataPersistor;
        $this->_urlInterface = $urlInterface;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @inheritDoc
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
            if (isset($data['image'])) {
                $name = $data['image'];
                unset($data['image']);
                $data['image'][0] = [
                    'name' => $name,
                    'url' => $mediaUrl.'Zigly/mobilehome/'.$name
                ];
            }
            $this->loadedData[$model->getId()] = $data;
        }
        $data = $this->dataPersistor->get('zigly_mobilehome_mobilehome');
        
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('zigly_mobilehome_mobilehome');

        }
        
        return $this->loadedData;
    }
}

