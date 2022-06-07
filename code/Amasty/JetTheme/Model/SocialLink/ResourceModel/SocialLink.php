<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\SocialLink\ResourceModel;

use Amasty\JetTheme\Api\Data\SocialLinkInterface;
use Amasty\JetTheme\Model\ImageProcessor;
use Magento\Framework\DB\Helper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class SocialLink extends AbstractDb
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
        $this->_init(SocialLinkInterface::TABLE_NAME, SocialLinkInterface::ENTITY_ID);
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
            ['stores' => $this->getTable(SocialLinkInterface::STORE_TABLE_NAME)],
            $table . '.'
            . SocialLinkInterface::ENTITY_ID
            . ' = stores.'
            . SocialLinkInterface::STORE_SOCIAL_ID_FIELD,
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
        if (!$object->hasData(SocialLinkInterface::STORES)) {
            return;
        }

        $connection = $this->getConnection();
        $socialLinkId = $object->getId();

        $table = $this->getTable(SocialLinkInterface::STORE_TABLE_NAME);
        $select = $select = $connection->select()
            ->from($table, SocialLinkInterface::STORE_SOCIAL_STORE_ID_FIELD)
            ->where(SocialLinkInterface::STORE_SOCIAL_ID_FIELD . ' = ?', $socialLinkId);
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
                    SocialLinkInterface::STORE_SOCIAL_ID_FIELD => $socialLinkId,
                    SocialLinkInterface::STORE_SOCIAL_STORE_ID_FIELD => $value
                ];
            }

            $connection->insertMultiple($table, $insertArray);
        }
    }

    private function saveImage(AbstractModel $object): void
    {
        $image = $object->getData(SocialLinkInterface::ICON_FILE);
        if (!$object->getData('skip_image_upload')
            && $object->dataHasChangedFor(SocialLinkInterface::ICON_FILE)
            && $image) {
            $savedImage = $this->imageProcessor->saveImage('social/' . $image);
            if ($image != $savedImage) {
                $object->setData(SocialLinkInterface::ICON_FILE, $savedImage);
            }
        }
    }
}
