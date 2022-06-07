<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_PincodeChecker
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\PincodeChecker\Model;

/**
 * Class Shipping
 * @package Ced\PincodeChecker\Model
 */
class Shipping extends \Magento\Shipping\Model\Shipping
{
    /**
     * Part of carrier xml config path
     *
     * @var string
     */
    protected $_availabilityConfigField = 'active';

    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * @var \Ced\PincodeChecker\Helper\Data
     */
    protected $_helper;

    /**
     * @var PincodeFactory
     */
    protected $pincodeFactory;

    /**
     * Shipping constructor.
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param \Ced\PincodeChecker\Helper\Data $helper
     * @param PincodeFactory $pincodeFactory
     */
    public function __construct(
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        \Ced\PincodeChecker\Helper\Data $helper,
        \Ced\PincodeChecker\Model\PincodeFactory $pincodeFactory
    )
    {
        $this->_carrierFactory = $carrierFactory;
        $this->_helper = $helper;
        $this->pincodeFactory = $pincodeFactory;
    }

    /**
     * Collect rates of given carrier
     *
     * @param string $carrierCode
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectCarrierRates($carrierCode, $request)
    {
        if ($this->_helper->isPinCodeCheckerEnabled()) {
            if ($request->getDestPostcode()) {
                $pincode_model = $this->pincodeFactory->create()
                    ->load($request->getDestPostcode(), 'zipcode')
                    ->getData();
                if (!empty($pincode_model)) {
                    if (!$pincode_model['can_ship']) {
                        return $this;
                    }
                }
            }
        }
        /* @var $carrier \Magento\Shipping\Model\Carrier\AbstractCarrier */
        $carrier = $this->_carrierFactory->createIfActive($carrierCode, $request->getStoreId());
        if (!$carrier) {
            return $this;
        }

        $carrier->setActiveFlag($this->_availabilityConfigField);
        $result = $carrier->checkAvailableShipCountries($request);
        if (false !== $result && !$result instanceof \Magento\Quote\Model\Quote\Address\RateResult\Error) {
            $result = $carrier->proccessAdditionalValidation($request);
        }
        /*
         * Result will be false if the admin set not to show the shipping module
         * if the delivery country is not within specific countries
         */
        if (false !== $result) {
            if (!$result instanceof \Magento\Quote\Model\Quote\Address\RateResult\Error) {
                if ($carrier->getConfigData('shipment_requesttype')) {
                    $packages = $this->composePackagesForCarrier($carrier, $request);
                    if (!empty($packages)) {
                        $sumResults = [];
                        foreach ($packages as $weight => $packageCount) {
                            $request->setPackageWeight($weight);
                            $result = $carrier->collectRates($request);
                            if (!$result) {
                                return $this;
                            } else {
                                $result->updateRatePrice($packageCount);
                            }
                            $sumResults[] = $result;
                        }
                        if (!empty($sumResults) && count($sumResults) > 1) {
                            $result = [];
                            foreach ($sumResults as $res) {
                                if (empty($result)) {
                                    $result = $res;
                                    continue;
                                }
                                foreach ($res->getAllRates() as $method) {
                                    foreach ($result->getAllRates() as $resultMethod) {
                                        if ($method->getMethod() == $resultMethod->getMethod()) {
                                            $resultMethod->setPrice($method->getPrice() + $resultMethod->getPrice());
                                            continue;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $result = $carrier->collectRates($request);
                    }
                } else {
                    $result = $carrier->collectRates($request);
                }
                if (!$result) {
                    return $this;
                }
            }
            if ($carrier->getConfigData('showmethod') == 0 && $result->getError()) {
                return $this;
            }
            // sort rates by price
            if (method_exists($result, 'sortRatesByPrice') && is_callable([$result, 'sortRatesByPrice'])) {
                $result->sortRatesByPrice();
            }
            $this->getResult()->append($result);
        }
        return $this;
    }

}
