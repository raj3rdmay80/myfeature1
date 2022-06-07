<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyLite
 */


namespace Amasty\ShopbyLite\Plugin\Ajax;

use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\Template;

class ProductListWrapper
{
    /**
     * @var Http
     */
    private $request;

    public function __construct(
        Http $request
    ) {
        $this->request = $request;
    }

    /**
     * @param Template $subject
     * @param string $result
     *
     * @return string
     */
    public function afterToHtml(Template $subject, string $result): string
    {
        if (!$result
            || strpos($subject->getNameInLayout(), 'product\productslist') !== false // do not wrap widget block
            || $this->request->getParam('is_scroll')
        ) {
            return $result;
        }

        return sprintf('<div id="amasty-shopby-product-list">%s</div>', $result);
    }
}
