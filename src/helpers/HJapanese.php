<?php
namespace batsg\helpers;

class HJapanese
{
  const DATETIME_YEAR = '年';
  const DATETIME_MONTH = '月';
  const DATETIME_DAY = '日';
  const DATETIME_HOUR = '時';
  const DATETIME_MINUTE = '分';
  const DATETIME_SECOND = '秒';

  /**
   * @var array Name of week days (as value of date('w'): 0 for Sunday, 1 for Monday, etc).
   */
  public static $weekDays = array('日', '月', '火', '水', '木', '金', '土');

  public static $halfWidthDigits = array(
    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
  );
  public static $fullWidthDigits = array(
    '０', '１', '２', '３', '４', '５', '６', '７', '８', '９'
  );

  public static $fullWidthDigitsToHalfWidth = array(
    '０' => 0, '１' => 1, '２' => 2, '３' => 3, '４' => 4, '５' => 5, '６' => 6, '７' => 7, '８' => 8, '９' => 9
  );

  /**
   * Replace all full width digits by half with digits in a string.
   * @param mixed $subject a string or array of string to be searched and replaced.
   * @return mixed This function return a string or an array with the replaced values.
   */
  public static function replaceFullWidthDigits($subject)
  {
    $subject = str_replace(self::$fullWidthDigits, self::$halfWidthDigits, $subject);
    return $subject;
  }

  /**
   * Replace all half width digits by full with digits in a string.
   * @param mixed $subject a string or array of string to be searched and replaced.
   * @return mixed This function return a string or an array with the replaced values.
   */
  public static function replaceHalfWidthDigits($subject)
  {
    $subject = str_replace(self::$halfWidthDigits, self::$fullWidthDigits, $subject);
    return $subject;
  }

  /**
   * Parse date time from a string.
   * @param string $dateTime String that contain 年, 月, 日, 時, 分, 秒
   * @param boolean $hasFullWidthDigits TRUE if this string contains full width digits
   *                so it should be converted to half with digits.
   * @return HDateTime
   */
  public static function parseDateTime($dateTime, $hasFullWidthDigits = TRUE)
  {
    $patterns = array(
        'year'   => '/(\d+)年/',
        'month'  => '/(\d+)月/',
        'day'    => '/(\d+)日/',
        'hour'   => '/(\d+)時/',
        'minute' => '/(\d+)分/',
        'second' => '/(\d+)秒/',
    );
    // Convert full width digits.
    if ($hasFullWidthDigits) {
      $dateTime = self::replaceFullWidthDigits($dateTime);
    }
    // Initiate the data.
    $now = HDateTime::now();
    $elements = array(
        'year'   => $now->getYear(),
        'month'  => $now->getMonth(),
        'day'    => $now->getDay(),
        'hour'   => 0,
        'minute' => 0,
        'second' => 0,
    );
    // Grep
    foreach ($patterns as $name => $pattern) {
      if (preg_match($pattern, $dateTime, $match)) {
        $elements[$name] = $match[1];
      }
    }
    return HDateTime::createFromYmdHms(
        $elements['year'],
        $elements['month'],
        $elements['day'],
        $elements['hour'],
        $elements['minute'],
        $elements['second']
    );
  }

  /**
   * Convert string from SJIS encoding to UTF8.
   * @param string $s
   * @return string
   */
  public static function sjisToUtf8($s)
  {
    return mb_convert_encoding($s, 'UTF-8', 'SJIS');
  }

  /**
   * Convert string from UTF8 encoding to SJIS.
   * @param string $s
   * @return string
   */
  public static function utf8ToSjis($s)
  {
    return mb_convert_encoding($s, 'SJIS', 'UTF-8');
  }

  /**
   * Get the Japanese era calendar year of a specified year.
   * For example, from 1980 to 昭和 and 55.
   * Currently, this is not sure to work with the year before 1970.
   *
   * @param HDateTime $dateTime
   * @param string $eraName
   * @param int $yearNumber
   * @return string String of era name and year number, of NULL if cannot calculate.
   */
  public static function getJapaneseYear($dateTime, &$eraName, &$yearNumber)
  {
    $calendarPoints = array (
      array("1989/01/08", "平成"),
      array("1926/12/25", "昭和"),
      array("1912/07/30", "大正"),
      array("1868/01/01", "明治"),
    );
    //年月日を文字列として結合
    $ymd = $dateTime->toString('Y/m/d');
    foreach ($calendarPoints as $calendarPoint) {
      if ($calendarPoint[0] <= $ymd) {
        $point = HDateTime::createFromString($calendarPoint[0]);
        $eraName = $calendarPoint[1];
        $yearNumber = $dateTime->getYear() - $point->getYear() + 1;
        return "{$eraName}{$yearNumber}年";
      }
    }
    return NULL;
  }

  /**
   * 和暦変換用の関数: 平成xx年yy月zz日
   * @param mixed $dateTime string or HDateTime
   * @param string $monthDayFormat the format string for month and day. For example "m月d日" or "n月j日"
   * @param string $weekDayFormat "(w)" or "w曜日"
   * @return string
   */
  public static function toJapaneseCalendar($dateTime, $monthDayFormat = 'm月d日', $weekDayFormat = NULL)
  {
    $result = NULL;

    if (!$dateTime instanceof HDateTime) {
      $dateTime = HDateTime::createFromString($dateTime);
    }
    $japaneseYear = self::getJapaneseYear($dateTime, $eraName, $yearNumber);
    if ($japaneseYear) {
      $result = $japaneseYear . $dateTime->toString($monthDayFormat);
      if ($weekDayFormat) {
        $result .= str_replace('w', self::$weekDays[$dateTime->getWDay()], $weekDayFormat);
      }
    } else {
      $result = $dateTime->toString('Y/m/d');
    }
    return $result;
  }

  public static function japaneseWeekDay($dateTime)
  {
    if (!$dateTime instanceof HDateTime) {
      $dateTime = HDateTime::createFromString($dateTime);
    }
    $result = self::$weekDays[$dateTime->getWDay()];
    return $result;
  }

  /**
   * Split a string to array.
   * This is like str_split, but work with UTF8 string.
   * @param string $str
   * @param int $split_length
   */
  public static function mb_str_split($str, $split_length = 1)
  {
    if ($split_length < 1) return FALSE;
    $result = array();
    for ($i = 0; $i < mb_strlen($str); $i += $split_length) {
      $result[] = mb_substr($str, $i, $split_length);
    }
    return $result;
  }

  /**
   * Implementation of mb_str_replace based on the code of sakai at d4k dot net.
   * (http://php.net/manual/ja/ref.mbstring.php)
   */
  public static function mb_str_replace($search, $replace, $subject) {
    if(is_array($subject)) {
      $ret = array();
      foreach($subject as $key => $val) {
        $ret[$key] = mb_str_replace($search, $replace, $val);
      }
      return $ret;
    }

    foreach((array) $search as $key => $s) {
      if($s == '') {
        continue;
      }
      $r = !is_array($replace) ? $replace : (array_key_exists($key, $replace) ? $replace[$key] : '');
      $pos = mb_strpos($subject, $s);
      while($pos !== false) {
        $subject = mb_substr($subject, 0, $pos) . $r . mb_substr($subject, $pos + mb_strlen($s));
        $pos = mb_strpos($subject, $s, $pos + mb_strlen($r));
      }
    }

    return $subject;
  }

  /**
   * Check if a string contains Hiragana.
   * @see http://pentan.info/php/reg/is_hira.html
   * @param string $str
   * @return int 0 or 1
   */
  public static function containHiragana($str)
  {
    return preg_match("/[ぁ-ゞ]+/u", $str);
  }

  /**
   * Check if a string contains Katakana.
   * @see http://pentan.info/php/reg/is_kana.html
   * @param string $str
   * @return int 0 or 1
   */
  public static function containKatakana($str)
  {
    return preg_match("/[ァ-ヾ]+/u", $str);
  }

  /**
   * Check if a string contains only Hiragana.
   * @see http://pentan.info/php/reg/is_hira.html
   * @param string $str
   * @return int 0 or 1
   */
  public static function onlyHiragana($str)
  {
      return preg_match("/^[ぁ-ゞ]+$/u", $str);
  }
  
  /**
   * Check if a string contains only Katakana.
   * @see http://pentan.info/php/reg/is_kana.html
   * @param string $str
   * @return int 0 or 1
   */
  public static function onlyKatakana($str)
  {
      return preg_match("/^[ァ-ヾ]+$/u", $str);
  }
 
  /**
   * Convert string from Hiragana to Kanatakan.
   * @param string $str
   * @return string
   */
  public static function hiraganaToKatakana($str)
  {
    return mb_convert_kana($str, 'C');
  }

  /**
   * Convert string from half width to full width.
   * @param string $str
   * @return string
   */
  public static function halfWidthToFullWidth($str)
  {
    return mb_convert_kana($str, 'AK');
  }

  /**
   * Convert string from half width to full width.
   * @param string $str
   * @return string
   */
  public static function fullWidthToHalfWidth($str)
  {
    return mb_convert_kana($str, 'ak');
  }

  /**
   * Convert string from Katakana to Hiragana.
   * @param string $str
   * @return string
   */
  public static function katakanaToHiragana($str)
  {
    return mb_convert_kana($str, 'c');
  }
  
  /**
   * Call to mb_str_len with UTF8 encoding.
   * @param string $str
   */
  public static function strlen($str)
  {
    return mb_strlen($str, 'UTF8');
  }
}
?>