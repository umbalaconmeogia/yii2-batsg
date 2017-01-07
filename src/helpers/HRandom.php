<?php
namespace batsg\helpers;

/**
 * Class HRandom
 */
class HRandom
{
    /**
     * Default length of generated string.
     * @var integer
     */
    const DEFAULT_LENGTH = 32;

    /**
     * Default characters that is used to generate password.
     *
     * @var string
     */
    const CHARACTER_SET_ALPHABET_DIGIT = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    const CHARACTER_SET_ALPHABET_ONLY = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    const CHARACTER_SET_DIGIT_ONLY = '0123456789';

    /**
     * Generate a HRandom password with specified length.
     * E.g:
     * <code>
     * HRandom::generateRandomStringInCharacterSet(4);
     * </code>
     * will generage a HRandom string like 'z7a4'
     *
     * @param int $length
     *            the length of the password to be generated.
     * @param string $characterSet
     *            the string that contains characters to be used.
     * @return string The password
     */
    static public function generateRandomStringInCharacterSet($length = self::DEFAULT_LENGTH, $characterSet = self::CHARACTER_SET_ALPHABET_DIGIT)
    {
        $randMax = strlen($characterSet) - 1;
        $pass = '';
        for ($i = 0; $i < $length; $i ++) {
            $pass .= substr($characterSet, rand(0, $randMax), 1);
        }
        return $pass;
    }

    /**
     *
     * @param number $length
     *            The length of the string.
     * @param string $characterSet
     *            If specified, then only character in this string is used.
     * @param integer $characterCase
     *            If 0, character is case sensitive. If -1, all characters are converted to lower case. If 1, all characters are converted to upper case.
     */
    public static function generateRandomString($length = self::DEFAULT_LENGTH, $characterSet = NULL, $characterCase = 0)
    {
        // Generate random string.
        $randomString = $characterSet ?
            self::generateRandomStringInCharacterSet($length, $characterSet) :
            \Yii::$app->getSecurity()->generateRandomString($length);
        // Convert character case if specified.
        switch ($characterCase) {
            case - 1:
                $randomString = strtolower($randomString);
                break;
            case 1:
                $randomString = strtoupper($randomString);
                break;
        }
        return $randomString;
    }
}
?>