<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Controller\Adminhtml\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Controller\RegistryConstants;

class Save extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    protected $session;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Zigly\Species\Model\SpeciesFactory $speciesFactory,
        \Zigly\Species\Model\BreedFactory $breedFactory,
        \Zigly\Managepets\Model\ManagepetsFactory $managepetsFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory
        )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->speciesFactory = $speciesFactory;
        $this->breedFactory = $breedFactory;
        $this->managepetsFactory = $managepetsFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
    }
    public function execute()
    {
            return;
            $result = $this->_resultJsonFactory->create();
            $resultPage = $this->resultPageFactory->create();
            $success = 0;
            $disablestatus = '';
            $data = $this->getRequest()->getPostValue();
            if(!empty($data['filedatas'])){
                $data['filepath'] = implode(",",$data['filedatas']);
            }
            $data['enable_breed']  = 1;
            $data['enable_species']  = 1;
            $model = $this->managepetsFactory->create();
            if((int)$data['pet_id']){
               $data['entity_id'] = (int)$data['pet_id'];
            }
            $model->setData($data);
            try {
                if(isset($data['type']) && (int)$data['type']){
                    $type = (int)$data['type'];
                    $specie = $this->speciesFactory->create()->load($type);
                    if($specie->getStatus() != 1 || !$specie->getSpeciesId()){
                        $disablestatus = "We cant allow with disabled type.";
                    }
                }
                if(isset($data['breed']) && (int)$data['breed']){
                    $breed = (int)$data['breed'];
                    $breedata = $this->breedFactory->create()->load($type);
                    if($breedata->getStatus() != 1 || !$breedata->getBreedId()){
                        $disablestatus = "We cant allow with disabled Breed.";
                    }
                }
                if(!$disablestatus){
                    $model->save();
                    $success = 1;
                }
                 $block = $resultPage->getLayout()
                ->createBlock('Zigly\Managepets\Block\Adminhtml\Edit\Tab\View\Managepets')->setManualCustomerId($data['customer_id'])->toHtml();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $block = $e->getMessage();
            } catch (\RuntimeException $e) {
                $block = $e->getMessage();
            } catch (\Exception $e) {
               $block = $e->getMessage();
            }
            $result->setData(['output' => $block,'success' => $success,'disablestatus' => $disablestatus]);
            return $result;
        }
}
