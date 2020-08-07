<?php
namespace batsg\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use batsg\Y;

/**
 * Model that has field $id, $data_status, $created_at, $created_by, $update_time, $updated_by.
 *
 * @property mixed $id
 * @property integer $data_status Record status. 1: new, 2: update, 9: delete.
 * @property mixed $created_by User id of creator.
 * @property integer $created_at Created timestamp.
 * @property mixed $updated_by User id of updator.
 * @property integer $updated_at Updated timestamp.
 * @property string $unique_id A unique string that is assigned to this record.
 *
 * @property string $createdAt Created date time.
 * @property string $createdAtDate Created date.
 * @property string $updatedAt Updated date time.
 * @property string $updatedAtDate Updated date.
 * @property string $dataStatusStr
 */
class BaseBatsgModel extends BaseModel
{
    const DATA_STATUS_ACTIVE = 1;
    const DATA_STATUS_DELETE = 9;

    /**
     * If TRUE, generate UUID for ID.
     * If ID is generated by RDBMS automatically (for example, AUTO_INCREMENT in MySQL)
     * set $genUuid = FALSE.
     * @var boolean
     */
    public $genUuid = FALSE;

    /**
     * {@inheritDoc}
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors()
    {
        return [
//             BlameableBehavior::className(),
            TimestampBehavior::className(),
        ];
    }

    /**
     * Perform massiveAssignment to a model.
     * @param CActiveRecord $model
     * @param array $parameters key=>value to assign to $model->attributes.
     * @param array $exclusiveFields Fields that are not assigned.
     */
//     public static function massiveAssign($model, $parameters,
//             $exclusiveFields = array('id', 'created_at', 'created_by', 'update_time', 'updated_by'))
//     {
//         parent::massiveAssign($model, $parameters, $exclusiveFields);
//     }

    /**
     * Get only valid models (data_status <> deleted) from model list.
     * @param BaseBatsgModel[] $modelList
     */
//     public static function getValidModels($modelList)
//     {
//         $result = array();
//         foreach ($modelList as $model) {
//             if ($model->data_status <> self::DATA_STATUS_DELETE) {
//                 $result[] = $model;
//             }
//         }
//         return $result;
//     }

    /**
     * Create ActiveQuery of finding records that are not deleted logically (data_status <> 9).
     * @param string|array|ExpressionInterface $condition the conditions that should be put in the WHERE part.
     * @param array $params — the parameters (name => value) to be bound to the query.
     * @return ActiveQuery
     */
    public static function findNotDeleted($condition = NULL, $params = []) {
        /* @var $result ActiveQuery */
        $result = static::find()->where(['OR', 'data_status IS NULL', ['!=', 'data_status', self::DATA_STATUS_DELETE]]);
        if ($condition) {
            $result = $result->andWhere($condition, $params);
        }
        return $result;
    }

    /**
     * Find all records that are not deleted logically (data_status <> 9).
     * @param string|array|ExpressionInterface $condition the conditions that should be put in the WHERE part.
     * @param array $params — the parameters (name => value) to be bound to the query.
     * @return Model[] Array of caller class objects.
     */
    public static function findAllNotDeleted($condition = NULL, $params = [])
    {
        return self::findNotDeleted($condition, $params)->all();
    }

    /**
     * Reset fields below to NULL.
     *     id
     *     data_status
     *     created_at
     *     created_by
     *     update_time
     *     updated_by
     */
    public function resetCommonFields()
    {
        $this->setFieldToNull(array('id', 'data_status', 'created_at', 'created_by', 'update_time', 'updated_by'));
    }

    public function deleteLogically()
    {
        $this->data_status = self::DATA_STATUS_DELETE;
        if (!$this->save()) {
            $this->logError();
            throw new \Exception("Error while deleting " . $this);
        }
    }

    /**
     * Generate unique string to be used as primary key.
     * @param string $prefix
     * @return string
     * @deprecated Not used.
     */
    public function generateId($prefix = NULL)
    {
        return $this->generateUniqueRandomAttribute('id', $prefix);
    }

    /**
     * created_at in date time format.
     * @return string
     */
    public function getCreatedAt()
    {
        return \Yii::$app->formatter->asDatetime($this->created_at);
    }

    /**
     * updated_at in date time format.
     * @return string
     */
    public function getUpdatedAt()
    {
        return \Yii::$app->formatter->asDatetime($this->updated_at);
    }

    /**
     * created_at in date format.
     * @return string
     */
    public function getCreatedAtDate()
    {
        return \Yii::$app->formatter->asDate($this->created_at);
    }

    /**
     * updated_at in date format.
     * @return string
     */
    public function getUpdatedDate()
    {
        return \Yii::$app->formatter->asDate($this->updated_at);
    }

    /**
     * Generate id for new record.
     * {@inheritDoc}
     * @see \yii\base\Model::beforeValidate()
     */
    public function beforeSave($insert)
    {
        $result = parent::beforeSave($insert);
		// Set unique_id.
        if ($result) {
            $this->generateUniqueIdAttribute();
        }
        return $result;
    }

    /**
     * Attribute name of the unique Id attribute.
     * If specified, then this attribute will be generated beforeSave();
     * Sub class should overwrite this method if use unique Id attribute.
     * @return string
     */
    protected function uniqueIdAttributeName()
    {
        return NULL;
    }

    /**
     * Generate unique id.
     * @param string $attributeName The name of the attribute. If NULL, then uniqueIdAttributeName() will be called to get the attribute name.
     */
    protected function generateUniqueIdAttribute($attributeName = NULL)
    {
        if (!$attributeName) {
            $attributeName = $this->uniqueIdAttributeName();
        }
        if ($attributeName && $this->hasAttribute($attributeName) && !$this->$attributeName) {
            $this->$attributeName = $this->generateUniqueRandomAttribute($attributeName);
        }
    }

    /**
     * Create <em>data_status <> 9</em> (not deleted) conditiion for a query string.
     *
     * @param ActiveQuery $query
     * @param string $tableName Specify table name (NULL for the table of this model).
     * @return string[] condition used in where(), addOnCodition()
     */
    public static function conditionNotDelete($tableName = NULL)
    {
        if (!$tableName) {
            $tableName = static::tableName();
        }
        return ['OR', "$tableName.data_status IS NULL", ['!=', "$tableName.data_status", self::DATA_STATUS_DELETE]];
    }

    /**
     * Add <em>data_status <> 9</em> (not deleted) condition to a query.
     * @param ActiveQuery $query
     * @param string $tableName Specify table name (NULL for the table of this model).
     */
    public static function addWhereNotDeleted(ActiveQuery $query, $tableName = NULL)
    {
        $query->andWhere(self::conditionNotDelete($tableName));
    }

    /**
     * @return string
     */
    public function getDataStatusStr()
    {
        return ArrayHelper::getValue(static::dataStatusOptionArr(), $this->data_status);
    }

    /**
     * @return string[]
     */
    public static function dataStatusOptionArr()
    {
        return [
            self::DATA_STATUS_ACTIVE => Y::t('active'),
            self::DATA_STATUS_DELETE => Y::t('deleted'),
        ];
    }

    /**
     * Get list of all data of table represented by this model.
     *
     * @param string $valueField
     * @param string $keyField
     * @return array If $valueField is specified, mapping keyField => keyValue
     *               Else mapping keyField => object.
     */
    public static function allDataOptionArr($valueField = NULL, $keyField = 'id')
    {
        return self::hashModels(static::findAllNotDeleted(), $keyField, $valueField);
    }

    /**
     * Get string that represent a relational data of this record.
     * For example, for an Employee record, we want to get its Department Name.
     * So we want to get $employee->department->name.
     * Example: The two commands below are equivalent.
     * <pre>
     *   $employee->getReleationDataName('department_id');
     *   $employee->department->id ? $employee->department->name : NULL;
     * </pre>
     * @deprecated This method is unceccessary. Below is simple enough and faster?
     *   $employee->department->id ? $employee->department->name : NULL;
     *
     * @param string $foreignKeyField Example: department_id
     * @param string $relationalObjectField If NULL, then it will be assumbed based on $foreignKeyField
     * @param string $valueField Name of field to get value in relational object. Example: name (of Department record).
     * @reurn mixed $this->$relationalObjectField->$valueField
     */
//     public function getRelationDataName($foreignKeyField, $relationalObjectField = NULL, $valueField = 'name')
//     {
//         if (!$relationalObjectField) {
//             // Cut _id at the end of $foreignKeyField
//             $relationalObjectField = preg_replace('/_id$/', '', $foreignKeyField);
//             // Change to camel case.
//             $relationalObjectField = Inflector::variablize($relationalObjectField);
//         }
//         return $this->$foreignKeyField ? $this->$relationalObjectField->$valueField : NULL;
//     }

    /**
     * Call updateAll(), also update 'updated_at' to current time.
     * See updateAll() for parameters.
     * @param array $attributes attribute values (name-value pairs) to be saved into the table
     * @param string|array $condition the conditions that will be put in the WHERE part of the UPDATE SQL.
     * Please refer to [[Query::where()]] on how to specify this parameter.
     * @param array $params the parameters (name => value) to be bound to the query.
     * @return int the number of rows updated
     */
    public static function updateAllTouchUpdatedAt($attributes, $condition = '', $params = [], $updatedAtAttribute = 'updated_at')
    {
        if (!isset($attributes[$updatedAtAttribute])) {
            $attributes[$updatedAtAttribute] = time();
        }
        return self::updateAll($attributes, $condition, $params);
    }
}
?>