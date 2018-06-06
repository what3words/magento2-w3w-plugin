<?php

namespace What3Words\What3Words\Plugin;

use Magento\Customer\Model\ResourceModel\AddressRepository;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use What3Words\What3Words\Model\AttributeHandler;
use What3Words\What3Words\Helper\Config;
use Magento\Framework\App\Request\Http;

/**
 * Class SaveToVarchar
 * @package What3Words\What3Words\Plugin
 * @author Vicki Tingle
 */
class SaveToVarchar
{
    /** @var ResourceConnection  */
    protected $resourceConnection;

    /** @var Attribute  */
    protected $attribute;

    /** @var AttributeHandler  */
    protected $attributeHandler;

    /** @var Http  */
    protected $request;

    protected $config;

    /**
     * SaveToVarchar constructor.
     * @param ResourceConnection $resourceConnection
     * @param Attribute $attribute
     * @param AttributeHandler $attributeHandler
     * @param Http $request
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Attribute $attribute,
        AttributeHandler $attributeHandler,
        Http $request,
        Config $config
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->attribute = $attribute;
        $this->attributeHandler = $attributeHandler;
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * Plugin for saving a customer from the admin
     * @param AddressRepository $subject
     * @param AddressInterface $address
     */
    public function afterSave(
        AddressRepository $subject,
        AddressInterface $address
    ) {
        if ($this->config->getIsEnabled()) {
            // Get the addresses from the request
            $addresses = $this->request->getParams();

            if (isset($addresses['address'])) {
                foreach ($addresses['address'] as $editedAddress) {
                    // If we have a 3 word address
                    if (isset($editedAddress['w3w']) && $editedAddress['w3w'] !== '') {
                        $addressId = $editedAddress['entity_id'];
                        $w3w = $editedAddress['w3w'];
                        $this->attributeHandler->saveAttribute($addressId, $w3w);
                    }
                }
            }
        }
    }

    /**
     * @return int
     */
    public function getAttributeId()
    {
        return $this->attribute->getIdByCode('customer_address', 'w3w');
    }
}
