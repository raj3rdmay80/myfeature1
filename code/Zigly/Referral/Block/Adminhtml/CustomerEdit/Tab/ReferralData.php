<?php
namespace Zigly\Referral\Block\Adminhtml\CustomerEdit\Tab;


class ReferralData extends \Magento\Backend\Block\Template implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'referral/referral_detail.phtml';

    protected $_customerRepositoryInterface;

    protected $referralCustomer;


    /**
     * View constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Zigly\Referral\Model\ReferralFactory $referralFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->referralCustomer = $referralFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(\Magento\Customer\Controller\RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    public function getReferralData() {
        if($this->getCustomerId()) {
            $customer = $this->_customerRepositoryInterface->getById($this->getCustomerId());
            return $customer;
        } else {
            return false;
        }
    }

    public function getReferralEarned(){
        if(!$this->getCustomerId()) {
            return 0;
        } else {
            $_referral_collection = $this->referralCustomer->create()->getCollection();
            $_referral_collection->addFieldToFilter('referred_customer_id', $this->getCustomerId());
		    $_referral_collection->addFieldToFilter('order_id', array('notnull' => true));
            if($_referral_collection->Count() > 0){
                $_referral_collection->getSelect()->columns(['referred_amount' => new \Zend_Db_Expr('SUM(referred_amount)')])->group('referred_customer_id');
                $referral_data = $_referral_collection->getData();
                return $referral_data[0]['referred_amount'];
            } else{
                return 0;
            }
        }
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Referral Program');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Referral Program');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }
}