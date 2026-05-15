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
                            'pageSize' => 15,
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

    $dias = (int)$periodo_pago->dias;
    $fecha_inicial_base = $grupo_pago->ultimo_pago_nomina;

    // 1. Definimos la nueva fecha inicial primero
    $nueva_fecha_inicial = date('Y-m-d', strtotime('+1 day', strtotime($fecha_inicial_base)));
    
    // Calculamos los datos del NUEVO mes (Mayo en tu caso)
    $anio_nuevo = date('Y', strtotime($nueva_fecha_inicial));
    $mes_nuevo = date('m', strtotime($nueva_fecha_inicial));
    $dia_base = (int)date('d', strtotime($fecha_inicial_base)); // El día del periodo anterior
    
    // Cuántos días tiene el mes del nuevo periodo
    $dias_mes_nuevo = cal_days_in_month(CAL_GREGORIAN, (int)$mes_nuevo, (int)$anio_nuevo);

    $sw = 0;

    // Lógica especial de Febrero (se mantiene igual)
    if (($dia_base == 28 || $dia_base == 29) && ($dias == 15 || $dias == 30)) {
        $sw = ($dia_base == 28) ? 1 : 2;
    }

    // 2. LÓGICA DE CÁLCULO
    if ($periodo_pago->continua == 0) {
        if ($dias == 15) {
            // Si el periodo anterior terminó el 15, este termina el 30 o fin de mes
            if ($dia_base == 15) {
                if ($dias_mes_nuevo == 31) {
                    // Si mayo tiene 31, la quincena comercial termina en 30
                    $nueva_fecha_final = $anio_nuevo . '-' . $mes_nuevo . '-30';
                } else {
                    // Si es abril (30) o febrero (28), termina el último día real
                    $nueva_fecha_final = date('Y-m-t', strtotime($nueva_fecha_inicial));
                }
            } else {
                // Si el periodo anterior terminó a fin de mes, este termina el 15
                $nueva_fecha_final = $anio_nuevo . '-' . $mes_nuevo . '-15';
            }
        } elseif ($dias == 30) {
            // Nómina mensual comercial
            if ($dias_mes_nuevo == 31) {
                $nueva_fecha_final = $anio_nuevo . '-' . $mes_nuevo . '-30';
            } else {
                $nueva_fecha_final = date('Y-m-t', strtotime($nueva_fecha_inicial));
            }
        }
    } else {
        // Si no es especial (7, 10, 14 días), simplemente suma los días
        $nueva_fecha_final = date('Y-m-d', strtotime('+' . ($dias - 1) . ' days', strtotime($nueva_fecha_inicial)));
    }

    // 3. ASIGNACIÓN AL MODELO
    $periodo_pago_nomina->id_grupo_pago = $id_grupo_pago;
    $periodo_pago_nomina->id_periodo_pago = $periodo_pago->id_periodo_pago;
    $periodo_pago_nomina->id_tipo_nomina = 1;

    if ($sw == 0) {
        $periodo_pago_nomina->fecha_desde = $nueva_fecha_inicial;
        $periodo_pago_nomina->fecha_hasta = $nueva_fecha_final;
    } else {
        // Ajuste febrero
        $f_ini = date('Y-m-d', strtotime('-1 day', strtotime($nueva_fecha_inicial)));
        $periodo_pago_nomina->fecha_desde = $f_ini;
        $periodo_pago_nomina->fecha_hasta = date('Y-m-d', strtotime('+14 days', strtotime($f_ini)));
        $nueva_fecha_final = $periodo_pago_nomina->fecha_hasta;
    }

    $periodo_pago_nomina->fecha_real_corte = $nueva_fecha_final;
    $periodo_pago_nomina->dias_periodo = $dias;
    $periodo_pago_nomina->estado_periodo = 0;
    $periodo_pago_nomina->usuariosistema = Yii::$app->user->identity->username;

    $periodo_pago_nomina->save(false);  
    Yii::$app->getSession()->setFlash('success', 'El periodo de nomina se creó exitosamente.');
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
