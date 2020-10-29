<?php
/**
 * What3Words_What3Words
 *
 * @category    WorkInProgress
 * @copyright   Copyright (c) 2020 What3Words
 * @author      Vlad Patru <vlad@wearewip.com>
 * @link        http://www.what3words.com
 */

namespace What3Words\What3Words\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use What3Words\What3Words\Helper\Config;

/**
 * Class LayoutProcessor
 * Used to add w3w custom attribute to checkout shipping address
 */
class LayoutProcessor implements LayoutProcessorInterface
{

    /**
     * @var Config
     */
    private $helperConfig;

    public function __construct(
        Config $helperConfig
    )
    {
        $this->helperConfig = $helperConfig;
    }

    /**
     * @param $result
     * @return mixed Checkout Layout with custom w3w attribute
     */
    public function getShippingFormFields($result)
    {
        if (isset($result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset'])
        ) {
            $customShippingFields = $this->getFields('shippingAddress.custom_attributes', 'shipping');

            $shippingFields = $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'];

            $shippingFields = array_replace_recursive($shippingFields, $customShippingFields);

            $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'] = $shippingFields;
        }

        return $result;
    }

    /**
     * @param array $result
     * @return array|mixed Update checkout layout dependent on admin
     * setting ( we either show the custom what3words attribute or simply remove it from layout )
     */
    public function process($result)
    {
        $api = $this->helperConfig->getApiKey();
        if ($this->helperConfig->getIsEnabled() === '1' && isset($api)) {
            $result = $this->getShippingFormFields($result);
        } else {
            unset($result['components']['checkout']['children']['steps']['children']
                ['shipping-step']['children']['shippingAddress']['children']
                ['shipping-address-fieldset']['children']['w3w']);
            unset($result['components']['checkout']['children']['steps']['children']
                ['shipping-step']['children']['shippingAddress']['children']
                ['shipping-address-fieldset']['children']['w3w_coordinates']);
            unset($result['components']['checkout']['children']['steps']['children']
                ['shipping-step']['children']['shippingAddress']['children']
                ['shipping-address-fieldset']['children']['w3w_nearest']);
        }

        return $result;
    }

    /**
     * Get defined fields
     * @param $scope
     * @param $addressType
     * @return array
     */
    public function getFields($scope, $addressType)
    {
        $fields = [];
        foreach ($this->getAdditionalFields($addressType) as $field) {
            if ($field === 'w3w') {
                $fields[$field] = $this->getField($field, $scope);
            }
        }

        return $fields;
    }

    /**
     * {@inheritDoc}
     */
    public function getField($attributeCode, $scope)
    {
        $description = 'By entering your 3 word address you make it much
            easier for our delivery partners to find you first time.
    To discover your 3 word address, visit <a href="https://what3words.com" target="_blank">what3words.com</a>';
        $field = [
            'component' => 'What3Words_What3Words/js/component/w3wAutosuggest',
            'config' => [
                'customScope' => $scope,
                'template' => 'What3Words_What3Words/form/field',
                'elementTmpl' => 'What3Words_What3Words/form/element/input',
                'placeholder' => $this->helperConfig->getPlaceholder(),
                'tooltipTpl' => 'What3Words_What3Words/form/element/helper/tooltip',
                'tooltip' => [
                    'description' => __($description),
                ],

            ],
            'dataScope' => $scope . '.' . $attributeCode,
            'additionalClasses' => 'w3w-checkout-field',
            'sortOrder' => '500',
            'visible' => true,
            'provider' => 'checkoutProvider',
            'validation' => [
                'validate-w3w' => true
            ],
            'options' => [],
            'label' => __('what3words address')
        ];

        return $field;
    }

    public function getHiddenField($attributeCode, $scope)
    {
        $field = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => $scope,
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
            ],
            'dataScope' => $scope . '.' . $attributeCode,
            'additionalClasses' => 'w3w-hidden-field',
            'sortOrder' => '510',
            'visible' => true,
            'provider' => 'checkoutProvider',
            'validation' => '',
            'options' => [],
            'label' => ''
        ];

        return $field;
    }

    /**
     * @param string $addressType
     * @return array
     */
    public function getAdditionalFields($addressType = 'shipping')
    {
        $shippingAttributes = [];
        $billingAttributes = [];
        $shippingAttributes[] = 'w3w';
        $shippingAttributes[] = 'w3w_coordinates';
        $shippingAttributes[] = 'w3w_nearest';

        return $addressType == 'shipping' ? $shippingAttributes : $billingAttributes;
    }
}
