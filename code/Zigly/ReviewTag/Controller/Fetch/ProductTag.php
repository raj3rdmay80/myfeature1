<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Controller\Fetch;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Zigly\ReviewTag\Model\ResourceModel\ReviewTag\CollectionFactory;

class ProductTag extends \Magento\Framework\App\Action\Action
{

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param JsonFactory $jsonResultFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute save action
     * @return void
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $result = $this->jsonResultFactory->create();
        $responseData = [
            'success' => false,
            'message' => 'Tag\'s not found.'
        ];
        try {
            if (!empty($post['rating'])) {
                if ((int)$post['rating'] == 10) {
                    $rating = 1;
                    $showText = 'What do you like?';
                } else {
                    $rating = 0;
                    $showText = 'What can we improve?';
                }
                $tagCollection = $this->collectionFactory->create()
                    ->addFieldToSelect('tag_name')
                    ->addFieldToFilter('is_active', '1')
                    ->addFieldToFilter('rating', $rating)
                    ->addFieldToFilter('type', 3)
                    ->setOrder('reviewtag_id', 'ASC');
                if (count($tagCollection)) {
                    $responseData['tag'] = $tagCollection->getData();
                    $responseData['show_text'] = $showText;
                    $responseData['success'] = true;
                }
            }
        } catch (\Exception $e) {
            $responseData['trace'] = $e->getMessage();
        }
        $result->setData($responseData);
        return $result;
    }
}