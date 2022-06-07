<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Block\Adminhtml\GroomerReview\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Zigly\GroomerReview\Model\ResourceModel\GroomerReview\CollectionFactory;

/**
 * Adminhtml review form
 */
class Tag extends \Magento\Backend\Block\Template
{

    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'tag.phtml';

    /** @var CollectionFactory */
    protected $orderCollection;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    /*protected $_systemStore;*/

    /**
     * @param Context $context
     * @param Registry $registry
     * @param OrderCollectionFactory $orderCollection
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        OrderCollectionFactory $orderCollection,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->orderCollection = $orderCollection;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    public function getFeedbackTag()
    {
        $model = $this->registry->registry('zigly_groomerreview_groomerreview');
        return $model->getTagName();
    }

    public function getReviewDetails()
    {
        $id = $this->getRequest()->getParam('groomerreview_id');
        $groomerReviewCollection = $this->collectionFactory->create()->addFieldToFilter('main_table.groomerreview_id', ['eq' => $id]);
        $groomerReviewCollection->getSelect()->join(['service'=>'zigly_service_grooming'],"main_table.service_id = service.entity_id",['service' => 'CONCAT("Grooming"," - ",service.center)', 'plan' => 'service.plan_name']);
        $groomerReviewCollection->getSelect()->join(['groomer'=>'zigly_groomer_groomer'],"main_table.groomer_id = groomer.groomer_id",['groomer_name' => 'groomer.name', 'groomer_id' => 'groomer.groomer_id']);
        $groomerReviewCollection->getSelect()->join(['order'=>'sales_order'],"service.entity_id = order.booking_id",['order_id' => 'order.increment_id']);
        $groomerReviewCollection->getSelect()->join(['customer'=>'customer_entity'],"service.customer_id = customer.entity_id",['customer_name' => 'customer.firstname', 'customer_id' => 'customer.entity_id']);
        $groomerReviewCollection->getFirstItem()->toArray();
        return $groomerReviewCollection->getData()[0];
    }

    /**
     * Return order increment id
     *
     * @return []
     */
    public function getOrderDetails() {
        $model = $this->registry->registry('zigly_groomerreview_groomerreview');
        $order = $this->orderCollection->create()->getItemByColumnValue('booking_id', $model->getServiceId());
        if (!empty($order)) {
            return [
                'increment_id' => $order->getIncrementId(),
                'url' => $this->getUrl('sales/order/view', array('order_id' => $order->getEntityId()))
            ];
        }
        return [];
    }
}