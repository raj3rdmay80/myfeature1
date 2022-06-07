<?php
/**
 * Copyright © 2017 CommerceExtensions. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CommerceExtensions\ProductImportExport\Helper;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $customerSession;
    protected $product;
    protected $scopeConfig;
    protected $productLinkInterfaceFactory;
    protected $urlRewriteCollection;
    protected $csvProcessor;
    protected $directoryList;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    protected $attributeRepository;
    public $msgtoreturn = array();
    public $rowCount = 1;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
	 
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\File\Mime $mime,
		\Magento\Framework\Filesystem\Io\File $io,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Catalog\Model\Product $product,
		\Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $productLinkInterfaceFactory,
		\Magento\Framework\Api\Data\ImageContentInterfaceFactory $ImageContentInterfaceFactory,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteCollection,
        \Magento\Framework\File\Csv $csvProcessor,
        DirectoryList $directoryList,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
    )
    {
		$this->mime            			    = $mime;
        $this->scopeConfig         			= $scopeConfig;
        $this->io 							= $io;
		$this->customerSession              = $customerSession;
		$this->product 					    = $product;
		$this->productLinkInterfaceFactory  = $productLinkInterfaceFactory;
		$this->ImageContentInterfaceFactory = $ImageContentInterfaceFactory;
        $this->urlRewriteCollection 		= $urlRewriteCollection;
        $this->csvProcessor         		= $csvProcessor;
        $this->directoryList 				= $directoryList;
        $this->searchCriteriaBuilder        = $searchCriteriaBuilder;
        $this->attributeRepository          = $attributeRepository;
				
        parent::__construct($context);
    }
	
	protected function getMediaDirImportDir()
    {
        #return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'import';
        return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp'. DIRECTORY_SEPARATOR . 'catalog'. DIRECTORY_SEPARATOR . 'product';
    }
	
   	public function checkUrlKey($storeId, $urlKey) {
	
			$urlrewritesCollection = $this->urlRewriteCollection->create()->getCollection();
			//->addFieldToFilter('store_id', $storeId)
			if($storeId !=0) {
				$urlrewritesCollection->addFieldToFilter('store_id', $storeId);
			} 
			$urlrewritesCollection->addFieldToFilter('entity_type', 'product')
								  ->addFieldToFilter('request_path', $urlKey)//.".html"
								  ->setPageSize(1);
			return $urlrewritesCollection->getFirstItem();
	}
   	public function checkIfImageIsExcluded($ProductAttributeData, $imageColumns) {
	
		if($ProductAttributeData['image_exclude'] == "1" && in_array("image",$imageColumns)) {
			return true;
		} else if($ProductAttributeData['small_image_exclude'] == "1" && in_array("small_image",$imageColumns)) {
			return true;
		} else if($ProductAttributeData['thumbnail_exclude'] == "1" &&  in_array("thumbnail",$imageColumns)) {
			return true;
		} else if($ProductAttributeData['swatch_image_exclude'] == "1" &&  in_array("swatch_image",$imageColumns)) {
			return true;
		} else if($ProductAttributeData['gallery_exclude'] == "1" &&  in_array("gallery",$imageColumns)) {
			return true;
		} else {
			return false;
		}
	}
	public function getStoreConfig($path, $storeId)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
	public function writeToCsv($data) {
		$fileDirectoryPath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
	
		if(!is_dir($fileDirectoryPath))
			mkdir($fileDirectoryPath, 0777, true);
		$fileName = 'ce_product_import_log_errors.csv';
		$filePath =  $fileDirectoryPath . '/' . $fileName;
	
		#$data2 = [];
		/* pass data array to write in csv file */
		#$data2 = [['column 1','column 2','column 3'],['100001','test','test2']];
		
		$this->csvProcessor
			->setEnclosure('"')
			->setDelimiter(',')
			->saveData($filePath, $data);
	
		return true;
	}
	public function addImage($imageName, $columnName, $imageArray = array()) {
	
		if($imageName == "no_selection") {
			if (array_key_exists($imageName, $imageArray)) {
				array_push($imageArray[$imageName],$columnName);
			} else {
				$imageArray[$imageName] = array($columnName);
			}
			return $imageArray; 
		}
		if($imageName=="") { return $imageArray; }
		$importDir = $this->getMediaDirImportDir();
		
		if($columnName == "media_gallery") {
			$galleryData = explode(',', $imageName);
			foreach( $galleryData as $gallery_img ) {
				if(file_exists($importDir.$gallery_img)) {  
					if (array_key_exists($gallery_img, $imageArray)) {
						array_push($imageArray[$gallery_img],$columnName);
					} else {
						$imageArray[$gallery_img] = array($columnName);
					}
				} else {
					return $imageArray;
				}
			}
		} else {
			if(file_exists($importDir.$imageName)) {  
				if (array_key_exists($imageName, $imageArray)) {
					array_push($imageArray[$imageName],$columnName);
				} else {
					$imageArray[$imageName] = array($columnName);
				}
			} else {
				return $imageArray; 
			}
		}
		return $imageArray;
	}
	
	public function getContentObject(string $fileName)
    {
		#$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $srcPath = $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'catalog' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR . $fileName;
 
        if ($this->io->fileExists($srcPath)) {
			#$ImageContentInterfaceFactory = $objectManager->get('Magento\Framework\Api\Data\ImageContentInterfaceFactory');
			#$mime = $objectManager->get('Magento\Framework\File\Mime');
            $content = $this->ImageContentInterfaceFactory->create();
            $content->setName(strtolower($fileName));
            $content->setBase64EncodedData(base64_encode(file_get_contents($srcPath)));
            $content->setType($this->mime->getMimeType($srcPath));
 
            return $content;
        }
 
        return null;
    }
    
	public function userCSVDataAsArray( $data )
	{
		return explode( ',', str_replace( " ", " ", $data ) );
	} 
	
	public function sendLog($rowCount , $column, $error) {
		$this->msgtoreturn[] = array('line' => $rowCount , 'column' => $column, 'error' => $error);
	}
	
	public function AppendRelatedProduct($ReProduct , $sku){
	
		$URCProducts = explode(',',$ReProduct);
		$linkDataAll = array();
		$i = 0;
		foreach($URCProducts as $linkedSku){
			if($linkedSku!="") {
				$id = $this->product->getIdBySku($linkedSku);
				if($id > 0) {
					$linkData = $this->productLinkInterfaceFactory->create()
						->setSku($sku)
						->setLinkedProductSku($linkedSku)
						->setLinkType("related");
					$linkDataAll[] = $linkData;
				} else {
					$this->sendLog($this->rowCount,'related','The column contains sku that does NOT exist "'.$linkedSku.'"');
				}
			}
		}
		return $linkDataAll;
		
		
	}
	public function AppendUpsellProduct($UpProduct , $sku){
		
		$URCProducts = explode(',',$UpProduct);
		$linkDataAll = array();
		$i = 0;
		foreach($URCProducts as $linkedSku){
			if($linkedSku!="") {
				$id = $this->product->getIdBySku($linkedSku);
				if($id > 0) {
					$linkData = $this->productLinkInterfaceFactory->create()
						->setSku($sku)
						->setLinkedProductSku($linkedSku)
						->setLinkType("upsell");
					$linkDataAll[] = $linkData;
				} else {
					$this->sendLog($this->rowCount,'upsell','The column contains sku that does NOT exist "'.$linkedSku.'"');
				}
			}
		}
		return $linkDataAll;
		
	}
	public function AppendCrossSellProduct($CsProduct , $sku){
	
		$URCProducts = explode(',',$CsProduct);
		$linkDataAll = array();
		$i = 0;
		foreach($URCProducts as $linkedSku){
			if($linkedSku!="") {
				$id = $this->product->getIdBySku($linkedSku);
				if($id > 0) {
					$linkData = $this->productLinkInterfaceFactory->create()
						->setSku($sku)
						->setLinkedProductSku($linkedSku)
						->setLinkType("crosssell");
					$linkDataAll[] = $linkData;
				} else {
					$this->sendLog($this->rowCount,'crosssell','The column contains sku that does NOT exist "'.$linkedSku.'"');
				}
			}
		}
		return $linkDataAll;
	}
    /**
     * get user defined attributes with store view scope
     * @return array
     */
	public function getUserDefinedAttributes()
    {
        $userDefindedAttributes = [];

        $this->searchCriteriaBuilder->addFilter('is_user_defined',1);
        $this->searchCriteriaBuilder->addFilter('is_global',ScopedAttributeInterface::SCOPE_STORE);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $attributeRepository = $this->attributeRepository->getList(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
            $searchCriteria
        );

        foreach ($attributeRepository->getItems() as $attribute) {
            $userDefindedAttributes[] = $attribute->getAttributeCode();
        }
        return $userDefindedAttributes;
    }
}