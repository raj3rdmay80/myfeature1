<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    protected $session;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Zigly\Species\Model\SpeciesFactory $speciesFactory,
        \Zigly\Species\Model\BreedFactory $breedFactory,
        \Zigly\Managepets\Model\ManagepetsFactory $managepetsFactory,
        PageFactory $resultPageFactory
        )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->speciesFactory = $speciesFactory;
        $this->breedFactory = $breedFactory;
        $this->managepetsFactory = $managepetsFactory;
    }
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $data['customer_id'] = $this->customerSession->create()->getCustomer()->getId();
        $data['enable_breed']  = 1;
        $data['enable_species']  = 1;
        $flag = false;
        if (!empty($data['filedatas'])) {
            $data['filepath'] = $data['filedatas'];
        } elseif (!empty($data['avatar_file'])) {
            $data['filepath'] = $data['avatar_file'];
            $type = (int)$data['type'];
            if ($type == 1) {
                $dogImg = ['images/ava_dog-1.png', 'images/ava_dog-2.png', 'images/ava_dog-3.png'];
                if (!in_array($data['avatar_file'], $dogImg)) {
                    $flag = true;
                }
            } elseif ($type == 2) {
                $catImg = ['images/ava_cat.png', 'images/ava_dog-4.png', 'images/cat3.png'];
                if (!in_array($data['avatar_file'], $catImg)) {
                    $flag = true;
                }
            }
        }
        if ($flag) {
            if ($data['groomer_service'] == "grooming") {
                if ($data['service'] == "home") {
                    $this->messageManager->addError('Something went wrong while saving the Data');
                    return $resultRedirect->setPath('services/grooming/index?pet='.$data['pet_id'].'');
                } elseif ($data['service'] == "center") {
                    $this->messageManager->addError('Something went wrong while saving the Data');
                    return $resultRedirect->setPath('services/grooming/center?pet='.$data['pet_id'].'');
                }
            } else {
                $this->messageManager->addError('Something went wrong while saving the Data');
                return $resultRedirect->setPath('customer/account/');
            }
        }
        $data['name'] = trim($data['name']);
        if (!isset($data['age_year']) || !is_numeric($data['age_year']) || strlen($data['age_year']) > 2 || $data['age_year'] < 0 ) {
            $this->messageManager->addError('Please enter a value greater than or equal to 0 for pet year.');
            return $resultRedirect->setPath('customer/account/');
        }
        if (!isset($data['age_month']) || !is_numeric($data['age_month']) || strlen($data['age_month']) > 2 || $data['age_month'] < 0 ) {
            $this->messageManager->addError('Please enter a value greater than or equal to 0 for pet month.');
            return $resultRedirect->setPath('customer/account/');
        }
        $model = $this->managepetsFactory->create();
        if((int)$data['pet_id']){
            $data['entity_id'] = (int)$data['pet_id'];
            $model = $model->load((int)$data['pet_id']);
            if ($data['customer_id'] != $model->getCustomerId()) {
                $this->messageManager->addError('Something went wrong.');
                return $resultRedirect->setPath('customer/account/');
            }
        }
        try {
            if(isset($data['type']) && (int)$data['type'] && isset($data['breed']) && (int)$data['breed']){
                $type = (int)$data['type'];
                $specie = $this->speciesFactory->create()->load($type);
                if($specie->getStatus() != 1 || !$specie->getSpeciesId()){
                    $this->messageManager->addError('We cant allow with disabled type.');
                    return $resultRedirect->setPath('customer/account/');
                }
                $breed = (int)$data['breed'];
                $breedata = $this->breedFactory->create()->load($breed);
                if($breedata->getStatus() != 1 || !$breedata->getBreedId()){
                    $this->messageManager->addError('We cant allow with disabled Breed.');
                    return $resultRedirect->setPath('customer/account/');
                }
            }else{
                if ($data['groomer_service'] == "grooming") {
                    if ($data['service'] == "home") {
                        $this->messageManager->addError('Some Required option value missing');
                        return $resultRedirect->setPath('services/grooming/index?pet='.$data['pet_id'].'');
                    } elseif ($data['service'] == "center") {
                        $this->messageManager->addError('Some Required option value missing');
                        return $resultRedirect->setPath('services/grooming/center?pet='.$data['pet_id'].'');
                    }
                } else {
                    $this->messageManager->addError('Some Required option value missing');
                    return $resultRedirect->setPath('customer/account/');
                }
            }

            $model->setData($data);
            $model->save();
            if ($data['groomer_service'] == "grooming") {
                if ($data['service'] == "home") {
                    $this->messageManager->addSuccess('Pets has been saved successfully.');
                    return $resultRedirect->setPath('services/grooming/index?pet='.$model->getEntityId().'');
                } elseif ($data['service'] == "center") {
                    $this->messageManager->addSuccess('Pets has been saved successfully.');
                    return $resultRedirect->setPath('services/grooming/center?pet='.$model->getEntityId().'');
                }
            } else {
                $this->messageManager->addSuccess(__('Pets has been saved successfully.'));
                return $resultRedirect->setPath('customer/account/');
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the Data.'));
        }
        return $resultRedirect->setPath('customer/account/');
    }
}
