<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Controller\Index;

use Zigly\Species\Model\BreedFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class Breed extends Action
{

    public function __construct(
        Context $context,
        BreedFactory $breedFactory,
        JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory
    ) {
        $this->breedFactory = $breedFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();
        $type = $data['type'];
        $search = $data['searched'];
        $collection = $this->breedFactory->create()->getCollection()->addFieldToFilter('species_id', ['in' => $type])->addFieldToFilter('status', ['eq' => 1])->addFieldToFilter('name', ['like' => '%'.$search.'%']);
        $collection->addFieldToSelect('breed_id');
        $collection->addFieldToSelect('name');
        if (empty($collection->getData())) {
            $collection = $this->breedFactory->create()->getCollection()->addFieldToFilter('species_id', ['in' => $type])->addFieldToFilter('status', ['eq' => 1])->addFieldToFilter('name', ['eq' => 'Other']);
            $collection->addFieldToSelect('breed_id');
            $collection->addFieldToSelect('name');
        }
        $res = [];
        foreach ($collection->getData() as $value) {
            $res[] = ['value' => $value['breed_id'], 'label' => $value['name']];
        }
        $result->setData($res);
        return $result;
    }
}
