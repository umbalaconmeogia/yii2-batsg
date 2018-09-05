<?php
namespace batsg\commands;

use batsg\helpers\HBackup;
use yii\console\Controller;
use batsg\helpers\HArray;

class BackupController extends Controller
{
    /**
     * @var array
     */
    private static $actionOptions = [
        'export-to-csv' => [
            'csvFile',
            'models'
        ],
        'import-from-csv' => [
            'csvFile',
        ],
    ];

    /**
     * @var string
     */
    public $csvFile;

    /**
     * @var string
     */
    public $models;

    /**
     * {@inheritDoc}
     * @see \yii\console\Controller::options()
     */
    public function options($actionID)
    {
        $result = [];
        if (isset(self::$actionOptions[$actionID])) {
            $result = self::$actionOptions[$actionID];
        }
        return $result;
    }

    /**
     * Syntax:
     *     ./yii backup/export-to-csv --csvFile=data/backup.csv --models=Source,User
     */
    public function actionExportToCsv()
    {
        if ($this->models) {
            $this->models = HArray::explode(',', $this->models);
        }
        HBackup::exportDbToCsv($this->csvFile, $this->models);
    }

    /**
     * Syntax:
     *     yii backup/import-from-csv --csvFile=data/source.csv
     */
    public function actionImportFromCsv()
    {
        HBackup::importDbFromCsv($this->csvFile);
    }
    
    /**
     * Update tables' id sequence of PostgreSQL.
     * Syntax:
     * ./yii backup/update-postgres-id-seq
     */
    public function actionUpdatePostgresIdSeq()
    {
        HBackup::updatePostgresIdSeq();
    }
}
?>