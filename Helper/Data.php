<?php

namespace What3Words\What3Words\Helper;

/** What3Words extension classes */
use What3Words\What3Words\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use What3Words\What3Words\Model\ResourceModel\Address\CollectionFactory as AddressCollectionFactory;
use What3Words\What3Words\Model\AddressRepository;
use What3Words\What3Words\Model\QuoteRepository;
use What3Words\What3Words\Model\OrderRepository;

/** Magento classes */
use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;

/**
 * Class Data
 * @package What3Words\What3Words\Helper
 * @author Vicki Tingle
 */
class Data extends AbstractHelper
{
    /** @var QuoteCollectionFactory  */
    protected $quoteCollectionFactory;

    /** @var AddressCollectionFactory  */
    protected $addressCollectionFactory;

    /** @var AddressRepository  */
    protected $addressRepository;

    /** @var QuoteRepository  */
    protected $quoteRepository;

    /** @var OrderRepository  */
    protected $orderRepository;

    /**
     * Data constructor.
     * @param Context $context
     * @param QuoteCollectionFactory $quoteCollectionFactory
     * @param AddressCollectionFactory $addressCollectionFactory
     * @param AddressRepository $addressRepository
     * @param QuoteRepository $quoteRepository
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        Context $context,
        QuoteCollectionFactory $quoteCollectionFactory,
        AddressCollectionFactory $addressCollectionFactory,
        AddressRepository $addressRepository,
        QuoteRepository $quoteRepository,
        OrderRepository $orderRepository
    )
    {
        parent::__construct($context);
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->addressCollectionFactory = $addressCollectionFactory;
        $this->addressRepository = $addressRepository;
        $this->quoteRepository = $quoteRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->_urlBuilder->getUrl('what3words/checkout/validation');
    }

    /**
     * @return string
     */
    public function getFetchInfoUrl()
    {
        return $this->_urlBuilder->getUrl('what3words/fetch/info');
    }

    /**
     * @param int $quoteId
     * @param string $what3words
     * @return array
     */
    public function shouldSaveQuote($quoteId, $what3words)
    {
        /** @var \What3Words\What3Words\Model\Quote $item */
        if ($item = $this->getW3wItemByQuoteId($quoteId)) {
            $savedWhat3words = $item->getW3w();
            if ($savedWhat3words == $what3words) {
                return array('save' => false);
            }
            return array('save' => true, 'replace' => true);
        }
        return array('save' => true, 'replace' => false);
    }

    /**
     * @param int $quoteId
     * @param string $what3words
     * @return bool
     */
    public function updateExistingQuote($quoteId, $what3words)
    {
        $items = $this->quoteRepository->getW3wByQuoteId($quoteId);
        if (count($items) > 0) {
            /** @var \What3Words\What3Words\Model\Quote $item */
            foreach ($items as $item) {
                try {
                    $item->setW3w($what3words);
                    $this->quoteRepository->save($item);
                    return true;
                } catch (\Exception $e) {
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * @param int $orderId
     * @return bool|\Magento\Framework\DataObject
     */
    public function getW3wItemByOrderId($orderId)
    {
        $orderItems = $this->orderRepository->getW3wByOrderId($orderId);
        if (count($orderItems) > 0) {
            /** @var \What3Words\What3Words\Model\Order $orderItem */
            foreach ($orderItems as $orderItem) {
                return $orderItem;
            }
        }
        return false;
    }

    /**
     * @param int $quoteId
     * @return bool|\Magento\Framework\DataObject
     */
    public function getW3wItemByQuoteId($quoteId)
    {
        $quoteItems = $this->quoteRepository->getW3wByQuoteId($quoteId);
        if (count($quoteItems) > 0) {
            /** @var \What3Words\What3Words\Model\Quote $quoteItem */
            foreach ($quoteItems as $quoteItem) {
                return $quoteItem;
            }
        }
        return false;
    }

    /**
     * @param int $addressId
     * @return bool| string
     */
    public function getW3wItemByAddressId($addressId)
    {
        if ($addressId) {
            if ($addressItems = $this->addressRepository->getW3wByAddressId($addressId)) {
                /** @var \What3Words\What3Words\Model\Address $addressItem */
                foreach ($addressItems as $addressItem) {
                    return $addressItem;
                }
            }
            return false;
        }
        return false;
    }
}
