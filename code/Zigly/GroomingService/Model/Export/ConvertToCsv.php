<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Zigly\GroomingService\Model\Export;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Ui\Model\Export\ConvertToCsv as ConvertToCsvParent;
/**
 * Class ConvertToCsv
 */
class ConvertToCsv extends ConvertToCsvParent
{
    /**
     * @var DirectoryList
     */
    protected $directory;
    /**
     * @var MetadataProvider
     */
    protected $metadataProvider;
    /**
     * @var int|null
     */
    protected $pageSize = null;
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var Product
     */
    private $productHelper;
    /**
     * @var TimezoneInterface
     */
    private $timezone;
    protected $fileFactory;

    /**
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param int $pageSize
     * @throws FileSystemException
     */
    public function __construct(Filesystem $filesystem, Filter $filter, MetadataProvider $metadataProvider, TimezoneInterface $timezone, FileFactory $fileFactory, $pageSize = 200)
    {
        $this->filter = $filter;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->metadataProvider = $metadataProvider;
        $this->pageSize = $pageSize;
        parent::__construct($filesystem, $filter, $metadataProvider, $pageSize);
        $this->timezone = $timezone;
        $this->fileFactory = $fileFactory;
    }
    /**
     * Returns CSV file
     *
     * @return array
     * @throws LocalizedException
     * @throws \Exception
     */
    public function getCsvFile()
    {
        $component = $this
            ->filter
            ->getComponent();
        $name = 'ziglyservices';
        $file = 'export/' . $name . '.csv';
        $this
            ->filter
            ->prepareComponent($component);
        $this
            ->filter
            ->applySelectionOnTargetProvider();
        $dataProvider = $component->getContext()
            ->getDataProvider();
        $fields = $this
            ->metadataProvider
            ->getFields($component);
        if ($component->getName() == 'zigly_groomingservice_grooming_listing')
        {
            $fields[] = 'firstname';
            $fields[] = 'lastname';
            $fields[] = 'speciesname';
            $fields[] = 'breedname';
            $fields[] = 'pet_category';
            $fields[] = 'plan_activites';
            $fields[] = 'wallet_money';
            $fields[] = 'coupon_code';
            $fields[] = 'payment_mode';
            $fields[] = 'street';
            $fields[] = 'region';
            $fields[] = 'pet_age';
            $fields[] = 'email';
            $fields[] = 'phone_no';
            $fields[] = 'created_at';
        }
        $options = $this
            ->metadataProvider
            ->getOptions();
        $this
            ->directory
            ->create('export');
        $stream = $this
            ->directory
            ->openFile($file, 'w+');
        $stream->lock();
        $header = $this
            ->metadataProvider
            ->getHeaders($component);
        if ($component->getName() == 'zigly_groomingservice_grooming_listing')
        {
            $header[] = 'First Name';
            $header[] = 'Last Name';
            $header[] = 'Specie Type';
            $header[] = 'Breed Type';
            $header[] = 'Pet Category';
            $header[] = 'Plan Activities';
            $header[] = 'Walet Money';
            $header[] = 'Coupon Code';
            $header[] = 'Payment Mode';
            $header[] = 'Street';
            $header[] = 'Region';
            $header[] = 'Pet Age';
            $header[] = 'Email';
            $header[] = 'Phone No';
            $header[] = 'Booking Date';
        }
        $stream->writeCsv($header);
        $i = 1;
        $setpagelimit = 100; //setPageSize
        if ($component->getName() == 'zigly_groomingservice_grooming_listing')
        {
            //     $searchCriteria = $dataProvider->getSearchCriteria()
            // ->setCurrentPage($i)
            // ->setPageSize($this->pageSize);
            $totalCount = (int)$dataProvider->getSearchResult()
                ->setPageSize($setpagelimit)->getData();
        }
        else
        {
            $searchCriteria = $dataProvider->getSearchCriteria()
                ->setCurrentPage($i)->setPageSize($this->pageSize);
            $totalCount = (int)$dataProvider->getSearchResult()
                ->getTotalCount();
            // code...
            
        }
        while ($totalCount > 0)
        {
            $items = $dataProvider->getSearchResult()
                ->getItems();

            foreach ($items as $item)
            {
                $this
                    ->metadataProvider
                    ->convertDate($item, $component->getName());
                $stream->writeCsv($this
                    ->metadataProvider
                    ->getRowData($item, $fields, $options));
            }
            if ($component->getName() == 'zigly_groomingservice_grooming_listing')
            {
                // $searchCriteria->setCurrentPage(++$i);
                $totalCount = $totalCount - $this->pageSize;
            }
            else
            {
                $searchCriteria->setCurrentPage(++$i);
                $totalCount = $totalCount - $this->pageSize;
            }
        }
        $stream->unlock();
        $stream->close();

        if ($component->getName() == 'zigly_groomingservice_grooming_listing')
        {
            return $this
                ->fileFactory
                ->create('ziglyservices.csv', ['type' => 'filename', 'value' => $file, 'rm' => true
            // can delete file after use
            ], 'var');
        }
        else
        {
             return ['type' => 'filename', 'value' => $file, 'rm' => true
            // can delete file after use
            ];
        }
    }
}

