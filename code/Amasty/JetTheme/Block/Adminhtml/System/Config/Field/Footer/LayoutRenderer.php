<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Block\Adminhtml\System\Config\Field\Footer;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;

class LayoutRenderer extends Template
{
    /**
     * @var string
     */
    private $elementName;

    /**
     * @var string
     */
    private $elementId;

    /**
     * @var string
     */
    private $elementValue;

    /**
     * @var array
     */
    private $layoutConfig = [];

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        Template\Context $context,
        SerializerInterface $serializer,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->serializer = $serializer;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("Amasty_JetTheme::system/config/field/footer/layout.phtml");
    }

    /**
     * @return string
     */
    public function getElementValue(): string
    {
        return $this->elementValue;
    }

    /**
     * @param string $elementValue
     *
     * @return $this
     */
    public function setElementValue(string $elementValue): self
    {
        $this->elementValue = $elementValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getElementName(): string
    {
        return $this->elementName;
    }

    /**
     * @param string $elementName
     *
     * @return $this
     */
    public function setElementName(string $elementName): self
    {
        $this->elementName = $elementName;

        return $this;
    }

    /**
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }

    /**
     * @param string $elementId
     *
     * @return $this
     */
    public function setElementId(string $elementId): self
    {
        $this->elementId = $elementId;

        return $this;
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setLayoutConfig(array $config): self
    {
        $this->layoutConfig = $config;

        return $this;
    }

    /**
     * @return array
     */
    public function getLayoutConfig(): array
    {
        return $this->layoutConfig;
    }

    /**
     * @return string
     */
    public function getLayoutConfigJson(): string
    {
        return $this->serializer->serialize($this->getLayoutConfig());
    }
}
