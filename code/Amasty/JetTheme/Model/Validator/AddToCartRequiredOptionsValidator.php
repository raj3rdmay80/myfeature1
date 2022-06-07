<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Validator;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Validator for check, is there enough params for add to cart
 */
class AddToCartRequiredOptionsValidator
{
    const AM_RECURRING_PAYMENTS_DISABLED = 'no';

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        Json $jsonSerializer,
        Manager $moduleManager
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->moduleManager = $moduleManager;
    }
    /**
     * @param ProductInterface $product
     * @param array $params
     * @return bool
     */
    public function validate(ProductInterface $product, array $params): bool
    {
        $requiredOptions = $product->getTypeInstance()->hasRequiredOptions($product);
        $showOptionsResponse = false;
        switch ($product->getTypeId()) {
            case 'configurable':
                $attributesCount = $product->getTypeInstance()->getConfigurableAttributes($product)->count();
                $superParamsCount = (array_key_exists('super_attribute', $params)) ?
                    count(array_filter($params['super_attribute'])) : 0;
                if (isset($params['configurable-option'])) {
                    // compatibility with Amasty_Conf product matrix
                    $matrixSelected = false;
                    foreach ($params['amconfigurable-option'] as $amConfigurableOption) {
                        $optionData = $this->jsonSerializer->unserialize($amConfigurableOption);
                        if (isset($optionData['qty']) && $optionData['qty'] > 0) {
                            $matrixSelected = true;
                            break;
                        }
                    }
                    if (!$matrixSelected) {
                        $showOptionsResponse = true;
                    }
                } elseif ($attributesCount != $superParamsCount) {
                    $showOptionsResponse = true;
                }
                break;
            case 'grouped':
                if (!array_key_exists('super_group', $params)) {
                    $showOptionsResponse = true;
                }
                break;
            case 'amgiftcard':
                if (!array_key_exists('am_giftcard_recipient_email', $params)) {
                    $showOptionsResponse = true;
                }
                break;
            case 'giftcard':
                if (!array_key_exists('is_modal_show', $params)) {
                    $showOptionsResponse = true;
                }
                break;
            case 'bundle':
                if (!array_key_exists('bundle_option', $params)) {
                    $showOptionsResponse = true;
                }
                break;
            case 'downloadable':
                if ($requiredOptions
                    && !array_key_exists('links', $params)
                    && !array_key_exists('options', $params)
                ) {
                    $showOptionsResponse = true;
                }
                break;
            case 'simple':
            case 'virtual':
                // required custom options
                if ($requiredOptions && !array_key_exists('options', $params)) {
                    $showOptionsResponse = true;
                }
                break;
        }

        $amRecurringPayments = $product->getData('am_recurring_enable');

        if ($amRecurringPayments
            && $amRecurringPayments !== self::AM_RECURRING_PAYMENTS_DISABLED
            && !isset($params['subscribe'])
            && $this->moduleManager->isEnabled('Amasty_RecurringPayments')
        ) {
            $showOptionsResponse = true;
        }

        return !$showOptionsResponse;
    }
}
