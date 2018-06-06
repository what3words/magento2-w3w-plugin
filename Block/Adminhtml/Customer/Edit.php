<?php

namespace What3Words\What3Words\Block\Adminhtml\Customer;

use What3Words\What3Words\Block\AbstractBlock;

/**
 * Class Edit
 * @package What3Words\What3Words\Block\Adminhtml\Customer
 * @author Vicki Tingle
 */
class Edit extends AbstractBlock
{
    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('what3words/customer/ajax');
    }
}
