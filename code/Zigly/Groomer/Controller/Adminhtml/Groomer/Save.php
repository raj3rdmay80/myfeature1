<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Groomer
 */
declare(strict_types=1);

namespace Zigly\Groomer\Controller\Adminhtml\Groomer;

use Zigly\Sales\Helper\Data;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Zigly\Groomer\Model\ImageUploader;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Zigly\Groomer\Model\ResourceModel\Groomer\CollectionFactory;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @var CollectionFactory $collectionFactory
     */
    protected $collectionFactory;

    /**
    * MSG91 authkey config path
    */
    const XML_PATH_MSG_AUTHKEY = 'msggateway/general/authkey';

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;


    /**
     * @param Data $helperData
     * @param Context $context
     * @param ImageUploader $imageUploader
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $CollectionFactory
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        Data $helperData,
        ImageUploader $imageUploader,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor
    ) {
        $this->helperData = $helperData;
        $this->scopeConfig = $scopeConfig;
        $this->imageUploader = $imageUploader;
        $this->dataPersistor = $dataPersistor;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('groomer_id');
            if (isset($data['profile_image'][0]['name']) && isset($data['profile_image'][0]['tmp_name'])) {
                $data['profile_image'] = $data['profile_image'][0]['name'];
                $this->imageUploader->moveFileFromTmp($data['profile_image']);
            } elseif (isset($data['profile_image'][0]['name']) && !isset($data['profile_image'][0]['tmp_name'])) {
                $data['profile_image'] = $data['profile_image'][0]['name'];
            } else {
                $data['profile_image'] = '';
            }
            if (isset($data['bd_profile_image'][0]['name']) && isset($data['bd_profile_image'][0]['tmp_name'])) {
                $data['bd_profile_image'] = $data['bd_profile_image'][0]['name'];
                $this->imageUploader->moveFileFromTmp($data['bd_profile_image']);
            } elseif (isset($data['bd_profile_image'][0]['name']) && !isset($data['bd_profile_image'][0]['tmp_name'])) {
                $data['bd_profile_image'] = $data['bd_profile_image'][0]['name'];
            } else {
                $data['bd_profile_image'] = '';
            }
            if (isset($data['aadhar_img'][0]['name']) && isset($data['aadhar_img'][0]['tmp_name'])) {
                $data['aadhar_img'] = $data['aadhar_img'][0]['name'];
                $this->imageUploader->moveFileFromTmp($data['aadhar_img']);
            } elseif (isset($data['aadhar_img'][0]['name']) && !isset($data['aadhar_img'][0]['tmp_name'])) {
                $data['aadhar_img'] = $data['aadhar_img'][0]['name'];
            } else {
                $data['aadhar_img'] = '';
            }
            if (isset($data['aadhar_img_back'][0]['name']) && isset($data['aadhar_img_back'][0]['tmp_name'])) {
                $data['aadhar_img_back'] = $data['aadhar_img_back'][0]['name'];
                $this->imageUploader->moveFileFromTmp($data['aadhar_img_back']);
            } elseif (isset($data['aadhar_img_back'][0]['name']) && !isset($data['aadhar_img_back'][0]['tmp_name'])) {
                $data['aadhar_img_back'] = $data['aadhar_img_back'][0]['name'];
            } else {
                $data['aadhar_img_back'] = '';
            }
            if (isset($data['pan_img'][0]['name']) && isset($data['pan_img'][0]['tmp_name'])) {
                $data['pan_img'] = $data['pan_img'][0]['name'];
                $this->imageUploader->moveFileFromTmp($data['pan_img']);
            } elseif (isset($data['pan_img'][0]['name']) && !isset($data['pan_img'][0]['tmp_name'])) {
                $data['pan_img'] = $data['pan_img'][0]['name'];
            } else {
                $data['pan_img'] = '';
            }
            if (isset($data['cheque_image'][0]['name']) && isset($data['cheque_image'][0]['tmp_name'])) {
                $data['cheque_image'] = $data['cheque_image'][0]['name'];
                $this->imageUploader->moveFileFromTmp($data['cheque_image']);
            } elseif (isset($data['cheque_image'][0]['name']) && !isset($data['cheque_image'][0]['tmp_name'])) {
                $data['cheque_image'] = $data['cheque_image'][0]['name'];
            } else {
                $data['cheque_image'] = '';
            }
            if (isset($data['same_as_permanent_address']) && $data['same_as_permanent_address'] == 1){
                $data['current_house_no'] = $data['permanent_house_no'];
                $data['current_locality'] = $data['permanent_locality'];
                $data['current_city'] = $data['permanent_city'];
                $data['current_pincode'] = $data['permanent_pincode'];
                $data['current_state'] = $data['permanent_state'];
            }
            if (isset($data['upload_certificate'][0]['name']) && isset($data['upload_certificate'][0]['tmp_name'])) {
                $data['upload_certificate'] = $data['upload_certificate'][0]['name'];
                $this->imageUploader->moveFileFromTmp($data['upload_certificate']);
            } elseif (isset($data['upload_certificate'][0]['name']) && !isset($data['upload_certificate'][0]['tmp_name'])) {
                $data['upload_certificate'] = $data['upload_certificate'][0]['name'];
            } else {
                $data['upload_certificate'] = '';
            }
            /*if (isset($data['select_specialisation'])) {
                $data['select_specialisation'] = implode(",", $data['select_specialisation']);
            }*/
            if (isset($data['select_specialisation']) && !empty($data['select_specialisation']) && $data['select_specialisation'] == "Other") {
                $data['specialisation_other'] = $data['specialisation_other'];
            } else {
                $data['specialisation_other'] = '';
            }
            if (isset($data['select_facilities'])) {
                $data['select_facilities'] = implode(",", $data['select_facilities']);
            }
            $model = $this->_objectManager->create(\Zigly\Groomer\Model\Groomer::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Professional no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            try {
                $groomerCollection = $this->collectionFactory->create();
                $groomerCollection->addFieldToFilter('groomer_id', array('neq' => $id));
                $flag = 1;
                foreach ($groomerCollection as $value) {
                    if ($data['phone_number'] && $value['phone_number'] == $data['phone_number']) {
                        $flag = 0;
                    }
                    if ($data['email'] && $value['email'] == $data['email']) {
                        $flag = 2;
                    }
                }
                if (!empty($data['phone_number']) && !empty($data['status']) && $model->getStatus() != 1 && $data['status'] == 1) {
                    $serviceVar['mobileNo'] = $data['phone_number'];
                    $serviceVar['templateid'] = 'status_notify/status/professional_approve_sms';
                    $this->sendProfessionalSms($serviceVar);
                    $templateVariable['email'] = $data['email'];
                    $templateVariable['template_id'] = 'status_notify/status/professional_approve_email';
                    $this->sendMail($templateVariable);
                }
                if ($flag == 1) {
                    $model->setData($data);
                    $model->save();
                    $this->messageManager->addSuccessMessage(__('You saved the Professional.'));
                    $this->dataPersistor->clear('zigly_groomer_groomer');
                } elseif ($flag == 2) {
                    $this->messageManager->addErrorMessage(__('Email Id already exists.'));
                } else {
                    $this->messageManager->addErrorMessage(__('Phone number already exists.'));
                }

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['groomer_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Professional.'));
            }
        
            $this->dataPersistor->set('zigly_groomer_groomer', $data);
            return $resultRedirect->setPath('*/*/edit', ['groomer_id' => $this->getRequest()->getParam('groomer_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Groomer::Groomer_save');
    }

    /*
    * send service provider detail submission sms & profile approved
    */
    public function sendProfessionalSms($serviceVar)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$serviceVar['templateid'].'', $storeScope);
        $senderName = $this->scopeConfig ->getValue('status_notify/status/professional_status_sender_name', $storeScope);
        $mobileNo = trim($serviceVar['mobileNo']);
        $mobileNo = '91'.$mobileNo;
        if($authkey){
            if(is_numeric($mobileNo) && !empty($smstemplateid) && !empty($senderName)){
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\"\n}",
                  CURLOPT_HTTPHEADER => array(
                    "authkey: ".$authkey."",
                    "content-type: application/JSON"
                  ),
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
            }
        }
        return true;
    }

    /*
    * get auth key
    */
    public function getMsgauthkey()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_MSG_AUTHKEY, $storeScope);
    }

    /*
    * send email function
    */
    public function sendMail($templatevariable)
    {
        $this->inlineTranslation->suspend();
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $email = $this->scopeConfig->getValue('trans_email/ident_sales/email', $storeScope, $storeId);
            $name = $this->scopeConfig->getValue('trans_email/ident_sales/name', $storeScope, $storeId);
            $templateid = $this->scopeConfig->getValue(''.$templatevariable['template_id'].'', $storeScope, $storeId);
            $sender = [
                'name' => $name,
                'email' => $email,
            ];

            $transport = $this->transportBuilder->setTemplateIdentifier(
                    $templateid
                )->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $storeId
                    ]
                )->setTemplateVars(
                    ['cancelDetails' => $templatevariable]
                )->setFrom(
                    $sender
                )->addTo(
                    $templatevariable['email']
                )->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            //$this->logger->debug($e->getMessage());
        }
        $this->inlineTranslation->resume();
        return $this;
    }
}

