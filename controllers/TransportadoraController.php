<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\ActiveQuery;
use yii\base\Model;
use yii\web\Response;
use yii\web\Session;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use Codeception\Lib\HelperModule;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
// models
use app\models\Transportadora;
use app\models\UsuarioDetalle;

/**
 * TransportadoraController implements the CRUD actions for Transportadora model.
 */
class TransportadoraController extends Controller
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
     * Lists all Transportadora models.
     * @return mixed
     */
     public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',186])->all()){
                $form = new \app\models\FiltroBusquedaProveedor();
                $nitcedula = null;
                $razon_social = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $nitcedula = Html::encode($form->nitcedula);
                        $razon_social = Html::encode($form->razon_social);
                        $table = Transportadora::find()
                                ->andFilterWhere(['like', 'nitcedula', $nitcedula])
                                ->andFilterWhere(['like', 'razon_social', $razon_social]);
                        $table = $table->orderBy('id_transportadora DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 15,
                            'totalCount' => $count->count()
                        ]);
                        $model = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                    ->all();
//                        if(isset($_POST['excel'])){                    
//                            $this->actionExcelconsultaTransportadora($tableexcel);
//                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = Transportadora::find()->orderBy('id_transportadora DESC');
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 15,
                        'totalCount' => $count->count(),
                    ]);
                    $tableexcel = $table->all();
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
//                    if(isset($_POST['excel'])){                    
//                            $this->actionExcelconsultaTransportadora($tableexcel);
//                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'model' => $model,
                            'form' => $form,
                            'pagination' => $pages,
                          
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }
    }

    /**
     * Displays a single Transportadora model.
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
     * Creates a new Transportadora model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Transportadora();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
       
        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            $dv = Html::encode($_POST["dv"]);
            $model->user_name = Yii::$app->user->identity->username;
            $model->dv =$dv;
            $model->user_name =Yii::$app->user->identity->username;
            $model->fecha_registro = date('Y-m-d');
            $model->save();
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Transportadora model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
                   
    public function actionUpdate($id)
        {
            $model = Transportadora::findOne($id);
            if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
               $dv = Html::encode($_POST["dv"]);
               $model->dv = $dv;
               $model->save();
                return $this->redirect(['index']);
            }



            return $this->render('update', [
                'model' => $model,

            ]);
    }

    /**
     * Deletes an existing Transportadora model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Transportadora model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transportadora the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transportadora::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
