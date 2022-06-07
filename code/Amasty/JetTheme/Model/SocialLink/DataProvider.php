<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\SocialLink;

use Amasty\JetTheme\Api\Data\SocialLinkInterface;
use Amasty\JetTheme\Api\SocialLinkRepositoryInterface;
use Amasty\JetTheme\Model\ImageProvider;
use Amasty\JetTheme\Model\SocialLink\ResourceModel\SocialLink\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var SocialLinkRepositoryInterface
     */
    private $socialLinkRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ImageProvider
     */
    private $imageProvider;

    /**
     * @var SvgProvider
     */
    private $svgProvider;

    public function __construct(
        SocialLinkRepositoryInterface $socialLinkRepository,
        RequestInterface $request,
        CollectionFactory $collectionFactory,
        ImageProvider $imageProvider,
        SvgProvider $svgProvider,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->socialLinkRepository = $socialLinkRepository;
        $this->request = $request;
        $this->imageProvider = $imageProvider;
        $this->svgProvider = $svgProvider;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array
    {
        if ($socialId = $this->request->getParam($this->getRequestFieldName(), null)) {
            $data = $this->socialLinkRepository->get((int)$socialId)->getData();
            if (isset($data[SocialLinkInterface::ICON_FILE])) {
                $data[SocialLinkInterface::ICON_FILE] = [
                    [
                        'name' => $data[SocialLinkInterface::ICON_FILE],
                        'url'  => $this->imageProvider->getThumbnailUrl($data[SocialLinkInterface::ICON_FILE])
                    ]
                ];
            }

            return [$socialId => $data];
        }

        return [];
    }
}
