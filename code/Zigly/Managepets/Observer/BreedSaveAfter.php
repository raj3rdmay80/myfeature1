<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Observer;

use Magento\Framework\Event\ObserverInterface;

class BreedSaveAfter implements ObserverInterface
{
   public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Zigly\Managepets\Model\ManagepetsFactory $managepetsFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\ResourceConnection $resource
    ){
        $this->_layout = $context->getLayout();
        $this->_request = $context->getRequest();
        $this->_objectManager = $objectManager;
        $this->managepetsFactory = $managepetsFactory;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;

    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            // Get the model object from observer
            $model = $observer->getEvent()->getDataObject();

            if ($model && !$model->isObjectNew()) {
                // perform some action if edit model
                $where = ['breed IN (?)' => array($model->getBreedId())];
                $connection = $this->connection;
                $connection->beginTransaction();
                $connection->update('zigly_managepets', ['enable_breed' => $model->getStatus()], $where);
                $connection->commit();
            }
        } catch (\Exception $e) {
            $this->connection->rollBack();
        }
    }
}
