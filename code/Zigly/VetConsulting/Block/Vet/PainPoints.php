<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Block\Vet;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Unserialize\Unserialize;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class PainPoints extends \Magento\Framework\View\Element\Template
{

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * Constructor
     * @param Json $json
     * @param Context $context
     * @param Unserialize $unserialize
     * @param SessionFactory $customer
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Json $json,
        Context $context,
        Unserialize $unserialize,
        SessionFactory $customer,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->json = $json;
        $this->customer = $customer;
        $this->unserialize = $unserialize;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /*
    * Get pain points
    */
    public function getPainPoints()
    {
        $value = $this->scopeConfig->getValue('vetconsulting/vetconsulting_pain_points_config/pain_points', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (empty($value)) {
            return false;
        }
        if ($this->isSerialized($value)) {
            $unserializer = $this->unserialize;
        } else {
            $unserializer = $this->json;
        }
        $data = $unserializer->unserialize($value);
        $reason = [];
        foreach ($data as $key => $reas) {
            $reason[] = $reas['painpoints'];
        }
        return $reason;
    }

    /**
     * Check if value is a serialized string
     *
     * @param string $value
     * @return boolean
     */
    private function isSerialized($value)
    {
        return (boolean) preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }
}