<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Helper;

use Amasty\AdvancedReview\Model\Sources\Frequency;
use Amasty\AdvancedReview\Model\Sources\AdminNotifications;
use Amasty\AdvancedReview\Model\Sources\UseDefaultConfig;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper implements ArgumentInterface
{
    const IMAGE_WIDTH = 200;

    const XML_PATH_ALLOW_GUEST = 'amasty_advancedreview/general/allow_guest';

    const CONFIG_SORT_ORDER = 'general/sort_order';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $imageFactory;

    /**
     * @var ReviewFactory
     */
    private $reviewFactory;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $fileManager;
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    private $filterManager;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var array
     */
    private $correctSortOrder;

    public function __construct(
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Framework\Filesystem\Io\File $fileManager,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magento\Framework\Filter\FilterManager $filterManager
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->reviewFactory = $reviewFactory;
        $this->fileManager = $fileManager;
        $this->jsonEncoder = $jsonEncoder;
        $this->sessionFactory = $sessionFactory;
        $this->filterManager = $filterManager;
        $this->serializer = $serializer;
        $this->correctSortOrder = array_keys($this->getSortOrder());
    }

    /**
     * @param $path
     * @param int $storeId
     * @return mixed
     */
    public function getGeneralConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $path
     * @param int $storeId
     * @return mixed
     */
    public function getModuleConfig($path, $storeId = null)
    {
        return $this->getGeneralConfig('amasty_advancedreview/' . $path, $storeId);
    }

    /**
     * @return bool
     */
    public function isAllowImages()
    {
        return (bool)$this->getModuleConfig('images/allow_upload');
    }

    /**
     * @return bool
     */
    public function isRecommendFieldEnabled()
    {
        return (bool)$this->getModuleConfig('general/recommend');
    }

    /**
     * @return bool
     */
    public function isImagesRequired()
    {
        return (bool)$this->getModuleConfig('images/is_required');
    }

    /**
     * @return bool
     */
    public function isAllowAnswer()
    {
        return (bool)$this->getModuleConfig('general/admin_answer');
    }

    /**
     * @return int
     */
    public function getReviewImageWidth()
    {
        $width = $this->getModuleConfig('images/image_width');
        if (!$width) {
            $width = self::IMAGE_WIDTH;
        }

        return $width;
    }

    /**
     * @return int
     */
    public function getSlidesToShow()
    {
        $slides = $this->getModuleConfig('images/slides_to_show');

        return $slides;
    }

    /**
     * @return array
     */
    public function getSortingOptions()
    {
        $sort = $this->getModuleConfig('general/sort_by');
        if ($sort) {
            $sort = explode(',', $sort);
        } else {
            $sort = [];
        }

        return $this->sortOptions($sort);
    }

    /**
     * @return array
     */
    public function getFilteringOptions()
    {
        $filter = $this->getModuleConfig('general/filter_by');
        if ($filter) {
            $filter = explode(',', $filter);
        } else {
            $filter = [];
        }

        return $filter;
    }

    /**
     * @return bool
     */
    public function isAllowHelpful()
    {
        return (bool)$this->getModuleConfig('general/helpful');
    }

    /**
     * @return bool
     */
    public function isAllowGuest()
    {
        return (bool)$this->getModuleConfig('general/allow_guest');
    }

    public function isReminderEnabled(?int $storeId = null): bool
    {
        return (bool)$this->getModuleConfig('reminder/enabled', $storeId);
    }

    public function getTriggerOrderStatus(?int $storeId = null): array
    {
        return explode(',', $this->getModuleConfig('reminder/order_status', $storeId));
    }

    /**
     * @return int
     */
    public function getDaysToSend()
    {
        return (int)$this->getModuleConfig('reminder/days');
    }

    /**
     * @return string
     */
    public function getTestEmail()
    {
        return $this->getModuleConfig('reminder/test_email');
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public function hash($data)
    {
        return hash("sha256", $data);
    }

    /**
     * @return bool
     */
    public function isProsConsEnabled()
    {
        return (bool)$this->getModuleConfig('general/pros_cons');
    }

    /**
     * @return bool
     */
    public function isGDPREnabled()
    {
        return (bool)$this->getModuleConfig('general/gdpr_enabled');
    }

    /**
     * @return string
     */
    public function getGDPRText()
    {
        return $this->filterManager->stripTags(
            $this->getModuleConfig('general/gdpr_text'),
            [
                'allowableTags' => '<a>',
                'escape' => false
            ]
        );
    }

    /**
     * @return bool
     */
    public function isEmailFieldEnable()
    {
        return (bool)$this->getModuleConfig('general/guest_email') && !$this->getCustomerSession()->getId();
    }

    /**
     * @return mixed
     */
    private function getCustomerSession()
    {
        return $this->sessionFactory->create();
    }

    /**
     * @return int
     */
    public function getReminderFrequency()
    {
        return (int) $this->getModuleConfig('reminder/frequency');
    }

    /**
     * @return array
     */
    public function getAdminNotificationEmails()
    {
        $emails = $this->getModuleConfig('admin_notifications/email');
        $emails = $emails ? explode(',', $emails) : [];

        return $emails;
    }

    /**
     * @return int
     */
    public function getAdminNotificationType()
    {
        return (int) $this->getModuleConfig('admin_notifications/enabled');
    }

    /**
     * @return bool
     */
    public function isReminderPerCustomer()
    {
        return $this->getReminderFrequency() == Frequency::PER_CUSTOMER;
    }

    /**
     * @return bool
     */
    public function isReminderPerProduct()
    {
        return $this->getReminderFrequency() == Frequency::PER_PRODUCT;
    }

    /**
     * @return bool
     */
    public function isAllowCoupons()
    {
        return (bool)$this->getModuleConfig('coupons/enabled');
    }

    /**
     * @return bool
     */
    public function isNeedReview()
    {
        return (bool)$this->getModuleConfig('coupons/review');
    }

    /**
     * @return array
     */
    public function getCustomerGroups()
    {
        $groups = $this->getModuleConfig('coupons/customer_group');

        return explode(',', $groups);
    }

    /**
     * @return bool
     */
    public function isDailyNotificationEnabled()
    {
        return $this->getAdminNotificationType() == AdminNotifications::DAILY;
    }

    /**
     * @return bool
     */
    public function isInstantlyNotificationEnabled()
    {
        return $this->getAdminNotificationType() == AdminNotifications::INSTANTLY;
    }

    /**
     * @return bool
     */
    public function isCommentsEnabled()
    {
        return (bool) $this->getModuleConfig('comments/enabled');
    }

    /**
     * @return bool
     */
    public function isGuestCanComment()
    {
        return (bool) $this->getModuleConfig('comments/guest');
    }

    /**
     * @return bool
     */
    public function isCommentApproved()
    {
        return (bool) $this->getModuleConfig('comments/auto_approve');
    }

    /**
     * @return bool
     */
    public function isAdminAnswerAccountOnly()
    {
        return (bool) $this->getModuleConfig('general/admin_answer_account_only');
    }

    /**
     * @param \Magento\Review\Model\Review $review
     *
     * @return bool
     */
    public function isAdminAnswerAvailableOnAccountOnly($review)
    {
        $status = $review->getData(\Amasty\AdvancedReview\Helper\BlockHelper::ADMIN_ANSWER_ACCOUNT_ONLY);
        if ($status) {
            $status = $status == UseDefaultConfig::YES;
        } else {
            $status = $this->isAdminAnswerAccountOnly();
        }

        return (bool)$status;
    }

    /**
     * @return array
     */
    public function getReminderGroups()
    {
        return explode(',', $this->getModuleConfig('reminder/customer_group'));
    }

    /**
     * @return array
     */
    public function getSortOrder()
    {
        $value = $this->getModuleConfig(self::CONFIG_SORT_ORDER);
        $value = $value ? $this->serializer->unserialize($value) : [];

        return $value;
    }

    /**
     * @param array $options
     * @return array $sortedOptions
     */
    public function sortOptions($options = [])
    {
        $sortedOptions = [];
        $options = $this->arrayFlip($options);
        foreach ($this->correctSortOrder as $item) {
            if (isset($options[$item])) {
                $sortedOptions[$item] = $options[$item];
            }
        }
        $sortedOptions = $this->arrayFlip($sortedOptions);

        return $sortedOptions;
    }

    /**
     * @param array $options
     * @return array
     */
    private function arrayFlip($options = [])
    {
        if (isset(array_values($options)[0]) && !is_object(array_values($options)[0])) {
            $options = array_flip($options);
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getReminderEmails()
    {
        $emails = $this->getModuleConfig('reminder/reminder_emails');
        $emails = $emails ? explode(',', $emails) : [];

        return $emails;
    }

    public function getReviewsPerPage(): int
    {
        return (int) ($this->getModuleConfig('general/page_limit') ?: 5);
    }
}
