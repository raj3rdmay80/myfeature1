<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Block\Vet;

use Zigly\Species\Model\BreedFactory;
use Zigly\Managepets\Model\ManagepetsFactory;
use Magento\Framework\View\Element\Template\Context;
use Zigly\VetConsulting\Model\Session as VetSession;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Customer\Model\SessionFactory as CustomerSession;
use Zigly\Groomer\Model\GroomerFactory as ProfessionalFactory;

/**
 * Show plans for selection
 */
class Review extends \Magento\Framework\View\Element\Template
{

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var VetSession
     */
    protected $vetSession;

    /**
     * @var vet session's value
     */
    protected $vetSessionData;

    /**
     * @var ManagepetsFactory
     */
    protected $pets;

    /**
     * @var BreedFactory
     */
    protected $breed;

    /**
     * Constructor
     * @param Context $context
     * @param BreedFactory $breedFactory
     * @param vetSession $groomingSession
     * @param ManagepetsFactory $petsFactory
     * @param CustomerSession $customerSession
     * @param PriceCurrencyInterface $priceCurrency
     * @param ProfessionalFactory $professionalFactory
     * @param array $data
     */
    public function __construct(
        VetSession $vetSession,
        BreedFactory $breedFactory,
        ManagepetsFactory $petsFactory,
        CustomerSession $customerSession,
        PriceCurrencyInterface $priceCurrency,
        ProfessionalFactory $professionalFactory,
        Context $context,
        array $data = []
    ) {
        $this->pets = $petsFactory;
        $this->breed = $breedFactory;
        $this->vetSession = $vetSession;
        $this->priceCurrency = $priceCurrency;
        $this->customerSession = $customerSession;
        $this->professionalFactory = $professionalFactory;
        parent::__construct($context, $data);
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

    /**
     * @return Grooming Session selected data
     */
    public function getVetSession()
    {
        if (!$this->vetSessionData) {
            $this->vetSessionData = $this->vetSession->getVet();
        }
        return $this->vetSessionData;
    }

    /*
    * get professional by id
    */
    public function getProfessionalById($id)
    {
        $professional = $this->professionalFactory->create()->load($id);
        return $professional;
    }

    public function getDate()
    {
        if (!empty($this->getVetSession()['selected_date']) && $date = $this->getVetSession()['selected_date']) {
            return \DateTime::createFromFormat('Y-m-d', $date)->format('d M \'y');
        }
        return '';
    }

    public function  getWalletBalance()
    {
        $customer = $this->customerSession->create()->getCustomer();
        $totalBalance = is_null($customer->getWalletBalance()) ? "0" : $customer->getWalletBalance();
        return $totalBalance;
    }

    /**
     * @return Update Vet Session selected data
     */
    public function updateVetSession($consultSession)
    {
        $this->vetSession->setVet($consultSession);
        return;
    }

    /**
     * Get Pet Details
     * @return ManagepetsFactory
     */
    public function getPet()
    {
        $petdetails = "";
        if (!empty($this->getVetSession()['pet_id']) && $petId = $this->getVetSession()['pet_id']) {
            $petdetails = $this->pets->create()->load($petId)->getData();
        }
        return $petdetails;
    }

    /**
     * Get Breed Type Name
     * @return string
     */
    public function getBreed()
    {
        $breedTypeName = "";
        $petdetails = $this->getPet();
        if ($petdetails) {
            $breedType = $this->breed->create()->load($petdetails['breed'])->getData('breed_type');
            switch ($breedType) {
                case '1':
                    $breedTypeName = "small";
                    break;
                case '2':
                    $breedTypeName = "medium";
                    break;
                case '3':
                    $breedTypeName = "large";
                    break;
            }
        }
        return $breedTypeName;
    }
}