<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


namespace Amasty\JetTheme\Setup;

use Amasty\JetTheme\Api\CmsBlockManagementInterface;
use Amasty\JetTheme\Model\AttributeLoader;
use Amasty\JetTheme\Model\XmlReader;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;

class CmsBlockManagement implements CmsBlockManagementInterface
{
    const XML_FILE_NAME = 'cms_block_attributes.xml';
    const TAG_NAME = 'blocks';

    /**
     * @var XmlReader
     */
    private $xmlReader;

    /**
     * @var AttributeLoader
     */
    private $attributeLoader;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(
        XmlReader $xmlReader,
        AttributeLoader $attributeLoader
    ) {
        $this->xmlReader = $xmlReader;
        $this->attributeLoader = $attributeLoader;
        $this->init();
    }

    /**
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function init(): void
    {
        $xmlArray = $this->xmlReader->readXmlFile(self::XML_FILE_NAME);

        if ($xmlArray) {
            $this->attributes = $this->attributeLoader->loadAttributes($xmlArray, self::TAG_NAME);
        }
    }

    /**
     * @return array
     */
    public function getAllBlocks(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $blockCode
     * @return array|null
     */
    public function getAttributesByBlockCode(string $blockCode): ?array
    {
        return $this->attributes[$blockCode];
    }

    /**
     * @param string $blockCode
     * @param string $attributeCode
     * @return mixed
     * @throws LocalizedException
     */
    public function getAttributeValue(string $blockCode, string $attributeCode)
    {
        if (!array_key_exists($blockCode, $this->attributes)) {
            throw new LocalizedException(__('Attribute does not exist'));
        }

        return $this->attributes[$blockCode][$attributeCode];
    }
}
