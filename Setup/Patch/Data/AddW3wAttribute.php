<?php
/**
 * What3Words_What3Words
 *
 * @category    WorkInProgress
 * @copyright   Copyright (c) 2020 What3Words
 * @author      Vlad Patru <vlad@wearewip.com>
 * @link        http://www.what3words.com
 */

namespace What3Words\What3Words\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Indexer\Address\AttributeProvider;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

/**
 * Class AddW3wAttribute
 * Setup w3w attribute into database
 */
class AddW3wAttribute implements DataPatchInterface, PatchRevertableInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var CustomerSetup
     */
    private $customerSetupFactory;
    /**
     * @var SetFactory
     */
    private $attributeSetFactory;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param SetFactory $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        SetFactory $attributeSetFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /** {@inheritdoc} */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(AttributeProvider::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet Set */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        if (!$customerSetup->getAttributeId(AttributeProvider::ENTITY, 'w3w')) {
            $customerSetup->addAttribute(AttributeProvider::ENTITY, 'w3w', [
                'label' => '3 word address',
                'input' => 'text',
                'type' => 'varchar',
                'source' => '',
                'required' => false,
                'position' => 500,
                'visible' => true,
                'system' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => true,
                'backend' => ''
            ]);
        }

        $attribute = $customerSetup->getEavConfig()->getAttribute(AttributeProvider::ENTITY, 'w3w');
        $attribute->addData([
            'used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address',
                'adminhtml_checkout',
                'checkout_register',
                'customer_account_edit'
            ],
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true
        ]);
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId

        ]);
        $attribute->save();

        if ($this->moduleDataSetup->tableExists('w3w_sales_order')) {
            $orderData = $this->getOrderData('w3w_sales_order');
            foreach ($orderData as $orderD) {
                $this->moduleDataSetup->getConnection()->update(
                    $this->moduleDataSetup->getTable('sales_order_address'),
                    ['w3w' => $orderD['w3w']],
                    ['parent_id = ?' => $orderD['order_id']]
                );
            }
        }

        if ($this->moduleDataSetup->tableExists('w3w_sales_quote')) {
            $orderData = $this->getOrderData('w3w_sales_quote');
            foreach ($orderData as $orderD) {
                $this->moduleDataSetup->getConnection()->update(
                    $this->moduleDataSetup->getTable('quote_address'),
                    ['w3w' => $orderD['w3w']],
                    ['quote_id = ?' => $orderD['quote_id']]
                );
            }
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /** {@inheritdoc} */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->removeAttribute(Customer::ENTITY, 'w3w');

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /** {@inheritdoc} */
    public function getAliases()
    {
        return [];
    }

    /** {@inheritdoc} */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get old table data
     *
     * @param $table
     * @return mixed
     */
    public function getOrderData($table)
    {
        $orderTable = $this->moduleDataSetup->getConnection();
        $sql = $orderTable->select()->from($table);
        return $orderTable->fetchAll($sql);
    }
}
