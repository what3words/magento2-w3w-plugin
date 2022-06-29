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

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Model\OrderRepository;
use Psr\Log\LoggerInterface;
use What3Words\What3Words\Helper\Config;

/**
 * Class SaveOrderAddress
 * Used to update w3w Attribute value when edited from Admin area
 */
class SaveOrderAddress implements ObserverInterface
{
    /** @var RequestInterface */
    protected $request;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var CartRepositoryInterface
     */
    public $quoteRepository;

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var Config
     */
    private $helperConfig;

    /**
     * SaveOrderAddress constructor.
     * @param RequestInterface $request
     * @param OrderRepository $orderRepository
     * @param CartRepositoryInterface $quoteRepository
     * @param Config $helperConfig
     */
    public function __construct(
        RequestInterface $request,
        OrderRepository $orderRepository,
        CartRepositoryInterface $quoteRepository,
        Config $helperConfig
    ) {
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
        $this->helperConfig = $helperConfig;
    }

    /**
     * Allow an admin user to update an order's three word address
     *
     * @param EventObserver $observer
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    public function execute(EventObserver $observer)
    {
        $api = $this->helperConfig->getApiKey();
        if ($this->helperConfig->getIsEnabled() === '1' && isset($api)) {
            $params = $this->request->getParams();
            $order = '';
            $quoteId = '';
            try {
                $orderId = $observer->getOrderId();
                $order = $this->orderRepository->get($orderId);
            } catch (NoSuchEntityException $exception) {
                $this->logger->error($exception->getMessage());
            }
            try {
                $quoteId = $order->getQuoteId();
                $quoteObj = $this->quoteRepository->get($quoteId);
            } catch (NoSuchEntityException $exception) {
                $this->logger->error($exception->getMessage());
            }

            if (isset($params['w3w']) && $params['w3w'] !== '' && $quoteObj) {
                $quoteAddress = $quoteObj->getShippingAddress()->setData('w3w', $params['w3w']);
                $quote = $quoteObj->setShippingAddress($quoteAddress);
                $this->quoteRepository->save($quote);
            }

            if (isset($params['w3w_nearest']) && $quoteObj) {
                $quoteAddress = $quoteObj->getShippingAddress()->setData('w3w_nearest', $params['w3w_nearest']);
                $quote = $quoteObj->setShippingAddress($quoteAddress);
                $this->quoteRepository->save($quote);
            }

            if (isset($params['w3w_coordinates']) && $quoteObj) {
                $quoteAddress = $quoteObj->getShippingAddress()->setData('w3w_coordinates', $params['w3w_coordinates']);
                $quote = $quoteObj->setShippingAddress($quoteAddress);
                $this->quoteRepository->save($quote);
            }

            return $this;
        }
    }
}
