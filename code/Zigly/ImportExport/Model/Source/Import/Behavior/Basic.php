<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Zigly\ImportExport\Model\Source\Import\Behavior;

use Magento\ImportExport\Model\Import;

/**
 * Import behavior source model used for defining the behaviour during the import.
 *
 * @api
 * @since 100.0.2
 */
class Basic extends \Magento\ImportExport\Model\Source\Import\Behavior\Basic
{
    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return [
            Import::BEHAVIOR_APPEND => __('Add/Update')
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'basic';
    }

    /**
     * @inheritdoc
     */
    public function getNotes($entityCode)
    {
        $messages = ['catalog_product' => [
            Import::BEHAVIOR_APPEND => __(
                "New product data is added to the existing product data for the existing entries in the database. "
                . "All fields except sku can be updated."
            )
        ]];
        return isset($messages[$entityCode]) ? $messages[$entityCode] : [];
    }
}
