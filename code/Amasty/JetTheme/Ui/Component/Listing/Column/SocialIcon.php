<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Ui\Component\Listing\Column;

use Amasty\JetTheme\Model\ImageProvider;
use Amasty\JetTheme\Model\SocialLink\SvgProvider;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class SocialIcon extends Column
{
    /**
     * @var ImageProvider
     */
    private $imageProvider;

    /**
     * @var SvgProvider
     */
    private $svgProvider;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ImageProvider $imageProvider,
        SvgProvider $svgProvider,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageProvider = $imageProvider;
        $this->svgProvider = $svgProvider;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$fieldName . '_src'] = $item['default_icon']
                    ? $this->svgProvider->getSvgUrlByKey($item['default_icon'])
                    : $this->imageProvider->getThumbnailUrl($item[$this->getData('name')]);
                $item[$fieldName . '_alt'] = ucfirst($item['title']);
            }
        }

        return $dataSource;
    }
}
