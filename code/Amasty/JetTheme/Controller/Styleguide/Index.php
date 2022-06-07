<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Controller\Styleguide;

use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ConfigProvider $configProvider
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->configProvider = $configProvider;
        parent::__construct($context);
    }

    public function execute()
    {
        if (!$this->configProvider->isStyleGuideAccessEnabled()) {
            /** @var Forward $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $result->forward('noroute');
            return $result;
        }

        return $this->resultPageFactory->create(false, [
            'template' => 'Amasty_JetTheme::storybook/index.phtml',
        ]);
    }
}
