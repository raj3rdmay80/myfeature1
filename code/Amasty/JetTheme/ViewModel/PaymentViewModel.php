<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel;

use Amasty\JetTheme\Model\ConfigProvider;
use Amasty\JetTheme\Model\ImageProvider;
use Amasty\JetTheme\Model\PaymentLink\PaymentLinkProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Amasty\JetTheme\Api\Data\PaymentLinkInterface;

class PaymentViewModel implements ArgumentInterface
{
    const PAYMENT_ICON_WIDTH = 64;
    const PAYMENT_ICON_HEIGHT = 64;

    /**
     * @var PaymentLinkProvider
     */
    private $paymentLinkProvider;

    /**
     * @var ImageProvider
     */
    private $imageProvider;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        PaymentLinkProvider $paymentLinkProvider,
        ConfigProvider $configProvider,
        ImageProvider $imageProvider
    ) {
        $this->paymentLinkProvider = $paymentLinkProvider;
        $this->imageProvider = $imageProvider;
        $this->configProvider = $configProvider;
    }

    /**
     * @return PaymentLinkInterface[]
     */
    public function getPaymentLinks(): array
    {
        return $this->paymentLinkProvider->getPaymentLinksForCurrentStore();
    }

    /**
     * @param $imageName
     * @return string
     * @throws NoSuchEntityException
     */
    public function getPaymentImage($imageName): string
    {
        return $this->imageProvider->getResizedUrl($imageName, self::PAYMENT_ICON_WIDTH, self::PAYMENT_ICON_HEIGHT);
    }

    /**
     * @return bool
     */
    public function isShowPaymentLinksFooterBlock(): bool
    {
        return $this->configProvider->isShowPaymentLinksFooterBlock();
    }

    /**
     * @return bool
     */
    public function isShowPaymentLinksMinicartBlock(): bool
    {
        return $this->configProvider->isShowPaymentLinksMinicartBlock();
    }

    /**
     * @return bool
     */
    public function isShowPaymentLinksCartPageBlock(): bool
    {
        return $this->configProvider->isShowPaymentLinksCartPageBlock();
    }

    /**
     * @return bool
     */
    public function isShowPaymentLinksProductPageBlock(): bool
    {
        return $this->configProvider->isShowPaymentLinksProductPageBlock();
    }
}
