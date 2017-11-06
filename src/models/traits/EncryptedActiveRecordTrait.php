<?php
namespace batsg\models\traits;

use \Yii;
use yii\db\ActiveRecord;

/**
 * Encrypt data saved in database.
 *
 * Model that uses this trait should override its __get() and __set() function as below.
 * <pre>
 * public function __get($name)
 * {
 *   return $this->getterEncryptedField($name);
 * }
 *
 * public function __set($name, $value)
 * {
 *   return $this->setterEncryptedField($name, $value);
 * }
 * </pre>
 * @property string[] attributeEncryptionKeys
 */
trait EncryptedActiveRecordTrait
{
    public static $sessinoKeyActiveRecordEncryptionKeys = 'SESSION_KEY_ACTIVE_RECORD_ENCRYPTION_KEYS_4xy3';

    private $_attributesEncrypted = [];

    /**
     * Set attribute encryption keys for ALL models.
     * This method is usually called when user inputs password.
     * @param string|string[] $keys
     */
    public static function setAttributeEncryptionKeyForModels($keys)
    {
        $model = new self();
        $model->setAttributeEncryptionKeys($keys);
    }

    /**
     * Get keys (passwords) to encrypt attribue's data.
     * This gets the keys from session.
     * Sub classes may override this function to change the way of retrieving keys.
     * @return string[]
     */
    public function getAttributeEncryptionKeys()
    {
        $value = \Yii::$app->session->get(self::$sessinoKeyActiveRecordEncryptionKeys);
        if (!$value) {
            $value = ['password123xyz'];
            $this->setAttributeEncryptionKeys($value);
        }
        return $value;
    }

    /**
     * Set keys (passwords) to encrypt attribue's data.
     * This will save the keys into session.
     * Sub classes may override this function to change the way of setting and storing keys.
     * @param string|string[] $values
     */
    public function setAttributeEncryptionKeys($values)
    {
        if (!is_array($values)) {
            $values = [$values];
        }
        Yii::$app->session->set(self::$sessinoKeyActiveRecordEncryptionKeys, $values);
    }

    /**
     * @param string $name
     */
    protected function getterEncryptedField($name)
    {
        if (isset($this->encryptedAttributeDbFields[$name])) {
            return $this->__getEncryptedField($name);
        } else {
            return parent::__get($name);
        }
    }

    /**
     * @param string $name
     * @param string $value
     */
    protected function setterEncryptedField($name, $value)
    {
        if (isset($this->encryptedAttributeDbFields[$name])) {
            $this->__setEncryptedField($name, $value);
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @param string $name
     */
    private function __getEncryptedField($name)
    {
        if (!isset($this->_attributesEncrypted[$name])) {
            $dbField = $this->encryptedAttributeDbFields[$name];
            $this->_attributesEncrypted[$name] = $this->decryptFieldValue($this->$dbField);
        }
        return $this->_attributesEncrypted[$name];
    }

    /**
     * @param string $name
     * @param string $value
     */
    private function __setEncryptedField($name, $value)
    {
        // Remember value in $_attributeEncrypted to used in __setEncryptedField().
        $this->_attributesEncrypted[$name] = $value;
        // Get the DB field name of the attribute $name
        $dbField = $this->encryptedAttributeDbFields[$name];
        // Set the DB field value.
        $this->$dbField = $this->encryptFieldValue($value);
    }

    /**
     * Encrypt a value.
     * @param mixed $value
     * @return string
     */
    private function encryptFieldValue($value)
    {
        // Encrypt the value by keys.
        foreach ($this->attributeEncryptionKeys as $key) {
            $value = Yii::$app->getSecurity()->encryptByKey($value, $key);
        }
        // Base 64 encode the value so that it can be saved into DB without problem.
        $value = base64_encode($value);
        return $value;
    }

    /**
     * Decrypt a value.
     * @param string $value
     * @return mixed
     */
    private function decryptFieldValue($value)
    {
        // Base 64 decode the value.
        $value = base64_decode($value);
        // Decrypt the value by keys.
        foreach ($this->attributeEncryptionKeys as $key)
        {
            $value = Yii::$app->getSecurity()->decryptByKey($value, $key);
        }
        return $value;
    }
}