<?php

namespace Zigly\ChangeLabel\Observer\Admin;

use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Amasty\ShopbyBase\Helper\FilterSetting;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Event\ObserverInterface;
use Amasty\ShopbyBrand\Model\ConfigProvider;

class OptionFormFeatured extends \Amasty\ShopbyBrand\Observer\Admin\OptionFormFeatured
{
    /**
     * @var Yesno
     */
    private $yesNoSource;

    /**
     * @var \Amasty\ShopbyBrand\Helper\Data
     */
    private $helper;

    /**
     * OptionFormFeatured constructor.
     *
     * @param Yesno $yesNosource
     * @param \Amasty\ShopbyBrand\Helper\Data $brandHelper
     */
    public function __construct(
        Yesno $yesNosource,
        ConfigProvider $configProvider,
        \Amasty\ShopbyBrand\Helper\Data $brandHelper
    ) {
        parent::__construct($yesNosource, $configProvider);
        $this->configProvider = $configProvider;
        $this->yesNoSource = $yesNosource;
        $this->helper = $brandHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Form $fieldSet */
        $fieldSet = $observer->getEvent()->getFieldset();
        $setting = $observer->getEvent()->getSetting();
        $storeId = $observer->getEvent()->getStoreId();
        $brandFilterCode = FilterSetting::ATTR_PREFIX . $this->configProvider->getBrandAttributeCode((int) $storeId);

        if ($setting->getFilterCode() == $brandFilterCode) {
            $fieldSet->setData('legend', 'Options');

            $fieldSet->addField(
                'slider_position',
                'text',
                [
                    'name' => 'slider_position',
                    'label' => __('Position'),
                    'title' => __('Position in Slider'),
                    'note' => __(
                        'Please make sure Sort By "Position" is selected in the following setting group:
                                  STORES -> Configuration -> Improved Layered Navigation: Brands -> Brand Slider'
                    )
                ]
            );

            $fieldSet->addField(
                OptionSettingInterface::IS_SHOW_IN_SLIDER,
                'select',
                [
                    'name' => OptionSettingInterface::IS_SHOW_IN_SLIDER,
                    'label' => __('Show in Brand Slider'),
                    'title' => __('Show in Brand Slider'),
                    'values'    => $this->yesNoSource->toOptionArray(),
                ]
            );

            $fieldSet->addField(
                'home_featured',
                'select',
                [
                    'name' => 'home_featured',
                    'label' => __('Home Featured'),
                    'title' => __('Home Featured'),
                    'values'    => $this->yesNoSource->toOptionArray(),
                ]
            );

            $fieldSet->addField(
                'home_sort_order',
                'text',
                [
                    'name' => 'home_sort_order',
                    'class' => 'validate-number',
                    'label' => __('Home Sort Order'),
                    'title' => __('Home Sort Order')
                ]
            );

        }
    }
}
