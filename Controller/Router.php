<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develodesign\Punchout\Controller;

use Develodesign\Punchout\Helper\PunchoutConfigHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;

class Router implements RouterInterface
{

    /**
     * @var PunchoutConfigHelper
     */
    protected $punchoutConfigHelper;

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /** @var CustomerSession */
    protected $customerSession;


    /**
     * Router constructor
     *
     * @param ActionFactory $actionFactory
     * @param PunchoutConfigHelper $punchoutConfigHelper
     * @param ResponseInterface $response
     */
    public function __construct(
        ActionFactory $actionFactory,
        PunchoutConfigHelper $punchoutConfigHelper,
        \Magento\Framework\App\ResponseInterface $response,
        CustomerSession $customerSession
    ) {
        $this->actionFactory = $actionFactory;
        $this->punchoutConfigHelper = $punchoutConfigHelper;
        $this->response = $response;
        $this->customerSession = $customerSession;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $redirectCheckoutPath = $this->punchoutConfigHelper->getRedirectCheckoutPath();
        $result = null;

        // Only apply if the customer is a punchout user, and there is a valid redirectCheckoutPath set.
        if(!empty($redirectCheckoutPath) && $this->isPunchoutCustomer()) {

            // Get the path info from the request
            $pathInfo = $request->getPathInfo();

            // Check the route is valid / not empty
            // Check the pathInfo matches the redirectCheckoutPath
            if($this->validateRoute($request) && $pathInfo == $redirectCheckoutPath) {

                // Redirect back to the /checkout/cart route.
                $result = $this->response->setRedirect('/checkout/cart', 302)->sendResponse();

            }

        }

        return $result;

    }

    public function isPunchoutCustomer() {

        $customerSession = $this->customerSession;

        // If a customer has any of these assigned to the session and they are logged in, they are a punchout customer.
        if(
            $customerSession->isLoggedIn()
            && (
                !empty($customerSession->getPayloadId()) // cxml
                || !empty($customerSession->getSenderIdentity()) // cxml
                || !empty($customerSession->getHookUrl()) // oci
                || !empty($customerSession->getCaller()) // oci
            )
        ) {
            return true;
        }

        return false;

    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    public function validateRoute(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');

        if(empty($identifier)) {
            return false;
        } else {
            return true;
        }

    }
}
