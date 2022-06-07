<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */

namespace Zigly\GroomingService\Controller\Grooming;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterface;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Model\Address\Mapper;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Customer\Model\Session;
use Magento\Directory\Helper\Data as HelperData;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

/**
 * Customer Address Form Post Controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Addresspost extends \Magento\Customer\Controller\Address implements HttpPostActionInterface
{
    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var Mapper
     */
    private $customerAddressMapper;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param FormKeyValidator $formKeyValidator
     * @param FormFactory $formFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param AddressInterfaceFactory $addressDataFactory
     * @param RegionInterfaceFactory $regionDataFactory
     * @param DataObjectProcessor $dataProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param ForwardFactory $resultForwardFactory
     * @param JsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     * @param RegionFactory $regionFactory
     * @param CookieManagerInterface $cookieManager
     * @param HelperData $helperData
     * @param Filesystem $filesystem
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        FormKeyValidator $formKeyValidator,
        FormFactory $formFactory,
        AddressRepositoryInterface $addressRepository,
        AddressInterfaceFactory $addressDataFactory,
        RegionInterfaceFactory $regionDataFactory,
        JsonFactory $resultJsonFactory,
        DataObjectProcessor $dataProcessor,
        DataObjectHelper $dataObjectHelper,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        RegionFactory $regionFactory,
        CookieManagerInterface $cookieManager,
        HelperData $helperData,
        Filesystem $filesystem = null
    ) {
        $this->regionFactory = $regionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cookieManager = $cookieManager;
        $this->helperData = $helperData;
        $this->filesystem = $filesystem ?: ObjectManager::getInstance()->get(Filesystem::class);
        parent::__construct(
            $context,
            $customerSession,
            $formKeyValidator,
            $formFactory,
            $addressRepository,
            $addressDataFactory,
            $regionDataFactory,
            $dataProcessor,
            $dataObjectHelper,
            $resultForwardFactory,
            $resultPageFactory
        );
    }

    /**
     * Extract address from request
     *
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    protected function _extractAddress()
    {
        $existingAddressData = $this->getExistingAddressData();

        /** @var \Magento\Customer\Model\Metadata\Form $addressForm */
        $addressForm = $this->_formFactory->create(
            'customer_address',
            'customer_address_edit',
            $existingAddressData
        );
        $addressData = $addressForm->extractData($this->getRequest());
        $attributeValues = $addressForm->compactData($addressData);

        $this->updateRegionData($attributeValues);

        $addressDataObject = $this->addressDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $addressDataObject,
            array_merge($existingAddressData, $attributeValues),
            \Magento\Customer\Api\Data\AddressInterface::class
        );
        $addressDataObject->setCustomerId($this->_getSession()->getCustomerId())
            ->setIsDefaultBilling(
                $this->getRequest()->getParam(
                    'default_billing',
                    isset($existingAddressData['default_billing']) ? $existingAddressData['default_billing'] : false
                )
            )
            ->setIsDefaultShipping(
                $this->getRequest()->getParam(
                    'default_shipping',
                    isset($existingAddressData['default_shipping']) ? $existingAddressData['default_shipping'] : false
                )
            );

        return $addressDataObject;
    }

    /**
     * Retrieve existing address data
     *
     * @return array
     * @throws \Exception
     */
    protected function getExistingAddressData()
    {
        $existingAddressData = [];
        if ($addressId = $this->getRequest()->getParam('id')) {
            $existingAddress = $this->_addressRepository->getById($addressId);
            if ($existingAddress->getCustomerId() !== $this->_getSession()->getCustomerId()) {
                throw new NotFoundException(__('Address not found.'));
            }
            $existingAddressData = $this->getCustomerAddressMapper()->toFlatArray($existingAddress);
        }
        return $existingAddressData;
    }

    /**
     * Update region data
     *
     * @param array $attributeValues
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function updateRegionData(&$attributeValues)
    {
        if (!empty($attributeValues['region_id'])) {
            $newRegion = $this->regionFactory->create()->load($attributeValues['region_id']);
            $attributeValues['region_code'] = $newRegion->getCode();
            $attributeValues['region'] = $newRegion->getDefaultName();
        }

        $regionData = [
            RegionInterface::REGION_ID => !empty($attributeValues['region_id']) ? $attributeValues['region_id'] : null,
            RegionInterface::REGION => !empty($attributeValues['region']) ? $attributeValues['region'] : null,
            RegionInterface::REGION_CODE => !empty($attributeValues['region_code'])
                ? $attributeValues['region_code']
                : null,
        ];

        $region = $this->regionDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $region,
            $regionData,
            \Magento\Customer\Api\Data\RegionInterface::class
        );
        $attributeValues['region'] = $region;
    }

    /**
     * Process address form save
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $response = [
            'errors' => true,
            'message' => ''
        ];
        $resultJson = $this->resultJsonFactory->create();

        $redirectUrl = null;
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $response['message'] = "Invalid Form key";
            return $resultJson->setData($response);
        }
        $city = $this->cookieManager->getCookie('city_screen');
        $formCity = $this->getRequest()->getPostValue('city');
        if ((strcasecmp($city, $formCity) !== 0)) {
            $response['message'] = "Please fill a address with your selected city location.";
            return $resultJson->setData($response);
        }
        try {
            $address = $this->_extractAddress();
            if ($this->_request->getParam('delete_attribute_value')) {
                $address = $this->deleteAddressFileAttribute($address);
            }
            $this->_addressRepository->save($address);
            $response = [
                'errors' => false,
                'message' => __('You saved the address.')
            ];
            return $resultJson->setData($response);
        } catch (InputException $e) {
            $response['message'] = $e->getMessage();
            foreach ($e->getErrors() as $error) {
                $response['message_errors'][] = $error->getMessage();
            }
        } catch (\Exception $e) {
            $response['message'] =  __('We can\'t save the address.');
        }
        return $resultJson->setData($response);

    }

    /**
     * Get Customer Address Mapper instance
     *
     * @return Mapper
     *
     * @deprecated 100.1.3
     */
    private function getCustomerAddressMapper()
    {
        if ($this->customerAddressMapper === null) {
            $this->customerAddressMapper = ObjectManager::getInstance()->get(
                \Magento\Customer\Model\Address\Mapper::class
            );
        }
        return $this->customerAddressMapper;
    }

    /**
     * Removes file attribute from customer address and file from filesystem
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return mixed
     */
    private function deleteAddressFileAttribute($address)
    {
        $attributeValue = $address->getCustomAttribute($this->_request->getParam('delete_attribute_value'));
        if ($attributeValue!== null) {
            if ($attributeValue->getValue() !== '') {
                $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                $fileName = $attributeValue->getValue();
                $path = $mediaDirectory->getAbsolutePath('customer_address' . $fileName);
                if ($fileName && $mediaDirectory->isFile($path)) {
                    $mediaDirectory->delete($path);
                }
                $address->setCustomAttribute(
                    $this->_request->getParam('delete_attribute_value'),
                    ''
                );
            }
        }

        return $address;
    }
}
