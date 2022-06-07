<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Controller\Adminhtml\StyleSwitcher;

use Amasty\Base\Model\Serializer;
use Amasty\JetTheme\Model\Style\FileReader;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Filter\Template;
use Psr\Log\LoggerInterface;

class StyleData extends Action
{
    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Template
     */
    private $templateFilter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        FileReader $fileReader,
        Serializer $serializer,
        Template $templateFilter,
        LoggerInterface $logger,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->fileReader = $fileReader;
        $this->serializer = $serializer;
        $this->templateFilter = $templateFilter;
        $this->logger = $logger;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $response = [
            'error' => false,
            'data' => []
        ];
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            if (!$this->getRequest()->isAjax()) {
                $response['error'] = true;
                $response['message'] = __('The request must be of type Ajax');

                return $resultJson->setData($response);
            }

            $styleName = $this->getRequest()->getParam('style_id', 'food');
            $styleContent = $this->fileReader->getFileContent($styleName, FileReader::STYLE);
            $processedStyleContent = $this->templateFilter->filter($styleContent);
            $response['data'] = $this->serializer->unserialize($processedStyleContent);
        } catch (\Exception $e) {
            $response['error'] = true;
            $response['message'] = __('Could not load style content. Please see exception log for details');
            $this->logger->critical($e);
        }

        return $resultJson->setData($response);
    }
}
