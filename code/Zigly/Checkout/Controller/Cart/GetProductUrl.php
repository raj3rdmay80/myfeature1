<?php
/**
 * Copyright (C) 2022  Zigly
 * @package  Zigly_Checkout
 */
declare(strict_types=1);

namespace Zigly\Checkout\Controller\Cart;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

class GetProductUrl extends \Magento\Framework\App\Action\Action
{

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param JsonFactory $jsonResultFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Execute save action
     * @return void
     */
    public function execute()
    {

        $result = $this->jsonResultFactory->create();
        $responseData = [
            'success' => false,
        ];
        try {
            $product = $this->_initProduct();
            if ($product) {
                $responseData['success'] = true;
                $responseData['url'] = $product->getProductUrl();
            }
        } catch (\Exception $e) {
            $responseData['trace'] = $e->getMessage();
        }
        $result->setData($responseData);

        return $result;
    }
}