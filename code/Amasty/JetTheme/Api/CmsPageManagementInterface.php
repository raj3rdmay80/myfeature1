<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


namespace Amasty\JetTheme\Api;

/**
 * Interface for managing cms pages
 * @api
 */
interface CmsPageManagementInterface
{
    /**
     * @return array
     */
    public function getAllPages(): array;

    /**
     * @param string $pageCode
     * @return array|null
     */
    public function getAttributesByPageCode(string $pageCode): ?array;

    /**
     * @param string $pageCode
     * @param string $attributeCode
     * @return mixed
     */
    public function getAttributeValue(string $pageCode, string $attributeCode);
}
