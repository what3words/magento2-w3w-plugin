<?php

namespace What3Words\What3Words\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderLoadAfter implements ObserverInterface
{
    /**
     * Add custom attribute to Rest API /orders/
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        if($order->getShippingAddress()) {
            $attr = $order->getShippingAddress()->getData('w3w');
            $order->getShippingAddress()->getExtensionAttributes()->setData('w3w', $attr);
        }
    }
}
