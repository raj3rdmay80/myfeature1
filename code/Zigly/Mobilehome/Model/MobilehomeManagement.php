<?php
/*
#declare(strict_types=1);
*/

namespace Zigly\Mobilehome\Model;

use Zigly\MobileHome\Model\ResourceModel\Mobilehome\CollectionFactory;
use Magento\Framework\Serialize\Serializer\Json as JsonSerialize;
use Magento\Framework\App\Response\Http;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class MobilehomeManagement implements \Zigly\Mobilehome\Api\MobilehomeManagementInterface
{

 /**
     * constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        JsonSerialize $jsonSerialize,
        StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlInterface,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\HTTP\Client\Curl $curl,
        Http $http
    ) {

        $this->collectionFactory = $collectionFactory;
        $this->jsonSerialize = $jsonSerialize;
        $this->_urlInterface = $urlInterface;
        $this->storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_curl = $curl;
        $this->http = $http;
     
    }

    /**
     * @return \Zigly\Mobilehome\Api\Data\MobilehomeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMobilehome()
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $result = $this->collectionFactory->create();
        $result->setOrder('position','ASC');
        $result->addFieldToFilter(['status'],[['eq' => 1]]);
        $data = $result->getData();


        $response_data = [];
        foreach($data as $key=>$value){
            if($value['type'] == 0){
                $response_data['banner'][]= array(
                        "mobilehome_id" => $value['mobilehome_id'],
                        "name" => $value['name'],
                        "image" =>$mediaUrl."Zigly/mobilehome/".$value['image'],
                        "isclickable" => $value['isclickable'],
                        "catid" => $value['catid'],
                        "type" => $value['type'],
                        "status" => $value['status'],
                        "position" => $value['position'],
                        "clickmeta" => $this->metafilter($value['url_key'])   
                );
            }
            if($value['type'] == 1){
                $response_data['category'][]= array(
                        "mobilehome_id" => $value['mobilehome_id'],
                        "name" => $value['name'],
                        "image" => $mediaUrl."Zigly/mobilehome/".$value['image'],
                        "isclickable" => $value['isclickable'],
                        "catid" => $value['catid'],
                        "type" => $value['type'],
                        "status" => $value['status'],
                        "position" => $value['position'],
                        "clickmeta" => $this->metafilter($value['url_key'])  
                );
            }
            if($value['type'] == 2){
                $response_data['brand'][]= array(
                        "mobilehome_id" => $value['mobilehome_id'],
                        "name" => $value['name'],
                        "image" => $mediaUrl."Zigly/mobilehome/".$value['image'],
                        "isclickable" => $value['isclickable'],
                        "catid" => $value['catid'],
                        "type" => $value['type'],
                        "status" => $value['status'],
                        "position" => $value['position'],
                        "clickmeta" => $this->metafilter($value['url_key'])  
                );
            }
            if($value['type'] == 3){
                $response_data['offer'][]= array(
                        "mobilehome_id" => $value['mobilehome_id'],
                        "name" => $value['name'],
                        "image" => $mediaUrl."Zigly/mobilehome/".$value['image'],
                        "isclickable" => $value['isclickable'],
                        "catid" => $value['catid'],
                        "type" => $value['type'],
                        "status" => $value['status'],
                        "position" => $value['position'],
                        "clickmeta" => $this->metafilter($value['url_key'])  
                );
            }

        }
        
        //return $response_data;
       header("Content-Type: application/json; charset=utf-8");
       $this->response = json_encode($response_data);
       print_r($this->response,false);
       die();
    }

    public function metafilter($url){

        $url_filter = explode("?",$url);
        //print_r($url_filter);
        //echo count($url_filter);
        //exit();
        if(count($url_filter) > 1){
            $type_id = explode("/",$url_filter[0]);
            $url_data = explode("&",$url_filter[1]);

            $filter = array();

            foreach($url_data as $key => $value){
                $filterItemArray = explode("=", $value);
		$filterKey = $filterItemArray[0];
		$filterValueArray = explode(",",$filterItemArray[1]);
		$filter[] = array('filter' => $filterKey, 'value' => $filterValueArray);
		//$filter[]['filter'] = $filterValueArray;
            }
            
            $filterdata = array(
                "type" => $type_id[0],
                "id" => $type_id[1],
                "filterdata"=>$filter
            );
        }elseif (count($url_filter) == 1) {
            $type_id = explode("/",$url_filter[0]);
            $filterdata = array(
                "type" => $type_id[0],
                "id" => $type_id[1],
                "filterdata"=>[]
            );
        }

        return $filterdata;
    
    }
}

