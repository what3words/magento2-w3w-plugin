<?php

namespace What3Words\What3Words\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\App\RequestInterface;
use What3Words\What3Words\Model\OrderRepository;

/**
 * Class SaveOrderAddress
 * @package What3Words\What3Words\Observer
 * @author Vicki Tingle
 */
class SaveOrderAddress implements ObserverInterface
{
    /** @var RequestInterface  */
    protected $request;

    /** @var OrderRepository  */
    protected $orderRepository;

    /**
     * SaveOrderAddress constructor.
     * @param RequestInterface $request
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        RequestInterface $request,
        OrderRepository $orderRepository
    ) {
        $this->request = $request;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Allow an admin user to update an order's three word address
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $params = $this->request->getParams();
        $orderId = $observer->getOrderId();

        if (isset($params['w3w']) && $params['w3w'] !== '') {
            if ($orderItems = $this->orderRepository->getW3wByOrderId($orderId)) {
                /** @var \What3Words\What3Words\Model\Order $orderItem */
                foreach ($orderItems as $orderItem) {
                    $orderItem->setW3w($params['w3w']);
                    $this->orderRepository->save($orderItem);
                }
            }
        }
        return $this;
    }
}
