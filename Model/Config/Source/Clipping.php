<?php
/**
 * What3Words_What3Words
 *
 * @category    WorkInProgress
 * @copyright   Copyright (c) 2020 What3Words
 * @author      Vlad Patru <vlad@wearewip.com>
 * @link        http://www.what3words.com
 */

namespace What3Words\What3Words\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Clipping implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            '' => 'Select clipping',
            'country' => 'To country',
            'circle' => 'To circle',
            'bounding-box' => 'To bounding box',
            'polygon' => 'To polygon',
        ];
    }
}
