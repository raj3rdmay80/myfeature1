<?php
namespace Zigly\Referral\Model\Source;

class ReferralOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource 
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
                'label' => 'Fixed'
            ];
        $type[] = [
                'value' => 2,
                'label' => 'Percentage'
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