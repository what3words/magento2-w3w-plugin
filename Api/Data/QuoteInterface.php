<?php

namespace What3Words\What3Words\Api\Data;

/**
 * Interface QuoteInterface
 * @package What3Words\What3Words\Api\Data
 */
interface QuoteInterface
{
    const ENTITY_ID = 'entity_id';
    const QUOTE_ID = 'quote_id';
    const W3W = 'w3w';
    const ADDRESS_FLAG = 'address_flag';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @return int
     */
    public function getQuoteId();

    /**
     * @return string
     */
    public function getW3w();

    /**
     * @return int
     */
    public function getAddressFlag();

    /**
     * @param int $entityId
     * @return mixed
     */
    public function setEntityId($entityId);

    /**
     * @param int $quoteId
     * @return QuoteInterface
     */
    public function setQuoteId($quoteId);

    /**
     * @param string $w3w
     * @return QuoteInterface
     */
    public function setW3w($w3w);

    /**
     * @param int $addressFlag
     * @return QuoteInterface
     */
    public function setAddressFlag($addressFlag);
}
