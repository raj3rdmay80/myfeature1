<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


/**
 * @codingStandardsIgnoreFile
 */

namespace Amasty\CustomTabs\Test\Unit\Model\Tabs;

use Amasty\CustomTabs\Model\Tabs\Tabs;
use Amasty\CustomTabs\Test\Unit\Traits;

/**
 * Class TabsTest
 *
 * @see Tabs
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TabsTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var Tabs
     */
    private $model;

    /**
     * @covers Tabs::addStore
     *
     * @dataProvider addStoreDataProvider
     *
     * @throws \ReflectionException
     */
    public function testAddStore(...$storeIds)
    {
        $this->model = $this->getObjectManager()->getObject(Tabs::class);

        foreach ($storeIds as $storeId) {
            $this->model->addStore($storeId);
        }

        $this->assertEquals($storeIds, $this->model->getStores());
    }

    /**
     * Data provider for addStore test
     * @return array
     */
    public function addStoreDataProvider()
    {
        return [
            [1, 3, 4],
            [2],
            []
        ];
    }
}
