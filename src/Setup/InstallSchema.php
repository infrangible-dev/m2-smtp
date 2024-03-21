<?php

namespace Infrangible\Smtp\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class InstallSchema
    implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        $logTableName = $setup->getTable('smtp_log');

        if ($connection->isTableExists($logTableName)) {
            $connection->dropTable($logTableName);
        }

        $logTable = $connection->newTable($logTableName);

        $logTable->addColumn('log_id', Table::TYPE_INTEGER, 10,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'The identifier of the log');
        $logTable->addColumn('from', Table::TYPE_TEXT, 2048, ['nullable' => true]);
        $logTable->addColumn('sender', Table::TYPE_TEXT, 2048, ['nullable' => true]);
        $logTable->addColumn('to', Table::TYPE_TEXT, 2048, ['nullable' => true]);
        $logTable->addColumn('cc', Table::TYPE_TEXT, 2048, ['nullable' => true]);
        $logTable->addColumn('bcc', Table::TYPE_TEXT, 2048, ['nullable' => true]);
        $logTable->addColumn('reply_to', Table::TYPE_TEXT, 2048, ['nullable' => true]);
        $logTable->addColumn('subject', Table::TYPE_TEXT, 255, ['nullable' => true]);
        $logTable->addColumn('encoding', Table::TYPE_TEXT, 255, ['nullable' => true]);
        $logTable->addColumn('type', Table::TYPE_TEXT, 255, ['nullable' => true]);
        $logTable->addColumn('body', Table::TYPE_TEXT, 32768, ['nullable' => true]);
        $logTable->addColumn('status', Table::TYPE_SMALLINT, 1, ['nullable' => true]);
        $logTable->addColumn('message', Table::TYPE_TEXT, 32768, ['nullable' => true]);
        $logTable->addColumn('created_at', Table::TYPE_DATETIME, null, ['nullable' => false]);
        $logTable->addColumn('updated_at', Table::TYPE_DATETIME, null, ['nullable' => true]);

        $connection->createTable($logTable);
    }
}
