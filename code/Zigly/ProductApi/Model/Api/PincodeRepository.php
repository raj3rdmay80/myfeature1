<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProductApi
 */
declare(strict_types=1);

namespace Zigly\ProductApi\Model\Api;

use Ced\PincodeChecker\Model\ResourceModel\Pincode\CollectionFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Zigly\ProductApi\Api\PincodeRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class PincodeRepository implements PincodeRepositoryInterface
{

    /**
     * @var  CollectionFactory
     */
    protected $pincodeCollectionFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Initialize service
     *
     * @param CollectionFactory $pincodeCollectionFactory
     * @param Request $request
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CollectionFactory $pincodeCollectionFactory,
        Request $request,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->pincodeCollectionFactory = $pincodeCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    /**
     * get Status
     * @return \Zigly\ProductApi\Api\Data\PincodeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
     public function pincodeApi()
     {
        try{
            $data=$this->request->getBodyParams();
            $zip_not_found = $this->scopeConfig->getValue('pincode/general/zip_not_found', ScopeInterface::SCOPE_STORE);
            $pincode_collection=$this->pincodeCollectionFactory->create()->addFieldToFilter('vendor_id', 'admin')
            ->addFieldToFilter('zipcode', array('eq' => $data['zipcode']))->getData();
            foreach($pincode_collection as $model){
                $days_to_deliver = $model['days_to_deliver'];
            }
            if(count($pincode_collection)){
                $msg = $this->getDeliveryDaysMessage((int)$days_to_deliver, $data['zipcode']);
                $zip_data = ['status'=> "true",'message' => $msg];
            }else{
                 $zip_data = ['status'=> "false", 'message' => $zip_not_found];
            }
             $response = new \Magento\Framework\DataObject();
             $response->setStatus($zip_data['status']);
             $response->setMessage($zip_data['message']);
             return $response;
         } catch (\Exception $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }

    /**
     * @param $days
     * @param $pincode
     * @return mixed
     */
    protected function getDeliveryDaysMessage($days, $pincode)
    {
        $codes = array('{{from-days}}', '{{to-days}}', '{{pincode}}');
        $msg = $this->scopeConfig->getValue('pincode/general/delivery_text', ScopeInterface::SCOPE_STORE);
        $margin_days = (int)$this->scopeConfig->getValue('pincode/general/delivery_days_margin', ScopeInterface::SCOPE_STORE);
        $replace_string = array($days, $days + $margin_days, $pincode);
        return str_replace($codes, $replace_string, $msg);
    }
   
}