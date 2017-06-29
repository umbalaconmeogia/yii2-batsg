<?php
namespace batsg\helpers;

use \Yii;

/**
 * Utility function for backup and migration.
 */
class HBackup {

  /**
   * The marker at the first column of the CSV row to announce
   * that this is the start of a table (with the table name).
   * @var string
   */
  const TABLE_MARKER = '*';

  /**
   * For internal use in the class.
   * Index of element in a array that keeps the model object.
   * @var string
   */
  const IDX_MODEL = 'model';

  /**
   * For internal use in the class.
   * Index of element in a array that keeps the column index in an array.
   * @var string
   */
  const IDX_COLUMN_INDEX = 'columnIndex';

  /**
   * Export data of specified tables to csv file.
   *
   * @param string $outputFileName
   * @param string[] $modelClassNames Name of model classes to be backed up.
   *                  If NULL, then backup all tables.
   */
  public static function exportDbToCsv($outputFileName, $modelClassNames = NULL)
  {
    if ($modelClassNames === NULL) {
      $modelClassNames = self::getModelClassList();
    }
    if (!is_array($modelClassNames)) {
      $modelClassNames = [$modelClassNames];
    }
    // Open output file.
    $handle = fopen($outputFileName, 'w');

    // Export each table.
    foreach ($modelClassNames as $modelClassName) {
      // Write data.
      self::appendTableToCsv(self::fullQualifiedModelClassName($modelClassName), $handle);
    }

    // Close output file.
    fclose($handle);
  }

  /**
   * Get full qualified model class name.
   * @param string $className
   * @return string
   */
  public static function fullQualifiedModelClassName($className)
  {
      return (strpos($className, '\\') === FALSE) ? "app\\models\\$className" : $className; 
  }
  
  /**
   * Try to get all model classes in app/models
   * @return string[]
   */
  public static function getModelClassList()
  {
    $classList = [];

    $tableNames = Yii::$app->db->schema->tableNames;

    $searchStr = Yii::$app->basePath . '/models/*.php';
    foreach(glob($searchStr) as $filePath){
      $className = self::fullQualifiedModelClassName(HFile::fileFileName($filePath));

      // Check if table exist.
      if (is_subclass_of($className, 'yii\db\ActiveRecord') && method_exists($className, 'tableName')) {
        try {
          $tableName = $className::tableName();
        } catch (Exception $e) {
          $tableName = NULL;
        }
        if (in_array($tableName, $tableNames)) {
          $classList[] = $className;
        }
      }
    }
    return $classList;
  }

  /**
   * Export data of specified table to csv file.
   *
   * @param string $modelClassName Name of model class to be backed up.
   * @param string $outputFileName
   */
  public static function exportTableToCsv($modelClassName, $outputFileName) {
    exportDbToCsv([$modelClassName], $outputFileName);
  }

  /**
   * Append table data to the CSV file.
   * <p>
   * The first row is the table column names.
   * Follow is the records, each on one row.
   * @param string $modelClassName
   * @param resource $handle Output file handle.
   */
  private static function appendTableToCsv($modelClassName, $handle)
  {
    Yii::trace("Save data of type $modelClassName");

    // Get model object.
    $model = new $modelClassName;
    // Get array of attributes.
    $attributes = $model->attributes();

    // Write table name.
    fputcsv($handle, [self::TABLE_MARKER, $modelClassName]);
    // Write column name.
    fputcsv($handle, $attributes);

    $offset = 0;
    $limit = 1000;
    do {
      Yii::trace("Load $limit records from $offset");
      // Get all record from db.
      $records = $modelClassName::find()
          ->offset($offset)
          ->limit($limit)
          ->all();
      if (!$records) {
        break;
      }

      // Write records.
      foreach ($records as $record) {
        // Put record data to an array.
        $data = [];
        foreach ($attributes as $attribute) {
          $data[] = $record->$attribute;
        }
        fputcsv($handle, $data);
      }

      Yii::trace("Memory usage " . number_format(memory_get_usage()) . " bytes");
      // Goto next offset.
      $offset += $limit;
//      $records = null; // Free memory.
    } while (true);
  }

  public static function setForeignKeyCheck($value)
  {
    Yii::$app->db->createCommand("SET FOREIGN_KEY_CHECKS={$value};")->execute();
  }

  public static function truncate($tableName, $setForeignKeyCheck = FALSE)
  {
    if ($setForeignKeyCheck) {
      self::setForeignKeyCheck(0);
    }
    Yii::$app->db->createCommand("TRUNCATE TABLE {$tableName};")->execute();
  }

  /**
   * Import data from csv file created by exportDbToCsv().
   *
   * @param string $inputFileName
   */
  public static function importDbFromCsv($inputFileName)
  {
    self::setForeignKeyCheck(0);
    $truncate = TRUE;
    $updateKey = NULL;
    $transaction = Yii::$app->db->beginTransaction(); // Open transaction.
    try {
      // Open input file.
      $handle = fopen($inputFileName, 'r');

      // Read each line of csv.
      $attributes = NULL;
      while (($data = fgetcsv($handle)) !== FALSE) {
        if ($data[0] === self::TABLE_MARKER) {
          // Process model class name line.
          $modelClassName = self::fullQualifiedModelClassName($data[1]);
          if (isset($data[2]) && $data[2] == 'update') {
              $updateKey= $data[3];
              $truncate = FALSE;
          }
          // Truncate table
          if ($truncate) {
              self::truncate($modelClassName::tableName());
          }
          $attributes = NULL; // Reset attribute names.
        } else if ($attributes === NULL) {
          // Process model attribute names line.
          $attributes = $data;
        } else {
          // Process record data line.
          $model = NULL;
          if ($updateKey) {
              $model = $modelClassName::findOne([$updateKey => $data[array_search($updateKey, $attributes)]]);
          }
          $model = $model ? $model : new $modelClassName;
          foreach ($attributes as $index => $attribute) {
            if ($attribute) {
              $model->$attribute = $data[$index] === 'NULL' || $data[$index] === '' ? NULL : $data[$index];
            }
          }
          // Save record.
          if (!$model->save()) {
            $model->logError();
            throw new Exception("Error saving $modelClassName");
          }
        }
      }

      // Close output file.
      fclose($handle);

      $transaction->commit(); // Commit transaction.
    } catch (Exception $e) {
      $transaction->rollback(); // Rolback transaction.
      throw $e;
    }
    self::setForeignKeyCheck(1);
  }

  /**
   * Load list of model from backed up csv file.
   * @param string $inputFileName The path to the CSV file.
   * @param string[] $fields Fields to be load into the model if specified. If is NULL, all columns are get.
   * @return array, each element is an array load from a CSV row, adding
   *     two elements 'model' => <the created model>,
   *     'columnIndex' => the column indexes.
   */
  public static function loadDbFromCsv($inputFileName, $fields = NULL) {
    $lines = []; // Return result.

    // Open input file.
    $handle = fopen($inputFileName, 'r');

    // Read each line of csv.
    $attributes = NULL; // Columns to be loaded.
    $columnIndexes = NULL; // Column indexes in CSV columns.
    while (($data = fgetcsv($handle)) !== FALSE) {
      if ($data[0] === self::TABLE_MARKER) {
        // Process model class name line.
        $modelClassName = $data[1];
        $attributes = NULL; // Reset attribute names.
      } else if ($attributes === NULL) {
        $nColumns = count($data);
        // Process model attribute names line.
        $columnIndexes = self::parseColumnFromCsv($data);
        // Get all column if $fields is NULL, else get only specified fields.
        $attributes = $fields == NULL ? $data : $fields;
      } else if ($nColumns == count($data)) {
        // Process record data line.
        $model = new $modelClassName;
        foreach ($attributes as $attribute) {
          $model->$attribute = $data[$columnIndexes[$attribute]];
        }
        // Keep the model object and the column index information in $data.
        $data[self::IDX_MODEL] = $model;
        $data[self::IDX_COLUMN_INDEX] =& $columnIndexes;
        // Add $data to $lines.
        $lines[] = $data;
      }
    }

    // Close output file.
    fclose($handle);

    return $lines;
  }

  /**
   * Get the index of columns on a csv line.
   * @param string[] $csvLine
   * @return array Array[columnName] = index
   */
  public static function parseColumnFromCsv($csvLine)
  {
    $columns = [];
    foreach ($csvLine as $index => $columnName) {
      $columns[$columnName] = $index;
    }
    return $columns;
  }
}
?>