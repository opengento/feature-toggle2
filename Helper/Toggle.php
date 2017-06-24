<?php
/**
 * Copyright © 2017 Opengento - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Opengento\FeatureToggle2\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

use Opengento\FeatureToggle2\Model\Config\Reader;

use Qandidate\Toggle\Context as QuandidateContext;
use Qandidate\Toggle\Operator\LessThan;
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

    public function __construct(
        Reader $reader,
        QuandidateContext $quandidateContext,
        InMemoryCollection $inMemoryCollection,
        Context $context
    ) {
        $this->reader               = $reader;
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
        $this->lessThan             = new LessThan($this->scopeConfig->getValue('opengento_featuretoggle2/toggle/' . $toggleId . '_percent'));
        $this->operatorCondition    = new OperatorCondition(
            'percent', $this->lessThan
        );
        $this->toggle               = new QuandidateToggle($toggleId, array($this->operatorCondition));

        // Add the toggle to the manager
        $this->toggleManager->add($this->toggle);

        $context = $this->quandidateContext;
        $context->set('percent', 5);
        return $this->toggleManager->active($toggleId, $context);
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
}

