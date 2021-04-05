<?php
namespace batsg\helpers;

/**
 * This class help dealing with CSV file that has the first row as data header.
 *
 * By using CsvWithHeader, we can access to CSV element data via col name defined in header row.
 *
 * Example of usage:
 * ```php
 *   CsvWithHeader::read(\Yii::$aliases('@data/employee.csv'), function($csv) {
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
 */
class CsvWithHeader
{
    public $csvEscape = '\\';

    public $csvDelimiter = ',';

    public $csvEnclosure = '"';

    private $csvFile;

    private $handle;

    private $header;

    private $row;

    /**
     * @var array Mapping between attribute to value.
     */
    private $rowAsAttributes;

    /**
     * @var array Mapping between attribute to index on header.
     */
    private $attributeIndexes;

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
     * @param string|resource $csvFile A CSV file name, or handle of opened file.
     * @param string $mode See fopen()
     */
    public function fopen($csvFile, $mode = 'r')
    {
        if (is_string($csvFile)) {
            \Yii::trace("fopen($csvFile)");
            $this->csvFile = $csvFile;
            $this->handle = fopen($csvFile, $mode);
        } else {
            $this->handle = $csvFile;
        }

        $this->ignoreBomCharacters();
    }

    /**
     * Read over BOM characters at header of file if exists.
     */
    private function ignoreBomCharacters()
    {
        // BOM as a string for comparison.
        $bom = "\xef\xbb\xbf";
        // Progress file pointer and get first 3 characters to compare to the BOM string.
        if (fgets($this->handle, 4) !== $bom) {
            // BOM not found - rewind pointer to start of file.
            rewind($this->handle);
        }
    }

    /**
     * Load and ignore several rows.
     * @param integer $rowNum
     */
    public function skipRow($rowNum = 1)
    {
        for ($i = 0; $i < $rowNum; $i++) {
            $this->loadRow();
        }
    }

    /**
     * Load a CSV row.
     * <p />
     * The loaded data can be accessed via getRow() or getRowAsAttributes().
     *
     * @param boolean $trim Trim value or not.
     * @return array
     */
    public function loadRow($trim = TRUE)
    {
        $this->row = fgetcsv($this->handle, 0, $this->csvDelimiter, $this->csvEnclosure, $this->csvEscape);
        if ($trim && $this->row) {
            foreach ($this->row as $key => $value) {
                $this->row[$key] = trim($value);
            }
        }
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

    /**
     * Load header row (remember associated key).
     * @param boolean $trim Trim value or not.
     */
    public function loadHeader($trim = TRUE)
    {
        $this->header = $this->loadRow($trim);

        // Set attribute index.
        $this->attributeIndexes = [];
        foreach ($this->header as $index => $attribute) {
            $this->attributeIndexes[$attribute] = $index;
        }
    }

    public function fclose()
    {
        fclose($this->handle);
        \Yii::trace("fclose({$this->csvFile})");
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     */
    public function setRowAttribute($attribute, $value)
    {
        $this->getRowAsAttributes();
        $this->rowAsAttributes[$attribute] = $value;
        $this->row[$this->attributeIndexes[$attribute]] = $value;
    }
}