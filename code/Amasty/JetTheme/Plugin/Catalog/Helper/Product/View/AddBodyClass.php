<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Plugin\Catalog\Helper\Product\View;

use Magento\Catalog\Helper\Product\View as ProductViewHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Result\Page as ResultPage;

class AddBodyClass
{
    const PRODUCT_ACTION_NAME = 'catalog_product_view';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Http
     */
    private $request;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Http $request,
        Manager $moduleManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param ProductViewHelper $subject
     * @param ProductViewHelper $result
     * @param ResultPage $resultPage
     * @param Product $product
     * @param DataObject|null $params
     */
    public function afterInitProductLayout(
        ProductViewHelper $subject,
        $result,
        ResultPage $resultPage,
        $product,
        $params = null
    ): ProductViewHelper {
        if ($this->moduleManager->isEnabled('Amasty_CustomTabs')
            && $this->scopeConfig->isSetFlag('amcustomtabs/general/allow_default')
            && $this->request->getFullActionName() == self::PRODUCT_ACTION_NAME) {
            $resultPage->getConfig()->addBodyClass('am-tabs-allow-default-edit');
        }

        return $result;
    }
}
