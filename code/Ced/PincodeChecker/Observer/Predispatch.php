<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_PincodeChecker
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\PincodeChecker\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class Predispatch
 * @package Ced\PincodeChecker\Observer
 */
class Predispatch implements ObserverInterface
{
    /**
     * @var \Ced\PincodeChecker\Model\Feed
     */
    protected $_feed;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * Predispatch constructor.
     * @param \Ced\PincodeChecker\Model\Feed $_feed
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     */
    public function __construct(
        \Ced\PincodeChecker\Model\Feed $_feed,
        \Magento\Backend\Model\Auth\Session $backendAuthSession
    )
    {
        $this->_feed = $_feed;
        $this->_backendAuthSession = $backendAuthSession;
    }


    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_backendAuthSession->isLoggedIn()) {
            $this->_feed->checkUpdate();
        }
        return $this;
    }
}
