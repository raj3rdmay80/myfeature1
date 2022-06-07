<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */

namespace Zigly\GroomingService\Model\Session;

class Storage extends \Magento\Framework\Session\Storage
{
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $namespace = 'groomingsession',
        array $data = []
    ) {
        parent::__construct($namespace, $data);
    }
}