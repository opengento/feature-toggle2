<?php
/**
 * Copyright © 2017 Opengento - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Opengento\FeatureToggle2\Plugin;

use Opengento\FeatureToggle2\Model\CookieManager;
use Magento\Framework\App\Response\Http;

class ActionInterfacePlugin
{
    /**
     * @var CookieManager
     */
    protected $cookieManager;

    /**
     * @var Http
     */
    protected $http;

    /**
     * ActionInterfacePlugin constructor.
     * @param CookieManager $cookieManager
     * @param Http $http
     */
    public function __construct(
        CookieManager $cookieManager,
        Http $http
    ) {
        $this->http             = $http;
        $this->cookieManager    = $cookieManager;
    }

    /**
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param $response
     * @return mixed
     */
    public function afterDispatch(
        \Magento\Framework\App\ActionInterface $subject,
        $response
    ) {
        if ($this->http->getHttpResponseCode() == 200) {
            $this->cookieManager->handleCustomerDataCookie();
        }

        return $response;
    }
}