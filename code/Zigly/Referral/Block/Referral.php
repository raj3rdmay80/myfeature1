<?php
namespace Zigly\Referral\Block;

use \Magento\Framework\App\ObjectManager;

class Referral extends \Magento\Framework\View\Element\Template
{
    
    protected $_template = 'Zigly_Referral::referral.phtml';

    protected $request;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        array $data = []
    ) {
        $this->request = $request;
        parent::__construct($context, $data);
    }
 
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }

    public function getReferral()
    {
        return $this->request->getParam('reference');
    }
}
