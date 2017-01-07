<?php
namespace batsg\migrations;

use yii\db\Migration;
use yii\db\ActiveRecord;

/**
 * Base class for create a table.
 */
class BaseMigrationCreateTable extends BaseMigration
{
    /**
     * Name of table to be created.
     * @var string
     */
    protected $table;

    /**
     * Create a table, and initiate data.
     * If error occurs and table was created (but info is not added in migration table),
     * then delete the created table.
     * @inheritdoc
     */
    public function safeUp()
    {
        try {
            $this->createDbTable();
            $this->initDbTable();
        } catch (\Exception $e) {
            $this->dropTableThrow($e);
        } catch (\Throwable $e) {
            $this->dropTableThrow($e);
        }
    }

    /**
     * Drop table.
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable($this->table);
    }

    private function dropTableThrow($e)
    {
        // Drop table if exist.
        echo "Drop table $this->table";
        if (\Yii::$app->db->schema->getTableSchema($this->table)) {
            $this->dropTable($this->table);
        }
        throw $e;
    }

    /**
     * Create table.
     * This is called in safeUp().
     */
    protected function createDbTable()
    {
    }

    /**
     * Initiate table data.
     * This is called in safeUp().
     */
    protected function initDbTable()
    {
    }
}