<?php
namespace batsg\models\traits;

use \Yii;

/**
 * Encrypt data saved in database.
 *
 */
trait EncryptedActiveRecordTrait
{
    /**
     * @var string|string[]
     */
    public static $attributeEncryptionKey = 'password';

    /**
     * @var string[]
     */
//     protected $encryptedAttributeDbFields = [];

    private $_attributesEncrypted = [];

    /**
     * Usage example: Class that uses this trait may implement
     * <pre>
     * public function __get($name)
     * {
     *   return $this->getterEncryptedField($name);
     * }
     * </pre>
     *
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
     */
    protected function __getEncryptedField($name)
    {
        if (!isset($this->_attributesEncrypted[$name])) {
            $dbField = $this->encryptedAttributeDbFields[$name];
            $this->_attributesEncrypted[$name] = Yii::$app->getSecurity()->decryptByKey(
                base64_decode($this->$dbField), self::$attributeEncryptionKey);
//             echo "$dbField = {$this->$dbField}\n";
        }
        return $this->_attributesEncrypted[$name];
    }

    /**
     * Usage example: Class that uses this trait may implement
     * <pre>
     * public function __set($name, $value)
     * {
     *   return $this->setterEncryptedField($name, $value);
     * }
     * </pre>
     *
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
     * @param string $value
     */
    protected function __setEncryptedField($name, $value)
    {
        $this->_attributesEncrypted[$name] = $value;
        $dbField = $this->encryptedAttributeDbFields[$name];
        $this->$dbField = base64_encode(Yii::$app->getSecurity()->encryptByKey($value, self::$attributeEncryptionKey));
    }
}