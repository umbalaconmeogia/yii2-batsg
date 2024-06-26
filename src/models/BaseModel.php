<?php

namespace batsg\models;

use batsg\helpers\HRandom;
use Exception;
use Yii;
use yii\db\ActiveRecord;

class BaseModel extends \yii\db\ActiveRecord
{
    /**
     * Get all errors on a model.
     * @param ActiveRecord $model
     * @param string $attribute attribute name. Use null to retrieve errors for all attributes.
     * @return array errors for all attributes or the specified attribute. Empty array is returned if no error.
     */
    public static function getErrorMessagesModel($model, $attribute = NULL)
    {
        if ($attribute === NULL) {
            $attribute = $model->attributes();
        }
        if (!is_array($attribute)) {
            $attribute = array($attribute);
        }
        $errors = array();
        foreach ($attribute as $attr) {
            if ($model->hasErrors($attr)) {
                $errors = array_merge($errors, array_values($model->getErrors($attr)));
            }
        }
        return $errors;
    }

    /**
     * Log error of a model.
     * @param ActiveRecord $model
     * @param string $message The message to be exported first.
     * @param string $category
     */
    public static function logErrorModel($model, $message = NULL, $category = 'application')
    {
        if ($message) {
            Yii::error($message, $category);
        }
        Yii::error($model->tableName() . " " . print_r($model->attributes, TRUE), $category);
        Yii::error(print_r(self::getErrorMessagesModel($model), TRUE), $category);
    }

    /**
     * Save a model, write error to log if error occurs.
     * @param ActiveRecord $model
     * @param string $errorMessage
     * @return boolean
     */
    public static function saveLogErrorModel($model, $errorMessage = NULL)
    {
        if ($errorMessage === NULL) {
            $errorMessage = "Error while saving " . self::toStringModel($model);
        }
        $result = $model->save();
        if (!$result) {
            self::logErrorModel($model, $errorMessage);
        }
        return $result;
    }

    /**
     * Save a model, write error to log and throw exception if error occurs.
     * @param ActiveRecord $model
     * @param string $errorMessage
     * @throws \Exception
     */
    public static function saveThrowErrorModel($model, $errorMessage = NULL)
    {
        if ($errorMessage === NULL) {
            $errorMessage = "Error while saving " . self::toStringModel($model);
        }
        if (!self::saveLogErrorModel($model, $errorMessage)) {
            throw new \Exception($errorMessage);
        }
    }

    /**
     * Create a hash of model list by a field value.
     * @param ActiveRecord[] $models
     * @param string $keyField Default by id.
     * @param string $valueField If specified, then this field's value is used as array's value.
     *                           Else set the model instance as value.
     * @return array field $keyValue => ($value or model).
     */
    public static function hashModels($models, $keyField = 'id', $valueField = NULL) {
        $hash = [];
        foreach ($models as $model) {
            $hash[$model->$keyField] = $valueField ? $model->$valueField : $model;
        }
        return $hash;
    }

    /**
     * Create an array of model's field value.
     * @param ActiveRecord $models
     * @param string $keyField
     * @return array
     */
    public static function getArrayOfFieldValue($models, $keyField = 'id') {
        $values = [];
        foreach ($models as $model) {
            $values[] = $model->$keyField;
        }
        return $values;
    }

    public function __toString()
    {
        return $this->toString($this->toStringFields());
    }

    /**
     * Create a string that describe all fields of an model object.
     * @param ActiveRecord $model
     * @param mixed $fields String or string array. If NULL, all attributes are used.
     * @return string.
     */
    public static function toStringModel($model, $fields = NULL)
    {
        // Get attributes.
        if ($fields === NULL) {
            $fields = array_keys($model->attributes);
        }
        if (!is_array($fields)) {
            $fields = array($fields);
        }
        $info = [];
        foreach ($fields as $field) {
            $value = is_array($model->$field) ? print_r($model->$field, TRUE) : $model->$field;
            $info[] = "$field: $value";
        }

        // Get class name.
        $className = get_class($model);
        $className = ($pos = strrpos($className, '\\')) === FALSE ? $className : substr($className, $pos + 1);

        return "$className(" . join(', ', $info) . ')';

    }

    /**
     * Define mandator fields to be displayed in __toString().
     *
     * Display only primary key fields by default. Sub class should override this.
     *
     * @return string[]
     */
    protected function toStringFields()
    {
        return $this->tableSchema->primaryKey;
    }

    /**
     * Generate a random and unique value for an attribute.
     * Developer may overwrite the generateUniqueRandomAttribute() function, to decide to generate an integer value (by calling generateUniqueRandomInteger) or a string (by calling generateUniqueRandomString()).
     * @param string $attribute The attribute to be checked.
     * @param string $prefix The prefix of the generated string.
     * @param number $length The length of the string.
     * @param string $characterSet
     *            If specified, then only character in this string is used.
     * @param integer $characterCase
     *            If 0, character is case sensitive. If -1, all characters are converted to lower case. If 1, all characters are converted to upper case.
     */
    public function generateUniqueRandomAttribute($attribute, $prefix = NULL, $length = 12, $characterSet = NULL, $characterCase = 0)
    {
        // Loop until find unique value.
        do {
            $randomValue = $this->generateUniqueRandomValue($prefix, $length, $characterSet, $characterCase);
            if ($this->findOne([$attribute => $randomValue])) {
                $randomValue = null;
            }
        } while ($randomValue == null);

        return $randomValue;
    }

    /**
     * Generate a random and unique value.
     * Overwrite this function to decide to generate an integer value (by calling generateUniqueRandomBigInteger())
     * or a string (by calling generateUniqueRandomString()).
     * @param string $prefix The prefix of the generated string.
     * @param number $length The length of the string.
     * @param string $characterSet
     *            If specified, then only character in this string is used.
     * @param integer $characterCase
     *            If 0, character is case sensitive. If -1, all characters are converted to lower case. If 1, all characters are converted to upper case.
     */
    protected function generateUniqueRandomValue($prefix = NULL, $length = 12, $characterSet = NULL, $characterCase = 0)
    {
        return $this->generateUniqueRandomBigInteger($prefix);
        //return $this->generateUniqueRandomString($prefix, $length, $characterSet, $characterCase);
    }

    /**
     * Generate a random bigint value.
     * @param string $prefix The prefix of the generated string.
     * @return number
     */
    protected function generateUniqueRandomBigInteger($prefix = NULL)
    {
        return $prefix . random_int(1, PHP_INT_MAX);
    }

    /**
     * Generate a random string that is unique when put on an attribute of a DB table.
     * @param string $prefix The prefix of the generated string.
     * @param number $length The length of the string.
     * @param string $characterSet
     *            If specified, then only character in this string is used.
     * @param integer $characterCase
     *            If 0, character is case sensitive. If -1, all characters are converted to lower case. If 1, all characters are converted to upper case.
     * @return string
     */
    protected function generateUniqueRandomString($prefix = NULL, $length = 12, $characterSet = NULL, $characterCase = 0)
    {
        $randomString = HRandom::generateRandomString($length, $characterSet, $characterCase);
        $randomString = $prefix . $randomString;
        return $randomString;
    }

    /**
     * Find one model by its id.
     * @param string $id
     * @param mixed $nullValue Value to return if not found.
     * @return ActiveRecord
     */
    public static function findModel($id, $nullValue = NULL)
    {
        $result = $nullValue;
        if ($id) {
            $result = static::findOne($id);
        }
        return $result;
    }

    /**
     * Find one object that match $condition. If not exist, create new one with specified condition.
     * Example of usage:
     * ```php
     * // Find a user by specified user name. If not found, create new object but not save into DB.
     * $user = User::findOneCreateNew(['username' => 'admin']);
     * // Find a user by specified user name. If not found, create new object and save into DB.
     * $user = User::findOneCreateNew(['username' => 'admin'], TRUE);
     * ```
     * @param array  $condition (type of key=>value).
     * @param boolean $saveDb Save record into DB or not incase create new.
     * @param string $className Class name of the model to be found in. If null, then caller class is used.
     * @return \batsg\models\BaseModel
     */
    public static function findOneCreateNew($condition, $saveDb = FALSE, $className = NULL)
    {
        if (!$className) {
            $className = static::className();
        }
        $result = $className::findOne($condition);
        if (!$result) {
            $result = \Yii::createObject($className);
            \Yii::configure($result, $condition);
            if ($saveDb) {
                self::saveThrowErrorModel($result);
            }
        }
        return $result;
    }

    /**
     * Find all objects that match $condition. If not exist, create new one with specified condition.
     * Example of usage:
     * ```php
     * // Find a user by specified user name. If not found, create new object but not save into DB.
     * $user = User::findAllCreateNew(['username' => 'admin']);
     * // Find a user by specified user name. If not found, create new object and save into DB.
     * $user = User::findAllCreateNew(['username' => 'admin'], TRUE);
     * ```
     * @param array  $condition (type of key=>value).
     * @param boolean $saveDb Save record into DB or not incase create new.
     * @param string $className Class name of the model to be found in. If null, then caller class is used.
     * @return \batsg\models\BaseModel[]
     */
    public static function findAllCreateNew($condition, $saveDb = FALSE, $className = NULL)
    {
        if (!$className) {
            $className = static::class;
        }
        $result = $className::findAll($condition);
        if (!$result) {
            $model = \Yii::createObject($className);
            \Yii::configure($model, $condition);
            if ($saveDb) {
                self::saveThrowErrorModel($model);
            }
            $result = [$model];
        }
        return $result;
    }

    /**
     * Find one object by specified attributes condition, and set another attributes' value.
     * If not exist, create new one.
     * Example of usage:
     * ```php
     * // Find a user by specified user name. If not found, create new object but not save into DB.
     * $user = User::findSetAttr(['username' => 'admin', 'password'='dkwoei298', 'email' => 'user@example.com'], 'username');
     * // Find a user by specified user name. If not found, create new object and save into DB.
     * $user = User::findSetAttr(['username' => 'admin', 'password'='dkwoei298', 'email' => 'user@example.com'], 'username', TRUE);
     * ```
     * @param array $attributes Attributes used to find and set value (type of key=>value).
     * @param string|string[] $keys Keys used to find.
     * @param boolean $saveDb Save record into DB or not incase create new.
     * @param string $className Class name of the model to be found in. If null, then caller class is used.
     * @return \batsg\models\BaseModel
     */
    public static function findSetAttr($attributes, $keys, $saveDb = FALSE, $className = NULL)
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $condition = [];
        foreach ($keys as $key) {
            $condition[$key] = $attributes[$key];
        }

        $model = self::findOneCreateNew($condition, FALSE, $className);
        foreach ($attributes as $key => $value) {
            $model->$key = $value;
        }
        if ($saveDb) {
            self::saveThrowErrorModel($model);
        }
        return $model;
    }

    /**
     * Find all objects by specified attributes condition, and set another attributes' value.
     * If not exist, create new one.
     * Example of usage:
     * ```php
     * // Find a user by specified user name. If not found, create new object but not save into DB.
     * $user = User::findAllSetAttr(['username' => 'admin', 'password'='dkwoei298', 'email' => 'user@example.com'], 'username');
     * // Find a user by specified user name. If not found, create new object and save into DB.
     * $user = User::findAllSetAttr(['username' => 'admin', 'password'='dkwoei298', 'email' => 'user@example.com'], 'username', TRUE);
     * ```
     * @param array $attributes Attributes used to find and set value (type of key=>value).
     * @param string|string[] $keys Keys used to find.
     * @param boolean $saveDb Save record into DB or not incase create new.
     * @param string $className Class name of the model to be found in. If null, then caller class is used.
     * @return \batsg\models\BaseModel[]
     */
    public static function findAllSetAttr($attributes, $keys, $saveDb = FALSE, $className = NULL)
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $condition = array_intersect_key($attributes, array_flip($keys));

        $models = self::findAllCreateNew($condition, FALSE, $className);
        foreach ($models as $model) {
            foreach ($attributes as $key => $value) {
                $model->$key = $value;
            }
            if ($saveDb) {
                self::saveThrowErrorModel($model);
            }
        }
        return $models;
    }

    /**
     * Find existing object by specified attributes condition.
     * @param array  $condition (type of key=>value).
     * @param string $className Class name of the model to be found in. If null, then caller class is used.
     * @return \batsg\models\BaseModel
     * @throw Exception if not found.
     */
    public static function findOneExisting($condition, $className = NULL)
    {
        if (!$className) {
            $className = static::className();
        }
        $result = $className::findOne($condition);
        if (!$result) {
            throw new Exception("$className not found with condition " . print_r($condition));
        }
        return $result;

    }

  /**
   * Compare this model and other model by specified $field.
   * @param BaseModel $other
   * @param string $field
   * @param integer $direction
   * @return int If $direction = 1, return -1 if this model is "smaller", 0 if two are equal or 1 if this model is "larger".
   *             If $direction = -1, the result is inversed.
   */
  public function cmp(BaseModel $other, $fields, $direction = 1)
  {
      if (!is_array($fields)) {
          $fields = array($fields);
        }
        $result = 0;
        foreach ($fields as $field) {
          if ($this->$field != $other->$field) {
                $result = $this->$field < $other->$field ? -1 : 1;
                break;
            }
        }
    return $result * $direction;
  }

  /**
   * Set model attribute by array elements, by same attribute/key name.
   * @param array $attributeNames Array define pair of model attribute and array key.
   *                    If attribute and key are same, then it may be defined as string element,
   *                    else it should be defined as $arrayKey => $modelAttribute pair.
   * @param string|string[] $attributeNames
   */
  public function setAttributeFromArray(&$attr, $attributeNames)
  {
      if (!is_array($attributeNames)) {
          $attributeNames = [$attributeNames];
      }
      // $attributeNames' elements maybe define in two types: As an normal array element or an assoc element.
      // Example: $attributeNames = ['modelAttribute1', 'arrayAttribute' => 'modelAttribute2']
      // In case 1, it means $model->modelAttribute1 = $attr['modelAttribute1'];
      // In case 2, it means $model->modelAttribute2 = $attr['arrayAttribute'];
      foreach ($attributeNames as $key => $value) {
          $attrKey = is_numeric($key) ? $value : $key;
          if (isset($attr[$attrKey])) {
              $this->$value = $attr[$attrKey];
          }
      }
  }

  /**
   * Join speicifed fields' value.
   * @param string $fields
   * @param string $join
   * @param boolean $ignoreEmpty If TRUE, then field's value is used if it is set.
   * @return string
   */
  public function joinFieldValues($fields, $join = ' ', $ignoreEmpty = TRUE)
  {
      $values = [];
      foreach ($fields as $field) {
          if (!$ignoreEmpty || $this->$field) {
              $values[] = $this->$field;
          }
      }
      return join($join, $values);
  }

  /**
   * Get all errors on this model.
   * @param string $attribute attribute name. Use null to retrieve errors for all attributes.
   * @return array errors for all attributes or the specified attribute. Empty array is returned if no error.
   */
  public function getErrorMessages($attribute = NULL)
  {
      return self::getErrorMessagesModel($this, $attribute);
  }

  /**
   * Log error of this model.
   * @param string $message The message to be exported first.
   * @param string $category
   */
  public function logError($message = NULL, $category = 'application')
  {
      self::logErrorModel($this, $message, $category);
  }

  /**
   * Save this model, write error to log if error occurs.
   * @param string $errorMessage
   * @return boolean
   */
  public function saveLogError($errorMessage = NULL)
  {
      return self::saveLogErrorModel($this, $errorMessage);
  }

  /**
   * Save this model, write error to log and throw exception if error occurs.
   * @param string $errorMessage
   * @throws \Exception
   */
  public function saveThrowError($errorMessage = NULL)
  {
      return self::saveThrowErrorModel($this, $errorMessage);
  }

  /**
   * Create a string that describe all fields of this object.
   * @param mixed $fields String or string array. If NULL, all attributes are used.
   * @return string.
   */
  public function toString($fields = NULL)
  {
      return self::toStringModel($this, $fields);
  }

  /**
   * Get class short name of an object or FQDN classname.
   * @param string|object $anything
   * @return string
   */
  public static function classShortName($anything)
  {
      $shortName = null;
      if (is_object($anything)) {
          $shortName = (new \ReflectionClass($anything))->getShortName();
      } else {
          $shortName = substr(strrchr(__CLASS__, "\\"), 1);
      }
      return $shortName;
  }
}
