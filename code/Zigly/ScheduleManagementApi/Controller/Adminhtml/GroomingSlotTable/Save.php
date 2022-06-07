<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Controller\Adminhtml\GroomingSlotTable;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
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
            $id = $this->getRequest()->getParam('groomingslottable_id');
        
            $model = $this->_objectManager->create(\Zigly\ScheduleManagementApi\Model\GroomingSlotTable::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Groomingslottable no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        
            $model->setData($data);
        
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Groomingslottable.'));
                $this->dataPersistor->clear('zigly_schedulemanagementapi_groomingslottable');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['groomingslottable_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Groomingslottable.'));
            }
        
            $this->dataPersistor->set('zigly_schedulemanagementapi_groomingslottable', $data);
            return $resultRedirect->setPath('*/*/edit', ['groomingslottable_id' => $this->getRequest()->getParam('groomingslottable_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

