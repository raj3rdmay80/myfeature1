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

use Amasty\CustomTabs\Model\Tabs\Rule;
use Amasty\CustomTabs\Test\Unit\Traits;
use Magento\Catalog\Model\Product;

/**
 * Class RuleTest
 *
 * @see Rule
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RuleTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var Rule
     */
    private $model;

    /**
     * @covers Rule::callbackValidateProduct
     *
     * @dataProvider callbackValidateProductDataProvider
     *
     * @throws \ReflectionException
     */
    public function testCallbackValidateProduct($productsInfo, $expectedResult)
    {
        $this->model = $this->getObjectManager()->getObject(Rule::class);

        $conditions = $this->getMockBuilder(\Magento\Rule\Model\Condition\Combine::class)
            ->disableOriginalConstructor()
            ->setMethods(['validate'])
            ->getMock();

        $conditions->expects($this->any())->method('validate')->willReturn(true);



        $this->setProperty($this->model, '_conditions', $conditions, Rule::class);
        foreach ($productsInfo as $productInfo) {
            $productInfo['product'] = $this->createProductMock($productInfo['product_id']);
            $this->model->callbackValidateProduct($productInfo);
        }
        $actualResult = $this->getProperty($this->model, '_productIds', Rule::class);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @param $productId
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function createProductMock($productId)
    {
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $product->expects($this->any())->method('getId')->willReturn($productId);

        return $product;
    }

    /**
     * Data provider for callbackValidateProduct test
     * @return array
     */
    public function callbackValidateProductDataProvider()
    {
        return [
            [
                [
                    [
                        'store_id' => 1,
                        'product_id' => 8,
                        'row' => []
                    ],
                    [
                        'store_id' => 4,
                        'product_id' => 333,
                        'row' => []
                    ],
                    [
                        'store_id' => 2,
                        'product_id' => 8,
                        'row' => []
                    ]
                ],
                [
                    8 => [1, 2],
                    333 => [4]
                ]
            ]
        ];
    }
}
