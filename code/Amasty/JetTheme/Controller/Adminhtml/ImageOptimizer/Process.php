<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


namespace Amasty\JetTheme\Controller\Adminhtml\ImageOptimizer;

use Amasty\ImageOptimizer\Model\Image\ForceOptimization;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

class Process extends Action
{
    const ADMIN_RESOURCE = 'Amasty_JetTheme::config';

    /**
     * @var ForceOptimization
     */
    private $forceOptimization;

    public function __construct(
        ForceOptimization $forceOptimization,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->forceOptimization = $forceOptimization;
    }

    public function execute()
    {
        $limit = (int)$this->getRequest()->getParam('limit', 10);
        if (!$limit || $limit < 0) {
            $limit = 10;
        }

        $this->forceOptimization->execute($limit);

        return $this->resultFactory->create(ResultFactory::TYPE_RAW)->setContents(1);
    }
}
