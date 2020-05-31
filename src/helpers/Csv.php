<?php
namespace batsg\helpers;

class Csv
{
    /**
     * Save array to csv file.
     * @param array $array
     * @param string $csv
     */
    public static function arrayToCsv(&$array, $csv)
    {
        $handle = fopen($csv, 'w');
        foreach ($array as $row)
        {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }
}