<?php
/**
 * What3Words_What3Words
 *
 * @category    WorkInProgress
 * @copyright   Copyright (c) 2020 What3Words
 * @author      Vlad Patru <vlad@wearewip.com>
 * @link        http://www.what3words.com
 */
namespace What3Words\What3Words\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderRepository;
use Psr\Log\LoggerInterface;
use What3Words\What3Words\Helper\Config;

/**
 * Class SaveInOrder
 * Save 'w3w' attribute value to order address after order is placed
 */
class SaveInOrder implements ObserverInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;
    /**
     * @var Config
     */
    private $helperConfig;

    /**
     * Construct method
     *
     * @param OrderRepository $orderRepository
     * @param CartRepositoryInterface $quoteRepository
     * @param LoggerInterface $logger
     * @param Config $helperConfig
     */
    public function __construct(
        OrderRepository $orderRepository,
        CartRepositoryInterface $quoteRepository,
        LoggerInterface $logger,
        Config $helperConfig
    ) {
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->helperConfig = $helperConfig;
    }

    /**
     * Observer to save custom attribute to order
     *
     * @param Observer $observer
     * @return $this|void
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $api = $this->helperConfig->getApiKey();
        $saveCoordinates = $this->helperConfig->getCoordinates();
        $saveNearestPlace = $this->helperConfig->getNearest();
        if ($this->helperConfig->getIsEnabled() === '1' && isset($api)) {
            /** @var Quote $quote */
            $quote = $observer->getEvent()->getQuote();
            $order = $observer->getEvent()->getOrder();

            if (!$quote->getId() || !$order->getId()) {
                return $this;
            }

            $quoteObj = $this->quoteRepository->get($quote->getId());
            $orderObj = $this->orderRepository->get($order->getId());
            $quoteW3w = $quoteObj->getShippingAddress()->getData('w3w');
            $quoteW3wCoords = $quoteObj->getShippingAddress()->getData('w3w_coordinates');
            $quoteW3wNearest = $quoteObj->getShippingAddress()->getData('w3w_nearest');

            try {
                if (!$quote->isVirtual()) {
                    $orderW3w = $orderObj->getShippingAddress()->setData('w3w', $quoteW3w);
                    if ($saveCoordinates === '1') {
                        $orderW3w = $orderObj->getShippingAddress()->setData('w3w_coordinates', $quoteW3wCoords);
                    }

                    if ($saveNearestPlace === '1') {
                        $orderW3w = $orderObj->getShippingAddress()->setData('w3w_nearest', $quoteW3wNearest);
                    }
                    $orderAddress = $orderObj->setShippingAddress($orderW3w);
                    $this->orderRepository->save($orderAddress);
                }
            } catch (\Exception $e) {
                throw new LocalizedException(__('Something went wrong while saving custom fields to address.'));
            }
        }
    }
}
