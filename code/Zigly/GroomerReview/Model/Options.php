<?php

namespace Zigly\GroomerReview\Model;

/**
 * Tag name Options
 */
class Options implements \Magento\Framework\Data\OptionSourceInterface
{
 
    /**
     * @var $reviewTagCollection
     */
    protected $reviewTagCollectionFactory;

    protected $options;
    /**
     * Options constructor
     *
     * @param Database $coreFileStorageDatabase
     */
    public function __construct(

        \Zigly\ReviewTag\Model\ResourceModel\ReviewTag\CollectionFactory $reviewTagCollectionFactory,
        \Zigly\Sales\Block\Order\Booking $booking
    ) {
        $this->reviewTagCollectionFactory = $reviewTagCollectionFactory;
        $this->booking = $booking;
    }

    /**
         * Return array of options as value-label pairs
         *
         * @return array Format: array(array("value" => "<value>", "label"=> "<label>"), ...)
         */
        public function toOptionArray()
        {
            if ($this->options === null) {
                $collection = $this->reviewTagCollectionFactory->create();
                $this->options = [['label' => '', 'value' => '']];
                foreach ($collection as $tagName) {
                    $this->options[] = [
                        'label' => __($tagName->getTagName()),
                        'value' => $tagName->getTagName()
                    ];
                }
            }
        return $this->options;
    }
}
