<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Species\Controller\Adminhtml\Species;

use Magento\Framework\Exception\LocalizedException;
use Zigly\Species\Model\Species\ImageUploader;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @param Context $context
     * @param ImageUploader $imageUploader
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        ImageUploader $imageUploader,
        DataPersistorInterface $dataPersistor
    ) {
        $this->imageUploader = $imageUploader;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('species_id');
            if (isset($data['profile_image'][0]['name']) && isset($data['profile_image'][0]['tmp_name'])) {
                $data['profile_image'] = $data['profile_image'][0]['name'];
                $this->imageUploader->moveFileFromTmp($data['profile_image']);
            } elseif (isset($data['profile_image'][0]['name']) && !isset($data['profile_image'][0]['tmp_name'])) {
                $data['profile_image'] = $data['profile_image'][0]['name'];
            } else {
                $data['profile_image'] = '';
            }
            $model = $this->_objectManager->create(\Zigly\Species\Model\Species::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Species no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        
            $model->setData($data);
        
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Species.'));
                $this->dataPersistor->clear('zigly_species_species');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['species_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Species.'));
            }
        
            $this->dataPersistor->set('zigly_species_species', $data);
            return $resultRedirect->setPath('*/*/edit', ['species_id' => $this->getRequest()->getParam('species_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

