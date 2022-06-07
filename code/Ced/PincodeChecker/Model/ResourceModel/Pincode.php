<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_PincodeChecker
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\PincodeChecker\Model\ResourceModel;

use Magento\Framework\Filesystem\DirectoryList;

/**
 * Class Pincode
 * @package Ced\PincodeChecker\Model\ResourceModel
 */
class Pincode extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('ced_pincode_checker', 'id');
    }

    /**
     * @var int
     */
    protected $_importWebsiteId = 0;

    /**
     * @var array
     */
    public $_importErrors = [];

    /**
     * @var int
     */
    public $_importedRows = 0;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    public $_logger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    public $_filesystem;

    /**
     * @var \Ced\PincodeChecker\Model\PincodeFactory
     */
    protected $pincodeFactory;

    /**
     * Pincode constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Ced\PincodeChecker\Model\PincodeFactory $pincodeFactory
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Ced\PincodeChecker\Model\PincodeFactory $pincodeFactory,
        $connectionName = null
    )
    {
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
        $this->_filesystem = $filesystem;
        $this->pincodeFactory = $pincodeFactory;
        parent::__construct($context, $connectionName);
    }


    /**
     * @param $csvFile
     * @param $columns
     * @param bool $flush_flag
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importCsv($csvFile, $columns, $flush_flag = true)
    {
        $website_id = $this->_storeManager->getWebsite()->getId();
        $this->_importWebsiteId = (int)$website_id;
        $this->_importErrors = [];
        $this->_importedRows = 0;
        $csvFile = $csvFile['tmp_name'];
        $tmpDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
        $path = $tmpDirectory->getRelativePath($csvFile);
        $stream = $tmpDirectory->openFile($path);
        $headers = $stream->readCsv();

        if ($headers === false || count($headers) < 4) {
            $stream->close();
            throw new \Magento\Framework\Exception\LocalizedException(__('Please correct Pincode Checker File Format.'));
        }
        $connection = $this->getConnection();
        $connection->beginTransaction();
        // delete old data by website and condition name
        if ($flush_flag) {
            try {
                $rowNumber = 1;
                $importData = [];
                $updateData = [];
                while (false !== ($csvLine = $stream->readCsv())) {
                    $rowNumber++;

                    if (empty($csvLine)) {
                        continue;
                    }

                    $row = $this->_getImportRow($csvLine, $rowNumber);
                    
                    if (!$row) {
                        continue;
                    }

                    $exist = $this->pincodeFactory->create()->load($row[2], 'zipcode')->getData();
                    if (!empty($exist)) {
                        if ($row !== false) {
                            $updateData[] = $row;
                        }
                        continue;
                    }

                    if ($row !== false) {
                        $importData[] = $row;
                    }

                    if (count($importData) == 5000) {
                        $this->_saveImportData($importData, $columns);
                        $importData = [];
                    }
                }
                $array = array();
                foreach ($importData as $key => $data) {

                    if (in_array($data[2], $array)) {
                        unset($importData[$key]);
                    }
                    $array[] = $data[2];
                }
                $this->_saveImportData($importData, $columns);

                if (count($updateData) > 0) {
                    $uparray = array();
                    foreach ($updateData as $ukey => $udata) {

                        if (in_array($udata[2], $uparray)) {
                            unset($updateData[$ukey]);
                        } else {
                            $updateData[$ukey] = [
                                '`website_id` = "' . $udata[0] . '"',
                                '`can_cod` = "' . $udata[3] . '"',
                                '`can_ship` = "' . $udata[4] . '"',
                                '`days_to_deliver` = "' . $udata[5] . '"'
                            ];

                            $sql = "Update " . $this->getMainTable() . " Set " . implode(' ,', $updateData[$ukey]) . " where `zipcode` = '" . $udata[2] . "'";

                            $this->getConnection()->query($sql);

                            $this->_importedRows += count($udata);
                        }
                        $uparray[] = $udata[2];
                    }
                }

                $stream->close();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $connection->rollback();
                $stream->close();
                $this->_logger->critical($e);
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
            } catch (\Exception $e) {
                $connection->rollback();
                $stream->close();
                $this->_logger->critical($e);
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($e->getMessage())
                );
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while importing advance rates.')
                );
            }
            $connection->commit();
            if ($this->_importErrors) {
                $error = __(
                    'We couldn\'t import this file because of these errors: %1',
                    implode(" \n", $this->_importErrors)
                );
                throw new \Magento\Framework\Exception\LocalizedException($error);
            }
            return $this;
        }

    }

    /**
     * @return string
     */
    public function getVendorId()
    {
        return 'admin';
    }


    /**
     * @param $row
     * @param int $rowNumber
     * @return array|bool
     */
    protected function _getImportRow($row, $rowNumber = 0)
    {
        // validate row
        if (count($row) < 4) {
            $this->_importErrors[] = __('Please correct Table Rates format in the Row #%1.', $rowNumber);
            return false;
        }

        // strip whitespace from the beginning and end of each row
        foreach ($row as $k => $v) {
            $row[$k] = trim($v);
        }

        //$row[0] = preg_replace('/[^0-9\-]/', '', $row[0]);
        if ($row[0] == '' || !preg_match('/^[a-z0-9\-\s]+$/i', $row[0])) {
            $this->_importErrors[] = sprintf('Invalid Zipcode "%s" in the Row #%s', $row[0], $rowNumber);
            return false;
        } else {
            $zipcode = $row[0];//preg_replace('/[^0-9\-]/', '', $row[2]);
        }


        if ($row[1] == '') {
            $can_ship = 0;
        } elseif (strcasecmp($row[1], 'y') == 0 || strcasecmp($row[1], 'yes') == 0) {
            $can_ship = 1;
        } elseif (strcasecmp($row[1], 'n') == 0 || strcasecmp($row[1], 'no') == 0) {
            $can_ship = 0;
        } else {
            $can_ship = (int)$row[1];
        }

        if ($row[2] == '') {
            $can_cod = 0;
        } elseif (strcasecmp($row[2], 'y') == 0 || strcasecmp($row[2], 'yes') == 0) {
            $can_cod = 1;
        } elseif (strcasecmp($row[2], 'n') == 0 || strcasecmp($row[2], 'no') == 0) {
            $can_cod = 0;
        } else {
            $can_cod = (int)$row[2];
        }

        if (is_numeric($row[3])) {
            $days = $row[3];
        } else {
            $this->_importErrors[] = sprintf('Invalid Days to deliver "%s" in the Row #%s', $row[3], $rowNumber);
            return false;
        }

        $vendorId = $this->getVendorId();

        return [
            $this->_importWebsiteId,
            $vendorId,
            $zipcode,
            $can_cod,
            $can_ship,
            $days
        ];
    }

    /**
     * @param array $data
     * @param $columns
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _saveImportData(array $data, $columns)
    {
        if (!empty($data)) {
            $columns = [
                'website_id', 'vendor_id', 'zipcode', 'can_cod', 'can_ship', 'days_to_deliver'
            ];
            $this->getConnection()->insertArray(
                $this->getMainTable(),
                $columns,
                $data
            );

            $this->_importedRows += count($data);
            return $this;
        }
    }

}
