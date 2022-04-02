<?php
namespace batsg\helpers;

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
     * Usage example
     * ```php
     * <?= GridView::widget([
     *     'columns' => [
     *         ['class' => 'yii\grid\SerialColumn'],
     *
     *         // Code that use this function
     *         HGridView::linkColumn('title', function($model) {
     *             return [$model->title, ['/data/view', 'id' => $model->id]];
     *         }),
     *     ],
     * ]); ?>
     * ```
     * The code below
     * ```php
     *     HGridView::linkColumn('title', function($model) {
     *         return [$model->title, ['/data/view', 'id' => $model->id]];
     *     }, TRUE),
     * ```
     *equivalents to
     * ```php
     *      [
     *          'attribute' => 'title',
     *          'format' => 'raw',
     *          'value' => function($model) {
     *              $title = $model->title;
     *              return ($title || $title === 0 || $title === '0') ? Html::a($model->title, $model->navitime_url, ['target' => '_blank']) : NULL;
     *          },
     *      ],
     * ```
     * @param string $attribute
     * @param callable $createHtmlAParam A function that receive $model as parameter, and return an array of elements text, url, and option used for Html::a()
     * @param bool $newTab If true, then add target="_blank" to option of Html::a().
     * @return array used for GridView::widget() columns element.
     */
    public static function linkColumn($attribute, $anchorParam, $newTab = FALSE)
    {
        return [
            'attribute' => $attribute,
            'format' => 'raw',
            'value' => function($model) use ($anchorParam, $newTab) {
                $params = call_user_func($anchorParam, $model);
                list($text, $url, $option) = array_pad($params, 3, []);
                return HHtml::a($text, $url, $option, $newTab);
            },
        ];
    }
}
