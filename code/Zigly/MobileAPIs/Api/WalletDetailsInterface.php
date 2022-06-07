<?php 

namespace Zigly\MobileAPIs\Api;

interface WalletDetailsInterface {
    /**
     * Customer walletDetails.
     *
     * @return array
     */
    public function walletDetails();
	/**
     * Customer addMoney.
	 *
     * @return array
     */
    public function addMoney(); 
    /**
     * Customer walletStatus.
     *
     * @return array
     */
    public function walletStatus(); 
}