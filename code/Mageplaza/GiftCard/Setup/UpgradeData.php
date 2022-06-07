<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Setup;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Backend\Price;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Driver\File as DriverFile;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;
use Mageplaza\GiftCard\Model\Product\Type\GiftCard;
use Mageplaza\GiftCard\Model\TemplateFactory;
use Zend_Validate_Exception;

/**
 * Class UpgradeData
 * @package Mageplaza\GiftCard\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * @var string
     */
    private $viewDir = '';

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var File
     */
    private $ioFile;

    /**
     * @var DriverFile
     */
    private $driverFile;

    /**
     * @var ReadInterface
     */
    private $mediaDirectory;

    /**
     * @var Csv
     */
    private $csvReader;

    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * UpgradeData constructor.
     *
     * @param CategorySetupFactory $categorySetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param Reader $moduleReader
     * @param File $ioFile
     * @param DriverFile $driverFile
     * @param Filesystem $filesystem
     * @param Csv $csvReader
     * @param TemplateFactory $templateFactory
     *
     * @throws FileSystemException
     */
    public function __construct(
        CategorySetupFactory $categorySetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        Reader $moduleReader,
        File $ioFile,
        DriverFile $driverFile,
        Filesystem $filesystem,
        Csv $csvReader,
        TemplateFactory $templateFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->moduleReader = $moduleReader;
        $this->ioFile = $ioFile;
        $this->driverFile = $driverFile;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->csvReader = $csvReader;
        $this->templateFactory = $templateFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var CategorySetup $catalogSetup */
        $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $catalogSetup->updateAttribute(Product::ENTITY, 'price_rate', [
                'frontend_input' => 'price',
                'backend_model' => Price::class
            ]);
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $catalogSetup->updateAttribute(Product::ENTITY, 'gift_card_amounts', 'backend_type', 'text');
        }

        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $catalogSetup->addAttribute(Product::ENTITY, 'mpgiftcard_conditions', array_merge($this->getOptions(), [
                'label' => 'Gift Code Condition',
                'type' => 'text',
                'input' => 'text',
                'sort_order' => 110,
                'visible' => false,
            ]));
        }

        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            /** @var SalesSetup $salesInstaller */
            $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

            /** @var QuoteSetup $quoteInstaller */
            $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

            $salesInstaller->addAttribute('order', 'mp_gift_cards', ['type' => 'text']);
            $quoteInstaller->addAttribute('quote', 'mp_gift_cards', ['type' => 'text']);

            $connection = $setup->getConnection();

            // populate data from gift_cards to mp_gift_cards
            foreach (['sales_order', 'quote'] as $item) {
                $table = $setup->getTable($item);

                $select = $connection->select()->from(['org' => $table]);

                $select->reset()->joinLeft(
                    ['clone' => $table],
                    'org.entity_id = clone.entity_id',
                    ['mp_gift_cards' => 'clone.gift_cards']
                );

                $updateSql = $select->crossUpdateFromSelect(['org' => $table]);

                $connection->query($updateSql);
            }
        }

        if (version_compare($context->getVersion(), '1.0.6') < 0) {
            $catalogSetup->updateAttribute(Product::ENTITY, 'min_amount', 'frontend_label', 'Range From');
        }

        // create sample template
        if (version_compare($context->getVersion(), '1.0.7') < 0) {
            // copy sample template image to media directory
            $this->copyTemplateSampleImage();

            $file = $this->getModuleDir() . '/Files/Sample/mageplaza_giftcard_template.csv';
            if ($this->ioFile->fileExists($file)) {
                $rows = $this->csvReader->getData($file);
                $header = array_shift($rows);
                foreach ($rows as $row) {
                    $data = [];
                    foreach ($row as $key => $value) {
                        $data[$header[$key]] = $value;
                    }
                    unset($data['template_id']);
                    $this->templateFactory->create()
                        ->addData($data)
                        ->save();
                }
            }
        }

        $setup->endSetup();
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            'group' => 'Gift Card Information',
            'backend' => '',
            'frontend' => '',
            'class' => '',
            'source' => '',
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'unique' => false,
            'apply_to' => GiftCard::TYPE_GIFTCARD,
            'used_in_product_listing' => true
        ];
    }

    /**
     * @return string
     */
    protected function getModuleDir()
    {
        if (!$this->viewDir) {
            $this->viewDir = $this->moduleReader->getModuleDir(
                '',
                'Mageplaza_GiftCard'
            );
        }

        return $this->viewDir;
    }

    /**
     * @throws FileSystemException
     * @throws Exception
     */
    public function copyTemplateSampleImage()
    {
        $path = $this->getModuleDir() . '/Files/Sample/template';

        $files = $this->driverFile->readDirectory($path);

        $mediaTemplateSamplePath = $this->mediaDirectory->getAbsolutePath('mageplaza/giftcard/sample/template/');
        $this->ioFile->checkAndCreateFolder($mediaTemplateSamplePath);

        foreach ($files as $file) {
            $fileName = $this->ioFile->getPathInfo($file)['basename'];

            $this->ioFile->read(
                $file,
                $mediaTemplateSamplePath . $fileName
            );
        }
    }
}
