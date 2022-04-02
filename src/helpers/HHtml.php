<?php
namespace batsg\helpers;

use yii\helpers\Html;

/**
 * Helper class for generating HTML.
 */
class HHtml
{
    /**
     * Display hidden fields.
     * @param ActiveRecord $model
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

	public static function datetimeLocalValue($dateTime)
    {
        return date('Y-m-d\TH:i:s', strtotime($dateTime));
	}

	/**
	 *
	 * @param array|string $src the image URL. This parameter will be processed by [[Url::to()]].
	 * @param integer $width the width of the thumbnail.
	 * $param array $options the tag options in terms of name-value pairs.
	 * @return string the generated image tag.
	 */
	public static function imgThumbnail($src, $width = 100, $options = []) {
	    // Set width attribute of the image.
	    if ($width && !isset($options['width'])) {
	        $options['width'] = $width;
	    }

	    return Html::img($src, $options);
    }

    /**
     * Generate ruby tag.
     * @pram string $text
     * @param string|null $ruby
     */
    public static function ruby($text, $ruby)
    {
        return $ruby ? "<ruby>{$text}<rp>(</rp><rt>{$ruby}</rt><rp>)</rp></ruby>" : $text;
    }

    /**
     * @param string $text Anchor text.
     * @param array|string|null $url
     * @param array $option
     * @param bool $newTab If true, then add target="_blank" to option of Html::a().
     * @return string|null anchor tag. If $text is NULL, then this function return NULL.
     */
    public static function a($text, $url, $option = [], $newTab = FALSE)
    {
        if ($newTab) {
            $option['target'] = '_blank';
        }
        return ($text || $text === 0 || $text === '0') ? Html::a($text, $url, $option) : NULL;
    }
}