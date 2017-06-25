<?php
/**
 * Copyright © 2017 Opengento - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Opengento\FeatureToggle2\Model;

use Magento\Framework\Stdlib\Cookie\CookieReaderInterface;
use Magento\Framework\Stdlib\Cookie\CookieScopeInterface;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Opengento\FeatureToggle2\Helper\Toggle;


class CookieManager extends PhpCookieManager implements CookieManagerInterface
{
    const COOKIE_NAME = 'feature_toggle_data';

    /**
     * @var array
     */
    protected $customerData;

    public function __construct(
        CookieScopeInterface $scope,
        CookieReaderInterface $reader
    ) {
        parent::__construct($scope, $reader);
    }

    /**
     *
     */
    public function handleCustomerDataCookie()
    {
        $customerData = json_encode($this->_getPublicCustomerData());
        $this->setCookie('feature_toggle_data', $customerData, [PhpCookieManager::KEY_EXPIRE_TIME => time() + 86400*365*2]); // 2 years
    }

    /**
     * @param $valueCode
     * @return string
     */
    public function getUserValue($valueCode)
    {
        $customerValues = $this->_getPublicCustomerData();
        return $customerValues[$valueCode] ?? '';
    }

    /**
     * @return array|null|string
     */
    protected function _getPublicCustomerData()
    {
        if (null === $this->customerData) {
            if ($cookie = $this->getCookie(CookieManager::COOKIE_NAME)) {
                $this->customerData = json_decode($cookie, true) ?: [];
            }

            if (!$this->customerData) {
                $this->customerData = [
                    'percent' => rand(1, 100),
                ];
            }
        }
        return $this->customerData;
    }
}

