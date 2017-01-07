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
}
