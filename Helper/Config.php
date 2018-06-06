<?php

namespace What3Words\What3Words\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;
use \Magento\Framework\Encryption\EncryptorInterface;

/**
 * Class Config
 * @package What3Words\What3Words\Helper
 * @author Vicki Tingle
 */
class Config extends AbstractHelper
{
    const PREFIX = 'what3words/';

    /** @var EncryptorInterface  */
    protected $encryptor;

    /**
     * Config constructor.
     * @param Context $context
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor
    ) {
        parent::__construct($context);
        $this->encryptor = $encryptor;
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
        $key = $this->getConfig('general/api_key');
        return $this->encryptor->decrypt($key);
    }

    /**
     * @return string
     */
    public function getAllowedCountries()
    {
        return $this->getConfig('general/allowed_countries');
    }

    /**
     * @return string
     */
    public function getPlaceHolder()
    {
        return $this->getConfig('frontend/placeholder');
    }

    /**
     * @return string
     */
    public function getTypeaheadDelay()
    {
        return $this->getConfig('frontend/delay');
    }
}
