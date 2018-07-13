<?php
namespace batsg\models\attrEncrypt;

class EncryptAttributeInfo
{
    public static $sessinoKeyActiveRecordEncryptionKeys = 'SESSION_KEY_ACTIVE_RECORD_ENCRYPTION_KEYS_4xy3';

    const TYPE_STRING = 0;

    const TYPE_INT = 1;

    const TYPE_FLOAT = 2;

    const TYPE_DATE = 3;

    /**
     * Attribute that holds encryption data (usually is a DB field).
     * @var string
     */
    public $encryptionAttribute;

    public $dataType = self::TYPE_STRING;

    public $keyLevel;

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
     * Set keys (passwords) to encrypt attribue's data.
     * This will save the keys into session.
     * Sub classes may override this function to change the way of setting and storing keys.
     * @param string|string[] $values
     */
    public function setAttributeEncryptionKeys($values)
    {
        // Assure that $values is an array.
        if (!is_array($values)) {
            $values = [$values];
        }
        // Get current value from session.
        $session = \Yii::$app->session->get(self::$sessinoKeyActiveRecordEncryptionKeys);
        if (!$session) {
            $session = [];
        }
        // Set password into session values.
        foreach ($values as $key => $value) {
            $session[$key] = $value;

        }
        // Save to session.
        Yii::$app->session->set(self::$sessinoKeyActiveRecordEncryptionKeys, $session);
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
}