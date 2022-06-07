<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Observer;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Event\ObserverInterface;

class CartSaveBeforeClearWallet implements ObserverInterface
{

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @param SerializerInterface $serializer
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        SerializerInterface $serializer,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->serializer = $serializer;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/CLearcart.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('----------SS-------');
        $quote = $observer->getEvent()->getCart()->getQuote();
        $logger->info(print_r($quote->getZwallet(), true));
        if (!empty($quote->getZwallet())) {
            $zwallet = $this->serializer->unserialize($quote->getZwallet());
            $zwallet['applied'] = false;
            $quote->setZwallet($this->serializer->serialize($zwallet));
            $quote->setDataChanges(true);
            $quote->collectTotals();
            $this->quoteRepository->save($quote);
        }
        $logger->info(print_r($quote->getZwallet(), true));
        $logger->info('------EE-----------');
    }
}