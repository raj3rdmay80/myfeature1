<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Console\Command\ImageOptimizer\Operation;

use Amasty\ImageOptimizer\Api\ImageQueueServiceInterface;
use Amasty\ImageOptimizer\Model\ConfigProvider;
use Amasty\ImageOptimizer\Model\Image\CheckTools;
use Amasty\ImageOptimizer\Model\Image\ForceOptimization;
use Amasty\ImageOptimizer\Model\Image\GenerateQueue;
use Amasty\ImageOptimizer\Model\ImageProcessor;
use Amasty\JetTheme\Model\ImageOptimizer\ImageSettingsGenerator;
use Amasty\PageSpeedTools\Model\JobManager;
use Amasty\PageSpeedTools\Model\JobManagerFactory;
use Magento\Framework\App\ObjectManager;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Optimize
{
    /**
     * @var ForceOptimization
     */
    private $forceOptimization;

    /**
     * @var ImageQueueServiceInterface
     */
    private $queueService;

    /**
     * @var GenerateQueue
     */
    private $generateQueue;

    /**
     * @var JobManagerFactory
     */
    private $jobManagerFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var array
     */
    private $batches = [];

    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var ImageSettingsGenerator
     */
    private $imageSettingsGenerator;

    /**
     * @var CheckTools
     */
    private $checkTools;

    public function __construct(
        ImageSettingsGenerator $imageSettingsGenerator,
        ForceOptimization $forceOptimization,
        ImageQueueServiceInterface $queueService,
        GenerateQueue $generateQueue,
        JobManagerFactory $jobManagerFactory,
        ConfigProvider $configProvider,
        ImageProcessor $imageProcessor,
        CheckTools $checkTools
    ) {
        $this->imageSettingsGenerator = $imageSettingsGenerator;
        $this->queueService = $queueService;
        $this->forceOptimization = $forceOptimization;
        $this->generateQueue = $generateQueue;
        $this->jobManagerFactory = $jobManagerFactory;
        $this->configProvider = $configProvider;
        $this->imageProcessor = $imageProcessor;
        $this->checkTools = $checkTools;
    }

    /**
     * @inheritdoc
     */
    public function execute(
        InputInterface $input,
        OutputInterface $output
    ) :?int {
        $output->writeln('<info>Generating Images Queue.</info>');
        $imageSettings = $this->imageSettingsGenerator->getSettingsToProcess();
        $errors = $this->checkTools->check($imageSettings);
        if (count($errors)) {
            $output->writeln('<error>' . implode('</error>' . PHP_EOL . '<error>', $errors));

            return 0;
        }

        $queueSize = $this->generateQueue->generateQueue([$imageSettings]);
        $counter = 0;

        $maxJobs = $this->configProvider->getMaxJobsCount();
        $maxJobs = (int)$maxJobs;
        if ($maxJobs > 1) {
            if (!function_exists('pcntl_fork')) {
                $output->writeln(__('Warning: \'pcntl\' php extension is required for parallel image optimization.'));
                $maxJobs = 1;
            }
        }

        $multiProcessMode = $maxJobs > 1;

        if ($multiProcessMode) {
            /** @var JobManager $jobManager */
            $jobManager = $this->jobManagerFactory->create(['maxJobs' => $maxJobs]);
            while (!$this->queueService->isQueueEmpty()) {
                $this->batches[] = $this->queueService->shuffleQueues(100);
            }
        }

        /** @var ProgressBar $progressBar */
        $progressBar = ObjectManager::getInstance()->create(
            ProgressBar::class,
            [
                'output' => $output,
                'max' => ceil($queueSize/100)
            ]
        );
        $progressBar->setFormat(
            '<info>%message%</info> %current%/%max% [%bar%]'
        );
        $output->writeln('<info>Optimization Process Started.</info>');
        $progressBar->start();
        $progressBar->display();

        if ($multiProcessMode) {
            while (!empty($this->batches)) {
                if ($jobManager->waitForFreeSlot()) {
                    $progressBar->advance();
                }

                $imagesQueue = array_shift($this->batches);
                $counter += count($imagesQueue);
                $progressBar->setMessage('Process Images ' . ($counter) . ' from ' . $queueSize . '...');
                $progressBar->display();
                if (!$jobManager->fork()) { // Child process
                    foreach ($imagesQueue as $queue) {
                        $this->imageProcessor->process($queue);
                    }

                    return 0;
                }

                while (!$this->queueService->isQueueEmpty()) {
                    $generatedQueues = $this->queueService->shuffleQueues(100);
                    $this->batches[] = $generatedQueues;
                    $queueSize += count($generatedQueues);
                    $progressBar->setMaxSteps($progressBar->getMaxSteps() + 1);
                }
            }

            $jobManager->waitForAllJobs();
        } else {
            while (!$this->queueService->isQueueEmpty()) {
                $progressBar->setMessage('Process Images ' . (($counter++) * 100) . ' from ' . $queueSize . '...');
                $progressBar->display();
                $this->forceOptimization->execute(100);
                $progressBar->advance();
            }
        }

        $progressBar->setMessage('Process Images ' . $queueSize . ' from ' . $queueSize . '...');
        $progressBar->display();
        $progressBar->finish();
        $output->writeln('');
        $output->writeln('<info>Images were optimized successfully.</info>');

        return 0;
    }
}
