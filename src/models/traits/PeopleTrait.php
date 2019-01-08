<?php
namespace batsg\models\traits;

use yii\helpers\ArrayHelper;

/**
 * Some functions that relates to a Person, such as
 * gender, age.
 * At this time, the yii2-batsg library does not implement i18n, so you should
 * config i18n source by yourself.
 * For example, for yii2-advanced-template, add the following part into main.php's components
 * <pre>
 * 'i18n' => [
 *     'translations' => [
 *         'app' => [
 *             'class' => 'yii\i18n\PhpMessageSource',
 *             'basePath' => '@common/messages',
 *             //'sourceLanguage' => 'en-US',
 *             'fileMap' => [
 *                 'app' => 'app.php',
 *                 'app/error' => 'error.php',
 *             ],
 *         ],
 *     ],
 * ],
 * </pre>
 * Then in "common" directory, create the file messages/ja-JP/app.php,
 * define the translation in this file.
 *
 * @property string $genderStr
 * @property int $age
 */
trait PeopleTrait
{
    // Trait can not have constant, so use static.
    static $GENDER_MALE = 1;
    static $GENDER_FEMALE = 2;

    public static function genderOptionArr()
    {
        return [
            self::$GENDER_MALE => \Yii::t('app', 'Male'),
            self::$GENDER_FEMALE => \Yii::t('app', 'Female'),
        ];
    }

    /**
     * Assume that the class has property "gender".
     * @return mixed|array|object
     */
    public function getGenderStr()
    {
        $options = self::genderOptionArr();
        return ArrayHelper::getValue($options, $this->gender, $options[self::$GENDER_MALE]);
    }

    private $_age = NULL;

    /**
     * Assume that the class has property "birth_date"
     * @return int
     */
    public function getAge()
    {
        if ($this->_age === NULL) {
            $this->_age = date_diff(date_create($this->birth_date), date_create('today'))->y;

        }
        return $this->_age;
    }
}