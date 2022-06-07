<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\System\Store as SystemStore;

class Stores implements OptionSourceInterface
{
    /**
     * @var SystemStore
     */
    private $store;

    public function __construct(
        SystemStore $store
    ) {
        $this->store = $store;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return $this->store->getStoreValuesForForm(false, true);
    }
}
