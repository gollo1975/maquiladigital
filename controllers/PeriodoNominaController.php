<?php

namespace app\controllers;

use app\models\GrupoPago;
use app\models\GrupoPagoSearch;
use app\models\PeriodoPago;
use app\models\PeriodopagoSearch;
use app\models\PeriodoPagoNomina;
use app\models\FormFiltroConsultaPeriodoNomina;

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
use app\models\UsuarioDetalle;

/**
 * OrdenProduccionController implements the CRUD actions for Ordenproduccion model.
 */
class PeriodoNominaController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
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
     * Lists all Ordenproduccion models.
     * @return mixed
     */
  
   public function actionIndexconsulta() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',76])->all()){
                $form = new FormFiltroConsultaPeriodoNomina();
                $id_grupo_pago = null;
                $id_periodo_pago = null;                
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $id_grupo_pago = Html::encode($form->id_grupo_pago);
                        $id_periodo_pago = Html::encode($form->id_periodo_pago);
                        $table = GrupoPago::find()
                                ->andFilterWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                                ->andFilterWhere(['=', 'id_periodo_pago', $id_periodo_pago]);
                        $table = $table->orderBy('id_grupo_pago desc');
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
                            $check = isset($_REQUEST['id_grupo_pago']);
                            $this->actionExcelconsulta($tableexcel);
                        }
                        if(isset($_POST['crear_periodo_nomina'])){                            
                            if(isset($_REQUEST['id_grupo_pago'])){                            
                                $intIndice = 0;
                                foreach ($_POST["id_grupo_pago"] as $intCodigo) {
                                    if ($_POST["id_grupo_pago"][$intIndice]) {                                
                                        $id_grupo_pago = $_POST["id_grupo_pago"][$intIndice];
                                        $this->actionCrearPeriodoNomina($id_grupo_pago);
                                    }
                                    $intIndice++;
                                }
                            }
                            $this->redirect(["periodo-nomina/indexconsulta"]);
                        }
                        if(isset($_POST['crear_periodo_prima'])){                            
                            if(isset($_REQUEST['id_grupo_pago'])){                            
                                $intIndice = 0;
                                foreach ($_POST["id_grupo_pago"] as $intCodigo) {
                                    if ($_POST["id_grupo_pago"][$intIndice]) {                                
                                        $id_grupo_pago = $_POST["id_grupo_pago"][$intIndice];
                                        $this->actionCrearPeriodoPrima($id_grupo_pago);
                                    }
                                    $intIndice++;
                                }
                            }
                            $this->redirect(["periodo-nomina/indexconsulta"]);
                        }
                        if(isset($_POST['crear_periodo_cesantia'])){                            
                            if(isset($_REQUEST['id_grupo_pago'])){                            
                                $intIndice = 0;
                                foreach ($_POST["id_grupo_pago"] as $intCodigo) {
                                    if ($_POST["id_grupo_pago"][$intIndice]) {                                
                                        $id_grupo_pago = $_POST["id_grupo_pago"][$intIndice];
                                        $this->actionCrearPeriodoCesantia($id_grupo_pago);
                                    }
                                    $intIndice++;
                                }
                            }
                            $this->redirect(["periodo-nomina/indexconsulta"]);
                        }
                    } else {
                        $form->getErrors();
                    }                    
                }else {
                $table = GrupoPago::find()
                        ->orderBy('id_grupo_pago desc');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 80,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                    $this->actionExcelconsulta($tableexcel);
                }
                if(isset($_POST['crear_periodo_nomina'])){                            
                    if(isset($_REQUEST['id_grupo_pago'])){                            
                        $intIndice = 0;
                        foreach ($_POST["id_grupo_pago"] as $intCodigo) {
                            if ($_POST["id_grupo_pago"][$intIndice]) {                                
                                $id_grupo_pago = $_POST["id_grupo_pago"][$intIndice];
                                $this->actionCrearPeriodoNomina($id_grupo_pago);
                            }
                            $intIndice++;
                        }
                    }
                   // $this->redirect(["periodo-nomina/indexconsulta"]);
                }
                if(isset($_POST['crear_periodo_prima'])){                            
                    if(isset($_REQUEST['id_grupo_pago'])){                            
                        $intIndice = 0;
                        foreach ($_POST["id_grupo_pago"] as $intCodigo) {
                            if ($_POST["id_grupo_pago"][$intIndice]) {                                
                                $id_grupo_pago = $_POST["id_grupo_pago"][$intIndice];
                                $this->actionCrearPeriodoPrima($id_grupo_pago);
                            }
                            $intIndice++;
                        }
                    }
                    $this->redirect(["periodo-nomina/indexconsulta"]);
                }
                if(isset($_POST['crear_periodo_cesantia'])){                            
                    if(isset($_REQUEST['id_grupo_pago'])){                            
                        $intIndice = 0;
                        foreach ($_POST["id_grupo_pago"] as $intCodigo) {
                            if ($_POST["id_grupo_pago"][$intIndice]) {                                
                                $id_grupo_pago = $_POST["id_grupo_pago"][$intIndice];
                                $this->actionCrearPeriodoCesantia($id_grupo_pago);
                            }
                            $intIndice++;
                        }
                    }
                    $this->redirect(["periodo-nomina/indexconsulta"]);
                }
            }
                $to = $count->count();
                return $this->render('index_consulta', [
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
    
    public function actionCrearPeriodoNomina($id_grupo_pago) {
        $periodo_pago_nomina = new PeriodoPagoNomina();
        $grupo_pago = GrupoPago::findOne($id_grupo_pago);
        $periodo_pago = PeriodoPago::findOne($grupo_pago->id_periodo_pago);                                                 
        $dias = $periodo_pago->dias;
        $fecha_inicial = $grupo_pago->ultimo_pago_nomina;
        $anio_inicio = strtotime ('Y' , strtotime($fecha_inicial )) ;
        $anio_inicio = date('Y',$anio_inicio);
        $mes_inicio = strtotime ('m' , strtotime($fecha_inicial)) ;
        $mes_inicio = date('m',$mes_inicio);
        $dia_inicio = strtotime ('d' , strtotime($fecha_inicial )) ;
        $dia_inicio = date('d',$dia_inicio);
        $diaFebrero = substr($fecha_inicial, 8, 8);
        $sw = 0;
        if($diaFebrero == 28){
            if($grupo_pago->periodoPago->dias == 15 or $grupo_pago->periodoPago->dias == 30){
                $sw = 1;
                $fecha_inicial = strtotime('1 day', strtotime($fecha_inicial));
                $fecha_inicial = date('Y-m-d', $fecha_inicial);
            }     
        }
        if($diaFebrero == 29){
            if($grupo_pago->periodoPago->dias == 15 or $grupo_pago->periodoPago->dias == 30){
                $sw = 2;
                $fecha_inicial = strtotime('1 day', strtotime($fecha_inicial));
                $fecha_inicial = date('Y-m-d', $fecha_inicial);
            }            
        }
        
       $numero_dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes_inicio, $anio_inicio);
        
        if ($periodo_pago->continua == 1){            
            $nueva_fecha_inicial = strtotime ( '+1 day' , strtotime ( $fecha_inicial ) ) ;
            $nueva_fecha_inicial = date ( 'Y-m-d' , $nueva_fecha_inicial );
            $nueva_fecha_final = strtotime ( '+'.$dias.' day' , strtotime ( $fecha_inicial ) ) ;
            $nueva_fecha_final = date ( 'Y-m-d' , $nueva_fecha_final );
        }else{
            if($dias == 15){
                if($dia_inicio <= 15){
                    if ($numero_dias_mes == 28){
                        $nueva_fecha_inicial = strtotime ( '+1 day' , strtotime ( $fecha_inicial ) ) ;
                        $nueva_fecha_inicial = date ( 'Y-m-d' , $nueva_fecha_inicial );
                        $nueva_fecha_final = strtotime ( '+'.($dias - 2).' day' , strtotime ( $fecha_inicial ) ) ;
                        $nueva_fecha_final = date ( 'Y-m-d' , $nueva_fecha_final );
                    }
                    if ($numero_dias_mes == 29){
                        $nueva_fecha_inicial = strtotime ( '+1 day' , strtotime ( $fecha_inicial ) ) ;
                        $nueva_fecha_inicial = date ( 'Y-m-d' , $nueva_fecha_inicial );
                        $nueva_fecha_final = strtotime ( '+'.($dias - 1).' day' , strtotime ( $fecha_inicial ) ) ;
                        $nueva_fecha_final = date ( 'Y-m-d' , $nueva_fecha_final );
                    }
                    if ($numero_dias_mes <> 28 and $numero_dias_mes <> 29){
                        $nueva_fecha_inicial = strtotime ( '+1 day' , strtotime ( $fecha_inicial ) ) ;
                        $nueva_fecha_inicial = date ( 'Y-m-d' , $nueva_fecha_inicial );
                        $nueva_fecha_final = strtotime ( '+'.$dias.' day' , strtotime ( $fecha_inicial ) ) ;
                        $nueva_fecha_final = date ( 'Y-m-d' , $nueva_fecha_final );
                    }                    
                }else{
                    if ($numero_dias_mes == 28){
                        $nueva_fecha_inicial = strtotime ( '+1 day' , strtotime ( $fecha_inicial ) ) ;
                        $nueva_fecha_inicial = date ( 'Y-m-d' , $nueva_fecha_inicial );
                        $nueva_fecha_final = strtotime ( '+'.($dias - 2).' day' , strtotime ( $fecha_inicial ) ) ;
                        $nueva_fecha_final = date ( 'Y-m-d' , $nueva_fecha_final );
                    }
                    if ($numero_dias_mes == 29){
                        $nueva_fecha_inicial = strtotime ( '+1 day' , strtotime ( $fecha_inicial ) ) ;
                        $nueva_fecha_inicial = date ( 'Y-m-d' , $nueva_fecha_inicial );
                        $nueva_fecha_final = strtotime ( '+'.($dias).' day' , strtotime ( $fecha_inicial ) ) ;
                        $nueva_fecha_final = date ( 'Y-m-d' , $nueva_fecha_final );
                    }
                    if ($numero_dias_mes == 30){
                        $nueva_fecha_inicial = strtotime ( '+1 day' , strtotime ( $fecha_inicial ) ) ;
                        $nueva_fecha_inicial = date ( 'Y-m-d' , $nueva_fecha_inicial );
                        $nueva_fecha_final = strtotime ( '+'.($dias ).' day' , strtotime ( $fecha_inicial ) ) ;
                        $nueva_fecha_final = date ( 'Y-m-d' , $nueva_fecha_final );
                    }
                    if ($numero_dias_mes == 31){
                        $nueva_fecha_inicial = strtotime ( '+2 day' , strtotime ( $fecha_inicial ) ) ;
                        $nueva_fecha_inicial = date ( 'Y-m-d' , $nueva_fecha_inicial );
                        $nueva_fecha_final = strtotime ( '+'.($dias + 1).' day' , strtotime ( $fecha_inicial ) ) ;
                        $nueva_fecha_final = date ( 'Y-m-d' , $nueva_fecha_final );
                    }
                }
            }
            if ($dias == 30){
                if ($numero_dias_mes == 28){
                    $nueva_fecha_inicial = strtotime ( '+1 day' , strtotime ( $fecha_inicial ) ) ;
                    $nueva_fecha_inicial = date ( 'Y-m-d' , $nueva_fecha_inicial );
                    $nueva_fecha_final = strtotime ( '+'.($dias - 2).' day' , strtotime ( $fecha_inicial ) ) ;
                    $nueva_fecha_final = date ( 'Y-m-d' , $nueva_fecha_final );
                }
                if ($numero_dias_mes == 29){
                    $nueva_fecha_inicial = strtotime ( '+1 day' , strtotime ( $fecha_inicial ) ) ;
                    $nueva_fecha_inicial = date ( 'Y-m-d' , $nueva_fecha_inicial );
                    $nueva_fecha_final = strtotime ( '+'.($dias - 1).' day' , strtotime ( $fecha_inicial ) ) ;
                    $nueva_fecha_final = date ( 'Y-m-d' , $nueva_fecha_final );
                }
                if ($numero_dias_mes == 30){
                    $nueva_fecha_inicial = strtotime ( '+1 day' , strtotime ( $fecha_inicial ) ) ;
                    $nueva_fecha_inicial = date ( 'Y-m-d' , $nueva_fecha_inicial );
                    $nueva_fecha_final = strtotime ( '+'.($dias).' day' , strtotime ( $fecha_inicial ) ) ;
                    $nueva_fecha_final = date ( 'Y-m-d' , $nueva_fecha_final );
                }
                if ($numero_dias_mes == 31){
                    $nueva_fecha_inicial = strtotime ( '+2 day' , strtotime ( $fecha_inicial ) ) ;
                    $nueva_fecha_inicial = date ( 'Y-m-d' , $nueva_fecha_inicial );
                    $nueva_fecha_final = strtotime ( '+'.($dias + 1).' day' , strtotime ( $fecha_inicial ) ) ;
                    $nueva_fecha_final = date ( 'Y-m-d' , $nueva_fecha_final );
                }
            }
        }        
        $periodo_pago_nomina->id_grupo_pago = $id_grupo_pago;
        $periodo_pago_nomina->id_periodo_pago = $periodo_pago->id_periodo_pago;
        $periodo_pago_nomina->id_tipo_nomina = 1; // nomina
        $diaFebrero = substr($fecha_inicial, 8, 8);
        if($sw == 0) {
            $periodo_pago_nomina->fecha_desde = $nueva_fecha_inicial;
            $periodo_pago_nomina->fecha_hasta = $nueva_fecha_final;
        }else{
            if($sw == 1) {
                 $nueva_fecha_inicial = strtotime('-1 day', strtotime($nueva_fecha_inicial));
                 $nueva_fecha_inicial = date('Y-m-d', $nueva_fecha_inicial);
                 $periodo_pago_nomina->fecha_desde = $nueva_fecha_inicial;
                 $nueva_fecha_final = strtotime('+14 day', strtotime($nueva_fecha_inicial));
                 $nueva_fecha_final = date('Y-m-d', $nueva_fecha_final);
                 $periodo_pago_nomina->fecha_hasta = $nueva_fecha_final;
            }else{
                if($sw == 2) {
                    $nueva_fecha_inicial = strtotime('-1 day', strtotime($nueva_fecha_inicial));
                    $nueva_fecha_inicial = date('Y-m-d', $nueva_fecha_inicial);
                    $periodo_pago_nomina->fecha_desde = $nueva_fecha_inicial;
                    $nueva_fecha_final = strtotime('+14 day', strtotime($nueva_fecha_inicial));
                    $nueva_fecha_final = date('Y-m-d', $nueva_fecha_final);
                    $periodo_pago_nomina->fecha_hasta = $nueva_fecha_final;
                }
            }
        }
        $periodo_pago_nomina->fecha_real_corte = $nueva_fecha_final;
        $periodo_pago_nomina->dias_periodo = $periodo_pago->dias;
        $periodo_pago_nomina->estado_periodo = 0;
        $periodo_pago_nomina->usuariosistema = Yii::$app->user->identity->username;
        $periodo_pago_nomina->save(false);  
        Yii::$app->getSession()->setFlash('success', 'El periodo de nomina se credo exitosamente.');
    }
    
    public function actionCrearPeriodoPrima($id_grupo_pago) {
        $periodo_pago_nomina = new PeriodoPagoNomina();
        $grupo_pago = GrupoPago::findOne($id_grupo_pago);
        $periodo_pago = PeriodoPago::findOne($grupo_pago->id_periodo_pago);                                         
        $dias = 180;
        $fecha_inicial = $grupo_pago->ultimo_pago_prima;                
        $anio_inicio = strtotime ('Y' , strtotime($fecha_inicial )) ;
        $anio_inicio = date('Y',$anio_inicio);
        $mes_inicio = strtotime ('m' , strtotime($fecha_inicial )) ;
        $mes_inicio = date('m',$mes_inicio);
        if ($mes_inicio <= 12 and $mes_inicio > 6){
            $nueva_fecha_inicial = ($anio_inicio + 1)."-01-01";
            $nueva_fecha_final = ($anio_inicio + 1)."-06-30";
        }
        if ($mes_inicio <= 6 and $mes_inicio > 1){
            $nueva_fecha_inicial = $anio_inicio."-07-01";
            $nueva_fecha_final = $anio_inicio."-12-30";            
        }        
        $periodo_pago_nomina->id_grupo_pago = $id_grupo_pago;
        $periodo_pago_nomina->id_periodo_pago = $periodo_pago->id_periodo_pago;
        $periodo_pago_nomina->id_tipo_nomina = 2; // primas
        $periodo_pago_nomina->fecha_desde = $nueva_fecha_inicial;
        $periodo_pago_nomina->fecha_hasta = $nueva_fecha_final;
        $periodo_pago_nomina->fecha_real_corte = $nueva_fecha_final;
        $periodo_pago_nomina->dias_periodo = $dias;
        $periodo_pago_nomina->estado_periodo = 0;
        $periodo_pago_nomina->usuariosistema = Yii::$app->user->identity->username;
        $periodo_pago_nomina->save(false);
        Yii::$app->getSession()->setFlash('success', 'El periodo de Prima semestral se credo exitosamente.');
    }
    
    public function actionCrearPeriodoCesantia($id_grupo_pago) {
        $periodo_pago_nomina = new PeriodoPagoNomina();
        $grupo_pago = GrupoPago::findOne($id_grupo_pago);
        $periodo_pago = PeriodoPago::findOne($grupo_pago->id_periodo_pago);                                         
        $dias = 360;
        $fecha_inicial = $grupo_pago->ultimo_pago_cesantia;                
        $anio_inicio = strtotime ('Y' , strtotime($fecha_inicial )) ;
        $anio_inicio = date('Y',$anio_inicio);        
        
        $nueva_fecha_inicial = ($anio_inicio +1)."-01-01";
        $nueva_fecha_final = ($anio_inicio +1)."-12-30";
               
        $periodo_pago_nomina->id_grupo_pago = $id_grupo_pago;
        $periodo_pago_nomina->id_periodo_pago = $periodo_pago->id_periodo_pago;
        $periodo_pago_nomina->id_tipo_nomina = 3; // primas
        $periodo_pago_nomina->fecha_desde = $nueva_fecha_inicial;
        $periodo_pago_nomina->fecha_hasta = $nueva_fecha_final;
        $periodo_pago_nomina->fecha_real_corte = $nueva_fecha_final;
        $periodo_pago_nomina->dias_periodo = $dias;
        $periodo_pago_nomina->estado_periodo = 0;
        $periodo_pago_nomina->usuariosistema = Yii::$app->user->identity->username;
        $periodo_pago_nomina->save(false);
        Yii::$app->getSession()->setFlash('success', 'El periodo de Cesantias se credo exitosamente.');
    }
    
    public function actionExcelconsulta($tableexcel) {                
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
                               
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Id')
                    ->setCellValue('B1', 'Grupo Pago')
                    ->setCellValue('C1', 'Periodo Pago')
                    ->setCellValue('D1', 'Departamento')
                    ->setCellValue('E1', 'Municipio')                    
                    ->setCellValue('F1', 'Ultimo Periodo Nomina')
                    ->setCellValue('G1', 'Ultimo Periodo Prima')
                    ->setCellValue('H1', 'Ultimo Periodo Cesantia')
                    ->setCellValue('I1', 'Dias Pago')
                    ->setCellValue('J1', 'Estado')
                    ->setCellValue('K1', 'Observaciones');
        $i = 2;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_grupo_pago)
                    ->setCellValue('B' . $i, $val->grupo_pago)
                    ->setCellValue('C' . $i, $val->periodoPago->nombre_periodo)
                    ->setCellValue('D' . $i, $val->departamento->departamento)
                    ->setCellValue('E' . $i, $val->municipio->municipio)                    
                    ->setCellValue('F' . $i, $val->ultimo_pago_nomina)
                    ->setCellValue('G' . $i, $val->ultimo_pago_prima)
                    ->setCellValue('H' . $i, $val->ultimo_pago_cesantia)
                    ->setCellValue('I' . $i, $val->dias_pago)
                    ->setCellValue('J' . $i, $val->estado)
                    ->setCellValue('K' . $i, $val->observacion);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('grupos_de_pago');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="grupos_de_pago.xlsx"');
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
