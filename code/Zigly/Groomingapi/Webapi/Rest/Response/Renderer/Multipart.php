<?php


namespace Zigly\GroomingService\Webapi\Rest\Response\Renderer;

use Magento\Framework\Webapi\Rest\Response\RendererInterface;
use Magento\Framework\Webapi\Exception as WebApiException;

class Multipart implements RendererInterface
{
    /**
     * Renderer mime type.
     */
    const MIME_TYPE = 'multipart/form-data';

    /**
     * @return string
     */
    public function getMimeType() {
        return self::MIME_TYPE;
    }

    /**
     * @param object|array|int|string|bool|float|null $data
     * @return string
     * @throws WebApiException
     */
    public function render($data) {
        // return reponse according to your needs
        if (is_string($data)) {
            return $data;
        }
        throw new WebApiException(
            __('Internal Server Error')
        );
    }
}