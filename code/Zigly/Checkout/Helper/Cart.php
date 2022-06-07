<?php 
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Checkout
 */
namespace Zigly\Checkout\Helper;

class Cart
{
    public function afterGetDeletePostJson($subject, $result)
    {
        $result = json_decode($result, true);
        $result['data']['confirmationMessage'] = 'Are you sure you would like to remove this item from the shopping cart?';
        $result['data']['confirmation'] = true;
        return json_encode($result);
    }
}