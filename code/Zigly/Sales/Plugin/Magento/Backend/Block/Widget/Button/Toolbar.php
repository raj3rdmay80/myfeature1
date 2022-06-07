<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Sales\Plugin\Magento\Backend\Block\Widget\Button;

class Toolbar
{

    public function beforePushButtons(
        \Magento\Backend\Block\Widget\Button\Toolbar $subject,
        $context,
        $buttonList
    ) {
    	if (!$context instanceof \Magento\Sales\Block\Adminhtml\Order\View) {
            return [$context, $buttonList];
        }
        // Remove default cancel button
        $buttonList->remove('order_cancel');
        return [$context, $buttonList];
    }
}
