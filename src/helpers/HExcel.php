<?php
namespace batsg\helpers;

class HExcel
{
  /**
   * Convert an integer to a string of uppercase letters (A-Z, AA-ZZ, AAA-ZZZ, etc.)
   * @param int $n The column number.
   * @param int startIndex from 0 or 1
   * @param string
   */
  public static function columnNumberToAlphabet($n, $startIndex = 0)
  {
    $n -= $startIndex;
    for($r = ''; $n >= 0; $n = intval($n / 26) - 1) {
      $r = chr($n % 26 + 0x41) . $r;
    }
    return $r;
  }

  /**
   * Convert a string of uppercase letters to an integer.
   * @param string $a
   * @param int startIndex from 0 or 1
   * @return int
   */
  public static function columnAlphabetToNumber($a, $startIndex = 0)
  {
    $a = strtoupper($a);
    $l = strlen($a);
    $n = 0;
    for($i = 0; $i < $l; $i++)
    $n = $n*26 + ord($a[$i]) - 0x40;
    return $n - 1 + $startIndex;
  }

  /**
   * Get value of a column (specified by name) on a row data.
   * @param array $rowValues
   * @param string $colName
   * @param integer startIndex from 0 or 1
   * @return mixed
   */
  public static function colValue(&$row, $colName, $startIndex = 0)
  {
    return $row[self::columnAlphabetToNumber($colName, $startIndex)];
  }

  /**
   * Get value of a column (specified by name) on a row data as number.
   * @param array $rowValues
   * @param string $colName
   * @param integer startIndex from 0 or 1
   * @return number
   */
  public static function colValueNum(&$row, $colName, $startIndex = 0)
  {
    $value = $row[self::columnAlphabetToNumber($colName, $startIndex)];
    return $value ? $value : 0;
  }

  /**
   * Convert Excel datetime to PHP timestamp.
   * @param float $xlDate Excel date time.
   * @return int
   */
  public static function xl2timestamp($xlDate)
  {
    $timestamp = round(($xlDate - 25569) * 86400);
    return $timestamp;
  }
}
?>
