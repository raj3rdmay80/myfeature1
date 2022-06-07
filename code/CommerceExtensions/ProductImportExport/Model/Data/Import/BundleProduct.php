<?php

/**
 * Copyright © 2017 CommerceExtensions. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CommerceExtensions\ProductImportExport\Model\Data\Import;

#use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
#use Magento\Framework\Filesystem;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product;

/**
 *  CSV Import Handler Bundle Product
 */
 
class BundleProduct{

	protected $ProductFactory;
	
    public function __construct(
        ResourceConnection $resource,
		\Magento\Catalog\Model\ProductFactory $ProductFactory,
		\Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
		\Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $ProductLinkInterfaceFactory,
		\Magento\Catalog\Api\ProductRepositoryInterface $ProductRepositoryInterface,
		\Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterfaceFactory $ProductAttributeMediaGalleryEntryInterfaceFactory,
		\Magento\Catalog\Model\Product $Product,
		\CommerceExtensions\ProductImportExport\Helper\Data $helper
    ) {
         // prevent admin store from loading
         $this->_resource = $resource;
		 $this->ProductFactory = $ProductFactory;
    	 $this->productResourceModel = $productResourceModel;
		 $this->ProductLinkInterfaceFactory = $ProductLinkInterfaceFactory;
		 $this->ProductRepositoryInterface = $ProductRepositoryInterface;
		 $this->ProductAttributeMediaGalleryEntryInterfaceFactory = $ProductAttributeMediaGalleryEntryInterfaceFactory;
		 $this->Product = $Product;
         $this->helper = $helper;
    }
	/*
	private function prepareBundleOptionsMap($product)
	{
		$bundle_options_map = array();
	
		foreach ($product['children'] as $childName => $childId) {
			$child_map = [
				'title' => $childName,
				'default_title' => $childName,
				'type' => 'select',
				'required' => 1,
				'delete' => '',
			];
	
			$bundle_options_map[] = $child_map;
		}
	
		return $bundle_options_map;
	}
	
	private function prepareBundleSelectionsMap($product)
		{
		$bundle_selections_map = array();
	
	
		foreach ($product['children'] as $childName => $childData) {
			$child_map = [[
				'product_id' => $childData['id'],
				'selection_qty' => 1,
				'selection_can_change_qty' => $childData['license_extended_can_change_qty'],
				'delete' => '',
				'user_defined' => $childData['license_extended_can_change_qty']
			]];
	
			$bundle_selections_map[] = $child_map;
	
			if ($childData['status'] == Status::STATUS_DISABLED) {
				$this->_logger->addInfo(__(' Bundle ' . $product['title'] . ' - one of children is disabled: ' . $childData['name']));
			}
		}
	
		return $bundle_selections_map;
	}
	*/
	
	public function BundleProductData($rowCount,$productID,$newProduct,$SetProductData,$params,$ProcuctData,$ProductAttributeData,$ProductImageGallery,$ProductStockdata,$ProductSupperAttribute,$ProductCustomOption){
	
	$this->helper->rowCount = $rowCount;
	
	//UPDATE PRODUCT ONLY [START]
	$allowUpdateOnly = false;
	if(!$SetProductData || empty($SetProductData)) {
		#$SetProductData = $this->ProductFactory->create();
	}
	if($newProduct && $params['update_products_only'] == "true") {
		$allowUpdateOnly = true;
	} 
	
	//UPDATE PRODUCT ONLY [END]
	if ($allowUpdateOnly == false) {
		
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
		#$this->helper->sendLog($this->helper->rowCount, 'store_id', 'STORE ID: ' . $productModel->getStoreId());
		//force attributes NULL if not set in csv FOR STORE VIEWS other then admin ONLY... w/o it leaves them unchecked
		if($ProductAttributeData['store_id'] != 0 && !$newProduct) {
			if(!isset($ProductAttributeData['name'])) { $ProductAttributeData['name'] = null; }
			if(!isset($ProductAttributeData['description'])) { $ProductAttributeData['description'] = null; }
			if(!isset($ProductAttributeData['short_description'])) { $ProductAttributeData['short_description'] = null; }
			if(!isset($ProductAttributeData['status'])) { $ProductAttributeData['status'] = null; }
			if(!isset($ProductAttributeData['tax_class_id'])) { $ProductAttributeData['tax_class_id'] = null; }
			if(!isset($ProductAttributeData['visibility'])) { $ProductAttributeData['visibility'] = null; }
			if(!isset($ProductAttributeData['is_returnable'])) { $ProductAttributeData['is_returnable'] = null; }
			if(!isset($ProductAttributeData['options_container'])) { $ProductAttributeData['options_container'] = null; }
			if(!isset($ProductAttributeData['url_key'])) { $ProductAttributeData['url_key'] = null; }
		}
		
		if($ProductAttributeData['store_id'] > 0 && $productID != ""){
			#$this->helper->sendLog($this->helper->rowCount, 'store_id', 'STORE ID UPDATE: ' . $ProductAttributeData['store_id'] . "PRODUCT ID: " . $productID);
			$this->SetDataToStoreView($productID, $ProductAttributeData);
		} else { 
		
		if(empty($ProductAttributeData['url_key'])) {
			unset($ProductAttributeData['url_key']);
			//$ProductAttributeData['url_key'] = null;
		} else {
			//this solve the error:  URL key for specified store already exists. 
			$urlrewrite = $this->helper->checkUrlKey($ProcuctData['store_id'], $ProductAttributeData['url_key']);
			if ($urlrewrite->getId()) {
				for ($addNumberUrlKey = 0; $addNumberUrlKey <= 10; $addNumberUrlKey++) {
					$addToKey = $addNumberUrlKey + 1;
					$newUrlKey = $ProductAttributeData['url_key'] . '-' . $addToKey;
					$urlrewriteCheck = $this->helper->checkUrlKey($ProcuctData['store_id'], $newUrlKey);
					if (!$urlrewriteCheck->getId()) { break; }
				}
				$ProductAttributeData['url_key'] = $newUrlKey;
				$ProductAttributeData['url_path'] = $newUrlKey;
			}
		}
		if(empty($ProductAttributeData['url_path'])) { unset($ProductAttributeData['url_path']); }
		if(isset($ProductAttributeData['product_id'])) { unset($ProductAttributeData['product_id']); }
		
		$productModel = $this->ProductFactory->create(['data' => $ProductAttributeData]);
		
		//get existing custom options and set them if we are not passing in any
		if(empty($ProductCustomOption)) {
			#$this->helper->sendLog($this->helper->rowCount, 'store_id', 'STORE ID UPDATE: ' . $ProductAttributeData['store_id'] . "PRODUCT ID: " . $productID);
			$productModel2 = $this->ProductFactory->create()->setStoreId($ProductAttributeData['store_id'])->load($productModel->getId());
			$customOptions = $productModel2->getOptions();
			$productModel->setCanSaveCustomOptions(true);
			$productModel->setOptions($customOptions); //added this in 2.2.3
			$productModel->setHasOptions(true); //added this in 2.2.3
			#$productModel->save();
		}
		
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
			#foreach ($_productImages as $columnName => $ImageFile) {
				$skipImageOnMatch = false;
				if($ImageFile != "no_selection") {
					
					$existingEntryIds = [];
					if(!$newProduct) {
						
						$product = $this->ProductRepositoryInterface->get($ProductAttributeData['sku']);
						$entries = $product->getMediaGalleryEntries();
					} else {
						$entries =  [];
					}
					if(is_array($entries)) {
						foreach ($entries as $mediaEntry) {
							if (basename($mediaEntry->getFile()) === basename($ImageFile)) {
								$this->helper->sendLog($this->helper->rowCount, implode(" / " , $imageColumns), 'Image Already Exists: ' . basename($mediaEntry->getFile()));
								#$mediaEntry->unsData('file');
								$skipImageOnMatch = true;
								break;
							}
						}
					}
					//above code is for update of a product.. the create below works fine for new only
					if(!$skipImageOnMatch) {
						#if (!isset($mediaEntry)) {
							$mediaEntry = $this->ProductAttributeMediaGalleryEntryInterfaceFactory->create();
						#}
						#$content = $this->getContentObject($ImageFile);
						$content = $this->helper->getContentObject($ImageFile);
						$mediaEntry->setContent($content);
						$mediaEntry->setMediaType('image');
						$mediaEntry->setPosition('0');
						$mediaEntry->setFile($ImageFile);
						$this->helper->sendLog($this->helper->rowCount, implode(" / " , $imageColumns), 'Importing New Image: ' . $ImageFile);						
						$mediaEntry->setTypes($imageColumns);
						
						if($this->helper->checkIfImageIsExcluded($ProductAttributeData, $imageColumns)) { 
							$mediaEntry->setDisabled(true);
						} else {
							$mediaEntry->setDisabled(false);
						}
						#if($columnName == "image" && $_productImages['image'] !="no_selection") {
						if(in_array("image", $imageColumns)) {
							$productModel->setImage($ImageFile);
							#$this->helper->sendLog($this->helper->rowCount, '', 'setImage()' . $ImageFile);
							if(isset($ProductImageGallery['image_label'])) {
								$mediaEntry->setLabel($ProductImageGallery['image_label']);
							}
						}
						#if($columnName == "small_image" && $_productImages['small_image'] !="no_selection") {
						if(in_array("small_image", $imageColumns)) {
							$productModel->setSmallImage($ImageFile);
							#$this->helper->sendLog($this->helper->rowCount, '', 'setSmallImage()' . $ImageFile);
							if(isset($ProductImageGallery['small_image_label'])) {
								$mediaEntry->setLabel($ProductImageGallery['small_image_label']);
							}
						}
						#if($columnName == "thumbnail" && $_productImages['thumbnail'] !="no_selection") {
						if(in_array("thumbnail", $imageColumns)) {
							$productModel->setThumbnail($ImageFile);
							#$this->helper->sendLog($this->helper->rowCount, '', 'setThumbnail()' . $ImageFile);
							if(isset($ProductImageGallery['thumbnail_label'])) {
								$mediaEntry->setLabel($ProductImageGallery['thumbnail_label']);
							}
						}
						#if($columnName == "swatch_image" && $_productImages['swatch_image'] !="no_selection") {
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
									$labelCounter++; // this is here 
								}
							}
						}
						$entries[] = $mediaEntry;
						$productModel->setStoreId($productModel->getStoreId());
						$productModel->setMediaGalleryEntries($entries);	
						$productModel->save();	
					} else {
						if(in_array("media_gallery", $imageColumns)) {
							if (!empty( $ProductImageGallery['gallery_label'])) {
								$galleryLabels = explode(';', $ProductImageGallery['gallery_label']);
								if(isset($galleryLabels[$labelCounter])) {
									$labelCounter++; 
									// this is here so if we are importing only 1 new gallery image and there is existing ones. 
									// that we only import the label for that gallery image
								}
							}
						}
					}
					#$productModel->setMediaGalleryEntries($entries);
					#$productModel->setData($mediaAttribute, 'no_selection');
					#$productRepository->save($product);									
				} else {
					foreach( $imageColumns as $mediaAttribute ) {
						$productModel->setData($mediaAttribute, 'no_selection');
					}
				}
			}
		}
		
		if($ProductStockdata!=""){ $productModel->setStockData($ProductStockdata); }
			 	
		$relatedProductData = array();
		$upSellProductData = array();
		$crossSellProductData = array();
		
		if($ProductSupperAttribute['related']!=""){ $relatedProductData = $this->helper->AppendRelatedProduct($ProductSupperAttribute['related'],$ProcuctData['sku']);}
		if($ProductSupperAttribute['upsell']!=""){ $upSellProductData = $this->helper->AppendUpsellProduct($ProductSupperAttribute['upsell'],$ProcuctData['sku']);}
		if($ProductSupperAttribute['crosssell']!=""){ $crossSellProductData = $this->helper->AppendCrossSellProduct($ProductSupperAttribute['crosssell'],$ProcuctData['sku']);}
		
		if(!empty($relatedProductData) || !empty($upSellProductData) || !empty($crossSellProductData)) {
			$allProductLinks = array_merge($relatedProductData, $upSellProductData, $crossSellProductData);
			$productModel->setProductLinks($allProductLinks);
		}
	
		if($ProductSupperAttribute['tier_prices']!=""){ 
			$productModel->setTierPrice($ProductSupperAttribute['tier_prices']); 
		}
		/*
		$bundle_options_map = $this->prepareBundleOptionsMap($migrated_product);
		$bundle_selections_map = $this->prepareBundleSelectionsMap($migrated_product);
		
			$product->setBundleOptionsData($bundle_options_map);
			$product->setBundleSelectionsData($bundle_selections_map);
		
			if ($product->getBundleOptionsData()) {
				$options = [];
				foreach ($product->getBundleOptionsData() as $key => $optionData) {
					if (!(bool)$optionData['delete']) {
						$option = $this->optionFactory->create(['data' => $optionData]);
						$option->setSku($product->getSku());
						$option->setOptionId(null);
		
						$links = [];
						$bundleLinks = $product->getBundleSelectionsData();
						if (!empty($bundleLinks[$key])) {
							foreach ($bundleLinks[$key] as $linkData) {
								if (!(bool)$linkData['delete']) {
									$link = $this->linkFactory->create(['data' => $linkData]);
									$linkProduct = $this->productRepository->getById($linkData['product_id']);
									$link->setSku($linkProduct->getSku());
									$link->setQty($linkData['selection_qty']);
									$link->setCanChangeQuantity($linkData['selection_can_change_qty']);
									$links[] = $link;
								}
							}
							$option->setProductLinks($links);
							$options[] = $option;
						}
					}
				}
				$extension = $product->getExtensionAttributes();
				$extension->setBundleProductOptions($options);
				$product->setExtensionAttributes($extension);
				$product->save(); //DO NOT USE SAVE MODEL
			}
			see --> https://magento.stackexchange.com/questions/151711/adding-bundle-product-programatically
			*/
		//THIS IS FOR BUNDLE PRODUCTS
		if($ProductSupperAttribute['bundle_options'] == "") {
			//throw new \Magento\Framework\Exception\LocalizedException(__('SKU: '.$ProcuctData['sku'].' ERROR column "bundle_options" is empty. If you are just updating other data simply remove the column'));
			$this->helper->sendLog($this->helper->rowCount,'bundle_options','The column is empty. If you are just updating other data simply remove the column');
		} else {
			if ($newProduct) {
				$optionscount=0;
				$items = array();
				//THIS IS FOR BUNDLE OPTIONS
				$commadelimiteddata = explode('|',$ProductSupperAttribute['bundle_options']);
				foreach ($commadelimiteddata as $data) {
					$configBundleOptionsCodes = $this->helper->userCSVDataAsArray($data);
					$titlebundleselection = ucfirst(str_replace('_',' ',$configBundleOptionsCodes[0]));
					$items[$optionscount]['title'] = $titlebundleselection;
					$items[$optionscount]['default_title'] = $titlebundleselection;
					if(isset($configBundleOptionsCodes[1])) {
						$items[$optionscount]['type'] = $configBundleOptionsCodes[1];
					} else {
						$this->helper->sendLog($this->helper->rowCount,'bundle_options','The format is incorrect no value found for "type"');
					}
					if(isset($configBundleOptionsCodes[2])) {
						$items[$optionscount]['required'] = $configBundleOptionsCodes[2];
					} else {
						$this->helper->sendLog($this->helper->rowCount,'bundle_options','The format is incorrect no value found for "required"');
					}
					if(isset($configBundleOptionsCodes[3])) {
						$items[$optionscount]['position'] = $configBundleOptionsCodes[3];
					} else {
						$this->helper->sendLog($this->helper->rowCount,'bundle_options','The format is incorrect no value found for "position"');
					}
					$items[$optionscount]['delete'] = 0;
					
					if ($items) {
						$productModel->setBundleOptionsData($items);
						#$productModel->save();
					}
					$optionscount+=1;
					
					$selections = array();
					$bundleConfigData = array();
					$optionscountselection=0;
					//THIS IS FOR BUNDLE SELECTIONS
					if($ProductSupperAttribute['bundle_selections'] !="") {
						$commadelimiteddataselections = explode('|',$ProductSupperAttribute['bundle_selections']);
						foreach ($commadelimiteddataselections as $selection) {
							$configBundleSelectionCodes = $this->helper->userCSVDataAsArray($selection);
							$selectionscount=0;
							foreach ($configBundleSelectionCodes as $selectionItem) {
								$bundleConfigData = explode(':',$selectionItem);
								if(isset($bundleConfigData[0])) {
									if($bundleSelection_product_id = $productModel->getIdBySku($bundleConfigData[0])) {
										$selections[$optionscountselection][$selectionscount]['product_id'] = $bundleSelection_product_id;
									} else {
										$this->helper->sendLog($this->helper->rowCount,'bundle_selection','The sku "'.$bundleConfigData[0].'" is not found and cannot be assoicated with the bundle');
									}
								} else {
								    $this->helper->sendLog($this->helper->rowCount,'bundle_selection','The format is incorrect no value found for "sku"');
								}
								if(isset($bundleConfigData[1])) {
									$selections[$optionscountselection][$selectionscount]['selection_price_type'] = $bundleConfigData[1];
								} else {
									$this->helper->sendLog($this->helper->rowCount,'bundle_selection','The format is incorrect no value found for "selection_price_type"');
	   							}
								if(isset($bundleConfigData[2])) {
				  					$selections[$optionscountselection][$selectionscount]['selection_price_value'] = $bundleConfigData[2];
								} else {
									$this->helper->sendLog($this->helper->rowCount,'bundle_selection','The format is incorrect no value found for "selection_price_value"');
	   							}
								if(isset($bundleConfigData[3])) {
									$selections[$optionscountselection][$selectionscount]['is_default'] = $bundleConfigData[3];
								} else {
									$this->helper->sendLog($this->helper->rowCount,'bundle_selection','The format is incorrect no value found for "is_default"');
	   							}
								if(isset($bundleConfigData[4]) && $bundleConfigData[4] != '') {
									$selections[$optionscountselection][$selectionscount]['selection_qty'] = $bundleConfigData[4];
									$selections[$optionscountselection][$selectionscount]['selection_can_change_qty'] = $bundleConfigData[5];
								} else {
									$selections[$optionscountselection][$selectionscount]['selection_qty'] = '';
									$this->helper->sendLog($this->helper->rowCount,'bundle_selection','The format is incorrect no value found for "selection_qty"');
								}
								if(isset($bundleConfigData[6]) && $bundleConfigData[6] != '') {
									$selections[$optionscountselection][$selectionscount]['position'] = $bundleConfigData[6];
								}
								$selections[$optionscountselection][$selectionscount]['delete'] = 0;
								$selectionscount+=1;
							}
							$optionscountselection+=1;
						}
						if ($selections) {
							$productModel->setBundleSelectionsData($selections);
						}
					} else {
						$this->helper->sendLog($this->helper->rowCount,'bundle_selection','The column is empty. If you are just updating other data simply disregard');
	   				}
				}
				

				if ($productModel->getPriceType() == '0') {
					$productModel->setCanSaveCustomOptions(true);
					if ($customOptions = $productModel->getProductOptions()) {
						foreach ($customOptions as $key => $customOption) {
							$customOptions[$key]['is_delete'] = 1;
						}
						$productModel->setProductOptions($customOptions);
					}
				}
			
				if ($productModel->getBundleOptionsData()) {
					$options = [];
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
					$productRepository = $objectManager->create('\Magento\Catalog\Api\ProductRepositoryInterface');
					foreach ($productModel->getBundleOptionsData() as $key => $optionData) {
						if (!(bool)$optionData['delete']) {
							$option = $objectManager->create('\Magento\Bundle\Api\Data\OptionInterfaceFactory')
								->create(['data' => $optionData]);
							$option->setSku($productModel->getSku());
							$option->setOptionId(null);
							$links = [];
							$bundleLinks = $productModel->getBundleSelectionsData();
							if (!empty($bundleLinks[$key])) {
								foreach ($bundleLinks[$key] as $linkData) {
									if (!(bool)$linkData['delete']) {
										/** @var \Magento\Bundle\Api\Data\LinkInterface$link */
										if(isset($linkData['product_id'])) {
											$link = $objectManager->create('\Magento\Bundle\Api\Data\LinkInterfaceFactory')
												->create(['data' => $linkData]);
											$linkProduct = $productRepository->getById($linkData['product_id']);
											$link->setSku($linkProduct->getSku());
											$link->setQty($linkData['selection_qty']);
											if (isset($linkData['selection_can_change_qty'])) {
												$link->setCanChangeQuantity($linkData['selection_can_change_qty']);
											}
											$links[] = $link;
										}
									}
								}
								$option->setProductLinks($links);
								$options[] = $option;
							}
						}
					}
					$extension = $productModel->getExtensionAttributes();
					$extension->setBundleProductOptions($options);
					$productModel->setExtensionAttributes($extension);
				}
				#$productModel->setCanSaveBundleSelections();
			} else {
			
				$optionscount=0;
				$items = array();
				//THIS IS FOR BUNDLE OPTIONS
				$commadelimiteddata = explode('|',$ProductSupperAttribute['bundle_options']);
				foreach ($commadelimiteddata as $data) {
					$configBundleOptionsCodes = $this->helper->userCSVDataAsArray($data);
					$titlebundleselection = ucfirst(str_replace('_',' ',$configBundleOptionsCodes[0]));
					$items[$optionscount]['title'] = $titlebundleselection;
					$items[$optionscount]['default_title'] = $titlebundleselection;
					if(isset($configBundleOptionsCodes[1])) {
						$items[$optionscount]['type'] = $configBundleOptionsCodes[1];
					} else {
						$this->helper->sendLog($this->helper->rowCount,'bundle_options','The format is incorrect no value found for "type"');
					}
					if(isset($configBundleOptionsCodes[2])) {
						$items[$optionscount]['required'] = $configBundleOptionsCodes[2];
					} else {
						$this->helper->sendLog($this->helper->rowCount,'bundle_options','The format is incorrect no value found for "required"');
					}
					if(isset($configBundleOptionsCodes[3])) {
						$items[$optionscount]['position'] = $configBundleOptionsCodes[3];
					} else {
						$this->helper->sendLog($this->helper->rowCount,'bundle_options','The format is incorrect no value found for "position"');
					}
					$items[$optionscount]['delete'] = 0;
					
					$options_id = "";
					$product2 = $this->Product->loadByAttribute('sku', $ProcuctData['sku']);
					#$options_id = $product2->getOptionId();
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
				    #$catalog_product_bundle_option_value = $_resource->getTableName('catalog_product_bundle_option_value');
					$_bundleOption = $objectManager->create('\Magento\Bundle\Model\Option');
				    $optionModel = $_bundleOption->getResourceCollection()->setProductIdFilter($product2->getId());
					$_resource = $this->_resource;
					$connection = $_resource->getConnection();
					$catalog_product_bundle_option_value = $_resource->getTableName('catalog_product_bundle_option_value');
					
					foreach($optionModel as $eachOption) {
						
						$selectOptionID = "SELECT title FROM ".$catalog_product_bundle_option_value." WHERE option_id = ".$eachOption->getData('option_id')."";
						$Optiondatarows = $connection->query($selectOptionID);
						while ($Option_row = $Optiondatarows->fetch()) {
							$finaltitle = $Option_row['title'];
						}
						if($titlebundleselection == $finaltitle) {
			 				#throw new \Magento\Framework\Exception\LocalizedException(__('MATCH OPTION ID: ' .$eachOption->getData('option_id')));
							$options_id = $eachOption->getData('option_id');
							$items[$optionscount]['option_id'] = $eachOption->getData('option_id');
						}
					}
					
					if ($items) {
						$productModel->setBundleOptionsData($items);
						#$productModel->save();
					} 
					
					$optionscount+=1;
					$selections = array();
					$bundleConfigData = array();
					$optionscountselection=0;
					//THIS IS FOR BUNDLE SELECTIONS
					if($ProductSupperAttribute['bundle_selections'] !="") {
						$commadelimiteddataselections = explode('|',$ProductSupperAttribute['bundle_selections']);
						foreach ($commadelimiteddataselections as $selection) {
							$configBundleSelectionCodes = $this->helper->userCSVDataAsArray($selection);
							$selectionscount=0;
							foreach ($configBundleSelectionCodes as $selectionItem) {
								$bundleConfigData = explode(':',$selectionItem);
								if($options_id !="") { $selections[$optionscountselection][$selectionscount]['option_id'] = $options_id; }
								
								if(isset($bundleConfigData[0])) {
									if($bundleSelection_product_id = $productModel->getIdBySku($bundleConfigData[0])) {
										$selections[$optionscountselection][$selectionscount]['product_id'] = $bundleSelection_product_id;
									} else {
										$this->helper->sendLog($this->helper->rowCount,'bundle_selection','The sku "'.$bundleConfigData[0].'" is not found and cannot be assoicated with the bundle');
									}
								} else {
								    $this->helper->sendLog($this->helper->rowCount,'bundle_selection','The format is incorrect no value found for "sku"');
								}
								if(isset($bundleConfigData[1])) {
									$selections[$optionscountselection][$selectionscount]['selection_price_type'] = $bundleConfigData[1];
								} else {
									$this->helper->sendLog($this->helper->rowCount,'bundle_selection','The format is incorrect no value found for "selection_price_type"');
	   							}
								if(isset($bundleConfigData[2])) {
				  					$selections[$optionscountselection][$selectionscount]['selection_price_value'] = $bundleConfigData[2];
								} else {
									$this->helper->sendLog($this->helper->rowCount,'bundle_selection','The format is incorrect no value found for "selection_price_value"');
	   							}
								if(isset($bundleConfigData[3])) {
									$selections[$optionscountselection][$selectionscount]['is_default'] = $bundleConfigData[3];
								} else {
									$this->helper->sendLog($this->helper->rowCount,'bundle_selection','The format is incorrect no value found for "is_default"');
	   							}
								if(isset($bundleConfigData[4]) && $bundleConfigData[4] != '') {
									$selections[$optionscountselection][$selectionscount]['selection_qty'] = $bundleConfigData[4];
									$selections[$optionscountselection][$selectionscount]['selection_can_change_qty'] = $bundleConfigData[5];
								} else {
									$selections[$optionscountselection][$selectionscount]['selection_qty'] = '';
									$this->helper->sendLog($this->helper->rowCount,'bundle_selection','The format is incorrect no value found for "selection_qty"');
								}
								if(isset($bundleConfigData[6]) && $bundleConfigData[6] != '') {
									$selections[$optionscountselection][$selectionscount]['position'] = $bundleConfigData[6];
								}
								$selections[$optionscountselection][$selectionscount]['delete'] = 0;
								$selectionscount+=1;
							}
							$optionscountselection+=1;
						}
						if ($selections) {
							$productModel->setBundleSelectionsData($selections);
						}
					} else {
						$this->helper->sendLog($this->helper->rowCount,'bundle_selection','The column is empty. If you are just updating other data simply disregard');
	   				}
				}
				

				if ($productModel->getPriceType() == '0') {
					$productModel->setCanSaveCustomOptions(true);
					if ($customOptions = $productModel->getProductOptions()) {
						foreach ($customOptions as $key => $customOption) {
							$customOptions[$key]['is_delete'] = 1;
						}
						$productModel->setProductOptions($customOptions);
					}
				}
				if ($productModel->getBundleOptionsData()) {
					
					
					$options = [];
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
					$productRepository = $objectManager->create('\Magento\Catalog\Api\ProductRepositoryInterface');
					foreach ($productModel->getBundleOptionsData() as $key => $optionData) {
						if (!(bool)$optionData['delete']) {
							$option = $objectManager->create('\Magento\Bundle\Api\Data\OptionInterfaceFactory')
								->create(['data' => $optionData]);
							$option->setSku($productModel->getSku());
							$option->setOptionId(null);
							$links = [];
							$bundleLinks = $productModel->getBundleSelectionsData();
							
							if (!empty($bundleLinks[$key])) {
								foreach ($bundleLinks[$key] as $linkData) {
									if (!(bool)$linkData['delete']) {
										/** @var \Magento\Bundle\Api\Data\LinkInterface$link */
										if(isset($linkData['product_id'])) {
											$link = $objectManager->create('\Magento\Bundle\Api\Data\LinkInterfaceFactory')
												->create(['data' => $linkData]);
											$linkProduct = $productRepository->getById($linkData['product_id']);
											$link->setSku($linkProduct->getSku());
											$link->setQty($linkData['selection_qty']);
											if (isset($linkData['selection_can_change_qty'])) {
												$link->setCanChangeQuantity($linkData['selection_can_change_qty']);
											}
											$links[] = $link;
										}
									}
								}
								$option->setProductLinks($links);
								$options[] = $option;
							}
						}
					}
					$extension = $productModel->getExtensionAttributes();
					$extension->setBundleProductOptions($options);
					$productModel->setExtensionAttributes($extension);
				}
			}
		}
		
		try {
			$productModel->setIsMassupdate(true);
			//set custom options from csv
			if(!empty($ProductCustomOption)) {
				$productModel->setCanSaveCustomOptions(true);
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
				foreach ($ProductCustomOption as $arrayOption) {
						$option = $objectManager->create('\Magento\Catalog\Model\Product\Option')
												->setProductId($productModel->getId())
												->setStoreId($ProcuctData['store_id'])
												->addData($arrayOption);
						$productModel->addOption($option);//$option->save(); //removed this in 2.2.3
				}
				$productModel->setHasOptions(true); //added this in 2.2.3
				#$productModel->save();
			}
			$this->productResourceModel->save($productModel);
			/* For get Version Number */
			#$productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
			#$version = $productMetadata->getVersion();
			#if($version < '2.1.0'){ };
		}
		catch (\Exception $e) { 
			if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
				$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "ERROR: " . $e->getMessage());
				$this->helper->writeToCsv($cronLogErrors);	
			} else {
				throw new \Magento\Framework\Exception\LocalizedException(__('SKU: '.$ProcuctData['sku'].' ERROR : '. $e->getMessage()));
			}
		}
		
		} //CHECK FOR IF STORE VIEW OR NOT
	  }//END UPDATE ONLY CHECK
	  return $this->helper->msgtoreturn;
	}
	
}