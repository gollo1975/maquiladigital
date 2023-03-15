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

use app\models\PagoBanco;
use app\models\PagoBancoSearch;
use app\models\UsuarioDetalle;
use app\models\FormFiltroBanco;
use app\models\PagoBancoDetalle;
/**
 * PagoBancoController implements the CRUD actions for PagoBanco model.
 */
class PagoBancoController extends Controller
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
     * Lists all PagoBanco models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 129])->all()) {
                $form = new FormFiltroBanco();
                $id_banco = null;
                $tipo_pago = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $tipo_proceso = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_banco = Html::encode($form->id_banco);
                        $tipo_pago = Html::encode($form->tipo_pago);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $tipo_proceso = Html::encode($form->tipo_proceso);
                        $table = PagoBanco::find()
                                ->andFilterWhere(['=', 'id_banco', $id_banco])
                                ->andFilterWhere(['=', 'tipo_pago', $tipo_pago])
                                ->andFilterWhere(['>=', 'fecha_creacion', $fecha_inicio])
                                ->andFilterWhere(['<=','fecha_creacion', $fecha_corte])
                                ->andFilterWhere(['=', 'tipo_proceso', $tipo_proceso]);
                        $table = $table->orderBy('id_pago_banco DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 20,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_pago_banco DESC']);
                            $this->actionExcelconsultaPagoBanco($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = PagoBanco::find()
                             ->orderBy('id_pago_banco DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 20,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                         $this->actionExcelconsultaPagoBanco($tableexcel);
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
     * Displays a single PagoBanco model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
       $listado = PagoBancoDetalle::find()->where(['=','id_pago_banco', $id])->orderBy('nombres ASC')->all();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'listado' => $listado,
        ]);
    }

    /**
     * Creates a new PagoBanco model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PagoBanco();
        $empresa = \app\models\Matriculaempresa::findOne(1);
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {           
            if ($model->validate()) {
                $table = new PagoBanco();
                $table->id_banco = $model->id_banco;
                $table->tipo_pago = $model->tipo_pago;
                $table->id_tipo_nomina = $model->id_tipo_nomina;
                $table->aplicacion = $model->aplicacion;
                $table->secuencia = $model->secuencia;
                $table->fecha_creacion = $model->fecha_creacion;
                $table->fecha_aplicacion = $model->fecha_aplicacion;
                $table->descripcion = $model->descripcion; 
                $table->usuario =  Yii::$app->user->identity->username;
                $table->nit_empresa = $empresa->id;
                $table->nit = $empresa->nitmatricula;
                $table->save(false);
                return $this->redirect(['index']);
            }else{
                 $model->getErrors();
            }     
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PagoBanco model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
  // proceso que busca los operarios para pago
      public function actionNuevopagoperario($id, $tipo_proceso)
    {
        if($tipo_proceso == 1 || $tipo_proceso == 2 ||  $tipo_proceso == 3){
            $listadoPago = \app\models\ProgramacionNomina::find()->where(['=','pago_aplicado', 0])
                                                                 ->andWhere(['=','id_tipo_nomina', $tipo_proceso])->orderBy('id_programacion ASC')->all();
            $form = new \app\models\FormMaquinaBuscar();
            $q = null;
            $mensaje = '';
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $q = Html::encode($form->q);                                
                    if ($q){
                        $listadoPago = \app\models\ProgramacionNomina::find()
                                ->where(['like','cedula_empleado',$q])
                                ->andWhere(['=','pago_aplicado', 0])
                                ->orderBy('id_programacion ASC')
                                ->all();
                    }               
                } else {
                    $form->getErrors();
                }                    

            } else {
                 $listadoPago = \app\models\ProgramacionNomina::find()->where(['=','pago_aplicado', 0])
                                                                 ->andWhere(['=','id_tipo_nomina', $tipo_proceso])->orderBy('id_programacion ASC')->all();
            }
        }
        if($tipo_proceso == 7){
            $listadoPago = \app\models\PagoNominaServicios::find()->where(['=','pago_aplicado', 0])->orderBy('id_planta ASC')->all();
            $form = new \app\models\FormMaquinaBuscar();
            $q = null;
            $mensaje = '';
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $q = Html::encode($form->q);                                
                    if ($q){
                        $listadoPago = \app\models\PagoNominaServicios::find()
                                ->where(['like','documento',$q])
                                ->orwhere(['like','operario',$q])
                                ->andWhere(['=','pago_aplicado', 0])
                                ->orderBy('id_pago DESC')
                                ->all();
                    }               
                } else {
                    $form->getErrors();
                }                    

            } else {
                $listadoPago = \app\models\PagoNominaServicios::find()->where(['=','pago_aplicado', 0])->orderBy('id_planta ASC')->all();
            }
        }    
        if (isset($_POST["aplicar_pago"])) {
            $intIndice = 0;
            foreach ($_POST["aplicar_pago"] as $intCodigo) {
                $pago_banco = PagoBanco::findOne($id);
                //proceso de nomina
                if($tipo_proceso == 1 || $tipo_proceso ==  2 || $tipo_proceso == 3){
                    $nomina = \app\models\ProgramacionNomina::find()->where(['id_programacion' => $intCodigo])->one();
                    $empleado = \app\models\Empleado::findOne($nomina->id_empleado);
                    $table = new PagoBancoDetalle();
                    $detalle = PagoBancoDetalle::find()
                        ->where(['=', 'id_pago_banco', $id])
                        ->andWhere(['=', 'documento', $nomina->cedula_empleado])
                        ->all();
                    $reg = count($detalle);
                    if ($reg == 0) {
                        $table->id_pago_banco = $id;
                        $table->tipo_documento = $empleado->tipoDocumento->codigo_interfaz;
                        $table->documento = $nomina->cedula_empleado;
                        $table->nombres = utf8_decode($nomina->empleado->nombrecorto);
                        $table->tipo_transacion = $empleado->tipo_transacion;
                        $table->codigo_banco = $empleado->bancoEmpleado->codigo_interfaz;
                        $table->numero_cuenta = $empleado->cuenta_bancaria;
                        $table->valor_transacion = $nomina->total_pagar;
                        $table->fecha_aplicacion = $pago_banco->fecha_aplicacion;
                        $table->tipo_pago = $tipo_proceso;
                        $table->id_colilla = $intCodigo;
                        $table->save(false); 
                         $this->ActualizarOperarioTotales($id);
                    }
                }
                                
                //proceso de prestacion de servicio
                if($pago_banco->id_tipo_nomina == 7){
                    $nomina = \app\models\PagoNominaServicios::find()->where(['id_pago' => $intCodigo])->one();
                    $operario = \app\models\Operarios::findOne($nomina->id_operario);
                    $table = new PagoBancoDetalle();
                    $detalle = PagoBancoDetalle::find()
                        ->where(['=', 'id_pago_banco', $id])
                        ->andWhere(['=', 'documento', $nomina->documento])
                        ->all();
                    $reg = count($detalle);
                    if ($reg == 0) {
                        $table->id_pago_banco = $id;
                        $table->tipo_documento = $operario->tipoDocumento->codigo_interfaz;
                        $table->documento = $nomina->documento;
                        $table->nombres = utf8_decode($nomina->operario);
                        $table->tipo_transacion = $operario->tipo_transacion;
                        $table->codigo_banco = $operario->bancoEmpleado->codigo_interfaz;
                        $table->numero_cuenta = $operario->numero_cuenta;
                        $table->valor_transacion = $nomina->Total_pagar;
                        $table->fecha_aplicacion = $pago_banco->fecha_aplicacion;
                        $table->tipo_pago = $tipo_proceso;
                        $table->id_colilla = $intCodigo;
                        $table->save(false); 
                        $this->ActualizarOperarioTotales($id);
                    }
                }
             $intIndice++;   
            }
           $this->redirect(["pago-banco/view", 'id' => $id]);
        }else{

        }
        return $this->render('_listado_operario', [
            'listadoPago' => $listadoPago,            
            'mensaje' => $mensaje,
            'id' => $id,
            'form' => $form,
            'tipo_proceso' => $tipo_proceso,

        ]);
    }
    
   //proceso de contar los empleados y sumar valores
    protected function ActualizarOperarioTotales($id)
    {
        $banco = PagoBancoDetalle::find()->where(['=','id_pago_banco', $id])->all();
        $pago = PagoBanco::findOne($id);
        $sumarEmpleado = 0;
        $sumarPago = 0;
        foreach ($banco as $bancos):
            $sumarEmpleado += 1;
            $sumarPago += $bancos->valor_transacion;
        endforeach;
        $pago->total_empleados = $sumarEmpleado;
        $pago->total_pagar = $sumarPago;
        $pago->save(false);
    }
    //ELIMINAR DETALLE DE PAGO
      public function actionEliminar_pago_banco()
    {
        if(Yii::$app->request->post())
        {
            $iddetalle = Html::encode($_POST["id_detalle"]);
            $id = Html::encode($_POST["id_banco"]);
            if((int) $iddetalle){
                if(PagoBancoDetalle::deleteAll("id_detalle=:id_detalle", [":id_detalle" => $iddetalle])){
                    $this->ActualizarOperarioTotales($id);
                    $this->redirect(["pago-banco/view",'id' => $id]);
                }else{
                    echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("pago-banco/index")."'>";
                }
            }else{
                echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("pago-banco/index")."'>";
            }
        }else{
            return $this->redirect(["pago-banco/index"]);
        }
    }
    //PROCESO QUE ELIMINA TODO
     public function actionEliminartododetalle($id)
    {
        $detalles = PagoBancoDetalle::find()->where(['=', 'id_pago_banco', $id])->orderBy('nombres ASC')->all();
        if(Yii::$app->request->post())
        {
            $intIndice = 0;
            if (isset($_POST["seleccion"])) {
                foreach ($_POST["seleccion"] as $intCodigo)
                {
                    $costodetalle = PagoBancoDetalle::findOne($intCodigo);
                    if(PagoBancoDetalle::deleteAll("id_detalle=:id_detalle", [":id_detalle" => $intCodigo]))
                    {
                       
                    }
                }
                $this->ActualizarOperarioTotales($id);
                $this->redirect(["pago-banco/view",'id' => $id]);
            }else {
                Yii::$app->getSession()->setFlash('warning', 'Se debe de seleccin al menos un registro para el proceso.');
            }
        }
        return $this->render('_formeliminartodopago', [
            'detalles' => $detalles,
            'id' => $id,
        ]);
    }
    
    
    //proceso de autorizacion
    
        public function actionAutorizado($id) {
        $model = $this->findModel($id);
        if($model->autorizado == 0){
            $pago = PagoBancoDetalle::find()->where(['=','id_pago_banco', $id])->all();
            if (count($pago)> 0) {
                $model->autorizado = 1;
                $model->update();
                $this->redirect(["pago-banco/view", 'id' => $id]);
            } else {
                    Yii::$app->getSession()->setFlash('error', 'Para autorizar el registro debe de programaar el listado de pagos de nómina.');
                    $this->redirect(["pago-banco/view", 'id' => $id]);
            }
        } else {
                $model->autorizado = 0;
                $model->update();
                $this->redirect(["pago-banco/view", 'id' => $id]);
        }
    }
    //CERRAR PROCESO
    
    public function actionClose_cast($id, $tipo_proceso)
    {
     $model = $this->findModel($id);
     $detalle = PagoBancoDetalle::find()->where(['=','id_pago_banco', $id])->all();
     foreach ($detalle as $detalles):
        
        if($tipo_proceso == 7){ 
            $nomina = \app\models\PagoNominaServicios::find()->where(['=','id_pago', $detalles->id_colilla])->andWhere(['=','pago_aplicado', 0])->one();
            if($nomina){
                $nomina->pago_aplicado = 1;             
                $nomina->save(false); 
             }
        }     
        if($tipo_proceso == 1 || $tipo_proceso == 2 || $tipo_proceso == 3){ 
            $nomina = \app\models\ProgramacionNomina::find()->where(['=','id_programacion', $detalles->id_colilla])->andWhere(['=','pago_aplicado', 0])->one();
            if($nomina){
                $nomina->pago_aplicado = 1;             
                $nomina->save(false); 
             }
        }   
     endforeach;
     $model->cerrar_proceso= 1;
     $model->update();
     $this->redirect(["pago-banco/view", 'id' => $id, 'tipo_proceso' => $tipo_proceso]);
    }
    
    protected function findModel($id)
    {
        if (($model = PagoBanco::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
