<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Block\Sales;

use Magento\Framework\Serialize\SerializerInterface;

class WalletTotals extends \Magento\Framework\View\Element\Template
{

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Magento\Framework\DataObject $DataObject
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\DataObject $DataObject,
        SerializerInterface $serializer,
        array $data = []
    ) {
        $this->serializer = $serializer;
        $this->_currency = $currency;
        $this->DataObject = $DataObject;
        parent::__construct($context, $data);
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
        return $this->getParentBlock()->getOrder();
    }
    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
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