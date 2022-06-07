<?php

namespace Zigly\Referral\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magento\Customer\Api\CustomerRepositoryInterface;

class GenerateReferCode extends Command
{

    private $state;

    protected $_customerFactory;

    protected $_customerRepository;

    protected $_helper;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        \Zigly\Referral\Helper\Data $helper
    ) {
        $this->state = $state;
        $this->_customerRepository = $customerRepository;
        $this->_customerFactory = $customerFactory;
        $this->_helper = $helper;
        parent::__construct();
    }
    protected function configure()
    {
        $this->setName('customer:generateReferCode')
            ->setDescription('Customer Refer Code Generation Script');
        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        $customerCollection = $this->_customerFactory->create();
        $customerCollection->addAttributeToSelect('*')
            ->addAttributeToFilter('refercode', array('null' => true))
            ->load();
        foreach ($customerCollection as $customerData) {
            $customer = $this->_customerRepository->getById($customerData->getId());
            $referCode = $this->_helper->getReferCode();
            if ($referCode) {
                $customer->setCustomAttribute('refercode', $referCode);
                $this->_customerRepository->save($customer);
                $output->writeln('Customer Id: ' . $customerData->getId() . ', Refer Code: ' . $referCode);
            }
        }
    }
}
