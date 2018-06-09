<?php
namespace batsg\helpers;

/**
 * Example of usage:
 * <pre>
 *   $constantValues = [];
 *   CsvWithHeader::read(\Yii::$aliases('@app/config/constantValue.csv'), function($csv) {
 *       while ($csv->loadRow() !== FALSE) {
 *           // Get attributes as an array.
 *           $attr = $csv->getRowAsAttributes();
 *           // Create a model from attributes array.
 *           $constantValues[] = new ConstantValue($attr);
 *       }
 *   });
 * </pre>
 * @author thanh
 */
class CsvWithHeader
{
    private $csvFile;

    private $handle;

    private $header;

    private $row;

    private $rowAsAttributes;

    /**
     * Read a CSV file with header.
     * <p />
     * This will open the CSV file, and load header from the first row.
     * <p />
     * @param string $csvFile
     * @param function $callback Call back function that receives CsvWithHeader as parameter.
     */
    public static function read($csvFile, $callback)
    {
        $csvWithHeader = new CsvWithHeader();
        $csvWithHeader->fopen($csvFile);
        $csvWithHeader->loadHeader();
        call_user_func($callback, $csvWithHeader);
        $csvWithHeader->fclose();
    }

    /**
     * Open an CSV file.
     * @param string $csvFile
     * @param string $mode See fopen()
     */
    public function fopen($csvFile, $mode = 'r')
    {
        \Yii::trace("fopen($csvFile)");
        $this->csvFile = $csvFile;
        $this->handle = fopen($csvFile, $mode);
    }

    /**
     * Load and ignore several rows.
     * @param integer $rowNum
     */
    public function skipRow($rowNum = 1)
    {
        for ($i = 0; $i < $rowNum; $i++) {
            $data = fgetcsv($this->handle);
        }
    }

    /**
     * Load a CSV row.
     * <p />
     * The loaded data can be accessed via getRow() or getRowAsAttributes().
     *
     * @return array
     */
    public function loadRow()
    {
        $this->row = fgetcsv($this->handle);
        $this->rowAsAttributes = NULL;
        return $this->row;
    }

    /**
     * Get the loaded row.
     * @return array
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Get the loaded row as associated array, with keys defied by header
     *
     * @return array
     */
    public function getRowAsAttributes()
    {
        // Parse $this->row to $this->rowAsAttributes if it is not parsed.
        if (!$this->rowAsAttributes) {
            $this->rowAsAttributes = []; // Initiate array.
            // Use $header element value as key, to set $rowAsAttributes' value.
            foreach ($this->header as $index => $attribute) {
                if (isset($this->row[$index])) {
                    $this->rowAsAttributes[$attribute] = $this->row[$index];
                }
            }
        }
        // Return $rowAsAttributes
        return $this->rowAsAttributes;
    }

    public function loadHeader()
    {
        $this->header = $this->loadRow();
    }

    public function fclose()
    {
        fclose($this->handle);
        \Yii::trace("fclose({$this->csvFile})");
    }
}