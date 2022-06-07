<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Adminhtml customer manage pets grid block
 */
class Managepets extends \Magento\Backend\Block\Widget\Grid\Extended
{
    const MALE = 1;

    const FEMALE = 2;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Sales\Model\Resource\Order\Grid\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Zigly\Managepets\Model\ResourceModel\Managepets\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Zigly\Managepets\Model\ResourceModel\Managepets\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        $this->_logger = $logger;
        $this->_coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize the service grid.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('managepets_view_customer_grid');
        $this->setDefaultSort('created_at', 'desc');
        // $this->setSortable(false);
        // $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setUseAjax(true);

    }
    /**
     * {@inheritdoc}
    //  */
    // protected function _preparePage()
    // {
    //     $this->getCollection()->setPageSize(5)->setCurPage(1);
    // }
 //
    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $customerid = $this->getCustomerId();
        if(!$customerid){
            $customerid = $this->getManualCustomerId();
            if(!$customerid){
                $customerid = $this->getRequest()->getParam('id');
            }
        }
        $collection = $this->_collectionFactory->create()->addFieldtoFilter('customer_id',$customerid);
        $collection->getSelect()
        ->joinLeft(
            ['species'=>'zigly_species_species'],
            "main_table.type = species.species_id",
            [
                'speciesname' => 'species.name'
            ]
        );
        $collection->getSelect()
        ->joinLeft(
            ['breed'=>'zigly_species_breed'],
            "main_table.breed = breed.breed_id",
            [
                'breedname' => 'breed.name'
            ]
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        $customerid = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        if(!$customerid){
            $customerid = $this->getManualCustomerId();
            if(!$customerid){
                $customerid = $this->getRequest()->getParam('id');
            }
        }
        return $customerid;
    }

    /**
     * {@inheritdoc}
     */
     protected function _prepareColumns()
    {
        $this->addColumn(
           'filepath',
           array(
               'header' => __('Image'),
               'index' => 'filepath',
               'renderer'  => '\Zigly\Managepets\Block\Adminhtml\Grid\Renderer\Image',
           )
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Pet Name'),
                'index' => 'name',
            ]
        );
        $this->addColumn(
            'speciesname',
            [
                'header' => __('Type'),
                'index' => 'speciesname',
            ]
        );
        $this->addColumn(
            'breedname',
            [
                'header' => __('Breed'),
                'index' => 'breedname',
            ]
        );
        $this->addColumn(
            'age_year',
            [
                'header' => __('Age (year)'),
                'index' => 'age_year',
            ]
        );
        $this->addColumn(
            'age_month',
            [
                'header' => __('Age (month)'),
                'index' => 'age_month',
            ]
        );
        $this->addColumn(
            'pet_dob',
            [
                'header' => __('Date of birth'),
                'index' => 'pet_dob',
                'type' => 'date',
            ]
        );
        $this->addColumn(
            'gender',
            [
                'header' => __('Gender'),
                'index' => 'gender',
                'type' => 'options',
                'options' => [self::MALE => __('Male'), self::FEMALE => __('Female')],
            ]
        );
        /*if($this->_authorization->isAllowed('Zigly_Managepets::Managepets_update')){
            $this->addColumn(
               'Edit',
               array(
                   'header' => __('Edit'),
                   'index' => 'entity_id',
                   'renderer'  => '\Zigly\Managepets\Block\Adminhtml\Grid\Renderer\Editlink',
               )
            );
        }*/
        if($this->_authorization->isAllowed('Zigly_Managepets::Managepets_delete')){
            $this->addColumn(
               'Delete',
               array(
                   'header' => __('Delete'),
                   'index' => 'entity_id',
                   'renderer'  => '\Zigly\Managepets\Block\Adminhtml\Grid\Renderer\Deletelink',
               )
            );
        }
        return parent::_prepareColumns();
    }

    /**
     * Get headers visibility
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
    //  */
    public function getHeadersVisibility()
    {
        return $this->getCollection()->getSize() >= 0;
    }

    /**
     * {@inheritdoc}
     */
     public function getGridUrl()
    {
        return $this->getUrl('managepets/*/loadgrid', ['id' =>$this->getCustomerId(),'_current' => true]);
    }

}
