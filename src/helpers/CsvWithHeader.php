<?php
namespace batsg\helpers;

/**
 * This class help dealing with CSV file that has the first row as data header.
 *
 * By using CsvWithHeader, we can access to CSV element data via col name defined in header row.
 *
 * Example of usage:
 * ```php
 *   CsvWithHeader::read('employee.csv', function($csv) {
 *       while ($csv->loadRow() !== FALSE) {
 *           // Get attributes as an array.
 *           $attr = $csv->getRowAsAttributes();
 *           // Display "name" attribute of data row.
 *           echo 'Employee name: ' . $attr['name'] . "\n";
 *       }
 *   });
 * ```
 *
 * If you have CSV file content in memory (for example, file content is post from form), use CsvWithHeader as below.
 * ```php
 *   $stream = fopen('php://memory', 'r+');
 *   fwrite($stream, $csvText);
 *   rewind($stream);
 *   CsvWithHeader::read($stream, function($csv) {
 *       while ($csv->loadRow() !== FALSE) {
 *           // Get attributes as an array.
 *           $attr = $csv->getRowAsAttributes();
 *           // Display "name" attribute of data row.
 *           echo 'Employee name: ' . $attr['name'] . "\n";
 *       }
 *   });
 * ```
 * @author thanh
 * @deprecated Use \umbalaconmeogia\phputil\data\CsvWithHeader instead.
 */
class CsvWithHeader extends \umbalaconmeogia\phputil\data\CsvWithHeader {}