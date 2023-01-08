<?php

namespace app\controllers;

use app\models\Ordenproduccion;
use app\models\Ordenproducciontipo;
use app\models\CostoProduccionDiaria;
use app\models\FormGenerarCostoProduccionDiaria;
use app\models\ModelSimuladorTiempo;
use app\models\SimuladorTiempo;
use app\models\ModelSimuladorSalario;
use app\models\SimuladorSalario;
use yii;
use yii\base\Model;
use Codeception\Lib\HelperModule;
use yii\web\Controller;
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
use moonland\phpexcel\Excel;
use app\models\UsuarioDetalle;

class CostoProduccionDiariaController extends Controller {

    public function actionCostodiario() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',20])->all()){
                $ordenesproduccion = Ordenproduccion::find()
                                                      ->Where(['=', 'autorizado', 1])
                                                      ->andWhere(['=','facturado', 0])
                                                      ->orderBy('idordenproduccion desc')->all();
                $form = new FormGenerarCostoProduccionDiaria;
                $operarias = null;
                $horaslaboradas = null;
                $minutoshora = null;
                $idordenproduccion = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $operarias = Html::encode($form->operarias);
                        $horaslaboradas = Html::encode($form->horaslaboradas);
                        $minutoshora = Html::encode($form->minutoshora);
                        $idordenproduccion = Html::encode($form->idordenproduccion);
                        if ($idordenproduccion){
                            if($operarias > 0 && $horaslaboradas > 0){
                                $ordenproduccion = Ordenproduccion::findOne($idordenproduccion);
                                if ($ordenproduccion->cantidad > 0){
                                    if($ordenproduccion->segundosficha > 0){
                                        $costolaboralhora = \app\models\CostoLaboralHora::findOne(1);                        
                                        $costodiario = CostoProduccionDiaria::findOne(1);
                                        $costodiario->idcliente = $ordenproduccion->idcliente;
                                        $costodiario->idordenproduccion = $ordenproduccion->idordenproduccion;
                                        $costodiario->cantidad = $ordenproduccion->cantidad;
                                        $costodiario->ordenproduccion = $ordenproduccion->ordenproduccion;
                                        $costodiario->ordenproduccionext = $ordenproduccion->ordenproduccionext;
                                        $costodiario->idtipo = $ordenproduccion->idtipo;
                                        $costodiario->cantidad_x_hora = round($minutoshora / ($ordenproduccion->segundosficha / 60),2);
                                        $costodiario->cantidad_diaria = round(($costodiario->cantidad_x_hora * $horaslaboradas) * $operarias,2);
                                        $costodiario->tiempo_entrega_dias = round($costodiario->cantidad / $costodiario->cantidad_diaria,2);
                                        $costodiario->nro_horas = round($horaslaboradas * $costodiario->tiempo_entrega_dias,2);
                                        $costodiario->dias_entrega = round($costodiario->nro_horas / $horaslaboradas);
                                        $costodiario->costo_muestra_operaria = round($ordenproduccion->segundosficha / 60 * $costolaboralhora->valor_minuto,0);
                                        $costodiario->costo_x_hora = round($costodiario->costo_muestra_operaria * $costodiario->cantidad_x_hora,0);
                                        $costodiario->update();
                                        $table = CostoProduccionDiaria::find()->where(['=','id_costo_produccion_diaria',1])->all();                        
                                        $model = $table;
                                    }else{
                                        Yii::$app->getSession()->setFlash('error', 'La orden de produccion no tiene operaciones asignadas en la ficha de operaciones.');                        
                                        $model = CostoProduccionDiaria::find()->where(['=','id_costo_produccion_diaria',0])->all();
                                    }                            
                                } else{
                                       Yii::$app->getSession()->setFlash('error', 'La cantidad de la orden de produccion debe ser mayor a cero');                        
                                       $model = CostoProduccionDiaria::find()->where(['=','id_costo_produccion_diaria',0])->all(); 
                                }

                            }else{
                                Yii::$app->getSession()->setFlash('error', 'La cantidad de operarias y/o horas laboradas, no pueden ser 0 (cero)');                        
                                $model = CostoProduccionDiaria::find()->where(['=','id_costo_produccion_diaria',0])->all();
                            }

                        }else{
                            Yii::$app->getSession()->setFlash('error', 'No se tiene el valor de la orden de producción para generar el informe');
                            $model = CostoProduccionDiaria::find()->where(['=','id_costo_produccion_diaria',0])->all();
                        }                
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = CostoProduccionDiaria::find()->where(['=','id_costo_produccion_diaria',0])->all();            
                    $model = $table;            
                }        
                return $this->render('costodiario', [
                            'model' => $model,
                            'form' => $form,
                            //'pagination' => $pages,
                            'ordenesproduccion' => ArrayHelper::map($ordenesproduccion, "idordenproduccion", "ordenProduccion"),
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
    }
    
    //simulador de tiempo
    public function actionSimuladortiempo() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso', 126])->all()){
                $form = new ModelSimuladorTiempo();
                $cantidad_operarios = null;
                $horario_trabajo = null;
                $eficiencia = null;
                $vlr_minuto_contrato = null;
                $salario = null;
                $vinculado = null;
                $unidades = null;
                $tiempo_confeccion = null;
                $id_cliente = null;
                $fecha_inicio = null;
                $simulador = SimuladorTiempo::find()->where(['=','id_simulador', 1])->all();
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_cliente = Html::encode($form->id_cliente);
                        $cantidad_operarios = Html::encode($form->cantidad_operarios);
                        $horario_trabajo = Html::encode($form->horario_trabajo);
                        $eficiencia = Html::encode($form->eficiencia);
                        $vlr_minuto_contrato = Html::encode($form->vlr_minuto_contrato);
                        $salario = Html::encode($form->salario);
                        $vinculado = Html::encode($form->vinculado);
                        $unidades = Html::encode($form->unidades);
                        $tiempo_confeccion = Html::encode($form->tiempo_confeccion);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        //inicio de grabado
                        $cliente = \app\models\Cliente::findOne($id_cliente);
                        $table = SimuladorTiempo::findOne(1);
                        $table->cantidad_operarios = $cantidad_operarios;
                        $table->id_horario = $horario_trabajo;
                        $table->eficiencia = $eficiencia;
                        $table->vlr_minuto_contrato = $vlr_minuto_contrato;
                        $table->salario = $salario;
                        $table->vinculado = $vinculado;
                        $table->unidades_lote = $unidades;
                        $table->sam_prenda = $tiempo_confeccion;
                        $table->idcliente = $id_cliente;
                        $table->vlr_minuto_venta = $cliente->minuto_confeccion;
                        $table->fecha_inicio = $fecha_inicio;
                        $table->valor_lote = round(($table->vlr_minuto_venta * $tiempo_confeccion) * $unidades);
                        $table->update();
                        $this->CalculoUnidadFechaDia($table);
                        $this->CalcularCostoLote($table);
                        $simulador= SimuladorTiempo::find()->where(['=','id_simulador', 1])->all();
                    } else {
                        $form->getErrors();
                    }
                }else{
                  $simulador = SimuladorTiempo::find()->where(['=','id_simulador', 1])->all();
                } 
                return $this->render('simuladortiempo', [
                            'form' => $form,
                            'model' => $simulador,
               ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
    }
    //SIMULADOR DE SALARIO
     public function actionSimuladorsalario() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso', 127])->all()){
                $form = new ModelSimuladorSalario();
                $arl = null;
                $salario_basico = null;
                $aplica_auxilio = null;
                $eficiencia = null;
                $valor_minuto = null;
                $sam = null;
                $dias_laborados =  null;
                $id_horario = null;
                $otros_gastos = null;
                $simulador = \app\models\SimuladorSalario::find()->where(['=','id_simulador_salario', 1])->all();
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $arl = Html::encode($form->arl);
                        $salario_basico = Html::encode($form->salario_basico);
                        $aplica_auxilio = Html::encode($form->aplica_auxilio);
                        $eficiencia = Html::encode($form->eficiencia);
                        $valor_minuto = Html::encode($form->valor_minuto);
                        $sam = Html::encode($form->sam);
                        $dias_laborados = Html::encode($form->dias_laborados);
                        $id_horario = Html::encode($form->id_horario);
                        $otros_gastos = Html::encode($form->otros_gastos);
                        //inicio de grabado
                        $salario = \app\models\ConfiguracionSalario::find()->where(['=','estado', 1])->one();
                        $conPension = \app\models\ConfiguracionPension::findOne(1);
                        $caja = \app\models\CajaCompensacion::findOne(1);
                        $matricula = \app\models\Matriculaempresa::findOne(1);
                        $table = \app\models\SimuladorSalario::findOne(1);
                        $table->salario = $salario_basico;
                        $table->id_arl = $arl;
                        if($aplica_auxilio == 1){
                             $table->auxilio_transporte = $salario->auxilio_transporte_actual;
                        }else{
                             $table->auxilio_transporte = 0;
                        }
                        //seguridad social
                        $table->valor_pension = round(($salario_basico * $conPension->porcentaje_empleador)/100);
                        $table->valor_caja = round(($salario_basico * $caja->porcentaje_caja)/100);
                        $table->valor_arl = round(($salario_basico * $table->arl->arl)/100);
                        //prestaciones
                        $table->valor_prima = round((($salario_basico + $salario->auxilio_transporte_actual) * 30)/360);
                        $table->valor_cesantia = round((($salario_basico + $salario->auxilio_transporte_actual) * 30)/360);
                        $table->valor_interes = round(($table->valor_cesantia * 12)/100);
                        $table->valor_vacacion = round(($salario_basico * 30)/ 720);
                        $table->ajuste_vacacion = round(($table->valor_vacacion * $matricula->ajuste_caja)/ 100);
                        $table->total_salarios = $salario_basico + $otros_gastos + $table->auxilio_transporte + $table->valor_pension + $table->valor_caja + $table->valor_arl + $table->valor_prima + $table->valor_cesantia + $table->valor_interes + $table->valor_vacacion + $table->ajuste_vacacion;
                        //datos de eficiencia
                        $table->id_horario = $id_horario;
                        $table->valor_minuto = $valor_minuto;
                        $table->sam_prenda = $sam;
                        $table->valor_prenda = round($sam * $valor_minuto);
                        $table->eficiencia = $eficiencia;
                        $table->dias_laborados = $dias_laborados;
                        $table->unidades_dia = round(((60/$sam) * $table->horario->total_horas) * $eficiencia)/100;
                        $table->unidades_mes = $table->unidades_dia * $dias_laborados;
                        $table->valor_venta = round($table->unidades_mes * $table->valor_prenda);
                        $table->usuario = Yii::$app->user->identity->username;
                        $table->save(false);
                        
                        $simulador= SimuladorSalario::find()->where(['=','id_simulador_salario', 1])->all();
                    } else {
                        $form->getErrors();
                    }
                }else{
                  $simulador = SimuladorSalario::find()->where(['=','id_simulador_salario', 1])->all();
                } 
                return $this->render('simuladorsalario', [
                            'form' => $form,
                            'model' => $simulador,
                            'aplica_auxilio' => $aplica_auxilio,
               ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
    }
    
    // calculo de la eficiencia y costo del lote
    protected function CalcularCostoLote($table) {
        $simulador = SimuladorTiempo::findOne(1);
        if($table->vinculado == 1){
            $cesantia = 0; $prima = 0; $interes = 0; $vacacion = 0; $basico = 0; 
            $pension = 0; $arl = 0; $caja = 0; $ajuste = 0; $valor_dia = 0; 
            $total_seguridad = 0; $total_prestacion = 0; $total_salario = 0; $total_auxilio = 0;
            $transporte = \app\models\ConfiguracionSalario::find()->where(['=','estado', 1])->one();
            $entidad_pension = \app\models\ConfiguracionPension::findOne(1);
            $entidad_caja = \app\models\CajaCompensacion::findOne(1);
            $entidad_arl = \app\models\Arl::findOne(2);
            $empresa = \app\models\Matriculaempresa::findOne(1);
            $valor_dia = round($simulador->salario / 30);
            $basico = round($valor_dia * $simulador->dias_reales);
            //seguridad social
            $pension = round(($basico * $entidad_pension->porcentaje_empleador)/100);    
            $arl = round(($basico * $entidad_arl->arl)/100);    
            $caja = round(($basico * $entidad_caja->porcentaje_caja)/100);    
            //prestaciones sociales
            $cesantia = round((($simulador->salario + $transporte->auxilio_transporte_actual)* $simulador->dias_reales)/360) ;
            $prima = round((($simulador->salario + $transporte->auxilio_transporte_actual)* $simulador->dias_reales)/360) ;
            $interes = round(($cesantia * 12)/100) ;
            $vacacion = round(($simulador->salario * $simulador->dias_reales)/720);
            $ajuste = round(($vacacion * $empresa->ajuste_caja)/100) ;
            //totales
            $total_seguridad = ($pension + $arl + $caja) * $simulador->cantidad_operarios;
            $total_prestacion = ($cesantia + $prima + $interes + $vacacion + $ajuste ) * $simulador->cantidad_operarios;
            $total_salario = $basico * $simulador->cantidad_operarios;
            $total_auxilio = round($transporte->auxilio_transporte_actual /30 * $simulador->dias_reales)* $simulador->cantidad_operarios;
            $simulador->valor_costo_lote = $total_seguridad + $total_prestacion + $total_salario + $total_auxilio;
            $simulador->utilidad_lote = round($simulador->valor_lote - $simulador->valor_costo_lote);
            $simulador->update();
        }else{
            $simulador->valor_costo_lote = round(($simulador->sam_prenda * $simulador->vlr_minuto_contrato)* $simulador->unidades_lote);
            $simulador->utilidad_lote = round($simulador->valor_lote - $simulador->valor_costo_lote);
            $simulador->update(); 
        }
    }
    //proceso que calcula las unidades, fecha y dias
    protected function CalculoUnidadFechaDia($table) {
        $empresa = \app\models\Matriculaempresa::findOne(1);
        $simulador = SimuladorTiempo::findOne(1);
        $unidad_diarias = 0; $dias_reales = 0; $dias = 0;
        //algoritmo
        $unidad_diarias = ((60 / $simulador->sam_prenda) * $simulador->cantidad_operarios) * $simulador->horario->total_horas;
        $unidad_diarias = round(($unidad_diarias * $simulador->eficiencia)/100);
        //valida eficiencia
        $dias_reales = number_format($simulador->unidades_lote / $unidad_diarias, 2);
        $dias = round($simulador->unidades_lote / $unidad_diarias);
        $date_inicio = date($simulador->fecha_inicio);
        $date_future = strtotime('+'.$dias. 'day', strtotime($date_inicio)-1);
        $date_future = date('Y-m-d', $date_future);
        $simulador->fecha_final = $date_future;
        $simulador->dias_proceso = $dias;
        $simulador->dias_reales = $dias_reales;
        $simulador->unidades_por_dia = $unidad_diarias;
        $simulador->save(false);
    }
    //proceso de excel
    public function actionExcel($id) {
        $costoproducciondiario = CostoProduccionDiaria::find()->all();
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
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Cliente')
                    ->setCellValue('B1', 'Orden Producción')
                    ->setCellValue('C1', 'Cantidad por Hora')
                    ->setCellValue('D1', 'Cantidad Diaria')
                    ->setCellValue('E1', 'Tiempo Entrega Días')
                    ->setCellValue('F1', 'Nro Horas')
                    ->setCellValue('G1', 'Días Entrega')
                    ->setCellValue('H1', 'Costo Muestra Operaría')
                    ->setCellValue('I1', 'Costo por Hora');

        $i = 2;
        
        foreach ($costoproducciondiario as $costoproducciondiario) {
            
            $cliente = "";
            if ($costoproducciondiario->idcliente){
                $arCliente = \app\models\Cliente::findOne($costoproducciondiario->idcliente);
                $cliente = $arCliente->nombrecorto;
            }
            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $cliente)
                    ->setCellValue('B' . $i, $costoproducciondiario->ordenproduccion)
                    ->setCellValue('C' . $i, $costoproducciondiario->cantidad_x_hora)
                    ->setCellValue('D' . $i, $costoproducciondiario->cantidad_diaria)
                    ->setCellValue('E' . $i, $costoproducciondiario->tiempo_entrega_dias)
                    ->setCellValue('F' . $i, $costoproducciondiario->nro_horas)
                    ->setCellValue('G' . $i, $costoproducciondiario->dias_entrega)
                    ->setCellValue('H' . $i, $costoproducciondiario->costo_muestra_operaria)
                    ->setCellValue('I' . $i, $costoproducciondiario->costo_x_hora);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Costo_produccion_diaria');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Costo_produccion_diaria.xlsx"');
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
    
    public function actionExcelsimulacion($id) {
        $simulador = SimuladorTiempo::find()->all();
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);        
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);        
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);   
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);        
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'CLIENTE')
                    ->setCellValue('C1', 'CANT. OPERARIOS')
                    ->setCellValue('D1', 'HORARIO')
                    ->setCellValue('E1', '% EFICIENCIA')
                    ->setCellValue('F1', 'VL. MINUTO CONTRATO')
                    ->setCellValue('G1', 'SALARIO')
                    ->setCellValue('H1', 'CANT. LOTE')
                    ->setCellValue('I1', 'SAM PRENDA')
                    ->setCellValue('J1', 'UNIDADES X DIA')
                    ->setCellValue('K1', 'FECHA INICIO')
                    ->setCellValue('L1', 'FECHA FINAL')
                    ->setCellValue('M1', 'DIAS TRABAJO')
                    ->setCellValue('N1', 'DIAS REALES')
                    ->setCellValue('O1', 'VALOR VENTA')
                    ->setCellValue('P1', 'COSTO CONFECCION')
                    ->setCellValue('Q1', 'UTILIDAD')
                    ->setCellValue('R1', 'FECHA REGISTRO');
                  

        $i = 2;
        
        foreach ($simulador as $simuladores) {
            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $simuladores->id_simulador)
                    ->setCellValue('B' . $i, $simuladores->cliente->nombrecorto)
                    ->setCellValue('C' . $i, $simuladores->cantidad_operarios)
                    ->setCellValue('D' . $i, $simuladores->horario->horario)
                    ->setCellValue('E' . $i, $simuladores->eficiencia)
                    ->setCellValue('F' . $i, $simuladores->vlr_minuto_contrato)
                    ->setCellValue('G' . $i, $simuladores->salario)
                    ->setCellValue('H' . $i, $simuladores->unidades_lote)
                    ->setCellValue('I' . $i, $simuladores->sam_prenda)
                    ->setCellValue('J' . $i, $simuladores->unidades_por_dia)
                    ->setCellValue('K' . $i, $simuladores->fecha_inicio)
                    ->setCellValue('L' . $i, $simuladores->fecha_final)
                    ->setCellValue('M' . $i, $simuladores->dias_proceso)
                    ->setCellValue('N' . $i, $simuladores->dias_reales)
                    ->setCellValue('O' . $i, $simuladores->valor_lote)
                    ->setCellValue('P' . $i, $simuladores->valor_costo_lote)
                    ->setCellValue('Q' . $i, $simuladores->utilidad_lote)
                    ->setCellValue('R' . $i, $simuladores->fecha_registro);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Simulador_lote');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Simulador_lote.xlsx"');
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
