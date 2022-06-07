<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Script\Controller\Adminhtml\Script;

use Magento\Framework\App\ResourceConnection;

class Reset extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context  $context
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        ResourceConnection $resourceConnection
    ) {

        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('script/script/script');
        $connection = $this->resourceConnection->getConnection();

        try
        {
            $query = "DELETE FROM `catalog_product_entity_text` where store_id = 1";
            $connection->query($query);
            $query = "DELETE FROM `catalog_product_entity_datetime` where store_id = 1;";
            $connection->query($query);
            $query = "DELETE FROM `catalog_product_entity_decimal` where store_id = 1;";
            $connection->query($query);
            $query = "DELETE FROM `catalog_product_entity_int` where store_id = 1;";
            $connection->query($query);
            $query = "DELETE FROM `catalog_product_entity_varchar` where store_id = 1;";
            $connection->query($query);
        }
        catch (\Exception $e) {
            $this->logger->critical("Something went wrong while reset the default value.");
        return $resultRedirect;
        }
        
        $this->messageManager->addSuccess(__('successfully reset default value'));
        return $resultRedirect;
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zigly_Script::script_reset');
    }

}



