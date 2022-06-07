<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
declare(strict_types=1);

namespace Zigly\Sales\Block\Booking;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Directory\Model\RegionFactory;
use Zigly\Groomer\Model\GroomerFactory as ProfessionalFactory;

class InvoicePdf extends \Magento\Framework\View\Element\Template
{

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Constructor
     * @param Context $context
     * @param TimezoneInterface $timezone
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param ProfessionalFactory $professionalFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        TimezoneInterface $timezone,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        CollectionFactory $collectionFactory,
        Registry $registry,
        ProfessionalFactory $professionalFactory,
        RegionFactory $regionFactory,
        array $data = []
    ) {
        $this->timezone = $timezone;
        $this->storeManager = $storeManager;
        $this->priceCurrency =  $priceCurrency;
        $this->collectionFactory = $collectionFactory;
        $this->coreRegistry = $registry;
        $this->regionFactory = $regionFactory;
        $this->professionalFactory = $professionalFactory;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    public function getCacheLifetime()
    {
        return false;
    }

    /**
     * Convert and format price value for current application store
     *
     * @param   float $value
     * @param   bool $format
     * @param   bool $includeContainer
     * @return  float|string
     */
    public function currency($value, $format = true, $includeContainer = true)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat($value, $includeContainer)
            : $this->priceCurrency->convert($value);
    }



    public function getConfig($config_path)
    {
        return $this->storeManager->getStore()->getConfig($config_path);
    }

    public function getCompanyGstin()
    {
        return $this->getConfig('invoicepdf/zigly/gstin');
    }

    public function getCompanyUpi()
    {
        return $this->getConfig('invoicepdf/zigly/upi');
    }

    public function getCompanyPan()
    {
        return $this->getConfig('invoicepdf/zigly/pan');
    }

    public function getCompanyCin()
    {
        return $this->getConfig('invoicepdf/zigly/cin');
    }

    public function getCompanyBankno()
    {
        return $this->getConfig('invoicepdf/zigly/bankno');
    }

    public function getCompanyAddress()
    {
        return $this->getConfig('invoicepdf/zigly/address');
    }

    public function getCompanyState()
    {
        return $this->getConfig('invoicepdf/zigly/state');
    }

    public function getCompanyFooter()
    {
        return $this->getConfig('invoicepdf/zigly/footer');
    }

    public function getHsnService()
    {
        return $this->getConfig('invoicepdf/zigly/hsnservice');
    }

    public function getHsnCommission()
    {
        return $this->getConfig('invoicepdf/zigly/hsncommission');
    }




    /*
    * set date format
    */
    public function getDate($date)
    {
        return $this->timezone->date(new \DateTime($date))->format('d M, Y');
    }

    /*
    * AmountInWords
    */
    public function amountInWords(float $amount)
    {
       $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
       // Check if there is any number after decimal
       $amt_hundred = null;
       $count_length = strlen((string)$num);
       $x = 0;
       $string = array();
       $change_words = array(0 => '', 1 => 'One', 2 => 'Two',
         3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
         7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
         10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
         13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
         16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
         19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
         40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
         70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
        $here_digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
        while( $x < $count_length ) {
          $get_divider = ($x == 2) ? 10 : 100;
          $amount = floor($num % $get_divider);
          $num = floor($num / $get_divider);
          $x += $get_divider == 10 ? 1 : 2;
          if ($amount) {
           $add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
           $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
           $string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' 
           '.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. ' 
           '.$here_digits[$counter].$add_plural.' '.$amt_hundred;
            }
       else $string[] = null;
       }
       $implode_to_Rupees = implode('', array_reverse($string));
       $get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . " 
       " . $change_words[$amount_after_decimal % 10]) . ' Paise' : '';
       return ($implode_to_Rupees ? $implode_to_Rupees . 'Rupees ' : '') . $get_paise;
    }

    /*
    * Region
    */
    public function getRegionData($regionId)
    {
        $region = $this->regionFactory->create()->load($regionId);
        return $region;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getBooking()
    {
        return $this->coreRegistry->registry('current_booking');
    }

    /**
     * Retrieve current booking model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /*
    * get professional by id
    */
    public function getProfessional($id)
    {
        $professional = $this->professionalFactory->create()->load($id);
        return $professional;
    }

    /**
     * @return address string
     */
    public function getAddressDetails($addressObj)
    {
        $address = false;
        if ($addressObj) {
            // $shippingAddress = $this->collectionFactory->create()->addFieldToFilter('entity_id',array($addressId))->getFirstItem();
            if (!empty($addressObj)) {
                $street = $addressObj->getStreet();
                $address = $street['0'].", ";
                if (isset($street['1'])) {
                    $address .= $street['1'].", ";
                }
                $address .= $addressObj->getCity().", ".$addressObj->getRegion()." - ". $addressObj->getPostcode();
            }
        }
        return $address;
    }
}
