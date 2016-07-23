# yii2-batsg
My style, my best practice and my libraries when using yii2 framework.

## Overview

* Convention when designing database.
* gii modification
 
## Convention when designing database

* All DB table should contain following columns.

Column name | Data type | Description
---|---|---
id | serial | primary key
data_status | int | 1: new, 2: updated, 9: deleted
created_by | int | Create user id. Set automatically.
created_at | int | Created timestamp. Set automatically.
updated_by | int | Update user id. Set automatically.
updated_at | int | Updated timestamp. Set automatically.

## Gii modification

* File `vendor/yiisoft/yii2-gii/generators/model/default/model.php`


  Old
   ```php
   class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
   ```
  New
   ```php
   class <?= $className ?> extends BaseModel
   ```

* File `vendor/yiisoft/yii2-gii/generators/crud/default/views/index.php`
   Old
   ```php
<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>
   ```
   New
   ```php
<?php
$count = 0;
$ignoredColumns = ['id', 'created_at', 'updated_at', 'data_status', 'created_by', 'updated_by'];    // THIS LINE IS ADDED
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        if (!in_array($column->name, $ignoredColumns)) { // THIS LINE IS ADDED
            $format = $generator->generateColumnFormat($column);
            if (++$count < 6) {
                echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
            } else {
                echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
            }
        }                                                // THIS LINE IS ADDED
    }
}
?>
   ```
