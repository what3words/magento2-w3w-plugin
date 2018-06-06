<?php

namespace What3Words\What3Words\Model;

use Magento\Framework\Model\AbstractModel;
use What3Words\What3Words\Api\Data\QuoteInterface;

/**
 * Class Quote
 * @package What3Words\What3Words\Model
 * @author Vicki Tingle
 */
class Quote extends AbstractModel implements QuoteInterface
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('What3Words\What3Words\Model\ResourceModel\Quote');
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
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * @return string
     */
    public function getW3w()
    {
        return $this->getData(self::W3W);
    }

    /**
     * @return int
     */
    public function getAddressFlag()
    {
        return $this->getData(self::ADDRESS_FLAG);
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
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * @param string $w3w
     * @return $this
     */
    public function setW3w($w3w)
    {
        return $this->setData(self::W3W, $w3w);
    }

    /**
     * @param int $addressFlag
     * @return $this
     */
    public function setAddressFlag($addressFlag)
    {
        return $this->setData(self::ADDRESS_FLAG, $addressFlag);
    }
}
