<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Catalog
 */
namespace Zigly\Catalog\Block\Html;

use Magento\Backend\Model\Menu;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\Node\Collection;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\CollectionFactory as BrandCollection;

/**
 * Html page top menu block
 *
 * @api
 * @since 100.0.2
 */
class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    /**
     * Cache identities
     *
     * @var array
     */
    protected $identities = [];

    /**
     * Top menu data tree
     *
     * @var Node
     */
    protected $_menu;

    /**
     * @var NodeFactory
     */
    private $nodeFactory;

    /**
     * @var TreeFactory
     */
    private $treeFactory;

     /**
     * Topmenu constructor.
     * @param Template\Context $context
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        BrandCollection $brandCollection,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->brandCollection = $brandCollection;
        $this->storeManager = $storeManager;
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
    }

    /**
     * Count All Subnavigation Items
     *
     * @param Menu $items
     * @return int
     */
    protected function _countItems($items)
    {
        $total = $items->count();
        foreach ($items as $item) {
            /** @var $item Menu\Item */
            if ($item->hasChildren()) {
                $total += $this->_countItems($item->getChildren());
            }
        }
        return $total;
    }

    /**
     * Building Array with Column Brake Stops
     *
     * @param Menu $items
     * @param int $limit
     * @return array|void
     *
     * @todo: Add Depth Level limit, and better logic for columns
     */
    protected function _columnBrake($items, $limit)
    {
        $total = $this->_countItems($items);
        if ($total <= $limit) {
            return;
        }

        $result[] = ['total' => $total, 'max' => (int)ceil($total / ceil($total / $limit))];

        $count = 0;
        $firstCol = true;

        foreach ($items as $item) {
            $place = $this->_countItems($item->getChildren()) + 1;
            $count += $place;

            if ($place >= $limit) {
                $colbrake = !$firstCol;
                $count = 0;
            } elseif ($count >= $limit) {
                $colbrake = !$firstCol;
                $count = $place;
            } else {
                $colbrake = false;
            }

            $result[] = ['place' => $place, 'colbrake' => $colbrake];

            $firstCol = false;
        }

        return $result;
    }

    /**
     * Add sub menu HTML code for current menu item
     *
     * @param Node $child
     * @param string $childLevel
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string HTML code
     */
    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        $html = '';
        if ($child->getId() == "category-node-70")
                {
                    $optionCollectionFeatured = $this->brandCollection->create()->join(['option' => 'eav_attribute_option_value'], 'option.option_id = main_table.value', 'IF(main_table.title != \'\', main_table.title, option.value) as title');
                    $optionCollectionFeatured->addFieldToSelect('*')->addFieldToFilter('is_show_in_slider','1')->addFieldToFilter('is_featured','1')->setOrder('slider_position','ASC');
                    $optionCollectionNotFeatured = $this->brandCollection->create()->join(['option' => 'eav_attribute_option_value'], 'option.option_id = main_table.value', 'IF(main_table.title != \'\', main_table.title, option.value) as title');
                    $optionCollectionNotFeatured->addFieldToSelect('*')->addFieldToFilter('is_show_in_slider','1')->addFieldToFilter('is_featured','0')->setOrder('slider_position','ASC');
                    $mediaUrl = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
                    $placeHolderImg = $mediaUrl . 'catalog/product/placeholder/' . $this->storeManager->getStore()->getConfig("catalog/placeholder/image_placeholder");
                    $html.= '<ul class="level1 submenu">';
                    $html.= '<div class="product data items pc-tab"
                                data-mage-init=\'{"mage/tabs": {"openedState": "active", "active": 0, "disabled": [2], "disabledState": "disabled"}}\'>
                                <div class="data item title" data-role="collapsible">
                                    <a class="data switch" data-toggle="trigger" href="#tab1">Featured</a>
                                </div>
                                
                                <div id="tab1" class="data item content" data-role="content"><ul>';
                    foreach ($optionCollectionFeatured as $item) {
        
                        $imgUrl = ($item->getImage()) ? $mediaUrl . "amasty/shopby/option_images/" . $item->getImage() : $placeHolderImg;
                        $html.= '<li class="brand-item column">';
                        $html.= '<a href="' . $this->getBaseUrl() . 'brand/' . str_replace(" ","-",$item->getTitle()) . '">';
                        $html.= '<img src="' . $imgUrl . '" width="111" height="111"/>';
                        $html.= '<div class="brand-title">' . $item->getTitle() . '</div>';
                        $html.= '</a>';
                        $html.= '</li>';
                    }
                    $html.= '</ul></div><div class="data item title" data-role="collapsible">
                                    <a class="data switch" data-toggle="trigger" href="#tab2">Other Brands</a>
                                </div><div id="tab2" class="data item content" data-role="content"><ul>';
                    foreach ($optionCollectionNotFeatured as $item) {
        
                        $imgUrl = ($item->getImage()) ? $mediaUrl . "amasty/shopby/option_images/" . $item->getImage() : $placeHolderImg;
                        $html.= '<li class="brand-item column">';
                        $html.= '<a href="' . $this->getBaseUrl() . 'brand/' . str_replace(" ","-",$item->getTitle()) . '">';
                        $html.= '<img src="' . $imgUrl . '" width="111" height="111"/>';
                        $html.= '<div class="brand-title">' . $item->getTitle() . '</div>';
                        $html.= '</a>';
                        $html.= '</li>';
                    }
                    $html.= '</ul></div>
                                </div>';
                    $html.= '</ul>';
                } else if ($child->getId() == "category-node-15")
                     {
                        $html.= $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId('concern_menu')->toHtml();
                     }

        if (!$child->hasChildren()) {
            return $html;
        }

        $colStops = [];
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        $html .= '<ul class="level' . $childLevel . ' ' . $childrenWrapClass . '">';
        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);
        $html .= '</ul>';

        return $html;
    }

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param Node $menuTree
     * @param string $childrenWrapClass
     * @param int $limit
     * @param array $colBrakes
     * @return string
     */
    protected function _getHtml(
        Node $menuTree,
        $childrenWrapClass,
        $limit,
        array $colBrakes = []
    ) {
        $html = '';

        $children = $menuTree->getChildren();
        $childLevel = $this->getChildLevel($menuTree->getLevel());
        $this->removeChildrenWithoutActiveParent($children, $childLevel);

        $counter = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        /** @var Node $child */
        foreach ($children as $child) {
            $child->setLevel($childLevel);
            $child->setIsFirst($counter === 1);
            $child->setIsLast($counter === $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel === 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $this->setCurrentClass($child, $outermostClass);
            }

            if ($this->shouldAddNewColumn($colBrakes, $counter)) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            if ($child->getId() == "category-node-15" || $child->getId() == "category-node-70") {
                $html .= '<a href="javascript:void(0);" ' . $outermostClassCode . '><span>' . $this->escapeHtml(
                    $child->getName()
                ) . '</span></a>' . $this->_addSubMenu(
                    $child,
                    $childLevel,
                    $childrenWrapClass,
                    $limit
                ) . '</li>';
            } else {
                $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>' . $this->escapeHtml(
                    $child->getName()
                ) . '</span></a>' . $this->_addSubMenu(
                    $child,
                    $childLevel,
                    $childrenWrapClass,
                    $limit
                ) . '</li>';
            }
            $counter++;
        }

        if (is_array($colBrakes) && !empty($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }

        return $html;
    }

    /**
     * Generates string with all attributes that should be present in menu item element
     *
     * @param Node $item
     * @return string
     */
    protected function _getRenderedMenuItemAttributes(Node $item)
    {
        $html = '';
        foreach ($this->_getMenuItemAttributes($item) as $attributeName => $attributeValue) {
            $html .= ' ' . $attributeName . '="' . str_replace('"', '\"', $attributeValue) . '"';
        }
        return $html;
    }

    /**
     * Get tags array for saving cache
     *
     * @return array
     * @since 100.1.0
     */
    protected function getCacheTags()
    {
        return array_merge(parent::getCacheTags(), $this->getIdentities());
    }
    /**
     * Remove children from collection when the parent is not active
     *
     * @param Collection $children
     * @param int $childLevel
     * @return void
     */

    private function removeChildrenWithoutActiveParent(Collection $children, int $childLevel): void
    {
        /** @var Node $child */
        foreach ($children as $child) {
            if ($childLevel === 0 && $child->getData('is_parent_active') === false) {
                $children->delete($child);
            }
        }
    }

    /**
     * Retrieve child level based on parent level
     *
     * @param int $parentLevel
     *
     * @return int
     */
    private function getChildLevel($parentLevel): int
    {
        return $parentLevel === null ? 0 : $parentLevel + 1;
    }

    /**
     * Check if new column should be added.
     *
     * @param array $colBrakes
     * @param int $counter
     * @return bool
     */
    private function shouldAddNewColumn(array $colBrakes, int $counter): bool
    {
        return count($colBrakes) && $colBrakes[$counter]['colbrake'];
    }

    /**
     * Set current class.
     *
     * @param Node $child
     * @param string $outermostClass
     */
    private function setCurrentClass(Node $child, string $outermostClass): void
    {
        $currentClass = $child->getClass();
        if (empty($currentClass)) {
            $child->setClass($outermostClass);
        } else {
            $child->setClass($currentClass . ' ' . $outermostClass);
        }
    }
}
