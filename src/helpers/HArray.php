<?php
namespace batsg\helpers;

/**
 * Manipulate array functions.
 */
class HArray
{
    /**
     * Check if two arrays contain same value set.
     * 
     * @param array $arr1            
     * @param array $arr2            
     * @return boolean TRUE if two arrays contain same value set.
     */
    public static function equal($arr1, $arr2)
    {
        return ! array_diff($arr1, $arr2) && ! array_diff($arr2, $arr1);
    }

    /**
     * Flatten elements of a multi-dimension array.
     * 
     * @param mixed $arr
     *            Anything (normal object, or array).
     * @return array
     */
    public static function flatten($arr)
    {
        if (! is_array($arr)) {
            $arr = array(
                $arr
            );
        }
        
        $result = array();
        foreach ($arr as $element) {
            // Merge element to $result if it is an array.
            if (is_array($element)) {
                $result = array_merge($result, self::flatten($element));
            } else {
                // Add element to $result.
                $result[] = $element;
            }
        }
        
        return $result;
    }
    
    /**
     * Split a string into array.
     * @param string $delimiter
     * @param string $string
     * @param boolean $trim
     * @param boolean $ignoreEmpty
     * @return string[]
     */
    public static function explode($delimiter , $string, $trim = TRUE, $ignoreEmpty = FALSE)
    {
        $result = [];
        $splitted = explode($delimiter, $string);
        foreach ($splitted as $element) {
            if ($trim) {
                $element = trim($element);
            }
            if ($element || !$ignoreEmpty) {
                $result[] = $element;
            }
        }
        return $result;
    }
}
?>