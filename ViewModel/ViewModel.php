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
 * Class to expose config to frontend
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
     *
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
     * ApiKey set in admin config
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->helper->getApiKey();
    }


    /**
     * Get placeholder
     *
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->helper->getPlaceholder();
    }

    /**
     * Get address helper
     *
     * @return Address
     */
    public function getAddressHelper()
    {
        return $this->addressHelper;
    }

    /**
     * Get Directory helper
     *
     * @return Data
     */
    public function getDirHelper()
    {
        return $this->dirHelper;
    }

    /**
     * Get Coordinates set in admin config
     *
     * @return string
     */
    public function getSaveCoordinates()
    {
        return $this->helper->getCoordinates();
    }

    /**
     * Get nearest place set in admin config
     *
     * @return string
     */
    public function getSaveNearest()
    {
        return $this->helper->getNearest();
    }

    /**
     * Return config array
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'api_key' => $this->helper->getApiKey(),
            'clipping' => 'clip-to-' . $this->helper->getClipping(),
            'save_coordinates' => $this->helper->getCoordinates(),
            'save_nearest' => $this->helper->getNearest(),
            'country_iso' => $this->helper->getCountryIso(),
            'circle_data' => $this->helper->getCircleCoords(),
            'box_data' => $this->helper->getBoxCoords(),
            'polygon_data' => $this->helper->getPolygonCoords(),
            'w3w_version' => $this->moduleResource->getDbVersion('What3Words_What3Words'),
            'show_tooltip' => $this->helper->getShowTooltip(),
            'override_label' => $this->helper->getOverrideLabel(),
            'custom_label' => $this->helper->getCustomLabel(),
            'magento_version' => $this->helper->getMagentoVersion(),
            'autosuggest_focus' => $this->helper->getAutosuggestFocus(),
            'invalid_error_message' => $this->helper->getInvalidErrorMessage(),
            'tooltip_text' => $this->helper->getTooltipText(),
            'rtl' => $this->helper->getRtlDirection(),
            'lang' => $this->helper->getAutocompleteLang(),
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

    /**
     * Get show_tooltip option saved in admin
     *
     * @return bool
     */
    public function getShowTooltip()
    {
        return $this->helper->getShowTooltip();
    }

    /**
     * Get override_label option saved in admin
     *
     * @return bool
     */
    public function getOverrideLabel()
    {
        return $this->helper->getOverrideLabel();
    }

    /**
     * Get field_label option saved in admin
     *
     * @return string
     */
    public function getCustomLabel()
    {
        return $this->helper->getCustomLabel();
    }

    /**
     * Get magento version
     *
     * @return string
     */
    public function getMagentoVersion()
    {
        return $this->helper->getMagentoVersion();
    }

    /**
     * Get autosuggest_focus option saved in admin
     *
     * @return bool
     */
    public function getAutosuggestFocus()
    {
        return $this->helper->getAutosuggestFocus();
    }

    /**
     * Return the Invalid error message
     *
     * @return string|null
     */
    public function getInvalidMessage()
    {
        return $this->helper->getInvalidErrorMessage();
    }

    /**
     * Return RTL option if set
     *
     * @return bool
     */
    public function getRtlDir()
    {
        return $this->helper->getRtlDirection();
    }

    /**
     * Return Autocomplete Lang if set
     *
     * @return mixed|null
     */
    public function getLang()
    {
        return $this->helper->getAutocompleteLang();
    }

    /**
     * Get Tooltip Text
     *
     * @return mixed|null
     */
    public function getTooltipText()
    {
        return $this->helper->getTooltipText();
    }
}
