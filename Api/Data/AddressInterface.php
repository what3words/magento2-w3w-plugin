<?php

namespace What3Words\What3Words\Api\Data;

/**
 * Interface AddressInterface
 * @package What3Words\What3Words\Api\Data
 */
interface AddressInterface
{
    const ENTITY_ID = 'entity_id';
    const ADDRESS_ID = 'address_id';
    const W3W = 'w3w';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @return int
     */
    public function getAddressId();

    /**
     * @return string
     */
    public function getW3w();

    /**
     * @param int $entityId
     * @return AddressInterface
     */
    public function setEntityId($entityId);

    /**
     * @param int $addressId
     * @return AddressInterface
     */
    public function setAddressId($addressId);

    /**
     * @param string $w3w
     * @return AddressInterface
     */
    public function setW3w($w3w);
}
