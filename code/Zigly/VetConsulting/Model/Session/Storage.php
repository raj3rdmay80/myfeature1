<?php
/**
 * Copyright (C) 2021 Zigly
 * @package  Zigly_VetConsulting
 */

namespace Zigly\VetConsulting\Model\Session;

class Storage extends \Magento\Framework\Session\Storage
{
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $namespace = 'vetconsulting',
        array $data = []
    ) {
        parent::__construct($namespace, $data);
    }
}