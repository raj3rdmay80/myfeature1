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

namespace Ced\PincodeChecker\Block\Adminhtml\Pincode;

/**
 * Class Grid
 * @package Ced\PincodeChecker\Block\Adminhtml\Pincode
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Ced\PincodeChecker\Model\ResourceModel\Pincode\CollectionFactory
     */
    protected $pincodeCollectionFactory;

    /**
     * Grid constructor.
     * @param \Ced\PincodeChecker\Model\ResourceModel\Pincode\CollectionFactory $pincodeCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Ced\PincodeChecker\Model\ResourceModel\Pincode\CollectionFactory $pincodeCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    )
    {
        $this->pincodeCollectionFactory = $pincodeCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('pincodechecker');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return mixed
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->getStore($storeId);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->pincodeCollectionFactory->create());
        try {
            return parent::_prepareCollection();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', [
                'header' => __('ID'),
                'align' => 'right',
                'index' => 'id',
                'width' => '80px',
                'type' => 'text',
                'is_system' => true
            ]
        );

        $this->addColumn('zipcode', [
                'header' => __('Zipcode'),
                'align' => 'left',
                'type' => 'text',
                'index' => 'zipcode',
            ]
        );

        $this->addColumn('can_ship', [
                'header' => __('Shipment Available'),
                'align' => 'left',
                'index' => 'can_ship',
                'type' => 'options',
                'renderer' => 'Ced\PincodeChecker\Block\Adminhtml\Pincode\Grid\Renderer\Canship',
                'options' => [0 => 'No', 1 => 'Yes']
            ]
        );

        $this->addColumn('can_cod', [
                'header' => __('COD Available'),
                'align' => 'left',
                'index' => 'can_cod',
                'type' => 'options',
                'renderer' => 'Ced\PincodeChecker\Block\Adminhtml\Pincode\Grid\Renderer\Cancod',
                'options' => [0 => 'No', 1 => 'Yes']
            ]
        );

        $this->addColumn('days_to_deliver', [
                'header' => __('Days To Deliver'),
                'align' => 'left',
                'index' => 'days_to_deliver',
                'type' => 'text',
            ]
        );

        $this->addColumn('action', [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => ['base' => '*/*/edit'],
                        'field' => 'id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'action',
                'is_system' => true
            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * @return $this|\Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id'); 
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete Item(s)'),
                'url' => $this->getUrl('*/*/deletezip'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = array('0' => 'No', '1' => 'Yes');

        $this->getMassactionBlock()->addItem('can_ship_status',
            [
                'label' => __('Change Can Ship(s) Status'),
                'url' => $this->getUrl('*/*/shipstatus/', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'ship_status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => ('Status'),
                        'default' => '-1',
                        'values' => $statuses,
                    ]
                ]
            ]
        );

        $this->getMassactionBlock()->addItem('can_cod_status',
            [
                'label' => __('Change Can COD(s) Status'),
                'url' => $this->getUrl('*/*/codstatus/', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'cod_status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => ('Status'),
                        'default' => '-1',
                        'values' => $statuses,
                    ]
                ]
            ]
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

}
