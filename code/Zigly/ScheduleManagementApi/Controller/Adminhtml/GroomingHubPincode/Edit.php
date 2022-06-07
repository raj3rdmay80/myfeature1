<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Controller\Adminhtml\GroomingHubPincode;

class Edit extends \Zigly\ScheduleManagementApi\Controller\Adminhtml\GroomingHubPincode
{

    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('pincode_id');
        $model = $this->_objectManager->create(\Zigly\ScheduleManagementApi\Model\GroomingHubPincode::class);
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Grooming hub pincode no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('zigly_schedulemanagementapi_groominghubpincode', $model);
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Groominghubpincode') : __('New Grooming hub pincode'),
            $id ? __('Edit Groominghubpincode') : __('New Grooming hub pincode')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Grooming hub pincodes'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Grooming hub pincode %1', $model->getId()) : __('New Grooming hub pincode'));
        return $resultPage;
    }
}

