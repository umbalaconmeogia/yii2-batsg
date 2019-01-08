<?php
namespace batsg\controllers;

use yii\filters\AccessControl;

/**
 * Base controller that required user login for every actions.
 */
class BaseUserController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
}