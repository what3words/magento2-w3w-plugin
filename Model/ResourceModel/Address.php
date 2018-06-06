<?php

namespace What3Words\What3Words\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Address
 * @package What3Words\What3Words\Model\ResourceModel
 * @author Vicki Tingle
 */
class Address extends AbstractDb
{
    /**
     * Initialize resource model
     * Get table name from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('w3w_customer_address', 'entity_id');
    }
}
