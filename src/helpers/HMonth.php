<?php
namespace batsg\helpers;

/**
 * Manipulate string yyyy-mm as month data.
 *
 * @author Tran Trung Thanh <umbalaconmeogia@gmail.com>
 */
class HMonth extends HDateTime
{
  /**
   * @param string $format The format string as used in date().
   * @return string
   */
  public function toString($format = 'Y-m')
  {
    return parent::toString($format);
  }
}
?>