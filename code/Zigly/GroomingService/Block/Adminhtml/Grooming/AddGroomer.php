<?php
namespace Zigly\GroomingService\Block\Adminhtml\Grooming;
 
use Magento\Backend\Block\Widget\Grid\Container;
 
class AddGroomer extends Container
{
    protected function _construct()
    {
    	// $this->_backButtonLabel = __('Back');
    	// $this->_addBackButton();
        parent::_construct();
        $this->removeButton('add');
    }
}
