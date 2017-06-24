<?php
/**
 * Copyright © 2017 Opengento - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace Opengento\FeatureToggle2\Model\Config;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function convert($source)
    {
        $xpath = new \DOMXPath($source);
        $output = [];
        foreach ($xpath->evaluate('/toggles/toggle') as $typeNode) {

            $output[$typeNode->getAttribute('id')] = [];
            foreach ($typeNode->childNodes as $childNode) {
                if ($childNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $output[$typeNode->getAttribute('id')][$childNode->nodeName] = $childNode->nodeValue;
            }
        }
        return $output;
    }

}
