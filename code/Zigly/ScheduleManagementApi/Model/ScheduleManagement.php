<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Model;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingSlotTable\CollectionFactory as ScheduleCollectionFactory;
use Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHub\CollectionFactory as hubCollectionFactory;
use Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHubPincode\CollectionFactory as groomingHubPincode;

class ScheduleManagement implements \Zigly\ScheduleManagementApi\Api\ScheduleManagementInterface
{

    protected $_customerSession; 
    public function __construct(
        \Webkul\MobikulCore\Helper\Data $helper,
        StoreManagerInterface $storeManager,
        ScheduleCollectionFactory $ScheduleCollectionFactory,
        hubCollectionFactory $hubCollectionFactory,
        groomingHubPincode $groomingHubPincode,
        \Magento\Framework\Webapi\Rest\Request $request
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->hubCollectionFactory = $hubCollectionFactory;
        $this->ScheduleCollectionFactory = $ScheduleCollectionFactory;
        $this->groomingHubPincode = $groomingHubPincode;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchedule($date,$pincode,$customerIdtoken)
    {
        $date = $this->request->getParam('date');
        $pincode = $this->request->getParam('pincode');
        $customerIdtoken = $this->request->getParam('customer_id');
        $customerId = $this->helper->getCustomerByToken($customerIdtoken);
        try{
            $collection = $this->groomingHubPincode->create()
            ->addFieldtoFilter('pincode',['eq'=>$pincode])
            ->addFieldtoFilter('hub_status',['eq'=>1]);
            $collection->getSelect()
            ->joinLeft(
                ['zs_slottable'=>'zigly_schedulemanagementapi_groomingslottable'],
                "main_table.hub_id = zs_slottable.hub_id",
                [
                    'hub_id' => 'zs_slottable.hub_id',
                    'date'=> 'zs_slottable.date',
                    'slot_start_time'=> 'zs_slottable.slot_start_time',
                    'slot_end_time'=> 'zs_slottable.slot_end_time',
                    'allowed_booking'=> 'zs_slottable.allowed_booking',
                    'status'=> 'zs_slottable.status',
                ]
            )->where("zs_slottable.status !=0 AND zs_slottable.status !=''");

            if(!empty($collection->getData())){
                return $collection->getData();
            }else{
                $data = array("status"=>"false", "message"=>"No Time Slot Available Currently.");


                $response = new \Magento\Framework\DataObject();
                $response->setStatus($data['status']);
                $response->setMessage($data['message']);
                //print_r($response['_data:protected']); exit('ss');
                return (array)$response;
            }
        }catch (\Exception $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
        return $collection->getData();
    }
}

