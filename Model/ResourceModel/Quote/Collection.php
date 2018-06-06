<?php

namespace What3Words\What3Words\Model\ResourceModel\Quote;

/** What3Words Extension classes */
use What3Words\What3Words\Model\Quote;

/** Magento classes */
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package What3Words\What3Words\Model\ResourceModel\Quote
 * @author Vicki Tingle
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct() //@codingStandards ignoreLine
    {
        $this->_init('What3Words\What3Words\Model\Quote', 'What3Words\What3Words\Model\ResourceModel\Quote');
    }

    /**
     * @param $quoteId
     * @return $this
     */
    public function setQuoteIdFilter($quoteId)
    {
        $this->addFieldToFilter('main_table.' . Quote::QUOTE_ID, $quoteId);
        return $this;
    }
}

