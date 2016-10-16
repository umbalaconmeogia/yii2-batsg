<?php
namespace app\components\yii2batsg;

/**
 * Debug functions.
 */
class Debug
{
    private static $timerStartTime;

    /**
     * Set the start point of a timer to measure time of a processing.
     * This also remember the time in an internal static variable,
     * but that varibale may be overwritted by another calling
     * of Debug::startTimer(). Using the return value of this method is safer.
     * <p>Usage</p>
     * 1. Simple usage (not safe because the remenbered time may be overwritten by another cal of Debug::startTimer().
     * <pre>
     * Debug::startTimer();
     * // Run your processing here.
     * Debug::stopTimer();
     * </pre>
     * 2. Safe usage.
     * <pre>
     * $t = Debug::startTimer();
     * // Run your processing here.
     * Debug::stopTimer($t);
     * </pre>
     * @return The current time measured in the number of seconds since the Unix epoch (0:00:00 January 1, 1970 GMT).
     */
    public static function startTimer()
    {
        $timeStart = microtime(true);
        self::$timerStartTime = $timeStart;
        return $timeStart;
    }

    /**
     * Set the stop point of the timer, display the time measuring.
     * @param string $message Message to be displayed
     * @param float $startTime Return value of Debug::startTimer();
     * @param string $echoMessage Display message or not.
     * @return string The generated message.
     */
    public static function endTimer($message = NULL, $startTime = NULL, $echoMessage = TRUE)
    {
        if ($startTime === NULL) {
            $startTime = self::$timerStartTime;
        }
        $timeDiff = microtime(true) - $startTime;
        if ($message) {
            $message .= ' ';
        }
        $message .= "time: $timeDiff seconds.";
        if ($echoMessage) {
            echo "$message\n";
        }
        return $message;
    }

    /**
     * Measure time of a function running.
     * <p>Example of usage:</p>
     * <pre>
     * // $test is an object that has method myProcess().
     * Debug::time("Runnig my process", function() use ($test) {$test->myProcess(); });
     * </pre>
     * @param string $message Message to be displayed when finish running the $callback.
     * @param callable $callback Function to be performed.
     * @param boolean $echoMessage If TRUE, then echo the message.
     * @return string The generated message.
     */
    public static function time($message, $callback, $echoMessage = TRUE)
    {
        $t = self::startTimer();
        call_user_func($callback);
        return self::endTimer($message, $t, $echoMessage);
    }
}