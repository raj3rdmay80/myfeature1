<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Setup\UpgradeSchema;

use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpdateImageTable
 * @package Amasty\AdvancedReview\Setup\UpgradeSchema
 */
class UpdateImageTable
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $resolvedImageTable = $setup->getTable('amasty_advanced_review_images');

        $setup->getConnection()->changeColumn(
            $resolvedImageTable,
            'review_id',
            'review_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                'length' => null,
                'nullable' => false,
                'unsigned' => true,
                'comment' => 'Review ID'
            ]
        );
        $setup->getConnection()->addForeignKey(
            $setup->getFkName('amasty_advanced_review_images', 'review_id', 'review', 'review_id'),
            $resolvedImageTable,
            'review_id',
            $setup->getTable('review'),
            'review_id'
        );
    }
}
