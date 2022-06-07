<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Plugin\Framework\Module\Status;

use Amasty\JetTheme\Model\StoreThemeMapper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Status;

class CheckBeforeThemeDisable
{
    /**
     * @var StoreThemeMapper
     */
    private $storeThemeMapper;

    public function __construct(StoreThemeMapper $storeThemeMapper)
    {
        $this->storeThemeMapper = $storeThemeMapper;
    }

    /**
     * @param Status $subject
     * @param array $result
     * @param bool $isEnabled
     * @param string[] $modules
     */
    public function afterGetModulesToChange(Status $subject, array $result, bool $isEnabled, array $modules): array
    {
        if (in_array('Amasty_JetTheme', $modules) && $isEnabled === false) {
            $appliedStores = $this->getAppliedStores();
            if ($appliedStores) {
                throw new LocalizedException(
                    __(
                        'Before disable Amasty_JetTheme please use another theme for %1 store(s)',
                        implode(', ', $appliedStores)
                    )
                );
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getAppliedStores(): array
    {
        return $this->storeThemeMapper->getStoresAppliedTheme();
    }
}
