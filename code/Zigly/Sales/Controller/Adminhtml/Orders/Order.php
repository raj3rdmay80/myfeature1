<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
declare(strict_types=1);

namespace Zigly\Sales\Controller\Adminhtml\Orders;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;

abstract class Order extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}