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
use yii\helpers\ArrayHelper;

//modelos
use app\models\Maquinas;
use app\models\MaquinasSearch;
use app\models\FormFiltroMaquinas;
use app\models\UsuarioDetalle;
use app\models\MantenimientoMaquina;
use app\models\Mecanico;
use app\models\ServicioMantenimiento;


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
                        $codigo = Html::encode($form->codigo_maquina);
                        $table = Maquinas::find()
                                ->andFilterWhere(['=', 'modelo', $modelo])
                                ->andFilterWhere(['like', 'codigo_maquina', $codigo])
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
        $mantenimiento = MantenimientoMaquina::find()->where(['=','id_maquina', $id])->orderBy('id_mantenimiento DESC')->all();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'mantenimiento' => $mantenimiento,
            
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
                $tipo_maquina = \app\models\TiposMaquinas::find()->where(['=','id_tipo', $model->id_tipo])->one();
                $table = new Maquinas();
                $table->id_tipo = $model->id_tipo;
                $table->codigo = $model->codigo;
                $table->serial = $model->serial;
                $table->id_marca = $model->id_marca;
                $table->codigo_maquina= $model->codigo_maquina ;
                $table->modelo = $model->modelo;      
                $table->fecha_compra = $model->fecha_compra;
                $table->fecha_ultimo_mantenimiento = $model->fecha_compra;
                //codigo que pone la fecha de mantenimiento
                $fecha = date($table->fecha_compra);
                $nuevafecha = strtotime ( '+'.$tipo_maquina->tiempo_mantenimiento.' day' , strtotime ( $fecha ) ) ;
                $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
                $table->fecha_nuevo_mantenimiento = $nuevafecha;
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
         if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
             if ($model->validate()) {
                $tipo_maquina = \app\models\TiposMaquinas::find()->where(['=','id_tipo', $model->id_tipo])->one();
                $table = Maquinas::findOne($id);
                $table->id_tipo = $model->id_tipo;
                $table->codigo = $model->codigo;
                $table->serial = $model->serial;
                $table->id_marca = $model->id_marca;
                $table->codigo_maquina= $model->codigo_maquina ;
                $table->modelo = $model->modelo;      
                $table->fecha_compra = $model->fecha_compra;
                $table->fecha_ultimo_mantenimiento = $model->fecha_compra;
                //codigo que pone la fecha de mantenimiento
                $fecha = date($table->fecha_compra);
                $nuevafecha = strtotime ( '+'.$tipo_maquina->tiempo_mantenimiento.' day' , strtotime ( $fecha ) ) ;
                $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
                $table->fecha_nuevo_mantenimiento = $nuevafecha;
                $table->save();
                return $this->redirect(['view', 'id' => $model->id_maquina]);
             }
        }
        $sw = MantenimientoMaquina::find()->where(['=','id_maquina', $id])->all();
        if(count($sw) > 0){
            Yii::$app->getSession()->setFlash('success', 'El registro no se puede modificar.');
            return $this->redirect(['index']); 
        }else{
              return $this->render('update', [
                'model' => $model,
                ]);   
        }         
    }
   
   // metodo de crea los mantenimientos
     public function actionMantenimiento_maquina($id) {
        
        $model = new MantenimientoMaquina();
        $servicio= ArrayHelper::map(ServicioMantenimiento::find()->orderBy('servicio ASC')->all(), 'id_servicio', 'servicio');
        $mecanico= ArrayHelper::map(Mecanico::find()->orderBy('nombre_completo ASC')->all(), 'id_mecanico', 'nombre_completo');
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()){
                if (isset($_POST["enviardatos"])) {
                    $maquina = Maquinas::findOne($id);
                    $tipo_servicio = ServicioMantenimiento::findOne($model->id_servicio);
                    $tipo_maquina = \app\models\TiposMaquinas::find()->where(['=','id_tipo', $maquina->id_tipo])->one();
                    $tabla = new MantenimientoMaquina();
                    $tabla->id_maquina = $id;
                    $tabla->id_servicio = $model->id_servicio;
                    $tabla->id_mecanico = $model->id_mecanico;
                    $tabla->fecha_mantenimiento = $model->fecha_mantenimiento;
                    $tabla->observacion = $model->observacion;
                    $tabla->usuario = Yii::$app->user->identity->username;
                    $tabla->save(false);
                    if($tipo_servicio->valide_fecha == 1){
                        $fecha = date($maquina->fecha_nuevo_mantenimiento);
                        $nuevafecha = strtotime ( '+'.$tipo_maquina->tiempo_mantenimiento.' day' , strtotime ( $fecha ) ) ;
                        $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
                        $maquina->fecha_ultimo_mantenimiento = $maquina->fecha_nuevo_mantenimiento;
                        $maquina->fecha_nuevo_mantenimiento = $nuevafecha;
                        $maquina->save(false); 
                    }
                    $this->redirect(["view", 'id' => $id]); 
                }
            }
        }
        return $this->renderAjax('mantenimientomecanica', [
            'model' => $model,   
            'servicio' => $servicio,
            'mecanico' => $mecanico,
            
        ]);      
    }
    
    //ESTE PROCESO EDITA UN REGISTRO DE MANTENIMIENTO
    public function actionEditarobservacion($id, $id_mto)
    {
        $model = MantenimientoMaquina::findOne($id_mto);
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()){
                if (isset($_POST["enviarobservacion"])) {
                    $tabla = MantenimientoMaquina::findOne($id_mto);
                    $tabla->observacion = $model->observacion;
                    $tabla->save(false);
                    $this->redirect(["view", 'id' => $id]); 
                }
            }
        }
         return $this->renderAjax('editarmantenimientomaquina', [
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
