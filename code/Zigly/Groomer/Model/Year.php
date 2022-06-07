<?php
/**
* Copyright (C) 2021  Zigly
* @package   Zigly_Groomer
*/
namespace Zigly\Groomer\Model;

use Magento\Framework\Data\OptionSourceInterface;

class Year implements OptionSourceInterface
{

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $res = [];
        $year = range(1940,date('Y'));
        foreach($year as $value){
            $res[] = ['value' => $value, 'label' => $value];
        }
        return $res;
    }
}
