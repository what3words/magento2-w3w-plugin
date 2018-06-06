<?php

namespace What3Words\What3Words\Block\Adminhtml\Sales\Address;

use Magento\Sales\Block\Adminhtml\Order\Address;
use What3Words\What3Words\Model\OrderRepository;
use \Magento\Backend\Block\Widget\Context;
use \Magento\Framework\Registry;

/**
 * Class Edit
 * @package What3Words\What3Words\Block\Adminhtml\Sales\Address
 * @author Vicki Tingle
 */
class Edit extends Address
{
    /** @var OrderRepository  */
    protected $orderRepository;

    /**
     * Edit constructor.
     * @param Context $context
     * @param Registry $registry
     * @param OrderRepository $orderRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        OrderRepository $orderRepository,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return bool|string
     */
    public function getW3wFromOrder()
    {
        $address = $this->_coreRegistry->registry('order_address');
        $orderId = $address->getOrder()->getEntityId();

        if ($orderItems = $this->orderRepository->getW3wByOrderId($orderId)) {
            /** @var \What3Words\What3Words\Model\Order $orderItem */
            foreach ($orderItems as $orderItem) {
                return $orderItem->getW3w();
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('what3words/address/save');
    }
}
