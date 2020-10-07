<?php
namespace batsg\models\traits;

/**
 * Utility to manipulate attribute control bits value.
 * Example of a class that use TraitBitAttribute
 * ```php
 * // @property int $skill
 * // @property string $skillStr
 * // @property int[] $skillBits
 * class Employee extends \yii\db\ActiveRecord
 * {
 *     use TraitBitAttribute;
 *
 *     const SKILL_NEED_IMPROVE = 1 << 0; // 1
 *     const SKILL_NORMAL = 1 << 1; // 2
 *     const SKILL_GOOD = 1 << 2; // 4
 *     const SKILL_EXCELLENT = 1 << 3; // 8
 *     const SKILL_OUTSTANDING = 1 << 4; // 16
 *
 *     public $skill;
 *
 *     // Mapping between SKILL_XXX values and their names.
 *     // @return string[]
 *     public static function skillOptionArr()
 *     {
 *         return [
 *             self::SKILL_NEED_IMPROVE => Yii::t('app', 'Need improve'),
 *             self::SKILL_NORMAL => Yii::t('app', 'Normal'),
 *             self::SKILL_GOOD => Yii::t('app', 'Good'),
 *             self::SKILL_EXCELLENT => Yii::t('app', 'Excellent'),
 *             self::SKILL_OUTSTANDING => Yii::t('app', 'Outstanding'),
 *         ];
 *     }
 *
 *     // Get corresponding name of `skill` attribute.
 *     // @return string
 *     public function getSkillStr()
 *     {
 *         return self::getBitNameStr(self::skillOptionArr(), $this->skill);
 *     }
 *
 *     // @return int[]
 *     public function getSkillBits()
 *     {
 *         return $this->getBitAttributeAsArray('skill', self::skillOptionArr());
 *     }
 *
 *     // @param int[] $values
 *     public function setSkillBits($values)
 *     {
 *         return $this->setBitAttributeFromArray('skill', $values);
 *     }
 * }
 * ```
 * On `view`
 * ```php
 * echo DetailView::widget([
 *     'model' => $model,
 *     'attributes' => [
 *         [
 *             'attribute' => 'skill',
 *             'value' => 'skillStr',
 *         ],
 *         // or simply 'skillStr',
 *         // Other stuffs
 *     ],
 * ]);
 * ```
 * On '_form'
 * ```php
 * <?= $form->field($model, 'skillBits')->checkboxList(Employee::skillOptionArr()) ?>
 * ```
 */
trait TraitBitAttribute
{
    /**
     * Check if $attributeValue is set in $groupValue.
     * @param int $attributeValue
     * @param int $bit A bit to be checked whether it is set in $attributeValue
     * @return boolean
     */
    public static function bitIsOn($attributeValue, $bit)
    {
        return ($attributeValue & $bit) <> 0;
    }

    /**
     * Get names of bits.
     *
     * This is used to create string attribute.
     * Example:
     * ```
     * public function getRankStr()
     * {
     *     return self::getBitNameStr(self::rankOptionArr(), $this->rank);
     * }
     * ```
     * @param string[] $allBitNames
     * @param int $attributeValue
     * @param string $delimiter Delimiter between names.
     * @return string
     */
    public static function getBitNameStr($allBitNames, $attributeValue, $delimiter = ', ')
    {
        $bitsStr = [];
        foreach ($allBitNames as $bit => $name) {
            // \Yii::debug("Attribute Value: $attributeValue, bit $bit");
            if (self::bitIsOn($attributeValue, $bit)) {
                $bitsStr[] = $name;
            }
        }
        return join($delimiter, $bitsStr);
    }

    /**
     * Usage example
     * ```
     * public function setRankBits($value)
     * {
     *     $this->setBitAttributeFromArray('rank', $value);
     * }
     * ```
     * @param string $attributeName
     * @param int[] $values Selected values.
     */
    public function setBitAttributeFromArray($attributeName, $values)
    {
        if (!is_array($values)) {
            $values = [$values];
        }
        $group = 0;
        foreach ($values as $element) {
            if ($element) {
                $group |= $element;
            }
        }
        $this->$attributeName = $group;
    }

    /**
     * Return attribute as array of selected values.
     * Usage example
     * ```
     * public function getRankBits()
     * {
     *     return $this->getBitAttributeAsArray('rank', self::rankOptionArr());
     * }
     * ```
     * @return int[]
     */
    public function getBitAttributeAsArray($attributeName, $allBitNames)
    {
        $groups = [];
        $attributeValue = $this->$attributeName;
        foreach ($allBitNames as $value => $name) {
            if (self::bitIsOn($attributeValue, $value)) {
                $groups[] = $value;
            }
        }
        return $groups;
    }

}

/*
class Employee extends \yii\db\ActiveRecord
{
    use TraitBitAttribute;

    const SKILL_NEED_IMPROVE = 1 << 0; // 1
    const SKILL_NORMAL = 1 << 1; // 2
    const SKILL_GOOD = 1 << 2; // 4
    const SKILL_EXCELLENT = 1 << 3; // 8
    const SKILL_OUTSTANDING = 1 << 4; // 16

    public $skill;

    // Mapping between SKILL_XXX values and their names.
    // @return string[]
    public static function skillOptionArr()
    {
        return [
            self::SKILL_NEED_IMPROVE => Yii::t('app', 'Need improve'),
            self::SKILL_NORMAL => Yii::t('app', 'Normal'),
            self::SKILL_GOOD => Yii::t('app', 'Good'),
            self::SKILL_EXCELLENT => Yii::t('app', 'Excellent'),
            self::SKILL_OUTSTANDING => Yii::t('app', 'Outstanding'),
        ];
    }

    // Get corresponding name of `skill` attribute.
    // @return string
    public function getSkillStr()
    {
        return self::getBitNameStr(self::skillOptionArr(), $this->skill);
    }

    // @return int[]
    public function getSkillBits()
    {
        return $this->getBitAttributeAsArray('skill', self::skillOptionArr());
    }

    // @param int[] $values
    public function setSkillBits($values)
    {
        return $this->setBitAttributeFromArray('skill', $values);
    }
}
*/