<?php

namespace What3Words\What3Words\Api\Data;

/**
 * Interface OrderInterface
 * @package What3Words\What3Words\Api\Data
 */
interface OrderInterface
{
    const ENTITY_ID = 'entity_id';
    const ORDER_ID = 'order_id';
    const W3W = 'w3w';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @return string
     */
    public function getW3w();

    /**
     * @param int $entityId
     * @return OrderInterface
     */
    public function setEntityId($entityId);

    /**
     * @param int $orderId
     * @return OrderInterface
     */
    public function setOrderId($orderId);

    /**
     * @param $w3w
     * @return OrderInterface
     */
    public function setW3w($w3w);
}
