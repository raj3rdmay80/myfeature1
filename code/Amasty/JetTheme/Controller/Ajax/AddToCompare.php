<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Controller\Ajax;

use Amasty\JetTheme\Model\Product\ProductLoader;
use Amasty\JetTheme\Model\Validator\AddToRequestValidator;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Product\Compare;
use Magento\Catalog\Model\Product\Compare\ListCompare;
use Magento\Catalog\ViewModel\Product\Checker\AddToCompareAvailability;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Visitor;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class AddToCompare extends Action implements HttpPostActionInterface
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var Visitor
     */
    private $customerVisitor;

    /**
     * @var ListCompare
     */
    private $catalogProductCompareList;

    /**
     * @var Compare
     */
    private $compare;

    /**
     * @var AddToRequestValidator
     */
    private $addToRequestValidator;

    /**
     * @var ProductLoader
     */
    private $productLoader;

    /**
     * @var AddToCompareAvailability
     */
    private $compareAvailability;

    public function __construct(
        Context $context,
        Session $customerSession,
        Visitor $customerVisitor,
        ListCompare $catalogProductCompareList,
        Compare $compare,
        AddToRequestValidator $addToRequestValidator,
        ProductLoader $productLoader,
        AddToCompareAvailability $compareAvailability
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->customerVisitor = $customerVisitor;
        $this->catalogProductCompareList = $catalogProductCompareList;
        $this->compare = $compare;
        $this->addToRequestValidator = $addToRequestValidator;
        $this->productLoader = $productLoader;
        $this->compareAvailability = $compareAvailability;
    }

    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $isModal = $this->getRequest()->getParam('is_modal_shown');

        try {
            $this->addToRequestValidator->validateRequest($this->getRequest());
            $this->validateSession();
            $productId = (int)$this->getRequest()->getParam('product');
            $product = $this->validateAndGetProduct($productId);
        } catch (LocalizedException $e) {
            $resultJson->setHttpResponseCode(400);
            $resultJson->setData(['message' => $e->getMessage()]);
            if (!$isModal) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $resultJson;
        }

        $this->catalogProductCompareList->addProduct($product);
        $this->_eventManager->dispatch('catalog_product_compare_add_product', ['product' => $product]);
        $this->compare->calculate();
        $resultJson->setHttpResponseCode(200);
        $this->messageManager->addComplexSuccessMessage(
            'amAjaxSuccessAddToCompare',
            [
                'product_name' => $product->getName(),
                'compare_url' => $this->getCompareUrl(),
            ]
        );
        $resultJson->setData(['success' => true]);

        return $resultJson;
    }

    /**
     * Returns compare url
     *
     * @return string
     */
    private function getCompareUrl(): string
    {
        return $this->_url->getUrl('catalog/product_compare', ['_secure' => true]);
    }

    /**
     * @throws LocalizedException
     */
    private function validateSession(): void
    {
        if (!$this->customerVisitor->getId() && !$this->customerSession->isLoggedIn()) {
            throw new LocalizedException(__('Unable to add to compare'));
        }
    }

    /**
     * @param int $productId
     * @return ProductInterface
     * @throws LocalizedException
     */
    private function validateAndGetProduct(int $productId): ProductInterface
    {
        $product = $this->productLoader->getProduct($productId);
        if (!$this->compareAvailability->isAvailableForCompare($product)) {
            throw new LocalizedException(__('Product is not available for compare'));
        }

        return $product;
    }
}
