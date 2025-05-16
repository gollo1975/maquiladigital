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
        if($tipo_proceso == 1 || $tipo_proceso == 2 ||  $tipo_proceso == 3){ //nominas, primas y cesantias
            $listadoPago = \app\models\ProgramacionNomina::find()->where(['=','pago_aplicado', 0])
                                                                 ->andWhere(['=','id_tipo_nomina', $tipo_proceso])->orderBy('id_programacion ASC')->all();
            $form = new \app\models\FormMaquinaBuscar();
            $nombres = null;
            $mensaje = '';
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
                
                 //proceso de nomina, CESANTIAS Y PRIMAS
                if($tipo_proceso == 1 || $tipo_proceso ==  2 || $tipo_proceso == 3){ //nominas, cesantias y primas
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
                        $table->codigo_banco = $empleado->bancoEmpleado->codigo_interfaz;
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
                        $table->codigo_banco = $operario->bancoEmpleado->codigo_interfaz;
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
           $this->redirect(["pago-banco/view", 'id' => $id, 'token' => $token]);
        }else{

        }
        return $this->render('_listado_operario', [
            'listadoPago' => $listadoPago,            
            'mensaje' => $mensaje,
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
        $strArchivo = "archivoPlanoPab".".txt";                
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
