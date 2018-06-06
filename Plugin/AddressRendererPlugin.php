<?php

namespace What3Words\What3Words\Plugin;

use Magento\Customer\Block\Address\Renderer\DefaultRenderer;
use \Magento\Framework\View\Element\Context;
use What3Words\What3Words\Helper\Data;
use What3Words\What3Words\Helper\Config;
use \Magento\Framework\App\Request\Http;

/**
 * Class AddressRendererPlugin
 * @package What3Words\What3Words\Plugin
 * @author Vicki Tingle
 */
class AddressRendererPlugin
{
    /** @var int */
    protected $addressId;

    /** @var Data  */
    protected $helper;

    /** @var Config  */
    protected $config;

    /** @var Http  */
    protected $request;

    /**
     * AddressRendererPlugin constructor.
     * @param Context $context
     * @param Data $helper
     * @param Config $config
     * @param Http $request
     */
    public function __construct(
        Context $context,
        Data $helper,
        Config $config,
        Http $request
    ) {
        $this->context = $context;
        $this->helper = $helper;
        $this->config = $config;
        $this->request = $request;
    }

    /**
     * @param DefaultRenderer $subject
     * @param $result
     * @return array
     */
    public function beforeRenderArray(DefaultRenderer $subject, $result)
    {
        if ($this->config->getIsEnabled()) {
            if (isset($result['customer_address_id'])) {
                $this->addressId = $result['customer_address_id'];
            } elseif (isset($result['id'])) {
                $this->addressId = $result['id'];
            }
        }
    }

    /**
     * Append the 3 word address to the formatted
     * address string
     * @param DefaultRenderer $subject
     * @param $result
     * @return string
     */
    public function afterRenderArray(DefaultRenderer $subject, $result)
    {
        if ($this->config->getIsEnabled()) {
            if (!is_null($this->addressId)) {
                /** @var \What3Words\What3Words\Model\Address $addressItem */
                $addressItem = $this->helper->getW3wItemByAddressId($this->addressId);
                if ($addressItem && !empty($addressItem->getW3w())) {
                    $threeWordAddress = $addressItem->getW3w();
                    $result .= '<br>' . '/// ' . $threeWordAddress;

                }
            } elseif ($this->request->getParam('order_id')) {
                /** @var \What3Words\What3Words\Model\Order $orderItem */
                $orderItem = $this->helper->getW3wItemByOrderId($this->request->getParam('order_id'));
                if ($orderItem && !empty($orderItem->getW3w())) {
                    $threeWordAddress = $orderItem->getW3w();
                    $result .= '<br>' . '/// ' . $threeWordAddress;
                }
            }
        }
        return $result;
    }
}
