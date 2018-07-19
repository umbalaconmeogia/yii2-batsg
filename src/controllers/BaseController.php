<?php
namespace batsg\controllers;

use yii\web\Controller;
use Yii;
use batsg\Y;
use yii\web\NotFoundHttpException;
use yii\db\ActiveRecord;

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
     * Default action to list all Project models.
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
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function defaultActionView($id, $modelClass)
    {
        return $this->render('view', [
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
     * @return mixed
     */
    protected function defaultActionCreate($modelClass, $redirect = 'view')
    {
        $model = new $modelClass;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Y::setFlashSuccess((new \ReflectionClass($model))->getShortName() . ' is created successfully.');
                return $this->redirect([$redirect, 'id' => $model->id]);
            } else {
                Y::setFlashError((new \ReflectionClass($model))->getShortName() . ' creation is fail.');
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
            if ($model->save()) {
                Y::setFlashSuccess((new \ReflectionClass($model))->getShortName() . ' is updated successfully.');
                return $this->redirect([$redirect, 'id' => $model->id]);
            } else {
                Y::setFlashError((new \ReflectionClass($model))->getShortName() . ' updating is fail.');
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
}