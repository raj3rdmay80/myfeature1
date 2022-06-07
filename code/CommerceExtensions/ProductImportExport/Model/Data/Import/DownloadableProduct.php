<?php

/**
 * Copyright © 2018 CommerceExtensions. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CommerceExtensions\ProductImportExport\Model\Data\Import;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product;
use Magento\Downloadable\Model\Product\Type;
use Magento\Framework\App\ResourceConnection;

/**
 *  CSV Import Handler Bundle Product
 */

class DownloadableProduct{

	protected $_filesystem;
		
	protected $ProductFactory;

    public function __construct(
		ResourceConnection $resource,
		\Magento\Catalog\Model\ProductFactory $ProductFactory,
		\Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $ProductLinkInterfaceFactory,
		\Magento\Catalog\Api\ProductRepositoryInterface $ProductRepositoryInterface,
		\Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterfaceFactory $ProductAttributeMediaGalleryEntryInterfaceFactory,
		Filesystem $filesystem,
		\Magento\Catalog\Model\Product $Product,
		\Magento\Downloadable\Model\Product\Type $DownloadableProductType,
		\CommerceExtensions\ProductImportExport\Helper\Data $helper
    ) {
         // prevent admin store from loading
		 $this->_resource = $resource;
		 $this->ProductFactory = $ProductFactory;
		 $this->ProductLinkInterfaceFactory = $ProductLinkInterfaceFactory;
		 $this->ProductRepositoryInterface = $ProductRepositoryInterface;
		 $this->ProductAttributeMediaGalleryEntryInterfaceFactory = $ProductAttributeMediaGalleryEntryInterfaceFactory;
		 $this->_filesystem = $filesystem;
		 $this->Product = $Product;
		 $this->DownloadableProductType = $DownloadableProductType;
         $this->helper = $helper;

    }
	
	public function DownloadableProductData($rowCount,$productID,$newProduct,$SetProductData,$params,$ProcuctData,$ProductAttributeData,$ProductImageGallery,$ProductStockdata,$ProductSupperAttribute){
	
	$this->helper->rowCount = $rowCount;
	
	//UPDATE PRODUCT ONLY [START]
	$allowUpdateOnly = false;
	if(!$SetProductData || empty($SetProductData)) {
		$SetProductData = $this->ProductFactory->create();
	}
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
		$SetProductData->setData($ProductAttributeData);
		
		/*		
		$SetProductData->setSku($ProcuctData['sku']);
		$SetProductData->setStoreId($ProcuctData['store_id']);
		if(isset($ProcuctData['websites'])) { $SetProductData->setWebsiteIds($ProcuctData['websites']); }
		if(isset($ProcuctData['attribute_set'])) { $SetProductData->setAttributeSetId($ProcuctData['attribute_set']); }
		if(isset($ProcuctData['prodtype'])) { $SetProductData->setTypeId($ProcuctData['prodtype']); }
		if(isset($ProcuctData['category_ids'])) { 
			if($ProcuctData['category_ids'] == "remove") { 
				$SetProductData->setCategoryIds(array()); 
			} else if($ProcuctData['category_ids'] != "") { 
				$SetProductData->setCategoryIds($ProcuctData['category_ids']);
			}
		}
		$SetProductData->addData($ProductAttributeData);
		*/
		
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
						$entries =  [];
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
						$SetProductData->setImage($ImageFile);
						#$this->helper->sendLog($this->helper->rowCount, '', 'setImage()' . $ImageFile);
						if(isset($ProductImageGallery['image_label'])) {
							$mediaEntry->setLabel($ProductImageGallery['image_label']);
						}
					}
					if(in_array("small_image", $imageColumns)) {
						$SetProductData->setSmallImage($ImageFile);
						#$this->helper->sendLog($this->helper->rowCount, '', 'setSmallImage()' . $ImageFile);
						if(isset($ProductImageGallery['small_image_label'])) {
							$mediaEntry->setLabel($ProductImageGallery['small_image_label']);
						}
					}
					if(in_array("thumbnail", $imageColumns)) {
						$SetProductData->setThumbnail($ImageFile);
						#$this->helper->sendLog($this->helper->rowCount, '', 'setThumbnail()' . $ImageFile);
						if(isset($ProductImageGallery['thumbnail_label'])) {
							$mediaEntry->setLabel($ProductImageGallery['thumbnail_label']);
						}
					}
					if(in_array("swatch_image", $imageColumns)) {
						$SetProductData->setSwatchImage($ImageFile);
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
					$SetProductData->setStoreId($SetProductData->getStoreId());
					$SetProductData->setMediaGalleryEntries($entries);	
					$SetProductData->save();	
					#$productModel->setMediaGalleryEntries($entries);
					#$productModel->setData($mediaAttribute, 'no_selection');
					#$productRepository->save($product);									
				} else {
					foreach( $imageColumns as $mediaAttribute ) {
						$SetProductData->setData($mediaAttribute, 'no_selection');
					}
				}
			}
		}
			
		
		if($ProductStockdata!=""){ $SetProductData->setStockData($ProductStockdata); }
				
		//THIS IS FOR DOWNLOADABLE PRODUCTS
		#$SetProductData->setLinksTitle("Download");
		#$SetProductData->setSamplesTitle("Samples");
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$FilePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('import');
		$containsDownloadableinfo = false;
		
		if ($ProductSupperAttribute['downloadable_options'] != "") {
		
			//THIS IS FOR DOWNLOADABLE OPTIONS
			$linkFactory = $objectManager->create('\Magento\Downloadable\Api\Data\LinkInterfaceFactory');
			$commadelimiteddata = explode('|',$ProductSupperAttribute['downloadable_options']);
			$links = [];
			$containsDownloadableinfo = true;
			$extension = $SetProductData->getExtensionAttributes();
			$connection = $this->_resource->getConnection();
			$_downloadable_link = $this->_resource->getTableName('downloadable_link');
								
			foreach ($commadelimiteddata as $data) {
				$configBundleOptionsCodes = $this->helper->userCSVDataAsArray($data);	
				
				if(!isset($configBundleOptionsCodes[0])) {
					$this->helper->sendLog($this->helper->rowCount,'downloadable_options','The format is incorrect no value found for "title"');
				}
				if(!isset($configBundleOptionsCodes[1])) {
					$this->helper->sendLog($this->helper->rowCount,'downloadable_options','The format is incorrect no value found for "price"');
				}
				if(!isset($configBundleOptionsCodes[2])) {
					$this->helper->sendLog($this->helper->rowCount,'downloadable_options','The format is incorrect no value found for "number_of_downloads"');
				}
				if(!isset($configBundleOptionsCodes[3])) {
					$this->helper->sendLog($this->helper->rowCount,'downloadable_options','The format is incorrect no value found for "type"');
				}
				if(!isset($configBundleOptionsCodes[4])) {
					$this->helper->sendLog($this->helper->rowCount,'downloadable_options','The format is incorrect no value found for "file"');
				}
				if(!isset($configBundleOptionsCodes[7])) {
					//$this->helper->sendLog($this->helper->rowCount,'downloadable_options','The format is incorrect no value found for "sort_order". Using zero instead');
					$configBundleOptionsCodes[7] = 0;
				}
				if(isset($configBundleOptionsCodes[4])) {
					
					//first delete all links then we update
					if(!$newProduct) {
						/*
						$ProductId = $this->Product->getResource()->getIdBySku($SetProductData->getSku());
						$productDownloadable = $this->Product->load($ProductId);
						if ($productDownloadable->getTypeInstance()->hasLinks($productDownloadable)) {
							$_links=$productDownloadable->getTypeInstance()->getLinks($productDownloadable);
							
							foreach ($_links as $_link) {
								#$_link->delete();
							}
						}
						*/
						//went with this because the above wasn't removing and i was still getting the error on update "There is no downloadable link with provided ID"
						$select_qry_set_id = $connection->query("SELECT link_id FROM ".$_downloadable_link." WHERE product_id ='".$SetProductData->getId()."' AND link_type ='".$configBundleOptionsCodes[3]."'");
						$rowSetId = $select_qry_set_id->fetch();
						$existing_link_id = $rowSetId['link_id'];
						#$connection->query("DELETE FROM ".$_downloadable_link." WHERE product_id = ".$SetProductData->getId()."");
						#$existing_link_id = null;
					} else {
						$existing_link_id = null;
					}
					$linkData = [
						'product_id' => $SetProductData->getId(),
						'sort_order' => ($configBundleOptionsCodes[7]>=0) ? $configBundleOptionsCodes[7] : '0',
						'title' => $configBundleOptionsCodes[0],
						'sample' => [
							'type' => ($configBundleOptionsCodes[5]=="file") ? \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE : \Magento\Downloadable\Helper\Download::LINK_TYPE_URL,
							'url' => ($configBundleOptionsCodes[5]=="url") ? $configBundleOptionsCodes[5] : null,
						],
						'type' => ($configBundleOptionsCodes[3]=="file") ? \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE : \Magento\Downloadable\Helper\Download::LINK_TYPE_URL,
						'is_shareable' => \Magento\Downloadable\Model\Link::LINK_SHAREABLE_CONFIG,
						'price' => $configBundleOptionsCodes[1],
						'number_of_downloads' => $configBundleOptionsCodes[2],
						'link_url' => ($configBundleOptionsCodes[3]=="url") ? $configBundleOptionsCodes[4] : null,
						'link_id' => 0,
					];
				
					$link = $linkFactory->create(['data' => $linkData]);
					$link->setId($existing_link_id);
						
					/**
					 * @var \Magento\Downloadable\Api\Data\File\ContentInterface $content
					 */
					if($configBundleOptionsCodes[3]=="file") {  
						if(file_exists($FilePath.$configBundleOptionsCodes[4])) {
							$content = $objectManager->create('\Magento\Downloadable\Api\Data\File\ContentInterfaceFactory')->create();
							$content->setFileData(base64_encode(file_get_contents($FilePath.$configBundleOptionsCodes[4])));
							$content->setName(str_replace("/","",$configBundleOptionsCodes[4]));
							$link->setLinkFileContent($content);
							
							if(isset($configBundleOptionsCodes[6])) {
								/**
								 * @var \Magento\Downloadable\Api\Data\File\ContentInterface $sampleContent
								 */
								if($configBundleOptionsCodes[6] !="") {
									if(file_exists($FilePath.$configBundleOptionsCodes[6])) {  
										if(isset($configBundleOptionsCodes[5])) {
											$link->setSampleType($configBundleOptionsCodes[5]);
										} else {
											$link->setSampleType($linkData['sample']['type']);
										}
										$sampleContent = $objectManager->create('\Magento\Downloadable\Api\Data\File\ContentInterfaceFactory')->create();
										$sampleContent->setFileData(base64_encode(file_get_contents($FilePath.$configBundleOptionsCodes[6])));
										$sampleContent->setName(str_replace("/","",$configBundleOptionsCodes[6]));
										$link->setSampleFileContent($sampleContent);
									} else {
										//throw new \Magento\Framework\Exception\LocalizedException(__('Downloadable Product ['.$ProcuctData['sku'].'] in column "downloadable_options" is missing file in: ' .$FilePath.$configBundleOptionsCodes[6]));
										$this->helper->sendLog($this->helper->rowCount,'downloadable_options','Downloadable Product ['.$ProcuctData['sku'].'] in column "downloadable_options" is missing file in: ' .$FilePath.$configBundleOptionsCodes[6].'');
									}
								}
							}
						} else {
							//throw new \Magento\Framework\Exception\LocalizedException(__('Downloadable Product ['.$ProcuctData['sku'].'] in column "downloadable_options" is missing file in: ' .$FilePath.$configBundleOptionsCodes[4]));
							$this->helper->sendLog($this->helper->rowCount,'downloadable_options','Downloadable Product ['.$ProcuctData['sku'].'] in column "downloadable_options" is missing file in: ' .$FilePath.$configBundleOptionsCodes[4].'');
						}
					}
					$link->setSampleUrl($linkData['sample']['url']);
					$link->setLinkType($linkData['type']);
					$link->setStoreId($SetProductData->getStoreId());
					$link->setWebsiteId($SetProductData->getStore()->getWebsiteId());
					$link->setProductWebsiteIds($SetProductData->getWebsiteIds());
					if (!$link->getSortOrder()) {
						$link->setSortOrder(1);
					}
					if (null === $link->getPrice()) {
						$link->setPrice(0);
					}
					if ($link->getIsUnlimited()) {
						$link->setNumberOfDownloads(0);
					}
					$links[] = $link;			
					
					$extension->setDownloadableProductLinks($links);
				} else {
					$this->helper->sendLog($this->helper->rowCount,'downloadable_options','The format is incorrect/incomplete or empty for the column format is e.g title,price,numberofdownloads,type,link_url,(optional)sort_order,(optional)sample_link_url,');
				}
			}
		} else {
			$this->helper->sendLog($this->helper->rowCount,'downloadable_options','The column is empty. If you are just updating other data just disregard');
		}
		
		if ($ProductSupperAttribute['downloadable_sample_options'] != "") {
		
			$sampleFactory = $objectManager->create('\Magento\Downloadable\Api\Data\SampleInterfaceFactory');
			$samplecommadelimiteddata = explode('|',$ProductSupperAttribute['downloadable_sample_options']);
			$samples = [];
			$containsDownloadableinfo = true;
			
			foreach ($samplecommadelimiteddata as $sample_data) {
				$downloadable_sample_options_data = $this->helper->userCSVDataAsArray($sample_data);
				if(!isset($downloadable_sample_options_data[0])) {
					$this->helper->sendLog($this->helper->rowCount,'downloadable_sample_options','The format is incorrect no value found for "title"');
				}
				if(!isset($downloadable_sample_options_data[1])) {
					$this->helper->sendLog($this->helper->rowCount,'downloadable_sample_options','The format is incorrect no value found for "type"');
				}
				if(!isset($downloadable_sample_options_data[2])) {
					$this->helper->sendLog($this->helper->rowCount,'downloadable_sample_options','The format is incorrect no value found for "file"');
				}
				if($downloadable_sample_options_data[1]=="file") {
					$downloadableData = [
						'sample' => [
							[
								'is_delete' => 0,
								'sample_id' => 0,
								'title' => $downloadable_sample_options_data[0],
								'type' => \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE,
								'file' => json_encode(
									[
										[
											'file' => $downloadable_sample_options_data[2],
											'name' => $downloadable_sample_options_data[0],
											'size' => 1024,
											'status' => 0,
										],
									]
								),
								'sample_url' => null,
								'sort_order' => '0',
							],
						],
					];
				} else {
					$downloadableData = [
					'sample' => [
							[
								'is_delete' => 0,
								'sample_id' => 0,
								'title' => $downloadable_sample_options_data[0],
								'type' => \Magento\Downloadable\Helper\Download::LINK_TYPE_URL,
								'file' => null,
								'sample_url' => $downloadable_sample_options_data[2],
								'sort_order' => '0',
							],
						],
					];
				}
				if (isset($downloadableData['sample']) && is_array($downloadableData['sample'])) {
					foreach ($downloadableData['sample'] as $sampleData) {
						if (!$sampleData || (isset($sampleData['is_delete']) && (bool)$sampleData['is_delete'])) {
							continue;
						} else {
							unset($sampleData['sample_id']);
							/**
							 * @var \Magento\Downloadable\Api\Data\SampleInterface $sample
							 */
							$sample = $sampleFactory->create(['data' => $sampleData]);
							$sample->setId(null);
							$sample->setStoreId($SetProductData->getStoreId());
							$sample->setSampleType($sampleData['type']);
							$sample->setSampleUrl($sampleData['sample_url']);
							/**
							 * @var \Magento\Downloadable\Api\Data\File\ContentInterface $content
							 */
							if($downloadable_sample_options_data[1]=="file") {
								if(file_exists($FilePath.$downloadable_sample_options_data[2])) {  
									$content = $objectManager->create('\Magento\Downloadable\Api\Data\File\ContentInterfaceFactory')->create();
									$content->setFileData(base64_encode(file_get_contents($FilePath.$downloadable_sample_options_data[2])));
									$content->setName(str_replace("/","",$downloadable_sample_options_data[2]));
									$sample->setSampleFileContent($content);
								} else {
									if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
										$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "Downloadable Product [".$ProcuctData['sku']."] in column 'downloadable_sample_options' is missing file in: " . $FilePath.$downloadable_sample_options_data[2]);
										$this->helper->writeToCsv($cronLogErrors);	
									} else {
										throw new \Magento\Framework\Exception\LocalizedException(__('Downloadable Product ['.$ProcuctData['sku'].'] in column "downloadable_sample_options" is missing file in: ' .$FilePath.$downloadable_sample_options_data[2]));
									}
								}
							}
							$sample->setSortOrder($sampleData['sort_order']);
							$samples[] = $sample;
						}
					}
					$extension->setDownloadableProductSamples($samples);
				}
			}
		} else {
			$this->helper->sendLog($this->helper->rowCount,'downloadable_sample_options','The column is empty. If you are just updating other data just disregard');
		}
		if($containsDownloadableinfo) {
			$SetProductData->setExtensionAttributes($extension);
			#$SetProductData->setLinksPurchasedSeparately(true);
			if ($SetProductData->getLinksPurchasedSeparately()) {
				$SetProductData->setTypeHasRequiredOptions(true)->setRequiredOptions(true);
			} else {
				$SetProductData->setTypeHasRequiredOptions(false)->setRequiredOptions(false);
			}
			//THIS IS FOR DOWNLOADABLE PRODUCTS
		}
		
		$relatedProductData = array();
		$upSellProductData = array();
		$crossSellProductData = array();
		
		if($ProductSupperAttribute['related']!=""){ $relatedProductData = $this->helper->AppendRelatedProduct($ProductSupperAttribute['related'] ,$ProcuctData['sku']); }
		if($ProductSupperAttribute['upsell']!=""){ $upSellProductData = $this->helper->AppendUpsellProduct($ProductSupperAttribute['upsell'] ,$ProcuctData['sku']); }
		if($ProductSupperAttribute['crosssell']!=""){ $crossSellProductData = $this->helper->AppendCrossSellProduct($ProductSupperAttribute['crosssell'] , $ProcuctData['sku']); }
		
		if(!empty($relatedProductData) || !empty($upSellProductData) || !empty($crossSellProductData)) {
			$allProductLinks = array_merge($relatedProductData, $upSellProductData, $crossSellProductData);
			$SetProductData->setProductLinks($allProductLinks);
		}
		
		try {
			$SetProductData->save(); 
		}
		catch (\Exception $e) { 
			if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
				$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "ERROR: " . $e->getMessage());
				$this->helper->writeToCsv($cronLogErrors);	
			} else {
				throw new \Magento\Framework\Exception\LocalizedException(__('SKU: '.$ProcuctData['sku'].' ERROR : '. $e->getMessage()));
			}
		}
		
		if(isset($ProductSupperAttribute['tier_prices'])) { 
			if($ProductSupperAttribute['tier_prices']!=""){ $SetProductData->setTierPrice($ProductSupperAttribute['tier_prices'])->save(); }
		}
		$SetProductData->reset(); 
		
	  }//END UPDATE ONLY CHECK
	  return $this->helper->msgtoreturn;
	}
}