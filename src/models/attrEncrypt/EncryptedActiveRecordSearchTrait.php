<?php
namespace batsg\models\attrEncrypt;

trait EncryptedActiveRecordSearchTrait
{

    /**
     * Filter models by specified filtering attributes.
     * @param array $models
     * @return array Array of models.
     */
    protected function filterModels($models)
    {
        $result = [];
        $filters = []; // Filter attributes and filter values.
        // Create $filters array.
        foreach (self::encryptedAttributes() as $attribute) {
            if ($this->$attribute) {
                $filters[$attribute] = $this->$attribute;
            }
        }
        // Filter on $models, put filterred model into $result.
        foreach ($models as $model) {
            $passFilter = TRUE;
            foreach ($filters as $attribute => $filterValue) {
                if (mb_strstr($model->$attribute, $filterValue) === FALSE) {
                    $passFilter = FALSE;
                    break;
                }
            }
            if ($passFilter) {
                $result[] = $model;
            }
        }
        return $result;
    }
}