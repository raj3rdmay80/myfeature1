<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
namespace Zigly\VetConsulting\Controller\Vet;

use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Framework\File\Uploader;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Image\AdapterFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\FileSystemException;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

/**
 * Class Upload
 */
class Fileupload extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $adapterFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    private $productMediaConfig;

    /**
     * @param Context $context
     * @param Filesystem $filesystem
     * @param Config $productMediaConfig
     * @param RawFactory $resultRawFactory
     * @param AdapterFactory $adapterFactory
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        Filesystem $filesystem = null,
        UploaderFactory $uploaderFactory,
        Config $productMediaConfig = null,
        StoreManagerInterface $storeManager,
        AdapterFactory $adapterFactory = null
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->uploaderFactory = $uploaderFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->adapterFactory = $adapterFactory ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Image\AdapterFactory::class);
        $this->filesystem = $filesystem ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Filesystem::class);
        $this->productMediaConfig = $productMediaConfig ?: ObjectManager::getInstance()
            ->get(\Magento\Catalog\Model\Product\Media\Config::class);
    }

    /**
     * Upload image(s) to the product gallery.
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            $result = [];
            $images = $this->getRequest()->getFiles('image');
            if(count($images)){
                foreach($images as $key => $value){
                    if(isset($value['name']) && $value['name'] != '') {
                        $uploader = $this->uploaderFactory->create(['fileId' => 'image['.$key.']']);
                        /*$imageAdapter = $this->adapterFactory->create();
                        $uploader->addValidateCallback('image_doc_upload', $imageAdapter, 'validateUploadFile');*/
                        $uploader->setAllowedExtensions(['pdf', 'docx', 'doc', 'jpg', 'jpeg', 'png']);
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);
                        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                        $result[] = $uploader->save($mediaDirectory->getAbsolutePath('zigly/'));
                        unset($result[$key]['tmp_name']);
                        unset($result[$key]['path']);
                        $result[$key]['url'] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'zigly/'.$result[$key]['file'];
                        if($array = getimagesize($result[$key]['url'] )) {
                            $result[$key]['type'] = 'image';
                        }else{
                             $result[$key]['type'] = 'file';
                        }
                        if(isset($result[$key]['file'])){
                            $result[$key]['file'] = $result[$key]['file'];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $result = ['error' => ($e->getMessage() == 'Disallowed file type.') ? 'Only .pdf, .docx, .doc, .jpg, .jpeg, .png extensions are allowed' :$e->getMessage() , 'errorcode' => $e->getCode()];
        }
        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/json');
        $response->setContents(json_encode($result));
        return $response;
    }
}