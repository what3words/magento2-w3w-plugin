<?php
/**
 * What3Words_What3Words
 *
 * @category    WorkInProgress
 * @copyright   Copyright (c) 2020 What3Words
 * @author      Vlad Patru <vlad@wearewip.com>
 * @link        http://www.what3words.com
 */

namespace What3Words\What3Words\ViewModel;

use Magento\Customer\Helper\Address;
use Magento\Directory\Helper\Data;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use What3Words\What3Words\Helper\Config;

/**
 * Class ViewModel
 * Used to return admin settings into templates without
 * injecting as the coding standards require
 */
class ViewModel implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $helper;

    /**
     * @var Address
     */
    private $addressHelper;

    /**
     * @var Data
     */
    private $dirHelper;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * ViewModel constructor.
     * @param Config $helper
     * @param Address $addressHelper
     * @param Data $dirHelper
     * @param Json $serializer
     * @param ResourceInterface $moduleResource
     */
    public function __construct(
        Config $helper,
        Address $addressHelper,
        Data $dirHelper,
        Json $serializer,
        ResourceInterface $moduleResource
    ) {
        $this->helper = $helper;
        $this->addressHelper = $addressHelper;
        $this->dirHelper = $dirHelper;
        $this->serializer = $serializer;
        $this->moduleResource = $moduleResource;
    }

    /**
     * @return string ApiKey set in admin config
     */
    public function getApiKey()
    {
        return $this->helper->getApiKey();
    }

    /**
     * @return string Color
     */
    public function getColor()
    {
        return $this->helper->getIconColor();
    }

    /**
     * @return string Color
     */
    public function getPlaceholder()
    {
        return $this->helper->getPlaceholder();
    }

    /**
     * @return Address
     */
    public function getAddressHelper()
    {
        return $this->addressHelper;
    }

    /**
     * @return Data
     */
    public function getDirHelper()
    {
        return $this->dirHelper;
    }

    /**
     * @return string Save Coordinates set in admin config
     */
    public function getSaveCoordinates()
    {
        return $this->helper->getCoordinates();
    }

    /**
     * @return string Save nearest place set in admin config
     */
    public function getSaveNearest()
    {
        return $this->helper->getNearest();
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'clipping' => 'clip-to-' . $this->helper->getClipping(),
            'save_coordinates' => $this->helper->getCoordinates(),
            'save_nearest' => $this->helper->getNearest(),
            'country_iso' => $this->helper->getCountryIso(),
            'circle_data' => $this->helper->getCircleCoords(),
            'box_data' => $this->helper->getBoxCoords(),
            'polygon_data' => $this->helper->getPolygonCoords(),
            'w3w_version' => $this->moduleResource->getDbVersion('What3Words_What3Words')
        ];
    }

    /**
     * Get serialized config
     *
     * @return string
     */
    public function getSerializedConfig()
    {
        return $this->serializer->serialize($this->getConfig());
    }
}
