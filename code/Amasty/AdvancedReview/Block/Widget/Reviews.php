<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Widget;

use Amasty\AdvancedReview\Model\OptionSource\Widget\Type;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Amasty\AdvancedReview\Model\ResourceModel\Review\Collection as ReviewCollection;
use Amasty\AdvancedReview\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Framework\Registry;
use Amasty\AdvancedReview\Model\Indexer\Catalog\Category\Product\TableResolver;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

class Reviews extends Template implements \Magento\Widget\Block\BlockInterface
{
    const COMMON_TEMPLATE = 'Amasty_AdvancedReview::widget/review/reviews.phtml';
    const LAYOUT_CONTENT_TEMPLATE = 'Amasty_AdvancedReview::widget/review/content/main.phtml';
    const LAYOUT_SIDEBAR_TEMPLATE = 'Amasty_AdvancedReview::widget/review/sidebar/sidebar.phtml';

    const TITLE = 'title';
    const LIMIT = 'reviews_count';
    const CURRENT_CATEGORY = 'current_category';
    const CURRENT_PRODUCT_CATEGORY = 'current_product_category';
    const TYPE = 'review_type';
    const HIGHER_THAN = 'higher_than';

    const DEFAULT_LIMIT = 10;
    const REVIEW_MESSAGE_MAX_LENGTH = 75;

    /**
     * @var null|ReviewCollection
     */
    private $reviewsCollection = null;

    /**
     * @var ReviewCollectionFactory
     */
    private $reviewCollectionFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var TableResolver
     */
    private $tableResolver;

    /**
     * @var ImageBuilder
     */
    private $imageBuilder;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ProductCollection
     */
    private $productCollection;

    /**
     * @var \Amasty\AdvancedReview\Helper\BlockHelper
     */
    private $blockHelper;

    /**
     * @var int|null
     */
    private $categoryId;

    /**
     * @var int|null
     */
    private $productId;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        ReviewCollectionFactory $reviewCollectionFactory,
        Registry $registry,
        TableResolver $tableResolver,
        ImageBuilder $imageBuilder,
        ProductCollectionFactory $productCollectionFactory,
        \Amasty\AdvancedReview\Helper\BlockHelper $blockHelper,
        ProductRepositoryInterface $productRepository,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->registry = $registry;
        $this->tableResolver = $tableResolver;
        $this->imageBuilder = $imageBuilder;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->blockHelper = $blockHelper;
        $this->productRepository = $productRepository;
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->getReviewsCollection()->load()->addRateVotes();
        $this->createProductCollection();

        return parent::_beforeToHtml();
    }

    /**
     * @return ReviewCollection
     */
    public function getReviewsCollection()
    {
        if ($this->reviewsCollection === null) {
            $this->createCollection();
        }

        return $this->reviewsCollection;
    }

    /**
     * @param string $template
     * @return Template
     */
    public function setTemplate($template)
    {
        if ($template == self::LAYOUT_CONTENT_TEMPLATE) {
            $this->setContainerPosition('grid');
        } elseif ($template == self::LAYOUT_SIDEBAR_TEMPLATE) {
            $this->setContainerPosition('sidebar');
        }
        $template = self::COMMON_TEMPLATE;

        return parent::setTemplate($template);
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function createCollection()
    {
        /** @var ReviewCollection $reviewsCollection */
        $reviewsCollection = $this->reviewCollectionFactory->create()->addStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->addStatusFilter(
            \Magento\Review\Model\Review::STATUS_APPROVED
        )->setPageSize(
            $this->getLimit()
        );

        if ($higherThan = $this->getHigherThan()) {
            $reviewsCollection->setFlag('filter_by_stars', true);
            $reviewsCollection->getSelect()->having(
                'rating_summary >= ?',
                $higherThan
            );
        }

        $entityFilter = $this->getEntityFilter();
        if ($entityFilter !== null) {
            $reviewsCollection->addEntityFilter('product', $entityFilter);
        }

        if ($this->isCurrentProductCategoryOnly() && $this->getProductId()) {
            try {
                $entity = $this->productRepository->getById($this->getProductId());
                $categories = $entity->getCategoryIds();
            } catch (NoSuchEntityException $e) {
                $categories = [];
            }
            $reviewsCollection->addCategoriesFilter(['in' => $categories]);
        }

        switch ($this->getReviewType()) {
            case Type::RANDOM:
                $reviewsCollection->setOrder('RAND()');
                break;
            case Type::RECENT:
                $reviewsCollection->setDateOrder();
                break;
        }

        $this->reviewsCollection = $reviewsCollection;
    }

    /**
     * {@inheritDoc}
     */
    public function createProductCollection()
    {
        $this->productCollection = $this->productCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('small_image')
            ->addIdFilter($this->reviewsCollection->getProductIds())
            ->setFlag('has_stock_status_filter', true);
    }

    /**
     * @return array|int
     */
    private function getEntityFilter()
    {
        $entityFilter = null;

        if ($this->getCategoryId() && $this->isCurrentCategoryOnly()) {
            $entityFilter = $this->tableResolver->getProductIds($this->getCategoryId());
        }

        return $entityFilter;
    }

    /**
     * @param $product
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product)
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId('advanced_review_widget_image')
            ->setAttributes([])
            ->create();
    }

    /**
     * @param $review
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct($review)
    {
        if (!$this->productCollection) {
            $this->getReviewsCollection()->load()->addRateVotes();
            $this->createProductCollection();
        }
        return $this->productCollection->getItemById($review->getEntityPkValue());
    }

    /**
     * @return \Amasty\AdvancedReview\Helper\BlockHelper
     */
    public function getAdvancedHelper()
    {
        return $this->blockHelper;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    public function getLimit(): int
    {
        return (int)$this->getData(self::LIMIT) ?: self::DEFAULT_LIMIT;
    }

    /**
     * @return bool
     */
    public function isCurrentCategoryOnly()
    {
        return (bool)$this->getData(self::CURRENT_CATEGORY);
    }

    /**
     * @return bool
     */
    public function isCurrentProductCategoryOnly()
    {
        return (bool)$this->getData(self::CURRENT_PRODUCT_CATEGORY);
    }

    /**
     * @return string
     */
    public function getReviewType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @return float
     */
    public function getHigherThan()
    {
        return (float)$this->getData(self::HIGHER_THAN);
    }

    /**
     * @param $message
     *
     * @return string
     */
    public function getReviewMessage($message)
    {
        return (strlen($message) > self::REVIEW_MESSAGE_MAX_LENGTH)
            ? substr($message, 0, self::REVIEW_MESSAGE_MAX_LENGTH) . '...'
            : $message;
    }

    public function getCategoryId(): ?int
    {
        if ($this->categoryId === null && $this->_request->getFullActionName() === 'catalog_category_view') {
            /** @var \Magento\Catalog\Model\Category $entity */
            $entity = $this->registry->registry('current_category');
            $this->categoryId = (int) $entity->getId();
        }

        return $this->categoryId;
    }

    public function setCategoryId(?int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getProductId(): ?int
    {
        if ($this->productId === null && $this->_request->getFullActionName() == 'catalog_product_view') {
            /** @var \Magento\Catalog\Model\Product $entity */
            $entity = $this->registry->registry('current_product');
            $this->productId = (int) $entity->getId();
        }

        return $this->productId;
    }

    public function setProductId(?int $productId): void
    {
        $this->productId = $productId;
    }
}
