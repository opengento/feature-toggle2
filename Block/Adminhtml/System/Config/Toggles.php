<?php

namespace Opengento\FeatureToggle2\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;

use Opengento\FeatureToggle2\Helper\Toggle as ToggleHelper;

class Toggles extends Field
{
    /**
     * @var ToggleHelper
     */
    protected $toggleHelper;

    public function __construct(
        ToggleHelper $toggleHelper,
        Context $context,
        array $data = []
    ) {
        $this->toggleHelper = $toggleHelper;

        parent::__construct($context, $data);
    }
    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/toggles.phtml');
        }
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return array
     */
    public function getToggles()
    {
        return $this->toggleHelper->getToggles();
    }

    /**
     * @param $toggleId
     * @param $field
     * @return string
     */
    public function getConfig($toggleId, $field)
    {
        return $this->_scopeConfig->getValue('opengento_featuretoggle2/toggle/' . $toggleId . '_' . $field);
    }
}

