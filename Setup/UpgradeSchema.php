<?php
/**
 * @package   Styla/Connect2
 * @author    Oskar Wolanin <owolanin@divante.co>
 * @copyright 2018 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Styla\Connect2\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            if (!$installer->tableExists('styla_magazine')) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('styla_magazine')
                )
                                   ->addColumn(
                                       'id',
                                       \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                                       10,
                                       [
                                           'identity' => true,
                                           'nullable' => false,
                                           'primary'  => true,
                                           'unsigned' => true,
                                       ],
                                       'Magazine ID'
                                   )
                                   ->addColumn(
                                       'store_id',
                                       \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                                       1,
                                       [
                                           'nullable' => true,
                                           'unsigned' => true,
                                       ],
                                       'Store ID'
                                   )
                                   ->addColumn(
                                       'is_active',
                                       \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                                       1,
                                       [
                                           'nullable' => true,
                                           'unsigned' => true,
                                       ],
                                       'Magazine active'
                                   )
                                   ->addColumn(
                                       'is_default',
                                       \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                                       1,
                                       [
                                           'nullable' => true,
                                           'unsigned' => true,
                                       ],
                                       'Default magazine'
                                   )
                                   ->addColumn(
                                       'use_magento_layout',
                                       \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                                       1,
                                       [
                                           'nullable' => true,
                                           'unsigned' => true,
                                       ],
                                       'Magento layout'
                                   )
                                   ->addColumn(
                                       'include_in_navigation',
                                       \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                                       1,
                                       [
                                           'nullable' => true,
                                           'unsigned' => true,
                                       ],
                                       'Include in navigation'
                                   )
                                   ->addColumn(
                                       'navigation_label',
                                       \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                       255,
                                       ['nullable' => true],
                                       'Navigation label'
                                   )
                                   ->addColumn(
                                       'front_name',
                                       \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                       255,
                                       ['nullable' => true],
                                       'Front name'
                                   )
                                   ->addColumn(
                                       'client_name',
                                       \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                       255,
                                       ['nullable' => true],
                                       'Client name'
                                   )
                                   ->setComment('Styla Magazines');

                $installer->getConnection()->createTable($table);

                $storeForeignKeyName = $installer->getFkName(
                    'styla_magazine',
                    'store_id',
                    'store',
                    'store_id'
                );

                $installer->run(
                    "
                    ALTER TABLE `styla_magazine`
                    ADD CONSTRAINT `$storeForeignKeyName`
                    FOREIGN KEY (`store_id`)
                    REFERENCES `{$installer->getTable('store')}` (`store_id`)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE;
                ");

                $storeIndexName = $installer->getIdxName(
                    'styla_magazine',
                    'store_id',
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                );

                //set a unique index on store + front_name to prevent a having multiple magazines
                //on the same store with the same url
                $storeFrontNameIndex = $installer->getIdxName(
                    'styla_magazine',
                    [
                        'store_id',
                        'front_name',
                    ],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                );

                $installer->getConnection()->addIndex(
                    $installer->getTable('styla_magazine'),
                    $storeIndexName,
                    ['store_id']
                );

                $installer->getConnection()->addIndex(
                    $installer->getTable('styla_magazine'),
                    $storeFrontNameIndex,
                    [
                        'store_id',
                        'front_name',
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            //set a unique index on store + front_name to prevent a having multiple magazines
            //on the same store with the same url
            $storeFrontNameIndex = $installer->getIdxName(
                'styla_magazine',
                [
                    'store_id',
                    'front_name',
                ],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            );

            $installer->getConnection()->dropIndex($installer->getTable('styla_magazine'), $storeFrontNameIndex);

            $installer->getConnection()->addIndex(
                $installer->getTable('styla_magazine'),
                $storeFrontNameIndex,
                [
                    'store_id',
                    'front_name',
                ],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            );
        }

        $installer->endSetup();
    }
}
