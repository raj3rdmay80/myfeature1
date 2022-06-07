<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model;

use Magento\Framework\Module\Dir\Reader;

class DirReader
{
    const MODULE_NAME = 'Amasty_JetTheme';

    /**
     * @var Reader
     */
    private $moduleDirReader;

    public function __construct(Reader $moduleDirReader)
    {
        $this->moduleDirReader = $moduleDirReader;
    }

    /**
     * @param string $entityDir
     * @return string
     */
    public function getSampleModuleDir(string $entityDir): string
    {
        return $this->getModuleDir() . '/Setup/Files/' . $entityDir . '/';
    }

    /**
     * @return string
     */
    public function getColorPresetsDir(): string
    {
        return $this->getModuleDir() . '/Styles/Color/';
    }

    /**
     * @return string
     */
    public function getStylePresetsDir(): string
    {
        return $this->getModuleDir() . '/Styles/Design/';
    }

    /**
     * @return string
     */
    public function getModuleDir(): string
    {
        return $this->moduleDirReader->getModuleDir('', self::MODULE_NAME);
    }
}
