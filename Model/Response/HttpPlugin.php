<?php

namespace Develodesign\Punchout\Model\Response;

use Develodesign\Punchout\Helper\PunchoutConfigHelper;
use Magento\Framework\App\Response\Http;

class HttpPlugin
{
    /**
    * Prefix of model events names
    *
    * @var PunchoutConfigHelper
    */
    protected $_helper;

    /**
    * @param PunchoutConfigHelper
    */
    public function __construct(
        PunchoutConfigHelper $_helper
    ) {
        $this->_helper = $_helper;
    }

    /**
     * @param Http $subject
     */
    public function beforeSendHeaders(Http $subject)
    {
        $subject->clearHeader(Http::HEADER_X_FRAME_OPT);
        $allowedFrames = $this->_helper->getAllowedIframes();
        if (!$allowedFrames || !count($allowedFrames)) {
            return $this;
        }
        $this->remove($subject, 'Content-Security-Policy');
        $subject->setHeader(
            'Content-Security-Policy',
            "frame-ancestors 'self' " . implode(' ', $allowedFrames)
        );
    }


    protected function remove($subject, $name)
    {
        if ($subject->getHeaders()->has($name)) {
             $_header = $subject->getHeader($name);

            if ($_header instanceof \ArrayIterator) {
                 $_header = $_header->current();
            }

            if ($_header instanceof \Zend\Http\Header\HeaderInterface) {
                 $subject->getHeaders()->removeHeader($_header);
            }
        }
        return $this;
    }
}
