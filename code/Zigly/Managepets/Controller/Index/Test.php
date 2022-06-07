<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Test extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    protected $session;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Zigly\Login\Helper\Smsdata $data
        )
    {
        parent::__construct($context);
        $this->helper = $data;
    }
    public function execute()
    {

        //$data = $this->helper->cmsgcheckbalance();
        //echo "<pre>";print_r($data);
        // echo 'User IP Address - '. $_SERVER['REMOTE_ADDR']."<br/>";
        // exit();
        // echo 'User IP Address - '. $_SERVER['SERVER_ADDR']."<br/>";
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        // $file  =  $directory->getRoot()."/zig.csv";
        // $handle = fopen($file, "r");
        // $i = 0;
        // $attrbutesetnames= [];
        // $attrbutesetcreaionsonly = [];
        // if (empty($handle) === false) {
        //     while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        //         //echo "<pre>";print_r($data);
        //         if($i !=0){
        //             $setnamedd = str_replace(" ", "", $data[0])."_".str_replace(" ", "", $data[1])."_".str_replace(" ", "", $data[2]);
        //             $attrbutesetcreaionsonly[] = $setnamedd;
        //             $attrbutesassigns[$setnamedd] =[];
        //             //$attrbutesetname[$i]['attrbutesetname'] = $setnamedd;
        //             $sets =[3,6,9,12,15,18,21,24];
        //             foreach($sets as $s => $keu){
        //                 if(isset($data[$keu]) && $data[$keu] && isset($data[$keu+1]) && $data[$keu+1] && isset($data[$keu+2]) && $data[$keu+2]){
        //                     $setname = $setnamedd."_".$data[$keu];
        //                     $keyd =strtolower($setname);
        //                     $keyd = str_replace(" ", "", $keyd);
        //                     $keyd = str_replace(",", "_", $keyd);
        //                     $keyd = str_replace("&", "_", $keyd);
        //                     $keyd = str_replace("(willbeinvisible_multimapfeature)", "", $keyd);
        //                     $keyd = str_replace("__", "_", $keyd);
        //                     $keyd = str_replace("-", "", $keyd);
        //                     $keyd= str_replace("ticks_f", "f", $keyd);
        //                     $keyd= str_replace("dogclothingetc_c", "dogclothing_c", $keyd);
        //                     $attrbutesassigns[$setnamedd][]=$keyd;

        //                     //$attrbutesetcreaionsonly[] = $keyd;
        //                     if(strlen($keyd) <= 60){
        //                         //echo strlen($keyd)."<br/>";
        //                         $attrbutesetname[$keyd]['label'] = $data[$keu];

        //                         //echo str_replace(" ", "", strtolower($data[$keu+2]));exit();
        //                         if(str_replace(" ", "", strtolower($data[$keu+2])) == 'single'){
        //                             $attrbutesetname[$keyd]['type'] = 'select';
        //                         }
        //                         if(str_replace(" ", "", strtolower($data[$keu+2])) == 'multiselect'){
        //                             $attrbutesetname[$keyd]['type'] = 'multiselect';
        //                         }
        //                         $attrbutesetname[$keyd]['values'] = explode(",",$data[$keu+1]);
        //                     }

        //                 }
        //             }


        //         }
        //         $i++;
        //     }
        //     fclose($handle);
        //   }

        //echo "<pre>";print_r($attrbutesassigns);exit();
         //attribute creation
        // $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();

        // $eavSetup = $objectManager->get('Magento\Eav\Setup\EavSetupFactory');
        // $setup = $objectManager->get('Magento\Framework\Setup\ModuleDataSetupInterface');

        // $eavSetup1 = $eavSetup->create(['setup' => $setup]);

        // $attribute = array('Firstattributename');
        // $variable = array(array('Dropdown1','Dropdown12'));

        // foreach ($attrbutesetname as  $key=>$value) {
        //     $eavSetup1->addAttribute(
        //                 \Magento\Catalog\Model\Product::ENTITY,
        //                 $key,
        //                 [
        //                     'type' => 'int',
        //                     'backend' => '',
        //                     'frontend' => '',
        //                     'label' => $value['label'],
        //                     'input' => $value['type'],
        //                     'class' => '',
        //                     'source' => '',
        //                     'global' => 0,
        //                     'visible' => true,
        //                     'required' => false,
        //                     'user_defined' => true,
        //                     'default' => null,
        //                     'searchable' => true,
        //                     'filterable' => true,
        //                     'comparable' => true,
        //                     'visible_on_front' => true,
        //                     'is_filterable_in_search' =>true,
        //                     'used_in_product_listing' => true,
        //                     'unique' => false,
        //                     'apply_to' => '',
        //                     'system' => 1,
        //                     'group' => '',
        //                     'option' => ['values' => $value['values']]
        //                 ]
        //             );
        // }

        //attribute set creation
        // $i= 200;
        // foreach($attrbutesetcreaionsonly as $atsetkey =>$attsetvalue){
        //     $i++;
        //     //echo $atsetkey."=>".$attsetvalue."<br/>";
        //     //if($atsetkey > 5){
        //         $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        //         $categorySetupFactory = $objectManager->get('Magento\Catalog\Setup\CategorySetupFactory');
        //         $attributeSetFactory = $objectManager->get('Magento\Eav\Model\Entity\Attribute\SetFactory');
        //         $setup = $objectManager->get('Magento\Framework\Setup\ModuleDataSetupInterface');
        //         $categorySetup = $categorySetupFactory->create(['setup' => $setup]);
        //         $attributeSet = $attributeSetFactory->create();
        //         $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        //         $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
        //         $data = [
        //                'attribute_set_name' => $attsetvalue,
        //                'entity_type_id' => $entityTypeId,
        //                'sort_order' => $i,
        //         ];
        //        $attributeSet->setData($data);
        //        $attributeSet->validate();
        //        $attributeSet->save();
        //        $attributeSet->initFromSkeleton($attributeSetId);
        //        $attributeSet->save();
        //     //}
        // }

        //assign attributeset
        // foreach($attrbutesassigns as $assignkey => $assignvalue){
        //     $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        //     $eavSetup = $objectManager->create(\Magento\Eav\Setup\EavSetup::class);
        //     $config = $objectManager->get(\Magento\Catalog\Model\Config::class);
        //     $attributeManagement = $objectManager->get(\Magento\Eav\Api\AttributeManagementInterface::class);
        //     $attributeSetCollection = $objectManager->get('Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory');
        //     $attributeSetCollection = $attributeSetCollection->create()
        //       ->addFieldToSelect('attribute_set_id')
        //       ->addFieldToFilter('attribute_set_name', $assignkey)
        //       ->getFirstItem()
        //       ->toArray();
        //     $attributeGroupCode = "Key Features";
        //     $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        //     $group_id = $config->getAttributeGroupId($attributeSetCollection['attribute_set_id'], $attributeGroupCode);
        //     if(!$group_id){
        //         $eavSetup->addAttributeGroup(
        //             $entityTypeId,
        //             $attributeSetCollection['attribute_set_id'],
        //             $attributeGroupCode,
        //             2
        //         );
        //          $group_id = $eavSetup->getAttributeGroupId(
        //             $entityTypeId,
        //             $attributeSetCollection['attribute_set_id'],
        //             $attributeGroupCode
        //         );
        //     }

        //     if(isset($attributeSetCollection['attribute_set_id']) && $attributeSetCollection['attribute_set_id'] && $group_id){
        //         foreach($assignvalue as $k => $v){
        //             echo $v."<br/>";
        //              $attributeManagement->assign(
        //                 'catalog_product',
        //                 $attributeSetCollection['attribute_set_id'],
        //                 $group_id,
        //                 $v,
        //                 999
        //            );
        //         }
        //     }
        // }
        //assign to all
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $eavSetup = $objectManager->create(\Magento\Eav\Setup\EavSetup::class);
        $config = $objectManager->get(\Magento\Catalog\Model\Config::class);
        $attributeManagement = $objectManager->get(\Magento\Eav\Api\AttributeManagementInterface::class);

        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);
        foreach ($attributeSetIds as $attributeSetId) {
            if ($attributeSetId) {
                $attributeGroupCode = "general";
                $group_id = $config->getAttributeGroupId($attributeSetId, $attributeGroupCode);
                 if(!$group_id){
                    $eavSetup->addAttributeGroup(
                        $entityTypeId,
                        $attributeSetId,
                        $attributeGroupCode,
                        2
                    );
                     $group_id = $eavSetup->getAttributeGroupId(
                        $entityTypeId,
                        $attributeSetId,
                        $attributeGroupCode
                    );
                }
                $attributeManagement->assign(
                    'catalog_product',
                    $attributeSetId,
                    $group_id,
                    'show_at_home',
                    1001
                );
                $attributeManagement->assign(
                    'catalog_product',
                    $attributeSetId,
                    $group_id,
                    'show_at_experience',
                    1002
                );
                // Benefits - (z_benefits) (multiselect)
                $attributeManagement->assign(
                    'catalog_product',
                    $attributeSetId,
                    $group_id,
                    'z_benefits',
                    900
                );
                $allAttributes = $attributeManagement->getAttributes('4', $attributeSetId);
                if (in_array('cats_cathealth_supplements_benefits', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                        'cats_cathealth_supplements_benefits'
                    );
                }
                if (in_array('dogs_dogfood_dryfood_benefits', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                        'dogs_dogfood_dryfood_benefits'
                    );
                }
                if (in_array('dogs_dogfood_wetfood_benefits', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                        'dogs_dogfood_wetfood_benefits'
                    );
                }
                if (in_array('dogs_doggrooming_shampoos_conditioners_benefits', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                        'dogs_doggrooming_shampoos_conditioners_benefits'
                    );
                }
                if (in_array('dogs_doggrooming_skin_coatcare_benefits', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                        'dogs_doggrooming_skin_coatcare_benefits'
                    );
                }
                if (in_array('dogs_doghealth_oral_dentalcare_benefits', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                        'dogs_doghealth_oral_dentalcare_benefits'
                    );
                }
                if (in_array('dogs_doghealth_supplements_benefits', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                        'dogs_doghealth_supplements_benefits'
                    );
                }

                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_breed',
                    901
                );
                if (in_array('cats_food_dryfood_breed', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_dryfood_breed'
                    );
                }
                if (in_array('dogs_dogfood_dryfood_breed', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_dryfood_breed'
                    );
                }
                if (in_array('dogs_dogfood_wetfood_breed', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_wetfood_breed'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_trainingleash_breed', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_trainingleash_breed'
                    );
                }
                if (in_array('cats_food_prescriptionfood_breed', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_prescriptionfood_breed'
                    );
                }
                if (in_array('cats_food_wetfood_breed', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_wetfood_breed'
                    );
                }
                if (in_array('cats_treats_meatytreat_breed', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_treats_meatytreat_breed'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_breedsize',
                    902
                );
                if (in_array('dogs_dogtoys_squeakertoys_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_squeakertoys_breedsize'
                    );
                }
                if (in_array('dogs_dogfood_dryfood_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_dryfood_breedsize'
                    );
                }
                if (in_array('dogs_dogfood_weaningfood_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_weaningfood_breedsize'
                    );
                }
                if (in_array('dogs_dogfood_wetfood_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_wetfood_breedsize'
                    );
                }
                if (in_array('dogs_doghealth_trainingsupplies_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_trainingsupplies_breedsize'
                    );
                }
                if (in_array('dogs_dogtoys_chewtoys_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_chewtoys_breedsize'
                    );
                }
                if (in_array('dogs_dogtoys_fetchtoys_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_fetchtoys_breedsize'
                    );
                }
                if (in_array('dogs_dogtoys_interactivetoys_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_interactivetoys_breedsize'
                    );
                }
                if (in_array('dogs_dogtoys_plushtoys_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_plushtoys_breedsize'
                    );
                }
                if (in_array('dogs_dogtoys_rope_tugtoys_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_rope_tugtoys_breedsize'
                    );
                }
                if (in_array('dogs_dogclothing_tshirts_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_tshirts_breedsize'
                    );
                }
                if (in_array('dogs_dogtreats_biscuits_cookies_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_biscuits_cookies_breedsize'
                    );
                }
                if (in_array('dogs_dogtreats_meatytreats_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_meatytreats_breedsize'
                    );
                }
                if (in_array('dogs_fleasolution_fleacollars_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_fleasolution_fleacollars_breedsize'
                    );
                }
                if (in_array('dogs_fleasolution_spotontreatments_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_fleasolution_spotontreatments_breedsize'
                    );
                }
                if (in_array('dogs_kennels_carriers_cages_houses_barrier_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_cages_houses_barrier_breedsize'
                    );
                }
                if (in_array('dogs_kennels_carriers_petcarrier_crates_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_petcarrier_crates_breedsize'
                    );
                }
                if (in_array('dogs_kennels_carriers_travel_carrybags_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_travel_carrybags_breedsize'
                    );
                }
                if (in_array('dogs_ticks_fleasolution_spotontreatments_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_ticks_fleasolution_spotontreatments_breedsize'
                    );
                }
                if (in_array('dogs_ticks_fleasolution_ticks_fleacollars_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_ticks_fleasolution_ticks_fleacollars_breedsize'
                    );
                }
                if (in_array('dogs_dogaccessories_bandana_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_bandana_breedsize'
                    );
                }
                if (in_array('dogs_beds_mats_blankets_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_blankets_breedsize'
                    );
                }
                if (in_array('dogs_beds_mats_mats_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_mats_breedsize'
                    );
                }
                if (in_array('dogs_bowls_feedersandfoodmats_bowls_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_bowls_feedersandfoodmats_bowls_breedsize'
                    );
                }
                if (in_array('dogs_cleaning_odourcontrol_diaper_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_cleaning_odourcontrol_diaper_breedsize'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_collars_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_collars_breedsize'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_harnesses_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_harnesses_breedsize'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_leashcollarset_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_leashcollarset_breedsize'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_leashes_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_leashes_breedsize'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_retractableleash_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_retractableleash_breedsize'
                    );
                }
                if (in_array('breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'breedsize'
                    );
                }
                if (in_array('dogs_dogaccessories_bowties_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_bowties_breedsize'
                    );
                }
                if (in_array('dogs_dogaccessories_shoes_socks_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_shoes_socks_breedsize'
                    );
                }
                if (in_array('dogs_dogaccessories_travelgear_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_travelgear_breedsize'
                    );
                }
                if (in_array('dogs_dogclothing_coats_jackets_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_coats_jackets_breedsize'
                    );
                }
                if (in_array('dogs_dogclothing_coolingcoats_vests_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_coolingcoats_vests_breedsize'
                    );
                }
                if (in_array('dogs_dogclothing_dresses_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_dresses_breedsize'
                    );
                }
                if (in_array('dogs_dogclothing_raincoats_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_raincoats_breedsize'
                    );
                }
                if (in_array('dogs_dogclothing_sweatshirts_sweaters_breedsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_sweatshirts_sweaters_breedsize'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_chewtype',
                    903
                );
                if (in_array('dogs_dogtreats_calcium_milkchews_chewtype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_calcium_milkchews_chewtype'
                    );
                }
                if (in_array('dogs_dogtreats_dentaltreats_chews_chewtype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_dentaltreats_chews_chewtype'
                    );
                }
                if (in_array('dogs_dogtreats_meatytreats_chewtype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_meatytreats_chewtype'
                    );
                }
                if (in_array('dogs_dogtreats_trainingtreats_chewtype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_trainingtreats_chewtype'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_color',
                    904
                );
                if (in_array('dogs_dogaccessories_shoes_socks_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_shoes_socks_color'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_harnesses_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_harnesses_color'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_leashcollarset_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_leashcollarset_color'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_leashes_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_leashes_color'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_pettag_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_pettag_color'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_retractableleash_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_retractableleash_color'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_trainingleash_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_trainingleash_color'
                    );
                }
                if (in_array('dogs_dogaccessories_bandana_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_bandana_color'
                    );
                }
                if (in_array('dogs_dogaccessories_bowties_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_bowties_color'
                    );
                }
                if (in_array('dogs_dogaccessories_partyaccessories_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_partyaccessories_color'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_collars_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_collars_color'
                    );
                }
                if (in_array('dogs_dogaccessories_travelgear_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_travelgear_color'
                    );
                }
                if (in_array('dogs_dogclothing_sweatshirts_sweaters_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_sweatshirts_sweaters_color'
                    );
                }
                if (in_array('dogs_dogtoys_chewtoys_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_chewtoys_color'
                    );
                }
                if (in_array('dogs_dogtoys_fetchtoys_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_fetchtoys_color'
                    );
                }
                if (in_array('dogs_dogtoys_interactivetoys_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_interactivetoys_color'
                    );
                }
                if (in_array('dogs_dogtoys_plushtoys_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_plushtoys_color'
                    );
                }
                if (in_array('dogs_dogtoys_rope_tugtoys_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_rope_tugtoys_color'
                    );
                }
                if (in_array('dogs_dogtoys_squeakertoys_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_squeakertoys_color'
                    );
                }
                if (in_array('cats_cattoys_chewtoys_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cattoys_chewtoys_color'
                    );
                }
                if (in_array('cats_catbeds_crates_beds_crates_carriers_tent_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbeds_crates_beds_crates_carriers_tent_color'
                    );
                }
                if (in_array('cats_catbeds_crates_furniture_scratchers_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbeds_crates_furniture_scratchers_color'
                    );
                }
                if (in_array('cats_catbowls_feeders_automaticcatfeeders_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_automaticcatfeeders_color'
                    );
                }
                if (in_array('cats_catbowls_feeders_catbowls_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_catbowls_color'
                    );
                }
                if (in_array('cats_catbowls_feeders_catdiners_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_catdiners_color'
                    );
                }
                if (in_array('cats_catbowls_feeders_catfoodstorage_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_catfoodstorage_color'
                    );
                }
                if (in_array('cats_catcollars_leashes_harnesses_collars_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catcollars_leashes_harnesses_collars_color'
                    );
                }
                if (in_array('cats_catcollars_leashes_harnesses_harnesses_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catcollars_leashes_harnesses_harnesses_color'
                    );
                }
                if (in_array('cats_catcollars_leashes_harnesses_leashes_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catcollars_leashes_harnesses_leashes_color'
                    );
                }
                if (in_array('cats_cataccessories_bowties_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cataccessories_bowties_color'
                    );
                }
                if (in_array('cats_cattoys_interactivetoys_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cattoys_interactivetoys_color'
                    );
                }
                if (in_array('cats_cattoys_plushtoys_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cattoys_plushtoys_color'
                    );
                }
                if (in_array('cats_cattoys_scratchers_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cattoys_scratchers_color'
                    );
                }
                if (in_array('cats_furniture_scratchers_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_furniture_scratchers_color'
                    );
                }
                if (in_array('color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'color'
                    );
                }
                if (in_array('dogs_beds_mats_beds_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_beds_color'
                    );
                }
                if (in_array('dogs_beds_mats_blankets_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_blankets_color'
                    );
                }
                if (in_array('dogs_beds_mats_mats_color', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_mats_color'
                    );
                }
                if (in_array('dogs_dogclothing_coats_jackets_colour', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_coats_jackets_colour'
                    );
                }
                if (in_array('dogs_dogclothing_coolingcoats_vests_colour', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_coolingcoats_vests_colour'
                    );
                }
                if (in_array('dogs_dogclothing_dresses_colour', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_dresses_colour'
                    );
                }
                if (in_array('dogs_dogclothing_raincoats_colour', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_raincoats_colour'
                    );
                }
                if (in_array('dogs_dogclothing_sweatshirts_sweaters_colour', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_sweatshirts_sweaters_colour'
                    );
                }
                if (in_array('dogs_dogclothing_tshirts_colour', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_tshirts_colour'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_concern',
                    905
                );
                if (in_array('cats_cataccessories_bowties_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cataccessories_bowties_concern'
                    );
                }
                if (in_array('cats_catbeds_crates_beds_crates_carriers_tent_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbeds_crates_beds_crates_carriers_tent_concern'
                    );
                }
                if (in_array('cats_catbowls_feeders_automaticcatfeeders_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_automaticcatfeeders_concern'
                    );
                }
                if (in_array('cats_catbowls_feeders_catbowls_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_catbowls_concern'
                    );
                }
                if (in_array('cats_catbowls_feeders_catdiners_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_catdiners_concern'
                    );
                }
                if (in_array('cats_catbowls_feeders_catfoodstorage_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_catfoodstorage_concern'
                    );
                }
                if (in_array('cats_catbowls_feeders_catplacemats_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_catplacemats_concern'
                    );
                }
                if (in_array('cats_catcollars_leashes_harnesses_collars_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catcollars_leashes_harnesses_collars_concern'
                    );
                }
                if (in_array('cats_catcollars_leashes_harnesses_harnesses_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catcollars_leashes_harnesses_harnesses_concern'
                    );
                }
                if (in_array('cats_catcollars_leashes_harnesses_leashes_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catcollars_leashes_harnesses_leashes_concern'
                    );
                }
                if (in_array('cats_catgrooming_brushes_combs_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_brushes_combs_concern'
                    );
                }
                if (in_array('cats_catgrooming_deodorants_perfumes_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_deodorants_perfumes_concern'
                    );
                }
                if (in_array('cats_catgrooming_earcare_eyecare_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_earcare_eyecare_concern'
                    );
                }
                if (in_array('cats_catgrooming_groomingtools_accessories_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_groomingtools_accessories_concern'
                    );
                }
                if (in_array('cats_catgrooming_oral_dentalcare_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_oral_dentalcare_concern'
                    );
                }
                if (in_array('cats_catgrooming_shampoos_conditioners_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_shampoos_conditioners_concern'
                    );
                }
                if (in_array('cats_catgrooming_tick_fleasolutions_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_tick_fleasolutions_concern'
                    );
                }
                if (in_array('cats_catgrooming_towels_wipes_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_towels_wipes_concern'
                    );
                }
                if (in_array('cats_cathealth_supplements_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cathealth_supplements_concern'
                    );
                }
                if (in_array('cats_cattoys_chewtoys_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cattoys_chewtoys_concern'
                    );
                }
                if (in_array('cats_cattoys_interactivetoys_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cattoys_interactivetoys_concern'
                    );
                }
                if (in_array('cats_cattoys_plushtoys_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cattoys_plushtoys_concern'
                    );
                }
                if (in_array('cats_cattoys_scratchers_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cattoys_scratchers_concern'
                    );
                }
                if (in_array('cats_catwaterers_fountains_fountain_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catwaterers_fountains_fountain_concern'
                    );
                }
                if (in_array('cats_catwaterers_fountains_waterer_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catwaterers_fountains_waterer_concern'
                    );
                }
                if (in_array('cats_food_dryfood_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_dryfood_concern'
                    );
                }
                if (in_array('cats_food_prescriptionfood_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_prescriptionfood_concern'
                    );
                }
                if (in_array('cats_food_wetfood_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_wetfood_concern'
                    );
                }
                if (in_array('cats_furniture_scratchers_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_furniture_scratchers_concern'
                    );
                }
                if (in_array('cats_litter_accessories_catlitterscoop_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_litter_accessories_catlitterscoop_concern'
                    );
                }
                if (in_array('cats_litter_accessories_catlittertraymat_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_litter_accessories_catlittertraymat_concern'
                    );
                }
                if (in_array('cats_litter_accessories_catlittertray_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_litter_accessories_catlittertray_concern'
                    );
                }
                if (in_array('cats_litter_accessories_catlitter_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_litter_accessories_catlitter_concern'
                    );
                }
                if (in_array('cats_litter_accessories_stain_odourcontrol_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_litter_accessories_stain_odourcontrol_concern'
                    );
                }
                if (in_array('cats_treats_meatytreat_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_treats_meatytreat_concern'
                    );
                }
                if (in_array('concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'concern'
                    );
                }
                if (in_array('dogs_beds_mats_beds_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_beds_concern'
                    );
                }
                if (in_array('dogs_beds_mats_blankets_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_blankets_concern'
                    );
                }
                if (in_array('dogs_beds_mats_mats_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_mats_concern'
                    );
                }
                if (in_array('dogs_bowls_feedersandfoodmats_bowls_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_bowls_feedersandfoodmats_bowls_concern'
                    );
                }
                if (in_array('dogs_bowls_feedersandfoodmats_foodmats_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_bowls_feedersandfoodmats_foodmats_concern'
                    );
                }
                if (in_array('dogs_cleaning_odourcontrol_airfreshner_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_cleaning_odourcontrol_airfreshner_concern'
                    );
                }
                if (in_array('dogs_cleaning_odourcontrol_diaper_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_cleaning_odourcontrol_diaper_concern'
                    );
                }
                if (in_array('dogs_cleaning_odourcontrol_lintremover_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_cleaning_odourcontrol_lintremover_concern'
                    );
                }
                if (in_array('dogs_cleaning_odourcontrol_odor_stainremovers_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_cleaning_odourcontrol_odor_stainremovers_concern'
                    );
                }
                if (in_array('dogs_cleaning_odourcontrol_pooperscoopers_bags_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_cleaning_odourcontrol_pooperscoopers_bags_concern'
                    );
                }
                if (in_array('dogs_cleaning_odourcontrol_pottytrainingtrays_pads_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_cleaning_odourcontrol_pottytrainingtrays_pads_concern'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_collars_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_collars_concern'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_harnesses_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_harnesses_concern'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_leashcollarset_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_leashcollarset_concern'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_leashes_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_leashes_concern'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_pettag_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_pettag_concern'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_retractableleash_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_retractableleash_concern'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_trainingleash_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_trainingleash_concern'
                    );
                }
                if (in_array('dogs_dogaccessories_bandana_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_bandana_concern'
                    );
                }
                if (in_array('dogs_dogaccessories_bowties_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_bowties_concern'
                    );
                }
                if (in_array('dogs_dogaccessories_partyaccessories_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_partyaccessories_concern'
                    );
                }
                if (in_array('dogs_dogaccessories_shoes_socks_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_shoes_socks_concern'
                    );
                }
                if (in_array('dogs_dogaccessories_travelgear_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_travelgear_concern'
                    );
                }
                if (in_array('dogs_dogclothing_coats_jackets_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_coats_jackets_concern'
                    );
                }
                if (in_array('dogs_dogclothing_coolingcoats_vests_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_coolingcoats_vests_concern'
                    );
                }
                if (in_array('dogs_dogclothing_dresses_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_dresses_concern'
                    );
                }
                if (in_array('dogs_dogclothing_raincoats_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_raincoats_concern'
                    );
                }
                if (in_array('dogs_dogclothing_sweatshirts_sweaters_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_sweatshirts_sweaters_concern'
                    );
                }
                if (in_array('dogs_dogclothing_tshirts_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_tshirts_concern'
                    );
                }
                if (in_array('dogs_dogfood_dryfood_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_dryfood_concern'
                    );
                }
                if (in_array('dogs_dogfood_prescriptiondogfood_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_prescriptiondogfood_concern'
                    );
                }
                if (in_array('dogs_dogfood_weaningfood_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_weaningfood_concern'
                    );
                }
                if (in_array('dogs_dogfood_wetfood_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_wetfood_concern'
                    );
                }
                if (in_array('dogs_doggrooming_groomingtools_accessories_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doggrooming_groomingtools_accessories_concern'
                    );
                }
                if (in_array('dogs_doggrooming_paw_nailcare_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doggrooming_paw_nailcare_concern'
                    );
                }
                if (in_array('dogs_doggrooming_shampoos_conditioners_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doggrooming_shampoos_conditioners_concern'
                    );
                }
                if (in_array('dogs_doggrooming_skin_coatcare_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doggrooming_skin_coatcare_concern'
                    );
                }
                if (in_array('dogs_doggrooming_towels_wipes_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doggrooming_towels_wipes_concern'
                    );
                }
                if (in_array('dogs_doghealth_dewormer_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_dewormer_concern'
                    );
                }
                if (in_array('dogs_doghealth_eye_earcare_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_eye_earcare_concern'
                    );
                }
                if (in_array('dogs_doghealth_firstaid_recovery_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_firstaid_recovery_concern'
                    );
                }
                if (in_array('dogs_doghealth_medicine_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_medicine_concern'
                    );
                }
                if (in_array('dogs_doghealth_oral_dentalcare_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_oral_dentalcare_concern'
                    );
                }
                if (in_array('dogs_doghealth_supplements_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_supplements_concern'
                    );
                }
                if (in_array('dogs_doghealth_support_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_support_concern'
                    );
                }
                if (in_array('dogs_doghealth_trainingsupplies_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_trainingsupplies_concern'
                    );
                }
                if (in_array('dogs_dogtoys_chewtoys_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_chewtoys_concern'
                    );
                }
                if (in_array('dogs_dogtoys_fetchtoys_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_fetchtoys_concern'
                    );
                }
                if (in_array('dogs_dogtoys_interactivetoys_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_interactivetoys_concern'
                    );
                }
                if (in_array('dogs_dogtoys_plushtoys_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_plushtoys_concern'
                    );
                }
                if (in_array('dogs_dogtoys_rope_tugtoys_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_rope_tugtoys_concern'
                    );
                }
                if (in_array('dogs_dogtoys_squeakertoys_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_squeakertoys_concern'
                    );
                }
                if (in_array('dogs_dogtreats_biscuits_cookies_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_biscuits_cookies_concern'
                    );
                }
                if (in_array('dogs_dogtreats_calcium_milkchews_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_calcium_milkchews_concern'
                    );
                }
                if (in_array('dogs_dogtreats_dentaltreats_chews_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_dentaltreats_chews_concern'
                    );
                }
                if (in_array('dogs_dogtreats_meatytreats_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_meatytreats_concern'
                    );
                }
                if (in_array('dogs_dogtreats_trainingtreats_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_trainingtreats_concern'
                    );
                }
                if (in_array('dogs_fleasolution_fleacollars_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_fleasolution_fleacollars_concern'
                    );
                }
                if (in_array('dogs_fleasolution_fleacombs_accessories_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_fleasolution_fleacombs_accessories_concern'
                    );
                }
                if (in_array('dogs_fleasolution_flealiquids_sprays_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_fleasolution_flealiquids_sprays_concern'
                    );
                }
                if (in_array('dogs_fleasolution_fleapowder_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_fleasolution_fleapowder_concern'
                    );
                }
                if (in_array('dogs_fleasolution_fleashampoos_conditioners_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_fleasolution_fleashampoos_conditioners_concern'
                    );
                }
                if (in_array('dogs_fleasolution_fleasoaps_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_fleasolution_fleasoaps_concern'
                    );
                }
                if (in_array('dogs_fleasolution_spotontreatments_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_fleasolution_spotontreatments_concern'
                    );
                }
                if (in_array('dogs_kennels_carriers_cages_houses_barrier_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_cages_houses_barrier_concern'
                    );
                }
                if (in_array('dogs_kennels_carriers_petcarrier_crates_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_petcarrier_crates_concern'
                    );
                }
                if (in_array('dogs_kennels_carriers_travelaid_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_travelaid_concern'
                    );
                }
                if (in_array('dogs_kennels_carriers_travel_carrybags_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_travel_carrybags_concern'
                    );
                }
                if (in_array('dogs_specialoccasionwear_dogaccessories_dogclothing_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_specialoccasionwear_dogaccessories_dogclothing_concern'
                    );
                }
                if (in_array('dogs_ticks_fleasolution_spotontreatments_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_ticks_fleasolution_spotontreatments_concern'
                    );
                }
                if (in_array('dogs_ticks_fleasolution_ticks_fleacollars_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_ticks_fleasolution_ticks_fleacollars_concern'
                    );
                }
                if (in_array('dogs_ticks_fleasolution_ticks_fleacombs_accessories_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_ticks_fleasolution_ticks_fleacombs_accessories_concern'
                    );
                }
                if (in_array('dogs_ticks_fleasolution_ticks_flealiquids_sprays_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_ticks_fleasolution_ticks_flealiquids_sprays_concern'
                    );
                }
                if (in_array('dogs_ticks_fleasolution_ticks_fleapowder_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_ticks_fleasolution_ticks_fleapowder_concern'
                    );
                }
                if (in_array('dogs_ticks_fleasolution_ticks_fleasoaps_concern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_ticks_fleasolution_ticks_fleasoaps_concern'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_diet_type',
                    906
                );
                if (in_array('cats_food_dryfood_diettype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_dryfood_diettype'
                    );
                }
                if (in_array('cats_food_prescriptionfood_diettype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_prescriptionfood_diettype'
                    );
                }
                if (in_array('cats_food_wetfood_diettype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_wetfood_diettype'
                    );
                }
                if (in_array('cats_treats_meatytreat_diettype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_treats_meatytreat_diettype'
                    );
                }
                if (in_array('dogs_dogfood_dryfood_diettype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_dryfood_diettype'
                    );
                }
                if (in_array('dogs_dogfood_weaningfood_diettype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_weaningfood_diettype'
                    );
                }
                if (in_array('dogs_dogfood_wetfood_diettype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_wetfood_diettype'
                    );
                }
                if (in_array('dogs_dogtreats_biscuits_cookies_diettype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_biscuits_cookies_diettype'
                    );
                }
                if (in_array('dogs_dogtreats_calcium_milkchews_diettype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_calcium_milkchews_diettype'
                    );
                }
                if (in_array('dogs_dogtreats_dentaltreats_chews_diettype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_dentaltreats_chews_diettype'
                    );
                }
                if (in_array('dogs_dogtreats_meatytreats_diettype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_meatytreats_diettype'
                    );
                }
                if (in_array('dogs_dogtreats_trainingtreats_diettype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_trainingtreats_diettype'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_features',
                    907
                );
                if (in_array('cats_treats_meatytreat_feature', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_treats_meatytreat_feature'
                    );
                }
                if (in_array('cats_cattoys_interactivetoys_features', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cattoys_interactivetoys_features'
                    );
                }
                if (in_array('cats_cattoys_scratchers_features', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cattoys_scratchers_features'
                    );
                }
                if (in_array('cats_cattoys_plushtoys_features', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cattoys_plushtoys_features'
                    );
                }
                if (in_array('cats_cattoys_chewtoys_features', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cattoys_chewtoys_features'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_flavour',
                    908
                );
                if (in_array('dogs_dogfood_dryfood_flavour', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_dryfood_flavour'
                    );
                }
                if (in_array('dogs_dogfood_wetfood_flavour', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_wetfood_flavour'
                    );
                }
                if (in_array('dogs_dogtreats_biscuits_cookies_flavour', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_biscuits_cookies_flavour'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_form',
                    909
                );
                if (in_array('cats_litter_accessories_catlitter_form', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_litter_accessories_catlitter_form'
                    );
                }
                if (in_array('cats_litter_accessories_stain_odourcontrol_form Form', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_litter_accessories_stain_odourcontrol_form Form'
                    );
                }
                if (in_array('dogs_cleaning_odourcontrol_pooperscoopers_bags_form Form', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_cleaning_odourcontrol_pooperscoopers_bags_form Form'
                    );
                }
                if (in_array('dogs_doghealth_medicine_form', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_medicine_form'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_healthconcern',
                    910
                );
                if (in_array('cats_cathealth_supplements_healthconcern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_cathealth_supplements_healthconcern'
                    );
                }
                if (in_array('dogs_doghealth_medicine_healthconcern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_medicine_healthconcern'
                    );
                }
                if (in_array('dogs_doghealth_supplements_healthconcern', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_supplements_healthconcern'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_lifestage',
                    911
                );
                if (in_array('dogs_dogtreats_biscuits_cookies_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_biscuits_cookies_lifestage'
                    );
                }
                if (in_array('dogs_doghealth_dewormer_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_dewormer_lifestage'
                    );
                }
                if (in_array('dogs_doghealth_medicine_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_medicine_lifestage'
                    );
                }
                if (in_array('dogs_doghealth_oral_dentalcare_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_oral_dentalcare_lifestage'
                    );
                }
                if (in_array('dogs_doghealth_supplements_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_supplements_lifestage'
                    );
                }
                if (in_array('dogs_doghealth_trainingsupplies_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_trainingsupplies_lifestage'
                    );
                }
                if (in_array('dogs_dogtoys_chewtoys_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_chewtoys_lifestage'
                    );
                }
                if (in_array('dogs_dogtoys_fetchtoys_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_fetchtoys_lifestage'
                    );
                }
                if (in_array('dogs_dogtoys_interactivetoys_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_interactivetoys_lifestage'
                    );
                }
                if (in_array('dogs_dogtoys_plushtoys_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_plushtoys_lifestage'
                    );
                }
                if (in_array('dogs_dogtoys_rope_tugtoys_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_rope_tugtoys_lifestage'
                    );
                }
                if (in_array('dogs_dogtoys_squeakertoys_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_squeakertoys_lifestage'
                    );
                }
                if (in_array('dogs_doggrooming_skin_coatcare_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doggrooming_skin_coatcare_lifestage'
                    );
                }
                if (in_array('dogs_dogtreats_calcium_milkchews_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_calcium_milkchews_lifestage'
                    );
                }
                if (in_array('dogs_dogtreats_chocolates_beverages_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_chocolates_beverages_lifestage'
                    );
                }
                if (in_array('dogs_dogtreats_dentaltreats_chews_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_dentaltreats_chews_lifestage'
                    );
                }
                if (in_array('dogs_dogtreats_meatytreats_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_meatytreats_lifestage'
                    );
                }
                if (in_array('dogs_dogtreats_trainingtreats_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_trainingtreats_lifestage'
                    );
                }
                if (in_array('dogs_fleasolution_fleacollars_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_fleasolution_fleacollars_lifestage'
                    );
                }
                if (in_array('dogs_kennels_carriers_cages_houses_barrier_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_cages_houses_barrier_lifestage'
                    );
                }
                if (in_array('dogs_kennels_carriers_petcarrier_crates_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_petcarrier_crates_lifestage'
                    );
                }
                if (in_array('dogs_kennels_carriers_travel_carrybags_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_travel_carrybags_lifestage'
                    );
                }
                if (in_array('dogs_ticks_fleasolution_ticks_fleacollars_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_ticks_fleasolution_ticks_fleacollars_lifestage'
                    );
                }
                if (in_array('lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'lifestage'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_leashes_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_leashes_lifestage'
                    );
                }
                if (in_array('cats_food_dryfood_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_dryfood_lifestage'
                    );
                }
                if (in_array('cats_food_prescriptionfood_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_prescriptionfood_lifestage'
                    );
                }
                if (in_array('cats_food_wetfood_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_wetfood_lifestage'
                    );
                }
                if (in_array('cats_treats_meatytreat_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_treats_meatytreat_lifestage'
                    );
                }
                if (in_array('dogs_beds_mats_beds_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_beds_lifestage'
                    );
                }
                if (in_array('dogs_beds_mats_blankets_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_blankets_lifestage'
                    );
                }
                if (in_array('dogs_beds_mats_mats_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_mats_lifestage'
                    );
                }
                if (in_array('dogs_cleaning_odourcontrol_diaper_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_cleaning_odourcontrol_diaper_lifestage'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_collars_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_collars_lifestage'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_harnesses_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_harnesses_lifestage'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_leashcollarset_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_leashcollarset_lifestage'
                    );
                }
                if (in_array('cats_catgrooming_shampoos_conditioners_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_shampoos_conditioners_lifestage'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_retractableleash_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_retractableleash_lifestage'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_trainingleash_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_trainingleash_lifestage'
                    );
                }
                if (in_array('dogs_dogaccessories_bandana_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_bandana_lifestage'
                    );
                }
                if (in_array('dogs_dogaccessories_bowties_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_bowties_lifestage'
                    );
                }
                if (in_array('dogs_dogaccessories_partyaccessories_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_partyaccessories_lifestage'
                    );
                }
                if (in_array('dogs_dogaccessories_shoes_socks_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_shoes_socks_lifestage'
                    );
                }
                if (in_array('dogs_dogaccessories_travelgear_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_travelgear_lifestage'
                    );
                }
                if (in_array('dogs_dogfood_dryfood_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_dryfood_lifestage'
                    );
                }
                if (in_array('dogs_dogfood_weaningfood_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_weaningfood_lifestage'
                    );
                }
                if (in_array('dogs_dogfood_wetfood_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_wetfood_lifestage'
                    );
                }
                if (in_array('dogs_doggrooming_shampoos_conditioners_lifestage', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doggrooming_shampoos_conditioners_lifestage'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_material',
                    912
                );
                if (in_array('cats_catbowls_feeders_catplacemats_material', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_catplacemats_material'
                    );
                }
                if (in_array('cats_litter_accessories_catlittertraymat_material', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_litter_accessories_catlittertraymat_material'
                    );
                }
                if (in_array('dogs_beds_mats_beds_material', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_beds_material'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_leashcollarset_material', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_leashcollarset_material'
                    );
                }
                if (in_array('dogs_kennels_carriers_cages_houses_barrier_material', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_cages_houses_barrier_material'
                    );
                }
                if (in_array('dogs_kennels_carriers_petcarrier_crates_material', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_petcarrier_crates_material'
                    );
                }
                // $attributeManagement->assign(
                //    'catalog_product',
                //     $attributeSetId,
                //     $group_id,
                //    'z_materialtype',
                //     913
                // );
                if (in_array('z_materialtype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'z_materialtype'
                    );
                }
                if (in_array('cats_catbowls_feeders_automaticcatfeeders_materialtype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_automaticcatfeeders_materialtype'
                    );
                }
                if (in_array('cats_catbowls_feeders_catbowls_materialtype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_catbowls_materialtype'
                    );
                }
                if (in_array('cats_catbowls_feeders_catdiners_materialtype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_catdiners_materialtype'
                    );
                }
                if (in_array('cats_catbowls_feeders_catfoodstorage_materialtype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_catfoodstorage_materialtype'
                    );
                }
                if (in_array('dogs_dogtoys_chewtoys_materialtype', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtoys_chewtoys_materialtype'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_productsize',
                    914
                );
                if (in_array('dogs_doggrooming_groomingtools_accessories_productsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doggrooming_groomingtools_accessories_productsize'
                    );
                }
                if (in_array('dogs_fleasolution_fleacollars_productsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_fleasolution_fleacollars_productsize'
                    );
                }
                if (in_array('dogs_fleasolution_fleacombs_accessories_productsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_fleasolution_fleacombs_accessories_productsize'
                    );
                }
                if (in_array('dogs_ticks_fleasolution_ticks_fleacollars_productsize', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_ticks_fleasolution_ticks_fleacollars_productsize'
                    );
                }
                if (in_array('productsizeattribute', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'productsizeattribute'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_shape',
                    915
                );
                if (in_array('cats_catbeds_crates_beds_crates_carriers_tent_shape', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbeds_crates_beds_crates_carriers_tent_shape'
                    );
                }
                if (in_array('cats_catbeds_crates_furniture_scratchers_shape', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbeds_crates_furniture_scratchers_shape'
                    );
                }
                if (in_array('dogs_beds_mats_beds_shape', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_beds_shape'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_pettag_shape', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_pettag_shape'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_size',
                    916
                );
                if (in_array('cats_catbeds_crates_beds_crates_carriers_tent_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbeds_crates_beds_crates_carriers_tent_size'
                    );
                }
                if (in_array('cats_catbeds_crates_furniture_scratchers_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbeds_crates_furniture_scratchers_size'
                    );
                }
                if (in_array('cats_catbowls_feeders_automaticcatfeeders_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_automaticcatfeeders_size'
                    );
                }
                if (in_array('cats_catbowls_feeders_catbowls_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_catbowls_size'
                    );
                }
                if (in_array('cats_catbowls_feeders_catdiners_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_catdiners_size'
                    );
                }
                if (in_array('cats_catbowls_feeders_catfoodstorage_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catbowls_feeders_catfoodstorage_size'
                    );
                }
                if (in_array('cats_catwaterers_fountains_fountain_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catwaterers_fountains_fountain_size'
                    );
                }
                if (in_array('cats_catwaterers_fountains_waterer_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catwaterers_fountains_waterer_size'
                    );
                }
                if (in_array('cats_furniture_scratchers_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_furniture_scratchers_size'
                    );
                }
                if (in_array('dogs_beds_mats_beds_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_beds_size'
                    );
                }
                if (in_array('dogs_beds_mats_blankets_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_blankets_size'
                    );
                }
                if (in_array('dogs_beds_mats_mats_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_beds_mats_mats_size'
                    );
                }
                if (in_array('dogs_bowls_feedersandfoodmats_bowls_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_bowls_feedersandfoodmats_bowls_size'
                    );
                }
                if (in_array('dogs_bowls_feedersandfoodmats_foodmats_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_bowls_feedersandfoodmats_foodmats_size'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_collars_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_collars_size'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_harnesses_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_harnesses_size'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_leashcollarset_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_leashcollarset_size'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_leashes_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_leashes_size'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_retractableleash_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_retractableleash_size'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_trainingleash_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_trainingleash_size'
                    );
                }
                if (in_array('dogs_dogaccessories_bandana_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_bandana_size'
                    );
                }
                if (in_array('dogs_dogaccessories_bowties_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_bowties_size'
                    );
                }
                if (in_array('dogs_dogaccessories_partyaccessories_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_partyaccessories_size'
                    );
                }
                if (in_array('dogs_dogaccessories_shoes_socks_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_shoes_socks_size'
                    );
                }
                if (in_array('dogs_dogaccessories_travelgear_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogaccessories_travelgear_size'
                    );
                }
                if (in_array('dogs_dogclothing_coats_jackets_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_coats_jackets_size'
                    );
                }
                if (in_array('dogs_dogclothing_coolingcoats_vests_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_coolingcoats_vests_size'
                    );
                }
                if (in_array('dogs_dogclothing_dresses_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_dresses_size'
                    );
                }
                if (in_array('dogs_dogclothing_raincoats_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_raincoats_size'
                    );
                }
                if (in_array('dogs_dogClothing_shirts_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogClothing_shirts_size'
                    );
                }
                if (in_array('dogs_dogclothing_sweatshirts_sweaters_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_sweatshirts_sweaters_size'
                    );
                }
                if (in_array('dogs_dogclothing_tshirts_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogclothing_tshirts_size'
                    );
                }
                if (in_array('dogs_kennels_carriers_cages_houses_barrier_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_cages_houses_barrier_size'
                    );
                }
                if (in_array('dogs_kennels_carriers_petcarrier_crates_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_petcarrier_crates_size'
                    );
                }
                if (in_array('dogs_kennels_carriers_travel_carrybags_size', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_kennels_carriers_travel_carrybags_size'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_solutionfor',
                    917
                );
                if (in_array('cats_catgrooming_shampoos_conditioners_solutionfor', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_shampoos_conditioners_solutionfor'
                    );
                }
                if (in_array('cats_food_dryfood_solutionfor', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_dryfood_solutionfor'
                    );
                }
                if (in_array('cats_food_prescriptionfood_solutionfor', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_prescriptionfood_solutionfor'
                    );
                }
                if (in_array('cats_food_wetfood_solutionfor', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_food_wetfood_solutionfor'
                    );
                }
                if (in_array('cats_treats_meatytreat_solutionfor', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_treats_meatytreat_solutionfor'
                    );
                }
                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'z_type',
                    918
                );
                if (in_array('cats_catgrooming_brushes_combs_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_brushes_combs_type'
                    );
                }
                if (in_array('cats_catgrooming_earcare_eyecare_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_earcare_eyecare_type'
                    );
                }
                if (in_array('cats_catgrooming_groomingtools_accessories_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_groomingtools_accessories_type'
                    );
                }
                if (in_array('cats_catgrooming_oral_dentalcare_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_oral_dentalcare_type'
                    );
                }
                if (in_array('cats_catgrooming_shampoos_conditioners_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_shampoos_conditioners_type'
                    );
                }
                if (in_array('cats_catgrooming_tick_fleasolutions_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_tick_fleasolutions_type'
                    );
                }
                if (in_array('cats_catgrooming_towels_wipes_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'cats_catgrooming_towels_wipes_type'
                    );
                }
                if (in_array('dogs_cleaning_odourcontrol_lintremover_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_cleaning_odourcontrol_lintremover_type'
                    );
                }
                if (in_array('dogs_cleaning_odourcontrol_pooperscoopers_bags_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_cleaning_odourcontrol_pooperscoopers_bags_type'
                    );
                }
                if (in_array('dogs_cleaning_odourcontrol_pottytrainingtrays_pads_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_cleaning_odourcontrol_pottytrainingtrays_pads_type'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_collars_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_collars_type'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_harnesses_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_harnesses_type'
                    );
                }
                if (in_array('dogs_collars_harnesses_leashes_leashes_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_collars_harnesses_leashes_leashes_type'
                    );
                }
                if (in_array('dogs_dogfood_weaningfood_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogfood_weaningfood_type'
                    );
                }
                if (in_array('dogs_doggrooming_groomingtools_accessories_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doggrooming_groomingtools_accessories_type'
                    );
                }
                if (in_array('dogs_doggrooming_paw_nailcare_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doggrooming_paw_nailcare_type'
                    );
                }
                if (in_array('dogs_doggrooming_shampoos_conditioners_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doggrooming_shampoos_conditioners_type'
                    );
                }
                if (in_array('dogs_doggrooming_towels_wipes_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doggrooming_towels_wipes_type'
                    );
                }
                if (in_array('dogs_doghealth_diaper_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_diaper_type'
                    );
                }
                if (in_array('dogs_doghealth_eye_earcare_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_eye_earcare_type'
                    );
                }
                if (in_array('dogs_doghealth_firstaid_recovery_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_firstaid_recovery_type'
                    );
                }
                if (in_array('dogs_doghealth_oral_dentalcare_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_oral_dentalcare_type'
                    );
                }
                if (in_array('dogs_doghealth_trainingsupplies_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_doghealth_trainingsupplies_type'
                    );
                }
                if (in_array('dogs_dogtreats_chocolates_beverages_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_dogtreats_chocolates_beverages_type'
                    );
                }
                if (in_array('dogs_fleasolution_fleacombs_accessories_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_fleasolution_fleacombs_accessories_type'
                    );
                }
                if (in_array('dogs_ticks_fleasolution_ticks_fleacombs_accessories_type', $allAttributes)) {
                    $attributeManagement->unassign(
                        $attributeSetId,
                       'dogs_ticks_fleasolution_ticks_fleacombs_accessories_type'
                    );
                }

                $attributeManagement->assign(
                   'catalog_product',
                    $attributeSetId,
                    $group_id,
                   'vendorcode',
                    910
                );


               //  $attributeManagement->assign(
               //      'catalog_product',
               //      $attributeSetId,
               //      $group_id,
               //      'best_seller_home',
               //      1000
               // );
                /*$attributeManagement->assign(
                    'catalog_product',
                    $attributeSetId,
                    $group_id,
                    'catalog_popular',
                    999
               );
                $attributeManagement->assign(
                    'catalog_product',
                    $attributeSetId,
                    $group_id,
                    'catalog_new_arrival',
                    999
               );*/
               //  $attributeManagement->assign(
               //      'catalog_product',
               //      $attributeSetId,
               //      $group_id,
               //      'ph_balanced',
               //      999
               // );
               //   $attributeManagement->assign(
               //      'catalog_product',
               //      $attributeSetId,
               //      $group_id,
               //      'essential_oil',
               //      999
               // );
               //  $attributeManagement->assign(
               //      'catalog_product',
               //      $attributeSetId,
               //      $group_id,
               //      'keyfeatures_description',
               //      999
               // );
            }
        }
       //  $group_id = $config->getAttributeGroupId($attributeSetId, "General");

        echo "success";
    }
}
