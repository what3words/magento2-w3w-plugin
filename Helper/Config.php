<?php
/**
 * What3Words_What3Words
 *
 * @category    WorkInProgress
 * @copyright   Copyright (c) 2020 What3Words
 * @author      Vlad Patru <vlad@wearewip.com>
 * @link        http://www.what3words.com
 */
namespace What3Words\What3Words\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Config
 * Helper to return admin settings values
 */
class Config extends AbstractHelper
{
    const PREFIX = 'what3words/';

    /**
     * Config constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * @param $area
     * @return mixed
     */
    public function getConfig($area)
    {
        return $this->scopeConfig->getValue(self::PREFIX . $area);
    }

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->getConfig('general/enabled');
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getConfig('general/api_key');
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->getConfig('frontend/placeholder');
    }

    /**
     * @return string
     */
    public function getIconColor()
    {
        return $this->getConfig('frontend/icon_color');
    }

    /**
     * @return string
     */
    public function getCoordinates()
    {
        return $this->getConfig('general/save_coordinates');
    }

    /**
     * @return string
     */
    public function getNearest()
    {
        return $this->getConfig('general/save_nearest');
    }

    /**
     * @return string
     */
    public function getClipping()
    {
        return $this->getConfig('general/clipping');
    }

    /**
     * @return string
     */
    public function getCountryIso()
    {
        return $this->getConfig('general/country');
    }

    /**
     * @return string
     */
    public function getCircleCoords()
    {
        return $this->getConfig('general/circle') . ',' . $this->getConfig('general/circle_radius');
    }

    /**
     * @return string
     */
    public function getBoxCoords()
    {
        return $this->getConfig('general/bounding_box_sw') . ',' . $this->getConfig('general/bounding_box_ne');
    }

    /**
     * @return string
     */
    public function getPolygonCoords()
    {
        return $this->getConfig('general/polygon');
    }
}
