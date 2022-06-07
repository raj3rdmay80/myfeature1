<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Controller\Grooming;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Zigly\Managepets\Model\ManagepetsFactory;
use Zigly\Species\Model\BreedFactory;
use Zigly\GroomingService\Model\Session;
use Zigly\Activities\Model\ActivitiesFactory;
use Zigly\Plan\Model\PlanFactory;

class Setplan extends \Magento\Framework\App\Action\Action
{

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Session
     */
    protected $groomingSession;

    /**
     * @var ManagepetsFactory
     */
    protected $pets;

    /**
     * @var BreedFactory
     */
    protected $breed;

    /**
     * @var PlanFactory
     */
    protected $plans;

    /**
     * @var ActivitiesFactory
     */
    protected $activities;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param JsonFactory $jsonResultFactory
     * @param PageFactory $resultPageFactory
     * @param ManagepetsFactory $petsFactory
     * @param PlanFactory $planCollection
     * @param ActivitiesFactory $activitiesFactory
     * @param BreedFactory $breedFactory
     * @param Session $groomingSession
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        ManagepetsFactory $petsFactory,
        BreedFactory $breedFactory,
        PlanFactory $planCollection,
        ActivitiesFactory $activitiesFactory,
        PageFactory $resultPageFactory,
        Session $groomingSession
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->plans = $planCollection;
        $this->activities = $activitiesFactory;
        $this->pets = $petsFactory;
        $this->breed = $breedFactory;
        $this->groomingSession = $groomingSession;
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
        $resultPage = $this->resultPageFactory->create();
        $responseData = [
            'success' => false,
            'message' => 'Something went wrong. Please try again.'
        ];
        if (!empty($post['planid'])) {
            $groomSession = $this->groomingSession->getGroomService();
            $groomSession['planid'] = $post['planid'];
            $plan = $this->plans->create()->load($post['planid']);
            if (!empty($post['activity'])) {
                $groomSession['activity'] = $post['activity'];
                $breedName = $this->getBreed($groomSession['pet_id']);
                $activityPrice = 0;
                foreach ($post['activity'] as $activityId) {
                    $activity = $this->activities->create()->load($activityId);
                    if ($activity->getIsActive()) {
                        $activityPrice += (float)$activity->getData($breedName.'_price');
                    }
                }
                if ($activityPrice < $plan->getData($breedName.'_minimum_price')) {
                    $responseData = [
                        'success' => false,
                        'message' => 'Please select activities to fulfill the minimum price.'
                    ];
                    $result->setData($responseData);
                    return $result;
                }
            }
            $planName = $plan->getPlanName();
            $groomSession['subtotal'] = '';
            $groomSession['grand_total'] = '';
            $groomSession['discount_amount'] = '';
            $groomSession['wallet_amount'] = '';
            $groomSession['wallet_balance'] = '';
            $groomSession['wallet_discount_amount'] = '';
            $groomSession['coupon_discount_amount'] = '';
            $groomSession['wallet'] = 1;
            $groomSession['coupon'] = 1;
            $groomSession['coupon_code'] = '';
            $groomSession['coupon_amount'] = '';
            $groomSession['coupon_description'] = '';
            $this->groomingSession->setGroomService($groomSession);
            $block = $resultPage->getLayout()
                ->createBlock('Zigly\GroomingService\Block\Grooming\Plan')
                ->setTemplate('Zigly_GroomingService::grooming/timeslot.phtml')
                ->toHtml();
            $responseData = [
                'success' => true,
                'output' => $block,
                'plan_name' => $planName
            ];
        }
        $result->setData($responseData);
        return $result;
    }

    /**
     * Get Breed Type Name
     * @return string
     */
    public function getBreed($petId)
    {
        $breedTypeName = "";
        $petdetails = $this->pets->create()->load($petId)->getData();
        if ($petdetails) {
            $breedType = $this->breed->create()->load($petdetails['breed'])->getData('breed_type');
            switch ($breedType) {
                case '1':
                    $breedTypeName = "small";
                    break;
                case '2':
                    $breedTypeName = "medium";
                    break;
                case '3':
                    $breedTypeName = "large";
                    break;
                case '4':
                    $breedTypeName = "extra_large";
                    break;
            }
        }
        return $breedTypeName;
    }

    /**
     * Get Activity details
     * @return string
     */
    public function getActivity($activityId)
    {
        $activity = "";
        $activity = $this->activities->create()->load($activityId);
        if ($activity->getIsActive()) {
            return $activity;
        }
        return $activity;
    }

}