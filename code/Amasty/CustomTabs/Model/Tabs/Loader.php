<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Model\Tabs;

use Magento\Framework\App\Cache\Type\Layout as LayoutCache;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Loader
 */
class Loader
{
    const TABS_LOADED_FLAG = 'TABS_LOADED_STORE_';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LayoutCache
     */
    private $layoutCache;

    /**
     * @var Loader\ReadHandler
     */
    private $readHandler;

    /**
     * @var Loader\SaveHandler
     */
    private $saveHandler;

    public function __construct(
        StoreManagerInterface $storeManager,
        LayoutCache $layoutCache,
        Loader\ReadHandler $readHandler,
        Loader\SaveHandler $saveHandler
    ) {
        $this->storeManager = $storeManager;
        $this->layoutCache = $layoutCache;
        $this->readHandler = $readHandler;
        $this->saveHandler = $saveHandler;
    }

    public function execute()
    {
        foreach ($this->storeManager->getStores() as $store) {
            $storeId = $store->getId();
            if ($this->layoutCache->load(self::TABS_LOADED_FLAG . $storeId)) {
                continue;
            }
            $layout = $this->readHandler->execute($storeId);
            $this->saveHandler->execute($layout, $storeId);
            $this->layoutCache->save('loaded', self::TABS_LOADED_FLAG . $storeId);
        }
    }
}
