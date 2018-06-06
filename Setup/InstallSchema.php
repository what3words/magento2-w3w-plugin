<?php

namespace What3Words\What3Words\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package What3Words\What3Words\Setup
 * @author Vicki Tingle
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Create the quote table
        if ($setup->getConnection()->isTableExists('w3w_sales_quote') == false) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('w3w_sales_quote'))
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false,
                        'primary' => true],
                    'Entity Id'
                )
                ->addColumn(
                    'quote_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true],
                    'Quote Id'
                )
                ->addColumn(
                    'w3w',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['unsigned' => true],
                    'what3words'
                )->addColumn(
                    'address_flag',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true],
                    'Address Flag'
                )->addForeignKey(
                    $installer->getFkName('w3w_sales_quote', 'quote_id', 'quote', 'entity_id'),
                    'quote_id',
                    $installer->getTable('quote'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            $installer->getConnection()->createTable($table);
        }

        // Create the orde table
        if ($setup->getConnection()->isTableExists('w3w_sales_order') == false) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('w3w_sales_order'))
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false,
                        'primary' => true],
                    'Entity Id'
                )
                ->addColumn(
                    'order_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true],
                    'Order Id'
                )
                ->addColumn(
                    'w3w',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['unsigned' => true],
                    'what3words'
                )->addForeignKey(
                    $installer->getFkName('w3w_sales_order', 'order_id', 'sales_order', 'entity_id'),
                    'order_id',
                    $installer->getTable('sales_order'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            $installer->getConnection()->createTable($table);
        }

        // Create the customer address table
        if ($setup->getConnection()->isTableExists('w3w_customer_address') == false) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('w3w_customer_address'))
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false,
                        'primary' => true],
                    'Entity Id'
                )
                ->addColumn(
                    'address_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true],
                    'Address Id'
                )
                ->addColumn(
                    'w3w',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['unsigned' => true],
                    'what3words'
                )->addForeignKey(
                    $installer->getFkName('w3w_customer_address', 'address_id', 'customer_address_entity', 'entity_id'),
                    'address_id',
                    $installer->getTable('customer_address_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
