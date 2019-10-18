<?php
namespace batsg\helpers;

/**
 * Usage of counting running time
 * <pre>
 * HDebug::startCountTime('MY_COUNT'); // Start count time.
 * // do something
 * HDebug::runtimeReport(self::TIME_COUNT_KEY); // Display running time.
 * </pre>
 */
class HDebug
{
    /**
     * @var int[][] $countTime[key]
     */
    private static $countTime = [];

    /**
     * Remember current time specified by a key.
     * @param string $key
     * @return float current time.
     */
    public static function startCountTime($key)
    {
        self::$countTime[$key] = microtime(true);
        return self::$countTime[$key];
    }

    /**
     * Count time of registered key.
     * @param string $key
     * @return float diff time.
     */
    public static function countTime($key)
    {
        $time = microtime(true) - self::$countTime[$key];
        return $time;
    }

    /**
     * Display a running time.
     * @param string $key
     * @param string $messageFormat
     * @param boolean $echo If true, then echo the message.
     */
    public static function runtimeReport($key, $messageFormat = 'Time: %f seconds', $echo = TRUE)
    {
        $message = sprintf($messageFormat, self::countTime($key));
        if ($echo) {
            echo "$message\n";
        }
        return $message;
    }
}