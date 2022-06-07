<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Test\Unit\Model\ToolBar;

use Amasty\AdvancedReview\Model\Toolbar\Applier;
use Amasty\AdvancedReview\Model\ResourceModel\Review\Collection as ReviewCollection;
use Amasty\AdvancedReview\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ApplierTest
 *
 * @see Applier
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ApplierTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Applier::execute
     *
     * @throws \ReflectionException
     */
    public function testExecute()
    {
        $model = $this->createPartialMock(Applier::class, ['applySorting']);

        $collection = $this->createMock(ReviewCollection::class);
        $collection->expects($this->once())->method('getFlag')->willReturn(true);
        $collection->expects($this->never())->method('setFlag')->willReturn(true);
        $model->execute($collection);

        $collection = $this->createMock(ReviewCollection::class);
        $collection->expects($this->once())->method('getFlag')->willReturn(false);

        $urlBuilder = $this->createMock(\Amasty\AdvancedReview\Model\Toolbar\UrlBuilder::class);
        $urlBuilder->expects($this->once())->method('collectParams')->willReturn([]);
        $this->setProperty($model, 'urlBuilder', $urlBuilder, Applier::class);

        $config = $this->createMock(\Amasty\AdvancedReview\Helper\Config::class);
        $config->expects($this->once())->method('getSortingOptions')->willReturn([]);
        $this->setProperty($model, 'config', $config, Applier::class);

        $collection->expects($this->once())->method('setFlag')->willReturn(true);
        $model->expects($this->once())->method('applySorting');

        $model->execute($collection);
    }
}
