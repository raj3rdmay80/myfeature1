<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Block;

use Magento\Framework\App\Request\Http;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\SessionFactory;
use Zigly\Managepets\Model\ManagepetsFactory;
use Magento\Store\Model\StoreManager;
use Zigly\Managepets\Helper\Data;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Zigly\Species\Model\BreedFactory;
use Magento\Framework\Json\EncoderInterface;

class Managepetform extends \Magento\Framework\View\Element\Template
{

    /**
     * @param Http $request
     * @param Context $context
     * @param Data $helperdata
     * @param JsonHelper $jsonHelper
     * @param SessionFactory $customer
     * @param BreedFactory $breedFactory
     * @param StoreManager $storeManager
     * @param TimezoneInterface $timezone
     * @param EncoderInterface $jsonEncoder
     * @param ManagepetsFactory $managepetsFactory
     * @param array $data
     */
    public function __construct(
        Http $request,
        Data $helperdata,
        Context $context,
        JsonHelper $jsonHelper,
        SessionFactory $customer,
        BreedFactory $breedFactory,
        StoreManager $storeManager,
        TimezoneInterface $timezone,
        EncoderInterface $jsonEncoder,
        ManagepetsFactory $managepetsFactory,
        array $data = []
    ) {
        $this->request = $request;
        $this->customer = $customer;
        $this->managepetsFactory = $managepetsFactory;
        $this->storeManager = $storeManager;
        $this->helperdata = $helperdata;
        $this->timezone = $timezone;
        $this->jsonHelper = $jsonHelper;
        $this->jsonEncoder = $jsonEncoder;
        $this->breedFactory = $breedFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param array $dataToEncode
     * @return string
     */
    public function encodebreed($dataToEncode)
    {
        $encodedData = $this->jsonEncoder->encode($dataToEncode);
        return $encodedData;
    }

    public function getpetdetails(){
        $id= $this->getRequest()->getParam('id');
        $petdetails = [];
        $pet = $this->managepetsFactory->create();
        if($id){
           $pet = $this->managepetsFactory->create()->load($id);
           $petdetails = $pet->getData();
        }
        return $pet;
    }

    public function getSpeciesImage($url)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        $imageurl = $mediaUrl."species/feature/".$url;
        return $imageurl;
    }

    public function getPetimageurl($pathurl){
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        $imageurl = $mediaUrl."zigly/".$pathurl;
        return $imageurl;
    }

    public function getbreeddata($breedid){
        $bdata = $this->breedFactory->create()->load($breedid);
        return $bdata->getName();
    }

    public function getSpecies($savedspecie = null){
        return $this->helperdata->getSpecies($savedspecie);
    }

    public function getBreeds($savedspecie = null,$savedbreed =null){
        return $this->helperdata->getBreeds($savedspecie,$savedbreed);
    }

    public function getGenders(){
        return $this->helperdata->getGenders();
    }

    public function getDobvalue($datevalue = null){
        $dobvalue = '';
        if($datevalue){
            $dobvalue = str_replace(" 00:00:00", "", $datevalue);
        }
        return $dobvalue;
    }

    public function getGroomerParam(){
        return $this->request->getParam('destination');
    }

    public function getServiceParam(){
        return $this->request->getParam('service');
    }
}