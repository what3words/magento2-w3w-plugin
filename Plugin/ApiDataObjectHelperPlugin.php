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

use Magento\Quote\Api\Data\AddressExtensionInterface;
use What3Words\What3Words\Helper\Config;

/**
 * Class ApiDataObjectHelperPlugin
 * Plugin to add extension attributes to needed Api Interfaces
 */
class ApiDataObjectHelperPlugin
{

    /**
     * @var Config
     */
    private $helperConfig;

    /**
     * ApiDataObjectHelperPlugin constructor.
     * @param Config $helperConfig
     */
    public function __construct(
        Config $helperConfig
    ) {
        $this->helperConfig = $helperConfig;
    }

    /**
     * @param $helper
     * @param $dataObject
     * @param array $data
     * @param $interfaceName
     * @return array
     */
    public function beforePopulateWithArray($helper, $dataObject, array $data, $interfaceName)
    {
        $api = $this->helperConfig->getApiKey();
        if ($this->helperConfig->getIsEnabled() === '1' && isset($api)) {
            switch ($interfaceName) {
                case 'Magento\Sales\Api\Data\OrderAddressInterface':
                case 'Magento\Customer\Api\Data\AddressInterface':
                    if (isset($data['extension_attributes']) &&
                        ($data['extension_attributes'] instanceof AddressExtensionInterface)) {
                        $data['extension_attributes'] = $data['extension_attributes']->__toArray();
                        if (isset($data['extension_attributes']['w3w'])) {
                            $data['w3w'] = '///' . $data['extension_attributes']['w3w'];
                        }
                    }
                    break;
                case 'Magento\Quote\Api\Data\TotalsInterface':
                    unset($data['extension_attributes']);
                    break;
            }
            return [$dataObject, $data, $interfaceName];
        }
    }
}
