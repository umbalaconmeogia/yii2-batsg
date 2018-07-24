<?php
namespace batsg\helpers;

/**
 * Date and time manipulation.
 *
 * @author Tran Trung Thanh <umbalaconmeogia@gmail.com>
 */
class HTime
{
  /**
   * @var int
   */
  private $_hour = 0;

  /**
   * @var int
   */
  private $_minute = 0;

  /**
   * @var int
   */
  private $_second = 0;

  /**
   * @param string $time hh:mm:ss or hh:mm or hh
   */
  public function __construct($time = NULL)
  {
      if (!trim($time)) {
          $time = '0';
      }
      $timeArr = explode(':', $time);
      $timeArr = array_pad($timeArr, 3, 0);
      foreach ($timeArr as $element) {
          if (!is_numeric($element)) {
              throw new \Exception('Invalid time string.');
          }
      }
      $this->_hour = $timeArr[0];
      $this->_minute = $timeArr[1];
      $this->_second = $timeArr[2];
  }

  /**
   * Convert time to second.
   * @return number
   */
  public function toSenconds()
  {
      return $this->_hour * 3600 + $this->_minute * 60 + $this->_second;
  }

  /**
   * Reset data by number of seconds.
   * @param integer $seconds
   */
  public function resetSenconds($seconds)
  {
      $this->_second = $seconds % 60;
      $minutes = $seconds / 60;
      $this->_minute = $minutes % 60;
      $this->_hour = $minutes / 60;

      return $this;
  }

  /**
   * Add a time into current time.
   * @param string|HTime $time
   */
  public function add($time, $modify = FALSE)
  {
      if (!($time instanceof HTime)) {
          $time = new HTime($time);
      }
      return $this->addSeconds($time->toSenconds(), $modify);
  }

  public function subtract($time, $modify = FALSE)
  {
      if (!($time instanceof HTime)) {
          $time = new HTime($time);
      }
      return $this->addSeconds(-$time->toSenconds(), $modify);
  }

  /**
   * @param integer $seconds
   * @param boolean $modify
   * @return \batsg\helpers\HTime
   */
  private function addSeconds($seconds, $modify)
  {
      $seconds += $this->toSenconds();
      if ($modify) {
          $htime = $this;
      } else {
          $htime = new HTime();
      }
      return $htime->resetSenconds($seconds);
  }

  /**
   * Get hour value
   * @return int
   */
  public function getHour() {
    return $this->_hour;
  }

  /**
   * Get minute value
   * @return int
   */
  public function getMinute() {
    return $this->_minute;
  }

  /**
   * Get second value
   * @return int
   */
  public function getSecond()
  {
    return $this->_second;
  }

  /**
   * @param string $format The format string as used in date().
   * @return string
   */
  public function __toString()
  {
    return $this->toString();
  }

  /**
   * @param string $format The format string as used in date().
   * @return string
   */
  public function toString($format = "%02d:%02d")
  {
      return sprintf($format, $this->_hour, $this->_minute, $this->_second);
  }
}
?>