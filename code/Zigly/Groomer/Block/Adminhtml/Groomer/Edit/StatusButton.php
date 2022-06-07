<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Groomer
 */
declare(strict_types=1);

namespace Zigly\Groomer\Block\Adminhtml\Groomer\Edit;

use Magento\Backend\Block\Widget\Context;
use Zigly\Groomer\Model\GroomerFactory;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class StatusButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @param Context $context
     * @param GroomerFactory $groomerFactory
     */
    public function __construct(Context $context, GroomerFactory $groomerFactory)
    {
        $this->context = $context;
        $this->groomerFactory = $groomerFactory;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $groomer = $this->groomerFactory->create();
        $groomer->load($this->getModelId());
        if ($this->getModelId()) {
            if ($groomer->getProStatus() == '0') {
                $value = '1';
                $data = [
                    'label' => __('Approve'),
                    'class' => 'status',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to change '. $groomer->getName() .' to approve status ?'
                    ) . '\', \'' . $this->getStatusUrl($value) . '\')',
                    'sort_order' => 20,
                ];
            } elseif ($groomer->getProStatus() == '1') {
                $value = '2';
                $data = [
                    'label' => __('Un-Approve'),
                    'class' => 'status',
                    'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to change '. $groomer->getName() .' to un-approve status ?'
                    ) . '\', \'' . $this->getStatusUrl($value) . '\')',
                    'sort_order' => 20,
                ];
            }
        }
        return $data;
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    public function getStatusUrl($value)
    {
        return $this->getUrl('*/*/status', ['groomer_id' => $this->getModelId(), 'value' => $value]);
    }
}

