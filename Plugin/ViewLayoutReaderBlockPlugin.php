<?php
/**
 * Copyright © 2017 Opengento - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Opengento\FeatureToggle2\Plugin;

use Opengento\FeatureToggle2\Helper\Toggle;

class ViewLayoutReaderBlockPlugin
{
    /**
     * @var Toggle
     */
    protected $toggleHelper;

    public function __construct(Toggle $toggleHelper)
    {
        $this->toggleHelper = $toggleHelper;
    }

    /**
     * @param \Magento\Framework\View\Layout\Reader\Block $subject
     * @param callable $proceed
     * @param array ...$args
     * @return $this
     */
    public function aroundInterpret(
        \Magento\Framework\View\Layout\Reader\Block $subject,
        callable $proceed,
        ...$args
    ) {
        /** @var \Magento\Framework\View\Layout\Element $currentElement */
        $currentElement = $args[1];

        if ($toggle = $currentElement->getAttribute('toggle')) {
            // Return interpret only if toggle is active
            if (!$this->toggleHelper->isToggleActive($toggle)) {
                return $this;
            }
        }
        return $proceed(...$args);
    }
}