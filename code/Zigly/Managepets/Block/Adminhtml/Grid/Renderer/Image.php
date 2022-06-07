<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Block\Adminhtml\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class Image extends AbstractRenderer
{
   private $_storeManager;
   /**
    * @param \Magento\Backend\Block\Context $context
    * @param array $data
    */
   public function __construct(\Magento\Backend\Block\Context $context, StoreManagerInterface $storemanager, array $data = [])
   {
       $this->_storeManager = $storemanager;
       parent::__construct($context, $data);
       $this->_authorization = $context->getAuthorization();
   }
   /**
    * Renders grid column
    *
    * @param DataObject $row
    * @return  string
    */
   public function render(DataObject $row)
   {
       $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
       $imageurl = $mediaUrl.'catalog/product/placeholder/'.$this->getConfig('catalog/placeholder/thumbnail_placeholder');
       if($this->_getValue($row)){
          $imageurls = $this->_getValue($row);
          $images = explode(",",$imageurls);
          if(!empty($images)){
            foreach($images as $ikey => $image){
                if($image){
                    $imageurl = $mediaUrl."zigly/".$image;
                    break;
                }
            }
          }
       }
       return '<img src="'.$imageurl.'" width="50" height="50"/>';
   }
}
