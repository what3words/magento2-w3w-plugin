<?php

namespace What3Words\What3Words\Model\ResourceModel\Address;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package What3Words\What3Words\Model\ResourceModel\Address
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
        $this->_init('What3Words\What3Words\Model\Address', 'What3Words\What3Words\Model\ResourceModel\Address');
    }
}

