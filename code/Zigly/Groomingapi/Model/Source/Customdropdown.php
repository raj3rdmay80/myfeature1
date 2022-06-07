<?php


namespace Zigly\Groomingapi\Model\Source;

class Customdropdown extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource 
{
    public function getAllOptions() 
    {
        $type = [];
        $type[] = [
                'value' => '',
                'label' => '--Select--'
            ];
        $type[] = [
                'value' => 1,
                'label' => 'Home'
            ];
        $type[] = [
                'value' => 2,
                'label' => 'Office'
            ];
            $type[] = [
                    'value' => 3,
                    'label' => 'Other'
                ];
        return $type;
    }
    
    public function getOptionText($value) 
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}