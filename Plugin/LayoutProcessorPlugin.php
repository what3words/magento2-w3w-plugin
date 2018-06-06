<?php

namespace What3Words\What3Words\Plugin;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use What3Words\What3Words\Helper\Data;
use What3Words\What3Words\Helper\Config;

/**
 * Class LayoutProcessorPlugin
 * @package What3Words\What3Words\Plugin
 * @author Vicki Tingle
 */
class LayoutProcessorPlugin
{
    /** @var Data  */
    protected $helper;

    /** @var Config  */
    protected $config;

    /**
     * LayoutProcessorPlugin constructor.
     * @param Data $helper
     * @param Config $config
     */
    public function __construct(
        Data $helper,
        Config $config
    ) {
        $this->helper = $helper;
        $this->config = $config;
    }

    /**
     * Add the what3words input to the shipping form with validation
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(LayoutProcessor $subject, array $jsLayout)
    {
        if ($this->config->getIsEnabled()) {
            $jsLayout['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']
            ['children']['three_word_address'] = [
                'component' => 'What3Words_What3Words/js/view/checkout/shipping',
                'config' => [
                    // customScope is used to group elements within a single form (e.g. they can be validated separately)
                    'customScope' => 'shippingAddress.custom_attributes',
                    'customEntry' => null,
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'What3Words_What3Words/checkout/shipping',
                ],
                'dataScope' => 'what3words',
                'label' => '3 word address',
                'provider' => 'checkoutProvider',
                'sortOrder' => 200,
                'options' => [],
                'filterBy' => null,
                'customEntry' => null,
                'visible' => true,
                'validateUrl' => $this->helper->getValidationUrl(),
                'fetchInfoUrl' => $this->helper->getFetchInfoUrl(),
                'api_key' => $this->config->getApiKey(),
                'allowed_countries' => $this->getAllowedCountries(),
                'typeahead_delay' => $this->config->getTypeaheadDelay(),
                'placeholder' => $this->config->getPlaceHolder()
            ];
            return $jsLayout;
        }
    }

    /**
     * Get allowed countries from config.
     * @return string
     */
    public function getAllowedCountries()
    {
        $countries = explode(',', $this->config->getAllowedCountries());
        return json_encode($countries);
    }
}
