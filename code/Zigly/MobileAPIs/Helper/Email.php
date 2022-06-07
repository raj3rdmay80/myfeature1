<?php
namespace Zigly\MobileAPIs\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Zigly\MobileAPIs\Mail\TransportBuilder;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Area;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $inlineTranslation;
    protected $escaper;
    protected $transportBuilder;
    protected $logger;
	protected $uploaderFactory;
	protected $fileSystem;
	protected $scopeConfig;

    /**
    * MSG91 authkey config path
    */
    const XML_PATH_MSG_AUTHKEY = 'msggateway/general/authkey';

    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
		\Magento\Framework\Api\Data\ImageContentInterface $imageContentInterface,
		UploaderFactory $uploaderFactory,
		Filesystem $fileSystem,
        StoreManagerInterface $storeManager,
		ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
		$this->_imageContentInterface = $imageContentInterface;
		$this->uploaderFactory = $uploaderFactory; 
		$this->fileSystem = $fileSystem;
		$this->mediaDirectory = $fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->storeManager = $storeManager;
		$this->scopeConfig = $scopeConfig;

    }

    public function sendEmail($customerEmail, $customerName, $fileData)
    {
        try {
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->scopeConfig->getValue('trans_email/ident_support/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'email' => $this->scopeConfig->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            ];
            $storeId = $this->storeManager->getStore()->getId();
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('prescription_email')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $storeId,
                    ]
                )
                ->setTemplateVars([
                    'customerName'  => $customerName,
                ])
                ->setFrom($sender)
                ->addTo($customerEmail);
			foreach($fileData['prescription'] as $pages){
				$path = $this->fileuploader($pages);
				$transport->addAttachment(file_get_contents($path),$pages['name'],$pages['type']);
			}
			$transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
			return true;
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
			return false;
        }
    }
	public static function parseRequestParams($requestArray){
        $requestArray = json_decode($requestArray,true);
        $tmpArray = array();
        if(!empty($requestArray)){
            foreach($requestArray as $keys => $values){
                if(trim($keys) == 'defaultParameters'){
                   foreach($values as $defaultKeys => $defaultValues){
                        $tmpArray[$defaultKeys] = $defaultValues;
                   }
                }else{
                    $tmpArray[$keys] = $values;
                }
            }    
        }
        if(!empty($tmpArray)){
            $requestArray = $tmpArray;
        }
        return $requestArray;
    }

	public function fileuploader($pages){
		$uploader = $this->uploaderFactory->create(['fileId' => $pages]);
		$uploader->setFilesDispersion(false);
		$uploader->setFilenamesCaseSensitivity(false);
		$uploader->setAllowRenameFiles(true);
		$uploader->setAllowedExtensions(['pdf','pptx', 'xls', 'xlsx', 'flash', 'mp3', 'docx', 'doc', 'zip', 'jpg', 'jpeg', 'png', 'gif', 'ini', 'readme', 'avi', 'csv', 'txt', 'wma', 'mpg', 'flv', 'mp4']);
		$target = $this->mediaDirectory->getAbsolutePath('prescription/');   
		$result = $uploader->save($target);
		return $target.$result['name'];
	}

    /**
     * Retrieve path
     *
     * @param string $path
     * @param string $imageName
     *
     * @return string
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    /*
    * send Customer Not Reachable sms
    */
    public function sendSms($vetBook)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue('vetconsulting/received_prescription/received_prescription_sms_template_id', $storeScope);
        $senderName = $this->scopeConfig ->getValue('vetconsulting/booking/vet_booking_sms_sender_name', $storeScope);
        $mobileNo = str_replace(' ', '', $vetBook['mobileNo']);
        $appointmentDate = $vetBook['appointment_date'];
        $appointmentTime = $vetBook['appointment_time'];
        $vetName = $vetBook['vet_name'];
        $path = $vetBook['url'];
        $mobileNo = '91'.$mobileNo; 
        if($authkey){
            if(is_numeric($mobileNo) && $smstemplateid){
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\",\n  \"appointment_date\": \"".$appointmentDate."\",\n  \"appointment_time\": \"".$appointmentTime."\",\n  \"vet_name\": \"".$vetName."\",\n  \"url\": \"".$path."\"\n}",
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
}