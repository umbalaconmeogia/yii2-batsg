<?php
namespace batsg\helpers;

use yii\base\Model;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\base\Widget;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/**
 * Helper class for generating HTML.
 */
class HHtml
{
    /**
     * Tempalate of ActiveField used in method trXXX().
     * @var string
     */
    public static $tdTemplate = "<td>{label}</td><td>{input}\n{hint}\n{error}</td>";

    /**
     * Input wrapped by &lt;tr&gt; tag.
     * @param ActiveForm $form
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string HTML code, wrapped by &lt;tr&gt;
     */
    public static function tdTextInput($form, $model, $attribute, $options = ['maxlength' => true])
    {
        return self::tdActiveField($form, $model, $attribute)->textInput($options);
    }

    /**
     * Input wrapped by &lt;tr&gt; tag.
     * @param ActiveForm $form
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string HTML code, wrapped by &lt;tr&gt;
     */
    public static function tdTextarea($form, $model, $attribute, $options = ['rows' => 3])
    {
        return self::tdActiveField($form, $model, $attribute)->textarea($options);
    }

    /**
     * Input wrapped by &lt;tr&gt; tag.
     * @param ActiveForm $form
     * @param Model $model
     * @param string $attribute
     * @param array $items
     * @param array $options
     * @return string HTML code, wrapped by &lt;tr&gt;
     */
    public static function tdRadioList($form, $model, $attribute, $items, $options = [])
    {
        return self::tdActiveField($form, $model, $attribute)->radioList($items, $options);
    }

    /**
     * Input wrapped by &lt;tr&gt; tag.
     * @param ActiveForm $form
     * @param Model $model
     * @param string $attribute
     * @param array $items
     * @param array $options
     * @return string HTML code, wrapped by &lt;tr&gt;
     */
    public static function tdDropDownList($form, $model, $attribute, $items, $options = [])
    {
        return self::tdActiveField($form, $model, $attribute)->dropDownList($items, $options);
    }

    /**
     * Input wrapped by &lt;tr&gt; tag.
     * @param ActiveForm $form
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string HTML code, wrapped by &lt;tr&gt;
     */
    public static function tdCheckboxList($form, $model, $attribute, $items, $options = [])
    {
        return self::tdActiveField($form, $model, $attribute)->checkboxList($items);
    }

    /**
     * Input wrapped by &lt;tr&gt; tag.
     * @param ActiveForm $form
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string HTML code, wrapped by &lt;tr&gt;
     */
    public static function tdCheckbox($form, $model, $attribute, $options = [])
    {
        return self::tdActiveField($form, $model, $attribute)->checkbox($options);
    }

    /**
     * Input wrapped by &lt;tr&gt; tag.
     * @param ActiveForm $form
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string HTML code, wrapped by &lt;tr&gt;
     */
    public static function tdDatePicker($form, $model, $attribute)
    {
        return self::tdActiveField($form, $model, $attribute)->widget(DatePicker::className());
    }

    /**
     *
     * @param ActiveForm $form
     * @param Model $model
     * @param string $attribute
     * @return ActiveField
     */
    public static function tdActiveField($form, $model, $attribute)
    {
        $field = $form->field($model, $attribute, ['template' => self::$tdTemplate]);
        $field->options['tag'] = 'tr';
        return $field;
    }

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