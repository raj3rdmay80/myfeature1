<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_PincodeChecker
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\PincodeChecker\Controller\Adminhtml\Pincodechecker;

use Magento\Backend\App\Action;

/**
 * Class Edit
 * @package Ced\PincodeChecker\Controller\Adminhtml\Pincodechecker
 */
class Edit extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Ced\PincodeChecker\Model\PincodeFactory
     */
    protected $pincodeFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Ced\PincodeChecker\Model\PincodeFactory $pincodeFactory
     * @param \Magento\Backend\Model\Session $backendSession
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Ced\PincodeChecker\Model\PincodeFactory $pincodeFactory,
        \Magento\Backend\Model\Session $backendSession
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->pincodeFactory = $pincodeFactory;
        $this->backendSession = $backendSession;
        parent::__construct($context);
    }


    /**
     * @return \Magento\Framework\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_PincodeChecker::pincodechecker_pincodechecker')
            ->addBreadcrumb(__('PincodeChecker'), __('PincodeChecker'))
            ->addBreadcrumb(__('Manage Pincodes'), __('Manage Pincodes'));
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->pincodeFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This entry no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->backendSession->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('pincode_data', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Item') : __('New Item'),
            $id ? __('Edit Item') : __('New Item')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Zipcode'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Item'));

        return $resultPage;
    }
}
