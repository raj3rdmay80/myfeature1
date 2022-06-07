<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Model;

/**
 * Class ConfigProvider
 */
class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract
{
    protected $pathPrefix = 'amcustomtabs/';

    /**#@+
     * Constants defined for xpath of system configuration
     */
    const XPATH_ENABLED = 'general/enabled';
    const ALLOW_EDIT_DEFAULT_TABS = 'general/allow_default';
    const ACCORDION_VIEW = 'general/accordion_view';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isSetFlag(self::XPATH_ENABLED);
    }

    /**
     * @return bool
     */
    public function isChangeDefaultTabsAllowed()
    {
        return $this->isSetFlag(self::ALLOW_EDIT_DEFAULT_TABS);
    }

    /**
     * @return bool
     */
    public function isAccordionView()
    {
        return $this->isSetFlag(self::ACCORDION_VIEW) && $this->isEnabled();
    }
}
