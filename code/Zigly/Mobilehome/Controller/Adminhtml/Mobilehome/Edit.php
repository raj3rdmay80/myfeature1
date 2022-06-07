<?php
declare(strict_types=1);

namespace Zigly\Mobilehome\Controller\Adminhtml\Mobilehome;

class Edit extends \Zigly\Mobilehome\Controller\Adminhtml\Mobilehome
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
        $id = $this->getRequest()->getParam('mobilehome_id');
        $model = $this->_objectManager->create(\Zigly\Mobilehome\Model\Mobilehome::class);
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Mobilehome no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('zigly_mobilehome_mobilehome', $model);
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Mobilehome') : __('New Mobilehome'),
            $id ? __('Edit Mobilehome') : __('New Mobilehome')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Mobilehomes'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Mobilehome %1', $model->getId()) : __('New Mobilehome'));
        return $resultPage;
    }
}

