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
use app\models\PagoBancoDetalle;
use app\models\PagoBancoSearch;
use app\models\UsuarioDetalle;
use app\models\FormFiltroBanco;
use app\models\Operarios;
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
    public function actionIndex($token = 0) {
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
                                ->andFilterWhere(['=', 'id_tipo_nomina', $tipo_proceso]);
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
                            'token' => $token,
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
    public function actionView($id, $token)
    {
       $listado = PagoBancoDetalle::find()->where(['=','id_pago_banco', $id])->orderBy('nombres ASC')->all();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'listado' => $listado,
            'token' => $token,
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
                $table->codigo_oficina = $empresa->bancoFactura->codigo_oficina;
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
      public function actionNuevopagoperario($id, $tipo_proceso, $token)
    {
        if($tipo_proceso == 1 || $tipo_proceso == 2 ||  $tipo_proceso == 3){ //nominas, primas y cesantias personal vinculado
            $listadoPago = \app\models\ProgramacionNomina::find()->where(['=','pago_aplicado', 0])
                                                                 ->andWhere(['=','id_tipo_nomina', $tipo_proceso])->orderBy('id_programacion ASC')->all();
            $form = new \app\models\FormMaquinaBuscar();
            $nombres = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $nombres = Html::encode($form->nombres);
                    if ($nombres){
                        $listadoPago = \app\models\ProgramacionNomina::find()
                                ->where(['=','id_empleado', $nombres])
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
        
        if($tipo_proceso == 7){ //personal de prestacion del servicio
            $listadoPago = \app\models\PagoNominaServicios::find()->where(['=','pago_aplicado', 0])->orderBy('id_planta ASC')->all();
            $form = new \app\models\FormMaquinaBuscar();
            $nombres = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $nombres = Html::encode($form->$nombres);                                
                    if ($nombres){
                        $listadoPago = \app\models\PagoNominaServicios::find()
                                ->where(['like','documento',$nombres])
                                ->orwhere(['like','operario',$nombres])
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
        
        if($tipo_proceso == 4){ // pago de prestaciones sociales masivas
            $listadoPago = \app\models\PrestacionesSociales::find()->where(['=','pago_aplicado', 0])->orderBy('id_prestacion DESC')->all();
            $form = new \app\models\FormMaquinaBuscar();
            $fecha_inicio = null;
            $fecha_corte = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $fecha_inicio = Html::encode($form->fecha_inicio);                                
                    $fecha_corte = Html::encode($form->fecha_corte); 
                    if(empty($fecha_inicio) && empty($fecha_corte)){
                       Yii::$app->getSession()->setFlash('warning', 'Campos FECHA DE INICIO Y FECHA DE CORTE no se puede ser vacios.');
                       return $this->redirect(["pago-banco/nuevopagoperario", 'id' => $id, 'token' => $token,'tipo_proceso' => $tipo_proceso]);
                    }
                    $listadoPago = \app\models\PrestacionesSociales::find()
                                ->where(['between','fecha_termino_contrato',$fecha_inicio, $fecha_corte])
                                ->andWhere(['=','pago_aplicado', 0])
                                ->orderBy('id_prestacion DESC')
                                ->all();
                } else {
                    $form->getErrors();
                }                    

            } else {
                $listadoPago = \app\models\PrestacionesSociales::find()->where(['=','pago_aplicado', 0])->orderBy('id_prestacion DESC')->all();
            }
        } 
        if($tipo_proceso == 5){
            Yii::$app->getSession()->setFlash('error', 'El tipo de servicio que selecciono no esta disponible para esta empresa. Valide la informacion.');
            return $this->redirect(["pago-banco/view", 'id' => $id, 'token' => $token]);
        }
        if (isset($_POST["aplicar_pago"])) {
            $intIndice = 0;
            $banco = PagoBanco::findOne($id);
            foreach ($_POST["aplicar_pago"] as $intCodigo) {
                $pago_banco = PagoBanco::findOne($id);
                
                 //proceso de nomina, CESANTIAS Y PRIMAS
                if($tipo_proceso == 1 || $tipo_proceso ==  2 || $tipo_proceso == 3){ //nominas, cesantias y primas personal vincualdo
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
                        $table->concepto_documento = $empleado->tipoDocumento->tipo;
                        if($empleado->homologar_document == 0){
                            $table->documento = $nomina->cedula_empleado;
                        }else{
                           $table->documento = $empleado->documento_pago_banco;
                        }    
                        $table->nombres = utf8_decode(mb_substr($nomina->empleado->nombrecorto, 0, 20));
                        $table->tipo_transacion = $empleado->tipo_transacion;
                        if($banco->codigo_oficina != 'null'){
                            $table->codigo_banco = $empleado->bancoEmpleado->codigo_bogota; 
                        }else{
                            $table->codigo_banco = $empleado->bancoEmpleado->codigo_interfaz;
                        }
                        $table->banco = $empleado->bancoEmpleado->banco;
                        $table->numero_cuenta = $empleado->cuenta_bancaria;
                        $table->valor_transacion = $nomina->total_pagar;
                        $table->fecha_aplicacion = $pago_banco->fecha_aplicacion;
                        $table->tipo_pago = $tipo_proceso;
                        $table->id_colilla = $intCodigo;
                        $table->save(false); 
                        $this->ActualizarOperarioTotales($id);
                    }
                }
                
                if($tipo_proceso == 4){ //pago de prestaciones sociales
                    $nomina = \app\models\PrestacionesSociales::find()->where(['id_prestacion' => $intCodigo])->one();
                    $empleado = \app\models\Empleado::findOne($nomina->id_empleado);
                   
                    $table = new PagoBancoDetalle();
                    $detalle = PagoBancoDetalle::find()
                        ->where(['=', 'id_pago_banco', $id])
                        ->andWhere(['=', 'documento', $nomina->documento])
                        ->all();
                    $reg = count($detalle);
                    if ($reg == 0) {
                        $table->id_pago_banco = $id;
                        $table->tipo_documento = $empleado->tipoDocumento->codigo_interfaz;
                        $table->concepto_documento = $empleado->tipoDocumento->tipo;
                        if($empleado->homologar_document == 0){
                            $table->documento = $nomina->documento;
                        }else{
                           $table->documento = $empleado->documento_pago_banco;
                        }    
                        $table->nombres = utf8_decode(mb_substr($nomina->empleado->nombrecorto, 0, 20));
                        $table->tipo_transacion = $empleado->tipo_transacion;
                       
                        if($banco->codigo_oficina != null){
                            $table->codigo_banco = $empleado->bancoEmpleado->codigo_bogota; 
                        }else{
                            $table->codigo_banco = $empleado->bancoEmpleado->codigo_interfaz;
                        }
                        $table->banco = $empleado->bancoEmpleado->banco;
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
                        $table->concepto_documento = $operario->tipoDocumento->tipo;
                        $table->documento = $nomina->documento;
                        $table->nombres = utf8_decode(mb_substr($nomina->operario,0, 20));
                        $table->tipo_transacion = $operario->tipo_transacion;
                        if($banco->codigo_oficina != null){
                            $table->codigo_banco = $empleado->bancoEmpleado->codigo_bogota; 
                        }else{
                            $table->codigo_banco = $empleado->bancoEmpleado->codigo_interfaz;
                        }
                        $table->banco = $operario->bancoEmpleado->banco;
                        $table->numero_cuenta = $operario->numero_cuenta;
                        $table->valor_transacion = $nomina->total_pagar;
                        $table->fecha_aplicacion = $pago_banco->fecha_aplicacion;
                        $table->tipo_pago = $tipo_proceso;
                        $table->id_colilla = $intCodigo;
                        $table->save(false); 
                        $this->ActualizarOperarioTotales($id);
                    }
                }
             $intIndice++;   
            }
           return $this->redirect(["pago-banco/view", 'id' => $id, 'token' => $token]);
        }else{

        }
        return $this->render('_listado_operario', [
            'listadoPago' => $listadoPago,            
            'id' => $id,
            'form' => $form,
            'tipo_proceso' => $tipo_proceso,
            'token' => $token,

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
      public function actionEliminar_pago_banco($token)
    {
        if(Yii::$app->request->post())
        {
            $iddetalle = Html::encode($_POST["id_detalle"]);
            $id = Html::encode($_POST["id_banco"]);
            if((int) $iddetalle){
                if(PagoBancoDetalle::deleteAll("id_detalle=:id_detalle", [":id_detalle" => $iddetalle])){
                    $this->ActualizarOperarioTotales($id);
                    $this->redirect(["pago-banco/view",'id' => $id, 'token' => $token]);
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
     public function actionEliminartododetalle($id, $tipo_proceso, $token)
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
                $this->redirect(["pago-banco/view",'id' => $id,  'tipo_proceso' => $tipo_proceso, 'token' => $token]);
            }else {
                Yii::$app->getSession()->setFlash('warning', 'Se debe de seleccin al menos un registro para el proceso.');
            }
        }
        return $this->render('_formeliminartodopago', [
            'detalles' => $detalles,
            'id' => $id,
            'tipo_proceso' => $tipo_proceso,
            'token' => $token,
        ]);
    }
    
    
    //proceso de autorizacion
    
        public function actionAutorizado($id, $token) {
        $model = $this->findModel($id);
        if($model->autorizado == 0){
            $pago = PagoBancoDetalle::find()->where(['=','id_pago_banco', $id])->all();
            if (count($pago)> 0) {
                $model->autorizado = 1;
                $model->update();
                $this->redirect(["pago-banco/view", 'id' => $id, 'token' => $token]);
            } else {
                    Yii::$app->getSession()->setFlash('error', 'Para autorizar el registro debe de programaar el listado de pagos de nómina.');
                    $this->redirect(["pago-banco/view", 'id' => $id, 'token' => $token]);
            }
        } else {
                $model->autorizado = 0;
                $model->update();
                $this->redirect(["pago-banco/view", 'id' => $id, 'token' => $token]);
        }
    }
    //CERRAR PROCESO
    
    public function actionClose_cast($id, $tipo_proceso, $token)
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
        if($tipo_proceso == 4){ 
            $nomina = \app\models\PrestacionesSociales::find()->where(['=','id_prestacion', $detalles->id_colilla])->andWhere(['=','pago_aplicado', 0])->one();
            if($nomina){
                $nomina->pago_aplicado = 1;             
                $nomina->save(); 
             }
        }   
     endforeach;
     $model->cerrar_proceso= 1;
     $model->update();
     $this->redirect(["pago-banco/view", 'id' => $id, 'tipo_proceso' => $tipo_proceso, 'token' => $token]);
    }
    
    protected function findModel($id)
    {
        if (($model = PagoBanco::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    //ARCHIVO QUE PERMITE IMPRIMIR EL PDF
   public function actionImprimir_reporte($id, $token, $tipo_proceso)
    {
    Yii::$app->getSession()->setFlash('info', 'Este proceso esta en desarrollo. Solicitar la aprobacion al administrador.');
    $this->redirect(["pago-banco/view", 'id' => $id, 'token' => $token, 'tipo_proceso'=> $tipo_proceso]);
    /*  return $this->render('../formatos/reciboCaja', [
            'model' => $this->findModel($id),
        ]);*/
    }
    //GENERAR ARCHIVOS TXT PAB
    
    public function actionPagoarchivopab($id) {
       
        $empresa = \app\models\Matriculaempresa::findOne(1);
        $pago_banco = PagoBanco::findOne($id);
        ob_clean();
        $fijo = 6;
        $letra = 'S';
        $fechaPago = date('YmdHis');
        $tipoArchivo = 'PAB';
        $secuencia = $pago_banco->secuencia;
        $nombreArchivo = $tipoArchivo.$empresa->nitmatricula.$secuencia.$fechaPago; 
        $strArchivo = "$nombreArchivo".".txt";                
        $ar = fopen($strArchivo, "a+") or die("Problemas en la creacion del archivo plano");              
        //Linea inicial
        fputs($ar, $this->RellenarNr($pago_banco->nit_empresa, "1", 1));  
        fputs($ar, $this->RellenarNr($pago_banco->nit, "0", 15));
        fputs($ar, $pago_banco->aplicacion, 2);
        fputs($ar, $this->RellenarNr($pago_banco->tipo_pago, " ", 18));
        fputs($ar, str_pad(utf8_decode($pago_banco->descripcion), 10)); 
        fputs($ar, date("Ymd", strtotime($pago_banco->fecha_creacion)));
        fputs($ar, $pago_banco->secuencia);
        fputs($ar, date("Ymd", strtotime($pago_banco->fecha_aplicacion)));
        fputs($ar, $this->RellenarNr($pago_banco->total_empleados, "0", 6));
        fputs($ar, $this->RellenarNr($pago_banco->debitos, "0", 17));
        fputs($ar, $this->RellenarNr(round($pago_banco->total_pagar.$pago_banco->adicion_numero), "0", 17));
        fputs($ar, $this->RellenarNr($empresa->bancoFactura->numerocuenta, "0", 11));
        fputs($ar, $empresa->bancoFactura->producto, 1);
        fputs($ar, "\n");
        //fin linea
        $detalle_pago = PagoBancoDetalle::find()->where(['=','id_pago_banco', $id])->orderBy('nombres ASC')->all(); 
        foreach ($detalle_pago as $pago):
            fputs($ar, $fijo);  
            if(mb_strlen($pago->documento) == 6){
              fputs($ar, $pago->documento);                
              fputs($ar, "         ");
            }
            if(mb_strlen($pago->documento) == 7){
              fputs($ar, $pago->documento);                
              fputs($ar, "        ");
            }
             if(mb_strlen($pago->documento) == 8){
              fputs($ar, $pago->documento);                
              fputs($ar, "       ");
            }
            
            if(mb_strlen($pago->documento) == 10){
              fputs($ar, $pago->documento);                
              fputs($ar, "     ");
            }
            fputs($ar, str_pad(utf8_decode($pago->nombres), 30));
            fputs($ar, $this->RellenarNr($pago->codigo_banco, "0", 9));
            //campos para cuentas bancarias
            if(strlen($pago->numero_cuenta) == 12){
                fputs($ar, $pago->numero_cuenta);
                fputs($ar, "     ");
            }
            if(strlen($pago->numero_cuenta) == 11){
                fputs($ar, $pago->numero_cuenta);
                fputs($ar, "      ");
            }
            if(strlen($pago->numero_cuenta) == 10){
                fputs($ar, $pago->numero_cuenta);
                fputs($ar, "       ");
            }
            if(strlen($pago->numero_cuenta) == 9){
                fputs($ar, $pago->numero_cuenta);
                fputs($ar, "        ");
            }
            if(strlen($pago->numero_cuenta) == 8){
                fputs($ar, $pago->numero_cuenta);
                fputs($ar, "         ");
            }
           
            fputs($ar, $letra);  
            fputs($ar, $pago->tipo_transacion, 2);
            fputs($ar, $this->RellenarNr(round($pago->valor_transacion.$pago_banco->adicion_numero), "0", 17));
            fputs($ar, date("Ymd", strtotime($pago_banco->fecha_aplicacion)));
            fputs($ar, "                     ");
            fputs($ar, $pago->tipo_documento, 1);
            fputs($ar, "00000");
            fputs($ar, "\n");
        endforeach;
        fclose($ar);
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv; charset=ISO-8859-15');
        header('Content-Disposition: attachment; filename='.basename($strArchivo));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($strArchivo));
        readfile($strArchivo);
        unlink($strArchivo);
        exit;
    }
    
    //ARCHIVO PARA BANCO DE BOGOTA
    public function actionPagoarchivobogota($id) {
        $model = PagoBanco::findOne($id);
        $detalles = PagoBancoDetalle::find()->where(['id_pago_banco' => $id])->all();

        $filas = [];
        $totalCuerpo = 0;

        // 1. PROCESAR REGISTROS DE MOVIMIENTO (Tipo 2) - Longitud: 250
        foreach ($detalles as $pago) {
            $valorNumerico = round($pago->valor_transacion, 2);
            $totalCuerpo += $valorNumerico;

            $linea = "2"; // 1-1: Tipo Registro (9(1))
            $linea .= str_pad(substr($pago->concepto_documento ?? 'C', 0, 1), 1, "C"); // 2-2: Tipo ID (X(1))
            $linea .= str_pad(preg_replace('/[^0-9]/', '', $pago->documento), 11, "0", STR_PAD_LEFT); // 3-13: ID (9(11))

            $nombre = str_replace(['Á','É','Í','Ó','Ú','Ñ'], ['A','E','I','O','U','N'], strtoupper($pago->nombres));
            $linea .= str_pad(substr($nombre, 0, 40), 40, " ", STR_PAD_RIGHT); // 14-53: Nombre (X(40))

            $linea .= "02"; // 54-55: Tipo Cuenta (9(2))
            $cuentaDestino = preg_replace('/[^0-9]/', '', $pago->numero_cuenta);
            $linea .= str_pad($cuentaDestino, 17, " ", STR_PAD_RIGHT); // 56-72: Cuenta (X(17))

            $linea .= $this->formatearValorBogota($valorNumerico, 18); // 73-90: Valor (9(18))
            $linea .= "A"; // 91-91: Forma Pago (X(1))
            $linea .= str_pad($model->codigo_oficina ?? '000', 3, "0", STR_PAD_LEFT); // 92-94: Ofic (9(3))
            $linea .= str_pad($pago->codigo_banco, 3, "0", STR_PAD_LEFT); // 95-97: Banco (9(3))
            $linea .= str_pad($model->nitEmpresa->municipio->codigomunicipio ?? '0001', 4, "0", STR_PAD_LEFT); // 98-101: Ciudad (9(4))

            $linea .= str_pad("PAGO DE NOMINA", 80, " ", STR_PAD_RIGHT); // 102-181: Comentarios (X(80))
            $linea .= "0"; // 182-182: Valor en cero (9(1))
            $linea .= str_pad($pago->id_pago_banco, 10, "0", STR_PAD_LEFT); // 183-192: Factura (9(10))
            $linea .= "N"; // 193-193: Indicador Fax/Correo (X(1))
            $linea .= str_pad("", 8, " ", STR_PAD_RIGHT); // 194-201: Espacios (X(8))
            $linea .= str_pad("", 18, " ", STR_PAD_RIGHT); // 202-219: Libranza (9(18)) o espacios
            $linea .= str_pad("", 11, " ", STR_PAD_RIGHT); // 220-230: Créditos (X(11))
            $linea .= str_pad("", 11, " ", STR_PAD_RIGHT); // 231-241: Espacios (X(11))
            $linea .= "N"; // 242-242: Mensaje Adicional (X(1))
            $linea .= str_pad("", 8, " ", STR_PAD_RIGHT); // 243-250: Espacios (X(8))

            $filas[] = $linea;
        }

        // 2. CONSTRUIR CABECERA (Tipo 1) - Longitud: 250
        $cabecera = "1"; // 1-1: Registro (9(1))
        $cabecera .= date('Ymd', strtotime($model->fecha_aplicacion)); // 2-9: Fecha Aplica (9(8))
        $cabecera .= str_pad(count($detalles), 5, "0", STR_PAD_LEFT); // 10-14: Cantidad (9(5))
        $cabecera .= $this->formatearValorBogota($totalCuerpo, 18); // 15-32: Total (9(18))
        $cabecera .= ($model->banco->producto == 'S') ? "02" : "01"; // 33-34: Tipo Cuenta (9(2))

        $cuentaEmpresa = preg_replace('/[^0-9]/', '', $model->banco->numerocuenta);
        $cabecera .= str_pad($cuentaEmpresa, 17, "0", STR_PAD_LEFT); // 35-51: Cuenta Empresa (9(17))

        $razonSocial = str_replace(['Á','É','Í','Ó','Ú','Ñ'], ['A','E','I','O','U','N'], strtoupper($model->nitEmpresa->razonsocialmatricula));
        $cabecera .= str_pad(substr($razonSocial, 0, 40), 40, " ", STR_PAD_RIGHT); // 52-91: Nombre Empresa (X(40))

        $nitLimpio = preg_replace('/[^0-9]/', '', $model->nit . $model->nitEmpresa->dv);
        $cabecera .= str_pad($nitLimpio, 11, "0", STR_PAD_LEFT); // 92-102: NIT (9(11))

        $cabecera .= "001"; // 103-105: Tipo Movimiento Nomina (9(3))
        $cabecera .= str_pad($model->nitEmpresa->municipio->codigomunicipio ?? '0002', 4, "0", STR_PAD_LEFT); // 106-109: Ciudad (9(4))
        $cabecera .= date('Ymd'); // 110-117: Fecha Creación (9(8))
        $cabecera .= str_pad($model->codigo_oficina ?? '417', 3, "0", STR_PAD_LEFT); // 118-120: Ofic (9(3))
        $cabecera .= "N"; // 121-121: Tipo ID Titular (9(1))
        $cabecera .= str_pad("", 29, " ", STR_PAD_RIGHT); // 122-150: Espacios (X(29))
        $cabecera .= str_pad("", 18, " ", STR_PAD_RIGHT); // 151-168: Valor Libranzas (X(18))
        $cabecera .= " "; // 169-169: Espacio (X(1))
        $cabecera .= " "; // 170-170: Envío mensajes (X(1))
        $cabecera .= str_pad("", 80, " ", STR_PAD_RIGHT); // 171-250: Espacios (X(80))

        // 3. ENSAMBLE FINAL
        $contenidoFinal = $cabecera . "\r\n" . implode("\r\n", $filas);
        $contenidoFinal .= "\r\n"; // El banco suele requerir el último CRLF

        $contenidoFinal = mb_convert_encoding($contenidoFinal, "ISO-8859-1", "UTF-8");
        $nombreArchivo = "NOMINA_BOGOTA_" . date('Ymd_His') . ".txt";

        return Yii::$app->response->sendContentAsFile($contenidoFinal, $nombreArchivo);
    }

    private function formatearValorBogota($valor, $longitud) {
        // Convierte el valor a formato 9(X) donde los últimos 2 son decimales sin punto
        $valorLimpio = number_format($valor, 2, '', ''); 
        return str_pad($valorLimpio, $longitud, "0", STR_PAD_LEFT);
    }
    
    //bancolombia
    public static function RellenarNr($Nro, $Str, $NroCr) {
        $Longitud = strlen($Nro);

        $Nc = $NroCr - $Longitud;
        for ($i = 0; $i < $Nc; $i++)
            $Nro = $Str . $Nro;

        return (string) $Nro;
    }



    //PROCESOS DE EXCEL
    
     public function actionExportar_pago_banco($id) {        
        $model = PagoBancoDetalle::find()->where(['=','id_pago_banco', $id])->orderBy([ 'nombres' =>SORT_ASC ])->all();
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setLastModifiedBy("EMPRESA")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->mergeCells("a".(1).":l".(1));
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A2', 'ID')
                    ->setCellValue('B2', 'TIPO')
                    ->setCellValue('C2', 'CONCEPTO')
                    ->setCellValue('D2', 'DOCUMENTO')
                    ->setCellValue('E2', 'EMPLEADO')
                    ->setCellValue('F2', 'TIPO TRANSACION')
                    ->setCellValue('G2', 'TRANSACION')
                    ->setCellValue('H2', 'CODIGO BANCO')
                    ->setCellValue('I2', 'BANCO')
                    ->setCellValue('J2', 'NUMERO CUENTA')
                    ->setCellValue('K2', 'VALOR PAGO')
                    ->setCellValue('L2', 'TIPO PAGO');
                    
                  
        $i = 3;
        foreach ($model as $val) {                            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_detalle)
                    ->setCellValue('B' . $i, $val->tipo_documento)
                    ->setCellValue('C' . $i, $val->concepto_documento)
                    ->setCellValue('D' . $i, $val->documento)
                    ->setCellValue('E' . $i, $val->nombres)
                    ->setCellValue('F' . $i, $val->tipo_transacion)
                    ->setCellValue('G' . $i, $val->tipoTransacion)
                    ->setCellValue('H' . $i, $val->codigo_banco)
                    ->setCellValue('I' . $i, $val->banco)
                    ->setCellValue('J' . $i, $val->numero_cuenta)
                    ->setCellValue('K' . $i, $val->valor_transacion)
                    ->setCellValue('L' . $i, $val->tipoPago);
                   

              
                   
            $i++;                        
        }

        $objPHPExcel->getActiveSheet()->setTitle('Lista pago banco');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="Lista_Pago.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0 
        header("Content-Transfer-Encoding: binary ");
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);        
        $objWriter->save('php://output');
        //$objWriter->save($pFilename = 'Descargas');
        exit; 
        
    }
}
