<?php
namespace Zigly\Species\Ui\Component\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;

class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'thumbnail';

    const ALT_FIELD = 'name';


    private $_getModel;
    /**
     * @var string
     */
    private $editUrl;

    private $_objectManager = null;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Webkul\Hello\Model\Image\Image $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        StoreManagerInterface $storemanager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storemanager;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        $imageurl = $mediaUrl.'catalog/product/placeholder/'.$this->getConfig('catalog/placeholder/thumbnail_placeholder');
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $url = $imageurl;
                if (isset($item[$fieldName])) {
                    if ($item[$fieldName] != '') {
                        $url = $this->_storeManager->getStore()->getBaseUrl(
                            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                        ) . "breed/feature/" . $item[$fieldName];
                    } else {
                        $url = $imageurl;
                    }
                }
                $item[$fieldName . '_src'] = $url;
                $item[$fieldName . '_alt'] = $this->getAlt($item) ?: '';
                $item[$fieldName . '_orig_src'] = $url;
            }
        }

        return $dataSource;
    }
    public function getConfig($config_path)
    {
            return $this->_storeManager->getStore()->getConfig($config_path);
    }
       /**
       * @param array $row
       *
       * @return null|string
       */
     protected function getAlt($row)
     {
       $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
       return isset($row[$altField]) ? $row[$altField] : null;
     }
}
