<?php
/**
 * @package Styla/Connect2
 * @author Oskar Wolanin <owolanin@divante.co>
 * @copyright 2018 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Styla\Connect2\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            if (!$installer->tableExists('styla_magazine')) {
                $table = $setup->getTable('styla_magazine');
                $storeIndexName = $installer->getIdxName($table, 'store_id');
                $storeForeignKeyName = $installer->getFkName(
                    'styla_magazine',
                    'store_id',
                    'store',
                    'store_id'
                );

                //set a unique index on store + front_name to prevent a having multiple magazines
                //on the same store with the same url
                $storeFrontNameIndex = $installer->getIdxName(
                    'styla_magazine',
                    [
                        'store_id',
                        'front_name',
                    ],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                );

                $installer->run(
                    "CREATE TABLE `$table` (
                        `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                        `store_id` SMALLINT(5) UNSIGNED NULL,
                        `is_active` TINYINT(1) UNSIGNED NULL,
                        `is_default` TINYINT(1) UNSIGNED NULL,
                        `use_magento_layout` TINYINT(1) UNSIGNED NULL,
                        `include_in_navigation` TINYINT(1) UNSIGNED NULL,
                        `navigation_label` VARCHAR(255) NULL,
                        `front_name` VARCHAR(255) NULL,
                        `client_name` VARCHAR(255) NULL,
                        PRIMARY KEY (`id`),
                        INDEX `$storeIndexName` (`store_id` ASC),
                        CONSTRAINT `$storeForeignKeyName`
                            FOREIGN KEY (`store_id`)
                            REFERENCES `{$installer->getTable('store')}` (`store_id`)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE);"
                );


                $installer->run(
                    "
                    ALTER TABLE `$table`
                    ADD INDEX `$storeFrontNameIndex` (`store_id` ASC, `front_name` ASC);
                "
                );
            }
        }
        $installer->endSetup();
    }
}