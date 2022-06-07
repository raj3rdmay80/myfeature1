<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Controller\Adminhtml\ImageOptimizer;

use Amasty\ImageOptimizer\Controller\Adminhtml\Image\ClearFolder;
use Amasty\PageSpeedTools\Model\OptionSource\Resolutions;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

class ClearImagesFolder extends Action
{
    const ADMIN_RESOURCE = 'Amasty_JetTheme::config';

    /**
     * @var array
     */
    private $foldersToRemove = [
        Resolutions::MOBILE,
        Resolutions::TABLET,
        Resolutions::WEBP_DIR,
    ];

    /**
     * @var ClearFolder
     */
    private $clearFolder;

    public function __construct(
        ClearFolder $clearFolder,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->clearFolder = $clearFolder;
    }

    public function execute()
    {
        try {
            foreach ($this->foldersToRemove as $folder) {
                $this->clearFolder->execute(
                    $folder === Resolutions::WEBP_DIR ? $folder : Resolutions::RESOLUTIONS[$folder]['dir']
                );
            }

            $this->messageManager->addSuccessMessage(__('Image Folders were successful cleaned.'));
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
