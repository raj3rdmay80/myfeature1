<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Block\Adminhtml\Sales;

use Magento\Framework\Serialize\SerializerInterface;

class WalletTotals extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\DataObject $DataObject,
        SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_currency = $currency;
        $this->DataObject = $DataObject;
        $this->serializer = $serializer;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     *
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getOrder();
        $this->getSource();

        if (empty($this->getOrder()->getZwallet())) {
            return $this;
        }
        $zwallet = $this->serializer->unserialize($this->getOrder()->getZwallet());
        if ($zwallet['applied'] == false) {
            return $this;
        }
        $total = new \Magento\Framework\DataObject(
            [
                'code' => 'zwallet',
                'value' => $zwallet['spend_amount'],
                'label' => 'Wallet Used',
            ]
        );
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        return $this;
    }
}
