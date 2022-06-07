<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\PaymentLink;

use Amasty\JetTheme\Api\Data\PaymentLinkInterface;
use Amasty\JetTheme\Api\PaymentLinkRepositoryInterface;
use Amasty\JetTheme\Model\ImageProvider;
use Amasty\JetTheme\Model\PaymentLink\ResourceModel\PaymentLink\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var PaymentLinkRepositoryInterface
     */
    private $paymentLinkRepository;

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
        PaymentLinkRepositoryInterface $paymentLinkRepository,
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
        $this->paymentLinkRepository = $paymentLinkRepository;
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
        if ($paymentId = $this->request->getParam($this->getRequestFieldName(), null)) {
            $data = $this->paymentLinkRepository->get((int)$paymentId)->getData();
            if (isset($data[PaymentLinkInterface::ICON_FILE])) {
                $data[PaymentLinkInterface::ICON_FILE] = [
                    [
                        'name' => $data[PaymentLinkInterface::ICON_FILE],
                        'url'  => $this->imageProvider->getThumbnailUrl($data[PaymentLinkInterface::ICON_FILE])
                    ]
                ];
            }

            return [$paymentId => $data];
        }

        return [];
    }
}
