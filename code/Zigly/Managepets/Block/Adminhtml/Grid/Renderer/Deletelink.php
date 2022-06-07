<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Block\Adminhtml\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class Deletelink extends AbstractRenderer
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
      $url = '<a href="javascript:;" class="delete_pet" pet_id="'.$this->_getValue($row).'">Delete</a>';
       return $url;
   }
}
