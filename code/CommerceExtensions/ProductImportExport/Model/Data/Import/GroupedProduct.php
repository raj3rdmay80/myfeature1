<?php

/**
 * Copyright © 2018 CommerceExtensions. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CommerceExtensions\ProductImportExport\Model\Data\Import;

//use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product;

/**
 *  CSV Import Handler Grouped Product
 */

class GroupedProduct{

	protected $_filesystem;
		
	protected $ProductFactory;
	
    public function __construct(
		\Magento\Catalog\Model\ProductFactory $ProductFactory,
		\Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $ProductLinkInterfaceFactory,
		\Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
		\Magento\Catalog\Api\ProductRepositoryInterface $ProductRepositoryInterface,
		\Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterfaceFactory $ProductAttributeMediaGalleryEntryInterfaceFactory,
		\Magento\Catalog\Model\Product $Product,
		\CommerceExtensions\ProductImportExport\Helper\Data $helper
    ) {
         // prevent admin store from loading
		 $this->ProductFactory = $ProductFactory;
		 $this->ProductLinkInterfaceFactory = $ProductLinkInterfaceFactory;
    	 $this->productResourceModel = $productResourceModel;
		 $this->ProductRepositoryInterface = $ProductRepositoryInterface;
		 $this->ProductAttributeMediaGalleryEntryInterfaceFactory = $ProductAttributeMediaGalleryEntryInterfaceFactory;
		 $this->Product = $Product;
         $this->helper = $helper;
    }
	
	public function GroupedProductData($rowCount,$productID,$newProduct,$params,$ProcuctData,$ProductAttributeData,$ProductImageGallery,$ProductStockdata,$ProductSupperAttribute){
	
	$this->helper->rowCount = $rowCount;
	//UPDATE PRODUCT ONLY [START]
	$allowUpdateOnly = false;
	
	if($newProduct && $params['update_products_only'] == "true") {
		$allowUpdateOnly = true;
	}
	//UPDATE PRODUCT ONLY [END]
	
	if ($allowUpdateOnly == false) {
		
		#$imagePath = "/import";
		
		if(empty($ProductAttributeData['url_key'])) {
			unset($ProductAttributeData['url_key']);
		} else {
			//this solve the error:  URL key for specified store already exists. 
			$urlrewrite = $this->helper->checkUrlKey($ProcuctData['store_id'], $ProductAttributeData['url_key']);
			if ($urlrewrite->getId()) {
				for ($addNumberUrlKey = 0; $addNumberUrlKey <= 10; $addNumberUrlKey++) {
					$addToKey = $addNumberUrlKey + 1;
					$newUrlKey = $ProductAttributeData['url_key'] . '-' . $addToKey;
					$urlrewriteCheck = $this->helper->checkUrlKey($ProcuctData['store_id'], $newUrlKey);
					if (!$urlrewriteCheck->getId()) {
						break;
					}
				}
				$ProductAttributeData['url_key'] = $newUrlKey;
				$ProductAttributeData['url_path'] = $newUrlKey;
			}
		}
		if(empty($ProductAttributeData['url_path'])) {
			unset($ProductAttributeData['url_path']);
		}
		if(isset($ProductAttributeData['product_id'])) {
			unset($ProductAttributeData['product_id']);
		}
		
		$ProductAttributeData['entity_id'] = $productID;
		$ProductAttributeData['sku'] = $ProcuctData['sku'];
		$ProductAttributeData['store_id'] = $ProcuctData['store_id'];
		if(isset($ProcuctData['websites'])) { $ProductAttributeData['website_ids'] = $ProcuctData['websites']; }
		if(isset($ProcuctData['attribute_set'])) { $ProductAttributeData['attribute_set_id'] = $ProcuctData['attribute_set']; }
		if(isset($ProcuctData['prodtype'])) { $ProductAttributeData['type_id'] = $ProcuctData['prodtype']; }
		if(isset($ProcuctData['category_ids'])) { 
			if($ProcuctData['category_ids'] == "remove") { 
				$ProductAttributeData['category_ids'] = array(); 
			} else if($ProcuctData['category_ids'] != "") { 
				$ProductAttributeData['category_ids'] = $ProcuctData['category_ids'];
			}
		}
		$productModel = $this->ProductFactory->create(['data' => $ProductAttributeData]);
		
		if($newProduct || $params['reimport_images'] == "true") { 
		
			//media images
			$_productImages = array(
				'media_gallery'       => ($ProductImageGallery['gallery']!="") ? $ProductImageGallery['gallery'] : 'no_selection',
				'image'       => ($ProductImageGallery['image']!="") ? $ProductImageGallery['image'] : 'no_selection',
				'small_image'       => ($ProductImageGallery['small_image']!="") ? $ProductImageGallery['small_image'] : 'no_selection',
				'thumbnail'       => ($ProductImageGallery['thumbnail']!="") ? $ProductImageGallery['thumbnail'] : 'no_selection',
				'swatch_image'       => ($ProductImageGallery['swatch_image']!="") ? $ProductImageGallery['swatch_image'] : 'no_selection'
		
			);
			//create array of images with duplicates combind
			$labelCounter = 0;
			$imageArray = array();
			foreach ($_productImages as $columnName => $imageName) {
				$imageArray = $this->helper->addImage($imageName, $columnName, $imageArray);
			}
			
			foreach ($imageArray as $ImageFile => $imageColumns) {
				if($ImageFile != "no_selection") {
					$existingEntryIds = [];
					if(!$newProduct) {
						/*
						$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
						$productRepository = $objectManager->get('Magento\Catalog\Api\ProductRepositoryInterface');
						$product = $productRepository->get($ProductAttributeData['sku']);
						*/
						$product = $this->ProductRepositoryInterface->get($ProductAttributeData['sku']);
						$entries = $product->getMediaGalleryEntries();
					} else {
						$entries = [];
					}
					if(is_array($entries)) {
						foreach ($entries as $mediaEntry) {
							if (basename($mediaEntry->getFile()) === basename($ImageFile)) {
								break;
							}
						}
					}
					//above code is for update of a product.. the create below works fine for new only
					if (!isset($mediaEntry)) {
						$mediaEntry = $this->ProductAttributeMediaGalleryEntryInterfaceFactory->create();
					}
					$content = $this->helper->getContentObject($ImageFile);
					$mediaEntry->setContent($content);
					$mediaEntry->setMediaType('image');
					$mediaEntry->setPosition('0');
					$mediaEntry->setFile($ImageFile);
					$mediaEntry->setTypes($imageColumns);
					
					if($this->helper->checkIfImageIsExcluded($ProductAttributeData, $imageColumns)) { 
						$mediaEntry->setDisabled(true);
					} else {
						$mediaEntry->setDisabled(false);
					}
					if(in_array("image", $imageColumns)) {
						$productModel->setImage($ImageFile);
						#$this->helper->sendLog($this->helper->rowCount, '', 'setImage()' . $ImageFile);
						if(isset($ProductImageGallery['image_label'])) {
							$mediaEntry->setLabel($ProductImageGallery['image_label']);
						}
					}
					if(in_array("small_image", $imageColumns)) {
						$productModel->setSmallImage($ImageFile);
						#$this->helper->sendLog($this->helper->rowCount, '', 'setSmallImage()' . $ImageFile);
						if(isset($ProductImageGallery['small_image_label'])) {
							$mediaEntry->setLabel($ProductImageGallery['small_image_label']);
						}
					}
					if(in_array("thumbnail", $imageColumns)) {
						$productModel->setThumbnail($ImageFile);
						if(isset($ProductImageGallery['thumbnail_label'])) {
							$mediaEntry->setLabel($ProductImageGallery['thumbnail_label']);
						}
					}
					if(in_array("swatch_image", $imageColumns)) {
						$productModel->setSwatchImage($ImageFile);
						if(isset($ProductImageGallery['swatch_image_label'])) {
							$mediaEntry->setLabel($ProductImageGallery['swatch_image_label']);
						}
					}	
					if(in_array("media_gallery", $imageColumns)) {
						if (!empty( $ProductImageGallery['gallery_label'])) {
							$galleryLabels = explode(';', $ProductImageGallery['gallery_label']);
							if(isset($galleryLabels[$labelCounter])) {
								$mediaEntry->setLabel($galleryLabels[$labelCounter]);
								$mediaEntry->setPosition($labelCounter); //set position on gallery image
								$labelCounter++;
							}
						}
					}
					$entries[] = $mediaEntry;
					$productModel->setStoreId($productModel->getStoreId());
					$productModel->setMediaGalleryEntries($entries);	
					$productModel->save();	
				} else {
					foreach( $imageColumns as $mediaAttribute ) {
						$productModel->setData($mediaAttribute, 'no_selection');
					}
				}
			}
		}
		
		if($ProductStockdata!=""){ $productModel->setStockData($ProductStockdata); }
		
		/* MODDED TO ALLOW FOR GROUP POSITION AS WELL AND SHOULD WORK IF NO POSITION IS SET AS WELL CAN COMBO */
		$groupedpositionproducts = false;
		$finalIDssthatneedtobeconvertedto=array();
		$newLinks=array();
		
		if ($ProductSupperAttribute['grouped'] != "") {
			
			$groupedProductSkus = $ProductSupperAttribute['grouped'];
			if($params['append_grouped_products'] == "true") { 
				$_existingGrouped="";
				$associatedProducts = $productModel->getTypeInstance()->getAssociatedProducts($productModel, null);
				foreach($associatedProducts as $associatedProduct) {
					$_existingGrouped .= $associatedProduct->getSku() . ",";
				}
				$groupedProductSkus = $_existingGrouped . $groupedProductSkus;
			}
			
			$finalskusforarraytoexplode = explode(",",$groupedProductSkus);
			foreach($finalskusforarraytoexplode as $productskuexploded)
			{
				$pos = strpos($productskuexploded, ":");
				if ($pos !== false) {
					$finalidsforarraytoexplode = explode(":",$productskuexploded);
					$id = $this->Product->getIdBySku($finalidsforarraytoexplode[0]);
					if(isset($finalidsforarraytoexplode[1])) { $groupedQty = $finalidsforarraytoexplode[1]; } else { $groupedQty = 0; }
					if(isset($finalidsforarraytoexplode[2])) { $groupedPosition = $finalidsforarraytoexplode[2]; } else { $groupedPosition = 0; }
				} else {
					$id = $this->Product->getIdBySku($productskuexploded);
					$groupedQty = 0;
					$groupedPosition = 0;
				}
				if($id > 0) {
					/** @var \Magento\Catalog\Api\Data\ProductLinkInterface $productLink */
            		if(!isset($finalidsforarraytoexplode[2])) { $groupedPosition++; }
					$productLink = $this->ProductLinkInterfaceFactory->create();
					$linkedProduct = $this->ProductRepositoryInterface->getById($id);
					$productLink->setSku($productModel->getSku())
						->setLinkType('associated')
						->setLinkedProductSku($linkedProduct->getSku())
						->setLinkedProductType($linkedProduct->getTypeId())
						->setPosition($groupedPosition)
						->getExtensionAttributes()
						->setQty($groupedQty);
						
					$newLinks[] = $productLink;
				}
			}
			
		} else {
			$this->helper->sendLog($this->helper->rowCount,'grouped','The column is empty. If you are just updating other data just disregard');
		}
		
		$relatedProductData = array();
		$upSellProductData = array();
		$crossSellProductData = array();
		
		if($ProductSupperAttribute['related']!=""){ $relatedProductData = $this->helper->AppendRelatedProduct($ProductSupperAttribute['related'] ,$ProcuctData['sku']); }
		if($ProductSupperAttribute['upsell']!=""){ $upSellProductData = $this->helper->AppendUpsellProduct($ProductSupperAttribute['upsell'] ,$ProcuctData['sku']); }
		if($ProductSupperAttribute['crosssell']!=""){ $crossSellProductData = $this->helper->AppendCrossSellProduct($ProductSupperAttribute['crosssell'] , $ProcuctData['sku']); }
		
		if(!empty($relatedProductData) || !empty($upSellProductData) || !empty($crossSellProductData) || !empty($newLinks)) {
			$allProductLinks = array_merge($relatedProductData, $upSellProductData, $crossSellProductData, $newLinks);
			$productModel->setProductLinks($allProductLinks);
		}
		if($ProductSupperAttribute['tier_prices']!=""){ 
			$productModel->setTierPrice($ProductSupperAttribute['tier_prices']); 
		}
		
		try {
			#$productModel->save(); 
			$productModel->setIsMassupdate(true);
			$this->productResourceModel->save($productModel);
			if(!$productID) {
				$productID = $productModel->getEntityId();
			}
		}
		catch (\Exception $e) { 
			if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
				$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "ERROR: " . $e->getMessage());
				$this->helper->writeToCsv($cronLogErrors);	
			} else {
				throw new \Magento\Framework\Exception\LocalizedException(__('SKU: '.$ProcuctData['sku'].' ERROR : '. $e->getMessage()));
			}
		}
		
		if($ProductAttributeData['store_id'] != 0 && $productID != ""){
			#$this->helper->sendLog($this->helper->rowCount, 'store_id', 'STORE ID UPDATE: ' . $ProductAttributeData['store_id'] . "PRODUCT ID: " . $productID);
			$this->SetDataToStoreView($productID, $ProductAttributeData);
		}
		
	  }//END UPDATE ONLY CHECK
	  return $this->helper->msgtoreturn;
	}
	
	public function SetDataToStoreView($productID, $ProductAttributeData){
		
		$product = $this->ProductFactory->create()->reset();
		$this->productResourceModel->load($product, $productID);
		$product->setStoreId($ProductAttributeData['store_id']);
		if(isset($ProductAttributeData['price'])) { $product->setPrice($ProductAttributeData['price']); }
		#print_r(array_filter($ProductAttributeData));
		unset($ProductAttributeData['websites']);
		unset($ProductAttributeData['website_ids']);
		unset($ProductAttributeData['store']);
		unset($ProductAttributeData['prodtype']);
		unset($ProductAttributeData['sku']);
		unset($ProductAttributeData['store_id']);
		unset($ProductAttributeData['entity_id']);
		unset($ProductAttributeData['type_id']);
		$cleanedData = array_filter($ProductAttributeData);
		#print_r($cleanedData);
        foreach ($cleanedData as $rowIndex => $dataRow) {
			#$this->helper->sendLog($this->helper->rowCount, 'store_id', 'rowIndex: ' . $rowIndex . "dataRow: " . $dataRow);
			if($rowIndex == "price" || $rowIndex == "name" || $rowIndex == "description" || $rowIndex == "short_description" || $rowIndex == "meta_title" || $rowIndex == "meta_description") {
				#$this->helper->sendLog($this->helper->rowCount, 'store_id', 'rowIndex: ' . $rowIndex . " dataRow: " . $dataRow);
				$product->setData($rowIndex, $dataRow);
				$this->productResourceModel->saveAttribute($product, $rowIndex);
			}
		}
		
		#$this->productResourceModel->saveAttribute($product, 'price');
		
	}
}