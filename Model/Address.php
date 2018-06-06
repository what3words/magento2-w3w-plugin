<?php

namespace What3Words\What3Words\Model;

use Magento\Framework\Model\AbstractModel;
use What3Words\What3Words\Api\Data\AddressInterface;

/**
 * Class Address
 * @package What3Words\What3Words\Model
 * @author Vicki Tingle
 */
class Address extends AbstractModel implements AddressInterface
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct() // @codingStandardsIgnoreLine
    {
        $this->_init('What3Words\What3Words\Model\ResourceModel\Address');
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
    public function getAddressId()
    {
        return $this->getData(self::ADDRESS_ID);
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
     * @param int $addressId
     * @return $this
     */
    public function setAddressId($addressId)
    {
        return $this->setData(self::ADDRESS_ID, $addressId);
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
