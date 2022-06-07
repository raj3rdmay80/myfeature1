<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */

namespace Zigly\GroomingService\Model;

// use Magento\Framework\App\Request\Http;
// use Magento\Framework\Session\SidResolverInterface;
// use Magento\Framework\Session\Config\ConfigInterface;
// use Magento\Framework\Session\SaveHandlerInterface;
// use Magento\Framework\Session\ValidatorInterface;
// use Magento\Framework\Session\StorageInterface;
// use Magento\Framework\Stdlib\CookieManagerInterface;
// use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
// use Magento\Framework\App\Http\Context;
// use Magento\Framework\App\State;
// use Magento\Framework\Session\Generic;
// use Magento\Framework\Event\ManagerInterface;
// use Magento\Framework\App\Response\Http;

class Session extends \Magento\Framework\Session\SessionManager
{
    protected $_session;
    protected $_coreUrl = null;
    protected $_configShare;
    protected $_urlFactory;
    protected $_eventManager;
    protected $response;
    protected $_sessionManager;
 
  public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Session\SaveHandlerInterface $saveHandler,
        \Magento\Framework\Session\ValidatorInterface $validator,
        \Magento\Framework\Session\StorageInterface $storage,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Session\Generic $session,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Response\Http $response
    ) {

        $this->_session = $session;
        $this->_eventManager = $eventManager;
 
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState
        );
        $this->response = $response;
        $this->_eventManager->dispatch('groomingsession_session_init', ['groomingsession_session' => $this]);
    }  
}