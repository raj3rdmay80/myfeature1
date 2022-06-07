<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\Sales\Order\Invoice;

use Magento\Framework\View\Element\Template\Context;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use	Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\OrderFactory;

class IgstInvoiceTotal extends Template
{

    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Order invoice
     *
     * @var \Magento\Sales\Model\Order\Invoice|null
     */
    protected $_invoice = null;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;

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
    ) {
        $this->_dataHelper = $dataHelper;
        $this->quoteFactory = $quoteFactory;
		$this->orderFactory = $orderFactory;
		parent::__construct($context, $data);
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

    public function getInvoice()
    {
        return $this->getParentBlock()->getInvoice();
    }
    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getInvoice();
        $this->getSource();

        if(!$this->getSource()->getIgstCharge() || $this->getSource()->getIgstCharge() <=0) {
            return $this;
        }

		$order = $this->orderFactory->create()->load($this->getInvoice()->getOrderId());
		$taxType = $this->quoteFactory->create()->load($order->getQuoteId())->getExclPrice();

		$taxTypeLabel = ($taxType == 1) ? 'Excl. of IGST' : 'Incl. of IGST';

        $productIds=$this->getRequest()->getParam('invoice', []);



        if(count($productIds)>0)
        {
            $i=0;
            $igst=0;
            $invoiceQty=array();

            foreach ($productIds['items'] as $item=>$value)
            {

                $invoiceQty[]=$value;
            }
            foreach ($order->getAllItems() as $item)
            {
                $productData=$item->getData();

                if($invoiceQty[$i]!=0)
                {
                    $igst+= $productData['igst_charge'];
                }
                $i++;
            }
        }
        else
        {
            $igst=0;
            foreach ($order->getAllItems() as $item)
            {
                $productData=$item->getData();
                if($productData['qty_invoiced']==1)
                {
                    $igst+= $productData['igst_charge'];
                }
            }
            if($igst==0)
                $igst=$this->getSource()->getIgstCharge();
        }

        $total = new DataObject(
            [
                'code' => 'igst_charge',
                'value' => $order->getIgstCharge(),
                'label' => $taxTypeLabel,
            ]
        );

        $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        return $this;
    }
}
