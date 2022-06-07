<?php
/**
 * Copyright © 2019 CommerceExtensions. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CommerceExtensions\ProductImportExport\Helper;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Helper\AbstractHelper;

class Reset extends AbstractHelper
{

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Constructor
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {

        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function setDefaultValue()
    {
        $connection = $this->resourceConnection->getConnection();

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

}



