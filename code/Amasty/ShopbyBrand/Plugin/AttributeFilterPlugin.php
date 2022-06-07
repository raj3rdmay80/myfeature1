<?php

namespace Amasty\ShopbyBrand\Plugin;

use Amasty\ShopbyBase\Helper\FilterSetting;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Amasty\ShopbyBrand\Helper\Content;

class AttributeFilterPlugin
{
    /**
     * @var  Content
     */
    protected $contentHelper;

    public function __construct(Content $contentHelper)
    {
        $this->contentHelper = $contentHelper;
    }

    /**
     * @param AbstractFilter $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsVisibleWhenSelected(AbstractFilter $subject, $result)
    {
        return ($result && $this->isBrandingBrand($subject)) ? false : $result;
    }

    /**
     * @param AbstractFilter $subject
     * @param bool $result
     * @return bool
     */
    public function afterShouldAddState(AbstractFilter $subject, $result)
    {
        return ($result && $this->isBrandingBrand($subject)) ? false : $result;
    }

    /**
     * @param AbstractFilter $subject
     * @return bool
     */
    protected function isBrandingBrand(AbstractFilter $subject)
    {
        $brand = $this->contentHelper->getCurrentBranding();
        return $brand && (FilterSetting::ATTR_PREFIX . $subject->getRequestVar() == $brand->getFilterCode());
    }
}
