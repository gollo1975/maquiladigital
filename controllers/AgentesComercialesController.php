<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Session;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use Codeception\Lib\HelperModule;
use kartik\date\DatePicker;
use kartik\time\TimePicker;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;


//MODELS
use app\models\AgentesComerciales;
use app\models\UsuarioDetalle;
use app\models\Departamento;
use app\models\Municipio;
use app\models\FiltroBusquedaAgentes;

/**
 * AgentesComercialesController implements the CRUD actions for AgentesComerciales model.
 */
class AgentesComercialesController extends Controller
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
     * Lists all AgentesComerciales models.
     * @return mixed
     */
     public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',177])->all()){
                $form = new FiltroBusquedaAgentes();
                $documento = null;
                $cargo = null;
                $estado = null;
                $nombre_completo = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $documento = Html::encode($form->documento);
                        $nombre_completo = Html::encode($form->nombre_completo);
                        $cargo = Html::encode($form->cargo);
                        $estado = Html::encode($form->estado);
                        $table = AgentesComerciales::find()
                                ->andFilterWhere(['=', 'nit_cedula', $documento])
                                ->andFilterWhere(['like', 'nombre_completo', $nombre_completo])
                                ->andFilterWhere(['=', 'estado', $estado]);
                        $table = $table->orderBy('id_agente DESC');
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
                        if(isset($_POST['excel'])){                    
                            $this->actionExcelconsultaAgentes($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = AgentesComerciales::find()
                            ->orderBy('id_agente desc');
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
                    if(isset($_POST['excel'])){                    
                            $this->actionExcelconsultaAgentes($tableexcel);
                    }
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
     * Displays a single AgentesComerciales model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    
   
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            ''
        ]);
    }

    /**
     * Creates a new AgentesComerciales model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AgentesComerciales();

        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $table = new AgentesComerciales();
            $dv = Html::encode($_POST["dv"]);
            $table->id_tipo_documento = $model->id_tipo_documento;
            $table->nit_cedula = $model->nit_cedula;
            $table->dv = $dv;
            $table->primer_nombre = $model->primer_nombre;
            $table->segundo_nombre = $model->segundo_nombre;
            $table->primer_apellido = $model->primer_apellido;
            $table->segundo_apellido = $model->segundo_apellido;
            $table->nombre_completo = strtoupper($model->primer_nombre .' '. $model->segundo_nombre . ' '. $model->primer_apellido .' '. $model->segundo_apellido);
            $table->direccion = $model->direccion;
            $table->email_agente = $model->email_agente;
            $table->celular_agente = $model->celular_agente;
            $table->iddepartamento = $model->iddepartamento;
            $table->idmunicipio = $model->idmunicipio;
            $table->fecha_registro = date('Y-m-d H:i:s');
            $table->user_name = Yii::$app->user->identity->username;
            $table->save(false);
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'sw' => 0,
        ]);
    }

    /**
     * Updates an existing AgentesComerciales model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'sw' => 1,
        ]);
    }

    /**
     * Deletes an existing AgentesComerciales model.
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
     * Finds the AgentesComerciales model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AgentesComerciales the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AgentesComerciales::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
