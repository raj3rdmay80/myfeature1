<?php

namespace Zigly\Wallet\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;

class CashbackDateValidation extends Value
{
    /**
     * @var Random
     */
    protected $mathRandom;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param Random $mathRandom
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     * @param Json $serializer
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Random $mathRandom,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        Json $serializer = null
    ) {
        $this->mathRandom = $mathRandom;
        $this->serializer = $serializer ?: ObjectManager::getInstance()
            ->get(Json::class);
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Prepare data before save
     *
     * @return $this
     */
    public function beforeSave()
    {
        $dataObject = $this->getData();
        $fieldsetData = $dataObject['fieldset_data'];
        $path = $dataObject['path'];
        $path = $dataObject['path'];

        if (strpos($path, 'cashback_first') !== false) {
            $label = "First Cashback rules";
        } else {
            $label = "Second Cashback rules";
        }
        $startDate = \DateTime::createFromFormat('Y-m-d', $fieldsetData['start_date']);
        $endDate = \DateTime::createFromFormat('Y-m-d', $fieldsetData['end_date']);
        

        if ($startDate !== false && $endDate !== false) {
            if ($startDate >= $endDate) {
                throw new \Magento\Framework\Exception\ValidatorException(__($label . ': start date is greater than end date.'));
            }
        } else {
            throw new \Magento\Framework\Exception\ValidatorException(__($label . ': invalid start and end date'));
        }
        return $this;
    }

    /**
     * Process data after load
     *
     * @return $this
     * @throws LocalizedException
     */
    /*public function afterLoad()
    {
        if ($this->getValue()) {
            $value = $this->serializer->unserialize($this->getValue());
            if (is_array($value)) {
                $this->setValue($this->encodeArrayFieldValue($value));
            }
        }
        return $this;
    }*/

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     * @throws LocalizedException
     */
    // protected function encodeArrayFieldValue(array $value): array
    // {
    //     $result = [];
    //     foreach ($value as $country => $creditCardType) {
    //         $id = $this->mathRandom->getUniqueHash('_');
    //         $result[$id] = ['country_id' => $country, 'cc_types' => $creditCardType];
    //     }
    //     return $result;
    // }

    /**
     * Append unique countries to list of exists and reindex keys
     *
     * @param array $countriesList
     * @param array $inputCountriesList
     * @return array
     */
    // private function appendUniqueCountries(array $countriesList, array $inputCountriesList): array
    // {
    //     $result = array_merge($countriesList, $inputCountriesList);
    //     return array_values(array_unique($result));
    // }
}
