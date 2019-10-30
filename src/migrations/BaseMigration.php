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
     * @var array Each element is an array with keys 'table', 'column', 'refTable', 'refColumn'.
     */
    private $_addForeignKeyList = [];

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
     * @param boolean $addDefaultColumn If TRUE, then add created_at, updated_at etc.
     *
     */
    protected function createTableWithExtraFields($table, $columns, $options = NULL, $addDefaultColumn = TRUE)
    {
        $tableCreated = FALSE;
        try {
            // Merge column definition with default columns.
            if ($addDefaultColumn) {
                $columns = array_merge($this->defaultColumns(), $columns);
            }

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

            // Create table.
            if ($options === NULL && $this->db->driverName === 'mysql') {
                // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
                $options = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            }

            $this->createTable($table, $definitions, $options);
            $tableCreated = TRUE;

            // Set 'id' as primary key if 'id' is not speicified in $columns.
//             if (!isset($definitions[$this->primaryKeyColumnName])) {
//                 $this->addPrimaryKey(self::constraintNamePrimaryKey($table), $table, $this->primaryKeyColumnName);
//             }

            $this->addComments($table, NULL, $comments);
            $this->afterReferenceColumn();
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
            $this->primaryKeyColumnName => $this->primaryKey(),
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
     * @param string|string[]|string[] $columnSets Column to be created index. It may be
     *                                             + string (index for a single column)
     *                                             + string array (multiple indexes are created for multiple columns).
     *                                             + array of string array: Create multiple indexes, each index is for a single or set of columns.
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
        if ($refTable != NULL) {
            $columns = [[$columns, $refTable, $refColumn]];
        }

        foreach ($columns as $columnReference) {
            $column = $columnReference[0];
            $refTable = $columnReference[1];
            $refColumn = (isset($columnReference[2]) && $columnReference[2]) ?
                   $columnReference[2] : $this->primaryKeyColumnName;
            // Create foreign key.
            if (\Yii::$app->db->driverName != 'sqlite') { // SQLite does not support foreign key this way. Set REFERENCES in column definition.
                $this->addForeignKey(self::constraintNameForeignKey($table, $column),
                    $table, $column, $refTable, $refColumn);
            }
            // Create index for foreign key column.
            $this->createIndex(self::constraintNameIndex($table, $column),
                $table, $column);
        }
    }

    /**
     * Return definition for a column that is a foreign key.
     * <p />
     * If DB driver is sqlite, this will return definition with REFERENCES constrain,
     * else it will return $this->integer()->notNull().
     * <p />
     * This method also prepares information for addForeignKey and addIndex, which are invoked inside createTableWithExtraFields()
     *
     * @param string $refTable Table to be referenced.
     * @param boolean $notNull NOT NULL option.
     * @param string $column The column of the current table that is used as foreign key. If null, then it is set as {$refTable}_id
     * @param string $refColumn Default to "id"
     * @param string $type Default to "integer"
     * @return mixed
     */
    protected function referenceColumn($refTable, $notNull = TRUE, $column = NULL, $refColumn = 'id', $type = 'integer')
    {
        if (\Yii::$app->db->driverName == 'sqlite') {
            $definition = [$type];
            if ($notNull) {
                $definition[] = 'NOT NULL';
            }
            $definition[] = "REFERENCES {$refTable}(${refColumn})";
            $result = join(' ', $definition);
        } else {
            $result = $this->$type();
            if ($notNull) {
                $result = $result->notNull();
            }
        }

        // Set foreign key and index information.
        if ($column == NULL) {
            $column = "{$refTable}_id";
        }
        $this->_addForeignKeyList[] = [
            'table' => $this->table,
            'column' => $column,
            'refTable' => $refTable,
            'refColumn' => $refColumn,
        ];

        return $result;
    }

    /**
     * Method should be called after using of referenceColumn().
     * Usullly called in createTableWithExtraFields().
     */
    protected function afterReferenceColumn()
    {
        foreach ($this->_addForeignKeyList as $param) {
            $this->addForeignKeys($param['table'], $param['column'], $param['refTable'], $param['refColumn']);
        }
    }

    /**
     * Register model name and parameter before call insertRecord().
     * @param string $modelClassName
     * @param string ...$fieldNames
     * @deprecated Do not insert data in migration.
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
     * @deprecated Do not insert data in migration.
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
        $this->addComments($table, NULL, $comments);
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