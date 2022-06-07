<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\Sales\Order\Creditmemo;

use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\OrderFactory;


class SgstCreditTotal extends Template
{
    /**
     * Order invoice
     *
     * @var \Magento\Sales\Model\Order\Creditmemo|null
     */
    protected $_creditmemo = null;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;

    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $_dataHelper;

    /**
     * OrderFee constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    protected $quoteFactory;
    protected $orderFactory;

    public function __construct(
        Context $context,
        GstHelper $dataHelper,
        QuoteFactory $quoteFactory,
        OrderFactory $orderFactory,
        array $data = []
    )
    {
        $this->_dataHelper = $dataHelper;
        $this->quoteFactory = $quoteFactory;
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $data);
    }

    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getCreditmemo();
        $this->getSource();

        if (!$this->getSource()->getSgstCharge() || $this->getSource()->getSgstCharge() <= 0) {
            return $this;
        }

        $order = $this->orderFactory->create()->load($this->getCreditmemo()->getOrderId());
        $taxType = $this->quoteFactory->create()->load($order->getQuoteId())->getExclPrice();

        $taxTypeLabel = ($taxType == 1) ? 'Excl. of SGST' : 'Incl. of SGST';

        $fee = new DataObject(
            [
                'code' => 'sgst_charge',
                'strong' => false,
                'value' => $order->getSgstCharge(),
                'label' => $taxTypeLabel,
            ]
        );

        $this->getParentBlock()->addTotalBefore($fee, 'grand_total');

        return $this;
    }

    public function getCreditmemo()
    {
        return $this->getParentBlock()->getCreditmemo();
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }
}
