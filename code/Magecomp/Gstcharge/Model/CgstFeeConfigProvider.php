<?php
namespace Magecomp\Gstcharge\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magecomp\Gstcharge\Helper\Data as GstHelper;

class CgstFeeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */


   

    /**
     * @param \Magecomp\Gstcharge\Helper\Data $dataHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        GstHelper $dataHelper

    )
    {
        $this->dataHelper = $dataHelper;

    }

    /**
     * @return array
     */
    public function getConfig()
    {
		$GstchargeConfig = [];
        $enabled = $this->dataHelper->isModuleEnabled();
		if($enabled)
		{
			$gsttpye = $this->dataHelper->getGstTaxType();
			$GstchargeConfig['cgst_label'] = 'Incl. of CGST ';
			if($gsttpye == 1){
				$GstchargeConfig['cgst_label'] = 'Excl. of CGST ';	
			}
			$GstchargeConfig['cgst_charge'] = $this->dataHelper->getCgstCharge();
			$GstchargeConfig['show_hide_Gstcharge_block'] = ($enabled) ? true : false;
			$GstchargeConfig['show_hide_Gstcharge_shipblock'] = ($enabled) ? true : false;
		}
        return $GstchargeConfig;
    }
}
