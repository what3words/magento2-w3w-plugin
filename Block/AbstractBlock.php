<?php

namespace What3Words\What3words\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use What3Words\What3Words\Helper\Config;

/**
 * Class AbstractBlock
 * @package What3Words\What3words\Block
 * @author Vicki Tingle
 */
class AbstractBlock extends Template
{
    /** @var Config  */
    protected $config;

    /**
     * AbstractBlock constructor.
     * @param Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->config->getIsEnabled();
    }

    public function getFlags()
    {
        return $this->getViewFileUrl('What3Words_What3Words::');
    }
}
