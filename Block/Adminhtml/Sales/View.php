<?php

namespace What3Words\What3Words\Block\Adminhtml\Sales;

/** What3Words extension classes */
use What3Words\What3Words\Model\OrderRepository;
use What3Words\What3Words\Helper\Config as What3WordsConfig;

/** Magento classes */
use Magento\Sales\Block\Adminhtml\Order\View as OrderView;
use \Magento\Backend\Block\Widget\Context;
use \Magento\Framework\Registry;
use \Magento\Sales\Model\Config;
use \Magento\Sales\Helper\Reorder;

/**
 * Class View
 * @package What3Words\What3Words\Block\Adminhtml\Sales
 * @author Vicki Tingle
 */
class View extends OrderView
{
    /** @var OrderRepository  */
    protected $w3wOrderRepository;

    /** @var What3WordsConfig  */
    protected $config;

    /**
     * View constructor.
     * @param OrderRepository $orderRepository
     * @param Context $context
     * @param Registry $registry
     * @param Config $salesConfig
     * @param Reorder $reorderHelper
     * @param What3WordsConfig $config
     */
    public function __construct(
        OrderRepository $orderRepository,
        Context $context,
        Registry $registry,
        Config $salesConfig,
        Reorder $reorderHelper,
        What3WordsConfig $config

    ) {
        parent::__construct($context, $registry, $salesConfig, $reorderHelper);
        $this->w3wOrderRepository = $orderRepository;
        $this->config = $config;
    }

    /**
     * Get the corresponding 3 word address for this order
     * @return bool | string
     */
    public function getW3w()
    {
        $orderId = $this->getOrderId();
        $items = $this->w3wOrderRepository->getW3wByOrderId($orderId);

        /** @var \What3Words\What3Words\Model\Order $item */
        foreach ($items as $item) {
            if ($item->getW3w() && $item->getW3w() !== '') {
                return $item->getW3w();
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->config->getIsEnabled();
    }
}
