<?php
namespace batsg\controllers;


/**
 * Default controller with CRUD processing on a model.
 * A simple controller may be defined as below.
 * ```php
 * class UserController extends \batsg\controllers\DefaultController
 * {
 *     protected $searchClass = UserSearch::class;
 *
 *     protected $modelClass = User::class;
 * }
 * ```
 */
class DefaultController extends BaseUserController
{
    /**
     * Subclass must set this attribute, for example
     * ```php
     * protected $searchClass = UserSearch::class;
     * ```
     * var $string
     */
    protected $searchClass;

    /**
     * Subclass must set this attribute, for example
     * ```php
     * protected $modelClass = User::class;
     * ```
     * var $string
     */
    protected $modelClass;

    public function actionIndex()
    {
        return $this->defaultActionIndex($this->searchClass);
    }

    public function actionView($id)
    {
        return $this->defaultActionView($id, $this->modelClass);
    }

    public function actionCreate()
    {
        return $this->defaultActionCreate($this->modelClass);
    }

    public function actionUpdate($id)
    {
        return $this->defaultActionUpdate($id, $this->modelClass);
    }

    public function actionDelete($id)
    {
        return $this->defaultActionDelete($id, $this->modelClass);
    }
}