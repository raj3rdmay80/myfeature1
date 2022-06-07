<?php
/**
 * Copyright (C) 2022 Zigly
 * @package  Zigly_Groomingapi
 */
declare(strict_types=1);

namespace Zigly\Groomingapi\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Zigly\Cityscreen\Model\ResourceModel\Cityscreen\CollectionFactory;

class Data extends AbstractHelper
{
    private $imageProcessor;
    private $imageContentFactory;
     
    public function __construct(
     ImageProcessorInterface $imageProcessor,
     ImageContentInterfaceFactory $imageContentFactory,
     \Magento\Framework\Filesystem $fileSystem,
     CollectionFactory $cityscreenCollection,
     \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
     \Magento\Framework\Image\AdapterFactory $adapterFactory
    ) {

        $this->imageProcessor = $imageProcessor;
        $this->imageContentFactory = $imageContentFactory;
        $this->cityscreenCollection = $cityscreenCollection;
        $this->filesystem = $fileSystem;
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
    }

    public function ImageuploadApi($filedata) {
       if (isset($filedata['file_data']) && !empty($filedata['file_data']["name"])){
           try{
               $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'file_data']);
               //check upload file type or extension
               $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'pdf', 'docx', 'doc']);
               $imageAdapter = $this->adapterFactory->create();
               $uploaderFactory->addValidateCallback('custom_image_upload',$imageAdapter,'validateUploadFile');
               $uploaderFactory->setAllowRenameFiles(true);
               $uploaderFactory->setFilesDispersion(true);
               $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
               $destinationPath = $mediaDirectory->getAbsolutePath('zigly');
               $result = $uploaderFactory->save($destinationPath);
               if (!$result) {
                   throw new LocalizedException(
                       __('File cannot be saved to path: $1', $destinationPath)
                   );
               }
               $imagePath = $result['file'];
               
               //Set file path with name for save into database
               return $imagePath;
           
           } catch (\Exception $e) {
                return false;
           }
       }
    
    }

    public function getCityIdPlan($appliedcitie){

        $collection = $this->cityscreenCollection->create();
        $collection->addFieldToFilter(
            array('cityscreen_id'),
            array(
                array('like' => '%'.$appliedcitie.'%')
            )             
        );
        foreach ($collection as $key => $data) {
            
        }
    }

    public function getErrorMessage()
    {
        return "There is some error";
    }

    public function getSuccesMessage()
    {
      return "Information Updated";
    }

}
