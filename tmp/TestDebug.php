<?php
namespace app\components\yii2batsg;

include 'Debug.php';

class Test
{
    public function myProcess()
    {
        echo "myProcess() running.\n";
    }
}

$test = new Test();

// Example
Debug::startTimer();
$test->myProcess();
Debug::endTimer('Run test');

// Example
Debug::time("Run test 1", function() use ($test) {$test->myProcess(); });

// Example
Debug::time("Run test 2", [$test, 'myProcess']);
