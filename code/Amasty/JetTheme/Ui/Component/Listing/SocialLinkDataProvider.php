<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


namespace Amasty\JetTheme\Ui\Component\Listing;

use Amasty\JetTheme\Model\SocialLink\ResourceModel\SocialLink\Grid\Collection;
use Amasty\JetTheme\Model\SocialLink\ResourceModel\SocialLink\Grid\CollectionFactory;
use Magento\Framework\Api\Filter;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Ui\DataProvider\AbstractDataProvider;

class SocialLinkDataProvider extends DataProvider
{
    /**
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        if ($data['totalRecords'] > 0) {
            foreach ($data['items'] as &$item) {
                $item['stores'] = explode(',', $item['stores']);
            }
        }

        return $data;
    }
}
