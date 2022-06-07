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
use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Store\Model\StoreManagerInterface;

class SocialLinkProvider
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var SocialLinkRepositoryInterface
     */
    private $socialLinkRepository;

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
        SocialLinkRepositoryInterface $socialLinkRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SortOrderBuilder $sortOrderBuilder,
        StoreManagerInterface $storeManager,
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
        $this->socialLinkRepository = $socialLinkRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * @return SocialLinkInterface[]
     */
    public function getSocialLinksForCurrentStore(): array
    {
        $sortOrder = $this->sortOrderBuilder->setField('sort_order')->setDirection('ASC')->create();
        $searchCriteria = $this->searchCriteriaBuilderFactory->create()
            ->addFilter(SocialLinkInterface::STATUS, 1)
            ->addFilter(
                SocialLinkInterface::STORE_SOCIAL_STORE_ID_FIELD,
                [0, $this->storeManager->getStore()->getId()],
                'in'
            )
            ->setSortOrders([$sortOrder])->create();

        return $this->socialLinkRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @return bool
     */
    public function isShowLinksBlock(): bool
    {
        return $this->configProvider->isShowLinksBlock();
    }
}
