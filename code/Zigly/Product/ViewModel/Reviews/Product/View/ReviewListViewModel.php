<?php
/**
 * @author Zigly Team
 * @copyright Copyright (c) 2021 Zigly
 * @package Zigly_Product
 */

declare(strict_types=1);

namespace Zigly\Product\ViewModel\Reviews\Product\View;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class ReviewListViewModel implements ArgumentInterface
{
    /**
     * Get Date formatted.
     *
     * @param string $datetime
     * @param string $full
     * @return string
     */
    public function reviewedAtFormat($datetime, $full = false)
    {
        $now = new \DateTime;
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}
