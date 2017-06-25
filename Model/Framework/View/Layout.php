<?php
/**
 * Copyright © 2017 Opengento - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */

namespace Opengento\FeatureToggle2\Model\Framework\View;

use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Event\ManagerInterface;
use Opengento\FeatureToggle2\Helper\Toggle;
use Magento\Framework\View\Layout\ProcessorFactory;
use Magento\Framework\View\Layout\ReaderPool;
use Magento\Framework\View\Layout\Data;
use Magento\Framework\View\Layout\GeneratorPool;
use Magento\Framework\View\Layout\Reader;
use Magento\Framework\View\Layout\Generator;
use Magento\Framework\View\Design;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\App\State as AppState;

class Layout extends \Magento\Framework\View\Layout
{
    /**
     * @var Toggle
     */
    protected $toggleHelper;

    /**
     * Layout constructor.
     * @param Toggle $toggleHelper
     * @param ProcessorFactory $processorFactory
     * @param ManagerInterface $eventManager
     * @param Data\Structure $structure
     * @param MessageManagerInterface $messageManager
     * @param Design\Theme\ResolverInterface $themeResolver
     * @param ReaderPool $readerPool
     * @param GeneratorPool $generatorPool
     * @param FrontendInterface $cache
     * @param Reader\ContextFactory $readerContextFactory
     * @param Generator\ContextFactory $generatorContextFactory
     * @param AppState $appState
     * @param Logger $logger
     * @param bool $cacheable
     */
    public function __construct(
        Toggle $toggleHelper,
        ProcessorFactory $processorFactory,
        ManagerInterface $eventManager,
        Data\Structure $structure,
        MessageManagerInterface $messageManager,
        Design\Theme\ResolverInterface $themeResolver,
        ReaderPool $readerPool,
        GeneratorPool $generatorPool,
        FrontendInterface $cache,
        Reader\ContextFactory $readerContextFactory,
        Generator\ContextFactory $generatorContextFactory,
        AppState $appState,
        Logger $logger,
        $cacheable = true
    ) {
        $this->toggleHelper = $toggleHelper;

        parent::__construct(
            $processorFactory,
            $eventManager,
            $structure,
            $messageManager,
            $themeResolver,
            $readerPool,
            $generatorPool,
            $cache,
            $readerContextFactory,
            $generatorContextFactory,
            $appState,
            $logger,
            $cacheable
        );
    }

    /**
     * Override modification: add the active toggles hash to the cache id
     */
    public function generateElements()
    {
        \Magento\Framework\Profiler::start(__CLASS__ . '::' . __METHOD__);

        $activeTogglesHash = $this->toggleHelper->getAllActiveTogglesHash();
        $cacheId = 'structure_' . $this->getUpdate()->getCacheId() . $activeTogglesHash;
        $result = $this->cache->load($cacheId);
        if ($result) {
            $this->readerContext = unserialize($result);
        } else {
            \Magento\Framework\Profiler::start('build_structure');
            $this->readerPool->interpret($this->getReaderContext(), $this->getNode());
            \Magento\Framework\Profiler::stop('build_structure');
            $this->cache->save(serialize($this->getReaderContext()), $cacheId, $this->getUpdate()->getHandles());
        }

        $generatorContext = $this->generatorContextFactory->create(
            [
                'structure' => $this->structure,
                'layout' => $this,
            ]
        );

        \Magento\Framework\Profiler::start('generate_elements');
        $this->generatorPool->process($this->getReaderContext(), $generatorContext);
        \Magento\Framework\Profiler::stop('generate_elements');

        $this->addToOutputRootContainers();
        \Magento\Framework\Profiler::stop(__CLASS__ . '::' . __METHOD__);
    }
}