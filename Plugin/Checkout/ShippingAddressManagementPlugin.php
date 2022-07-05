<?php
/**
 * What3Words_What3Words
 *
 * @category    WorkInProgress
 * @copyright   Copyright (c) 2020 What3Words
 * @author      Vlad Patru <vlad@wearewip.com>
 * @link        http://www.what3words.com
 */
namespace What3Words\What3Words\Plugin\Checkout;

use Magento\Quote\Model\ShippingAddressManagement;
use Psr\Log\LoggerInterface;
use Magento\Quote\Api\Data\AddressInterface;
use What3Words\What3Words\Helper\Config;

/**
 * Class ShippingAddressManagementPlugin
 * Used to set the custom attribute into the shipping address from checkout
 */
class ShippingAddressManagementPlugin
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Config
     */
    private $helperConfig;

    /**
     * ShippingAddressManagementPlugin constructor.
     * @param LoggerInterface $logger
     * @param Config $helperConfig
     */
    public function __construct(
        LoggerInterface $logger,
        Config $helperConfig
    ) {
        $this->logger = $logger;
        $this->helperConfig = $helperConfig;
    }

    /**
     * Before assign method
     *
     * @param ShippingAddressManagement $subject
     * @param $cartId
     * \Magento\Quote\Api\Data\AddressInterface $address
     * @param AddressInterface $address
     * @return array
     */
    public function beforeAssign(
        ShippingAddressManagement $subject,
        $cartId,
        AddressInterface $address
    ) {
        $api = $this->helperConfig->getApiKey();
        if ($this->helperConfig->getIsEnabled() === '1' && isset($api)) {
            $extAttributes = $address->getExtensionAttributes();
            if (!empty($extAttributes)) {
                try {
                    $address->setData('w3w', $address->getExtensionAttributes()->getW3w());
                    $address->setData('w3w_nearest', $address->getExtensionAttributes()->getW3wNearest());
                    $address->setData('w3w_coordinates', $address->getExtensionAttributes()->getW3wCoordinates());
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
        }
        return [$cartId, $address];
    }
}
