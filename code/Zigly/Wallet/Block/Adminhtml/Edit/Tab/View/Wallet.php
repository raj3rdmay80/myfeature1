<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
namespace Zigly\Wallet\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Adminhtml customer manage pets grid block
 */
class Wallet extends \Magento\Backend\Block\Widget\Grid\Extended
{

    const CREDITED = 1;

    const DEBITED = 0;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $coreRegistry = null;

    /**
     * @var \Zigly\Wallet\Model\ResourceModel\Wallet\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Zigly\Wallet\Model\ResourceModel\Wallet\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Zigly\Wallet\Model\ResourceModel\Wallet\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        $this->_logger = $logger;
        $this->coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize the service grid.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('Wallet_view_customer_grid');
        $this->setDefaultSort('wallet_id', 'desc');
        $this->setSortable(false);
        $this->setPagerVisibility(true);
        $this->setFilterVisibility(false);
        $this->setUseAjax(true);

    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $customerid = $this->getCustomerId();
        $collection = $this->collectionFactory->create()->addFieldtoFilter('visibility', ['eq' => 1])->addFieldtoFilter('customer_id',$customerid)->setOrder('wallet_id', 'DESC');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        $customerid = $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        return $customerid;
    }

    /**
     * {@inheritdoc}
     */
     protected function _prepareColumns()
    {
        $this->addColumn(
            'comment',
            [
                'header' => __('Comment'),
                'index' => 'comment',
            ]
        );
        $this->addColumn(
            'amount',
            [
                'header' => __('Amount'),
                'index' => 'amount',
            ]
        );
        $this->addColumn(
            'flag',
            [
                'header' => __('Credit/Debit'),
                'index' => 'flag',
                'type' => 'options',
                'options' => [self::CREDITED => __('Credited'), self::DEBITED => __('Debited')],
            ]
        );
        $this->addColumn(
            'performed_by',
            [
                'header' => __('By'),
                'index' => 'performed_by',
            ]
        );
        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at',
                'type' => 'datetime',
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * Get headers visibility
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
    //  */
    public function getHeadersVisibility()
    {
        return $this->getCollection()->getSize() >= 0;
    }

    /**
     * {@inheritdoc}
     */
     public function getGridUrl()
    {
        return '';
    }

}
