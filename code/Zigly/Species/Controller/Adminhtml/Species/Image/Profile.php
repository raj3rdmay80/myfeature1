<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Species
 */
declare (strict_types = 1);

namespace Zigly\Species\Controller\Adminhtml\Species\Image;

use Zigly\Species\Model\Species\ImageUploader;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Profile extends Action implements HttpPostActionInterface
{
    /**
     * Image uploader
     *
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * Upload constructor.
     *
     * @param Context $context
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Context $context,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }
    /**
     * Upload file controller action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $imageId = $this->_request->getParam('param_name', 'profile_image');

        try {
            $result = $this->imageUploader->saveFileToTmpDir($imageId);
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
