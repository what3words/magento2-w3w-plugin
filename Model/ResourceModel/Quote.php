<?php

namespace What3Words\What3Words\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Quote
 * @package What3Words\What3Words\Model\ResourceModel
 * @author Vicki Tingle
 */
class Quote extends AbstractDb
{
    /**
     * Initialize resource model
     * Get table name from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('w3w_sales_quote', 'entity_id');
    }
}
