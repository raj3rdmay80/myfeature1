<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProfessionalGraphQl
 */
declare(strict_types=1);

namespace Zigly\ProfessionalGraphQl\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Zigly\ProfessionalGraphQl\Api\HubRepositoryInterface;
use Zigly\Hub\Model\ResourceModel\Hub\CollectionFactory;
use Zigly\ProfessionalGraphQl\Helper\Encryption;

class HubRepository implements HubRepositoryInterface
{

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /** @var Encryption */
    protected $encryption;

    /**
     * @param Encryption $encryption
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Encryption $encryption,
        CollectionFactory $collectionFactory
    ) {
        $this->encryption = $encryption;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getHubList($token)
    {
        $professional = $this->encryption->tokenAuthentication($token);
        if (!$professional) {
            throw new NoSuchEntityException(__('Invalid token'));
        }
        $res = [];
        $collection = $this->collectionFactory->create();
        foreach ($collection as $index => $value) {
            $res[] = ['value' => $value['hub_id'], 'label' => $value['location'].', '.$value['city']];
        }
        return $res;
    }
}