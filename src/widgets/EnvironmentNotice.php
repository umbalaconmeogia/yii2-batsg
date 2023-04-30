<?php
namespace batsg\widgets;

use yii\base\Widget;
use yii\helpers\Html;

/**
 * This is a widget to display a text at the top-left of the screen to show the system envirotment (such as "LOCAL", "STAGING", or nothing for product env).
 *
 * To use this widget, add this widget in `layouts/main.php`.
 * ```php
 * <?php $this->beginBody() ?>
 *     <?= EnvironmentNotice::widget() ?>
 *     <div class="wrap">
 *     // other stuff.
 * ```
 * By default, the text *LOCAL* is displayed at the top-left cornor.
 * To change the text, config the widget in yii2 configuration file (set to NULL on production environment to display nothing).
 * ```php
 *   'container' => [
 *       'definitions' => [
 *           'batsg\widgets\EnvironmentNotice' => ['environment' => NULL],
 *       ],
 *   ],
 * ```
 * or specify in widget call
 * ```php
 *     <?= EnvironmentNotice::widget(['environtment' => Yii::$app->params['environmentNotice']]) ?>
 * ```
 */
class EnvironmentNotice extends Widget
{
    /**
     * Message to be display at screen top left corner. Set to NULL on product environemt.
     * @var string
     */
    public $environment = 'LOCAL';

    public $position = ['position' => 'fixed', 'top' => '0', 'left' => '0', 'z-index' => '10000'];

    public $backgroundColor = 'yellow';

    public $textColor = 'red';

    public function run()
    {
        $result = NULL;

        if ($this->environment) {
            $result = Html::tag('div', $this->environment, [
                'style' => $this->cssStyleString(),
            ]);
        }
        return $result;
    }

    /**
     * CSS style for output HTML element.
     * @return string
     */
    private function cssStyleString()
    {
        $cssString = [];
        $cssValues = array_merge($this->position, ['background' => $this->backgroundColor, 'color' => $this->textColor]);
        foreach ($cssValues as $key => $value) {
            $cssString[] = "$key: $value";
        }
        return join('; ', $cssString);
    }
}