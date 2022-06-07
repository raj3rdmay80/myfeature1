<?php
declare(strict_types=1);

namespace Zigly\Mobilehome\Controller\Adminhtml\Mobilehome;

use Magento\Framework\Exception\LocalizedException;
use Zigly\Mobilehome\Model\ImageUploader;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        ImageUploader $imageUploader,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->imageUploader = $imageUploader;
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
            $id = $this->getRequest()->getParam('mobilehome_id');
        
            $model = $this->_objectManager->create(\Zigly\Mobilehome\Model\Mobilehome::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Mobilehome no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            if (isset($data['image'][0]['name']) && isset($data['image'][0]['tmp_name'])) {
                    $data['image'] = $data['image'][0]['name'];
                    $this->imageUploader->moveFileFromTmp($data['image']);
            } elseif (isset($data['image'][0]['name']) && !isset($data['image'][0]['tmp_name'])) {
                $data['image'] = $data['image'][0]['name'];
            } else {
                $data['image'] = '';
            }
            
            $model->setData($data);
        
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Mobilehome.'));
                $this->dataPersistor->clear('zigly_mobilehome_mobilehome');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['mobilehome_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Mobilehome.'));
            }
        
            $this->dataPersistor->set('zigly_mobilehome_mobilehome', $data);
            return $resultRedirect->setPath('*/*/edit', ['mobilehome_id' => $this->getRequest()->getParam('mobilehome_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

