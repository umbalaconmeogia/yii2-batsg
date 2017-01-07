<?php
namespace batsg;

use Yii;

class Y
{
    /**
     * Key parameter of setFlash() call for success.
     * @var string
     */
    static $flashKeySuccess = 'success';

    /**
     * Key parameter of setFlash() call for info.
     * @var string
     */
    static $flashKeyInfo = 'info';

    /**
     * Key parameter of setFlash() call for warning.
     * @var string
     */
    static $flashKeyWarning = 'warning';

    /**
     * Key parameter of setFlash() call for error.
     * @var string
     */
    static $flashKeyError = 'danger';

    /**
     *
     * @var string default category to be used in Yii::t().
     */
    public static $i18nDefaultCategory = 'app';

    /**
     * Call Yii::t().
     *
     * @param string $mesage
     * @param string $category
     *            use self::$i18nDefaultCategory if not specified.
     * @param array $params
     * @return string
     */
    public static function t($message, $params = [], $category = NULL)
    {
        if ($category === NULL) {
            $category = self::$i18nDefaultCategory;
        }
        return Yii::t($category, $message, $params);
    }

    /**
     * Display a translate text by echo.
     *
     * @param string $mesage
     * @param string $category
     *            use self::$i18nDefaultCategory if not specified.
     * @param array $params
     */
    public static function et($message, $params = [], $category = NULL)
    {
        if ($category === NULL) {
            $category = self::$i18nDefaultCategory;
        }
        echo Yii::t($category, $message, $params);
    }

    /**
     * Set a flash message with the key "error".
     *
     * @param string $message
     *
     */
    public static function setFlashError($message)
    {
        Yii::$app->session->setFlash(self::$flashKeyError, $message);
    }

    /**
     * Set a flash message with the key "error".
     * The message is translated by Y::t().
     *
     * @param string $message
     * @param array $params
     *
     */
    public static function setFlashErrorT($message, $params = [])
    {
        self::setFlashError(self::t($message, $params));
    }

    /**
     * Set a flash message with the key "warning".
     *
     * @param string $message
     *
     */
    public static function setFlashWarning($message)
    {
        Yii::$app->session->setFlash(self::$flashKeyWarning, $message);
    }

    /**
     * Set a flash message with the key "warning".
     * The message is translated by Y::t().
     *
     * @param string $message
     * @param array $params
     *
     */
    public static function setFlashWarningT($message, $params = [])
    {
        self::setFlashWarning(self::t($message, $params));
    }

    /**
     * Set a flash message with the key "info".
     *
     * @param string $message
     *
     */
    public static function setFlashInfo($message)
    {
        Yii::$app->session->setFlash(self::$flashKeyWarning, $message);
    }

    /**
     * Set a flash message with the key "info".
     * The message is translated by Y::t().
     *
     * @param string $message
     * @param array $params
     *
     */
    public static function setFlashInfoT($message, $params = [])
    {
        self::setFlashInfo(self::t($message, $params));
    }

    /**
     * Set a flash message with the key "success".
     *
     * @param string $message
     *
     */
    public static function setFlashSuccess($message)
    {
        Yii::$app->session->setFlash(self::$flashKeySuccess, $message);
    }

    /**
     * Set a flash message with the key "success".
     * The message is translated by Y::t().
     *
     * @param string $message
     * @param array $params
     *
     */
    public static function setFlashSuccessT($message, $params = [])
    {
        self::setFlashSuccess(self::t($message, $params));
    }
}