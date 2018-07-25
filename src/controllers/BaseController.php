<?php
namespace batsg\controllers;

use batsg\Y;
use Yii;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
     * @param string $modelClass The fully qualified class name.
     * @param string $redirect Page to redirect if creation is successfull, may be 'view' or 'index'.
     * @param callable $beforeSaveCallback Callback function with $model as parameter.
     * @return mixed
     */
    protected function defaultActionCreate($modelClass, $redirect = 'view', $beforeSaveCallback = NULL)
    {
        /** @var BaseBatsgModel $model */
        $model = new $modelClass;

        if ($model->load(Yii::$app->request->post())) {
            if ($beforeSaveCallback) {
                call_user_func($beforeSaveCallback, $model);
            }
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->saveThrowError();
                $transaction->commit();
                $this->flashUpdateSuccess($model, TRUE);
                return $this->redirect([$redirect, 'id' => $model->id]);
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
     * @param string $redirect Page to redirect if creation is successfull, may be 'view' or 'index'.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function defaultActionUpdate($id, $modelClass, $redirect = 'view')
    {
        $model = $this->findModelById($id, $modelClass);

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->saveThrowError();
                $transaction->commit();
                $this->flashUpdateSuccess($model, FALSE);
                return $this->redirect([$redirect, 'id' => $model->id]);
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
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function defaultActionDelete($id, $modelClass, $logicalDelete = TRUE)
    {
        $model = $this->findModelById($id, $modelClass);
        if ($logicalDelete) {
           $model->deleteLogically();
        } else {
            $model->delete();
        }
        Y::setFlashWarning((new \ReflectionClass($model))->getShortName() . ' is deleted.');

        return $this->redirect(['index']);
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