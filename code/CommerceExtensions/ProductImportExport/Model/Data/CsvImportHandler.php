<?php
/**
 * Copyright © 2017 CommerceExtensions. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CommerceExtensions\ProductImportExport\Model\Data;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Store\Model\Website;

class CsvImportHandler
{
    const MULTI_DELIMITER = ' , ';

    protected $_resource;

    protected $_filesystem;

    protected $date;

    protected $csvProcessor;

    protected $_eavConfig;

    protected $objectManager;

    protected $Product;

    protected $_imageFields     = ['image', 'swatch_image', 'small_image', 'thumbnail', 'media_gallery', 'gallery', 'gallery_label', 'image_label', 'small_image_label' , 'thumbnail_label'];

    protected $_requiredFields  = ['sku', 'store', 'prodtype'];

    protected $_stockDataFields = ['manage_stock',
                                   'use_config_manage_stock',
                                   'qty',
                                   'min_qty',
                                   'use_config_min_qty',
                                   'min_sale_qty',
                                   'use_config_min_sale_qty',
                                   'max_sale_qty',
                                   'use_config_max_sale_qty',
                                   'is_qty_decimal',
                                   'backorders',
                                   'use_config_backorders',
                                   'notify_stock_qty',
                                   'use_config_notify_stock_qty',
                                   'enable_qty_increments',
                                   'use_config_enable_qty_inc',
                                   'qty_increments',
                                   'use_config_qty_increments',
                                   'is_in_stock',
                                   'low_stock_date',
                                   'stock_status_changed_auto'];

    protected $_categoryCache   = [];

    protected $directoryList;

    protected $file;

    /**
     * @var array
     */
	 
    private $optionValues = [];

    public function __construct(
        ResourceConnection $resource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        Filesystem $filesystem,
        \Magento\Framework\File\Csv $csvProcessor,
		\Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \CommerceExtensions\ProductImportExport\Model\Data\Import\SimpleProduct $SimpleProduct,
        \CommerceExtensions\ProductImportExport\Model\Data\Import\BundleProduct $BundleProduct,
        \CommerceExtensions\ProductImportExport\Model\Data\Import\VirtualProduct $VirtualProduct,
        \CommerceExtensions\ProductImportExport\Model\Data\Import\ConfigurableProduct $ConfigurableProduct,
        \CommerceExtensions\ProductImportExport\Model\Data\Import\GroupedProduct $GroupedProduct,
        \CommerceExtensions\ProductImportExport\Model\Data\Import\DownloadableProduct $DownloadableProduct,
        \Magento\Catalog\Api\ProductTierPriceManagementInterface $ProductTierPriceManagementInterface,
        \Magento\Catalog\Model\Product $Product,
        \Magento\Store\Model\Website $Website,
        \CommerceExtensions\ProductImportExport\Helper\Data $helper,
        \Magento\Eav\Model\Config $eavConfig,
        DirectoryList $directoryList,
        File $file,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionFactory,
        \Magento\Catalog\Api\ProductAttributeOptionManagementInterface $optionManager
    ) {
        // prevent admin store from loading
        $this->_resource            = $resource;
        $this->scopeConfig          = $scopeConfig;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_filesystem          = $filesystem;
        $this->csvProcessor         = $csvProcessor;
        $this->moduleManager        = $moduleManager;
        $this->localeFormat         = $localeFormat;
        $this->_objectManager       = $objectManager;
        $this->SimpleProduct        = $SimpleProduct;
        $this->BundleProduct        = $BundleProduct;
        $this->VirtualProduct       = $VirtualProduct;
        $this->ConfigurableProduct  = $ConfigurableProduct;
        $this->GroupedProduct       = $GroupedProduct;
        $this->DownloadableProduct  = $DownloadableProduct;
        $this->tierPrice            = $ProductTierPriceManagementInterface;
        $this->_ProductModel        = $Product;
        $this->website              = $Website;
        $this->helper               = $helper;
        $this->_eavConfig           = $eavConfig;
        $this->directoryList        = $directoryList;
        $this->file                 = $file;
        $this->optionFactory        = $optionFactory;
        $this->optionManager        = $optionManager;
    }

    public function requiredDataForSaveProduct($rowCount, $product, $params)
    {

        $ProductStockdata     = [];
        $ProductAttributeData = [];
        $custom_options       = [];
		
		if (isset($params['enable_default_magento_format'])) {
			if (strtolower($params['enable_default_magento_format']) === "true") {
				if (!isset($product['product_type'])) {
					if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
						$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "ERROR: The REQUIRED FIELD 'column 'product_type' is not found. ' is required and was NOT FOUND");
						$this->helper->writeToCsv($cronLogErrors);	
					} else {
						throw new \Magento\Framework\Exception\LocalizedException(__('ERROR: The REQUIRED FIELD "product_type" is required and was NOT FOUND'));
					}
				}
				if (!isset($product['product_websites'])) {
					if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
						$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "ERROR: The REQUIRED FIELD 'column 'product_websites' is not found. ' is required and was NOT FOUND");
						$this->helper->writeToCsv($cronLogErrors);	
					} else {
						throw new \Magento\Framework\Exception\LocalizedException(__('ERROR: The REQUIRED FIELD "product_websites" is required and was NOT FOUND'));
					}
				}
				if (!isset($product['base_image'])) {
					if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
						$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "ERROR: The REQUIRED FIELD 'column 'base_image' is not found. ' is required and was NOT FOUND");
						$this->helper->writeToCsv($cronLogErrors);	
					} else {
						//throw new \Magento\Framework\Exception\LocalizedException(__('ERROR: The REQUIRED FIELD is required and was NOT FOUND'));
						$this->helper->sendLog($this->helper->rowCount,'base_image','The field "base_image" has is empty or has no value.');
					}
				}
				if (!isset($product['thumbnail_image'])) {
					if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
						$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "ERROR: The REQUIRED FIELD 'column 'thumbnail_image' is not found. ' is required and was NOT FOUND");
						$this->helper->writeToCsv($cronLogErrors);	
					} else {
						//throw new \Magento\Framework\Exception\LocalizedException(__('ERROR: The REQUIRED FIELD "thumbnail_image" is required and was NOT FOUND'));
						$this->helper->sendLog($this->helper->rowCount,'thumbnail_image','The field "thumbnail_image" has is empty or has no value.');
					}
				}
				if (!isset($product['tax_class_name'])) {
					if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
						$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "ERROR: The REQUIRED FIELD 'column 'tax_class_name' is not found. ' is required and was NOT FOUND");
						$this->helper->writeToCsv($cronLogErrors);	
					} else {
						throw new \Magento\Framework\Exception\LocalizedException(__('ERROR: The REQUIRED FIELD "tax_class_name" is required and was NOT FOUND'));
					}
				}
				$product['store'] = $product['store_view_code'];
				$product['prodtype'] = $product['product_type'];
				$product['websites'] = $product['product_websites'];
				$product['tax_class_id'] = $product['tax_class_name'];
				if(isset($product['display_product_options_in'])) {
					$product['options_container'] = $product['display_product_options_in'];
					unset($product['display_product_options_in']);
				}
				if(isset($product['base_image'])) {
					$product['image'] = $product['base_image'];
					unset($product['base_image']);
				}
				if(isset($product['thumbnail_image'])) {
					$product['thumbnail'] = $product['thumbnail_image'];
					unset($product['thumbnail_image']);
				}
				unset($product['store_view_code']);
				unset($product['product_type']);
				unset($product['product_websites']);
				unset($product['tax_class_name']);
				unset($product['display_product_options_in']);
				if(isset($product['attribute_set_id']) && !isset($product['attribute_set_code'])) {
					$product['attribute_set'] = $product['attribute_set_id'];
					unset($product['attribute_set_id']);
				}
				if(isset($product['attribute_set_code'])) {
					$product['attribute_set'] = $product['attribute_set_code'];
					unset($product['attribute_set_code']);
				}
			}
		}
		
		if (isset($params['import_fields'])) {
			if ($params['import_fields'] === "false") {
				if (isset($product['image'])) {
					if (strpos($product['image'],'/') === false) {
						$product['image'] = "/".$product['image'];
					}
				}
				if (isset($product['small_image'])) {
					if (strpos($product['small_image'],'/') === false) {
						$product['small_image'] = "/".$product['small_image'];
					}
				}
				if (isset($product['thumbnail'])) {
					if (strpos($product['thumbnail'],'/') === false) {
						$product['thumbnail'] = "/".$product['thumbnail'];
					}
				}
			}
		}
		
		$ProductAttributeData = [
            'image_exclude'                => (isset($product['image_exclude'])) ? $product['image_exclude'] : '',
            'small_image_exclude'          => (isset($product['small_image_exclude'])) ? $product['small_image_exclude'] : '',
            'thumbnail_exclude'            => (isset($product['thumbnail_exclude'])) ? $product['thumbnail_exclude'] : '',
            'swatch_image_exclude'         => (isset($product['swatch_image_exclude'])) ? $product['swatch_image_exclude'] : '',
            'gallery_exclude'              => (isset($product['gallery_exclude'])) ? $product['gallery_exclude'] : '',
        ];
		
        foreach ($product as $field => $value) {
            if (in_array($field, $this->_imageFields)) {
                continue;
            }
            if (in_array($field, $this->_stockDataFields)) {
                $ProductStockdata[$field] = $value;
                continue;
            }
            #if (!in_array( $field, $this -> _imageFields ) ) {
            #$ProductAttributeData[$field] = $setValue;
            #}
            #$attribute = $objectManager->create('Magento\Eav\Model\Config')->getAttribute(ModelProduct::ENTITY, $field);
            $attribute = $this->_eavConfig->getAttribute(ModelProduct::ENTITY, $field);

            if (!$attribute->getId()) {
                /* CUSTOM OPTION CODE END */
                if (strpos($field, ':') !== false && strlen($value)) {
                    $values = explode('|', $value);
                    if (count($values) > 0) {
                        $iscustomoptions = "true";

                        foreach ($values as $v) {
                            $parts = explode(':', $v);
                            $title = $parts[0];
                        }
						/*
                        @list($title, $type, $is_required, $sort_order) = explode(':', $field);
                        $title2           = $title;
                        $custom_options[] = [
                            'title'          => $title2,
                            'type'           => $type,
                            'is_require'     => $is_required,
                            'sort_order'     => $sort_order,
                            'values'         => []
                        ];
						*/
						@list($title,$type,$is_required,$sort_order,$view_mode,$is_onetime,$image_mode,$exclude_first_image,$sku_policy,$opt_in_group_id,$is_dependent,$qnty_input,$customer_groups2,$optiondescription) = explode(':',$field); //using [] before
						 $title2 = str_replace('_',' ',$title);
						 #$customer_groups = str_replace("{}",",", $customer_groups2);
						 $custom_options[] = array(
									 'is_delete'=>0,
									 'title'=>$title2,
									 'type'=>$type,
									 'is_require'=>$is_required,
									 'sort_order'=>$sort_order,
									 'disabled'=>$view_mode,
									 'one_time'=>$is_onetime,
									 'mageworx_option_image_mode'=>$image_mode,
									 'exclude_first_image'=>$exclude_first_image,
									 'sku_policy'=>$sku_policy,
									 'group_option_id'=>$opt_in_group_id,
									 'dependency_type'=>$is_dependent,
									 'qnty_input'=>$qnty_input,
									 'description'=>$optiondescription,
									 'values'=>array()
								  );
						
						//m1 to m2 mapping
						//view_mode = disabled	
						//is_dependent = dependency_type
						//in_group_id = group_option_id
						//customoptions_is_onetime = one_time
						//image_mode = mageworx_option_image_mode
						//customer_groups = gone in m2??
						
                        if ($is_required == 1) {
                            $iscustomoptionsrequired = "true";
                        }
                        foreach ($values as $v) {
                            $parts = explode(':', $v);
         	 				$dependent_ids = explode('^',$v);
                            $title = $parts[0];
                            if (count($parts) > 1) {
                                $price_type = $parts[1];
                            } else {
                                $price_type = 'fixed';
                            }
                            if (count($parts) > 2) {
                                $price = $parts[2];
                            } else {
                                $price = 0;
                            }
                            if (count($parts) > 3) {
                                $sku = $parts[3];
                            } else {
                                $sku = '';
                            }
                            if (count($parts) > 4) {
                                $sort_order = $parts[4];
                            } else {
                                $sort_order = 0;
                            }
                            if (count($parts) > 5) {
                                $max_characters = $parts[5];
                            } else {
                                $max_characters = '';
                            }
							if ($this->moduleManager->isEnabled('MageWorx_OptionBase')) {
								//magewox magento custom options
								 if(count($parts)>6) {
									if(isset($dependent_ids[1])) {
										$dependent_ids_parts = explode(':',$dependent_ids[0]);
										$mageworxcustomoptionqty = $dependent_ids_parts[6];
									} else {
										$mageworxcustomoptionqty = $parts[6];
									}
								 } else {
									$mageworxcustomoptionqty = '';
								 }
								 if(count($parts)>7) {
									if(isset($dependent_ids[1])) {
										$dependent_ids_parts = explode(':',$dependent_ids[0]);
										$special_price = $dependent_ids_parts[7];
									} else {
										$special_price = $parts[7];
									}
								 } else {
									$special_price = '';
								 }
								 if(count($parts)>8) {
									if(isset($dependent_ids[1])) {
										$dependent_ids_parts = explode(':',$dependent_ids[0]);
										$special_price_comment = $dependent_ids_parts[8];
									} else {
										$special_price_comment = $parts[8];
									}
								 } else {
									$special_price_comment = '';
								 }
								 
								 if(count($parts)>9) {
									if(isset($dependent_ids[1])) {
										$dependent_ids_parts = explode(':',$dependent_ids[0]);
										$image_path = $dependent_ids_parts[9];
									} else {
										$image_path = $parts[9];
									}
								 } else {
									$image_path = '';
								 }
								 if(count($parts)>10) {
									if(isset($dependent_ids[1])) {
										$dependent_ids_parts = explode(':',$dependent_ids[0]);
										$image_color_item = $dependent_ids_parts[10];
									} else {
										$image_color_item = $parts[10];
									}
								 } else {
									$image_color_item = '';
								 }
								 if(count($parts)>11) {
									if(isset($dependent_ids[1])) {
										$dependent_ids_parts = explode(':',$dependent_ids[0]);
										$in_group_id = $dependent_ids_parts[11];
									} else {
										$in_group_id = $parts[11];
									}
								 } else {
									$in_group_id = '';
								 }
								 if(count($parts)>12) {
									if(isset($dependent_ids[1])) {
										$dependent_ids_parts = explode(':',$dependent_ids[0]);
										$co_tier_prices = $dependent_ids_parts[12];
									} else {
										$co_tier_prices = $parts[12];
									}
								 } else {
									$co_tier_prices = '';
								 }
								 if(count($parts)>13) {
									if(isset($dependent_ids[1])) {
										$dependent_ids_parts = explode(':',$dependent_ids[0]);
										$weight = $dependent_ids_parts[13];
									} else {
										$weight = $parts[13];
									}
								 } else {
									$weight = '';
								 }
								 if(count($parts)>14) {
									if(isset($dependent_ids[1])) {
										$dependent_ids_parts = explode(':',$dependent_ids[0]);
										$file_extension = $dependent_ids_parts[14];
									} else {
										$file_extension = $parts[14];
									}
								 } else {
									$file_extension = '';
								 }
								 if(count($parts)>15) {
									$image_size_x = $parts[15];
								 } else {
									$image_size_x = '';
								 }
								 if(count($parts)>16) {
									$image_size_y = $parts[16];
								 } else {
									$image_size_y = '';
								 }
							} else {
								if (count($parts) > 6) {
									$file_extension = $parts[6];
								} else {
									$file_extension = '';
								}
								if (count($parts) > 7) {
									$image_size_x = $parts[7];
								} else {
									$image_size_x = '';
								}
								if (count($parts) > 8) {
									$image_size_y = $parts[8];
								} else {
									$image_size_y = '';
								}
							 	$special_price_comment='';
							 	$special_price='';
							 	$image_color_item = '';
							 	$image_path = '';
							 	$weight = '';
							 	$in_group_id = '';
							 	$mageworxcustomoptionqty = '';
							}
                            switch ($type) {
                                case 'file':
                                    $custom_options[count($custom_options) - 1]['price_type']     = $price_type;
                                    $custom_options[count($custom_options) - 1]['price']          = $price;
                                    $custom_options[count($custom_options) - 1]['sku']            = $sku;
                                    $custom_options[count($custom_options) - 1]['file_extension'] = $file_extension;
                                    $custom_options[count($custom_options) - 1]['image_size_x']   = $image_size_x;
                                    $custom_options[count($custom_options) - 1]['image_size_y']   = $image_size_y;
                                    break;

                                case 'field':
                                    $custom_options[count($custom_options) - 1]['max_characters'] = $max_characters;
                                case 'area':
                                    $custom_options[count($custom_options) - 1]['max_characters'] = $max_characters;

                                case 'date':
                                case 'date_time':
                                case 'time':
                                    $custom_options[count($custom_options) - 1]['price_type'] = $price_type;
                                    $custom_options[count($custom_options) - 1]['price']      = $price;
                                    $custom_options[count($custom_options) - 1]['sku']        = $sku;
                                    break;

                                case 'drop_down':
                                case 'radio':
                                case 'checkbox':
                                case 'multiple':
                                default:
                                    $custom_options[count($custom_options) - 1]['values'][] = [
                                        'title'          => $title,
                                        'price_type'     => $price_type,
                                        'price'          => $price,
                                        'sku'            => $sku,
                                        'sort_order'     => $sort_order,
									  	'max_characters' => $max_characters,
									  	'qty'=>$mageworxcustomoptionqty,
									  	'group_option_value_id'    => $in_group_id,
									  	'weight'		 => $weight,
									  	'dependent_ids'  => isset($dependent_ids[1]) ? $dependent_ids[1] : '',
									  	'tier_prices'	 => isset($co_tier_prices) ? $co_tier_prices : '',
									  	'image_path'	 => $image_path,
									  	'image_color'    => $image_color_item,
									  	'special_price'  => $special_price,
									  	'special_comment'=> $special_price_comment,
									  	'option_id'		 => '',
										'record_id' => 0,
                                    ];
                                    break;
									
									//in_group_id = group_option_value_id
									//tiers = tier_price
									//images = images_data
									//customoptions_qty = qty
                            }
                        }
                    }
                }
                /* CUSTOM OPTION CODE END */
                continue;
            }

            if (strtolower($params['import_attribute_value']) === "true") {
                if ($params['attribute_for_import_value'] != "") {
                    $attributestocheck = explode(',', trim($params['attribute_for_import_value']));
                    foreach ($attributestocheck as $single_attribute) {
                        if ($field === $single_attribute) {
							//checks if it's multi-select
							if (strpos($product[$field],' , ') !== false) {
								$attributestocheckvalues = explode(' , ', $product[$field]);
								foreach ($attributestocheckvalues as $single_attribute_value_for_import) {   
									if (!$this->checkAttributeOptionValue($single_attribute, $single_attribute_value_for_import) && $product[$field] !== '') {
										$option = $this->optionFactory->create();
										$option->setLabel(trim($single_attribute_value_for_import));
										$this->optionManager->add($field, $option);
		
										/** add the newly created option to the values array so we don't create it again */
										$this->addAttributeOptionValue($single_attribute, trim($single_attribute_value_for_import));
									}
								}
							//its a dropdown value
							} else {
								if (!$this->checkAttributeOptionValue($single_attribute, $product[$field]) && $product[$field] !== '') {
									$option = $this->optionFactory->create();
									$option->setLabel($product[$field]);
									$this->optionManager->add($field, $option);
	
									/** add the newly created option to the values array so we don't create it again */
									$this->addAttributeOptionValue($single_attribute, $product[$field]);
								}
							}
                        }
                    }
					$attribute = $this->_eavConfig->getAttribute(ModelProduct::ENTITY, $field); // add this to refresh attribute options after we add some new ones
                }
            }
            $isArray  = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value    = explode(self :: MULTI_DELIMITER, $value);
                $isArray  = true;
                $setValue = [];
            }

            if ($attribute->getData('is_global') == '1') {
                $arrayOfFieldstoSkip[] = $field;
            }
            if ($value && $attribute->getBackendType() == 'decimal') {
                $setValue = $this->localeFormat->getNumber($value);
            }

            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);

                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if (is_array($item['value'])) {
                            foreach ($item['value'] as $subValue) {
                                if (isset($subValue['value']) && $subValue['value'] == $value) {
                                    $setValue = $value;
                                }
                            }
                        } else {
                            if ($item['label'] == $value) {
                                $setValue = $item['value'];
                            }
                        }
                    }
                }
            }

            $product[$field] = $setValue;
			
            if (!in_array($field, $this->_imageFields)) {
                $ProductAttributeData[$field] = $setValue;
            }
        }

		
        //Checks if it is new Product
        $newProduct        = true;
        $productRepository = "";
		$productID = "";
		
		if(isset($product['prodtype'])) { $productType = $product['prodtype']; } else { $productType = ""; }
		
        if ($params['ref_by_product_id'] == "true" && isset($product['product_id'])) {
            if ($productRepository = $this->_ProductModel->load($product['product_id'])) {
                $newProduct = false;
				$productType = $productRepository->getTypeId();
				$productID = $productRepository->getId();
				if(!isset($product['url_key'])) { $product['url_key'] = $productRepository->getUrlKey(); }// this adds url_key for updating a product avoids this error SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'xxxxx.html-1' for key 'URL_REWRITE_REQUEST_PATH_STORE_ID'
            }
        } else {
            if ($productloadByAttribute = $this->_ProductModel->loadByAttribute('sku', $product['sku'])) {
				$productID = $productloadByAttribute->getId();
				#$productRepository = $this->_ProductModel->load($productID);
                #$productRepository = $this->_ProductModel->reset();
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
				$ProductModel = $objectManager->create('Magento\Catalog\Model\Product');
				$productRepository = $ProductModel->load($productID);
                $newProduct = false;
				#$productType = $productloadByAttribute->getTypeId();
				$productType = $productRepository->getTypeId();
				if(!isset($product['url_key'])) { $product['url_key'] = $productloadByAttribute->getUrlKey(); } // this adds url_key for updating a product avoids this error SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'xxxxx.html-1' for key 'URL_REWRITE_REQUEST_PATH_STORE_ID'
            }
        }
		
		if(isset($product['status'])) {
			if (strtolower($product['status']) == "delete") {
				$productRepository -> delete();
				return $messagetoreturn['noprodtype'] = ['line' => $rowCount, 'column' => 'sku', 'error' => 'The "sku"' . $product['sku'].' has been deleted'];
			}
		}
		if ($params['create_products_only'] == "true" && $newProduct == false) {
			return false;
		}
		if ($params['update_products_only'] == "true" && $productType == "") {
			return $messagetoreturn['noprodtype'] = ['line' => $rowCount, 'column' => 'sku', 'error' => 'The "sku"' . $product['sku'].' is not found and was skipped'];
		}
		if($productType == "") { 
			if (!isset($product['prodtype'])) {
				
        		if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
					$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "ERROR: sku: ".$product['sku']." is not found. The REQUIRED FIELD 'prodtype' is required and was NOT FOUND");
					$this->helper->writeToCsv($cronLogErrors);	
				} else {
					throw new \Magento\Framework\Exception\LocalizedException(__('ERROR: "sku:" '.$product['sku'].' is not found. The REQUIRED FIELD "prodtype" is required and was NOT FOUND'));
				}
			}
			$productType = $product['prodtype']; 
		}
		
        #keeps existing category_ids and adds new ones to them
        if (isset($product['category_ids'])) {
            if ($params['append_categories'] == "true") {
                #$product1 = $this->_ProductModel->loadByAttribute('sku', $product['sku']);
                if (!empty($productRepository)) {
                    $productModel            = $this->_ProductModel->load($productRepository->getId());
                    $cats                    = $productModel->getCategoryIds();
                    $catsarray               = explode(",", $product['category_ids']);
                    $finalcatsimport         = array_merge($cats, $catsarray);
                    $product['category_ids'] = $finalcatsimport;
                } else {
                    $catsarray               = explode(",", $product['category_ids']);
                    $product['category_ids'] = $catsarray;
                }
            } else {
                $catsarray               = explode(",", $product['category_ids']);
                $product['category_ids'] = $catsarray;
            }
        }

        $ProductData = $this->ProductData($product, $params, $newProduct);

        if (isset($product['categories'])) {
            if ($params['append_categories'] == "true") {
                #$product1 = $this->_ProductModel->loadByAttribute('sku', $product['sku']);
                if (!empty($productRepository)) {
                    $productModel            = $this->_ProductModel->load($productRepository->getId());
                    $cats                    = $productModel->getCategoryIds();
                    $catsarray               = explode(",", $ProductData['categories']);
                    $finalcatsimport         = array_merge($cats, $catsarray);
                    $product['category_ids'] = $finalcatsimport;
                } else {
                    $catsarray               = explode(",", $ProductData['categories']);
                    $product['category_ids'] = $catsarray;
                }
            } else {
                $catsarray               = explode(",", $ProductData['categories']);
                $product['category_ids'] = $catsarray;
            }
        }

        #$ProductAttributeData = $this->ProductAttributeData($product);
		if(isset($product['additional_attributes'])) { $ProductAttributeData['additional_attributes'] = $product['additional_attributes']; }
		if(isset($product['category_ids'])) { $ProductAttributeData['category_ids'] = $product['category_ids']; }
		if(isset($product['prodtype'])) { $ProductAttributeData['prodtype'] = $product['prodtype']; }
		if(isset($product['url_key'])) { $ProductAttributeData['url_key'] = $product['url_key']; }
		if(isset($product['config_attributes'])) { $ProductAttributeData['config_attributes'] = $product['config_attributes']; }
		
        //$ProductImageGallery = [];
        $ProductImageGallery = ["image", "small_image", "thumbnail", "gallery", "swatch_image"];
        if ($newProduct || $params['reimport_images'] == "true") {
            $ProductImageGallery = $this->ProductImageGallery($newProduct, $product, $params);
        }
		
        $ProductSupperAttribute = $this->ProductSupperAttribute($newProduct, $product, $params);

        return $this->CreateProductWithrequiredField($rowCount,
													 $productID,
                                                     $newProduct,
                                                     $productRepository,
                                                     strtolower($productType),
                                                     $params,
                                                     $ProductData,
                                                     $ProductAttributeData,
                                                     $ProductImageGallery,
                                                     $ProductStockdata,
                                                     $ProductSupperAttribute,
                                                     $custom_options);
    }
	
    protected function _filterData(array $RawDataHeader, array $RawData, array $params)
    {
        $rowCount    = 0;
        $RawDataRows = [];
        foreach ($RawData as $rowIndex => $dataRow) {
            // skip headers
            if ($rowIndex == 0) {
				if(isset($params['import_fields'])) {
					if ($params['import_fields'] == "false") {
						/* FOR THE FIELD MAPPING OPTION */
						$mappedFieldsOnly = array_combine($params['gui_data']['map']['product']['file'],$params['gui_data']['map']['product']['db']);
						unset($mappedFieldsOnly[0]);
						if (!in_array("sku", $mappedFieldsOnly)) {
							if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
								$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "ERROR: REQUIRED FIELD 'sku' NOT mapped");
								$this->helper->writeToCsv($cronLogErrors);	
							} else {
								throw new \Magento\Framework\Exception\LocalizedException(__('ERROR: ROW: '.$rowCount.' -- REQUIRED FIELD "sku" NOT mapped'));
							}
						}
						continue;
						/* FOR THE FIELD MAPPING OPTION */
					} else {
						if (!in_array("sku", $dataRow)) {
							if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
								$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "ERROR: REQUIRED FIELD 'sku' NOT FOUND");
								$this->helper->writeToCsv($cronLogErrors);	
							} else {
								throw new \Magento\Framework\Exception\LocalizedException(__('ERROR: ROW: '.$rowCount.' -- REQUIRED FIELD "sku" NOT FOUND'));
							}
						}
						continue;
					}
				}
            }
            // skip empty rows
            if (count($dataRow) <= 1) {
                unset($RawData[$rowIndex]);
                continue;
            }
            /* we take rows from [0] = > value to [website] = base */
            if ($rowIndex > 0) {
                foreach ($dataRow as $rowIndex => $dataRowNew) {
                    try {
						
						if(isset($params['import_fields'])) {
							if ($params['import_fields'] == "false") {
								/* FOR THE FIELD MAPPING OPTION */
								$mappedFieldsOnly = array_combine($params['gui_data']['map']['product']['db'],$params['gui_data']['map']['product']['file']);
								unset($mappedFieldsOnly[0]);
								foreach ($mappedFieldsOnly as $magentoField => $fileField) {
									if($RawDataHeader[$rowIndex] == $fileField) { $RawDataRows[$rowCount][$magentoField] = $dataRowNew; }
								}
								/* FOR THE FIELD MAPPING OPTION */
							} else {
								$RawDataRows[$rowCount][$RawDataHeader[$rowIndex]] = $dataRowNew;
							}
						} else {
							$RawDataRows[$rowCount][$RawDataHeader[$rowIndex]] = $dataRowNew;
						}
						
                    } catch (\Exception $e) {
						if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
							$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, " CHECK CSV DELIMITER SETTINGS AND/OR CSV FORMAT. CSV CANNOT BE PARSED -- ERROR: " . $e->getMessage());
							$this->helper->writeToCsv($cronLogErrors);	
						} else {
                        	throw new \Magento\Framework\Exception\LocalizedException(__("CHECK CSV DELIMITER SETTINGS AND/OR CSV FORMAT. CSV CANNOT BE PARSED"), $e);
						}
                    }
                }
            }
            $rowCount++;
        }

        return $RawDataRows;
    }

    public function UploadCsvOfproduct($file)
    {
        $uploader = $this->_fileUploaderFactory->create(['fileId' => $file]);
        $uploader->setAllowedExtensions(['csv']);
        $uploader->setAllowRenameFiles(false);
        $uploader->setFilesDispersion(false);

        $path = $this->getVarDirProductImportExportDir();
        $this->file->checkAndCreateFolder($path);
        #$path = $this->_filesystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath('ProductImportExport');
        //$result = $uploader->save($path);
        $result = $uploader->save(realpath($path));

        return $result;
    }

    public function _getNotCachedRow($path, $storeId)
    {
        $type = 'product';
        $cfg  = $this->helper->getStoreConfig($path . '/' . $type, $storeId);

        $scope   = 'default';
        $scopeId = 0;

        //'core/config_data_collection'
        $collection = $this->_objectManager->create("Magento\Config\Model\ResourceModel\Config\Data\Collection");
        $collection->addFieldToFilter('scope', $scope);
        $collection->addFieldToFilter('scope_id', $scopeId);
        $collection->addFieldToFilter('path', $path . '/' . $type . '/' . $path);
        $collection->setPageSize(1);

        $v = $this->_objectManager->create('Magento\Framework\App\Config\Value');
        if (count($collection)) {
            $v = $collection->getFirstItem();
        } else {
            $v->setScope($scope);
            $v->setScopeId($scopeId);
            $v->setPath($path . '/' . $type . '/' . $path);
        }

        return $v;
    }

    public function readCsvFile($PfilePath, $params)
    {
		
        $warnings        = [];
        $messagetoreturn = [];
        if ($params['import_delimiter'] != "") {
            $this->csvProcessor->setDelimiter($params['import_delimiter']);
        }
        if ($params['import_enclose'] != "") {
            $this->csvProcessor->setEnclosure($params['import_enclose']);
        }

        $RawProductData = $this->csvProcessor->getData($PfilePath); //$RawProductData[0] represents headers
        $productData    = $this->_filterData($RawProductData[0], $RawProductData, $params);
		//print_r($productData);
        $importStatus = $this->_getNotCachedRow('importstatus', 0);
        $importStatus->setValue("started")->save();

        $totalnumberofrows = count($productData);
        $totalRow          = $this->_getNotCachedRow('totalrow', 0);
        $totalRow->setValue($totalnumberofrows)->save();
        $rowCount = 0;
        foreach ($productData as $product) {
            $rowCount++;
            $warnings   = $this->requiredDataForSaveProduct($rowCount, $product, $params);
            $currentRow = $this->_getNotCachedRow('currentrow', 0);
            if ($currentRow->getValue() != $rowCount) {
                $currentRow->setValue($rowCount)->save();
				$importStatus = $this->_getNotCachedRow('importstatus', 0);
				if($importStatus->getValue() == "canceled") {
					$messagetoreturn['total_rows']    		= $totalnumberofrows;
					$messagetoreturn['total_success_rows'] 	= $rowCount;
					$messagetoreturn['import_status'] 		= "canceled";
					$messagetoreturn['warnings']      		= $warnings;
					return $messagetoreturn;
				}
            }
        }
		if($importStatus->getValue() != "canceled") {
			$importStatus = $this->_getNotCachedRow('importstatus', 0);
			$importStatus->setValue("finished")->save();
			$messagetoreturn['total_rows']    		= $totalnumberofrows;
			$messagetoreturn['total_success_rows'] 	= $rowCount;
			$messagetoreturn['import_status'] 		= "finished";
			$messagetoreturn['warnings']      		= $warnings;
		}

        return $messagetoreturn;
    }
	protected function getMediaDirImportDir()
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'import';
    }
    protected function getMediaDirTmpImportDir($folder1="a",$folder2="b")
    {
        #return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'import';
        return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp'. DIRECTORY_SEPARATOR . 'catalog'. DIRECTORY_SEPARATOR . 'product'. DIRECTORY_SEPARATOR . $folder1. DIRECTORY_SEPARATOR . $folder2;
    }

    public function getVarDirProductImportExportDir()
    {
        return $this->directoryList->getPath(DirectoryList::VAR_DIR) . DIRECTORY_SEPARATOR . 'ProductImportExport';
    }

    public function CreateProductWithrequiredField(
        $rowCount,
		$productID,
        $newProduct,
        $SetProductData,
        $prodtype,
        $params,
        $ProductData,
        $ProductAttributeData,
        $ProductImageGallery,
        $ProductStockdata,
        $ProductSupperAttribute,
        $ProductCustomOption
    ) {


        if ($prodtype == "simple") {
            $messagetoreturn['simple'] = $this->SimpleProduct->SimpleProductData($rowCount,
																				 $productID,
                                                                                 $newProduct,
                                                                                 $params,
                                                                                 $ProductData,
                                                                                 $ProductAttributeData,
                                                                                 $ProductImageGallery,
                                                                                 $ProductStockdata,
                                                                                 $ProductSupperAttribute,
                                                                                 $ProductCustomOption);
        } elseif ($prodtype == "bundle") {
            $messagetoreturn['bundle'] = $this->BundleProduct->BundleProductData($rowCount,
																				 $productID,
                                                                                 $newProduct,
                                                                                 $SetProductData,
                                                                                 $params,
                                                                                 $ProductData,
                                                                                 $ProductAttributeData,
                                                                                 $ProductImageGallery,
                                                                                 $ProductStockdata,
                                                                                 $ProductSupperAttribute,
                                                                                 $ProductCustomOption);
        } elseif ($prodtype == "virtual") {
            $messagetoreturn['virtual'] = $this->VirtualProduct->VirtualProductData($rowCount,
																				 	$productID,
                                                                                    $newProduct,
                                                                                    $params,
                                                                                    $ProductData,
                                                                                    $ProductAttributeData,
                                                                                    $ProductImageGallery,
                                                                                    $ProductStockdata,
                                                                                    $ProductSupperAttribute);
        } elseif ($prodtype == "configurable") {
            $messagetoreturn['configurable'] = $this->ConfigurableProduct->ConfigurableProductData($rowCount,
																								   $productID,
                                                                                                   $newProduct,
                                                                                                   $params,
                                                                                                   $ProductData,
                                                                                                   $ProductAttributeData,
                                                                                                   $ProductImageGallery,
                                                                                                   $ProductStockdata,
                                                                                                   $ProductSupperAttribute,
                                                                                 				   $ProductCustomOption);
        } elseif ($prodtype == "grouped") {
            $messagetoreturn['grouped'] = $this->GroupedProduct->GroupedProductData($rowCount,
																					$productID,
                                                                                    $newProduct,
                                                                                    $params,
                                                                                    $ProductData,
                                                                                    $ProductAttributeData,
                                                                                    $ProductImageGallery,
                                                                                    $ProductStockdata,
                                                                                    $ProductSupperAttribute);
        } elseif ($prodtype == "downloadable") {
            $messagetoreturn['downloadable'] = $this->DownloadableProduct->DownloadableProductData($rowCount,
																								   $productID,
                                                                                                   $newProduct,
                                                                                                   $SetProductData,
                                                                                                   $params,
                                                                                                   $ProductData,
                                                                                                   $ProductAttributeData,
                                                                                                   $ProductImageGallery,
                                                                                                   $ProductStockdata,
                                                                                                   $ProductSupperAttribute);
        } else {
            $messagetoreturn['noprodtype'][] = array('line' => $rowCount , 'column' => 'prodtype', 'error' => 'The "prodtype" column is required');
        }

        return $messagetoreturn;
    }

    public function ProductData($product, $params, $newProduct)
    {

        $defaultProductData['sku'] = $product['sku'];
        if (isset($product['url_key'])) {
            $defaultProductData['url_key'] = $product['url_key'];
        }
        if (isset($product['store'])) {
            $defaultProductData['store'] = $product['store'];
        } else {
            $defaultProductData['store'] = "admin";
        }
        if (isset($product['store_id'])) {
            $defaultProductData['store_id'] = $product['store_id'];
        } else {
            $defaultProductData['store_id'] = "0";
        }
        if (isset($product['websites'])) {
            $defaultProductData['websites'] = $this->websitenamebyid($params, $product['websites'], $product['sku'], $newProduct);
        }
        if (isset($product['attribute_set'])) {
            $defaultProductData['attribute_set'] = $this->attributeSetNamebyid($product['attribute_set']);
        }
        if (isset($product['prodtype'])) {
            $defaultProductData['prodtype'] = $product['prodtype'];
        }
        if (isset($product['categories'])) {
            $defaultProductData['categories'] = $this->addCategories($product['categories'], $defaultProductData['store_id'], $params);
        }
        if (isset($product['category_ids'])) {
            $defaultProductData['category_ids'] = $product['category_ids'];
        }

        return $defaultProductData;
    }

    public function ProductAttributeData($product)
    {
        $defaultAttributeData = [];
		$defaultAttributeData = [
            'image_exclude'                => (isset($product['image_exclude'])) ? $product['image_exclude'] : '',
            'small_image_exclude'          => (isset($product['small_image_exclude'])) ? $product['small_image_exclude'] : '',
            'thumbnail_exclude'            => (isset($product['thumbnail_exclude'])) ? $product['thumbnail_exclude'] : '',
            'swatch_image_exclude'         => (isset($product['swatch_image_exclude'])) ? $product['swatch_image_exclude'] : '',
            'gallery_exclude'              => (isset($product['gallery_exclude'])) ? $product['gallery_exclude'] : '',
        ];
        foreach ($product as $field => $value) {
            if (!in_array($field, $this->_imageFields)) {
                $defaultAttributeData[$field] = $value;
            }
        }
		if(isset($product['additional_attributes'])) { $defaultAttributeData['additional_attributes'] = $product['additional_attributes']; }
		
        return $defaultAttributeData;
    }

    public function ProductSupperAttribute($newProduct, $product, $params)
    {

        $defaultSupperAttributeData = [
            'related'                     => (isset($product['related'])) ? $product['related'] : '',
            'upsell'                      => (isset($product['upsell'])) ? $product['upsell'] : '',
            'crosssell'                   => (isset($product['crosssell'])) ? $product['crosssell'] : '',
            'tier_prices'                 => (isset($product['tier_prices'])) ? $this->TierPricedata($newProduct, $product['tier_prices'], $product['sku'], $params) : '',
            'associated'                  => (isset($product['associated'])) ? $product['associated'] : '',
            'bundle_options'              => (isset($product['bundle_options'])) ? $product['bundle_options'] : '',
            'bundle_selections'           => (isset($product['bundle_selections'])) ? $product['bundle_selections'] : '',
            'grouped'                     => (isset($product['grouped'])) ? $product['grouped'] : '',
            'group_price_price'           => (isset($product['group_price_price'])) ? $product['group_price_price'] : '',
            'downloadable_options'        => (isset($product['downloadable_options'])) ? $product['downloadable_options'] : '',
            'downloadable_sample_options' => (isset($product['downloadable_sample_options'])) ? $product['downloadable_sample_options'] : '',
        ];

        return $defaultSupperAttributeData;
    }

    public function ProductImageGallery($newProduct, $product, $params)
    {

        if ($params['deleteall_andreimport_images'] == "true" && !$newProduct) {
            // bug here.. something about this delete image and resave wipes out all store view default check box settings
            $productRepository = $this->_objectManager->get('Magento\Catalog\Api\ProductRepositoryInterface');
            $productModel      = $productRepository->get($product['sku']);
            $productModel->setMediaGalleryEntries([]);
            $productRepository->save($productModel);

            //DELETES EXTRA BLANK STORE IMAGES AND OTHER STORE VIEW IMAGES
            $product_id = $productModel->getId();
            $connection = $this->_resource->getConnection();

            $eav_attribute                              = $this->_resource->getTableName('eav_attribute');
            $catalog_product_entity_varchar             = $this->_resource->getTableName('catalog_product_entity_varchar');
            $catalog_product_entity_media_gallery       = $this->_resource->getTableName('catalog_product_entity_media_gallery');
            $catalog_product_entity_media_gallery_value = $this->_resource->getTableName('catalog_product_entity_media_gallery_value');
			$catalog_product_entity_type                = $this->_objectManager->get('Magento\Eav\Model\Entity')->setType('catalog_product')->getTypeId();
			
			if($this->getEdition() == "Community") { 
				$allImages = $connection->fetchAll("SELECT value_id FROM " . $catalog_product_entity_media_gallery_value . " WHERE entity_id = '" . $product_id . "'");
				foreach ($allImages as $eachImageRemove) {
					if ($eachImageRemove['value_id'] != "") {
						$connection->query("DELETE FROM " . $catalog_product_entity_media_gallery . " WHERE value_id = '" . $eachImageRemove['value_id'] . "'");
					}
				}
				$connection->query("DELETE FROM " . $catalog_product_entity_media_gallery_value . " WHERE entity_id = '" . $product_id . "'");
				$connection->query("DELETE FROM " . $catalog_product_entity_varchar . " WHERE entity_id = '" . $product_id . "' AND ( attribute_id = ( SELECT attribute_id FROM " . $eav_attribute . " AS eav WHERE eav.attribute_code = 'image' AND eav.entity_type_id ='".$catalog_product_entity_type."') OR attribute_id = ( SELECT attribute_id FROM " . $eav_attribute . " AS eav WHERE eav.attribute_code = 'small_image' AND eav.entity_type_id ='".$catalog_product_entity_type."') OR attribute_id = ( SELECT attribute_id FROM " . $eav_attribute . " AS eav WHERE eav.attribute_code = 'thumbnail' AND eav.entity_type_id ='".$catalog_product_entity_type."') OR attribute_id = ( SELECT attribute_id FROM " . $eav_attribute . " AS eav WHERE eav.attribute_code = 'media_gallery' AND eav.entity_type_id ='".$catalog_product_entity_type."'))");
			} else {
				$allImages = $connection->fetchAll("SELECT value_id FROM " . $catalog_product_entity_media_gallery_value . " WHERE row_id = '" . $product_id . "'");
				foreach ($allImages as $eachImageRemove) {
					if ($eachImageRemove['value_id'] != "") {
						$connection->query("DELETE FROM " . $catalog_product_entity_media_gallery . " WHERE value_id = '" . $eachImageRemove['value_id'] . "'");
					}
				}
				$connection->query("DELETE FROM " . $catalog_product_entity_media_gallery_value . " WHERE row_id = '" . $product_id . "'");
				$connection->query("DELETE FROM " . $catalog_product_entity_varchar . " WHERE row_id = '" . $product_id . "' AND ( attribute_id = ( SELECT attribute_id FROM " . $eav_attribute . " AS eav WHERE eav.attribute_code = 'image' AND eav.entity_type_id ='".$catalog_product_entity_type."') OR attribute_id = ( SELECT attribute_id FROM " . $eav_attribute . " AS eav WHERE eav.attribute_code = 'small_image' AND eav.entity_type_id ='".$catalog_product_entity_type."') OR attribute_id = ( SELECT attribute_id FROM " . $eav_attribute . " AS eav WHERE eav.attribute_code = 'thumbnail' AND eav.entity_type_id ='".$catalog_product_entity_type."') OR attribute_id = ( SELECT attribute_id FROM " . $eav_attribute . " AS eav WHERE eav.attribute_code = 'media_gallery' AND eav.entity_type_id ='".$catalog_product_entity_type."'))");
			
			}
        }

        if ($params['import_images_by_url'] == "true") {
            $arr = ["image", "small_image", "thumbnail", "gallery", "swatch_image"];
			
			$ProductImageGallery['image_label'] = (isset($product['image_label'])) ? $product['image_label'] : '';
			$ProductImageGallery['small_image_label'] = (isset($product['small_image_label'])) ? $product['small_image_label'] : '';
			$ProductImageGallery['thumbnail_label'] = (isset($product['thumbnail_label'])) ? $product['thumbnail_label'] : '';
			$ProductImageGallery['gallery_label'] = (isset($product['gallery_label'])) ? $product['gallery_label'] : '';
			
            foreach ($arr as $mediaAttributeCode) {
                if (isset($product[$mediaAttributeCode])) {
                    if ($product[$mediaAttributeCode] != "") {
                        if ($mediaAttributeCode == "gallery") {
                            $finalgalleryfiles = "";
                            $eachImageUrls     = explode(',', $product[$mediaAttributeCode]);
                            foreach ($eachImageUrls as $rawImageUrl) {
                                if ($rawImageUrl != "") {
									$imageUrl = str_replace(' ', '%20', $rawImageUrl);
                                    /** @var string $tmpDir */
									$folderNames = str_split(baseName($imageUrl), 1);
                                    $importDir = $this->getMediaDirTmpImportDir($folderNames[0],$folderNames[1]);
                                    #$importDir = $this->getMediaDirImportDir();
                                    /** create folder if it is not exists */
                                    $this->file->checkAndCreateFolder($importDir);
                                    /** @var string $newFileName */
                                    #$newFileName = $importDir . '/' . baseName($imageUrl);
									$newFileName = $importDir . '/' . baseName(parse_url($imageUrl)['path']); //removes parameters
                                    /** read file from URL and copy it to the new destination */
                                    $result = $this->file->read($imageUrl, $newFileName);
                                    if ($result) {
                                        #$finalgalleryfiles .= '/' . baseName($newFileName) . ",";
                                        $finalgalleryfiles .= '/' . $folderNames[0].'/'.$folderNames[1].'/' . baseName($newFileName) . ",";
                                    } else {
                                        $this->helper->sendLog($this->helper->rowCount, 'gallery', 'Image URL: ' . $imageUrl . ' cannot be Found 404');
                                        $ProductImageGallery[$mediaAttributeCode] = 'no_selection';
                                    }
                                }
                            }
                            $ProductImageGallery[$mediaAttributeCode] = substr_replace($finalgalleryfiles, "", -1);
                        } else {
                            $imageUrl = str_replace(' ', '%20', $product[$mediaAttributeCode]);
                            try {
                                /** @var string $tmpDir */
								$folderNames = str_split(baseName($imageUrl), 1);
								$importDir = $this->getMediaDirTmpImportDir($folderNames[0],$folderNames[1]);
                                #$importDir = $this->getMediaDirImportDir();
                                /** create folder if it is not exists */
                                $this->file->checkAndCreateFolder($importDir);
                                /** @var string $newFileName */
                                #$newFileName = $importDir . '/' . baseName($imageUrl);
								$newFileName = $importDir . '/' . baseName(parse_url($imageUrl)['path']); //removes parameters
                                /** read file from URL and copy it to the new destination */
                                $result = $this->file->read($imageUrl, $newFileName);
                                if ($result) {
                                    #$ProductImageGallery[$mediaAttributeCode] = '/' . baseName($newFileName);
                                    $ProductImageGallery[$mediaAttributeCode] = '/'.$folderNames[0].'/'.$folderNames[1].'/' . baseName($newFileName);
                                } else {
                                    $this->helper->sendLog($this->helper->rowCount, $mediaAttributeCode, 'Image URL: ' . $imageUrl . ' cannot be Found 404');
                                    $ProductImageGallery[$mediaAttributeCode] = 'no_selection';
                                }
                            } catch (\Exception $e) {
								if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
									$cronLogErrors[] = array("import_products", "ROW: " . $rowCount, "IMAGE IMPORT ERROR: " . $e->getMessage());
									$this->helper->writeToCsv($cronLogErrors);	
								} else {
                               		throw new \Magento\Framework\Exception\LocalizedException(__("IMAGE IMPORT ERROR: " . $e->getMessage()), $e);
								}
                            }
                        }
                    } else {
                        $ProductImageGallery[$mediaAttributeCode] = '';
                    }
                } else {
                    $ProductImageGallery[$mediaAttributeCode] = 'no_selection';
                }
            }
        } else {
            $ProductImageGallery = [
                'gallery'       => (isset($product['gallery'])) ? $product['gallery'] : 'no_selection',
                'image'         => (isset($product['image'])) ? $product['image'] : 'no_selection',
                'small_image'   => (isset($product['small_image'])) ? $product['small_image'] : 'no_selection',
                'thumbnail'     => (isset($product['thumbnail'])) ? $product['thumbnail'] : 'no_selection',
                'swatch_image'  => (isset($product['swatch_image'])) ? $product['swatch_image'] : 'no_selection',
                'image_label'         => (isset($product['image_label'])) ? $product['image_label'] : '',
                'small_image_label'   => (isset($product['small_image_label'])) ? $product['small_image_label'] : '',
                'thumbnail_label'     => (isset($product['thumbnail_label'])) ? $product['thumbnail_label'] : '',
                'gallery_label' => (isset($product['gallery_label'])) ? $product['gallery_label'] : ''
            ];
			// Added this to take image locally from pub/media/import and move it one time to pub/media/tmp/catalog/product/*
            $arr = ["image", "small_image", "thumbnail", "gallery", "swatch_image"];
			foreach ($arr as $mediaAttributeCode) {
                if (isset($product[$mediaAttributeCode])) {
                    if ($product[$mediaAttributeCode] != "") {
                        if ($mediaAttributeCode == "gallery") {
                            $finalgalleryfiles = "";
                            $eachImageFile     = explode(',', $product[$mediaAttributeCode]);
                            foreach ($eachImageFile as $rawImagefile) {
                                if ($rawImagefile != "") {
                                    $importDir = $this->getMediaDirImportDir();
                                    /** create folder if it is not exists */
                                    $this->file->checkAndCreateFolder($importDir);
                                    /** @var string $newFileName */
                                    $currentFileName = $importDir . '/' . $rawImagefile;
									
									$folderNames = str_split(baseName($rawImagefile), 1);
									#$importTmpDir = $this->getMediaDirTmpImportDir($folderNames[0],$folderNames[1]);
									#$importTmpDir = $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp'. DIRECTORY_SEPARATOR . 'catalog'. DIRECTORY_SEPARATOR . 'product';
									if(isset($folderNames[0]) && isset($folderNames[1])) {
										$importTmpDirWFolders = $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp'. DIRECTORY_SEPARATOR . 'catalog'. DIRECTORY_SEPARATOR . 'product'. DIRECTORY_SEPARATOR . $folderNames[0] . DIRECTORY_SEPARATOR . $folderNames[1];
										$this->file->checkAndCreateFolder($importTmpDirWFolders);
									}
									$importTmpDir = $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp'. DIRECTORY_SEPARATOR . 'catalog'. DIRECTORY_SEPARATOR . 'product';
									$this->file->checkAndCreateFolder($importTmpDir);
									#$importDir = $this->getMediaDirImportDir();
									/** create folder if it is not exists */
                                    $newFileName = $importTmpDir . '/' . $rawImagefile;
                                    /** read file from pub/media/import and copy it to the new destination */
                                    $result = $this->file->read($currentFileName, $newFileName);
                                    if (!$result) {
                                        $this->helper->sendLog($this->helper->rowCount, 'gallery', 'Image Cannot be copied/found: ' . $rawImagefile);
                                    }
                                }
                            }
                        } else {
                            $rawImagefile = $product[$mediaAttributeCode];
							$importDir = $this->getMediaDirImportDir();
							/** create folder if it is not exists */
							$this->file->checkAndCreateFolder($importDir);
							/** @var string $newFileName */
							$currentFileName = $importDir . '/' . $rawImagefile;
							
							$folderNames = str_split(baseName($rawImagefile), 1);
							#$importTmpDir = $this->getMediaDirTmpImportDir($folderNames[0],$folderNames[1]);
							#$importTmpDir = $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp'. DIRECTORY_SEPARATOR . 'catalog'. DIRECTORY_SEPARATOR . 'product';
							
							if(isset($folderNames[0]) && isset($folderNames[1])) {
								$importTmpDirWFolders = $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp'. DIRECTORY_SEPARATOR . 'catalog'. DIRECTORY_SEPARATOR . 'product'. DIRECTORY_SEPARATOR . $folderNames[0] . DIRECTORY_SEPARATOR . $folderNames[1];
								$this->file->checkAndCreateFolder($importTmpDirWFolders);
							}
							$importTmpDir = $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp'. DIRECTORY_SEPARATOR . 'catalog'. DIRECTORY_SEPARATOR . 'product';
							#$importDir = $this->getMediaDirImportDir();
							/** create folder if it is not exists */
							$this->file->checkAndCreateFolder($importTmpDir);
							$newFileName = $importTmpDir . '/' . $rawImagefile;
							/** read file from pub/media/import and copy it to the new destination */
							$result = $this->file->read($currentFileName, $newFileName);
							if (!$result) {
								$this->helper->sendLog($this->helper->rowCount, $mediaAttributeCode, 'Image Cannot be copied/found: ' . $rawImagefile);
							}
						}
					}
				}
			}
        }

        return $ProductImageGallery;
    }

    public function TierPricedata($newProduct, $TPData, $product_sku, $params)
    {
		$tps_toAdd = [];
		
		if(!empty($TPData)) {
			if ($newProduct) {
				//parse incoming tier prices string
				$incoming_tierps = explode('|', $TPData);
				$tierpricecount  = 0;
	
				foreach ($incoming_tierps as $tier_str) {
	
					if (empty($tier_str)) {
						continue;
					}
					$tmp = [];
					$tmp = explode('=', $tier_str);
					if (!isset($tmp[1])) {
						//throw new \Magento\Framework\Exception\LocalizedException(__('Invalid data in "tier_prices" column. No value for QTY found'));
						$this->helper->sendLog($this->helper->rowCount, 'tier_prices', 'Invalid data in column. No value for QTY found');
						continue;
					}
					if (!isset($tmp[2])) {
						$this->helper->sendLog($this->helper->rowCount, 'tier_prices', 'Invalid data in column. No value for PRICE found');
						continue;
					}
					if ($tmp[1] <= 0) {
						$this->helper->sendLog($this->helper->rowCount, 'tier_prices', 'Invalid data in column. QTY cannot be 0 using 1');
						$tmp[1] = 1;
					}
					if ($tmp[1] == 0 && $tmp[2] == 0) {
						continue;
					}
					if (isset($tmp[3])) {
						$value_type = $tmp[3];
					} else {
						$value_type = "fixed"; //percent
					}
	
					$tps_toAdd[$tierpricecount] = [
						'website_id' => 0, // !!!! this is hard-coded for now
						#'website_id' => $tmp[0], // !!!! this is hard-coded for now
						#'website_id' => $store->getWebsiteId(),
						'cust_group' => $tmp[0],
						'price_qty'  => $tmp[1],
						'price'      => $tmp[2],
						'value_type' => $value_type,
						'delete'     => ''
					];
	
					$tierpricecount++;
				}
			} else {
	
				if ($params['append_tier_prices'] != "true") {
					//get current product tier prices
					$existing_tps = [];
					$productModel = $this->_ProductModel->loadByAttribute('sku', $product_sku);
					if ($productModel) {
						$productModelTier = $this->_ProductModel->load($productModel->getId());
						if (!empty($productModelTier->getTierPrice())) {
							$existing_tps = $productModelTier->getTierPrice();
						} else {
							if ($attribute = $productModelTier->getResource()->getAttribute('tier_price')) {
								$attribute->getBackend()->afterLoad($productModelTier);
								$existing_tps = $productModelTier->getData('tier_price');
							}
						}
					}
					$etp_lookup = [];
					//make a lookup array to prevent dup tiers by qty
					foreach ($existing_tps as $key => $etp) {
						$etp_lookup[intval($etp['cust_group']) . "_" . intval($etp['price_qty'])] = $key;
					}
				}
	
				//parse incoming tier prices string
				$incoming_tierps = explode('|', $TPData);
				foreach ($incoming_tierps as $tier_str) {
	
					if (empty($tier_str)) {
						continue;
					}
	
					$tmp = [];
					$tmp = explode('=', $tier_str);
					if (!isset($tmp[1])) {
						//throw new \Magento\Framework\Exception\LocalizedException(__('Invalid data in "tier_prices" column. No value for QTY found'));
						$this->helper->sendLog($this->helper->rowCount, 'tier_prices', 'Invalid data in column. No value for QTY found');
						continue;
					}
					if (!isset($tmp[2])) {
						$this->helper->sendLog($this->helper->rowCount, 'tier_prices', 'Invalid data in column. No value for PRICE found');
						continue;
					}
					if ($tmp[1] <= 0) {
						$this->helper->sendLog($this->helper->rowCount, 'tier_prices', 'Invalid data in column. QTY cannot be 0 using 1');
						$tmp[1] = 1;
					}
					if ($tmp[1] == 0 && $tmp[2] == 0) {
						continue;
					}
	
					if ($tmp[2] != "0.00" && $tmp[0] != '32000') {
						$this->tierPrice->add($product_sku, $tmp[0], $tmp[2], $tmp[1]);
					} else if ($tmp[0] == '32000') {
						$website_id = '0';
           				$connection = $this->_resource->getConnection();
						$_catalog_product_entity_tier_price = $this->_resource->getTableName('catalog_product_entity_tier_price');
						if($product_sku!="") {
						  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
						  $productCheck = $objectManager->create('Magento\Catalog\Model\ProductFactory')->create();
						  $productId = $productCheck->getIdBySku($product_sku);
						}
						$qty_format_number = number_format($tmp[1], 4, '.', '');
						
						if($this->getEdition() == "Community") { 
							$select_qry =$connection->query("select value_id from `".$_catalog_product_entity_tier_price."` WHERE `entity_id` = '".$productId."' AND `qty` = '".$qty_format_number."' AND `all_groups` = 1 AND website_id = '".$website_id."'");
						} else {
							$_catalog_product_entity = $this->_resource->getTableName('catalog_product_entity');
							$select_qryRowId =$connection->query("select row_id from `".$_catalog_product_entity."` WHERE `entity_id` = '".$productId."'");
							$newrowId = $select_qryRowId->fetch();
							$productRowId = $newrowId['row_id'];
							$select_qry =$connection->query("select value_id from `".$_catalog_product_entity_tier_price."` WHERE `row_id` = '".$productRowId."' AND `qty` = '".$qty_format_number."' AND `all_groups` = 1 AND website_id = '".$website_id."'");
						}
						 $newrow = $select_qry->fetch();
						 $newGroupId = $newrow['value_id'];
						 if($newGroupId != "") {
							$connection->query("
							  UPDATE ".$_catalog_product_entity_tier_price." val
							  SET  val.value = $tmp[2], val.qty = $tmp[1]
							  WHERE val.value_id = '".$newGroupId."'
							");
						} else {
							
							if($this->getEdition() == "Community") { 
								$connection->query("Insert INTO `".$_catalog_product_entity_tier_price."` (entity_id,all_groups,customer_group_id,qty,value,website_id) VALUES ('$productId','1','0','".$tmp[1]."','".$tmp[2]."','".$website_id."')");
							} else {
								$connection->query("Insert INTO `".$_catalog_product_entity_tier_price."` (row_id,all_groups,customer_group_id,qty,value,website_id) VALUES ('$productRowId','1','0','".$tmp[1]."','".$tmp[2]."','".$website_id."')");
							}
						
						}
					}
	
					if ($params['append_tier_prices'] != "true") {
						//drop any existing tier values by qty
						if (isset($etp_lookup[intval($tmp[0]) . "_" . intval($tmp[1])])) {
							unset($existing_tps[$etp_lookup[intval($tmp[0]) . "_" . intval($tmp[1])]]);
						}
					}
				}
	
				//remove the non matches them if we are not appending
				if ($params['append_tier_prices'] != "true") {
					foreach ($existing_tps as $key => $etp) {
						$this->tierPrice->remove($product_sku, $etp['cust_group'], $etp['price_qty']);
					}
				}
			}
		}

        return $tps_toAdd;
    }

    protected function addCategories($categories, $storeId, $params)
    {
		if($categories!="") {
			//$rootId = $store->getRootCategoryId();
			//$rootId = Mage::app()->getStore()->getRootCategoryId();
			//$rootId = 2; // our store's root category id
			$objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
			$delimitertouse = "/";
			if ($params['root_catalog_id'] != "") {
				$rootId = $params['root_catalog_id'];
			} else {
				$rootId = 2;
			}
			if (!$rootId) {
				return [];
			}
			$rootPath = '1/' . $rootId;
			if (empty($this->_categoryCache[$storeId])) {
	
				$collection = $objectManager->create('Magento\Catalog\Model\Category')->getCollection()
											->setStoreId($storeId)
											->addAttributeToSelect('name');
				/*
				$collection = Mage::getModel('catalog/category')->getCollection()
					->setStore($store)
					->addAttributeToSelect('name');
				*/
				$collection->getSelect()->where("path like '" . $rootPath . "/%'");
	
				foreach ($collection as $cat) {
					$pathArr  = explode('/', $cat->getPath());
					$namePath = '';
					for ($i = 2, $l = sizeof($pathArr); $i < $l; $i++) {
						if(!is_null($collection->getItemById($pathArr[$i]))) {
						//if($categoryLookup = $collection->getItemById($pathArr[$i])) {
							$categoryLookup = $collection->getItemById($pathArr[$i]);
							$name     = $categoryLookup->getName();
							$namePath .= (empty($namePath) ? '' : '/') . trim($name);
						}
					}
					$cat->setNamePath($namePath);
				}
	
				$cache = [];
				foreach ($collection as $cat) {
					$cache[$cat->getNamePath()] = $cat;
					$cat->unsNamePath();
				}
				$this->_categoryCache[$storeId] = $cache;
			}
			$cache =& $this->_categoryCache[$storeId];
	
			$catIds = [];
			//->setIsAnchor(1)
			//Delimiter is ' , ' so people can use ', ' in multiple categorynames
			foreach (explode(' , ', $categories) as $categoryPathStr) {
				//Remove this line if your using ^ vs / as delimiter for categories.. fix for cat names with / in them
				$categoryPathStr = preg_replace('#\s*/\s*#', '/', trim($categoryPathStr));
				if (!empty($cache[$categoryPathStr])) {
					$catIds[] = $cache[$categoryPathStr]->getId();
					continue;
				}
				$path     = $rootPath;
				$namePath = '';
				#foreach (explode($delimitertouse, $categoryPathStr) as $catName) {
				foreach (explode('/', $categoryPathStr) as $catName) {
					$namePath .= (empty($namePath) ? '' : '/') . $catName;
					if (empty($cache[$namePath])) {
						if ($params['auto_create_categories'] == "true") {
							$cat              = $objectManager->create('Magento\Catalog\Model\Category')
															  ->setStoreId($storeId)
															  ->setPath($path)
															  ->setName($catName)
															  ->setIsActive(1)
															  ->save();
							$cache[$namePath] = $cat;
							$catId = $cache[$namePath]->getId();
							$path  .= '/' . $catId;
						} else {
							$catId = false;
						}
					} else {
						$catId = $cache[$namePath]->getId();
						$path  .= '/' . $catId;
					}
					if ($catId) {
						$catIds[] = $catId;
					}
				}
			}

        	return join(',', $catIds);
		} else {
			return "";
		}
    }

    public function attributeSetNamebyid($attributeSetName)
    {

        /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attributeSetCollection */
        $objectManager          = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $entityType             = $objectManager->create('\Magento\Eav\Model\Entity\Type')->loadByCode('catalog_product');
        $attributeSetCollection = $objectManager->create('\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection');
        $attributeSetCollection->addFilter('attribute_set_name', $attributeSetName);
        $attributeSetCollection->addFilter('entity_type_id', $entityType->getId());
        $attributeSetCollection->setOrder('attribute_set_id'); // descending is default value
        $attributeSetCollection->setPageSize(1);
        $attributeSetCollection->load();
        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
        $attributeSet = $attributeSetCollection->fetchItem();
        if ($attributeSet) {
            return $attributeSet->getId();
        } else {
			if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
				$cronLogErrors[] = array("import_products", "Attribute Set " . $attributeSetName . " does NOT exist.", "ERROR: You Must Create Attribute Set First");
				$this->helper->writeToCsv($cronLogErrors);	
			} else {
           		 throw new \Magento\Framework\Exception\LocalizedException(__('Attribute Set "' . $attributeSetName . '" does NOT exist.'));
			}
        }
    }

    public function websitenamebyid($params, $webid, $product_sku, $newProduct)
    {

        if ($params['append_websites'] == "true" && !$newProduct) {
            $productWebsites = $this->_ProductModel->loadByAttribute('sku', $product_sku);
            try {
                $websiteIds = $productWebsites->getWebsiteIds();
            } catch (\Exception $e) {
				
        		if ($this->helper->getStoreConfig('productimportexport/allowdebuglog/enabled', 0)){
					$cronLogErrors[] = array("import_products", "SKU: " . $product_sku . " is not assoicated to any website. Please check product or set append websites false", "ERROR: " . $e->getMessage());
					$this->helper->writeToCsv($cronLogErrors);	
				} else {
                	throw new \Magento\Framework\Exception\LocalizedException(__("SKU: " . $product_sku . " is not assoicated to any website. Please check product or set append websites false"), $e);
				}
            }
            if (!is_array($websiteIds)) {
                $websiteIds = [];
            }
        } else {
            $websiteIds = [];
        }
        if ($webid != "") {
            $webidX = explode(',', $webid);
            foreach ($webidX as $webids) {
                $website = $this->website->load($webids);
                if (!in_array($website->getId(), $websiteIds)) {
                    $websiteIds[] = $website->getId();
                }
            }
        } else {
            $websiteIds = [];
            $this->helper->sendLog($this->helper->rowCount, 'websites', 'The column is empty. If the intended result is to remove the product from all websites then disregard');
        }

        return $websiteIds;
    }

	public function getEdition()
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$magentoVersion = $objectManager->create('Magento\Framework\App\ProductMetadataInterface');
        return $magentoVersion->getEdition();
    }
	
    /**
     * Checks to see if we have already stored option values for the attribute
     *
     * @param string $attributeCode
     *
     * @return bool
     */
    private function hasStoredOptionValuesForAttribute($attributeCode)
    {
        return array_key_exists($attributeCode, $this->optionValues);
    }

    /**
     * Creates a placeholder array for the attribute that we will store values for
     *
     * @param $attributeCode
     *
     * @return $this
     */
    private function initAttributeOptionValues($attributeCode)
    {
        if (!$this->hasStoredOptionValuesForAttribute($attributeCode)) {
            $this->optionValues[$attributeCode] = [];
        }

        return $this;
    }

    /**
     * Stores the attribute option value so we don't have to pull from db again
     *
     * @param string $attributeCode
     * @param string $optionValue
     *
     * @return $this
     */
    private function addAttributeOptionValue($attributeCode, $optionValue)
    {
        $this->initAttributeOptionValues($attributeCode);

        $optionValue = strtolower($optionValue);
        if (!in_array($optionValue, $this->optionValues[$attributeCode])) {
            $this->optionValues[$attributeCode][] = $optionValue;
        }

        return $this;
    }

    /**
     * Pulls all options for a specific attribute and stores them
     *
     * @param string $attributeCode
     *
     * @return $this
     */
    private function addAttributeOptionValues($attributeCode)
    {
        if (!$this->hasStoredOptionValuesForAttribute($attributeCode)) {
			$this->initAttributeOptionValues($attributeCode);
            $values = $this->optionManager->getItems($attributeCode);
            foreach ($values as $value) {
                if (trim($value['label']) !== '') {
                    $this->addAttributeOptionValue($attributeCode, $value['label']);
                }
            }
        }

        return $this;
    }

    /**
     * Checks to see if the attribute option value already exists
     *
     * @param string $attributeCode
     * @param string $optionValue
     *
     * @return bool
     */
    private function checkAttributeOptionValue($attributeCode, $optionValue)
    {
        $this->addAttributeOptionValues($attributeCode);

        return in_array(strtolower($optionValue), $this->optionValues[$attributeCode]);
    }
}