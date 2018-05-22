<?php
namespace batsg\controllers;

use yii\web\Controller;

class BaseController extends Controller
{

    /**
     * Get the specified URL parameter.
     * <p>
     * This is the wrapper for getting <code>$_REQUEST[&lt;parameter&gt;]</code>,
     * it will return the default value if the parameter is not set.
     * <p>
     * If it is not set, $defValue will be returned.
     * @param string $paramName The parameter to be get.
     * @param mixed $defValue Value to return in case the parameter is not specified in the URL.
     * @param boolean $trim Trim the input value if set to TRUE
     * @return mixed
     */
    public static function getParam($paramName = NULL, $defValue = NULL, $trim = TRUE)
    {
        $value = isset($_REQUEST[$paramName]) ? $_REQUEST[$paramName] : $defValue;
        // Trim
        if ($trim) {
            if (is_array($value)) {
                foreach ($value as $key => $v) {
					if (!is_array($v)) {
                      $value[$key] = trim($v);
					}
                }
            } else {
                $value = trim($value);
            }
        }
        return $value;
    }
}