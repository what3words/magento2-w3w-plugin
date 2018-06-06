<?php

namespace What3Words\What3Words\Observer;

/** What3Words extension classes */
use What3Words\What3Words\Model\OrderFactory;
use What3Words\What3Words\Model\AddressFactory;
use What3Words\What3Words\Helper\Data;
use What3Words\What3Words\Model\OrderRepository;
use What3Words\What3Words\Model\AddressRepository;
use What3Words\What3Words\Helper\Config;
use What3Words\What3Words\Model\AttributeHandler;

/** Magento classes */
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory;

/**
 * Class SaveToOrderTableObserver
 * @package What3Words\What3Words\Observer
 * @author Vicki Tingle
 */
class SaveToOrderTableObserver implements ObserverInterface
{
    /** @var OrderFactory  */
    protected $w3wOrderFactory;

    /** @var AddressFactory  */
    protected $addressFactory;

    /** @var Data  */
    protected $w3wHelper;

    /** @var CollectionFactory  */
    protected $addressCollectionFactory;

    /** @var OrderRepository  */
    protected $orderRepository;

    /** @var AddressRepository  */
    protected $w3wAddressRepository;

    /** @var Config  */
    protected $config;

    /** @var AttributeHandler  */
    protected $attributeHandler;

    /**
     * SaveToOrderTableObserver constructor.
     * @param OrderFactory $w3wOrderFactory
     * @param AddressFactory $addressFactory
     * @param Data $w3wHelper
     * @param CollectionFactory $addressCollectionFactory
     * @param OrderRepository $orderRepository
     * @param AddressRepository $addressRepository
     * @param Config $config
     * @param AttributeHandler $attributeHandler
     */
    public function __construct(
        OrderFactory $w3wOrderFactory,
        AddressFactory $addressFactory,
        Data $w3wHelper,
        CollectionFactory $addressCollectionFactory,
        OrderRepository $orderRepository,
        AddressRepository $addressRepository,
        Config $config,
        AttributeHandler $attributeHandler
    ) {
        $this->w3wOrderFactory = $w3wOrderFactory;
        $this->addressFactory = $addressFactory;
        $this->w3wHelper = $w3wHelper;
        $this->addressCollectionFactory = $addressCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->w3wAddressRepository = $addressRepository;
        $this->config = $config;
        $this->attributeHandler = $attributeHandler;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if ($this->config->getIsEnabled()) {
            /** @var $order \Magento\Sales\Model\Order */
            $order = $observer->getEvent()->getOrder();
            $addressItem = $this->getOrderAddressItem($order->getEntityId());
            // Get the 3 word address from the quote
            /** @var \What3Words\What3Words\Model\Quote $w3wQuoteItem */
            if ($w3wQuoteItem = $this->w3wHelper->getW3wItemByQuoteId($order->getQuoteId())) {
                // Create a record for the order
                $w3wOrderItem = $this->w3wOrderFactory->create()
                    ->setW3w($w3wQuoteItem->getW3w())
                    ->setOrderId($order->getEntityId());

                $this->orderRepository->save($w3wOrderItem);

                // If we should save the customer's 3 word address, create a record for it
                if ($w3wQuoteItem->getAddressFlag() && !is_null($addressItem->getData('customer_address_id'))) {
                    $this->attributeHandler->saveAttribute(
                        $addressItem->getData('customer_address_id'),
                        $w3wQuoteItem->getW3w()
                    );
                }
            }
        }
        return $this;
    }

    /**
     * Return the customer address item to get it's customer address ID
     * @param int $orderId
     * @return \Magento\Framework\DataObject
     */
    public function getOrderAddressItem($orderId)
    {
        return $this->addressCollectionFactory->create()
            ->addFieldToFilter('parent_id', $orderId)
            ->addFieldToFilter('address_type', 'shipping')
            ->getFirstItem();
    }
}
