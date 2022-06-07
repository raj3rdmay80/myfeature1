<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Setup\Patch\Data;

use Amasty\JetTheme\Api\Data\SocialLinkInterfaceFactory;
use Amasty\JetTheme\Api\SocialLinkRepositoryInterface;
use Amasty\JetTheme\Model\ConfigProvider;
use Amasty\JetTheme\Model\OptionSource\Status;
use Amasty\JetTheme\Model\SocialLink\SvgProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class MigrateSocialLink implements DataPatchInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SocialLinkRepositoryInterface
     */
    private $socialLinkRepository;

    /**
     * @var SocialLinkInterfaceFactory
     */
    private $socialLinkFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SvgProvider
     */
    private $svgProvider;

    /**
     * @var string[]
     */
    private $socialLinkMap = [
        'google' => 'google',
        'youtube' => 'youtube',
        'facebook' => 'facebook',
        'twitter' => 'twitter',
    ];

    public function __construct(
        ConfigProvider $configProvider,
        StoreManagerInterface $storeManager,
        SocialLinkRepositoryInterface $socialLinkRepository,
        SocialLinkInterfaceFactory $socialLinkFactory,
        ScopeConfigInterface $scopeConfig,
        SvgProvider $svgProvider
    ) {
        $this->configProvider = $configProvider;
        $this->storeManager = $storeManager;
        $this->socialLinkRepository = $socialLinkRepository;
        $this->socialLinkFactory = $socialLinkFactory;
        $this->scopeConfig = $scopeConfig;
        $this->svgProvider = $svgProvider;
    }

    /**
     * @return void
     */
    public function apply(): void
    {
        $linksData = array_fill_keys(array_keys($this->socialLinkMap), []);
        foreach ($this->storeManager->getStores() as $store) {
            if ($this->isShowLinksBlock((int)$store->getId())) {
                $linksData = $this->updateLinksData($linksData, (int)$store->getId());
            }
        }

        $this->generateModelsAndSave($linksData);
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @param array $linksData
     * @param int $storeId
     *
     * @return array
     */
    private function updateLinksData(array $linksData, int $storeId): array
    {
        foreach ($this->socialLinkMap as $socialLinkCode => $socialIcon) {
            if ($link = $this->getSocialLinkByCode($socialLinkCode, (int)$storeId)) {
                if (empty($linksData[$socialLinkCode][$link]['store_ids'])) {
                    $linksData[$socialLinkCode][$link]['store_ids'] = [];
                }

                $linksData[$socialLinkCode][$link]['store_ids'][] = $storeId;
            }
        }

        return $linksData;
    }

    /**
     * @param string $socialCode
     * @param int $storeId
     * @return mixed
     */
    private function getSocialLinkByCode(string $socialCode, int $storeId)
    {
        return $this->scopeConfig->getValue(
            'amasty_jettheme/' . ConfigProvider::SOCIAL_LINKS . $socialCode,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return bool
     */
    private function isShowLinksBlock(int $storeId): bool
    {
        return $this->scopeConfig->isSetFlag(
            'amasty_jettheme/' . ConfigProvider::SOCIAL_LINKS . ConfigProvider::SHOW_LINKS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param array $linksData
     */
    private function generateModelsAndSave(array $linksData): void
    {
        $sortOrder = 1;
        foreach ($linksData as $socialLinkKey => $linkData) {
            foreach ($linkData as $link => $storeData) {
                $socialLinkModel = $this->socialLinkFactory->create();
                $socialLinkModel->setLink($link);
                $socialLinkModel->setSortOrder($sortOrder);
                $socialLinkModel->setTitle(ucfirst($socialLinkKey));
                $socialLinkModel->setDefaultIcon($this->socialLinkMap[$socialLinkKey]);
                $socialLinkModel->setDefaultIconContent(
                    base64_encode($this->svgProvider->getSvgContentByKey($this->socialLinkMap[$socialLinkKey]))
                );
                $socialLinkModel->setStores($storeData['store_ids']);
                $socialLinkModel->setStatus(Status::STATUS_ACTIVE);
                $socialLinkModel->setData('skip_image_upload', true);

                $this->socialLinkRepository->save($socialLinkModel);
            }

            $sortOrder++;
        }
    }
}
