<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


declare(strict_types=1);

namespace Amasty\CustomTabs\Model\Tabs\Indexer;

class CacheContext extends \Magento\Framework\Indexer\CacheContext
{
    /**
     * @var int
     */
    private $countElements = 0;

    /**
     * Register entity Ids
     *
     * @param string $cacheTag
     * @param array $ids
     *
     * @return $this
     */
    public function registerEntities($cacheTag, $ids)
    {
        parent::registerEntities($cacheTag, $ids);
        $this->countElements += count($ids);

        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->countElements;
    }

    /**
     * {@inheritDoc}
     */
    public function flush(): void
    {
        $this->countElements = 0;
        $this->entities = [];
    }
}
