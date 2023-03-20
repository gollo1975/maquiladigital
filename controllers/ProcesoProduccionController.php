<?php

namespace app\controllers;

use Yii;
use app\models\ProcesoProduccion;
use app\models\ProcesoProduccionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UsuarioDetalle;

/**
 * ProcesoProduccionController implements the CRUD actions for ProcesoProduccion model.
 */
class ProcesoProduccionController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ProcesoProduccion models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',28])->all()){
                $searchModel = new ProcesoProduccionSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }
    }

    /**
     * Displays a single ProcesoProduccion model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProcesoProduccion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProcesoProduccion();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) { 
                $table = new ProcesoProduccion();
                $table->proceso = $model->proceso;
                if ($model->estado == 1){
                     $table->segundos = $model->segundos;
                     $table->minutos = round($table->segundos /60, 2);
                    $table->estandarizado = $model->estandarizado;
                    $table->save(false);
                    return $this->redirect(['index']);
                }else{
                    $table->minutos = $model->minutos;
                    $table->segundos = round($table->minutos * 60);
                    $table->estandarizado = $model->estandarizado;
                    $table->save(false);
                    return $this->redirect(['index']);
                }
            }else{
                   $model->getErrors();
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProcesoProduccion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = new ProcesoProduccion();

        if ($model->load(Yii::$app->request->post())) {
            $table = ProcesoProduccion::find()->where(['idproceso' => $id])->one();
            $table->proceso = $model->proceso;
            if($model->estado == 1){
                $table->segundos = $model->segundos;
                $table->minutos = round( $table->segundos / 60, 2);
                $table->estandarizado = $model->estandarizado;
                $table->save(false);
                return $this->redirect(['index']);
            }else{
                $table->minutos = $model->minutos;
                $table->segundos = round($table->minutos * 60);
                $table->estandarizado = $model->estandarizado;
                $table->save(false);
                return $this->redirect(['index']);
            }    
        }
        if (Yii::$app->request->get("id")) {
          $table = ProcesoProduccion::find()->where(['idproceso' => $id])->one();
            if($table){ 
                $model->proceso =   $table->proceso;
                $model->segundos = $table->segundos;
                $model->minutos = $table->minutos;
                $model->estandarizado = $table->estandarizado;
            }else{
                return $this->redirect(['index']);
            }
        } 
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProcesoProduccion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
            $this->redirect(["proceso-produccion/index"]);
        } catch (IntegrityException $e) {
            $this->redirect(["proceso-produccion/index"]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar la operación, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar la operación, tiene registros asociados en otros procesos');
            $this->redirect(["proceso-produccion/index"]);
        }
    }

    /**
     * Finds the ProcesoProduccion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProcesoProduccion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProcesoProduccion::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
