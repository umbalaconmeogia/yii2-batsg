<?php
namespace batsg\controllers;

use batsg\Y;
use Yii;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * A controller that inherit from BaseController looks as following
 * <pre>
 * class NewController extends BaseController
 * {
 *     public function actionIndex()
 *     {
 *         return $this->defaultActionIndex(UserSearch::class);
 *     }
 *
 *     public function actionView($id)
 *     {
 *         return $this->defaultActionView($id, User::class);
 *     }
 *
 *     public function actionCreate()
 *     {
 *         return $this->defaultActionCreate(User::class, 'index');
 *     }
 *
 *     public function actionUpdate($id)
 *     {
 *         return $this->defaultActionUpdate($id, User::class, 'index');
 *     }
 *
 *     public function actionDelete($id)
 *     {
 *         return $this->defaultActionDelete($id, User::class);
 *     }
 * }
 * </pre>
 */
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

    /**
     * Default action to list all models.
     *
     * This may be used to create actionIndex() as below:
     * <pre>
     * public function actionIndex()
     * {
     *     return $this->defaultActionIndex(UserSearch::class);
     * }
     *
     * @return mixed
     */
    protected function defaultActionIndex($searchModelClass)
    {
        $searchModel = new $searchModelClass;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Default action to display a single model.
     *
     * This may be used to create actionView($id) as below:
     * <pre>
     * public function actionView($id)
     * {
     *     return $this->defaultActionView($id, User::class);
     * }
     * </pre>
     *
     * @param integer $id
     * @param string $modelClass The fully qualified class name.
     * @param string $view View name
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function defaultActionView($id, $modelClass, $view = 'view')
    {
        return $this->render($view, [
            'model' => $this->findModelById($id, $modelClass),
        ]);
    }

    /**
     * Default action to create a new model.
     * If creation is successful, the browser will be redirected to the $redirect page.
     *
     * This may be used to create actionCreate($id) as below:
     * <pre>
     * public function actionCreate()
     * {
     *     return $this->defaultActionCreate(User::class, 'index');
     * }
     * </pre>
     *
     * @param ActiveRecord|string $model A model object or fully qualified class name of model.
     * @param string|array $redirect Page to redirect if creation is successfull. If not set, it will be ['view', 'id' => $id].
     * @param callable $beforeSaveCallback Callback function with $model as parameter.
     * @param callable $afterSaveCallback Callback function with $model as parameter.
     * @return mixed
     */
    protected function defaultActionCreate($model, $redirect = NULL, $beforeSaveCallback = NULL, $afterSaveCallback = NULL)
    {
        /** @var BaseBatsgModel $model */
        if (is_string($model)) {
            $model = new $model;
        }

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($beforeSaveCallback) {
                    call_user_func($beforeSaveCallback, $model);
                }

                $model->saveThrowError();

                if ($afterSaveCallback) {
                    call_user_func($afterSaveCallback, $model);
                }

                $transaction->commit();
                $this->flashUpdateSuccess($model, TRUE);

                // Set default value of $redirect
                if ($redirect === NULL) {
                    $redirect = ['view', 'id' => $model->id];
                }
                return $this->redirect($redirect);
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->flashUpdateFail($model, TRUE);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Default action to update an existing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * This may be used to create actionUpdate($id) as below:
     * <pre>
     * public function actionUpdate($id)
     * {
     *     return $this->defaultActionUpdate($id, User::class, 'index');
     * }
     * </pre>
     *
     * @param integer $id
     * @param string $modelClass The fully qualified class name.
     * @param string|array $redirect Page to redirect if creation is successfull. If not set, it will be ['view', 'id' => $id].
     * @param callable $beforeSaveCallback Callback function with $model as parameter.
     * @param callable $afterSaveCallback Callback function with $model as parameter.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function defaultActionUpdate($id, $modelClass, $redirect = NULL, $beforeSaveCallback = NULL, $afterSaveCallback = NULL)
    {
        $model = $this->findModelById($id, $modelClass);

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($beforeSaveCallback) {
                    call_user_func($beforeSaveCallback, $model);
                }

                $model->saveThrowError();

                if ($afterSaveCallback) {
                    call_user_func($afterSaveCallback, $model);
                }

                $transaction->commit();
                $this->flashUpdateSuccess($model, FALSE);

                // Set default value of $redirect
                if ($redirect === NULL) {
                    $redirect = ['view', 'id' => $id];
                }
                return $this->redirect($redirect);
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->flashUpdateFail($model, FALSE);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * Default action to delete logically an existing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * This may be used to create actionDelete($id) as below:
     * <pre>
     * public function actionDelete($id)
     * {
     *     return $this->defaultActionDelete($id, User::class);
     * }
     * </pre>
     *
     * @param string $modelClass The fully qualified class name.
     * @param integer $id
     * @param string $redirect Page to redirect if creation is successfull, may be 'view' or 'index'.
     * @param boolean $logicalDelete Delete logically if TRUE or physically if FALSE.
     * @param string|array $redirect Page to redirect if creation is successfull. If not set, it will be ['view', 'id' => $id].
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function defaultActionDelete($id, $modelClass, $logicalDelete = TRUE, $redirect = ['index'])
    {
        $model = $this->findModelById($id, $modelClass);
        if ($logicalDelete) {
           $model->deleteLogically();
        } else {
            $model->delete();
        }
        Y::setFlashWarning((new \ReflectionClass($model))->getShortName() . ' is deleted.');

        return $this->redirect($redirect);
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param string $modelClass The fully qualified class name.
     * @return ActiveRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelById($id, $modelClass)
    {
        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Set flash when create/update model successfully.
     * @param ActiveRecord $model
     * @param boolean $isNewRecord
     */
    protected function flashUpdateSuccess($model, $isNewRecord)
    {
        $message = $isNewRecord ? ' is created successfully.' : ' is updated successfully.';
        Y::setFlashSuccess((new \ReflectionClass($model))->getShortName() . $message);
    }

    /**
     * Set flash when create/update model failure.
     * @param ActiveRecord $model
     * @param boolean $isNewRecord
     */
    protected function flashUpdateFail($model, $isNewRecord)
    {
        $message = $isNewRecord ? ' creation is fail.' : ' updating is fail.';
        Y::setFlashError((new \ReflectionClass($model))->getShortName() . $message);
    }
}