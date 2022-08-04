<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\Html;
use yii\data\Pagination;
use yii\bootstrap\Modal;

//modelos
use app\models\Maquinas;
use app\models\MaquinasSearch;
use app\models\FormFiltroMaquinas;
use app\models\UsuarioDetalle;


/**
 * MaquinasController implements the CRUD actions for Maquinas model.
 */
class MaquinasController extends Controller
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
     * Lists all Maquinas models.
     * @return mixed
     */
      public function actionIndex() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 120])->all()) {
                $form = new FormFiltroMaquinas();
                $id_tipo = null;
                $id_marca = null;
                $fecha_desde = null;
                $fecha_corte = null;
                $modelo = null;
                $codigo = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_marca = Html::encode($form->id_marca);
                        $id_tipo = Html::encode($form->id_tipo);
                        $fecha_desde = Html::encode($form->fecha_desde);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $modelo = Html::encode($form->modelo);
                        $codigo = Html::encode($form->codigo);
                        $table = Maquinas::find()
                                ->andFilterWhere(['=', 'modelo', $modelo])
                                ->andFilterWhere(['=', 'codigo', $codigo])
                                ->andFilterWhere(['=', 'id_tipo', $id_tipo])
                                ->andFilterWhere(['=','id_marca', $id_marca])
                                ->andFilterWhere(['=','id_marca', $id_marca]) 
                                ->andFilterWhere(['>=','fecha_compra', $fecha_desde])
                                ->andFilterWhere(['<=','fecha_compra', $fecha_corte]); 
                        $table = $table->orderBy('id_maquina DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 40,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_maquina DESC']);
                            $this->actionExcelconsultaMaquinas($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = Maquinas::find()
                             ->orderBy('id_maquina DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 40,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelconsultaMaquinas($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }

    /**
     * Displays a single Maquinas model.
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
     * Creates a new Maquinas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Maquinas();

       if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $table = new Maquinas();
                $table->id_tipo = $model->id_tipo;
                $table->codigo = $model->codigo;
                $table->serial = $model->serial;
                $table->id_marca = $model->id_marca;
                $table->modelo = $model->modelo;      
                $table->fecha_compra = $model->fecha_compra;
                $table->usuario =  Yii::$app->user->identity->username;
                if($table->save(false)){;
                   return $this->redirect(["maquinas/index"]);
                }else{
                    Yii::$app->getSession()->setFlash('error', 'Error al grabar el registro en la base de datos');
                }   

            } else {
                $model->getErrors();
            } 
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Maquinas model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_maquina]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * Finds the Maquinas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Maquinas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Maquinas::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
