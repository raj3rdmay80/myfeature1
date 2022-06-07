<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProductApi
 */
declare(strict_types=1);

namespace Zigly\ProductApi\Model\Api;

use Zigly\ProductApi\Api\ProductRepositoryInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory;

class ProductRepository implements ProductRepositoryInterface
{
     /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ProductSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @param Request $request
     * @param ProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(
        Request $request,
        ProductSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionFactory $productCollectionFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory; 
        $this->searchResultsFactory = $searchResultsFactory;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function ProductCollectionApi()
    {
        $data = $this->request->getBodyParams();
        $catagoryId = $data['category_id'];
        if (!isset($data['attribute_code']) || empty($data['attribute_code'])) {
            throw new NoSuchEntityException(__('Please enter attribute code.'));
        }
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')->addAttributeToFilter($data['attribute_code'], ['eq' => 1]);
        if($catagoryId != ""){
            $collection->addCategoriesFilter(['eq' => $catagoryId]);
        }
        $collection->setPageSize(8)->load();
        $productValue=[];
        foreach($collection as $value){
            $brandName= $value->getResource()->getAttribute('brand')->getFrontend()->getValue($value);
            $value->setData('brand', $brandName);
            $productValue[]=$value->getData();
        }
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setItems($productValue);
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }
}