<?php
namespace Magecomp\Gstcharge\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magecomp\Gstcharge\Helper\Data as GstHelper;


class SgstFeeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $dataHelper;



   
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
			$GstchargeConfig['sgst_label'] = 'Incl. of SGST';
			if($gsttpye == 1){
				$GstchargeConfig['sgst_label'] = 'Excl. of SGST';
			}
			$GstchargeConfig['sgst_charge'] = $this->dataHelper->getSgstCharge();
			$GstchargeConfig['show_hide_sgst_block'] = ($enabled) ? true : false;
			$GstchargeConfig['show_hide_sgst_shipblock'] = ($enabled) ? true : false;
		}
        return $GstchargeConfig;
    }
}
