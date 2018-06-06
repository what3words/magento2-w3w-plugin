<?php

namespace What3Words\What3Words\Model;

use Magento\Framework\Model\AbstractModel;
use What3Words\What3Words\Api\Data\OrderInterface;

/**
 * Class Order
 * @package What3Words\What3Words\Model
 * @author Vicki Tingle
 */
class Order extends AbstractModel implements OrderInterface
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('What3Words\What3Words\Model\ResourceModel\Order');
    }

    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @return string
     */
    public function getW3w()
    {
        return $this->getData(self::W3W);
    }

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @param string $w3w
     * @return $this
     */
    public function setW3w($w3w)
    {
        return $this->setData(self::W3W, $w3w);
    }
}
