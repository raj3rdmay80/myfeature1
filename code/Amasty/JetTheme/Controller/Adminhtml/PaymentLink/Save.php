<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Controller\Adminhtml\PaymentLink;

use Amasty\JetTheme\Api\Data\PaymentLinkInterface;
use Amasty\JetTheme\Api\Data\PaymentLinkInterfaceFactory;
use Amasty\JetTheme\Api\PaymentLinkRepositoryInterface;
use Amasty\JetTheme\Model\PaymentLink\SvgProvider;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Amasty_JetTheme::manage_payment_links';

    /**
     * @var PaymentLinkRepositoryInterface
     */
    private $paymentLinkRepository;

    /**
     * @var PaymentLinkInterfaceFactory
     */
    private $paymentLinkFactory;

    /**
     * @var SvgProvider
     */
    private $svgProvider;

    public function __construct(
        Context $context,
        PaymentLinkRepositoryInterface $paymentLinkRepository,
        PaymentLinkInterfaceFactory $paymentLinkFactory,
        SvgProvider $svgProvider
    ) {
        parent::__construct($context);
        $this->paymentLinkRepository = $paymentLinkRepository;
        $this->paymentLinkFactory = $paymentLinkFactory;
        $this->svgProvider = $svgProvider;
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        $id = $this->getRequest()->getParam('entity_id', null);

        try {
            if ($id) {
                $model = $this->paymentLinkRepository->get((int)$id);
            } else {
                $model = $this->paymentLinkFactory->create();
            }
        } catch (NoSuchEntityException $e) {
            $model = $this->paymentLinkFactory->create();
        }

        try {
            $model->setData($this->prepareData($data));
            $this->paymentLinkRepository->save($model);
            $this->messageManager->addSuccessMessage(__('Payment link have been saved.'));

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Payment link.'));
        }

        return $resultRedirect->setPath(
            '*/*/edit',
            ['id' => $model->getId() ?? $this->getRequest()->getParam('id')]
        );
    }

    /**
     * @param array $data
     * @return array
     */
    private function prepareData(array $data): array
    {
        if (!empty($data['default_icon'])) {
            $data['icon_file'] = null;
            $data['skip_image_upload'] = true;
            $data['default_icon_content'] = base64_encode(
                $this->svgProvider->getSvgContentByKey($data['default_icon'])
            );
        } elseif (is_array($data['icon_file']) && isset($data['icon_file'][0]['tmp_name'])) {
            $data['default_icon_content'] = null;
            $data['default_icon'] = null;
            $data[PaymentLinkInterface::ICON_FILE] = $data['icon_file'][0]['name'];
        } elseif (isset($data[PaymentLinkInterface::ICON_FILE][0]['name'])) {
            $data[PaymentLinkInterface::ICON_FILE] = $data[PaymentLinkInterface::ICON_FILE][0]['name'] ?? '';
            $data['default_icon_content'] = null;
            $data['default_icon'] = null;
            $data['skip_image_upload'] = true;
        }

        return $data;
    }
}
