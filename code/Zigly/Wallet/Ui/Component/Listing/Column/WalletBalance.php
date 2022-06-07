<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Customer\Model\CustomerFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

class WalletBalance extends Column
{

    protected $escaper;

    protected $systemStore;

    protected $productloader;

    /**
     * @param array $data
     * @param Escaper $escaper
     * @param array $components
     * @param Customer $customer
     * @param ContextInterface $context
     * @param CollectionFactory $collectionFactory
     * @param UiComponentFactory $uiComponentFactory
     */
    public function __construct(
        Escaper $escaper,
        CustomerFactory $customer,
        ContextInterface $context,
        CollectionFactory $collectionFactory,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->escaper = $escaper;
        $this->customer = $customer;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $customerId = $item['entity_id'];
                $customer = $this->customer->create()->load($customerId);
                $item[$this->getData('name')] = (!empty($customer->getWalletBalance())) ? floor($customer->getWalletBalance()): 0 ;
            }
        }
        return $dataSource;
    }

}