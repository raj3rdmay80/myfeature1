<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Block\Widget;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Framework\Pricing\Render;
use Magento\Widget\Block\BlockInterface;
use Psr\Log\LoggerInterface;

class TabWidget extends AbstractProduct implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "widget/category-tab.phtml";

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ProductsList
     */
    private $productList;

    /**
     * @var Visibility
     */
    private $visibility;

    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository,
        ProductCollectionFactory $productCollectionFactory,
        ProductsList $productList,
        Visibility $visibility,
        LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->categoryRepository = $categoryRepository;
        $this->logger = $logger;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productList = $productList;
        $this->visibility = $visibility;
    }

    /**
     * @return string
     */
    public function getWidgetTitle(): string
    {
        return (string)$this->getData('widget_title');
    }

    /**
     * @return int
     */
    public function getVisibleQty(): int
    {
        return (int)$this->getData('visible_qty');
    }

    /**
     * @return int
     */
    public function getDesktopVisibleQty(): int
    {
        return $this->getVisibleQty() - 1 ?: 1;
    }

    /**
     * @return int
     */
    public function getTabletVisibleQty(): int
    {
        $quantity = 1;
        if ($this->getDesktopVisibleQty() > 1) {
            $quantity = ($this->getDesktopVisibleQty() - 1 > 3) ? 3 : 2;
        }

        return $quantity;
    }

    /**
     * @return int
     */
    public function getMobileVisibleQty(): int
    {
        $quantity = 1;
        if ($this->getTabletVisibleQty() > 1) {
            $quantity = ($this->getTabletVisibleQty() - 1 > 2) ? 2 : 1;
        }

        return $quantity;
    }

    /**
     * @return int
     */
    public function getSliderMaxWidth(): int
    {
        return (int)$this->getData('slider_max_width');
    }

    /**
     * @return bool
     */
    public function isShowArrowsSetting(): bool
    {
        return (bool)$this->getData('show_arrows');
    }

    /**
     * @return string
     */
    public function isShowArrows(): string
    {
        return (string)$this->getData('show_arrows') ? 'true' : 'false';
    }

    /**
     * @return string
     */
    public function displayArrowsType(): string
    {
        return (string)$this->getData('display_arrows_type');
    }

    /**
     * @return string
     */
    public function isShowDots(): string
    {
        return (string)$this->getData('show_dots') ? 'true' : 'false';
    }

    /**
     * @return int
     */
    public function getMaxProductQty(): int
    {
        return (int)$this->getData('max_qty');
    }

    /**
     * @return string
     */
    public function isInfinityLoop(): string
    {
        return (string)$this->getData('infinity_loop') ? 'true' : 'false';
    }

    /**
     * @return string
     */
    public function isAutoPlay(): string
    {
        return (string)$this->getData('auto_play') ? 'true' : 'false';
    }

    /**
     * @return int
     */
    public function autoPlaySpeed(): int
    {
        return (int)$this->getData('auto_play_speed') ?: 0;
    }

    /**
     * @return string
     */
    public function isSimulateTouch(): string
    {
        return (string)$this->getData('simulate_touch') ? 'true' : 'false';
    }

    /**
     * @return array
     */
    public function getTabsData(): array
    {
        $tabsData = [];
        if (!$this->getMaxProductQty()) {
            return [];
        }

        try {
            foreach ((range(1, 3)) as $iterator) {
                if ($this->getData('title_' . $iterator) && $this->getData('category_' . $iterator)) {
                    $categoryId = str_replace('category/', '', $this->getData('category_' . $iterator));
                    if (!is_numeric($categoryId)) {
                        continue;
                    }

                    $products = $this->getProductsByCategoryId((int)$categoryId);
                    if (!$products || count($products) == 0) {
                        continue;
                    }

                    $tabsData[] = [
                        'title' => $this->getData('title_' . $iterator),
                        'products' => $products
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return $tabsData;
    }

    /**
     * @return array
     */
    private function getProductsByCategoryId(int $categoryId): array
    {
        try {
            $category = $this->categoryRepository->get($categoryId);
        } catch (\Exception $exception) {
            return [];
        }

        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->visibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addCategoryFilter($category)
            ->addAttributeToSort('created_at', 'desc')
        ;
        $collection->getSelect()->limit($this->getMaxProductQty());

        return $collection->getItems();
    }

    /**
     * @param $product
     * @return array
     */
    public function getAddToCartPostParams($product): array
    {
        return $this->productList->getAddToCartPostParams($product);
    }

    /**
     * @param Product $product
     * @param null $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     */
    public function getProductPriceHtml(
        Product $product,
        $priceType = null,
        $renderZone = Render::ZONE_ITEM_LIST,
        array $arguments = []
    ): string {
        return $this->productList->getProductPriceHtml($product, $priceType, $renderZone, $arguments);
    }

    /**
     * Returns widget's unique ID
     * @return string
     */
    public function getWidgetUniqueId(): string
    {
        return $this->getRandomString(20);
    }
}
