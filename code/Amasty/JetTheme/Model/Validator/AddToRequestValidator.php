<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Validator;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;

class AddToRequestValidator
{
    /**
     * @var Validator
     */
    private $formKeyValidator;

    public function __construct(Validator $formKeyValidator)
    {
        $this->formKeyValidator = $formKeyValidator;
    }

    /**
     * @param RequestInterface $request
     * @throws LocalizedException
     */
    public function validateRequest(RequestInterface $request): void
    {
        if (!$this->formKeyValidator->validate($request)) {
            throw new LocalizedException(__('Invalid form key'));
        }

        $productId = (int)$request->getParam('product');
        if (!$productId) {
            throw new LocalizedException(__('Invalid product id'));
        }
    }
}
