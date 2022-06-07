<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Block\Adminhtml;
use Magento\Backend\Block\Template;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Zigly\Managepets\Model\ResourceModel\Managepets\CollectionFactory;

class Editpet extends Template
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        CollectionFactory $collectionFactory,
        \Zigly\Managepets\Model\ManagepetsFactory $managepetsFactory,
        \Zigly\Managepets\Helper\Data $helperdata,
        \Magento\Store\Model\StoreManager $storeManager,
        \Zigly\Species\Model\BreedFactory $breedFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->managepetsFactory = $managepetsFactory;
        $this->helperdata = $helperdata;
        $this->storeManager = $storeManager;
        $this->_jsonEncoder = $jsonEncoder;
        $this->breedFactory = $breedFactory;
        parent::__construct($context, $data);
    }
    /**
     * @param array $dataToEncode
     * @return string
     */
    public function encodebreed($dataToEncode)
    {
        $encodedData = $this->_jsonEncoder->encode($dataToEncode);
        return $encodedData;
    }
    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        $customerid = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        if(!$customerid){
            $customerid = $this->getManualCustomerId();
            if(!$customerid){
                $customerid = $this->getRequest()->getParam('id');
            }
        }
        return $customerid;
    }
    public function getbreeddata($breedid){
        $bdata = $this->breedFactory->create()->load($breedid);
        return $bdata->getName();
    }
    public function getauthorizationnew(){
        if($this->_authorization->isAllowed('Zigly_Managepets::Managepets_new')){
            return true;
        }
        return false;
    }
    public function getpetdetails(){
        $id= $this->getPetId();
        $petdetails = [];
        $pet = $this->managepetsFactory->create();
        if($id){
           $pet = $this->managepetsFactory->create()->load($id);
           $petdetails = $pet->getData();
        }
        return $pet;
    }
    public function getPetimageurl($pathurl){
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        $imageurl = $mediaUrl."zigly/".$pathurl;
        return $imageurl;
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
}
