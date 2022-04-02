<?php
namespace batsg\helpers;

use Exception;

class HGridView
{
    /**
     * Generate a editable column for used with Jeditable plugin.
     * @param string $attribute
     * @param array $columnOptions
     * @param string $editableClass
     * @return array
     */
    public static function jeditableColumn($attribute, $columnOptions = [], $editableClass = 'editable')
    {
        $column = [
            'attribute' => $attribute,
            'contentOptions' => function ($model, $key, $index, $column)
                    use ($attribute, $editableClass) {
                return [
                    'class' => [$editableClass],
                    'id' => (new \ReflectionClass($model))->getShortName() . "[$attribute][$model->id]",
                ];
            },
        ];
        $column = array_merge_recursive($column, $columnOptions);
        return $column;
    }

    /**
     * Create a GridView column that is a HTML link (anchor tag) that is opened in new tab.
     * Example of usage
     * ```php
     * <?= GridView::widget([
     *     'columns' => [
     *         ['class' => 'yii\grid\SerialColumn'],
     *
     *         // Code that use this function
     *         HGridView::linkColumn('title', function($model) {
     *             return [$model->title, ['/book/view', 'id' => $model->id]];
     *         }),
     *     ],
     * ]); ?>
     * ```
     * The code below
     * ```php
     *     HGridView::linkColumn('title', function($model) {
     *         return [$model->title, ['/book/view', 'id' => $model->id]];
     *     }, TRUE),
     * ```
     *equivalents to
     * ```php
     *      [
     *          'attribute' => 'title',
     *          'format' => 'raw',
     *          'value' => function($model) {
     *              $title = $model->title;
     *              return ($title || $title === 0 || $title === '0') ? Html::a($model->title, $['/data/view', 'id' => $model->id], ['target' => '_blank']) : NULL;
     *          },
     *      ],
     * ```
     * There are 3 ways to use this.
     * 1. Simplest way
     *   ```php
     *    HGridView::linkColumn('url'); // Generate link to $model->url, with text is $model->url itself.
     *   ```
     * 2. Fixed title text.
     *   ```php
     *    HGridView::linkColumn('url', 'Link to page'); // Generate link to $model->url, with text is "Link to page".
     *   ```
     * 3. Use function to generate anchor tag dyanically.
     *   ```php
     *     HGridView::linkColumn('title', function($model) {
     *         return [$model->title, ['/book/view', 'id' => $model->id]];
     *     }, TRUE),
     *   ```
     * @param string $attribute
     * @param callable|string|null $anchorParam
     *                             If it is a function, then it receives $model as parameter, and return an array of elements text, url, and option used for Html::a()
     *                             If it is a string, then it is an attribute name that used to generate the link (link to $model->$anchorParam)
     *                             If it is NULL, then this is a link to $model->$attribute.
     * @param bool $newTab If true, then add target="_blank" to option of Html::a().
     * @return array used for GridView::widget() columns element.
     */
    public static function linkColumn($attribute, $anchorParam = NULL, $newTab = FALSE)
    {
        return [
            'attribute' => $attribute,
            'format' => 'raw',
            'value' => function($model) use ($attribute, $anchorParam, $newTab) {
                if ($anchorParam == NULL) {
                    $params = [$model->$attribute, $model->$attribute, []];
                } else if (is_string($anchorParam)) {
                    $params = [$anchorParam, $model->$attribute, []];
                } else if (is_callable($anchorParam)) {
                    $params = call_user_func($anchorParam, $model);
                } else {
                    throw new Exception("anchorParam accepts only callable|string|null");
                }
                list($text, $url, $options) = array_pad($params, 3, []);
                return HHtml::a($text, $url, $options, $newTab);
            },
        ];
    }

    /**
     * Create a GridView column that display an image.
     * Example of usage
     * ```php
     * <?= GridView::widget([
     *     'columns' => [
     *         ['class' => 'yii\grid\SerialColumn'],
     *
     *         // Code that use this function
     *         HGridView::imageColumn('image_path'),
     *     ],
     * ]); ?>
     * ```
     */
    public static function imageColumn($attribute, $options = [], $width = 100)
    {
        return [
            'attribute' => $attribute,
            'format' => 'raw',
            'value' => function($model) use ($attribute, $options, $width) {
                $src = $model->$attribute;
                $thumbnail = HHtml::imgThumbnail($src, $options, $width);
                return HHtml::a($thumbnail, $src, [], TRUE);
            },
        ];
    }
}
