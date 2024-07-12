<?php

namespace app\controllers;

//modelos
use app\models\FormFiltroCreditoOperarios;
use app\models\CreditoOperarios;
use app\models\CreditoOperariosSearch;
use app\models\UsuarioDetalle;
use app\models\FormPrestamoOperario;
use app\models\Operarios;
use app\models\AbonoCreditoOperarios;
use app\models\FormAbonoPrestamo;
use app\models\Credito;
//clases
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
 * CreditoOperariosController implements the CRUD actions for CreditoOperarios model.
 */
class CreditoOperariosController extends Controller
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
     * Lists all CreditoOperarios models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',111])->all()){
                $form = new FormFiltroCreditoOperarios();
                $id_operaro = null;
                $codigo_credito = null;
                $desde = null;
                $hasta = null;
                $saldo = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $id_operario = Html::encode($form->id_operario);
                        $codigo_credito = Html::encode($form->codigo_credito);
                        $desde = Html::encode($form->desde);
                        $hasta = Html::encode($form->hasta);
                        $saldo = Html::encode($form->saldo);
                       $table = CreditoOperarios::find()
                                ->andFilterWhere(['=', 'id_operario', $id_operario])                                                                                              
                                ->andFilterWhere(['=','codigo_credito', $codigo_credito])
                                ->andFilterWhere(['>=','fecha_inicio', $desde])
                                ->andFilterWhere(['<=','fecha_inicio', $hasta]);
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
                $table = CreditoOperarios::find()
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
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',136])->all()){
                $form = new \app\models\FormConsultaCredito();
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
                        $table = \app\models\PagoNominaServicioDetalle::find()
                                ->andFilterWhere(['=', 'id_credito', $numero_credito])                                                                                              
                                ->andFilterWhere(['between', 'fecha_corte', $fecha_inicio, $fecha_corte])
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
    /**
     * Displays a single CreditoOperarios model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
       $abonos = AbonoCreditoOperarios::find()->where(['=','id_credito',$id])->orderBy('id_abono DESC')->all();
       $refinanciacion = \app\models\RefinanciarCredito::find()->where(['=','id_credito', $id])->all();
       $registros = count($abonos);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'registros' => $registros,
            'abonos' => $abonos, 
            'refinanciacion' => $refinanciacion,
        ]);
    }

    /**
     * Creates a new CreditoOperarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
     public function actionNuevo(){
       $model = new FormPrestamoOperario();
       if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        $operario = Operarios::find()->where(['=','estado', 1])->all(); 
        if ($model->load(Yii::$app->request->post())) {           
            if ($model->validate()) {
                if($operario){
                    $table = new CreditoOperarios();
                    $table->id_operario = $model->id_operario;
                    $table->codigo_credito = $model->codigo_credito;
                    $table->vlr_credito = $model->vlr_credito;
                    $table->vlr_cuota = $model->vlr_cuota;
                    $table->numero_cuotas = $model->numero_cuotas;
                    $table->numero_cuota_actual = $model->numero_cuota_actual;
                    $table->validar_cuotas = $model->validar_cuotas;
                    $table->fecha_inicio = $model->fecha_inicio;
                    $table->saldo_credito = $model->vlr_credito;
                    $table->estado_credito = 1;
                    $table->observacion = $model->observacion;
                    $table->usuariosistema = Yii::$app->user->identity->username; 
                    $table->save(false);
                    return $this->redirect(["credito-operarios/index"]);  
                } else {
                    Yii::$app->getSession()->setFlash('error', 'No existe el documento del operario.');    
                }
            }else{
            $model->getErrors();
            }
        } 
         return $this->render('_form', [
                 'model' => $model,
             ]);
   }

    /**
     * Updates an existing CreditoOperarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = new FormPrestamoOperario();
       if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
      
        if ($model->load(Yii::$app->request->post())) {            
                $table = CreditoOperarios::find()->where(['id_credito'=>$id])->one();
                $abono_credito_operario = AbonoCreditoOperarios::find()->where(['=','id_credito', $id])->one();
                if($abono_credito_operario){
                    Yii::$app->getSession()->setFlash('error', 'No se puede modificar el prestamo porque tiene asociado varios descuentos'); 
                }else{
                    if ($table){
                        $table->id_operario = $model->id_operario;
                        $table->codigo_credito = $model->codigo_credito;
                        $table->vlr_credito = $model->vlr_credito;
                        $table->vlr_cuota = $model->vlr_cuota;
                        $table->numero_cuotas = $model->numero_cuotas;
                        $table->numero_cuota_actual = $model->numero_cuota_actual;
                        $table->validar_cuotas = $model->validar_cuotas;
                        $table->fecha_inicio = $model->fecha_inicio;
                        $table->saldo_credito = $model->vlr_credito;
                        $table->observacion = $model->observacion;
                        $table->save(false);
                    return $this->redirect(["credito-operarios/index"]); 
                    }    
                }
        }
        if (Yii::$app->request->get("id")) {
            $table = CreditoOperarios::find()->where(['id_credito' => $id])->one();            
            if ($table) {     
               $model->id_operario = $table->id_operario;
               $model->codigo_credito = $table->codigo_credito;
               $model->vlr_credito = $table->vlr_credito;
               $model->vlr_cuota = $table->vlr_cuota;
               $model->numero_cuotas = $table->numero_cuotas;
               $model->numero_cuota_actual =  $table->numero_cuota_actual;
               $model->validar_cuotas =  $table->validar_cuotas;
               $model->fecha_inicio =  $table->fecha_inicio;
               $model->saldo_credito =  $table->saldo_credito;
               $model->observacion = $table->observacion;
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

    //NUEVO ABONO
    
    public function actionNuevoabono($id_credito)
    { 
        $model = new FormAbonoPrestamo();
        $credito = CreditoOperarios::find()->where(['=','id_credito',$id_credito])->one();
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
                        $table = new AbonoCreditoOperarios();
                        $table->id_credito = $id_credito;
                        $table->vlr_abono = $model->vlr_abono;
                        $table->saldo = $saldoC;
                        $table->cuota_pendiente = ($nro - 1);
                        $credito->saldo_credito = $saldoC;
                        $table->observacion = $model->observacion;
                        $table->usuariosistema = Yii::$app->user->identity->username;                    
                        $table->insert();
                        $credito_total = CreditoOperarios::findOne($id_credito);
                        $credito_total->saldo_credito = $saldoC;
                        $credito_total->numero_cuota_actual = ($credito_total->numero_cuota_actual + 1);
                        $credito_total->update();
                        $this->redirect(["credito-operarios/view", 'id' => $id_credito]);                    
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
        $model = new \app\models\RefinanciarCredito();
        $credito = CreditoOperarios::find()->where(['=','id_credito',$id_credito])->one();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {           
            if ($model->validate()) {
                if ($credito){
                    $table = new \app\models\RefinanciarCredito();
                    $table->id_credito = $id_credito;
                    $table->id_operario= $credito->id_operario;
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
                    $this->redirect(["credito-operarios/view", 'id' => $id_credito]);                    
                    
                }else{                
                    Yii::$app->getSession()->setFlash('error', 'El Número del credito no existe!');
                }
            }else{
                 $model->getErrors();
            }    
        }
        return $this->render('refinanciar_credito', [
            'model' => $model,
            'credito' => $credito,
        ]);
    }
    
    
   public function actionEliminar($id) {
        
        if (Yii::$app->request->post()) {
            $credito = CreditoOperarios::findOne($id);
            if ((int) $id) {
                try {
                    CreditoOperarios::deleteAll("id_credito=:id_credito", [":id_credito" => $id]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    $this->redirect(["credito-operarios/index"]);
                } catch (IntegrityException $e) {
                    $this->redirect(["credito-operarios/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el prestamo Nro :' .$credito->id_credito .', tiene registros asociados en otros procesos');
                } catch (\Exception $e) {

                    $this->redirect(["credito-operarios/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el prestamo Nro: ' . $credito->id_credito . ', tiene registros asociados en otros procesos');
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el registros, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("credito-operarios/index") . "'>";
            }
        } else {
            return $this->redirect(["credito-operarios/index"]);
        }
    }

    /**
     * Finds the CreditoOperarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CreditoOperarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CreditoOperarios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
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
                     ->setCellValue('A1', 'NRO PRESTAMO')
                    ->setCellValue('B1', 'DOCUMENTO')
                    ->setCellValue('C1', 'OPERARIO')
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
                    ->setCellValue('B' . $i, $val->operario->documento)
                    ->setCellValue('C' . $i, $val->operario->nombrecompleto)
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

        $objPHPExcel->getActiveSheet()->setTitle('Prestamos');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Prestamos.xlsx"');
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
                    ->setCellValue('B1', 'OPERARIO')
                    ->setCellValue('C1', 'TIPO DE CREDITO')
                    ->setCellValue('D1', 'FECHA DE CORTE')
                    ->setCellValue('E1', 'VR. ABONO');
        $i = 2  ;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->pago->operarios->documento)
                    ->setCellValue('B' . $i, $val->pago->operarios->nombrecompleto)
                    ->setCellValue('C' . $i, $val->codigoSalario->nombre_concepto)
                    ->setCellValue('D' . $i, $val->fecha_corte)
                    ->setCellValue('E' . $i, $val->deduccion);                    
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Abonos_credito_operarios.xlsx"');
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
