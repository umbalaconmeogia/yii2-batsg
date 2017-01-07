<?php
namespace batsg\helpers;

use yii\base\Model;
use yii\helpers\Html;

/**
 * Helper class for generating HTML.
 */
class HHtml
{
    /**
     * Display hidden fields.
     * @param CActiveRecord $model
     * @param mixed $fields NULL, or string (fields name), or array of fieldNames.
     * @param string $index If specified, then field name will be set as modelName[index][fieldName]
     * @param array $htmlOptions
     * @return string HTML code.
     */
    public static function hiddenInput($model, $fields = NULL, $index = NULL, $htmlOptions = array(), $separator = NULL)
    {
        // Output all attributes if $fields is not specified.
        if (!$fields) {
            $fields = array_keys($model->attributes);
        }
        // Wrap $fields by array if only string specified.
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        $html = [];
        foreach ($fields as $field) {
            $attribute = $index !== NULL ? "[$index]$field" : $field;
            // Add class to html options.
            $options = $htmlOptions;
//             if (isset($options['class'])) {
//                 $options['class'] .= " $field";
//             } else {
//                 $options['class'] = "$field";
//             }
            $html[] = Html::activeHiddenInput($model, $attribute, $options);
        }
        return join($separator, $html);
    }

}