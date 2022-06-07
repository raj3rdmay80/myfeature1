<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


namespace Amasty\JetTheme\Setup;

use Amasty\JetTheme\Api\CmsPageManagementInterface;
use Amasty\JetTheme\Model\AttributeLoader;
use Amasty\JetTheme\Model\XmlReader;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;

class CmsPageManagement implements CmsPageManagementInterface
{
    const XML_FILE_NAME = 'cms_page_attributes.xml';
    const TAG_NAME = 'pages';

    /**
     * @var XmlReader
     */
    private $xmlReader;

    /**
     * @var AttributeLoader
     */
    private $attributeLoader;

    /**
     * @var array[]
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
    private function init()
    {
        $xmlArray = $this->xmlReader->readXmlFile(self::XML_FILE_NAME);

        if ($xmlArray) {
            $this->attributes = $this->attributeLoader->loadAttributes($xmlArray, self::TAG_NAME);
        }
    }

    /**
     * @return array
     */
    public function getAllPages(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $pageCode
     * @return array|null
     */
    public function getAttributesByPageCode(string $pageCode): ?array
    {
        return $this->attributes[$pageCode];
    }

    /**
     * @param string $pageCode
     * @param string $attributeCode
     * @return mixed
     * @throws LocalizedException
     */
    public function getAttributeValue(string $pageCode, string $attributeCode)
    {
        if (!array_key_exists($pageCode, $this->attributes)) {
            throw new LocalizedException(__('Attribute does not exist'));
        }

        return $this->attributes[$pageCode][$attributeCode];
    }
}
