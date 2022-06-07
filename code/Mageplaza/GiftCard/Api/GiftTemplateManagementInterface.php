<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Api;

/**
 * Interface GiftTemplateManagementInterface
 *
 * @package Mageplaza\GiftCard\Api
 */
interface GiftTemplateManagementInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Mageplaza\GiftCard\Api\Data\GiftTemplateSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $id
     *
     * @return \Mageplaza\GiftCard\Api\Data\GiftTemplateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param string $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function delete($id);

    /**
     * @param \Mageplaza\GiftCard\Api\Data\GiftTemplateInterface $entity
     *
     * @return \Mageplaza\GiftCard\Api\Data\GiftTemplateInterface
     * @throws \Exception
     */
    public function save(\Mageplaza\GiftCard\Api\Data\GiftTemplateInterface $entity);

    /**
     * @param string[] $file
     *
     * @return string[]
     * @throws \Exception
     */
    public function uploadImage($file);
}
