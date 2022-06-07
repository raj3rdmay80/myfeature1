<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Block\Adminhtml\SocialLink\Edit;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    /**
     * @var UrlInterface
     */
    private $urlManager;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        UrlInterface $urlManager,
        RequestInterface $request
    ) {
        $this->urlManager = $urlManager;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $data = [];
        if ($this->request->getParam('id', false)) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete this social link?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    private function getDeleteUrl(): string
    {
        return $this->urlManager->getUrl('*/*/delete', ['id' => $this->request->getParam('id')]);
    }
}
