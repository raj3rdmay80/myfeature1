<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
declare(strict_types=1);

namespace Zigly\Sales\Plugin\Magento\Sales\Block\Adminhtml\Order;

class View
{

    public function beforeSetLayout(\Magento\Sales\Block\Adminhtml\Order\View $subject,$layout)
    {
        $order = $subject->getOrder();
        $cancelStatus = ['processing', 'pending'];
        $returnStatus = ['complete'];
        if (in_array($order->getStatus(), $cancelStatus)) {
            $subject->addButton(
                'order_newcancel',
                [
                    'label' => __('Cancel'),
                    'class' => 'order-cancel',
                    'onclick' => "",
                    'id' => 'order-view-cancel',
                    'sort_order' => 20
                ]
            );
        }
        if (in_array($order->getStatus(), $returnStatus)) {
            $subject->addButton(
                'order_returned_button',
                [
                    'label' => __('Return'),
                    'class' => __('return-order'),
                    'onclick' => "",
                    'id' => 'order-view-returned',
                    'sort_order' => 30
               ]
           );
        }
        $subject->removeButton('send_notification');
        return [$layout];
    }

    public function afterToHtml(\Magento\Sales\Block\Adminhtml\Order\View $subject, $result)
    {
        if($subject->getNameInLayout() == 'sales_order_edit'){
            $customBlockHtml = $subject->getLayout()->createBlock(\Zigly\Sales\Block\Adminhtml\Order\ModalBox::class,
                '_modal_box'
            )->setOrder($subject->getOrder())
                ->setTemplate('Zigly_Sales::order/modalbox.phtml')
                ->toHtml();
            return $result.$customBlockHtml;
        }
        return $result;
    }
}
