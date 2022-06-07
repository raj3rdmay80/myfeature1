<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Controller\Adminhtml\ColorSwitcher;

use Amasty\Base\Model\Serializer;
use Amasty\JetTheme\Model\Style\FileReader;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class PresetData extends Action
{
    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        FileReader $fileReader,
        Serializer $serializer,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->fileReader = $fileReader;
        $this->serializer = $serializer;
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

            $presetName = $this->getRequest()->getParam('preset_id', 'default');
            $response['data'] = $this->serializer->unserialize($this->fileReader->getFileContent($presetName));
        } catch (\Exception $e) {
            $response['error'] = true;
            $response['message'] = __('Could not load color preset content.');
        }

        return $resultJson->setData($response);
    }
}
