<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Test2 extends \Magento\Framework\App\Action\Action
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
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $ATTRIBUTE_GROUP = 'Key Features';
        $ATTRIBUTE_CODE = 'key-features';


        $eavSetup = $objectManager->create(\Magento\Eav\Setup\EavSetup::class);
        $config = $objectManager->get(\Magento\Catalog\Model\Config::class);
        $attributeManagement = $objectManager->get(\Magento\Eav\Api\AttributeManagementInterface::class);

        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);
        foreach ($attributeSetIds as $attributeSetId) {
            if ($attributeSetId) {
                $attributeGroupId = $config->getAttributeGroupId($attributeSetId, $ATTRIBUTE_GROUP);
                $attributeGroupId = $attributeGroupId ? $attributeGroupId : $ATTRIBUTE_GROUP;
                echo $attributeSetId." - ".$attributeGroupId;
                echo "<br>";
                
                $attributeManagement->assign(
                    'catalog_product',
                    $attributeSetId,
                    $attributeGroupId,
                    'alcohol_free',
                    100
                );
                $attributeManagement->assign(
                    'catalog_product',
                    $attributeSetId,
                    $attributeGroupId,
                    'non_toxic',
                    101
                );
                $attributeManagement->assign(
                    'catalog_product',
                    $attributeSetId,
                    $attributeGroupId,
                    'vet_approved',
                    102
                );
                $attributeManagement->assign(
                    'catalog_product',
                    $attributeSetId,
                    $attributeGroupId,
                    'ph_balanced',
                    103
                );
                $attributeManagement->assign(
                    'catalog_product',
                    $attributeSetId,
                    $attributeGroupId,
                    'essential_oil',
                    104
                );
                $attributeManagement->assign(
                    'catalog_product',
                    $attributeSetId,
                    $attributeGroupId,
                    'keyfeatures_description',
                    105
                );
            }
        }
        echo "Success";


    }
}
