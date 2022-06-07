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
use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Store\Model\StoreManagerInterface;

class PaymentLinkProvider
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var PaymentLinkRepositoryInterface
     */
    private $paymentLinkRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        PaymentLinkRepositoryInterface $paymentLinkRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SortOrderBuilder $sortOrderBuilder,
        StoreManagerInterface $storeManager,
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
        $this->paymentLinkRepository = $paymentLinkRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * @return PaymentLinkInterface[]
     */
    public function getPaymentLinksForCurrentStore(): array
    {
        $sortOrder = $this->sortOrderBuilder->setField('sort_order')->setDirection('ASC')->create();
        $searchCriteria = $this->searchCriteriaBuilderFactory->create()
            ->addFilter(PaymentLinkInterface::STATUS, 1)
            ->addFilter(
                PaymentLinkInterface::STORE_PAYMENT_STORE_ID_FIELD,
                [0, $this->storeManager->getStore()->getId()],
                'in'
            )
            ->setSortOrders([$sortOrder])->create();

        return $this->paymentLinkRepository->getList($searchCriteria)->getItems();
    }
}
