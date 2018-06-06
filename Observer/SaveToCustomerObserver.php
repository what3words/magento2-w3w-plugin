<?php

namespace What3Words\What3Words\Observer;

/** What3Words Extension classes */
use What3Words\What3Words\Helper\Data;
use What3Words\What3Words\Model\AddressFactory;
use What3Words\What3Words\Model\AddressRepository;
use What3Words\What3Words\Helper\Config;
use What3Words\What3Words\Model\AttributeHandler;

/** Magento classes */
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\App\RequestInterface;
use \Magento\Customer\Api\AddressRepositoryInterface;
use \Magento\Customer\Api\Data\AddressExtensionInterface;

/**
 * Class SaveToCustomerObserver
 * @package What3Words\What3Words\Observer
 * @author Vicki Tingle
 */
class SaveToCustomerObserver implements ObserverInterface
{
    /** @var Data  */
    protected $w3wHelper;

    /** @var AddressFactory  */
    protected $addressFactory;

    /** @var RequestInterface  */
    protected $request;

    /** @var AddressRepositoryInterface  */
    protected $addressRepository;

    /** @var AddressRepository  */
    protected $w3wAddressRepository;

    /** @var Config  */
    protected $config;

    /** @var AttributeHandler  */
    protected $attributeHandler;

    /**
     * SaveToCustomerObserver constructor.
     * @param Data $w3wHelper
     * @param AddressFactory $addressFactory
     * @param RequestInterface $request
     * @param AddressRepositoryInterface $addressRepository
     * @param AddressRepository $w3wAddressRepository
     * @param Config $config
     * @param AttributeHandler $attributeHandler
     */
    public function __construct(
        Data $w3wHelper,
        AddressFactory $addressFactory,
        RequestInterface $request,
        AddressRepositoryInterface $addressRepository,
        AddressRepository $w3wAddressRepository,
        Config $config,
        AttributeHandler $attributeHandler
    ) {
        $this->w3wHelper = $w3wHelper;
        $this->addressFactory = $addressFactory;
        $this->request = $request;
        $this->addressRepository = $addressRepository;
        $this->w3wAddressRepository = $w3wAddressRepository;
        $this->config = $config;
        $this->attributeHandler = $attributeHandler;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if ($this->config->getIsEnabled()) {
            /** @var \Magento\Customer\Model\Address $address */
            $addressId = $observer->getCustomerAddress()->getId();
            $params = $this->request->getParams();

            // The parameter will vary depending on if we are in the admin or not
            $what3words = null;
            if (isset($params['what3words'])) {
                $what3words = $params['what3words'];
            } elseif (isset($params['customer']['w3w'])) {
                $what3words = $params['customer']['w3w'];
            }

            if (!is_null($what3words)) {
                $this->attributeHandler->saveAttribute($addressId, $what3words);
            }
        }
        return $this;
    }
}
