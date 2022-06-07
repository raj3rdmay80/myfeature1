<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\TransferConfigProcessor;

interface TransferConfigInterface
{
    /**
     * Process styles config
     *
     * @return string
     */
    public function process(): string;
}
