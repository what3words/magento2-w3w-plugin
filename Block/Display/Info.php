<?php

namespace What3Words\What3Words\Block\Display;

use What3Words\What3Words\Block\AbstractBlock;
use What3Words\What3Words\Helper\Config;
use What3Words\What3Words\Model\OrderRepository;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Info
 * @package What3Words\What3Words\Block\Display
 * @author Vicki Tingle
 */
class Info extends AbstractBlock
{

    /** @var OrderRepository  */
    protected $orderRepository;

    /**
     * Info constructor.
     * @param OrderRepository $orderRepository
     * @param Context $context
     * @param Config $config
     */
    public function __construct(
        OrderRepository $orderRepository,
        Context $context,
        Config $config
    )
    {
        parent::__construct($context, $config);
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return string | bool
     */
    public function getW3w()
    {
        $request = $this->getRequest()->getParams();
        if (isset($request['order_id'])) {
            $items = $this->orderRepository->getW3wByOrderId($request['order_id']);
            /** @var \What3Words\What3Words\Model\Order $item */
            foreach ($items as $item) {
                return $item->getW3w();
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getFetchInfoUrl()
    {
        return $this->getUrl('what3words/fetch/info');
    }
}
