<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


namespace Amasty\JetTheme\Controller\Adminhtml\ImageOptimizer;

use Amasty\ImageOptimizer\Model\ConfigProvider;
use Amasty\ImageOptimizer\Model\Image\CheckTools;
use Amasty\ImageOptimizer\Model\Image\GenerateQueue;
use Amasty\JetTheme\Model\ImageOptimizer\ImageSettingsGenerator;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Start extends Action
{
    const ADMIN_RESOURCE = 'Amasty_JetTheme::config';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var GenerateQueue
     */
    private $generateQueue;

    /**
     * @var ImageSettingsGenerator
     */
    private $imageSettingsGenerator;

    /**
     * @var CheckTools
     */
    private $checkTools;

    public function __construct(
        GenerateQueue $generateQueue,
        ConfigProvider $configProvider,
        ImageSettingsGenerator $imageSettingsGenerator,
        CheckTools $checkTools,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->configProvider = $configProvider;
        $this->generateQueue = $generateQueue;
        $this->imageSettingsGenerator = $imageSettingsGenerator;
        $this->checkTools = $checkTools;
    }

    public function execute()
    {
        $imageSettings = $this->imageSettingsGenerator->getSettingsToProcess();
        $errors = $this->checkTools->check($imageSettings);
        if (count($errors)) {
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData([
                'errors' => $errors
            ]);
        }

        $queueSize = $this->generateQueue->generateQueue([$imageSettings]);

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData([
            'filesCount' => $queueSize,
            'filesPerRequest' => $this->configProvider->getImagesPerRequest()
        ]);
    }
}
