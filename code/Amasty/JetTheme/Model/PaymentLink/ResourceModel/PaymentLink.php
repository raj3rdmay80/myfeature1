<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\PaymentLink\ResourceModel;

use Amasty\JetTheme\Api\Data\PaymentLinkInterface;
use Amasty\JetTheme\Model\ImageProcessor;
use Magento\Framework\DB\Helper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class PaymentLink extends AbstractDb
{
    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var Helper
     */
    private $dbHelper;

    public function __construct(
        ImageProcessor $imageProcessor,
        Helper $dbHelper,
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->imageProcessor = $imageProcessor;
        $this->dbHelper = $dbHelper;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(PaymentLinkInterface::TABLE_NAME, PaymentLinkInterface::ENTITY_ID);
    }

    protected function _beforeSave(AbstractModel $object)
    {
        $this->saveImage($object);

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->updateStores($object);

        return parent::_afterSave($object);
    }

    /**
     * @inheritdoc
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        $table = $this->getMainTable();
        $select->joinLeft(
            ['stores' => $this->getTable(PaymentLinkInterface::STORE_TABLE_NAME)],
            $table . '.'
            . PaymentLinkInterface::ENTITY_ID
            . ' = stores.'
            . PaymentLinkInterface::STORE_PAYMENT_ID_FIELD,
            []
        );
        $this->dbHelper->addGroupConcatColumn(
            $select,
            'stores',
            'stores.store_id'
        );

        return $select;
    }

    /**
     * @param AbstractModel $object
     */
    private function updateStores(AbstractModel $object): void
    {
        if (!$object->hasData(PaymentLinkInterface::STORES)) {
            return;
        }

        $connection = $this->getConnection();
        $paymentLinkId = $object->getId();

        $table = $this->getTable(PaymentLinkInterface::STORE_TABLE_NAME);
        $select = $select = $connection->select()
            ->from($table, PaymentLinkInterface::STORE_PAYMENT_STORE_ID_FIELD)
            ->where(PaymentLinkInterface::STORE_PAYMENT_ID_FIELD . ' = ?', $paymentLinkId);
        $oldData = $connection->fetchCol($select);
        $newData = is_array($object->getStores()) ? $object->getStores() : explode(',', $object->getStores());

        if (is_array($newData)) {
            $toDelete = array_diff($oldData, $newData);
            $toInsert = array_diff($newData, $oldData);
            $toInsert = array_diff($toInsert, ['']);
        } else {
            $toDelete = $oldData;
            $toInsert = null;
        }

        if (!empty($toDelete)) {
            $deleteSelect = clone $select;
            $deleteSelect->where('store_id IN (?)', $toDelete);
            $query = $connection->deleteFromSelect($deleteSelect, $table);
            $connection->query($query);
        }

        if (!empty($toInsert)) {
            $insertArray = [];
            foreach ($toInsert as $value) {
                $insertArray[] = [
                    PaymentLinkInterface::STORE_PAYMENT_ID_FIELD => $paymentLinkId,
                    PaymentLinkInterface::STORE_PAYMENT_STORE_ID_FIELD => $value
                ];
            }

            $connection->insertMultiple($table, $insertArray);
        }
    }

    private function saveImage(AbstractModel $object): void
    {
        $image = $object->getData(PaymentLinkInterface::ICON_FILE);
        if (!$object->getData('skip_image_upload')
            && $object->dataHasChangedFor(PaymentLinkInterface::ICON_FILE)
            && $image) {
            $savedImage = $this->imageProcessor->saveImage('payment/' . $image);
            if ($image != $savedImage) {
                $object->setData(PaymentLinkInterface::ICON_FILE, $savedImage);
            }
        }
    }
}
