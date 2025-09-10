<?php

namespace app\controllers;


use app\models\Credito;
use app\models\Empleado;
use app\models\TipoPagoCredito;
use app\models\UsuarioDetalle;
use app\models\FormConsultaCredito;
use app\models\FormCredito;
use app\models\AbonoCredito;
use app\models\Contrato;
// aplicacion de yii
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

/**
 * CreditoController implements the CRUD actions for Credito model.
 */
class CreditoController extends Controller
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
    
    public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',84])->all()){
                $form = new FormConsultaCredito();
                $id_empleado = null;
                $id_tipo_pago = null;
                $codigo_credito = null;
                $saldo = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $id_empleado = Html::encode($form->id_empleado);
                        $id_tipo_pago = Html::encode($form->id_tipo_pago);
                        $codigo_credito = Html::encode($form->codigo_credito);
                        $saldo = Html::encode($form->saldo);
                        $table = Credito::find()
                                ->andFilterWhere(['=', 'id_empleado', $id_empleado])                                                                                              
                                ->andFilterWhere(['=', 'id_tipo_pago', $id_tipo_pago])
                                ->andFilterWhere(['=','codigo_credito', $codigo_credito]);
                        if ($saldo == 1){
                            $table = $table->andFilterWhere(['>', 'saldo_credito', $saldo]);
                        }    
                        $table = $table->orderBy('id_credito DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 80,
                            'totalCount' => $count->count()
                        ]);
                        $model = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                            if(isset($_POST['excel'])){                            
                                $check = isset($_REQUEST['id_credito DESC']);
                                $this->actionExcelconsultaCreditos($tableexcel);
                            }
                } else {
                        $form->getErrors();
                }                    
            } else {
                $table = Credito::find()
                        ->orderBy('id_credito DESC');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 20,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                    $this->actionExcelconsultaCreditos($tableexcel);
                }
                if(isset($_POST['activar_periodo_registro'])){                            
                    if(isset($_REQUEST['id_credito'])){                            
                        $intIndice = 0;
                        foreach ($_POST["id_credito"] as $intCodigo) {
                            if ($_POST["id_credito"][$intIndice]) {                                
                                $id_credito = $_POST["id_credito"][$intIndice];
                                $this->actionActivarPeriodoRegistro($id_credito);
                            }
                            $intIndice++;
                        }
                    }
                    $this->redirect(["credito/index"]);
                }
                if(isset($_POST['desactivar_periodo_registro'])){                            
                    if(isset($_REQUEST['id_credito'])){                            
                        $intIndice = 0;
                        foreach ($_POST["id_credito"] as $intCodigo) {
                            if ($_POST["id_credito"][$intIndice]) {                                
                                $id_credito = $_POST["id_credito"][$intIndice];
                                $this->actionDesactivarPeriodoRegistro($id_credito);
                            }
                            $intIndice++;
                        }
                    }
                    $this->redirect(["credito/index"]);
                }
                if(isset($_POST['activar_periodo'])){                            
                    if(isset($_REQUEST['id_credito'])){                            
                        $intIndice = 0;
                        foreach ($_POST["id_credito"] as $intCodigo) {
                            if ($_POST["id_credito"][$intIndice]) {                                
                                $id_credito = $_POST["id_credito"][$intIndice];
                                $this->actionActivarPeriodo($id_credito);
                            }
                            $intIndice++;
                        }
                    }
                    $this->redirect(["credito/index"]);
                }
                if(isset($_POST['desactivar_periodo'])){                            
                    if(isset($_REQUEST['id_credito'])){                            
                        $intIndice = 0;
                        foreach ($_POST["id_credito"] as $intCodigo) {
                            if ($_POST["id_credito"][$intIndice]) {                                
                                $id_credito = $_POST["id_credito"][$intIndice];
                                $this->actionDesactivarPeriodo($id_credito);
                            }
                            $intIndice++;
                        }
                    }
                    $this->redirect(["credito/index"]);
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
   
   //CONSULTA DE ABONOS A CREDITOS
     public function actionSearch_abono_credito() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',135])->all()){
                $form = new FormConsultaCredito();
                $fecha_inicio = null;
                $fecha_corte = null;
                $codigo_credito = null;
                $numero_credito= null;
                $pages = null;
                $model = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $numero_credito = Html::encode($form->numero_credito);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $codigo_credito = Html::encode($form->codigo_credito);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $table = \app\models\ProgramacionNominaDetalle::find()
                                ->andFilterWhere(['=', 'id_credito', $numero_credito])                                                                                              
                                ->andFilterWhere(['between', 'fecha_hasta', $fecha_inicio, $fecha_corte])
                                ->andFilterWhere(['=','codigo_salario', $codigo_credito]);
                        $table = $table->orderBy('id_detalle DESC');
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
                                $check = isset($_REQUEST['id_detalle DESC']);
                                $this->actionExcelconsultaAbonos($tableexcel);
                            }
                } else {
                        $form->getErrors();
                }                    
            } 
            return $this->render('search_abono_credito', [
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
   
    public function actionActivarPeriodoRegistro($id_credito) {        
        $aCredito = Credito::findOne($id_credito);
        $aCredito->estado_credito = 1;
        $aCredito->save(false);
    }
    public function actionDesactivarPeriodoRegistro($id_credito) {        
        $aCredito = Credito::findOne($id_credito);
        $aCredito->estado_credito = 0;
        $aCredito->save(false);
    }  
    public function actionActivarPeriodo($id_credito) {        
        $aCredito = Credito::findOne($id_credito);
        $aCredito->estado_periodo = 1;
        $aCredito->save(false);
    }   
     public function actionDesactivarPeriodo($id_credito) {        
        $aCredito = Credito::findOne($id_credito);
        $aCredito->estado_periodo = 0;
        $aCredito->save(false);
    }
    public function actionNuevo(){
       $model = new FormCredito();
       if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        $empleado = Empleado::find()->all(); 
        if ($model->load(Yii::$app->request->post())) {           
            if ($model->validate()) {
                $contrato = Contrato::find()->where(['=','id_empleado', $model->id_empleado])->andWhere(['=','contrato_activo', 1])->one();
                if($empleado){
                     $fecha_ultimo_pago = strtotime($contrato->ultimo_pago);
                     $fecha_inicio = strtotime($model->fecha_inicio);
                    if ($fecha_ultimo_pago <= $fecha_inicio)
                    {
                        $table = new Credito();
                        $table->id_empleado = $model->id_empleado;
                        $table->codigo_credito = $model->codigo_credito;
                        $table->id_tipo_pago = $model->id_tipo_pago;
                        $table->vlr_credito = $model->vlr_credito;
                        $table->vlr_cuota = $model->vlr_cuota;
                        $table->numero_cuotas = $model->numero_cuotas;
                        $table->numero_cuota_actual = $model->numero_cuota_actual;
                        $table->validar_cuotas = $model->validar_cuotas;
                        $table->fecha_inicio = $model->fecha_inicio;
                        $table->seguro = $model->seguro;
                        $table->numero_libranza = $model->numero_libranza;
                        $table->saldo_credito = $model->vlr_credito + $model->seguro;
                        $table->estado_credito = 1;
                        $table->estado_periodo = 1;
                        $table->aplicar_prima = $model->aplicar_prima;
                        $table->vlr_aplicar = $model->vlr_aplicar;
                        $table->observacion = $model->observacion;
                        $table->usuariosistema = Yii::$app->user->identity->username; 
                        $table->id_grupo_pago = $contrato->id_grupo_pago;
                        $table->save(false);
                        return $this->redirect(["credito/index"]);  
                    }else{
                        Yii::$app->getSession()->setFlash('error', 'La fecha de inicio del credito no puede ser menor que la ultima fecha de pago de la nómina o del contrato.');
                    }

                } else {
                    Yii::$app->getSession()->setFlash('error', 'No existe el documento del empleado.');    
                }
            }else{
            $model->getErrors();
            }
        } 
         return $this->render('_form', [
                 'model' => $model,
             ]);
   }

  public function actionView($id)
    {
       $abonos = AbonoCredito::find()->where(['=','id_credito',$id])->orderBy('id_abono DESC')->all();
       $registros = count($abonos);
        $refinanciacion = \app\models\RefinanciarCreditoEmpleado::find()->where(['=','id_credito', $id])->all();
        if(Yii::$app->request->post())
        {
            $intIndice = 0;
            if (isset($_POST["seleccion"])) {
                foreach ($_POST["seleccion"] as $intCodigo)
                {
                   $abono = Credito::findOne($intCodigo);                    
                    if(Credito::deleteAll("id_credito=:id_credito", [":id_credito" => $intCodigo]))
                    {                        
                    } 
                }
                 return $this->redirect(['credito/view', 'id' => $id]);
            }
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
            'abonos' => $abonos, 
            'registros' => $registros,  
            'refinanciacion'=> $refinanciacion,
            'id'=>$id,
            
        ]);
    }
    public function actionUpdate($id)
    {
        $model = new FormCredito();
       if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
      
        if ($model->load(Yii::$app->request->post())) {            
                $table = Credito::find()->where(['id_credito'=>$id])->one();
                $detalle_nomina = \app\models\ProgramacionNominaDetalle::find()->where(['=','id_credito', $id])->one();
                if ($table){
                    if($detalle_nomina){
                        Yii::$app->getSession()->setFlash('error', 'Error al modificar: No se puede modificar el credito por que tiene asociado el proceso de la nómina'); 
                    }else{
                        $table->id_empleado = $model->id_empleado;
                        $table->codigo_credito = $model->codigo_credito;
                        $table->id_tipo_pago = $model->id_tipo_pago;
                        $table->vlr_credito = $model->vlr_credito;
                        $table->vlr_cuota = $model->vlr_cuota;
                        $table->numero_cuotas = $model->numero_cuotas;
                        $table->numero_cuota_actual = $model->numero_cuota_actual;
                        $table->validar_cuotas = $model->validar_cuotas;
                        $table->fecha_inicio = $model->fecha_inicio;
                        $table->seguro = $model->seguro;
                        $table->numero_libranza = $model->numero_libranza;
                        $table->saldo_credito = $model->vlr_credito + $model->seguro;
                        $table->aplicar_prima = $model->aplicar_prima;
                        $table->vlr_aplicar = $model->vlr_aplicar;
                        $table->observacion = $model->observacion;
                        $contrato = Contrato::find()->where(['=','id_empleado', $model->id_empleado])->andWhere(['=','contrato_activo', 1])->one();
                        $table->id_grupo_pago = $contrato->id_grupo_pago;
                        $table->save(false);
                         return $this->redirect(["credito/index"]);  
                    }
                }
        }
        if (Yii::$app->request->get("id")) {
            $table = credito::find()->where(['id_credito' => $id])->one();            
            if ($table) {     
               $model->id_empleado = $table->id_empleado;
               $model->codigo_credito = $table->codigo_credito;
               $model->id_tipo_pago = $table->id_tipo_pago;
               $model->vlr_credito = $table->vlr_credito;
               $model->vlr_cuota = $table->vlr_cuota;
               $model->numero_cuotas = $table->numero_cuotas;
               $model->numero_cuota_actual =  $table->numero_cuota_actual;
               $model->validar_cuotas =  $table->validar_cuotas;
               $model->fecha_inicio =  $table->fecha_inicio;
               $model->seguro =  $table->seguro;
               $model->numero_libranza =  $table->numero_libranza;
               $model->saldo_credito =  $table->saldo_credito;
               $model->aplicar_prima =  $table->aplicar_prima;
               $model->vlr_aplicar =  $table->vlr_aplicar;
           }else{
                return $this->redirect(['index']);
           }
        } else {
                return $this->redirect(['index']);    
        }
        return $this->render('update', [
            'model' => $model,
            'id'=>$id, 
        ]);
    }
    
    public function actionEliminar($id) {
        
        if (Yii::$app->request->post()) {
            $credito = Credito::findOne($id);
            if ((int) $id) {
                try {
                    Credito::deleteAll("id_credito=:id_credito", [":id_credito" => $id]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    $this->redirect(["credito/index"]);
                } catch (IntegrityException $e) {
                    $this->redirect(["credito/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el credito Nro :' .$credito->id_credito .', tiene registros asociados en otros procesos');
                } catch (\Exception $e) {

                    $this->redirect(["credito/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el credito Nro: ' . $credito->id_credito . ', tiene registros asociados en otros procesos');
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el registros, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("credito/index") . "'>";
            }
        } else {
            return $this->redirect(["credito/index"]);
        }
    }
    
    
    public function actionNuevoabono($id_credito)
    { 
        $model = new \app\models\FormAbonoCredito();
        $credito = Credito::find()->where(['=','id_credito',$id_credito])->one();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {           
            if ($model->validate()) {
                
                $nro = ($credito->numero_cuotas - $credito->numero_cuota_actual);
                $saldoC = $credito->saldo_credito - $model->vlr_abono;
               if ($credito){
                   if ($model->vlr_abono > $credito->saldo_credito){
                        Yii::$app->getSession()->setFlash('error', 'El valor del abono no puede ser mayor al saldo');
                    }else{
                        $table = new AbonoCredito();
                        $table->id_credito = $id_credito;
                        $table->id_tipo_pago = $model->id_tipo_pago;
                        $table->vlr_abono = $model->vlr_abono;
                        $table->saldo = $saldoC;
                        $table->cuota_pendiente = ($nro - 1);
                        $credito->saldo_credito = $saldoC;
                        $table->observacion = $model->observacion;
                        $table->usuariosistema = Yii::$app->user->identity->username;                    
                        $table->insert();
                        $credito_total = Credito::findOne($id_credito);
                        $credito_total->saldo_credito = $saldoC;
                        $credito_total->numero_cuota_actual = ($credito_total->numero_cuota_actual + 1);
                        $credito_total->update();
                        $this->redirect(["credito/view", 'id' => $id_credito]);                    
                    }
                }else{                
                    Yii::$app->getSession()->setFlash('error', 'El Número del credito no existe!');
                }
            }else{
                 $model->getErrors();
            }    
        }
        return $this->render('_formabono', [
            'model' => $model,
            'credito' => $credito,
        ]);
    }

    ///REFINANCIAR CREDITO
    public function actionRefinanciar_credito($id_credito)
    { 
        $model = new \app\models\RefinanciarCreditoEmpleado();
        $credito = Credito::find()->where(['=','id_credito',$id_credito])->one();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {           
            if ($model->validate()) {
                if ($credito){
                    $table = new \app\models\RefinanciarCreditoEmpleado();
                    $table->id_credito = $id_credito;
                    $table->id_empleado= $credito->id_empleado;
                    $table->adicionar_valor = $model->adicionar_valor;
                    $table->nuevo_saldo = $credito->saldo_credito + $model->adicionar_valor;
                    $table->numero_cuotas = $model->numero_cuotas;
                    $table->numero_cuota_actual= $model->numero_cuota_actual;
                    $table->valor_cuota = $model->valor_cuota;
                    $table->user_name= Yii::$app->user->identity->username;                    
                    $table->save(false);
                    $credito->saldo_credito = $table->nuevo_saldo;
                    $credito->numero_cuotas = $model->numero_cuotas;
                    $credito->numero_cuota_actual = $model->numero_cuota_actual;
                    $credito->vlr_cuota = $model->valor_cuota;
                    $credito->save();
                    $this->redirect(["credito/view", 'id' => $id_credito]);                    
                    
                }else{                
                    Yii::$app->getSession()->setFlash('error', 'El Número del credito no existe!');
                }
            }else{
                 $model->getErrors();
            }    
        }
        return $this->render('refinanciar_credito_empleado', [
            'model' => $model,
            'credito' => $credito,
        ]);
    }
    
    //PROCESO PARA IMPRIMIR CREDITOS
    public function actionImprimir_credito($id) {
        $model = $this->findModel($id);
        return $this->render('../formatos/reporte_credito', [
           'model' => $model, 
        ]);
    }
    
        protected function findModel($id)
    {
        if (($model = Credito::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    //ARCHIVOS DE EXCEL
      public function actionExcelconsultaAbonos($tableexcel) {                
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'DOCUMENTO')
                    ->setCellValue('B1', 'EMPLEADO')
                    ->setCellValue('C1', 'TIPO DE CREDITO')
                    ->setCellValue('D1', 'FECHA DE CORTE')
                    ->setCellValue('E1', 'VR. ABONO');
        $i = 2  ;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->programacionNomina->cedula_empleado)
                    ->setCellValue('B' . $i, $val->programacionNomina->empleado->nombrecorto)
                    ->setCellValue('C' . $i, $val->codigoSalario->nombre_concepto)
                    ->setCellValue('D' . $i, $val->fecha_hasta)
                    ->setCellValue('E' . $i, $val->vlr_deduccion);                    
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Abonos_credito.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
        exit;
    }
    
    //DESCARGA LOS ABONOS A CREDITOS
     public function actionExcelconsultaCreditos($tableexcel) {                
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A1', 'NRO CREDITO')
                    ->setCellValue('B1', 'DOCUMENTO')
                    ->setCellValue('C1', 'EMPLEADO')
                    ->setCellValue('D1', 'TIPO CREDITO')
                    ->setCellValue('E1', 'VR. CREDITO')
                    ->setCellValue('F1', 'VR. SALDO')
                    ->setCellValue('G1', 'VR. CUOTA')                    
                    ->setCellValue('H1', 'NRO DE CUOTAS')
                    ->setCellValue('I1', 'CUOTA ACTUAL')
                    ->setCellValue('J1', 'VALIDAR CUOTA')
                    ->setCellValue('K1', 'FECHA INICIO')
                    ->setCellValue('L1', 'USUARIO')
                    ->setCellValue('M1', 'OBSERVACION');
                   
        $i = 2  ;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_credito)
                    ->setCellValue('B' . $i, $val->empleado->identificacion)
                    ->setCellValue('C' . $i, $val->empleado->nombrecorto)
                    ->setCellValue('D' . $i, $val->codigoCredito->nombre_credito)
                    ->setCellValue('E' . $i, $val->vlr_credito)
                    ->setCellValue('F' . $i, $val->saldo_credito)                    
                    ->setCellValue('G' . $i, $val->vlr_cuota)
                    ->setCellValue('H' . $i, $val->numero_cuotas)
                    ->setCellValue('I' . $i, $val->numero_cuota_actual)
                    ->setCellValue('J' . $i, $val->validarcuota)
                    ->setCellValue('K' . $i, $val->fecha_inicio)
                    ->setCellValue('L' . $i, $val->usuariosistema)
                    ->setCellValue('M' . $i, $val->observacion);
                  
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Creditos_empleados.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
        exit;
    }
}
