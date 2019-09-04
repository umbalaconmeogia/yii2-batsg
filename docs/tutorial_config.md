# Configuration

## Logging
Put most of log into app.log except DB log, that is put into sql.log
```php
'log' => [
    'targets' => [
        [
            'class' => 'yii\log\FileTarget',
            'levels' => ['error', 'warning', 'info'],
            'logVars' => [],
            'except' => ['yii\db\*'],
        ],
        [
            'class' => 'yii\log\FileTarget',
            'levels' => ['error', 'warning', 'info', 'trace'],
            'logVars' => [],
            'categories' => ['yii\db\*'],
            'logFile' => '@app/runtime/logs/sql.log',
        ],
    ],
],
```