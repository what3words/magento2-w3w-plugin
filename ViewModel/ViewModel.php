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

use Magento\Framework\View\Element\Block\ArgumentInterface;
use What3Words\What3Words\Helper\Config;
use Magento\Customer\Helper\Address;
use Magento\Directory\Helper\Data;

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
     * ViewModel constructor.
     * @param Config $helper
     * @param Address $addressHelper
     * @param Data $dirHelper
     */
    public function __construct(
        Config $helper,
        Address $addressHelper,
        Data $dirHelper
    ) {
        $this->helper = $helper;
        $this->addressHelper = $addressHelper;
        $this->dirHelper = $dirHelper;
    }

    /**
     * @return string ApiKey set in admin config
     */
    public function getApiKey()
    {
        return base64_encode($this->helper->getApiKey());
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
}
