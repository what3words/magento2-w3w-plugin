<?php

namespace What3Words\What3Words\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use What3Words\What3Words\Model\AddressRepository;

/**
 * Class AttributeHandler
 * @package What3Words\What3Words\Model
 * @author Vicki Tingle
 */
class AttributeHandler
{
    const ATTRIBUTE_CODE = 'w3w';

    /** @var ResourceConnection  */
    protected $resourceConnection;

    /** @var Attribute  */
    protected $attribute;

    /** @var \What3Words\What3Words\Model\AddressRepository  */
    protected $addressRepository;

    /**
     * AttributeHandler constructor.
     * @param ResourceConnection $resourceConnection
     * @param Attribute $attribute
     * @param \What3Words\What3Words\Model\AddressRepository $addressRepository
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Attribute $attribute,
        AddressRepository $addressRepository
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->attribute = $attribute;
        $this->addressRepository = $addressRepository;
    }

    /**
     * To handle the custom attribute reliably, we're using
     * direct queries
     * @param $addressId
     * @param $w3w
     */
    public function saveAttribute($addressId, $w3w)
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName('customer_address_entity_varchar');
        $attributeId = $this->getAttributeId();

        if ($attributeId) {
            // Figure out if we're editing or creating a new entry
            $select = $connection->select()
                ->from(
                    array('caev' =>  $table)
                )->where('entity_id=' . $addressId, 'attribute_id =' . (int)$attributeId);
            $result = $connection->fetchAll($select);

            // Update query if editing
            if (!empty($result)) {
                $connection->update(
                    $table,
                    array(
                        'value' => $w3w
                    ),
                    array(
                        'attribute_id = ?' => (int)$attributeId,
                        'entity_id = ?' => (int)$addressId
                    )
                );
                // Insert if new
            } else {
                $connection->insert(
                    $table,
                    array(
                        'attribute_id' => (int)$attributeId,
                        'entity_id' => $addressId,
                        'value' => $w3w
                    )
                );
            }
        }

        // Check for a 3 word address in our custom tables as well
        if ($addressItems = $this->addressRepository->getW3wByAddressId($addressId)) {
            /** @var \What3Words\What3Words\Model\Address $addressItem */
            foreach ($addressItems as $addressItem) {
                $addressItem->setW3w($w3w);
                $this->addressRepository->save($addressItem);
            }
        } else {
            // Else create a new one
            $data = array(
                'address_id' => $addressId,
                'w3w' => $w3w
            );
            $addressItem = $this->addressRepository->create();
            $addressItem->setData($data);
            $this->addressRepository->save($addressItem);
        }
    }

    /**
     * Return the attribute ID from the attribute code
     * @return int | bool
     */
    public function getAttributeId()
    {
        if ($attributeId = $this->attribute->getIdByCode('customer_address', self::ATTRIBUTE_CODE)) {
            return $attributeId;
        }
        return false;
    }
}
