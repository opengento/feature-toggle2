<?php
/**
 * Copyright © 2017 Opengento - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Opengento\FeatureToggle2\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

use Opengento\FeatureToggle2\Model\Config\Reader;
use Opengento\FeatureToggle2\Model\CookieManager;

use Qandidate\Toggle\Context as QuandidateContext;
use Qandidate\Toggle\Operator\LessThanEqual;
use Qandidate\Toggle\OperatorCondition;
use Qandidate\Toggle\Toggle as QuandidateToggle;
use Qandidate\Toggle\ToggleCollection\InMemoryCollection;
use Qandidate\Toggle\ToggleManager;

class Toggle extends AbstractHelper
{
    /**
     * @var QuandidateContext
     */
    protected $quandidateContext;

    /**
     * @var LessThan
     */
    protected $lessThan;

    /**
     * @var OperatorCondition
     */
    protected $operatorCondition;

    /**
     * @var Toggle
     */
    protected $toggle;

    /**
     * @var InMemoryCollection
     */
    protected $inMemoryCollection;

    /**
     * @var ToggleManager
     */
    protected $toggleManager;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var array
     */
    protected $toggles;

    /**
     * @var array
     */
    protected $activeToggles;

    /**
     * @var CookieManager
     */
    protected $cookieManager;

    /**
     * @var Session
     */
    protected $customerSession;

    public function __construct(
        Session $customerSession,
        Reader $reader,
        CookieManager $cookieManager,
        QuandidateContext $quandidateContext,
        InMemoryCollection $inMemoryCollection,
        Context $context
    ) {
        $this->customerSession      = $customerSession;
        $this->reader               = $reader;
        $this->cookieManager        = $cookieManager;
        $this->inMemoryCollection   = $inMemoryCollection;
        $this->toggleManager        = new ToggleManager($this->inMemoryCollection);
        $this->quandidateContext    = $quandidateContext;

        parent::__construct($context);
    }

    /**
     * @param int $toggleId
     * @return bool
     */
    public function isToggleActive($toggleId)
    {
        $this->lessThan             = new LessThanEqual($this->scopeConfig->getValue('opengento_featuretoggle2/toggle/' . $toggleId . '_percent'));
        $this->operatorCondition    = new OperatorCondition('percent', $this->lessThan);
        $this->toggle               = new QuandidateToggle($toggleId, array($this->operatorCondition));

        // Add the toggle to the manager
        $this->toggleManager->add($this->toggle);

        $context = $this->quandidateContext;
        $context->set('percent', $this->getUserValue('percent'));
        return $this->toggleManager->active($toggleId, $context);
    }

    /**
     * @param $valueCode
     * @return string
     */
    public function getUserValue($valueCode)
    {
        return $this->cookieManager->getUserValue($valueCode);
    }

    /**
     * @return array
     */
    public function getToggles()
    {
        if (null === $this->toggles) {
            $this->toggles = $this->reader->read();
        }

        return $this->toggles;
    }

    /**
     * @return string
     */
    public function getAllActiveTogglesHash()
    {
        if ($activeToggles = $this->customerSession->getActiveToggles($this->activeToggles)) {
            $this->activeToggles = $activeToggles;
        } else {
            $toggles                = $this->getToggles();
            $this->activeToggles    = [];

            foreach ($toggles as $toggleId => $toggle) {
                if ($this->isToggleActive($toggleId)) {
                    $this->activeToggles[] = md5($toggleId);
                }
            }

            $this->customerSession->setActiveToggles($this->activeToggles);
        }

        return count($this->activeToggles) ? '_' . implode('_', $this->activeToggles) : '';
    }
}

