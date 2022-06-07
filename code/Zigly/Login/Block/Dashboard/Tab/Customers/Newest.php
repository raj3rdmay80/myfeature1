<?php

namespace Zigly\Login\Block\Dashboard\Tab\Customers;

/**
 * Adminhtml dashboard most recent customers grid
 *
 */
class Newest extends \Magento\Backend\Block\Dashboard\Tab\Customers\Newest
{

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', ['header' => __('Customer'), 'sortable' => false, 'index' => 'firstname']);

        $this->addColumn(
            'orders_count',
            [
                'header' => __('Orders'),
                'sortable' => false,
                'index' => 'orders_count',
                'type' => 'number',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $baseCurrencyCode = (string)$this->_storeManager->getStore(
            (int)$this->getParam('store')
        )->getBaseCurrencyCode();

        $this->addColumn(
            'orders_avg_amount',
            [
                'header' => __('Average'),
                'sortable' => false,
                'type' => 'currency',
                'currency_code' => $baseCurrencyCode,
                'index' => 'orders_avg_amount',
                'renderer' => \Magento\Reports\Block\Adminhtml\Grid\Column\Renderer\Currency::class,
                'header_css_class' => 'col-avg',
                'column_css_class' => 'col-avg'
            ]
        );

        $this->addColumn(
            'orders_sum_amount',
            [
                'header' => __('Total'),
                'sortable' => false,
                'type' => 'currency',
                'currency_code' => $baseCurrencyCode,
                'index' => 'orders_sum_amount',
                'renderer' => \Magento\Reports\Block\Adminhtml\Grid\Column\Renderer\Currency::class,
                'header_css_class' => 'col-total',
                'column_css_class' => 'col-total'
            ]
        );

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return $this;
    }

}

