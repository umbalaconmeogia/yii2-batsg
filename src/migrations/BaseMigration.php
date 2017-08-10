<?php
namespace batsg\migrations;

use yii\db\Migration;
use yii\db\ActiveRecord;

/**
 * Base class for migration classes.
 */
class BaseMigration extends Migration
{
    /**
     * @var string
     */
    public $primaryKeyColumnName = 'id';

    /**
     * @var string[][] Mapping ModelClassName => insert $fieldNames
     */
    private $insertMetaInfo = [];

    /**
     * Generate constraint name for primary key.
     * @param string $table Table name.
     * @param string $column Primary column name.
     * @return string
     */
    public static function constraintNamePrimaryKey($table)
    {
        return self::constraintName($table, NULL, 'pkey');
    }

    /**
     * Generate constraint name for foreign key.
     * @param string $table Table name.
     * @param string $column Name of column to be foreign key.
     * @return string
     */
    public static function constraintNameForeignKey($table, $column)
    {
        return self::constraintName($table, $column, 'fkey');
    }

    /**
     * Generate constraint name for index.
     * @param string $table Table name.
     * @param string $column Name of column to be indexed.
     * @return string
     */
    public static function constraintNameIndex($table, $column)
    {
        return self::constraintName($table, $column, 'idx');
    }

    /**
     * Generate constraint name for primary key, foreign key, index...
     * @param string $table
     * @param string|string[] $columns
     * @param string $suffix
     * @return string
     */
    private static function constraintName($table, $columns, $suffix)
    {
        if (!is_array($columns)) {
            $columns = $columns ? array_map('trim', explode(',', $columns)) : [];
        }
        $columns[] = $suffix;
        return join('-', array_merge([$table], $columns));
    }

    // TODO: Should run create db table in try {} catch.
    /**
     * Create a table with specified columns, adding the columns bellow automatically.
     * The column "id" is set as primary key.
     * <ul>
     * <li>id</li>
     * <li>data_status</li>
     * <li>create_time</li>
     * <li>create_user_id</li>
     * <li>update_time</li>
     * <li>update_user_id</li>
     * </ul>
     * Usage example:
     * <pre>
     *   // Without specifying column comment.
     *   $this->createTableWithExtraFields('employee', [
     *     'name' => $this->text(),
     *     'age' => $this->integer(),
     *   ]);
     *   // With specifying column comment.
     *   $this->createTableWithExtraFields('employee', [
     *     'name' => [$this->text(), 'Employee name'],
     *     'age' => [$this->integer(), 'Age']
     *   ]);
     * </pre>
     * @param string $table Table name.
     * @param string[] $columns The columns information in two types: name => definition or name => [definition, comment]
     *                          If 'id' is not specified in $columns, then it will be added and is set as Primary Key.
     * @param string $options additional SQL fragment that will be appended to the generated SQL.
     *
     */
    protected function createTableWithExtraFields($table, $columns, $options = NULL)
    {
        $tableCreated = FALSE;
        try {
            // Prepare columns' definition and comment information.
            $definitions = [];
            $comments = [];
            foreach ($columns as $columnName => $info) {
                if (is_array($info)) {
                    $definitions[$columnName] = $info[0];
                    $comments[$columnName] = $info[1];
                } else {
                    $definitions[$columnName] = $info;
                }
            }

            // Merge column definition with default columns.
            $columns = array_merge(
                $this->defaultColumns(),
                $definitions
            );
            // Create table.
            $this->createTable($table, $columns, $options);
            $tableCreated = TRUE;

            // Set 'id' as primary key if 'id' is not speicified in $columns.
            if (!isset($definitions[$this->primaryKeyColumnName])) {
                $this->addPrimaryKey(self::constraintNamePrimaryKey($table), $table, $this->primaryKeyColumnName);
            }

            $this->addComments($table, NULL, $comments);
        } catch (\Exception $e) {
            if ($tableCreated) {
                $this->dropTable($table);
            }
            throw $e;
        }
    }

    /**
     * Default columns added to table.
     * @return \yii\db\ColumnSchemaBuilder[]
     */
    protected function defaultColumns()
    {
        $columns = [
            $this->primaryKeyColumnName => $this->string(),
            'data_status' => $this->integer()->defaultValue(1),
            'created_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_by' => $this->integer(),
            'updated_at' => $this->integer(),
        ];
        return $columns;
    }

    /**
     * Add comment on columns and table.
     * @param string $table
     * @param string $tableComment
     * @param array $columnComments Mapping between column name and its comment.
     */
    protected function addComments($table, $tableComment = NULL, $columnComments = [])
    {
        if (\Yii::$app->db->driverName != 'sqlite') {
            foreach ($columnComments as $column => $comment) {
                $this->addCommentOnColumn($table, $column, $comment);
            }
            if ($tableComment) {
                $this->addCommentOnTable($table, $tableComment);
            }
        }
    }

    /**
     * Create multiple indexes.
     * @param string $table
     * @param string|string[]|string[] $columnSets
     */
    protected function createIndexes($table, $columnSets)
    {
        if (!is_array($columnSets)) {
            $columnSets = [$columnSets];
        }
        foreach ($columnSets as $columns) {
            $this->createIndex(self::constraintNameIndex($table, $columns), $table, $columns);
        }
    }

    /**
     * Define foreign key constraint, and create index for foreign key column.
     * Example usage:
     * <pre>
     *   // tbl_employee.company_id refer to tbl_company.id
     *   $this->addForeignKeys('tbl_employee', 'company_id', 'tbl_company', 'id');
     *
     *   // tbl_employee.company_id refer to tbl_company.id and
     *   // tbl_employee.division_id refer to tbl_division.id
     *   $this->addForeignKeys('tbl_employee', [
     *     ['company_id', 'tbl_company', 'id'],
     *     ['division_id', 'tbl_division', 'id'],
     *   ]);
     * </pre>
     * @param string $table the table that the foreign key constraint will be added to.
     * @param string|array[] $columns A column or
     *                       an array with each element is an array that contains column, refTable, refColumn.
     * @param string $refTable
     * @param string $refColumn
     */
    protected function addForeignKeys($table, $columns, $refTable = NULL, $refColumn = 'id')
    {
        if (\Yii::$app->db->driverName != 'sqlite') { // SQLite does not support foreign key this way. Set REFERENCES in column definition.
            if ($refTable != NULL) {
                $columns = [[$columns, $refTable, $refColumn]];
            }
            foreach ($columns as $columnReference) {
                $column = $columnReference[0];
                $refTable = $columnReference[1];
                $refColumn = (isset($columnReference[2]) && $columnReference[2]) ?
                       $columnReference[2] : $this->primaryKeyColumnName;
                echo "$column, $refTable, $refColumn\n";
                // Create foreign key.
                $this->addForeignKey(self::constraintNameForeignKey($table, $column),
                    $table, $column, $refTable, $refColumn);
                // Create index for foreign key column.
                $this->createIndex(self::constraintNameIndex($table, $column),
                    $table, $column);
            }
        }
    }

    /**
     * Register model name and parameter before call insertRecord().
     * @param string $modelClassName
     * @param string ...$fieldNames
     */
    protected function registerInsertMeta()
    {
        $args = func_get_args();
        $modelClassName = array_shift($args);
        $this->insertMetaInfo[$modelClassName] = $args;
    }

    /**
     * Insert a record. The meta data is set by registerInsertMeta().
     * @param string $modelClassName
     * @param mixed ...$fieldValue
     * @return ActiveRecord
     */
    protected function insertRecord()
    {
        $args = func_get_args();
        $modelClassName = array_shift($args);
        if (!isset($this->insertMetaInfo[$modelClassName])) {
            throw new \Exception("Must call registerInsertMeta() before calling insertRecord().");
        }
        $model = new $modelClassName;
        $values = $args;
        foreach ($this->insertMetaInfo[$modelClassName] as $i => $fieldName) {
            if (isset($values[$i])) {
                $model->$fieldName = $values[$i];
            }
        }
        $model->saveThrowError();
        return $model;
    }

    /**
     * Add column and comment on table.
     * Usage example:
     * <pre>
     *   $this->addColumnWithComments('employee', [
     *     'name' => $this->text(), // Without specifying column comment.
     *     'age' => [$this->integer(), 'Age'], // With specifying column comment.
     *   ]);
     * </pre>
     * @param string $table Table name.
     * @param string[] $columns The columns information in two types: name => definition or name => [definition, comment]
     */
    protected function addColumnsWithComment($table, $columns)
    {
        // Prepare columns' definition and comment information.
        $definitions = [];
        $comments = [];
        foreach ($columns as $columnName => $info) {
            if (is_array($info)) {
                $definitions[$columnName] = $info[0];
                $comments[$columnName] = $info[1];
            } else {
                $definitions[$columnName] = $info;
            }
        }

        // Add columns.
        foreach ($definitions as $column => $type) {
            $this->addColumn($table, $column, $type);
        }

        // Add comments.
        foreach ($comments as $column => $comment) {
            $this->addCommentOnColumn($table, $column, $comment);
        }
    }

    /**
     * Drop list of columns.
     * @param string $table
     * @param string[] $columns
     */
    protected function dropColumns($table, $columns)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        foreach ($columns as $column) {
            $this->dropColumn($table, $column);
        }
    }
}