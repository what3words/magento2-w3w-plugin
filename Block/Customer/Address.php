<?php

namespace What3Words\What3Words\Block\Customer;

/** What3Words extension classes */
use What3Words\What3Words\Block\AbstractBlock;
use What3Words\What3Words\Helper\Data;
use What3Words\What3Words\Helper\Config;

/** Magento classes */
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Api\AddressRepositoryInterface;
use \Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\ResourceModel\CustomerRepository;

/**
 * Class Address
 * @package What3Words\What3Words\Block\Customer
 * @author Vicki Tingle
 */
class Address extends AbstractBlock
{
    /** @var Data  */
    protected $w3wHelper;

    /** @var Config  */
    protected $configHelper;

    /** @var AddressRepositoryInterface  */
    protected $addressRepository;

    /** @var AddressFactory  */
    protected $addressFactory;

    /** @var CustomerRepository  */
    protected $customerRepository;

    /**
     * Address constructor.
     * @param Context $context
     * @param Data $w3wHelper
     * @param Config $configHelper
     * @param AddressRepositoryInterface $addressRepository
     * @param AddressFactory $addressFactory
     * @param CustomerRepository $customerRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $w3wHelper,
        Config $configHelper,
        AddressRepositoryInterface $addressRepository,
        AddressFactory $addressFactory,
        CustomerRepository $customerRepository,
        array $data = []
    ) {
        parent::__construct($context, $configHelper, $data);
        $this->w3wHelper = $w3wHelper;
        $this->configHelper = $configHelper;
        $this->addressRepository = $addressRepository;
        $this->addressFactory = $addressFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get the 3 word address for this address
     * @return bool|string
     */
    public function get3WordAddress()
    {
       $addressId = $this->getRequest()->getParam('id');
       /** @var \What3Words\What3Words\Model\Address $addressItem */
       if ($addressItem = $this->w3wHelper->getW3wItemByAddressId($addressId)) {
           return $addressItem->getW3w();
       }
       return false;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->configHelper->getApiKey();
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->configHelper->getPlaceHolder();
    }

    /**
     * @return string
     */
    public function getTypeaheadDelay()
    {
        return $this->configHelper->getTypeaheadDelay();
    }

    /**
     * @return string
     */
    public function getAllowedCountries()
    {
        $countries = explode(',', $this->configHelper->getAllowedCountries());
        return json_encode($countries);
    }

    /**
     * @return string
     */
    public function getValidateUrl()
    {
        return $this->w3wHelper->getValidationUrl();
    }
}
