<?php
/**
 * What3Words_What3Words
 *
 * @category    WorkInProgress
 * @copyright   Copyright (c) 2020 What3Words
 * @author      Vlad Patru <vlad@wearewip.com>
 * @link        http://www.what3words.com
 */
namespace What3Words\What3Words\Plugin;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;
use What3Words\What3Words\Helper\Config;

/**
 * Class SaveToQuote
 * Plugin used to add w3w attribute to quote address table
 */
class SaveToQuote
{

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;
    /**
     * @var Config
     */
    private $helperConfig;

    /**
     * SaveToQuote constructor.
     *
     * @param QuoteRepository $quoteRepository
     * @param Config $helperConfig
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        Config $helperConfig
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->helperConfig = $helperConfig;
    }

    /**
     * Before method
     *
     * @param ShippingInformationManagement $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return void
     * @throws NoSuchEntityException
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $api = $this->helperConfig->getApiKey();

        if ($this->helperConfig->getIsEnabled() === '1' && isset($api)) {
            if (!$extAttributes = $addressInformation->getExtensionAttributes()) {
                return;
            }

            $quote = $this->quoteRepository->getActive($cartId);
            $quote->setW3w($extAttributes->getW3w());
            $quote->setW3wCoordinates($extAttributes->getW3wCoordinates());
            $quote->setW3wNearest($extAttributes->getW3wNearest());
        }
    }
}
