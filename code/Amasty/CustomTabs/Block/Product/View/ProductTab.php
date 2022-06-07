<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Block\Product\View;

use Amasty\CustomTabs\Api\Data\TabsInterface;
use Amasty\CustomTabs\Model\RotationFactory;
use Amasty\CustomTabs\Model\Tabs\Loader\SaveHandler;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;

class ProductTab extends Template implements IdentityInterface
{
    const NAME_IN_LAYOUT = 'amcustomtabs_tabs_';

    const REVIEW_TAB_SELECTOR = '#tab-label-reviews';

    // @codingStandardsIgnoreStart
    const PRODUCTS_BLOCKS = [
        'related' => [
            'catalog' => \Magento\Catalog\Block\Product\ProductList\Related::class,
            'rule' => 'Magento\\TargetRule\\Block\\Catalog\\Product\\ProductList\\Related'
        ],
        'upsell' => [
            'catalog' => \Magento\Catalog\Block\Product\ProductList\Upsell::class,
            'rule' => 'Magento\\TargetRule\\Block\\Catalog\\Product\\ProductList\\Upsell'
        ],
        'crosssell' => [
            'catalog' => \Magento\Catalog\Block\Product\ProductList\Crosssell::class,
            'rule' => 'Magento\\TargetRule\\Block\\Checkout\\Cart\\Crosssell'
        ]
    ];
    // @codingStandardsIgnoreEnd

    /**
     * @var TabsInterface
     */
    protected $tab;

    /**
     * @var Product
     */
    protected $product = null;

    /**
     * @var \Magento\Widget\Model\Template\Filter
     */
    private $filter;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var RotationFactory
     */
    private $rotationFactory;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        PriceCurrencyInterface $priceCurrency,
        Template\Context $context,
        \Magento\Widget\Model\Template\Filter $filter,
        \Magento\Framework\Registry $registry,
        RotationFactory $rotationFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->filter = $filter;
        $this->registry = $registry;
        $this->priceCurrency = $priceCurrency;
        $this->moduleManager = $moduleManager;
        $this->rotationFactory = $rotationFactory;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $html = '';
        if ($this->getTab()) {
            $html = $this->getTab()->getContent();
            if ($html) {
                $html = $this->parseVariables($html);
                $html = $this->parseWysiwyg($html);
            }

            $html = trim($this->addProductBlocks($html));
        }

        $html = $html == '<p></p>'
            ? ''
            : $html;

        if ($html) {
            $this->setData('title', $this->getTabTitle());
            $html = sprintf(
                '<div class="am-custom-tab am-custom-tab-%s">%s</div>',
                $this->getTab()->getTabId(),
                $html
            );

            $html = $this->fixReviewTabBug($html);
        }

        return $html;
    }

    /**
     * @param string $html
     *
     * @return string
     */
    protected function fixReviewTabBug(string $html)
    {
        if (strpos($html, self::REVIEW_TAB_SELECTOR) !== false) {
            $html = str_replace(
                self::REVIEW_TAB_SELECTOR,
                self::REVIEW_TAB_SELECTOR . ', #tab-label-' . self::NAME_IN_LAYOUT . $this->getTab()->getTabId(),
                $html
            );
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        $title = '';
        if ($this->getTab()) {
            $title = $this->getTab()->getTabTitle();
            $title = $this->escapeHtml($title, ['span', 'b', 'p']);
            if (strpos($title, SaveHandler::DEFAULT_TITLE_VARIABLE) !== false) {
                $parentTitle = $this->getTitleFromParent();
                $title = str_replace(SaveHandler::DEFAULT_TITLE_VARIABLE, $parentTitle, $title);
            }
        }

        return $title;
    }

    /**
     * @return string
     */
    protected function getTitleFromParent()
    {
        $title = '';
        $nameInLayout = $this->getTab()->getNameInLayout();
        $block = $this->_layout->getBlock($nameInLayout);
        if ($block) {
            $title = $block->getData('title');
        }

        return $title;
    }

    /**
     * @param $content
     *
     * @return string
     */
    protected function addProductBlocks($content)
    {
        $types = [TabsInterface::RELATED_ENABLED, TabsInterface::UPSELL_ENABLED, TabsInterface::CROSSSELL_ENABLED];
        foreach ($types as $type) {
            if (!$this->getTab()->getData($type)) {
                continue;
            }

            $html = null;
            switch ($type) {
                case TabsInterface::RELATED_ENABLED:
                    $html = $this->getHtml($this->getProductBlock('related'));
                    $html = preg_replace(
                        '@(data-mage-init=\'{")(relatedProducts)(.*?)(\')@s',
                        sprintf(
                            '$1Amasty_CustomTabs/js/related-products"
                            :{"relatedCheckbox":".am-custom-tab-%1$s .am-tab-related.checkbox",
                            "selectAllLink":"[data-role=\"select-all\"], [role=\"select-all\"]"}}$4',
                            $this->getTab()->getTabId()
                        ),
                        $html
                    );
                    break;
                case TabsInterface::UPSELL_ENABLED:
                    $html = $this->getHtml($this->getProductBlock('upsell'));
                    break;
                case TabsInterface::CROSSSELL_ENABLED:
                    $html = $this->getHtml($this->getProductBlock('crosssell'));
                    break;
            }

            if ($html) {
                $content .= $html;
            }
        }

        return $content;
    }

    /**
     * @param string $type
     *
     * @return \Magento\Framework\View\Element\BlockInterface|null
     */
    protected function getProductBlock($type)
    {
        $productBlock = null;
        $blocks = self::PRODUCTS_BLOCKS;
        $tabId = $this->getTab()->getTabId();
        if (isset($blocks[$type])) {
            if ($this->moduleManager->isEnabled('Magento_TargetRule')) {
                $productBlock = $this->_layout->createBlock(
                    $blocks[$type]['rule'],
                    'amcustomtabs.tabs.catalog.product.' . $type . $tabId,
                    [
                        'data' => [
                            'type'     => $type . '-rule',
                            'template' => 'Magento_Catalog::product/list/items.phtml',
                            'rotation' => $this->rotationFactory->get()
                        ]
                    ]
                );
            } else {
                $productBlock = $this->_layout->createBlock(
                    $blocks[$type]['catalog'],
                    'amcustomtabs.tabs.catalog.product.' . $type . $tabId,
                    [
                        'data' => [
                            'type'     => $type,
                            'template' => 'Magento_Catalog::product/list/items.phtml'
                        ]
                    ]
                );
            }

            $addToName = 'amcustomtabs.tabs.' . $type . '.product.addto';
            $addTo = $this->_layout->getBlock($addToName);
            if (!$addTo) {
                $addTo = $this->_layout->createBlock(
                    \Magento\Catalog\Block\Product\ProductList\Item\Container::class,
                    $addToName
                );
            }

            $compareName = 'amcustomtabs.tabs.' . $type . '.product.addto.compare';
            $compare = $this->_layout->getBlock($compareName);
            if (!$compare) {
                $compare = $this->_layout->createBlock(
                    \Magento\Catalog\Block\Product\ProductList\Item\AddTo\Compare::class,
                    $compareName,
                    [
                        'data' => [
                            'template' => 'Magento_Catalog::product/list/addto/compare.phtml'
                        ]
                    ]
                );
            }

            $addTo->setChild('compare', $compare);
            $productBlock->setChild('addto', $addTo);
            $productBlock->setViewModel($this->getViewModel());
            $this->_layout->getBlock(SaveHandler::TABS_NAME_IN_LAYOUT)
                ->setChild('', $productBlock);
        }

        return $productBlock;
    }

    /**
     * @param \Magento\Framework\View\Element\BlockInterface|null $block
     *
     * @return string
     */
    protected function getHtml($block)
    {
        return $block ? $block->toHtml() : '';
    }

    /**
     * @param $content
     *
     * @return string
     */
    protected function parseWysiwyg($content)
    {
        $content = $this->filter->filter($content);
        return $content;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function parseVariables(string $content)
    {
        if (strpos($content, SaveHandler::DEFAULT_CONTENT_VARIABLE) !== false) {
            $default = '';
            $nameInLayout = $this->getTab()->getNameInLayout();
            $block = $this->_layout->getBlock($nameInLayout);
            if ($block) {
                $default = $block->toHtml();
            }

            $content = str_replace(SaveHandler::DEFAULT_CONTENT_VARIABLE, $default, $content);
        }

        preg_match_all('@\{{(.+?) code="(.+?)"\}}@', $content, $matches);
        if (isset($matches[1]) && !empty($matches[1])) {
            foreach ($matches[1] as $key => $match) {
                $result = '';
                switch ($match) {
                    case 'amcustomtabs_attribute':
                        if ($this->getProduct() && isset($matches[2][$key])) {
                            $result = $this->getAttributeValue($this->getProduct(), $matches[2][$key]);
                        }
                        break;
                }

                $content = str_replace(
                    sprintf('{{%s code="%s"}}', $match, $matches[2][$key]),
                    $result,
                    $content
                );
            }
        }

        return $content;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string $attributeCode
     *
     * @return \Magento\Framework\Phrase|string
     */
    private function getAttributeValue($product, $attributeCode)
    {
        $attribute = $product->getResource()->getAttribute($attributeCode);
        $value = $attribute->getFrontend()->getValue($product);
        if (!$product->hasData($attribute->getAttributeCode())) {
            $value = __('N/A');
        } elseif ((string)$value == '') {
            $value = __('No');
        }

        return $value;
    }

    /**
     * @return TabsInterface
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     * @param TabsInterface $tab
     */
    public function setTab(TabsInterface $tab)
    {
        $this->tab = $tab;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->product) {
            $this->product = $this->registry->registry('product');
        }
        return $this->product;
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return $this->getTab() ? $this->getTab()->getIdentities() : [];
    }

    /**
     * Object manager for compatibility with old version
     *
     * @return \Magento\Catalog\ViewModel\Product\Listing\PreparePostData|null
     */
    protected function getViewModel()
    {
        $model = null;

        // @codingStandardsIgnoreLine
        $modelClass = '\Magento\Catalog\ViewModel\Product\Listing\PreparePostData';
        if (class_exists($modelClass)) {
            $model = \Magento\Framework\App\ObjectManager::getInstance()
                ->create($modelClass);
        }

        return $model;
    }
}
