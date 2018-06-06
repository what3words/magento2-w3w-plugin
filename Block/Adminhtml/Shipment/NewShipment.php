<?php

namespace What3Words\What3Words\Block\Adminhtml\Shipment;

/** What3Words extension classes */
use What3Words\What3Words\Model\OrderRepository;

/** Magento classes */
use Magento\Sales\Block\Adminhtml\Order\View\Info as OrderInfo;
use \Magento\Framework\Registry;
use \Magento\Backend\Block\Template\Context;
use Magento\Sales\Helper\Admin;
use \Magento\Customer\Api\GroupRepositoryInterface;
use \Magento\Customer\Api\CustomerMetadataInterface;
use \Magento\Customer\Model\Metadata\ElementFactory;
use \Magento\Sales\Model\Order\Address\Renderer;

/**
 * Class NewShipment
 * @package What3Words\What3Words\Block\Adminhtml\Shipment
 * @author Vicki Tingle
 */
class NewShipment extends OrderInfo
{
    /** @var OrderRepository  */
    protected $w3wOrderRepository;

    /**
     * NewShipment constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Admin $adminHelper
     * @param GroupRepositoryInterface $groupRepository
     * @param CustomerMetadataInterface $metadata
     * @param ElementFactory $elementFactory
     * @param Renderer $addressRenderer
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Admin $adminHelper,
        GroupRepositoryInterface $groupRepository,
        CustomerMetadataInterface $metadata,
        ElementFactory $elementFactory,
        Renderer $addressRenderer,
        OrderRepository $orderRepository
    ) {
        parent::__construct($context, $registry, $adminHelper, $groupRepository,
            $metadata, $elementFactory, $addressRenderer);
        $this->w3wOrderRepository = $orderRepository;
    }

    /**
     * Get the corresponding 3 word address for the order
     * @return bool | string
     */
    public function getW3w()
    {
        $orderId = $this->getOrder()->getEntityId();
        if ($orderId) {
            $items = $this->w3wOrderRepository->getW3wByOrderId($orderId);
            /** @var \What3Words\What3Words\Model\Order $item */
            foreach ($items as $item) {
                return $item->getW3w();
            }
        }
        return false;
    }
}
