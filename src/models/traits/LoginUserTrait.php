<?php
namespace batsg\models\traits;

use Yii;

/**
 * @property string $password_encryption
 * @property string $password
 * @property string $plainPassword
 */
trait LoginUserTrait
{
    public $plainPassword;

    /**
     * Finds an identity by the given ID.
     *
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return self::findOne(['username' => $username]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Store the hashed password.
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->plainPassword = $password;
        $this->password_encryption = \Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Check if a password is correct comparing to saved one.
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_encryption);
    }

    /**
     * Generate auth_key for new record.
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::beforeSave()
     */
    public function beforeSave($insert)
    {
        \Yii::trace('LoginUserTrait#beforeSave()');
        $result = parent::beforeSave($insert);
        if ($result && $this->isNewRecord) {
            // Set auth_key
            $this->auth_key = \Yii::$app->security->generateRandomString();
        }
        return $result;
    }
}
