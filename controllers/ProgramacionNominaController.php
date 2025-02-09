<?php

namespace app\controllers;

use app\models\GrupoPago;
use app\models\GrupoPagoSearch;
use app\models\PeriodoPago;
use app\models\PeriodopagoSearch;
use app\models\ProgramacionNomina;
use app\models\PeriodoPagoNomina;
use app\models\NovedadTiempoExtra;
use app\models\Contrato;
use app\models\FormFiltroConsultaPeriodoPagoNomina;
use app\models\FormFiltroComprobantePagoNomina;
use app\models\FormPeriodoPagoNomina;
use app\models\UsuarioDetalle;
use app\models\Incapacidad;
use app\models\Licencia;
use app\models\FormProgramacionNominaDetalle;
use app\models\ProgramacionNominaDetalle;
use app\models\ConceptoSalarios;
use app\models\ConfiguracionSalario;
use app\models\Credito;
use app\models\ConfiguracionCredito;
use app\models\PagoAdicionalPermanente;
use app\models\ConfiguracionLicencia;
use app\models\ConfiguracionIncapacidad;
use app\models\TipoPagoCredito;
use app\models\ConfiguracionPension;
use app\models\ConfiguracionEps;
use app\models\TiempoServicio;
use app\models\AbonoCredito;
use app\models\Consecutivo;
use app\models\ConfiguracionPrestaciones;
use app\models\InteresesCesantia;
use app\models\FormMaquinaBuscar;
use app\models\PeriodoNominaElectronica;

// clases de yii
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
 * OrdenProduccionController implements the CRUD actions for Ordenproduccion model.
 */
class ProgramacionNominaController extends Controller {

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
    public function actionIndex() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 75])->all()) {
                $form = new FormFiltroConsultaPeriodoPagoNomina();
                $id_grupo_pago = null;
                $id_periodo_pago = null;
                $id_tipo_nomina = null;
                $estado_periodo = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_grupo_pago = Html::encode($form->id_grupo_pago);
                        $id_periodo_pago = Html::encode($form->id_periodo_pago);
                        $id_tipo_nomina = Html::encode($form->id_tipo_nomina);
                        $estado_periodo = Html::encode($form->estado_periodo);
                        $table = PeriodoPagoNomina::find()
                                ->andFilterWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                                ->andFilterWhere(['=', 'id_periodo_pago', $id_periodo_pago])
                                ->andFilterWhere(['=', 'id_tipo_nomina', $id_tipo_nomina])
                                ->andFilterWhere(['=', 'estado_periodo', $estado_periodo]);
                        $table = $table->orderBy('id_periodo_pago_nomina DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 40,
                            'totalCount' => $count->count()
                        ]);
                        $model = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_periodo_pago_nomina DESC']);
                            $this->actionExcelconsulta($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = PeriodoPagoNomina::find()
                            ->where(['=', 'estado_periodo', 0])
                            ->orderBy('id_periodo_pago_nomina DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 40,
                        'totalCount' => $count->count(),
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelconsulta($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'model' => $model,
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
    
    //PROCESO QUE CARGA EL LISTADO DE NOMINA ELECTRONICA
    public function actionDocumento_electronico($token = 0) {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 150])->all()) {
                $form = new \app\models\FormFiltroBuscarNomina();
                $desde = null;
                $hasta = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $desde = Html::encode($form->desde);
                        $hasta = Html::encode($form->hasta);
                        $table = PeriodoNominaElectronica::find()
                                ->andFilterWhere(['between', 'fecha_inicio_periodo', $desde, $hasta]);
                        $table = $table->orderBy('id_periodo_electronico DESC');
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
                       /* if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_periodo_electronico DESC']);
                            $this->actionExcelconsulta($tableexcel);
                        }*/
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = PeriodoNominaElectronica::find()->orderBy('id_periodo_electronico DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 15,
                        'totalCount' => $count->count(),
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                   /* if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelconsulta($tableexcel);
                    }*/
                }
                //$to = $count->count();
                return $this->render('crear_periodo_nomina_electronica', [
                            'model' => $model,
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
    
    
     //PROCESO QUE CARGA EL LISTADO DE NOMINA ELECTRONICA
    public function actionListar_nomina_electronica($token = 1) {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 151])->all()) {
                $form = new \app\models\FormFiltroDocumentoElectronico();
                $documento = null;
                $empleado = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $documento = Html::encode($form->documento);
                        $empleado = Html::encode($form->empleado);
                        $table = \app\models\NominaElectronica::find()
                                ->andFilterWhere(['like', 'nombre_completo', $empleado])
                                ->andFilterWhere(['=', 'documento_empleado', $documento])
                                ->andWhere(['>', 'numero_nomina_electronica', 0])
                                ->andWhere(['=', 'exportado_nomina', 0]);
                        $table = $table->orderBy('numero_nomina_electronica ASC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 30,
                            'totalCount' => $count->count()
                        ]);
                        $model = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = \app\models\NominaElectronica::find()->Where(['>', 'numero_nomina_electronica', 0])
                                                                  ->andWhere(['=', 'exportado_nomina', 0])
                                                                  ->orderBy('numero_nomina_electronica ASC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 30,
                        'totalCount' => $count->count(),
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                }
                //PROCESO QUE ENVIA LA NOMINA ELECTRONICA
                if (isset($_POST["enviar_documento_electronico"])) { ////entra al ciclo cuando presiona el boton crear documentos
                    if (isset($_POST["documento_electronico_dian"])) {
                        $intIndice = 0;
                        $contador = 0;
                        foreach ($_POST["documento_electronico_dian"] as $intCodigo) { //vector que cargar cada items
                            $documento = \app\models\NominaElectronica::findOne($intCodigo);// vector del empleado
                            if($documento)
                            {
                                $periodo = PeriodoNominaElectronica::findOne($documento->id_periodo_electronico);
                                $total_devengado = intval($documento->total_devengado, 0) . '.00';
                                $total_deduccion = intval($documento->total_deduccion, 0) . '.00';
                                $tipo_nomina_enviada = $periodo->type_document_id;
                                $fecha_ingreso_empleado = $documento->fecha_inicio_contrato;
                                $fecha_inicio_nomina = $documento->fecha_inicio_nomina;
                                $fecha_corte_nomina = $documento->fecha_final_nomina;
                                $dias_trabajados = $documento->dias_trabajados;
                                $fecha_emision_nomina = date('Y-m-d');
                                $fecha_retiro_empleado = $documento->fecha_terminacion_contrato;
                                $codigo_empleado = $documento->id_empleado;
                                $consecutivo = $documento->numero_nomina_electronica;
                                $codigo_periodo_pago = $documento->periodoPago->codigo_api_nomina;// es el codigo del periodo de pago
                                $nota = $periodo->nota;
                                $type_worker_id = $documento->contrato->tipoCotizante->codigo_api_nomina;
                                $sub_type_worker_id = $documento->contrato->subtipoCotizante->codigo_api_nomina;
                                $tipo_documento_empleado = $documento->empleado->tipoDocumento->codigo_interface_nomina;
                                $codigo_municipio = $documento->empleado->municipio->codigo_api_nomina;
                                $tipo_contrato_empleado = $documento->contrato->tipoContrato->codigo_api_enlace;
                                $documento_empleado =  $documento->documento_empleado;
                                $primer_apellido = $documento->primer_apellido;
                                $segundo_apellido = $documento->segundo_apellido;
                                $primer_nombre = $documento->primer_nombre;
                                $segundo_nombre = $documento->segundo_nombre;
                                $direccion_empleado = $documento->direccion_empleado;
                                $salario_empleado = intval($documento->salario_contrato, 0) . '.00';
                                $email_empleado = $documento->email_empleado;
                                $forma_pago = $documento->empleado->formaPago->codigo_api_nomina;
                                $nombre_banco = $documento->empleado->bancoEmpleado->banco;
                                $tipo = $documento->empleado->tipo_cuenta;
                                $altoriesgo = $documento->contrato->id_pension;
                                $tipo_salario = $documento->contrato->tipo_salario;
                                $eps_type_law_deductions_id = $documento->contrato->pagoEps->codigo_api_nomina;
                                $pension_type_law_deductions_id = $documento->contrato->pagoPension->codigo_api_nomina;
                                if($tipo_salario == 'INTEGRAL'){
                                    $salario_integral = true;
                                }else{
                                    $salario_integral = false;
                                }
                                if($altoriesgo == 3){
                                    $alto_riesgo = true;
                                }else{
                                    $alto_riesgo = false;
                                }
                                if($tipo == 'S'){
                                    $tipo_cuenta_bancaria = 'Ahorro';
                                }else{
                                    $tipo_cuenta_bancaria = 'Corriente';
                                }
                                $numero_cuenta_bancaria = $documento->empleado->cuenta_bancaria;
                                // Configurar cURL
                                $curl = curl_init();
                              //  $API_KEY = Yii::$app->params['API_KEY_DESARROLLO']; //api_key de desarrollo
                                $API_KEY = Yii::$app->params['API_KEY_PRODUCCION']; //api_key de produccion
                                $dataBody = [
                                    "novelty" => [
                                        "novelty" => false,
                                        "uuidnov" => ""  
                                    ],

                                    "period" => [
                                        "admision_date" => "$fecha_ingreso_empleado",
                                        "settlement_start_date" => "$fecha_inicio_nomina",
                                        "settlement_end_date" => "$fecha_corte_nomina",
                                        "worked_time" => "$dias_trabajados",
                                        "retirement_date" => "$fecha_retiro_empleado",
                                        "issue_date" => "$fecha_emision_nomina"
                                    ],

                                    "sendmail" => false,
                                    "sendmailtome" => false,
                                    "worker_code" => "$codigo_empleado",
                                    "prefix" => "NI", // Siempre debe ser NI para nómina individual, NIAD para ajuste
                                    "consecutive" => $consecutivo,
                                    "type_document_id" => "$tipo_nomina_enviada",
                                    "payroll_period_id" => $codigo_periodo_pago,
                                    "notes" => "$nota",
                                    "worker" => [
                                        "type_worker_id" => $type_worker_id,
                                        "sub_type_worker_id" => $sub_type_worker_id,
                                        "payroll_type_document_identification_id" => $tipo_documento_empleado,
                                        "municipality_id" => $codigo_municipio,
                                        "type_contract_id" => $tipo_contrato_empleado,
                                        "high_risk_pension" => $alto_riesgo, //false => si es bajo riesgo y true => si alto riesgo
                                        "identification_number" => $documento_empleado,
                                        "surname" => "$primer_apellido",
                                        "second_surname" => "$segundo_apellido",
                                        "first_name" => "$primer_nombre",
                                        "middle_name" => "$segundo_nombre",
                                        "address" => "$direccion_empleado",
                                        "integral_salarary" => $salario_integral,//si esl TRUE->es verdadero, False => false
                                        "salary" => $salario_empleado,
                                        "email" => "$email_empleado"
                                    ],

                                    "payment" => [ //datos del banco
                                        "payment_method_id" => "$forma_pago",
                                        "bank_name" => "$nombre_banco",
                                        "account_type" => "$tipo_cuenta_bancaria",
                                        "account_number" => "$numero_cuenta_bancaria"
                                    ],

                                    "payment_dates" => [
                                        [
                                            "payment_date" => "$fecha_corte_nomina" // son las fechas de pagos 
                                        ]

                                    ],
                                    "accrued" => [],   //se inicia el vector
                                    "deductions" => [] //inicio el vector de deduccion
                                    
                                ]; 
                               
                               //CICLO QUE SUBE LOS DETALLES DE LA COLILLA
                                $detallesPago = \app\models\NominaElectronicaDetalle::find()->where(['=','id_nomina_electronica', $documento->id_nomina_electronica])->orderBy('id_agrupado ASC')->all();
                                foreach ($detallesPago as $key => $detalle) 
                                {
                                    $deduccion_pension = intval($detalle->deduccion_pension, 0) . '.00';
                                    $deduccion_eps = intval($detalle->deduccion_eps, 0) . '.00';
                                    $valor_pago_incapacidad = intval($detalle->valor_pago_incapacidad, 0) . '.00';
                                    $valor_pago_licencia = intval($detalle->valor_pago_licencia, 0) . '.00';
                                    $deduccion_fondo_solidaridad = intval($detalle->deduccion_fondo_solidaridad, 0) . '.00';
                                    $devengado = intval($detalle->devengado, 0) . '.00';
                                    $auxilio_transporte = intval($detalle->auxilio_transporte, 0) . '.00';
                                    $valor_pago_prima = intval($detalle->valor_pago_prima, 0) . '.00';
                                    $valor_pago_cesantias = intval($detalle->valor_pago_cesantias, 0) . '.00';
                                    $deducciones = intval($detalle->deduccion, 0) . '.00';
                                    $pago_intereses_cesantias = intval($detalle->valor_pago_intereses, 0) . '.00';
                                    
                                    if($detalle->codigo_incapacidad <> ''){
                                        $tipo_incapacidad = $detalle->configuracionIncapacidad->codigo_api_nomina;
                                    }else{
                                        $tipo_incapacidad = 0;
                                    }   
                                    
                                    //DEVENGADOS
                                    if($detalle->id_agrupado == 1){ //salario basico
                                        $dataBody["accrued"]["worked_days"] = $detalle->total_dias;
                                        $dataBody["accrued"]["salary"] = $devengado;
                                    }elseif ($detalle->id_agrupado == 2){ //auxilio de transporte
			                $dataBody["accrued"]["transportation_allowance"] =  $auxilio_transporte;
                                    }elseif ($detalle->id_agrupado == 3){ //horas extras diurnas
                                        if(!isset($dataBody["accrued"]['HEDs'])){
                                            $dataBody["accrued"]['HEDs'] = [];
                                        }
                                        $dataBody["accrued"]['HEDs'][] = [
                                            "start_time" => "2024-12-16T10:00:00",
                                            "start_date" => "2024-12-16T10:00:00",
                                            "end_time" => "2024-12-16T10:00:00",
                                            "end_date" => "2024-12-16T10:00:00",
                                            "quantity" => "2",
                                            "percentage" => 1,
                                            "payment" => "27500"  
                                        ]; 
                                    }elseif ($detalle->id_agrupado == 9){ // incapacidades
                                        if(!isset($dataBody["accrued"]['work_disabilities'])){
                                              $dataBody["accrued"]['work_disabilities'] = [];
                                        }
                                        $dataBody["accrued"]['work_disabilities'][] = [ 
                                            "start_date" => "$detalle->inicio_incapacidad",
                                            "end_date" => "$detalle->final_incapacidad",
                                            "quantity" => "$detalle->dias_incapacidad",
                                            "type" => "$tipo_incapacidad",
                                            "payment" => $valor_pago_incapacidad

                                        ];
                                            
                                    }elseif($detalle->id_agrupado == 10){ //icencias de maternida
                                        if(!isset($dataBody["accrued"]['maternity_leave'])){
                                            $dataBody["accrued"]['maternity_leave'] = [];
                                        }
                                        $dataBody["accrued"]['maternity_leave'][] = [
                                            "start_date" => "$detalle->inicio_licencia",
                                            "end_date" => "$detalle->final_licencia",
                                            "quantity" => "$detalle->dias_licencia",
                                            "payment" => $valor_pago_licencia
                                            
                                        ];
                                    }elseif ($detalle->id_agrupado == 8){ //LICENCIAS REMUNERADAS
                                        if(!isset($dataBody["accrued"]['paid_leave'])){
                                             $dataBody["accrued"]['paid_leave'] = [];
                                        }
                                        $dataBody["accrued"]['paid_leave'][] = [
                                            "start_date" => "$detalle->inicio_licencia",
                                            "end_date" => "$detalle->final_licencia",
                                            "quantity" => "$detalle->dias_licencia",
                                            "payment" => $valor_pago_licencia
                                               
                                        ];
                                        
                                    }elseif ($detalle->id_agrupado == 11){ //PRIMAS DE SERVICIO
                                        $dataBody["accrued"]['service_bonus'] = [
                                            [
                                               "quantity" => "$detalle->dias_prima",
                                               "payment" => $valor_pago_prima,
                                               "paymentNS" => 0
                                            ],
                                        ];  
                                    }elseif ($detalle->id_agrupado == 12){ //CESANTIAS
                                        $dataBody["accrued"]['severance'] = [
                                            [
                                               "payment" => $valor_pago_cesantias,
                                               "percentage" => 12,
                                               "interest_payment" => 0
                                            ],
                                        ];
                                    }elseif ($detalle->id_agrupado == 13){ //INTERESES A CESANTIAS
                                        $dataBody["accrued"]['severance'] = [
                                            [
                                               "payment" => 0,
                                               "percentage" => 0,
                                               "interest_payment" => $pago_intereses_cesantias
                                            ],
                                        ];    
                                    }elseif ($detalle->id_agrupado == 16){ //BONIFICACIONES
                                       if(!isset($dataBody["accrued"]['bonuses'])){
                                           $dataBody["accrued"]['bonuses'] = [];
                                       }
                                       $dataBody["accrued"]['bonuses'][] =  [
                                             
                                               "no_salary_bonus" => $devengado,
                                        ];
                                    }elseif ($detalle->id_agrupado == 19){ //BONIFICACIONES PRESTACIONES
                                       if(!isset($dataBody["accrued"]['bonuses'])){
                                           $dataBody["accrued"]['bonuses'] = [];
                                       }
                                       $dataBody["accrued"]['bonuses'][] =  [
                                               "salary_bonus" => $devengado,
                                               "no_salary_bonus" => 0,
                                        ];   
                                    }elseif ($detalle->id_agrupado == 15){ //comisiones  
                                        $dataBody["accrued"]["commissions"] = [
                                            [    
                                                "commission" => $devengado,
                                            ],
                                        ]; 
                                     
                                    }elseif ($detalle->id_agrupado == 20){ //VACACIONES
                                        if(!isset($dataBody["accrued"]['paid_vacation'])){ 
                                            $dataBody["accrued"]['paid_vacation'] = [];
                                        }
                                        $dataBody["accrued"]['paid_vacation'][] = [
                                            "quantity" => "$detalle->total_dias",
                                            "payment" => "$detalle->devengado"
                                        ];    
                                        
                                    }elseif ($detalle->id_agrupado == 21){ //LICENCIAS NO REMUNERADAS
                                        if(!isset($dataBody["accrued"]['non_paid_leave'])){ //si no existes lo declaracion vacio
                                            $dataBody["accrued"]['non_paid_leave'] = [];
                                        }
                                        $dataBody["accrued"]['non_paid_leave'][] = [
                                            "start_date" => "$detalle->inicio_licencia",
                                            "end_date" => "$detalle->final_licencia",
                                            "quantity" => "$detalle->dias_licencia_noremuneradas"
                                        ];

                                    }elseif ($detalle->id_agrupado == 18){//REINTEGRO O REEMBOLSO
                                        $dataBody["accrued"]["refund"] = $devengado;    
                                    }   
                                    
                                    $dataBody["accrued"]['accrued_total'] = $total_devengado;
                                    //FIN CONCEPTOS DEVENGADOS
                                    
                                    //INICIO DEDUCCIONES
                                    if($detalle->id_agrupado == 4){ //pension
                                        $dataBody["deductions"]["pension_type_law_deductions_id"] = $pension_type_law_deductions_id;
                                        $dataBody["deductions"]["pension_deduction"] = $deduccion_pension;
                                        
                                    }elseif ($detalle->id_agrupado == 5){ //salud
                                        $dataBody["deductions"]["eps_type_law_deductions_id"] = $eps_type_law_deductions_id;
                                        $dataBody["deductions"]["eps_deduction"] = $deduccion_eps;
                                        
                                    }elseif ($detalle->id_agrupado == 6){ //fondo de solidarida
                                        $dataBody["deductions"]["voluntary_pension"] = $deduccion_fondo_solidaridad; 
                                    }elseif ($detalle->id_agrupado == 7){ // prestamos empresa y otras deducciones
                                        if(!isset($dataBody["deductions"]['other_deductions'])){
                                          $dataBody["deductions"]["other_deductions"] = [];  
                                        }
                                        $dataBody["deductions"]['other_deductions'][] = [
                                            "other_deduction" => $deducciones, 
                                        ];    
                                        
                                    }elseif ($detalle->id_agrupado == 14){ // Libranzas prestamo
                                        if(!isset($dataBody["deductions"]['orders'])){
                                          $dataBody["deductions"]["orders"] = [];  
                                        }
                                        $dataBody["deductions"]['orders'][] = [
                                            "description" => "$detalle->descripcion", 
                                            "deduction" => $deducciones 
                                        ];
                                        
                                    }elseif ($detalle->id_agrupado == 17){//prestamo empresa
                                        $dataBody["deductions"]["debt"] = $deducciones;
                                    }
                                    $dataBody["deductions"]['deductions_total'] = $total_deduccion;
                                    
                                    
                                }//CIERRA EL PARA DEL DETALLE DEL PAGO 
                                
                                $dataBody = json_encode($dataBody);
                                
                                //   //EJECUTA EL DATABODY 
                                curl_setopt_array($curl, [
                                    CURLOPT_URL => "https://begranda.com/equilibrium2/public/api-nomina/payroll?key=$API_KEY",
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => $dataBody
                                ]);
                               
                                $response = curl_exec($curl);
                                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                                if (curl_errno($curl)) {
                                   throw new Exception(curl_error($curl));
                                }
                                curl_close($curl);
                                $data = json_decode($response, true);
                                Yii::info("Respuesta completa de la API desde Begranda: $response", __METHOD__);
                                // Verificar errores de conexión o códigos HTTP inesperados
                                if ($response === false || $httpCode !== 200) {
                                    $error = $response === false ? curl_error($curl) : "HTTP $httpCode";
                                    Yii::$app->getSession()->setFlash('error', 'Hubo un problema al comunicarse con la DIAN. Intenta reenviar más tarde.');
                                    Yii::error("Error en la solicitud CURL: $error", __METHOD__);
                                    return $this->redirect(['programacion-nomina/listar_nomina_electronica']);
                                }
                                if(isset($data) && isset($data['add']['ResponseDian']) && $data['add']['ResponseDian']['Envelope']['Body']['SendNominaSyncResponse']['SendNominaSyncResult']['IsValid'] == "true"){
                                    if (isset($data['add']['cune'])) {
                                        $cune = $data['add']['cune'];
                                        $documento->cune = $cune;
                                        $documento->fecha_envio_begranda = date("Y-m-d H:i:s");
                                        $documento->fecha_recepcion_dian = date("Y-m-d H:i:s");
                                        $qrstr = $data['add']['QRStr'];
                                        $documento->qrstr = $qrstr;
                                        $documento->exportado_nomina = 1;
                                        $documento->save(false);
                                        $contador += 1;                               
                                    }    
                               }else{
                                   $errors = [];
					// Documento no procesado por la DIAN
					if(isset($data["errors"])){ // Control Errores Begranda
						$errors = $data["errors"];
					}else if(isset($data['ResponseDian'])){ // Control de Errores DIAN
						$errors = $data['ResponseDian']['Envelope']['Body']['SendNominaSyncResponse']['SendNominaSyncResult']['ErrorMessage'];
					}else{
                                            $errorMessage = isset($data['message']) ? $data['message'] : 'Error desconocido';
                                            // Mostrar el mensaje específico de la API
                                            Yii::$app->getSession()->setFlash('error', "No se pudo enviar el documento electronico. Error: $errorMessage.");
                                            Yii::error("Error al reenviar documento de nomina No ($consecutivo): " . print_r($data, true), __METHOD__);
                                          
                                        }
                               }
                            } 
                            //Cierre la confirmacion de chequeo de registro que se van a envir.
                            
                        }//CIERRA EL PROCESO PARA
                        
                        Yii::$app->getSession()->setFlash('success','Se enviaron ('.$contador.') registros a la DIAN para el proceso de nomina electronica.');
                        return $this->redirect(['programacion-nomina/listar_nomina_electronica']);
                    }else{
                        Yii::$app->getSession()->setFlash('error','Debe de seleccionar el registro para enviar a la DIAN. ');
                    }
                }    
                return $this->render('listar_documentos_electronicos', [
                            'model' => $model,
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
    
    //CONSULTAR DOCUMENTOS ENVIADO
    public function actionSearch_documentos_electronicos($token = 2) {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 152])->all()) {
                $form = new \app\models\FormFiltroDocumentoElectronico();
                $documento = null;
                $empleado = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $documento = Html::encode($form->documento);
                        $empleado = Html::encode($form->empleado);
                        $table = \app\models\NominaElectronica::find()
                                ->andFilterWhere(['like', 'nombre_completo', $empleado])
                                ->andFilterWhere(['=', 'documento_empleado', $documento])
                                ->andWhere(['=', 'exportado_nomina', 1]);
                        $table = $table->orderBy('numero_nomina_electronica ASC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 30,
                            'totalCount' => $count->count()
                        ]);
                        $model = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = \app\models\NominaElectronica::find()->Where(['>', 'numero_nomina_electronica', 0])
                                                                  ->andWhere(['=', 'exportado_nomina', 1])
                                                                  ->orderBy('numero_nomina_electronica ASC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 30,
                        'totalCount' => $count->count(),
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                }
                $to = $count->count();
                return $this->render('search_documentos_electronicos', [
                            'model' => $model,
                            'form' => $form,
                            'token' => $token,
                            'pagination' => $pages,
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }    
                
    //COMPROBANTES DE NOMINAS
    public function actionComprobantepagonomina() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 88])->all()) {
                $form = new FormFiltroComprobantePagoNomina();
                $id_grupo_pago = null;
                $id_tipo_nomina = null;
                $id_empleado = null;
                $cedula_empleado = null;
                $fecha_desde = null;
                $fecha_hasta = null;
          
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_grupo_pago = Html::encode($form->id_grupo_pago);
                        $id_tipo_nomina = Html::encode($form->id_tipo_nomina);
                        $id_empleado = Html::encode($form->id_empleado);
                        $cedula_empleado = Html::encode($form->cedula_empleado);
                        $fecha_desde = Html::encode($form->fecha_desde);
                        $fecha_hasta = Html::encode($form->fecha_hasta);
                        $table = ProgramacionNomina::find()
                                ->andFilterWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                                ->andFilterWhere(['=', 'id_tipo_nomina', $id_tipo_nomina])
                                ->andFilterWhere(['=', 'id_empleado', $id_empleado])
                                ->andFilterWhere(['=', 'cedula_empleado', $cedula_empleado])
                                ->andFilterWhere(['between', 'fecha_desde', $fecha_desde, $fecha_hasta]);
                        $table = $table->orderBy('id_programacion DESC');
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
                            $check = isset($_REQUEST['id_programacion DESC']);
                            $this->actionExcelconsultaPago($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = ProgramacionNomina::find()
                             ->orderBy('id_programacion DESC');
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
                        $this->actionExcelconsultaPago($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('comprobantepagonomina', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages, 'nombre_empleado' => $id_empleado, 
                            'fecha_inicio' => $fecha_desde, 'fecha_corte' => $fecha_hasta,
                            'grupo_pago' => $id_grupo_pago,'tipo_nomina' => $id_tipo_nomina,
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }

    public function actionView($id, $id_grupo_pago, $fecha_desde, $fecha_hasta) {
        $model = PeriodoPagoNomina::findOne($id);
        $intereses = InteresesCesantia::find()->where(['=','id_periodo_pago_nomina', $id])->orderBy('id_programacion ASC')->all();
        $detalles = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->orderBy('id_programacion ASC')->all();
        $incapacidad = Incapacidad::find()->where(['=', 'id_grupo_pago', $id_grupo_pago])
                        ->andWhere(['<=', 'fecha_inicio', $fecha_hasta])
                        ->andWhere(['>=', 'fecha_final', $fecha_desde])
                        ->orderBy('identificacion ASC')->all();
        $licencia = Licencia::find()->where(['=', 'id_grupo_pago', $id_grupo_pago])
                        ->andWhere(['<=', 'fecha_desde', $fecha_hasta])
                        ->andWhere(['>=', 'fecha_hasta', $fecha_desde])
                        ->orderBy('identificacion ASC')->all();
        $novedad_tiempo = NovedadTiempoExtra::find()->where(['=', 'id_periodo_pago_nomina', $id])->orderBy('id_empleado ASC')->all();
        $credito_empleado = Credito::find()->where(['<=', 'fecha_inicio', $fecha_hasta])
                        ->andWhere(['=', 'estado_credito', 1])
                        ->andWhere(['=', 'estado_periodo', 1])
                        ->andWhere(['>', 'saldo_credito', 0])
                        ->andWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                        ->orderBy('id_empleado DESC')->all();
        
        
        if (Yii::$app->request->post()) {

           if (isset($_POST["id_programacion"])) {
                foreach ($_POST["id_programacion"] as $intCodigo) {
                    try {
                        $eliminar = ProgramacionNomina::findOne($intCodigo);
                        $eliminar->delete();
                        Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                        $this->redirect(["programacion-nomina/view", 'id' => $id, 'id_grupo_pago' => $id_grupo_pago, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
                    } catch (IntegrityException $e) {
                        Yii::$app->getSession()->setFlash('error', 'Error al eliminar la programacion de nomina, tiene registros asociados en otros procesos de la nómina');
                    } catch (\Exception $e) {
                        Yii::$app->getSession()->setFlash('error', 'Error al eliminar la programacion de nomina, tiene registros asociados en otros procesos');
                    }
                }
            } 
        }

        return $this->render('view', [
                    'detalles' => $detalles,
                    'model' => $model,
                    'incapacidad' => $incapacidad,
                    'licencia' => $licencia,
                    'novedad_tiempo' => $novedad_tiempo,
                    'credito_empleado' => $credito_empleado,
                    'intereses' => $intereses,
        ]);
    }
    
    public function actionDetallepagonomina($id_programacion)
    {
        $model = ProgramacionNomina::findOne($id_programacion);
        return $this->render('detallepagonomina', [
                    'id_programacion' => $id_programacion,
                    'model' => $model,        
        ]); 
    }

    public function actionNovedadeserror($id, $id_grupo_pago, $fecha_desde, $fecha_hasta) {
        Yii::$app->getSession()->setFlash('error', 'Debe de cargar  los empleados en la nomina para generar las novedades!');
        return $this->redirect(['view',
                    'id' => $id,
                    'id_grupo_pago' => $id_grupo_pago,
                    'fecha_desde' => $fecha_desde,
                    'fecha_hasta' => $fecha_hasta,
        ]);
    }

    public function actionCargar($id, $id_grupo_pago, $fecha_desde, $fecha_hasta, $tipo_nomina) {
        $model = PeriodoPagoNomina::findOne($id);
        $registros = 0;
        $configuracion_salario = ConfiguracionSalario::find()->where(['=', 'estado', 1])->one();
        if($tipo_nomina == 1){
            $registros = Contrato::find()
                    ->where(['=', 'id_grupo_pago', $model->id_grupo_pago])
                    ->andWhere(['<=', 'fecha_inicio', $model->fecha_hasta])
                    ->andWhere(['>=', 'fecha_final', $model->fecha_desde])
                    ->andWhere(['<','ultimo_pago', $model->fecha_hasta])
                    ->all();
        }else{
            if($tipo_nomina == 2){
                $registros = Contrato::find()
                        ->where(['=', 'id_grupo_pago', $model->id_grupo_pago])
                        ->andWhere(['<=', 'fecha_inicio', $model->fecha_hasta])
                        ->andWhere(['=', 'contrato_activo', 1])
                        ->andWhere(['<','ultima_prima', $model->fecha_hasta])
                        ->all();
            }else{
                if($tipo_nomina == 3){
                    $registros = Contrato::find()
                        ->where(['=', 'id_grupo_pago', $model->id_grupo_pago])
                        ->andWhere(['<=', 'fecha_inicio', $model->fecha_hasta])
                        ->andWhere(['=', 'contrato_activo', 1])
                        ->andWhere(['<','ultima_cesantia', $model->fecha_hasta])
                        ->all();
                }
            }
        }    
        $registroscargados = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->all();
        $cont = 0;
        if($registros == 0){
            Yii::$app->getSession()->setFlash('warning', 'Este grupo de pago a la fecha no tiene empleados con contratos activos!');
        }else{
            foreach ($registros as $val) {
                if (!ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->andWhere(['=', 'id_contrato', $val->id_contrato])->one()) {
                    $table = new ProgramacionNomina();
                    $table->id_grupo_pago = $model->id_grupo_pago;
                    $table->id_periodo_pago_nomina = $id;
                    $table->id_tipo_nomina = $tipo_nomina;
                    $table->id_contrato = $val->id_contrato;
                    $table->id_empleado = $val->id_empleado;
                    $table->cedula_empleado = $val->empleado->identificacion;
                    $table->salario_contrato = $val->salario;
                    $table->fecha_inicio_contrato = $val->fecha_inicio;
                    if ($val->contrato_activo == 0) {
                        $table->fecha_final_contrato = $val->fecha_final;
                    } 
                    if($tipo_nomina == 1 ){
                        $vacacion = \app\models\Vacaciones::find()->where(['=','documento', $val->identificacion])
                                                                  ->andWhere(['>=','fecha_desde_disfrute', $fecha_desde])
                                                                  ->orderBy('id_vacacion ASC')->one();
                        if ($vacacion){
                             $table->fecha_inicio_vacacion = $vacacion->fecha_desde_disfrute;
                             $table->fecha_final_vacacion = $vacacion->fecha_hasta_disfrute;
                        }
                        $vacacion = \app\models\Vacaciones::find()->where(['=','documento', $val->identificacion])
                                                                  ->andWhere(['<=','fecha_hasta_disfrute', $fecha_hasta])
                                                                  ->andWhere(['>','fecha_hasta_disfrute', $fecha_desde])
                                                                  ->orderBy('id_vacacion ASC')->one();
                        if ($vacacion){
                             $table->fecha_inicio_vacacion = $vacacion->fecha_desde_disfrute;
                             $table->fecha_final_vacacion = $vacacion->fecha_hasta_disfrute;
                        }
                        
                    }
                    $table->fecha_desde = $model->fecha_desde;
                    $table->fecha_hasta = $model->fecha_hasta;
                    $table->fecha_ultima_prima= $val->ultima_prima;
                    $table->fecha_ultima_cesantia = $val->ultima_cesantia;
                    $table->fecha_ultima_vacacion = $val->ultima_vacacion;
                    $table->fecha_real_corte = $model->fecha_real_corte;
                    $table->dias_pago = $model->dias_periodo;
                    $table->tipo_salario = $val->tipo_salario;
                    $table->usuariosistema = Yii::$app->user->identity->username;
                    $tiempo = TiempoServicio::find()->where(['=', 'id_tiempo', $val->id_tiempo])->one();
                    $table->factor_dia = $tiempo->horas_dia;
                    if ($table->factor_dia == 4) {
                        $table->salario_medio_tiempo = $configuracion_salario->salario_minimo_actual;
                    }
                    $table->save(false);
                    $cont = $cont + 1;
                    $model->cantidad_empleado = $cont;

                }
            }        $model->save(false);
        }    
       if ($registros == 0) {
            $this->redirect(["programacion-nomina/view", 'id' => $id,
                'id_grupo_pago' => $id_grupo_pago,
                'fecha_desde' => $fecha_desde,
                'fecha_hasta' => $fecha_hasta,
            ]);
        } else {

            $this->redirect(["programacion-nomina/view", 'id' => $id,
                'id_grupo_pago' => $id_grupo_pago,
                'fecha_desde' => $fecha_desde,
                'fecha_hasta' => $fecha_hasta,
            ]);
        }
    }

    //agregar un empleado para hacerla la nomina.
    
     public function actionAgregarempleado($id, $id_grupo_pago, $fecha_hasta, $fecha_desde, $tipo_nomina)
    {
        $contratos = Contrato::find()->where(['=','contrato_activo', 1])->andWhere(['<=','fecha_inicio', $fecha_hasta])->andWhere(['=','id_grupo_pago', $id_grupo_pago])->orderBy('id_contrato asc')->all();
        $form = new FormMaquinaBuscar();
        $q = null;
        $mensaje = '';
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                if ($q){
                    $contratos = Contrato::find()
                            ->where(['like','identificacion',$q])
                            ->orwhere(['like','descripcion',$q])
                            ->orderBy('id_empleado asc')
                            ->all();
                }               
            } else {
                $form->getErrors();
            }                    
        } else {
            $contratos = Contrato::find()->where(['=','contrato_activo', 1])->andWhere(['<=','fecha_inicio', $fecha_hasta])->andWhere(['=','id_grupo_pago', $id_grupo_pago])->orderBy('id_contrato asc')->all();
        }
        if (isset($_POST["id_contrato"])) {
                $intIndice = 0;
                $cont = 0;
                foreach ($_POST["id_contrato"] as $intCodigo) {
                    $table = new ProgramacionNomina();
                    $contrato = Contrato::find()->where(['id_contrato' => $intCodigo])->one();
                    $periodo = PeriodoPagoNomina::findOne($id);
                    $configuracion_salario = ConfiguracionSalario::find()->where(['=', 'estado', 1])->one();
                    $table->id_grupo_pago = $id_grupo_pago;
                    $table->id_periodo_pago_nomina = $id;
                    $table->id_tipo_nomina = $tipo_nomina;
                    $table->id_contrato = $contrato->id_contrato;
                    $table->id_empleado = $contrato->id_empleado;
                    $table->cedula_empleado = $contrato->identificacion;
                    $table->salario_contrato = $contrato->salario;
                    $table->fecha_inicio_contrato = $contrato->fecha_inicio;
                    $table->fecha_desde = $fecha_desde;
                    $table->fecha_hasta = $fecha_hasta;
                    $table->fecha_ultima_prima= $contrato->ultima_prima;
                    $table->fecha_ultima_cesantia = $contrato->ultima_cesantia;
                    $table->fecha_ultima_vacacion = $contrato->ultima_vacacion;
                    $table->fecha_real_corte = $fecha_hasta;
                    $table->dias_pago = $periodo->dias_periodo;
                    $table->tipo_salario = $contrato->tipo_salario;
                    $table->usuariosistema = Yii::$app->user->identity->username;
                    $tiempo = TiempoServicio::find()->where(['=', 'id_tiempo', $contrato->id_tiempo])->one();
                    $table->factor_dia = $tiempo->horas_dia;
                    if ($table->factor_dia == 4) {
                        $table->salario_medio_tiempo = $configuracion_salario->salario_minimo_actual;
                    }
                    $table->save(false); 
                    $cont += 1;
                    $periodo->cantidad_empleado = $cont;
                }
                $periodo->save(false);
               $this->redirect(["programacion-nomina/view", 'id' => $id,
                                'id_grupo_pago' => $id_grupo_pago,
                                'fecha_hasta' => $fecha_hasta,
                                'fecha_desde' => $fecha_desde,
                                'tipo_nomina' => $tipo_nomina,
                               ]);
            }else{
                
            }
        return $this->render('_formagregarempleado', [
            'contratos' => $contratos,            
            'mensaje' => $mensaje,
            'id' => $id,
            'id_grupo_pago' => $id_grupo_pago,
            'fecha_hasta' => $fecha_hasta,
            'fecha_desde' => $fecha_desde,
            'tipo_nomina' => $tipo_nomina,
            'form' => $form,

        ]);
    }
    
    public function actionNuevo() {
        $model = new FormPeriodoPagoNomina();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                   $table = new PeriodoPagoNomina();
                    $table->id_grupo_pago = $model->id_grupo_pago;
                    $table->id_periodo_pago = $model->id_periodo_pago;
                    $table->id_tipo_nomina = $model->id_tipo_nomina;
                    $table->fecha_desde = $model->fecha_desde;
                    $table->fecha_hasta = $model->fecha_hasta;
                    $table->fecha_real_corte = $table->fecha_hasta;
                    $table->estado_periodo = 0;
                    $table->dias_periodo = $model->dias_periodo;
                    $table->usuariosistema = Yii::$app->user->identity->username;
                    if ($table->save(false)) {
                        $this->redirect(["programacion-nomina/index"]);
                    } else {
                        $msg = "error";
                    }
            } else {
                $model->getErrors();
            }
        }
        return $this->render('form', ['model' => $model]);
    }

    public function actionEditar($id) {
        $validar = PeriodoPagoNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->one();
        $model = new FormPeriodoPagoNomina();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if (ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->all() or $validar->estado_periodo == 1) {
            Yii::$app->getSession()->setFlash('warning', 'No se puede modificar la información, tiene detalles asociados');
        } else {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate()) {
                    $table = PeriodoPagoNomina::find()->where(['id_periodo_pago_nomina' => $id])->one();
                    if ($table) {
                       $periodo = PeriodoPago::find()->where(['=', 'id_periodo_pago', $model->id_periodo_pago])->one();
                        if ($periodo->dias == $model->dias_periodo) {
                            $table->id_grupo_pago = $model->id_grupo_pago;
                            $table->id_periodo_pago = $model->id_periodo_pago;
                            $table->id_tipo_nomina = $model->id_tipo_nomina;
                            $table->fecha_desde = $model->fecha_desde;
                            $table->fecha_hasta = $model->fecha_hasta;
                            $table->fecha_real_corte = $table->fecha_hasta;
                            $table->dias_periodo = $model->dias_periodo;
                            if ($table->save(false)) {
                                $this->redirect(["programacion-nomina/index"]);
                            }
                        } else {
                            Yii::$app->getSession()->setFlash('error', 'El periodo de pago no corresponde al grupo de pago, favor validar el periodo.');
                        }
                    } else {
                        $msg = "El registro seleccionado no ha sido encontrado";
                        $tipomsg = "danger";
                    }
                } else {
                    $model->getErrors();
                }
            }
        }
        if (Yii::$app->request->get("id")) {
            $table = PeriodoPagoNomina::find()->where(['id_periodo_pago_nomina' => $id])->one();
            if ($table) {
                $model->id_tipo_nomina = $table->id_tipo_nomina;
                $model->id_periodo_pago = $table->id_periodo_pago;
                $model->id_grupo_pago = $table->id_grupo_pago;
                $model->dias_periodo = $table->dias_periodo;
                $model->fecha_desde = $table->fecha_desde;
                $model->fecha_hasta = $table->fecha_hasta;
            } else {
                return $this->redirect(["programacion-nomina/index"]);
            }
        } else {
            return $this->redirect(["programacion-nomina/index"]);
        }
       return $this->render("form", ["model" => $model]);
    }

    public function actionEliminar($id) {

        if (Yii::$app->request->post()) {
            $periodo = PeriodoPagoNomina::findOne($id);
            if ((int) $id) {
                try {
                    PeriodoPagoNomina::deleteAll("id_periodo_pago_nomina=:id_periodo_pago_nomina", [":id_periodo_pago_nomina" => $id]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    $this->redirect(["programacion-nomina/index"]);
                } catch (IntegrityException $e) {
                    $this->redirect(["programacion-nomina/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el periodo de pago Nro :' . $periodo->id_periodo_pago_nomina . ', tiene registros asociados en otros procesos');
                } catch (\Exception $e) {

                    $this->redirect(["programacion-nomina/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el periodo de pago Nro : ' . $periodo->id_periodo_pago_nomina . ', tiene registros asociados en otros procesos');
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el registros, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("programacion-nomina/index") . "'>";
            }
        } else {
            $this->redirect(["programacion-nomina/index"]);
        }
    }

    // funciones del proceso de nomina (Validar, procesar y aplicar pago)

    public function actionProcesarregistros($id, $id_grupo_pago, $fecha_desde, $fecha_hasta, $tipo_nomina,  $year=NULL) {
        if($tipo_nomina == 1){ // Este condicional permite saber si el tipo de pago es de nomina
            $total_dias = 0;
            $salarios = ConceptoSalarios::find()->where(['=', 'inicio_nomina', 1])->one();
            $codigo_salario = $salarios->codigo_salario;
            $registros = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->all();
            //codigo para auxilio de transporte
            $configuracion_salarios = ConfiguracionSalario::find()->where(['=', 'estado', 1])->one();
            $auxilio = $configuracion_salarios->auxilio_transporte_actual;
            $salario_transporte = ConceptoSalarios::find()->where(['=', 'auxilio_transporte', 1])->one();
            $_transporte = $salario_transporte->codigo_salario;
            //controladores de salario y auxilio de transporte
            foreach ($registros as $val) {
                $total_dias = $this->salario($val, $codigo_salario, $id_grupo_pago);
                $this->Auxiliotransporte($val, $_transporte, $total_dias, $auxilio, $fecha_desde, $fecha_hasta, $id_grupo_pago);
            }
            //codigo que envia parametros de novedades_ horas extras
            $novedad_tiempo_extra = NovedadTiempoExtra::find()->where(['=', 'id_periodo_pago_nomina', $id])->andWhere(['>', 'nro_horas', 0])->orderBy('id_empleado DESC')->all();
            $contNovedad = count($novedad_tiempo_extra);
            if ($contNovedad > 0) {
                foreach ($novedad_tiempo_extra as $tiempo_extra) {
                   $this->Novedadtiempoextra($tiempo_extra, $id, $fecha_desde, $fecha_hasta);
                }
            }
            //codigo que envia parametros de los creditos
           $creditosempleado = Credito::find()->where(['<=', 'fecha_inicio', $fecha_hasta])
                            ->andWhere(['=', 'estado_credito', 1])
                            ->andWhere(['=', 'estado_periodo', 1])
                            ->andWhere(['>', 'saldo_credito', 0])
                            ->andWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                            ->andWhere(['=','id_tipo_pago', $tipo_nomina])
                            ->orderBy('id_empleado DESC')->all();
            $contCredito = count($creditosempleado);
            if ($contCredito > 0) {
                foreach ($creditosempleado as $credito) {
                   $this->Modulocredito($fecha_desde, $fecha_hasta, $credito, $id);
                }
            }

            //codigo que envia los descuentos y adiciones por fecha
            $adicion_fecha = PagoAdicionalPermanente::find()->where(['=', 'fecha_corte', $fecha_hasta])
                    ->andWhere(['=', 'estado_registro', 1])
                    ->andWhere(['=', 'estado_periodo', 1])
                    ->andWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                    ->andWhere(['=','aplicar_prima', 0])
                    ->all();
            $contAdicion = count($adicion_fecha);
            if ($contAdicion > 0) {
                foreach ($adicion_fecha as $adicionfecha) {
                   $this->Moduloadicionfecha($fecha_desde, $fecha_hasta, $adicionfecha, $id);
                }
            }
            //codigo que valide el adicion al pago permanente
            $grupo_pago = GrupoPago::findone($id_grupo_pago);
            $adicion_permanente = PagoAdicionalPermanente::find()->where(['=', 'permanente', 1])
                            ->andWhere(['=', 'estado_registro', 1])
                            ->andWhere(['=', 'estado_periodo', 1])
                            ->andWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                            ->all();
            $contAdicionP = count($adicion_permanente);
            if ($contAdicionP > 0) {
                foreach ($adicion_permanente as $adicionpermanente) {
                   $this->Moduloadicionpermanente($fecha_desde, $fecha_hasta, $adicionpermanente, $id, $grupo_pago);
                }
            }
            //codigo que valida las licencias
            $licencias = Licencia::find()->where(['=', 'id_grupo_pago', $id_grupo_pago])
                    ->andWhere(['<=', 'fecha_desde', $fecha_hasta])
                    ->andWhere(['>=', 'fecha_hasta', $fecha_desde])
                    ->all();
            $contLicencia = count($licencias);
            if ($contLicencia > 0) {
                foreach ($licencias as $valor_licencia) {
                   $this->ModuloLicencias($fecha_desde, $fecha_hasta, $valor_licencia, $id);
                }
            }
            // codigo que valida las incapacidades del mismo periodo
            $incapacidad = Incapacidad::find()->where(['=', 'id_grupo_pago', $id_grupo_pago])
                    ->andWhere(['<=', 'fecha_inicio', $fecha_hasta])
                    ->andWhere(['>=', 'fecha_final', $fecha_desde])
                    ->all();
            $contIncapacidad = count($incapacidad);
            
            if ($contIncapacidad > 0) {
                foreach ($incapacidad as $valor_incapacidad) {
                  $this->ModuloIncapacidad($fecha_desde, $fecha_hasta, $valor_incapacidad, $id);
                  
                }
            }
            
            //codigo que validad incapacidades del mes anterior
            $incapacidad_mes_anterior = Incapacidad::find()->where(['=','fecha_aplicacion', $fecha_hasta])->andWhere(['=','estado_incapacidad_adicional', 1])->all();
            if($incapacidad_mes_anterior > 0){
                foreach ($incapacidad_mes_anterior as $valor_incapacidad){
                    $this->ModuloIncapacidad($fecha_desde, $fecha_hasta, $valor_incapacidad, $id);  
                }
            }
            //codigo que actualiza el estado_generado de la tabla programacion_nomina
            $detalle_nomina = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
            foreach ($detalle_nomina as $validar):
                $validar->estado_generado = 1;
                $validar->save(false);
            endforeach;
        //TERMINA EL CICLO DE LA NOMINA    
        }else{
            if($tipo_nomina == 2){ // este condicional permite generar las primas del personal
                $tabla_prima = ConfiguracionPrestaciones::findOne(1);
                $nomina = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->orderBy('id_programacion ASC')->all();
                $fecha_inicio_prima = strtotime(date($fecha_desde, time()));
                $year = ($year==NULL)? date('Y'):$year;
                if (($year%4 == 0 && $year%100 != 0) || $year%400 == 0 ){
                   $ano = 1;
                }else{
                    $ano = 2;
                }  
                foreach ($nomina as $prima_semestral):
                    $contador = 0; $sw = 0; $total_dias = 0;
                    $contador2 = 0; $salario_adicional = 0;
                    $salario_promedio = 0;
                    $vlr_prima = 0;
                    $nro_dias_licencia  = 0;
                    $ibp_prima_anterior = 0;
                    $total_ibp = 0;
                    $total_dias_adicional = 0;
                    $contrato_laboral = Contrato::find()->where(['=','id_contrato', $prima_semestral->id_contrato])->one();
                    $ibp_prima_anterior = $contrato_laboral->ibp_prima_inicial;
                    $fecha_contrato = strtotime($contrato_laboral->fecha_inicio);
                    $fecha_inicio_contrato = $prima_semestral->fecha_inicio_contrato;
                    //SUBPROCESO QUE CALCULA LOS DIAS.
                    if(strtotime($prima_semestral->fecha_inicio_contrato) < strtotime($prima_semestral->fecha_ultima_prima)){
                       $sw = 1;
                    } else {
                         if(strtotime($prima_semestral->fecha_inicio_contrato) == strtotime($prima_semestral->fecha_ultima_prima)){
                             $sw = 2;
                         }else{
                             $sw = 3;
                         }
                    }  
                    $total_dias = $this->CrearPrimaSemestral($sw, $prima_semestral, $ano);
                     // FIN CODIGO
                         //ESTE PROCESO VALIDA SI LA NOMINA SE HIZO HASTA EL ULTIMO DIA
                    $dato = 0;
                    if(strtotime($contrato_laboral->ultimo_pago) < strtotime($prima_semestral->fecha_hasta)){
                        $mes_ultimo_pago_nomina = substr($contrato_laboral->ultimo_pago, 5, 2);
                        if($mes_ultimo_pago_nomina == 05){
                          $total_dias_adicional = strtotime($fecha_hasta) - strtotime($contrato_laboral->ultimo_pago);
                          $total_dias_adicional = round($total_dias_adicional / 86400)-1;  
                        }else{
                            $total_dias_adicional = strtotime($fecha_hasta) - strtotime($contrato_laboral->ultimo_pago);
                            $total_dias_adicional = round($total_dias_adicional / 86400);
                        }
                        $dato = 1;
                        $salario_adicional = ($contrato_laboral->salario / 30) * $total_dias_adicional;
                    }
                    $vector_nomina = [];
                    if ($sw == 1){
                  
                        $fecha = date($prima_semestral->fecha_ultima_prima);
                        if($mes_ultimo_pago_nomina == 12){
                           $fecha_inicio_dias = strtotime('1 day', strtotime($fecha));
                        }else{
                             $fecha_inicio_dias = strtotime('2 day', strtotime($fecha));
                        }
                        $fecha_inicio_dias = date('Y-m-d', $fecha_inicio_dias);
                        
                        $vector_nomina = ProgramacionNomina::find()->where(['>=', 'fecha_desde', $fecha_inicio_dias])
                                                                    ->andWhere(['=','id_contrato', $prima_semestral->id_contrato])
                                                                  ->all();
                    }else{
                        if ($sw == 2){
                            $vector_nomina = ProgramacionNomina::find()->where(['>=', 'fecha_inicio_contrato', $prima_semestral->fecha_inicio_contrato])
                                                                    ->andWhere(['=','id_contrato', $prima_semestral->id_contrato])
                                                                    ->all();
                        }else{
                            //otro codigo
                        }  
                    }
                    foreach ($vector_nomina as $suma_ibc):
                         $contador +=  $suma_ibc->ibc_prestacional;
                         $contador2 += $suma_ibc->total_ibc_no_prestacional;
                         if($tabla_prima->aplicar_ausentismo == 1){
                             $contador_licencia = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $suma_ibc->id_programacion])->all();
                             foreach ($contador_licencia as $licencias):
                                 $nro_dias_licencia += $licencias->dias_licencia_descontar;    
                             endforeach;
                         }
                    endforeach;
                    if ($ibp_prima_anterior > 0){
                        $total_ibp = $contador + $contador2 + $ibp_prima_anterior;
                    }else{
                       $total_ibp = $contador + $contador2;
                    }    
                    $nro_dias_licencia = $nro_dias_licencia;
                    $auxilio_transporte_actual = ConfiguracionSalario::find()->where(['=','estado', 1])->one();
                    if($dato == 0){ // SE PREGUNTA SI LA NOMINA SE HIZO HASTA EL ULTIMO PERIODO
                        if($contrato_laboral->tipo_salario == 'FIJO') {
                            $salario_promedio = $contrato_laboral->salario;
                        }else{
                           $salario_promedio = ($total_ibp / $total_dias)* 30;    
                        }
                        $dias_prima_pago_real = $total_dias - $nro_dias_licencia;
                        if($contrato_laboral->auxilio_transporte == 1){
                             $vlr_prima = round(($salario_promedio + $auxilio_transporte_actual->auxilio_transporte_actual)* $dias_prima_pago_real) / 360; // formula de la prima
                         }else{
                             $vlr_prima = round($salario_promedio * $dias_prima_pago_real) / 360;
                         }   
                    }else{
                       
                        if($contrato_laboral->tipo_salario == 'FIJO') {
                            $salario_promedio = $contrato_laboral->salario;
                        }else{
                             $salario_promedio = (($total_ibp + $salario_adicional) / $total_dias)* 30;
                        }
                         $dias_prima_pago_real = $total_dias - $nro_dias_licencia;
                         if($contrato_laboral->auxilio_transporte == 1){
                            $vlr_prima = round(($salario_promedio + $auxilio_transporte_actual->auxilio_transporte_actual)* $dias_prima_pago_real) / 360; // formula de la prima
                         }else{
                            $vlr_prima = round($salario_promedio * $dias_prima_pago_real) / 360;
                         }   
                     }
                     $prognomdetalle = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $prima_semestral->id_programacion])
                                                                         ->andWhere(['=', 'codigo_salario', $tabla_prima->codigo_salario])
                                                                         ->all();
                    
                     
                     if(!$prognomdetalle){
                         $detalle_momina = new ProgramacionNominaDetalle();
                         $detalle_momina->id_programacion = $prima_semestral->id_programacion;
                         $detalle_momina->codigo_salario =  $tabla_prima->codigo_salario;
                         $detalle_momina->dias_reales =  $dias_prima_pago_real;
                         $detalle_momina->vlr_devengado = round($vlr_prima);
                         $detalle_momina->fecha_desde =  $fecha_desde;
                         $detalle_momina->fecha_hasta =  $fecha_hasta;
                         $detalle_momina->id_periodo_pago_nomina = $id;
                         $detalle_momina->insert(false);
                         $prima_semestral->dias_pago = $total_dias;
                         $prima_semestral->dia_real_pagado = $dias_prima_pago_real;
                         $prima_semestral->total_devengado = round($vlr_prima);
                         $prima_semestral->salario_promedio = round($salario_promedio);
                         $prima_semestral->dias_ausentes = $nro_dias_licencia;
                         $prima_semestral->save(false);
                     }    

                   
                endforeach;
                //codigo que actualiza el estado_generado de la tabla programacion_nomina
                $detalle_nomina = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
                foreach ($detalle_nomina as $validar):
                    $validar->estado_generado = 1;
                  $validar->save(false);
                endforeach;
            }else{
                //CODIGO QUE GENERA LAS CESANTIAS
                if ($tipo_nomina == 3){
                    $grupo_pago = GrupoPago::findOne($id_grupo_pago);
                    if(strtotime($grupo_pago->ultimo_pago_nomina) != strtotime($fecha_hasta)){
                        $this->redirect(["programacion-nomina/view", 'id' => $id,
                                        'id_grupo_pago' => $id_grupo_pago,
                                        'fecha_desde' => $fecha_desde,
                                        'fecha_hasta' => $fecha_hasta,
                                       ]);
                        Yii::$app->getSession()->setFlash('warning', 'Para procesar las cesantias de este grupo de pago, todo el personal debe de tener todas las nominas a '. $fecha_hasta .'.');
                    }else{
                        //INICIO DE ACUMULADOS DE NOMINA
                        $configuracion_c= ConfiguracionPrestaciones::findOne(2);
                        $nominas = ProgramacionNomina::find()->where(['=','id_periodo_pago_nomina', $id])->all();
                        $year = ($year==NULL)? date('Y'):$year;
                        if (($year%4 == 0 && $year%100 != 0) || $year%400 == 0 ){
                           $ano = 1;
                        }else{
                            $ano = 2;
                        }  
                        $total_acumulado = 0; $suma = 0; $suma2 = 0;
                        $total_dias_ausentes = 0; $suma3 = 0; $salario_promedio = 0;
                        $dias_reales = 0; $pago_cesantia = 0; $ibp_cesantia_anterior = 0;
                        foreach ($nominas as $cesantias):
                            $vector_nomina = ProgramacionNomina::find()->where(['>=', 'fecha_desde', $cesantias->fecha_desde])
                                                                    ->andWhere(['=','id_contrato', $cesantias->id_contrato])
                                                                    ->all();
                            foreach ($vector_nomina as $acumular):
                                 $suma += $acumular->ibc_prestacional;
                                 $suma2 += $acumular->total_ibc_no_prestacional;
                            endforeach;
                            //INICIO ACUMULADO DE DIAS A DESCONTAR
                            $auxiliar = 0; 
                            if ($configuracion_c->aplicar_ausentismo == 1){
                                $ausentismo = ConfiguracionLicencia::find()->where(['=','ausentismo', 1])->all();
                                foreach ($ausentismo  as $dato):
                                     $nomina = ProgramacionNomina::find()->where(['>=', 'fecha_desde', $cesantias->fecha_desde])
                                                                    ->andWhere(['=','id_contrato', $cesantias->id_contrato])
                                                                    ->all();
                                     foreach ($nomina as $nomina):
                                         $detalle_no = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $nomina->id_programacion])->andWhere(['=','codigo_salario', $dato->codigo_salario])->one();
                                          if($detalle_no){   
                                               $auxiliar += $detalle_no->dias_licencia_descontar; 
                                          }     
                                     endforeach; 
                                endforeach;
                            }
                            $contrato = Contrato::find()->where(['=','id_contrato', $cesantias->id_contrato])->one();
                            $ibp_cesantia_anterior = $contrato->ibp_cesantia_inicial;
                            if($ibp_cesantia_anterior > 0){
                                $total_acumulado = 0;
                                $total_acumulado = $suma + $suma2 + $ibp_cesantia_anterior;
                                $suma = 0; $suma2 = 0 ;
                            }else{
                                $total_acumulado = 0;
                                $total_acumulado = $suma + $suma2;
                                $suma = 0; $suma2 = 0 ; $ibp_cesantia_anterior = 0;
                            }
                           $total_dias_ausentes = $auxiliar;
                            $sw = 0;
                            if(strtotime($cesantias->fecha_inicio_contrato) < strtotime($cesantias->fecha_ultima_cesantia)){
                               $sw = 1;
                            } else {
                              $sw = 2;  
                            } 
                            $total_dias = $this->CrearCesantias($cesantias, $sw, $ano);
                            
                            $salario_promedio = ($total_acumulado / $total_dias) * 30;  
                            if($configuracion_c->aplicar_ausentismo == 1){
                                $dias_reales = ($total_dias ) - $total_dias_ausentes;
                            }else{
                                 $dias_reales = $total_dias;
                            }
                            if($contrato->tipo_salario == 'FIJO'){
                                $salario_promedio = $contrato->salario;  
                            }else{
                                $salario_promedio = ($total_acumulado / $total_dias) * 30;  
                            }
                            if($contrato->auxilio_transporte == 1){
                                $configuracion_transporte = ConfiguracionSalario::find()->where(['=','estado', 1])->one();
                                $pago_cesantia = round((($salario_promedio + $configuracion_transporte->auxilio_transporte_actual)* $dias_reales)/360);
                            }else{
                                $pago_cesantia = round((($salario_promedio)* $dias_reales)/360);
                            }
                            $detalle_nomina = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $cesantias->id_programacion])
                                                                               ->andWhere(['=','codigo_salario', $configuracion_c->codigo_salario])->one();
                            if(!$detalle_nomina){
                                $detalle = new ProgramacionNominaDetalle();
                                $detalle->id_programacion = $cesantias->id_programacion;
                                $detalle->codigo_salario =  $configuracion_c->codigo_salario;
                                $detalle->dias_reales =  $dias_reales;
                                $detalle->vlr_devengado = $pago_cesantia;
                                $detalle->fecha_desde =  $fecha_desde;
                                $detalle->fecha_hasta =  $fecha_hasta;
                                $detalle->id_periodo_pago_nomina = $id;
                               $detalle->insert(false);
                                $cesantias->dia_real_pagado = $dias_reales;
                                $cesantias->dias_pago = $total_dias;
                                $cesantias->total_devengado = $pago_cesantia;
                                $cesantias->dias_ausentes = $total_dias_ausentes;
                                $cesantias->salario_promedio = round($salario_promedio);
                                $cesantias->save(false);
                            }
                        endforeach; //termina el FOREACH DE CESANTIAS    
                        //codigo que actualiza el estado_generado de la tabla programacion_nomina
                        $nomina = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
                        foreach ($nomina as $validar):
                            $validar->estado_generado = 1;
                            $validar->save(false);
                        endforeach;
                    }
                }//TERMINA CICLO DE CESANTIAS
              
            }
        }    
     $this->redirect(["programacion-nomina/view", 'id' => $id,
            'id_grupo_pago' => $id_grupo_pago,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
        ]);
    }// termina el boton de proceso de regitros 
    
    //CODIGO QUE GENERA LOS DIAS DE LAS CESANTIAS
    protected function CrearCesantias($cesantias, $sw, $ano)
    {
       //codigo para aumentar dias
        $mesInicio = 0;
        $anioTerminacion = 0;
        $anioInicio = 0;
        $mesTerminacion = 0;
        $diaTerminacion = 0;
        $diaInicio = 0;
        $fecha = date($cesantias->fecha_ultima_cesantia);
        $fecha_inicio_dias = strtotime('1 day', strtotime($fecha));
        $fecha_inicio_dias = date('Y-m-d', $fecha_inicio_dias);
        if($sw == 1){    
            $fecha_inicio = $fecha_inicio_dias;
            $fecha_termino = $cesantias->fecha_hasta;
            $diaTerminacion = substr($fecha_termino, 8, 8);
            $mesTerminacion = substr($fecha_termino, 5, 2);
            $anioTerminacion = substr($fecha_termino, 0, 4);
            $diaInicio = substr($fecha_inicio, 8, 8);
            $mesInicio = substr($fecha_inicio, 5, 2);
            $anioInicio = substr($fecha_inicio, 0, 4);
          
        }else{
            $fecha_inicio = $cesantias->fecha_inicio_contrato;
            $fecha_termino = $cesantias->fecha_hasta;
            $diaTerminacion = substr($fecha_termino, 8, 8);
            $mesTerminacion = substr($fecha_termino, 5, 2);
            $anioTerminacion = substr($fecha_termino, 0, 4);
            $diaInicio = substr($fecha_inicio, 8, 8);
            $mesInicio = substr($fecha_inicio, 5, 2);
            $anioInicio = substr($fecha_inicio, 0, 4);
        } 
        $mes = 0;
        $febrero = 0;
        $mes = $mesInicio-1;
        if($mes == 2){
            if($ano == 1){
              $febrero = 29;
            }else{
              $febrero = 28;
            }
        }else if($mes <= 7){
            if($mes==0){
             $febrero = 31;
            }else if($mes%2==0){
                 $febrero = 30;
                }else{
                   $febrero = 31;
                }
        }else if($mes > 7){
              if($mes%2==0){
                  $febrero = 31;
              }else{
                  $febrero = 30;
              }
        }
        if(($anioInicio > $anioTerminacion) || ($anioInicio == $anioTerminacion && $mesInicio > $mesTerminacion) || 
            ($anioInicio == $anioTerminacion && $mesInicio == $mesTerminacion && $diaInicio > $diaTerminacion)){
                //mensaje
        }else{
            if($mesInicio <= $mesTerminacion){
                $anios = $anioTerminacion - $anioInicio;
                if($diaInicio <= $diaTerminacion){
                    $meses = $mesTerminacion - $mesInicio;
                    $dies = $diaTerminacion - $diaInicio;
                }else{
                    if($mesTerminacion == $mesInicio){
                       $anios = $anios - 1;
                    }
                    $meses = ($mesTerminacion - $mesInicio - 1 + 12) % 12;
                    $dies = $febrero-($diaInicio - $diaTerminacion);
                }
            }else{
                $anios = $anioTerminacion - $anioInicio - 1;
                if($diaInicio > $diaTerminacion){
                    $meses = $mesTerminacion - $mesInicio -1 +12;
                    $dies = $febrero - ($diaInicio-$diaTerminacion);
                }else{
                    $meses = $mesTerminacion - $mesInicio + 12;
                    $dies = $diaTerminacion - $diaInicio;
                }
            }
          $total_dias = (($anios * 360) + ($meses * 30)+ ($dies +1));
        }
         return ($total_dias);
    }

    protected function CrearPrimaSemestral($sw, $prima_semestral, $ano)
    {
         $mesInicio = 0;
         $anioTerminacion = 0;
         $mesTerminacion = 0;
         $anioInicio = 0;
         $diaTerminacion = 0;
         $diaInicio = 0;
        if($sw == 1){    
            $fecha = date($prima_semestral->fecha_ultima_prima);
            $fecha_inicio_dias = strtotime('1 day', strtotime($fecha));
            $fecha_inicio_dias = date('Y-m-d', $fecha_inicio_dias);
            //codigo de fechas
            $fecha_inicio = $fecha_inicio_dias;
            $fecha_termino = $prima_semestral->fecha_hasta;
            $diaTerminacion = substr($fecha_termino, 8, 8);
            $mesTerminacion = substr($fecha_termino, 5, 2);
            $anioTerminacion = substr($fecha_termino, 0, 4);
            $diaInicio = substr($fecha_inicio, 8, 8);
            $mesInicio = substr($fecha_inicio, 5, 2);
            $anioInicio = substr($fecha_inicio, 0, 4);
        }else{ 
            if($sw == 2){
              $fecha_inicio = $prima_semestral->fecha_inicio_contrato;
              $fecha_termino = $prima_semestral->fecha_hasta;
              $diaTerminacion = substr($fecha_termino, 8, 8);
              $mesTerminacion = substr($fecha_termino, 5, 2);
              $anioTerminacion = substr($fecha_termino, 0, 4);
              $diaInicio = substr($fecha_inicio, 8, 8);
              $mesInicio = substr($fecha_inicio, 5, 2);
              $anioInicio = substr($fecha_inicio, 0, 4);
            }else{
                //otro codigo
            } 
        }
        $febrero = 0;
        $mes = $mesInicio-1;
        if($mes == 2){
            if($ano == 1){
              $febrero = 29;
            }else{
              $febrero = 28;
            }
        }else if($mes <= 7){
            if($mes==0){
             $febrero = 31;
            }else if($mes%2==0){
                 $febrero = 30;
                }else{
                   $febrero = 31;
                }
        }else if($mes > 7){
              if($mes%2==0){
                  $febrero = 31;
              }else{
                  $febrero = 30;
              }
        }
        if(($anioInicio > $anioTerminacion) || ($anioInicio == $anioTerminacion && $mesInicio > $mesTerminacion) || 
            ($anioInicio == $anioTerminacion && $mesInicio == $mesTerminacion && $diaInicio > $diaTerminacion)){
                //mensaje
        }else{
            if($mesInicio <= $mesTerminacion){
                $anios = $anioTerminacion - $anioInicio;
                if($diaInicio <= $diaTerminacion){
                    $meses = $mesTerminacion - $mesInicio;
                    $dies = $diaTerminacion - $diaInicio;
                }else{
                    if($mesTerminacion == $mesInicio){
                       $anios = $anios - 1;
                    }
                    $meses = ($mesTerminacion - $mesInicio - 1 + 12) % 12;
                    $dies = $febrero-($diaInicio - $diaTerminacion);
                }
            }else{
                $anios = $anioTerminacion - $anioInicio - 1;
                if($diaInicio > $diaTerminacion){
                    $meses = $mesTerminacion - $mesInicio -1 +12;
                    $dies = $febrero - ($diaInicio-$diaTerminacion);
                }else{
                    $meses = $mesTerminacion - $mesInicio + 12;
                    $dies = $diaTerminacion - $diaInicio;
                }
            }
           $total_dias = (($anios * 360) + ($meses * 30)+ ($dies +1));
        }
         return ($total_dias);
       
    }

    //controlador de las incapacidades
    protected function ModuloIncapacidad($fecha_desde, $fecha_hasta, $valor_incapacidad, $id) {
        $contador = 0;
        $pro_nonima = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->andWhere(['=', 'id_empleado', $valor_incapacidad->id_empleado])->one();
        $tipo_incapacidad = ConfiguracionIncapacidad::find()->where(['=', 'codigo_incapacidad', $valor_incapacidad->codigo_incapacidad])->one();
        $prognomdetalle = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $pro_nonima->id_programacion])
                ->andWhere(['=', 'codigo_salario', $tipo_incapacidad->codigo_salario])
                ->andWhere(['=', 'id_incapacidad', $valor_incapacidad->id_incapacidad])
                ->all();
        if (!$prognomdetalle) {
            $detalleIncapacidad = new ProgramacionNominaDetalle();
            $detalleIncapacidad->id_programacion = $pro_nonima->id_programacion;
            $detalleIncapacidad->codigo_salario = $tipo_incapacidad->codigo_salario;
            $detalleIncapacidad->salario_basico = $valor_incapacidad->salario;
            $detalleIncapacidad->vlr_dia = $valor_incapacidad->vlr_hora * $pro_nonima->factor_dia;
            $detalleIncapacidad->vlr_hora = $valor_incapacidad->vlr_hora;
            $detalleIncapacidad->id_incapacidad = $valor_incapacidad->id_incapacidad;
            $detalleIncapacidad->fecha_desde = $valor_incapacidad->fecha_inicio;
            $detalleIncapacidad->fecha_hasta = $valor_incapacidad->fecha_final;
            $detalleIncapacidad->vlr_incapacidad = round($valor_incapacidad->vlr_liquidado);
            $detalleIncapacidad->nro_horas_incapacidad = $valor_incapacidad->dias_incapacidad * $pro_nonima->factor_dia;
            $detalleIncapacidad->horas_periodo = $valor_incapacidad->dias_incapacidad * $pro_nonima->factor_dia;
            $detalleIncapacidad->horas_periodo_reales = $valor_incapacidad->dias_incapacidad * $pro_nonima->factor_dia;
            $detalleIncapacidad->dias = $valor_incapacidad->dias_incapacidad;
            $detalleIncapacidad->dias_reales = $valor_incapacidad->dias_incapacidad;
            $detalleIncapacidad->dias_incapacidad_descontar = $valor_incapacidad->dias_incapacidad;
            $detalleIncapacidad->id_periodo_pago_nomina = $id;
            $detalleIncapacidad->dias_descontar_transporte = $valor_incapacidad->dias_incapacidad;
            $detalleIncapacidad->porcentaje = $valor_incapacidad->porcentaje_pago;
            if ($valor_incapacidad->pagar_empleado == 1) {
                $detalleIncapacidad->vlr_devengado = $detalleIncapacidad->vlr_incapacidad;
                $detalleIncapacidad->vlr_ajuste_incapacidad = $valor_incapacidad->ibc_total_incapacidad -  $detalleIncapacidad->vlr_devengado ;
            }else{
                $detalleIncapacidad->vlr_ajuste_incapacidad = $valor_incapacidad->ibc_total_incapacidad;
            }
            $detalleIncapacidad->insert(false);
            //codigo que actualiza el IBP
            $Concepto = ConceptoSalarios::find()->where(['=', 'codigo_salario', $tipo_incapacidad->codigo_salario])->andWhere(['=', 'ingreso_base_prestacional', 1])->one();
            if ($Concepto) {
                $actualizar_programacion = ProgramacionNomina::find()->where(['=', 'id_programacion', $pro_nonima->id_programacion])->one();
                if ($valor_incapacidad->pagar_empleado == 1) {
                    $contador = $actualizar_programacion->ibc_prestacional;
                    $actualizar_programacion->ibc_prestacional = $contador + $detalleIncapacidad->vlr_devengado;
                    $actualizar_programacion->save(false);
                } else {
                    $contador = $actualizar_programacion->ibc_prestacional;
                    $actualizar_programacion->ibc_prestacional = $contador + $detalleIncapacidad->vlr_incapacidad;
                    $actualizar_programacion->save(false);
                }
            }
        }
    }

    //codigo que valide las licencias
    protected function ModuloLicencias($fecha_desde, $fecha_hasta, $valor_licencia, $id) {
        $contador = 0;
        $pro_nonima = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->andWhere(['=', 'id_empleado', $valor_licencia->id_empleado])->one();
        $tipo_licencia = ConfiguracionLicencia::find()->where(['=', 'codigo_licencia', $valor_licencia->codigo_licencia])->one();
        $prognomdetalle = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $pro_nonima->id_programacion])
                ->andWhere(['=', 'codigo_salario', $tipo_licencia->codigo_salario])
                ->andWhere(['=', 'id_licencia', $valor_licencia->id_licencia_pk])
                ->all();
        if (!$prognomdetalle) {
            $detalleLicencia = new ProgramacionNominaDetalle();
            $detalleLicencia->id_programacion = $pro_nonima->id_programacion;
            $detalleLicencia->codigo_salario = $tipo_licencia->codigo_salario;
            $detalleLicencia->salario_basico = $valor_licencia->salario;
            $detalleLicencia->porcentaje = $tipo_licencia->porcentaje;
            $detalleLicencia->vlr_dia = $valor_licencia->salario / 30;
            if ($pro_nonima->factor_dia == 8) {
                $detalleLicencia->vlr_hora = $valor_licencia->salario / 240;
            } else {
                $detalleLicencia->vlr_hora = $valor_licencia->salario / 120;
            }

            $detalleLicencia->fecha_desde = $valor_licencia->fecha_desde;
            $detalleLicencia->fecha_hasta = $valor_licencia->fecha_hasta;
            $detalleLicencia->id_licencia = $valor_licencia->id_licencia_pk;
            //codigo para calcular los dias
            $fecha_final_licencia = strtotime($valor_licencia->fecha_hasta);
            $fecha_inicio_licencia = strtotime($valor_licencia->fecha_desde);
            $fecha_desde = strtotime($fecha_desde);
            $fecha_hasta = strtotime($fecha_hasta);

            if ($fecha_inicio_licencia < $fecha_desde) {
                if ($fecha_final_licencia >= $fecha_hasta) {
                    $total_dias = ($fecha_hasta) - $fecha_desde;
                    $total_dias = round($total_dias / 86400) + 1;
                } else {
                    $total_dias = ($fecha_final_licencia) - $fecha_desde;
                    $total_dias = round($total_dias / 86400) + 1;
                }
                $detalleLicencia->dias = $total_dias;
                $detalleLicencia->dias_reales = $total_dias;
                $detalleLicencia->horas_periodo = $total_dias * $pro_nonima->factor_dia;
                $detalleLicencia->horas_periodo_reales = $total_dias * $pro_nonima->factor_dia;
                $detalleLicencia->id_periodo_pago_nomina = $id;
                $detalleLicencia->nro_horas = $total_dias * $pro_nonima->factor_dia;
                $detalleLicencia->dias_licencia_descontar = $total_dias;
                if ($valor_licencia->pagar_empleado == 1) {
                    $detalleLicencia->vlr_devengado = round($detalleLicencia->vlr_hora * $detalleLicencia->horas_periodo);
                    $detalleLicencia->vlr_licencia = round($detalleLicencia->vlr_hora * $detalleLicencia->horas_periodo);
                } else {
                    $detalleLicencia->vlr_licencia = round($detalleLicencia->vlr_hora * $detalleLicencia->horas_periodo);
                    $detalleLicencia->vlr_devengado = 0;
                    $detalleLicencia->vlr_licencia_no_pagada = round($detalleLicencia->vlr_hora * $detalleLicencia->horas_periodo);
                }
                if ($valor_licencia->afecta_transporte == 1) {
                    $detalleLicencia->dias_descontar_transporte = $total_dias;
                }
            } else {
                if ($fecha_final_licencia <= $fecha_hasta) {
                    $total_dias = $fecha_final_licencia - $fecha_inicio_licencia;
                    $total_dias = round($total_dias / 86400) + 1;
                    $detalleLicencia->dias = $total_dias;
                    $detalleLicencia->dias_reales = $total_dias;
                    $detalleLicencia->horas_periodo = $total_dias * $pro_nonima->factor_dia;
                    $detalleLicencia->horas_periodo_reales = $total_dias * $pro_nonima->factor_dia;
                    $detalleLicencia->id_periodo_pago_nomina = $id;
                    $detalleLicencia->nro_horas = $total_dias * $pro_nonima->factor_dia;
                    $detalleLicencia->dias_licencia_descontar = $total_dias;
                    if ($valor_licencia->pagar_empleado == 1) {
                        $detalleLicencia->vlr_devengado = round($detalleLicencia->vlr_hora * $detalleLicencia->horas_periodo);
                        $detalleLicencia->vlr_licencia = round($detalleLicencia->vlr_hora * $detalleLicencia->horas_periodo);
                    } else {
                        $detalleLicencia->vlr_licencia = round($detalleLicencia->vlr_hora * $detalleLicencia->horas_periodo);
                        $detalleLicencia->vlr_devengado = 0;
                        $detalleLicencia->vlr_licencia_no_pagada = round($detalleLicencia->vlr_hora * $detalleLicencia->horas_periodo);
                    }
                    if ($valor_licencia->afecta_transporte == 1) {
                        $detalleLicencia->dias_descontar_transporte = $total_dias;
                    }
                } else {
                    $total_dias = $fecha_hasta - $fecha_inicio_licencia;
                    $total_dias = round($total_dias / 86400) + 1;
                    $detalleLicencia->dias = $total_dias;
                    $detalleLicencia->dias_reales = $total_dias;
                    $detalleLicencia->horas_periodo = $total_dias * $pro_nonima->factor_dia;
                    $detalleLicencia->horas_periodo_reales = $total_dias * $pro_nonima->factor_dia;
                    $detalleLicencia->id_periodo_pago_nomina = $id;
                    $detalleLicencia->nro_horas = $total_dias * $pro_nonima->factor_dia;
                    $detalleLicencia->dias_licencia_descontar = $total_dias;
                    $detalleLicencia->vlr_devengado = round($detalleLicencia->vlr_hora * $detalleLicencia->horas_periodo);
                    if ($valor_licencia->pagar_empleado == 1) {
                        $detalleLicencia->vlr_devengado = round($detalleLicencia->vlr_hora * $detalleLicencia->horas_periodo);
                        $detalleLicencia->vlr_licencia = round($detalleLicencia->vlr_hora * $detalleLicencia->horas_periodo);
                    } else {
                        $detalleLicencia->vlr_licencia = round($detalleLicencia->vlr_hora * $detalleLicencia->horas_periodo);
                        $detalleLicencia->vlr_devengado = 0;
                        $detalleLicencia->vlr_licencia_no_pagada = round($detalleLicencia->vlr_hora * $detalleLicencia->horas_periodo);
                    }
                    if ($valor_licencia->afecta_transporte == 1) {
                        $detalleLicencia->dias_descontar_transporte = $total_dias;
                    }
                }
            }
            $detalleLicencia->insert(false);
            //codigo que actualiza el IBP
        }
    }

    //codigo que valida los pagos permanentes
    protected function Moduloadicionpermanente($fecha_desde, $fecha_hasta, $adicionpermanente, $id, $grupo_pago) {
        $contador = 0;
        $contador_permanente = 0;
        $concepto_sal = ConceptoSalarios::find()->where(['=', 'codigo_salario', $adicionpermanente->codigo_salario])->one();
        $nonima = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->andWhere(['=', 'id_empleado', $adicionpermanente->id_empleado])->one();
        if($nonima){
            $programacion = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $nonima->id_programacion])->andWhere(['=', 'codigo_salario', $adicionpermanente->codigo_salario])->one();
            if (!$programacion) {
                $detalleapago = new ProgramacionNominaDetalle();
                $detalleapago->id_programacion = $nonima->id_programacion;
                $detalleapago->codigo_salario = $adicionpermanente->codigo_salario;
                $detalleapago->id_periodo_pago_nomina = $id;
                $detalleapago->fecha_desde = $fecha_desde;
                $detalleapago->fecha_hasta = $fecha_hasta;
                $periodo_pago = PeriodoPago::find()->where(['=', 'id_periodo_pago', $grupo_pago->id_periodo_pago])->one();
                if ($adicionpermanente->tipo_adicion == 1) {
                    if ($adicionpermanente->aplicar_dia_laborado == 1) {
                       $dias = $periodo_pago->dias;
                       $calculo = $adicionpermanente->vlr_adicion / $dias;

                        $total_pagado = round($calculo * $periodo_pago->dias);
                        if ($concepto_sal->prestacional == 1) {
                            $detalleapago->vlr_devengado = $total_pagado;
                        } else {
                           $detalleapago->vlr_devengado_no_prestacional = $total_pagado;
                           $detalleapago->vlr_devengado = $total_pagado;
                        }
                    } else {
                        if ($concepto_sal->prestacional == 1) {
                            $detalleapago->vlr_devengado = $adicionpermanente->vlr_adicion;

                        } else {
                            $detalleapago->vlr_devengado_no_prestacional = $adicionpermanente->vlr_adicion;
                            $detalleapago->vlr_devengado = $adicionpermanente->vlr_adicion;
                        }
                    }
                } else {
                    $detalleapago->vlr_deduccion = $adicionpermanente->vlr_adicion;
                    $detalleapago->deduccion = $adicionpermanente->vlr_adicion;
                }
                $detalleapago->save(false);
            }
        }    
    }

    //contralador de adicion al pago por fecha
    protected function Moduloadicionfecha($fecha_desde, $fecha_hasta, $adicionfecha, $id) {
        $contador = 0;
        $concepto_sal = ConceptoSalarios::find()->where(['=', 'codigo_salario', $adicionfecha->codigo_salario])->one();
        $nonima = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->andWhere(['=', 'id_empleado', $adicionfecha->id_empleado])->one();
        if($nonima){
            $detalle = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $nonima->id_programacion])->andWhere(['=', 'codigo_salario', $adicionfecha->codigo_salario])->one();
            if (!$detalle) {
                $detalleadicionpago = new ProgramacionNominaDetalle();
                $detalleadicionpago->id_programacion = $nonima->id_programacion;
                $detalleadicionpago->codigo_salario = $adicionfecha->codigo_salario;
                $detalleadicionpago->id_periodo_pago_nomina = $id;
                $detalleadicionpago->fecha_desde = $fecha_desde;
                $detalleadicionpago->fecha_hasta = $fecha_hasta;
                if ($adicionfecha->tipo_adicion == 1) {
                    if ($concepto_sal->prestacional == 1) {
                        $detalleadicionpago->vlr_devengado = $adicionfecha->vlr_adicion;
                    } else {
                        $detalleadicionpago->vlr_devengado_no_prestacional = $adicionfecha->vlr_adicion;
                        $detalleadicionpago->vlr_devengado = $adicionfecha->vlr_adicion;
                    }
                } else {
                    $detalleadicionpago->vlr_deduccion = $adicionfecha->vlr_adicion;
                    $detalleadicionpago->deduccion = $adicionfecha->vlr_adicion;
                }
                $detalleadicionpago->save(false);
            }
        }    
    }

    //contralor de los creditos
    protected function Modulocredito($fecha_desde, $fecha_hasta, $credito, $id) {
        $programacion_nonima = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->andWhere(['=', 'id_empleado', $credito->id_empleado])->one();
       if($programacion_nonima){
            $tipo_credito = ConfiguracionCredito::find()->where(['=', 'codigo_credito', $credito->codigo_credito])->one();
            $tipo_pago = TipoPagoCredito::find()->where(['=', 'id_tipo_pago', $credito->id_tipo_pago])->one();
            $prognomdetalle = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $programacion_nonima->id_programacion])
                    ->andWhere(['=', 'codigo_salario', $tipo_credito->codigo_salario])
                    ->andWhere(['=', 'id_credito', $credito->id_credito])
                    ->all();
            $valor = count($prognomdetalle);
            if (!$prognomdetalle) {
                $detallecredito = new ProgramacionNominaDetalle();
                if ($tipo_pago->id_tipo_pago == $credito->id_tipo_pago) {
                    $detallecredito->id_programacion = $programacion_nonima->id_programacion;
                    $detallecredito->codigo_salario = $tipo_credito->codigo_salario;
                    $detallecredito->id_periodo_pago_nomina = $id;
                    $detallecredito->vlr_deduccion = $credito->vlr_cuota;
                    $detallecredito->deduccion = $credito->vlr_cuota;
                    $detallecredito->fecha_desde = $fecha_desde;
                    $detallecredito->fecha_hasta = $fecha_hasta;
                    $detallecredito->id_credito = $credito->id_credito;
                    $detallecredito->save(false);
                }
            }
       }    
    }

    //controlador del tiempo extra
    protected function Novedadtiempoextra($tiempo_extra, $id, $fecha_hasta, $fecha_desde) {
        $contador = 0;
        $contador_recargo = 0;
        $programacion_nonima = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->one();
        $prognomdetalle = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $tiempo_extra->id_programacion])->andWhere(['=', 'codigo_salario', $tiempo_extra->codigo_salario])->all();
        if (!$prognomdetalle) {
            $detalle = new ProgramacionNominaDetalle();
            $detalle->id_programacion = $tiempo_extra->id_programacion;
            $detalle->codigo_salario = $tiempo_extra->codigo_salario;
            $detalle->vlr_hora = $tiempo_extra->vlr_hora;
            $detalle->id_periodo_pago_nomina = $id;
            $detalle->horas_periodo_reales = $tiempo_extra->nro_horas;
            $detalle->salario_basico = $tiempo_extra->salario_contrato;
            $detalle->vlr_devengado = round($tiempo_extra->total_novedad);
            $detalle->fecha_desde = $fecha_desde;
            $detalle->fecha_hasta = $fecha_hasta;
            $detalle->porcentaje = $tiempo_extra->porcentaje;
            $detalle->save(false);
            $Concepto = ConceptoSalarios::find()->where(['=', 'codigo_salario', $tiempo_extra->codigo_salario])->andWhere(['=', 'ingreso_base_prestacional', 1])->one();
            if ($Concepto) {
                $actualizar_programacion = ProgramacionNomina::find()->where(['=', 'id_programacion', $tiempo_extra->id_programacion])->one();
                $contador = $actualizar_programacion->total_tiempo_extra;
                $actualizar_programacion->total_tiempo_extra = $contador + $detalle->vlr_devengado;
                $actualizar_programacion->save(false);
            }
            $Concepto_recargo = ConceptoSalarios::find()->where(['=', 'codigo_salario', $tiempo_extra->codigo_salario])->andWhere(['=', 'recargo_nocturno', 1])->one();
            if ($Concepto_recargo) {
                $actualizar = ProgramacionNomina::find()->where(['=', 'id_programacion', $tiempo_extra->id_programacion])->one();
                $contador_recargo = $actualizar->total_recargo;
                $actualizar->total_recargo = $contador_recargo + $detalle->vlr_devengado;
                $actualizar->save(false);
            }
        }
    }

    protected function Auxiliotransporte($val, $_transporte, $total_dias, $auxilio, $fecha_hasta, $fecha_desde, $id_grupo_pago) {
        
        $prognomdetalle = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $val->id_programacion])->andWhere(['=', 'codigo_salario', $_transporte])->all();
        if (!$prognomdetalle) {
            $detalle = new ProgramacionNominaDetalle();
            $contrato = Contrato::find()->where(['=', 'id_contrato', $val->id_contrato])->one();
            if ($contrato->auxilio_transporte == 1) {
                $detalle->id_programacion = $val->id_programacion;
                $detalle->id_periodo_pago_nomina = $val->id_periodo_pago_nomina;
                $detalle->codigo_salario = $_transporte;
                $vlr_dia_auxilio = $auxilio / 30;
                $detalle->dias_transporte = $total_dias;
                $detalle->auxilio_transporte = round($total_dias * $vlr_dia_auxilio);
                $detalle->fecha_desde = $fecha_hasta;
                $detalle->fecha_hasta = $fecha_desde;
                $detalle->dias_reales = $total_dias;
                $detalle->vlr_dia = $vlr_dia_auxilio;
                $detalle->id_grupo_pago = $id_grupo_pago;
            }
        $detalle->save(false);
            
        }
    }

    protected function salario($val, $codigo_salario, $id_grupo_pago) {
        $total_dias_vacacion = $this->Sumardiasvacaciones($val);
        $prognomdetalle = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $val->id_programacion])->andWhere(['=', 'codigo_salario', $codigo_salario])->all();
        $sw = 0;
        if (!$prognomdetalle) { 
            $table = new ProgramacionNominaDetalle();
            $table->id_programacion = $val->id_programacion;
            $table->salario_basico = $val->salario_contrato;
            $table->id_periodo_pago_nomina = $val->id_periodo_pago_nomina;
            if ($val->factor_dia == 8) {
                $table->vlr_hora = $val->salario_contrato / 240;
                $table->vlr_dia = $val->salario_contrato / 30;
                $table->porcentaje = 100;
            } else {
                if ($val->salario_contrato <= $val->salario_medio_tiempo) {
                    $Vlr_dia_medio_tiempo = 0;
                    $sw = 1;
                    $table->vlr_hora = $val->salario_contrato / 120;
                    $table->vlr_dia = $val->salario_contrato / 30;
                    $Vlr_dia_medio_tiempo = $val->salario_medio_tiempo / 30;
                    $table->porcentaje = 50;
                } else {
                    $table->vlr_hora = $val->salario_contrato / 240;
                    $table->vlr_dia = $val->salario_contrato / 30;
                }
            }
            $table->codigo_salario = $codigo_salario;
            $table->id_grupo_pago = $id_grupo_pago;
            $contrato = Contrato::find()->where(['=', 'id_contrato', $val->id_contrato])->one();
            $fecha_inicio_contrato = strtotime(date($val->fecha_inicio_contrato, time()));
            $fecha_desde = strtotime($val->fecha_desde);
            $fecha_hasta = strtotime($val->fecha_hasta);
            if ($fecha_inicio_contrato < $fecha_desde) {
                if ($val->fecha_final_contrato != '') {
                    $total_dias = 0;
                    $total_dias = round((strtotime($val->fecha_final_contrato) - strtotime($val->fecha_desde)) / 86400) + 1 - $total_dias_vacacion;
                    $table->dias = $total_dias;
                    $table->dias_reales = $total_dias;
                    $table->dias_salario = $total_dias;
                    $table->horas_periodo = $total_dias * $val->factor_dia;
                    $table->horas_periodo_reales = $total_dias * $val->factor_dia;
                    $table->vlr_devengado = round($table->vlr_hora * $table->horas_periodo);
                    $table->fecha_desde = $val->fecha_desde;
                    $table->fecha_hasta = $val->fecha_final_contrato;
                    if ($sw == 1) {
                        $table->vlr_ibc_medio_tiempo = round($Vlr_dia_medio_tiempo * $total_dias);
                    }
                } else {
                        $total_dias = round((strtotime($val->fecha_hasta) - strtotime($val->fecha_desde)) / 86400) + 1 - $total_dias_vacacion;
                        //codigo de febrero
                        $mesFebrero = 0;
                        $diaFebrero = 0;
                        $mesFebrero = substr($val->fecha_hasta, 5, 2);
                        $diaFebrero = substr($val->fecha_hasta, 8, 8);
                        if($mesFebrero == 02){
                            if($diaFebrero == 28){
                                $total_dias = $total_dias + 2;
                            }else{
                                 if($diaFebrero == 29){
                                    $total_dias = $total_dias + 1;
                                 }
                            }
                        }
                        $table->dias = $total_dias;
                        $table->dias_reales = $total_dias;
                        $table->dias_salario = $total_dias;
                        $table->horas_periodo = $total_dias * $val->factor_dia;
                        $table->horas_periodo_reales = $total_dias * $val->factor_dia;
                        $table->vlr_devengado = round($table->vlr_hora * $table->horas_periodo);
                        $table->fecha_desde = $val->fecha_desde;
                        $table->fecha_hasta = $val->fecha_hasta;
                        if ($sw == 1) {
                            $table->vlr_ibc_medio_tiempo = round($Vlr_dia_medio_tiempo * $total_dias);
                        }
                }
            } else {
                if ($val->fecha_final_contrato != '') {
                    $total_dias = strtotime($val->fecha_final_contrato) - strtotime($val->fecha_inicio_contrato);
                    $total_dias = round($total_dias / 86400) + 1 - $total_dias_vacacion;
                    $table->dias = $total_dias;
                    $table->dias_reales = $total_dias;
                    $table->dias_salario = $total_dias;
                    $table->horas_periodo = $total_dias * $val->factor_dia;
                    $table->horas_periodo_reales = $total_dias * $val->factor_dia;
                    $table->vlr_devengado = round($table->vlr_hora * $table->horas_periodo);
                    $table->fecha_desde = $val->fecha_inicio_contrato;
                    $table->fecha_hasta = $val->fecha_final_contrato;
                    if ($sw == 1) {
                        $table->vlr_ibc_medio_tiempo = round($Vlr_dia_medio_tiempo * $total_dias);
                    }
                } else {
                  $total_dias = strtotime($val->fecha_hasta) - strtotime($val->fecha_inicio_contrato);
                   $total_dias = round($total_dias / 86400) + 1  - $total_dias_vacacion;
                    //codigo para febrero
                    $mesFebrero = 0;
                    $diaFebrero = 0;
                    $mesFebrero = substr($val->fecha_hasta, 5, 2);
                    $diaFebrero = substr($val->fecha_hasta, 8, 8);
                    if($mesFebrero == 02){
                        if($diaFebrero == 28){
                            $total_dias = $total_dias + 2;
                        }else{
                             if($diaFebrero == 29){
                                $total_dias = $total_dias + 1;
                             }
                        }
                    }
                    $table->dias = $total_dias;
                    $table->dias_reales = $total_dias;
                    $table->dias_salario = $total_dias;
                    $table->horas_periodo = $total_dias * $val->factor_dia;
                    $table->horas_periodo_reales = $total_dias * $val->factor_dia;
                    $table->vlr_devengado = round($table->vlr_hora * $table->horas_periodo);
                    $table->fecha_desde = $val->fecha_inicio_contrato;
                    $table->fecha_hasta = $val->fecha_hasta;
                    $sw = 0;
                    if($sw == 1) {
                        $table->vlr_ibc_medio_tiempo = round($Vlr_dia_medio_tiempo * $total_dias);
                    }
                }
            }
            $table->insert(false);
            $val->dia_real_pagado = $table->dias_reales;
            $val->save(false);
            return ($total_dias);
        }
    }

    //inicio del nuevo de proceso de validar los registros
    //CODIGO QUE ACTUALIZA EL.

    public function actionValidarregistros($id, $id_grupo_pago, $fecha_desde, $fecha_hasta, $tipo_nomina) {
        if($tipo_nomina == 1){
            $nomina = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
            foreach ($nomina as $validar):
               $this->ModuloActualizarDiasIncapacidades($validar);
            endforeach;
            //codigo para actualizar dias de licencia
            $nomina = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
            foreach ($nomina as $licencia):
                $this->ModuloActualizarDiasLicencia($licencia);
            endforeach;

            //codigo que actualiza los valores a pagar del adicion de pago permanente cuando
           $adicion_permanente = PagoAdicionalPermanente::find()->where(['=', 'permanente', 1])
                            ->andWhere(['=', 'estado_registro', 1])
                            ->andWhere(['=', 'estado_periodo', 1])
                            ->andWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                            ->andWhere(['=','aplicar_prima', 0])
                            ->orderBy('id_empleado ASC')->all();
            $contAdicionP = count($adicion_permanente);
            if ($contAdicionP > 0) {
                foreach ($adicion_permanente as $adicionpermanente) {
                    $this->ModuloActualizaSaldosPago($adicionpermanente, $id, $id_grupo_pago);
                }
            }
            // CODIGO QUE VALIDA ACTUALIZA DEDUCCION DE PENSION Y EPS
            $detalle_nomina_prestaciones = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->all();
           foreach ($detalle_nomina_prestaciones as $acumular_prestacion):
                $this->ModuloActualizarIbpEpsPension($acumular_prestacion, $fecha_desde, $fecha_hasta);
            endforeach;
            //codigo que actualiza saldos
            $detalle_nomina = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->all();
            foreach ($detalle_nomina as $actualizar_campos):
                $this->ModuloActualizarCampos($actualizar_campos);
            endforeach;
            //codigo que actualiza los dias a pagar reales
            $detalle_nomina = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
            foreach ($detalle_nomina as $actualizar_dias):
               $this->ModuloActualizarDias($actualizar_dias);
            endforeach;
            // codigo que actualiza el estado_liquidado de la programacion de la nomina
            $detalle_nomina = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
            foreach ($detalle_nomina as $validar):
                $validar->estado_liquidado = 1;
                $validar->total_devengado = $validar->ibc_prestacional + $validar->ibc_no_prestacional + $validar->total_auxilio_transporte - $validar->ajuste_incapacidad;;
                $validar->total_pagar = $validar->total_devengado - $validar->total_deduccion;
                $validar->save(false);
            endforeach;
        }else{
            if($tipo_nomina == 2){ //CONTROLADOR PARA EL PROCESO DE PRIMAS
                //PROCESO QUE BUSCA SI HAY CREDITOS PARA SUBIR AL PROCESO DE PRIMAS
                $creditosempleado = Credito::find()->where(['<=', 'fecha_inicio', $fecha_hasta])
                            ->andWhere(['=', 'estado_credito', 1])
                            ->andWhere(['=', 'estado_periodo', 1])
                            ->andWhere(['>', 'saldo_credito', 0])
                            ->andWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                            ->andWhere(['=','id_tipo_pago', $tipo_nomina])
                            ->orderBy('id_empleado DESC')->all();
                $contCredito = count($creditosempleado);
                if ($contCredito > 0) {
                    foreach ($creditosempleado as $credito_prima) {
                        $this->ModulocreditoPrima($credito_prima, $id, $fecha_desde, $fecha_hasta);
                    }
                }
                
                //CONTROLADOR QUE BUSCA SI HAY ADICION POR FECHA PARA ENVIAR AL MODULO DE PRIMA
                $adicion_fecha_prima = PagoAdicionalPermanente::find()->where(['=', 'fecha_corte', $fecha_hasta])
                    ->andWhere(['=', 'estado_registro', 1])
                    ->andWhere(['=', 'estado_periodo', 1])
                    ->andWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                    ->andWhere(['=','aplicar_prima', 1])
                    ->orderBy ('id_empleado DESC')->all();
                 $contAdicionP = count($adicion_fecha_prima);
                 if ($contAdicionP > 0) {
                     foreach ($adicion_fecha_prima as $vlr_fecha_adicionprima) {
                        
                        $this->ModuloInsertarPrima($vlr_fecha_adicionprima, $id, $fecha_desde, $fecha_hasta);
                     }
                 }
                 // ESTE CONTRALADOR CIERRA EL PROCESO DE VALIDACION DE REGISTRO
                $nomina = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
                foreach ($nomina as $validar):
                    $validar->estado_liquidado = 1;
                    $validar->save(false);
                endforeach;
                
            }else{
                //CODIGO QUE VALIDA LOS REGISTROS DE LAS CESANTIAS
                
                $nomina = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
                foreach ($nomina as $validar):
                    $validar->estado_liquidado = 1;
                    $validar->save(false);
                endforeach;
            }
        }    

    $this->redirect(["programacion-nomina/view", 'id' => $id,
          'id_grupo_pago' => $id_grupo_pago,
          'fecha_desde' => $fecha_desde,
          'fecha_hasta' => $fecha_hasta,
          ]);
    }
    protected function ModuloInsertarPrima($vlr_fecha_adicionprima, $id, $fecha_desde, $fecha_hasta) {
       
        $concepto_sal = ConceptoSalarios::find()->where(['=', 'codigo_salario', $vlr_fecha_adicionprima->codigo_salario])->one();
        $nonima = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->andWhere(['=', 'id_empleado', $vlr_fecha_adicionprima->id_empleado])->one();
        $detalle = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $nonima->id_programacion])->andWhere(['=', 'codigo_salario', $vlr_fecha_adicionprima->codigo_salario])->all();
        if (!$detalle) {
            $vlr_devengado = 0;
            $vlr_deduccion = 0;
            $vlr_deduccion = $nonima->total_deduccion;
            $vlr_devengado = $nonima->total_devengado;
            $detalleadicionpago = new ProgramacionNominaDetalle();
            $detalleadicionpago->id_programacion = $nonima->id_programacion;
            $detalleadicionpago->codigo_salario = $vlr_fecha_adicionprima->codigo_salario;
            $detalleadicionpago->fecha_desde = $fecha_desde;
            $detalleadicionpago->fecha_hasta = $fecha_hasta;
            $detalleadicionpago->vlr_devengado = $vlr_fecha_adicionprima->vlr_adicion;
            $detalleadicionpago->save(false);
            $nonima->total_devengado = $vlr_devengado + $vlr_fecha_adicionprima->vlr_adicion;
            $nonima->total_pagar =   $nonima->total_devengado - $vlr_deduccion;
            $nonima->save(false);
            $vlr_fecha_adicionprima->aplicar_prima = 6;
            $vlr_fecha_adicionprima->save(false);
            
        }
    }    
    
     //ESTE CONTRATO PERMITE INSERTAR LOS CREDITOS QUE SE VAN APLICAR EN LA PRIMA
    protected function ModulocreditoPrima($credito_prima, $id,  $fecha_desde, $fecha_hasta)
    {
        $tipo_credito = ConfiguracionCredito::find()->where(['=', 'codigo_credito', $credito_prima->codigo_credito])->one();
        $tipo_pago = TipoPagoCredito::find()->where(['=', 'id_tipo_pago', $credito_prima->id_tipo_pago])->one();
        $programacion_nonima = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->andWhere(['=', 'id_empleado', $credito_prima->id_empleado])->one(); 
        $prognomdetalle = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $programacion_nonima->id_programacion])
                ->andWhere(['=', 'codigo_salario', $tipo_credito->codigo_salario])
                ->andWhere(['=', 'id_credito', $credito_prima->id_credito])
                ->all();
        $valor = count($prognomdetalle);
        if (!$prognomdetalle) {
            $vlr_prima = 0;
            $vlr_prima = $programacion_nonima->total_devengado;
            $detallecredito = new ProgramacionNominaDetalle();
            $detallecredito->id_programacion = $programacion_nonima->id_programacion;
            $detallecredito->codigo_salario = $tipo_credito->codigo_salario;
            $detallecredito->vlr_deduccion = $credito_prima->vlr_cuota;
            $detallecredito->deduccion = $credito_prima->vlr_cuota;
            $detallecredito->fecha_desde = $fecha_desde;
            $detallecredito->fecha_hasta = $fecha_hasta;
            $detallecredito->id_credito = $credito_prima->id_credito;
            $detallecredito->save(false);
            $programacion_nonima->total_deduccion = $credito_prima->vlr_cuota;
            $programacion_nonima->total_pagar = $vlr_prima - $programacion_nonima->total_deduccion;
            $programacion_nonima->save(false);
        }
    }
    
    //codigo que actualiza los dias reales a pagar    
    protected function ModuloActualizarDias($actualizar_dias) {
        $concepto_salario = ConceptoSalarios::find()->where(['=', 'inicio_nomina', 1])->one();
        $detalle_programacion = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $actualizar_dias->id_programacion])->all();
        foreach ($detalle_programacion as $detalle):
            if ($detalle->codigo_salario == $concepto_salario->codigo_salario) {
               $actualizar_dias->dia_real_pagado = $detalle->dias_reales;
               $actualizar_dias->horas_pago = $actualizar_dias->dia_real_pagado * 8;
                $actualizar_dias->save(false);
            }
        endforeach;
    }

    //codigo que actualiza los saldos de deduccion, ingreso no prestacional, incapacidades
    protected function ModuloActualizarCampos($actualizar_campos) {
        $total_no_prestacional = 0;
        $total_deduccion = 0;
        $total_licencia = 0;
        $total_incapacidad = 0;
        $total_ajuste_incapacidad = 0;
        $total_auxilio = 0;
        $detalle_no = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $actualizar_campos->id_programacion])->orderBy('id_programacion DESC')->all();
        foreach ($detalle_no as $campos):
            $total_no_prestacional = $total_no_prestacional + $campos->vlr_devengado_no_prestacional;
            $total_deduccion = $total_deduccion + $campos->vlr_deduccion;
            $total_licencia = $total_licencia + $campos->vlr_licencia;
            $total_incapacidad = $total_incapacidad + $campos->vlr_incapacidad;
            $total_auxilio = $total_auxilio + $campos->auxilio_transporte;
            $total_ajuste_incapacidad += $campos->vlr_ajuste_incapacidad;
        endforeach;
        $actualizar_campos->ibc_no_prestacional = $total_no_prestacional;
        $actualizar_campos->total_deduccion = $total_deduccion;
        $actualizar_campos->total_licencia = $total_licencia;
        $actualizar_campos->total_incapacidad = $total_incapacidad;
        $actualizar_campos->total_auxilio_transporte= $total_auxilio;
        $actualizar_campos->ajuste_incapacidad =  $total_ajuste_incapacidad;
        $actualizar_campos->save(false);
    }

    // codigo para actualizar saldos de prestaciones
    protected function ModuloActualizarIbpEpsPension($acumular_prestacion, $fecha_desde, $fecha_hasta) {
        $contar = 0;
        $contar_medio = 0;
        $vlr_no_prestacional = 0;
        $concepto_salario = ConceptoSalarios::find()->where(['=', 'concepto_pension', 1])->one();
        $concepto_fondo = ConceptoSalarios::find()->where(['=', 'fsp', 1])->one();
        $contratos = Contrato::find()->where(['=', 'id_contrato', $acumular_prestacion->id_contrato])->one();
        $detalle_no = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $acumular_prestacion->id_programacion])->orderBy('id_programacion DESC')->all();
        foreach ($detalle_no as $saldo_devengado):
            $contar += ($saldo_devengado->vlr_devengado + $saldo_devengado->vlr_ajuste_incapacidad)-  $saldo_devengado->vlr_devengado_no_prestacional;
            $vlr_no_prestacional += $saldo_devengado->vlr_licencia_no_pagada; 
            $contar_medio +=  $saldo_devengado->vlr_ibc_medio_tiempo;
        endforeach;
        $acumular_prestacion->ibc_prestacional = $contar;
        $acumular_prestacion->total_ibc_no_prestacional = $vlr_no_prestacional;
        $acumular_prestacion->vlr_ibp_medio_tiempo = $contar_medio;
        $acumular_prestacion->save(false);
        //codigo que inserta el codigo de pension
        $con_pension = ConfiguracionPension::find()->all();
        foreach ($con_pension as $pension):
            $detalle = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $acumular_prestacion->id_programacion])->andWhere(['=', 'codigo_salario', $pension->codigo_salario])->all();
            if (!$detalle) {
                if ($contratos->id_pension == $pension->id_pension) {
                    if ($pension->porcentaje_empleado > 0) {
                        $detalle_pension = new ProgramacionNominaDetalle();
                        $detalle_pension->id_programacion = $acumular_prestacion->id_programacion;
                        $detalle_pension->codigo_salario = $pension->codigo_salario;
                        $detalle_pension->porcentaje = $pension->porcentaje_empleado;
                        $detalle_pension->fecha_desde = $fecha_desde;
                        $detalle_pension->fecha_hasta = $fecha_hasta;
                        $detalle_pension->id_periodo_pago_nomina = $acumular_prestacion->id_periodo_pago_nomina;
                        if ($acumular_prestacion->salario_contrato <= $acumular_prestacion->salario_medio_tiempo) {
                             $detalle_pension->vlr_deduccion = round(($contar_medio * $pension->porcentaje_empleado) / 100);
                             $detalle_pension->descuento_pension = round(($contar_medio * $pension->porcentaje_empleado) / 100);
                        } else {
                            $detalle_pension->vlr_deduccion = round(($contar * $pension->porcentaje_empleado) / 100);
                            $detalle_pension->descuento_pension = round(($contar * $pension->porcentaje_empleado) / 100);
                        }
                        $detalle_pension->save(false);
                    }
                }
            }
            $valor_prestacional = $contar * 2;
            $detalle_fondo = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $acumular_prestacion->id_programacion])->andWhere(['=', 'codigo_salario', $concepto_fondo->codigo_salario])->all();
            if(!$detalle_fondo){
                $configuracion_fondo = \app\models\FondoSolidaridadPensional::find()->all();
                foreach ($configuracion_fondo as $valor):
                    if($valor_prestacional >= $valor->rango1 && $valor_prestacional < $valor->rango2){
                        $fondo_solidaridad = new ProgramacionNominaDetalle;
                        $fondo_solidaridad->id_programacion = $acumular_prestacion->id_programacion;
                        $fondo_solidaridad->codigo_salario = $concepto_fondo->codigo_salario;
                        $fondo_solidaridad->id_periodo_pago_nomina = $acumular_prestacion->id_periodo_pago_nomina;
                        $fondo_solidaridad->vlr_deduccion = round(($contar * $valor->porcentaje) / 100);
                        $fondo_solidaridad->descuento_pension = round(($contar * $valor->porcentaje) / 100);
                        $fondo_solidaridad->porcentaje = $valor->porcentaje;
                        $fondo_solidaridad->fecha_desde = $fecha_desde;
                        $fondo_solidaridad->fecha_hasta = $fecha_hasta;
                        $fondo_solidaridad->save(false);
                    }
                endforeach;
            }
        endforeach;
        //codigo que inserta la eps

        $con_eps = ConfiguracionEps::find()->all();
        foreach ($con_eps as $eps):
            $detalle = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $acumular_prestacion->id_programacion])->andWhere(['=', 'codigo_salario', $eps->codigo_salario])->all();
            if (!$detalle) {
                if ($contratos->id_eps == $eps->id_eps) {
                    if ($eps->porcentaje_empleado_eps > 0) {
                        $detalle_eps = new ProgramacionNominaDetalle();
                        $detalle_eps->id_programacion = $acumular_prestacion->id_programacion;
                        $detalle_eps->codigo_salario = $eps->codigo_salario;
                        $detalle_eps->porcentaje = $eps->porcentaje_empleado_eps;
                        $detalle_eps->fecha_desde = $fecha_desde;
                        $detalle_eps->fecha_hasta = $fecha_hasta;
                        $detalle_eps->id_periodo_pago_nomina = $acumular_prestacion->id_periodo_pago_nomina;
                        if ($acumular_prestacion->salario_contrato <= $acumular_prestacion->salario_medio_tiempo) {
                            $detalle_eps->vlr_deduccion = round(($contar_medio * $eps->porcentaje_empleado_eps) / 100);
                            $detalle_eps->descuento_salud = round(($contar_medio * $eps->porcentaje_empleado_eps) / 100);
                        } else {
                            $detalle_eps->vlr_deduccion = round(($contar * $eps->porcentaje_empleado_eps) / 100);
                            $detalle_eps->descuento_salud = round(($contar * $eps->porcentaje_empleado_eps) / 100);
                        }
                        $detalle_eps->save(false);
                    }
                }
            }
        endforeach;
    }

    protected function ModuloActualizarDiasLicencia($licencia) {
        $con = 0;
        $actualizar_licencia = ConceptoSalarios::find()->where(['=', 'concepto_licencia', 1])->all();
        foreach ($actualizar_licencia as $actualizar):
            $suma = 0;
            $deta1 = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $licencia->id_programacion])->andWhere(['=', 'codigo_salario', $actualizar->codigo_salario])->orderBy('codigo_salario ASC')->one();
            if ($deta1) {
                if ($deta1->codigo_salario <> $actualizar->codigo_salario) {
                    $dia_licencia = $deta1->dias_licencia_descontar;
                    $suma = $dia_licencia;
                    $con = 1;
                    $salario = ConceptoSalarios::find()->where(['=', 'inicio_nomina', 1])->one();
                    $actuSalario = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $licencia->id_programacion])->andWhere(['=', 'codigo_salario', $salario->codigo_salario])->one();
                    if ($actuSalario) {
                        if ($con == 1) {
                            $actuSalario->dias_reales = $actuSalario->dias_reales - $suma;
                            $actuSalario->horas_periodo_reales = $actuSalario->dias_reales * $licencia->factor_dia;
                            $actuSalario->vlr_devengado = round($actuSalario->vlr_dia * $actuSalario->dias_reales);
                            $actuSalario->save(false);
                            $con = 2;
                        }
                    }
                    $transporte = ConceptoSalarios::find()->where(['=', 'auxilio_transporte', 1])->one();
                    $actu_trans = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $licencia->id_programacion])->andWhere(['=', 'codigo_salario', $transporte->codigo_salario])->one();
                    if ($actu_trans) {
                        if ($con == 1) {
                            $dias_transporte = $actu_trans->dias_transporte;
                            $actu_trans->dias_transporte = $dias_transporte - $suma;
                            $actu_trans->auxilio_transporte = round($actu_trans->vlr_dia * $actu_trans->dias_transporte);
                            $actu_trans->save(false);
                            $con = 2;
                        }
                    }
                }
            }//ternmina aca
            $deta = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $licencia->id_programacion])->andWhere(['=', 'codigo_salario', $actualizar->codigo_salario])->all();
            foreach ($deta as $varias_incapacidad):
                $suma = 0;
                $dia_licencia = $varias_incapacidad->dias_licencia_descontar;
                $suma = $suma + $dia_licencia;
                $con = 1;
                $salario = ConceptoSalarios::find()->where(['=', 'inicio_nomina', 1])->one();
                $actu = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $licencia->id_programacion])->andWhere(['=', 'codigo_salario', $salario->codigo_salario])->one();
                if ($actu) {
                    if ($con == 1) {
                        $dias = $actu->dias_reales;
                        $actu->dias_reales = $dias - $suma;
                        $actu->horas_periodo_reales = $actu->dias_reales * $licencia->factor_dia;
                        $actu->vlr_devengado = round($actu->vlr_dia * $actu->dias_reales);
                        $actu->save(false);
                    }
                }
                $transporte = ConceptoSalarios::find()->where(['=', 'auxilio_transporte', 1])->one();
                $actu_trans = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $licencia->id_programacion])->andWhere(['=', 'codigo_salario', $transporte->codigo_salario])->one();
                if ($actu_trans) {
                    if ($con == 1) {
                        $dias_transporte = $actu_trans->dias_transporte;
                        $actu_trans->dias_transporte = $dias_transporte - $suma;
                        $actu_trans->auxilio_transporte = round($actu_trans->vlr_dia * $actu_trans->dias_transporte);
                        $actu_trans->save(false);
                    }
                }
            endforeach;
        endforeach;
    }
    
    //controlador que actualiza el valor real a pagar de pago adicional.
 protected function ModuloActualizaSaldosPago($adicionpermanente, $id, $id_grupo_pago)
    {
       $dias = 0;
       $nomina= [];
       $grupo_pago = PeriodoPagoNomina::find()->where(['=','id_grupo_pago', $id_grupo_pago])->andWhere(['=','estado_periodo', 0])->one();
       $concepto_salario = ConceptoSalarios::find()->where(['=', 'inicio_nomina', 1])->one(); 
       $concepto_sal = ConceptoSalarios::find()->where(['=', 'codigo_salario', $adicionpermanente->codigo_salario])->one();
       $nonima = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->andWhere(['=', 'id_empleado', $adicionpermanente->id_empleado])->one();
       if($nomina){
            $detalle_nomina = ProgramacionNominadetalle::find()->where(['=','id_programacion', $nonima->id_programacion])->andwhere(['=','codigo_salario', $concepto_salario->codigo_salario])->one();
            $detalle_nomina_salario = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $nonima->id_programacion])->andwhere(['=','codigo_salario', $adicionpermanente->codigo_salario])->one();
            $dias = $nonima->dia_real_pagado;
            if($concepto_sal->prestacional == 1 && $adicionpermanente->aplicar_dia_laborado == 1){
                $detalle_nomina_salario->vlr_devengado = round($adicionpermanente->vlr_adicion / $grupo_pago->dias_periodo) * $dias;
                $detalle_nomina_salario->save(false);
            }else{
                 if($concepto_sal->prestacional == 1 && $adicionpermanente->aplicar_dia_laborado == 0){
                    $detalle_nomina_salario->vlr_devengado = $adicionpermanente->vlr_adicion;   
                    $detalle_nomina_salario->save(false);
                 }
            }
            if($concepto_sal->prestacional == 0 && $adicionpermanente->aplicar_dia_laborado == 0){
               $detalle_nomina_salario->vlr_devengado_no_prestacional = $adicionpermanente->vlr_adicion; 
               $detalle_nomina_salario->save(false);
            }else{

                  if($concepto_sal->prestacional == 0 && $adicionpermanente->aplicar_dia_laborado == 1){
                    $detalle_nomina_salario->vlr_devengado_no_prestacional = round($adicionpermanente->vlr_adicion / $grupo_pago->dias_periodo) * $dias;   
                    $detalle_nomina_salario->vlr_devengado = round(($adicionpermanente->vlr_adicion / $grupo_pago->dias_periodo) * $dias);   
                    $detalle_nomina_salario->save(false);
                  }    
            }
       }    
               
    }

    //controlador de actualizacion de ibc y ibp
    protected function ModuloActualizarDiasIncapacidades($validar) {
        $con = 0;
        $actualizar_incapacidad = ConceptoSalarios::find()->where(['=', 'concepto_incapacidad', 1])->andWhere(['=','compone_salario', 1])->all();
        foreach ($actualizar_incapacidad as $actualizar):
            $suma = 0;
            $deta1 = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $validar->id_programacion])->andWhere(['=', 'codigo_salario', $actualizar->codigo_salario])->orderBy('codigo_salario ASC')->one();
            if ($deta1) {
                if ($deta1->codigo_salario <> $actualizar->codigo_salario) {
                    $dia_incapacidad = $deta1->dias_incapacidad_descontar;
                    $suma = $dia_incapacidad;
                    $con = 1;
                    $salario = ConceptoSalarios::find()->where(['=', 'inicio_nomina', 1])->one();
                    $actuSalario = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $validar->id_programacion])->andWhere(['=', 'codigo_salario', $salario->codigo_salario])->one();
                    if ($actuSalario) {
                        if ($con == 1) {
                            $actuSalario->dias_reales = $dias = $actuSalario->dias_reales;
                            $actuSalario->horas_periodo_reales = $actuSalario->dias_reales * $validar->factor_dia;
                            $actuSalario->vlr_devengado = round($actuSalario->vlr_dia * $actuSalario->dias_reales);
                            $actuSalario->save(false);
                            $con = 2;
                        }
                    }
                    $transporte = ConceptoSalarios::find()->where(['=', 'auxilio_transporte', 1])->one();
                    $actu_trans = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $validar->id_programacion])->andWhere(['=', 'codigo_salario', $transporte->codigo_salario])->one();
                    if ($actu_trans) {
                        if ($con == 1) {
                            $dias_transporte = $actu_trans->dias_transporte;
                            $actu_trans->dias_transporte = $dias_transporte - $suma ;
                            $actu_trans->auxilio_transporte = round($actu_trans->vlr_dia * $actu_trans->dias_transporte);
                            $actu_trans->save(false);
                            $con = 2;
                        }
                    }
                }
            }//ternmina aca
            $deta = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $validar->id_programacion])->andWhere(['=', 'codigo_salario', $actualizar->codigo_salario])->all();
            foreach ($deta as $varias_incapacidad):
                $suma = 0;
                $dia_incapacidad = $varias_incapacidad->dias_incapacidad_descontar;
                $suma = $suma + $dia_incapacidad;
                $con = 1;
                $salario = ConceptoSalarios::find()->where(['=', 'inicio_nomina', 1])->one();
                $actu = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $validar->id_programacion])->andWhere(['=', 'codigo_salario', $salario->codigo_salario])->one();
                if ($actu) {
                    if ($con == 1) {
                        $dias = $actu->dias_reales;
                        $actu->dias_reales = $dias - $suma ;
                        $actu->horas_periodo_reales = $actu->dias_reales * $validar->factor_dia;
                        $actu->vlr_devengado = round($actu->vlr_dia * $actu->dias_reales);
                        $actu->save(false);
                    }
                }
                $transporte = ConceptoSalarios::find()->where(['=', 'auxilio_transporte', 1])->one();
                $actu_trans = ProgramacionNominaDetalle::find()->where(['=', 'id_programacion', $validar->id_programacion])->andWhere(['=', 'codigo_salario', $transporte->codigo_salario])->one();
                if ($actu_trans) {
                    if ($con == 1) {
                        $dias_transporte = $actu_trans->dias_transporte;
                        $actu_trans->dias_transporte = $dias_transporte - $suma ;
                        $actu_trans->auxilio_transporte = round($actu_trans->vlr_dia * $actu_trans->dias_transporte);
                        $actu_trans->save(false);
                    }
                }
            endforeach;

        endforeach;
    }
    //codigo que suma los dias de vacaciones
    protected function Sumardiasvacaciones($val) {
        $total_dias_vacacion = 0;
        if ($val->fecha_inicio_vacacion == ''){
            return ($total_dias_vacacion);
        }else{
            $total_dia = 0;
             if ($val->fecha_final_vacacion >= $val->fecha_hasta ){
                 $total_dias_vacacion = strtotime($val->fecha_hasta) - strtotime($val->fecha_inicio_vacacion);
                 $total_dias_vacacion =  round($total_dias_vacacion / 86400) + 1;
                 $val->dias_vacacion = $total_dias_vacacion;
                 $val->horas_vacacion = $val->dias_vacacion * 8;
                 $total_dia = $val->salario_contrato / 30;
                 $val->ibc_vacacion = $total_dia * $total_dias_vacacion;
                 $val->fecha_final_vacacion = $val->fecha_hasta;
                 $val->save(false);
                 return ($total_dias_vacacion);
             }else{
                 $total_dias_vacacion = strtotime($val->fecha_final_vacacion) - strtotime($val->fecha_desde);
                 $total_dias_vacacion =  round($total_dias_vacacion / 86400) + 1;
                 $val->dias_vacacion = $total_dias_vacacion;
                 $val->horas_vacacion = $val->dias_vacacion * 8;
                 $total_dia = $val->salario_contrato / 30;
                 $val->ibc_vacacion = $total_dia * $total_dias_vacacion;
                 $val->fecha_final_vacacion = $val->fecha_final_vacacion;
                 $val->fecha_inicio_vacacion = $val->fecha_desde;
                 $val->save(false);
                 return ($total_dias_vacacion);
             }
            
        }
    }

    public function actionDeshacer($id, $id_grupo_pago, $fecha_desde, $fecha_hasta) {
        $detalle_nomina = ProgramacionNomina::find()->where(['=', 'id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
        foreach ($detalle_nomina as $validar):
            $validar->estado_generado = 0;
            $validar->save(false);
        endforeach;
        $this->redirect(["programacion-nomina/view", 'id' => $id,
            'id_grupo_pago' => $id_grupo_pago,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
        ]);
    }
    
   //CONTRALADOR PARA APLICAR PAGOS 
    public function actionAplicarpagos($id, $id_grupo_pago, $fecha_desde, $fecha_hasta, $tipo_nomina)
    {
        if($tipo_nomina == 1){ //CONDICION SI ES DE NOMINA
            // consulta las programaciones que tiene creditos
            $creditosempleado = Credito::find()->where(['<=', 'fecha_inicio', $fecha_hasta])->andWhere(['=', 'estado_credito', 1])->andWhere(['=', 'estado_periodo', 1])
                            ->andWhere(['>', 'saldo_credito', 0])->andWhere(['=', 'id_grupo_pago', $id_grupo_pago])->andWhere(['=','id_tipo_pago', $tipo_nomina])->orderBy('id_empleado DESC')->all();
            $contCredito = count($creditosempleado);
            if ($contCredito > 0) {
                foreach ($creditosempleado as $credito) {
                    $this->ModuloActualizarCreditos($credito, $id, $tipo_nomina);
                }
            }
            //codigo que actualiza fecha ultimo pago nomina
            $nomina = ProgramacionNomina::find()->where(['=','id_periodo_pago_nomina', $id])->all();
            foreach ($nomina as $nomina_empleado) {
                    $contrato = Contrato::find()->where(['=','id_contrato', $nomina_empleado->id_contrato])->one();
                    $contrato->ultimo_pago = $fecha_hasta;
                    $contrato->save(false);
                }
            $grupo_pago = GrupoPago::find()->where(['=','id_grupo_pago', $id_grupo_pago])->one();
            $grupo_pago->ultimo_pago_nomina = $fecha_hasta;
            $grupo_pago->save(false);
            
            //codigo que actualiza el estado de la incapacidad adicional
            $incapacidad = Incapacidad::find()->where(['=','fecha_aplicacion', $fecha_hasta])->andWhere(['=','estado_incapacidad_adicional', 1])->orderBy('id_empleado asc')->all();
            foreach ($incapacidad as $buscar){
                $buscar_incapacidad = ProgramacionNominaDetalle::find(['=','id_incapacidad', $buscar->id_incapacidad])->one();
                if($buscar_incapacidad){
                    $buscar->estado_incapacidad_adicional = 2;
                    $buscar->save(false);
                }
            }
            
            
            
            //codigo que genera el consecutivo a la nomina nropago de la colilla
            $nomina = ProgramacionNomina::find()->where(['=','id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
            foreach ($nomina as $generar_consecutivo) {
                    $consecutivo = Consecutivo::findOne(7);
                    $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
                    $consecutivo->save(false);
                    $generar_consecutivo->nro_pago = $consecutivo->consecutivo;
                    $generar_consecutivo->estado_cerrado = 1;
                   $generar_consecutivo->save(false);
            }
             //actualizar el estado del periodo a 1
                $periodo_pago = PeriodoPagoNomina::findone($id);
                $periodo_pago->estado_periodo = 1;
                $periodo_pago->save(false);
             //inserta concepto de vacacion si tiene vacaciones
              $concepto_salario = ConceptoSalarios::find()->where(['=','concepto_vacacion', 1])->one();  
              $nomina = ProgramacionNomina::find()->where(['=','id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all(); 
              foreach ($nomina as $vacacion):
                  $saldo = 0;
                  if($vacacion->fecha_inicio_vacacion <> ''){
                      $detalle = new ProgramacionNominaDetalle();
                      $detalle->codigo_salario = $concepto_salario->codigo_salario;
                      $detalle->id_programacion = $vacacion->id_programacion;
                      $detalle->horas_periodo = $vacacion->horas_vacacion;
                      $detalle->horas_periodo_reales = $vacacion->horas_vacacion; 
                      $detalle->dias = $vacacion->dias_vacacion;
                      $detalle->dias_reales = $vacacion->dias_vacacion;
                      $detalle->vlr_devengado = $vacacion->ibc_vacacion;
                      $detalle->vlr_vacacion = $detalle->vlr_devengado;
                      $detalle->id_periodo_pago_nomina = $id;
                      $detalle->fecha_desde = $vacacion->fecha_inicio_vacacion;
                      $detalle->fecha_hasta = $vacacion->fecha_final_vacacion;
                      $detalle->insert(false);
                      $saldo = $vacacion->ibc_prestacional;
                      $vacacion->ibc_prestacional =  $saldo + $detalle->vlr_devengado;
                      $vacacion->save(false);
                  }
              endforeach;
                
        }else{ 
            //codigo para prima
            if($tipo_nomina == 2){ 
                // consulta las programaciones que tiene creditos
                $creditosempleado = Credito::find()->where(['<=', 'fecha_inicio', $fecha_hasta])->andWhere(['=', 'estado_credito', 1])->andWhere(['=', 'estado_periodo', 1])
                                ->andWhere(['>', 'saldo_credito', 0])->andWhere(['=', 'id_grupo_pago', $id_grupo_pago])->andWhere(['=','id_tipo_pago', $tipo_nomina])->orderBy('id_empleado DESC')->all();
                $contCredito = count($creditosempleado);
                if ($contCredito > 0) {
                    foreach ($creditosempleado as $credito) {
                        $this->ModuloActualizarCreditos($credito, $id, $tipo_nomina);
                    }
                }
                //codigo que actualiza fecha ultimo pago de prima
                $nomina = ProgramacionNomina::find()->where(['=','id_periodo_pago_nomina', $id])->all();
                foreach ($nomina as $nomina_empleado) {
                        $contrato = Contrato::find()->where(['=','id_contrato', $nomina_empleado->id_contrato])->one();
                        $contrato->ultima_prima = $fecha_hasta;
                        $contrato->ibp_prima_inicial = 0;
                        $contrato->save(false);
                }
                $grupo_pago = GrupoPago::find()->where(['=','id_grupo_pago', $id_grupo_pago])->one();
                $grupo_pago->ultimo_pago_prima = $fecha_hasta;
                $grupo_pago->save(false);
                //CODIGO QUE GENERA EL CONSECUTIVO DE PRIMA EN LA TABLA PROGRAMACION_NOMINA
                $nomina = ProgramacionNomina::find()->where(['=','id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
                foreach ($nomina as $generar_consecutivo) {
                        $consecutivo = Consecutivo::findOne(8);
                        $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
                        $consecutivo->save(false);
                        $generar_consecutivo->nro_pago = $consecutivo->consecutivo;
                        $generar_consecutivo->estado_cerrado = 1;
                        $generar_consecutivo->total_pagar = $generar_consecutivo->total_devengado - $generar_consecutivo->total_deduccion; 
                        $generar_consecutivo->save(false);
                }
                //actualizar el estado del periodo a 1
                $periodo_pago = PeriodoPagoNomina::findone($id);
                $periodo_pago->estado_periodo = 1;
                $periodo_pago->save(false);
            }else{
                //CODIGO QUE VALIDE CESANTIAS
                if($tipo_nomina == 3){
                    //codigo que genera los intereses
                    $nomina = ProgramacionNomina::find()->where(['=','id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
                    $total_porcentaje = 0;
                    foreach ($nomina as $intereses):
                        $interes = new InteresesCesantia();
                        $total_porcentaje = ($intereses->dias_pago * 0.12)/360; 
                        $interes->id_programacion = $intereses->id_programacion;
                        $interes->id_grupo_pago = $intereses->id_grupo_pago;
                        $interes->id_periodo_pago_nomina = $id;
                        $interes->id_tipo_nomina = 6;
                        $interes->id_contrato = $intereses->id_contrato;
                        $interes->id_empleado = $intereses->id_empleado;
                        $interes->documento = $intereses->cedula_empleado;
                        $interes->inicio_contrato = $intereses->fecha_inicio_contrato;
                        $interes->salario_promedio = $intereses->salario_promedio;
                        $interes->vlr_cesantia = $intereses->total_devengado;
                        $interes->id_programacion = $intereses->id_programacion;
                        $interes->fecha_inicio = $fecha_desde;
                        $interes->fecha_corte = $fecha_hasta;
                        $interes->dias_generados = $intereses->dias_pago;
                        $interes->vlr_intereses = round($intereses->total_devengado * $total_porcentaje);
                        $interes->porcentaje = round($total_porcentaje,3);
                        $interes->usuariosistema = $intereses->usuariosistema;
                        $interes->insert();
                    endforeach;
                    
                    //CODIGO QUE ACTUALIZA LOS CONTRATOS EN EL CAMPO ULTIMA CESANTIA
                    $nomina = ProgramacionNomina::find()->where(['=','id_periodo_pago_nomina', $id])->all();
                    foreach ($nomina as $actualizar) {
                        $contrato = Contrato::find()->where(['=','id_contrato', $actualizar->id_contrato])->one();
                        $contrato->ultima_cesantia = $fecha_hasta;
                        $contrato->ibp_cesantia_inicial = 0;
                        $contrato->save(false);
                    }
                    //ACTUALIZA EL GRUPO DE PAGO EN SU CAMPO 'ULTIMO_PAGO_CESANTIA'
                    $grupo_pago = GrupoPago::find()->where(['=','id_grupo_pago', $id_grupo_pago])->one();
                    $grupo_pago->ultimo_pago_cesantia = $fecha_hasta;
                    $grupo_pago->save(false);
                    //GENERA EL CONSECUTIVO DE LAS PRESTACIONES
                    $nomina = ProgramacionNomina::find()->where(['=','id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
                    foreach ($nomina as $generar_consecutivo) {
                        $consecutivo = Consecutivo::findOne(10);
                        $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
                        $consecutivo->save(false);
                        $generar_consecutivo->nro_pago = $consecutivo->consecutivo;
                        $generar_consecutivo->estado_cerrado = 1;
                        $generar_consecutivo->total_pagar = $generar_consecutivo->total_devengado - $generar_consecutivo->total_deduccion; 
                        $generar_consecutivo->save(false);
                    }
                    //actualizar el estado del periodo a 1
                    $periodo_pago = PeriodoPagoNomina::findone($id);
                    $periodo_pago->estado_periodo = 1;
                     $periodo_pago->save(false);
                } //FIN CODIGO DE CESANTIAS
            }//FIN CODIGO DE PRIMA
        }//FIN CODIGO DE APLICAR PAGO   
       
         $this->redirect(["programacion-nomina/view", 'id' => $id,
            'id_grupo_pago' => $id_grupo_pago,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
        ]);
        
    }
    //codigo que actualiza saldos de creditos
    protected function ModuloActualizarCreditos($credito, $id, $tipo_nomina)
    {
        $nomina = ProgramacionNomina::find()->where(['=','id_periodo_pago_nomina', $id])->andWhere(['=','id_empleado', $credito->id_empleado])->one();
        if($nomina){
            $detalle_nomina = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $nomina->id_programacion])->andWhere(['=','id_credito', $credito->id_credito ])->one();        
            $vlr_cuota = $detalle_nomina->deduccion;
            $nro_cuotas = $credito->numero_cuotas;
            $cuota_actual  = $credito->numero_cuota_actual;
            $saldo_credito = $credito->saldo_credito;
            $credito->saldo_credito = $saldo_credito - $vlr_cuota;
            $credito->numero_cuota_actual = $cuota_actual + 1;
            if ($credito->saldo_credito <= 0){
                $credito->estado_credito = 0;
                $credito->estado_periodo = 0;
            }
            $credito->save(false);
            $abono_credito = new AbonoCredito();
            $abono_credito->id_credito = $credito->id_credito;
            $abono_credito->vlr_abono = $vlr_cuota;
            $abono_credito->saldo = $credito->saldo_credito;
            $abono_credito->cuota_pendiente = $nro_cuotas - $credito->numero_cuota_actual;
            if($tipo_nomina == 1){
                $abono_credito->id_tipo_pago = 1;
                $abono_credito->observacion = 'Deduccion de nomina'; 
            }else{
                $abono_credito->id_tipo_pago = 2;
                $abono_credito->observacion = 'Deduccion de primas'; 
            }    
            $abono_credito->usuariosistema = Yii::$app->user->identity->username;
            $abono_credito->insert();
        }    
    }
    
    public function actionEditarcolillapagonomina($id_programacion, $id_grupo_pago, $id, $fecha_desde, $fecha_hasta) {
        
        $model = ProgramacionNomina::findOne($id_programacion);
        if (Yii::$app->request->post()) {
            if (isset($_POST["eliminardetallecolilla"])) { 
                if (isset($_POST["id_detalle_colilla"])) {
                    $intIndice = 0; $codigo = 0;
                    foreach ($_POST["id_detalle_colilla"] as $intCodigo):
                        $codigo = $intCodigo;
                        $eliminar = ProgramacionNominaDetalle::findOne($intCodigo);
                        $this->ActualizarColillaPago($id_programacion, $codigo);
                        $eliminar->delete(); 
                        $this->redirect(["programacion-nomina/view", 'id' => $id, 'id_grupo_pago' => $id_grupo_pago, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
                        $intIndice ++;
                    endforeach;
                }
               return $this->redirect(['view','id' => $id, 'id_grupo_pago' => $id_grupo_pago, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
            }    
        }else{
            return $this->render('editarcolillapagonomina', [
                        'id_programacion' => $id_programacion,
                        'model' => $model,  
                        'id' => $id,
                        'fecha_desde' => $fecha_desde,
                        'fecha_hasta' => $fecha_hasta,
                        'id_grupo_pago' => $id_grupo_pago,
            ]); 
        } 
    }
    
    protected function ActualizarColillaPago($id_programacion, $codigo) {
        $model = ProgramacionNominaDetalle::findOne($codigo);
        $concepto = ConceptoSalarios::find()->all();
        $nomina = ProgramacionNomina::findOne($id_programacion);
        $prestacional = 0; $vlr_no_prestacional = 0; $descuento = 0;
        $total_prestacional = 0; $total_no_prestacional = 0; $total_descuento = 0; $transporte = 0; $total_transporte = 0;
        foreach ($concepto as $detalle):
            if($detalle->codigo_salario === $model->codigo_salario){
                 $codigo;
                 if ($detalle->prestacional == 1){
                     $prestacional = $model->vlr_devengado;
                 }
                 if($detalle->debito_credito == 2){
                     $descuento = $model->vlr_deduccion;
                 }  
                 if ($detalle->prestacional == 0){
                        $vlr_no_prestacional = $model->vlr_devengado_no_prestacional;
                 } 
                  if ($detalle->auxilio_transporte == 1){
                        $transporte = $model->auxilio_transporte;
                 } 
            }
        endforeach;    
        $total_prestacional += $prestacional;
        $total_descuento += $descuento;
        $total_no_prestacional += $vlr_no_prestacional;
        $total_transporte += $transporte;
        $nomina->ibc_prestacional = $nomina->ibc_prestacional - $prestacional;
        $nomina->ibc_no_prestacional =  $nomina->ibc_no_prestacional - $total_no_prestacional;
        $nomina->total_auxilio_transporte =  $nomina->total_auxilio_transporte - $total_transporte;
        $nomina->total_devengado = $nomina->total_devengado - $total_prestacional - $total_no_prestacional - $total_transporte;
        $nomina->total_deduccion = $nomina->total_deduccion - $total_descuento;
        $nomina->total_pagar = $nomina->total_devengado - $nomina->total_deduccion;
        $nomina->save(false);
    }
    
    public function actionVernomina($id_programacion, $id_empleado, $id_grupo_pago, $id_periodo_pago_nomina)
    {
        $model = new \app\models\FormSoportePagoNomina();
       
     
        if (Yii::$app->request->get("id_programacion")) {
           $nomina = ProgramacionNomina::find()->where(['=','id_programacion', $id_programacion])->one();            
            if ($nomina) {                                
                $model->id_programacion = $id_programacion;
                $model->id_empleado = $nomina->id_empleado;
                $model->cedula_empleado = $nomina->cedula_empleado;
                $model->salario_contrato = $nomina->salario_contrato;
                $model->nro_pago = $nomina->nro_pago;
                $model->fecha_desde = $nomina->fecha_desde;
                $model->fecha_hasta = $nomina->fecha_hasta;
                $model->dias_pago = $nomina->dias_pago;
                $model->dia_real_pagado = $nomina->dia_real_pagado;
                $model->total_devengado = $nomina->total_devengado;
                $model->total_deduccion = $nomina->total_deduccion;
                $model->id_contrato = $nomina->id_contrato;
                $model->total_pagar = $nomina->total_pagar;
                $model->fecha_inicio_contrato = $nomina->fecha_inicio_contrato;
                $model->id_periodo_pago_nomina= $nomina->id_periodo_pago_nomina;
                $model->salario_promedio= $nomina->salario_promedio;
                $model->dias_ausentes = $nomina->dias_ausentes;
                $model->usuariosistema = $nomina->usuariosistema;
                $model->fecha_creacion = $nomina->fecha_creacion;
            }
        }
       return $this->renderAjax('vernominapago',
               ['model' => $model,
               'id' => $id_programacion,
               'id_empleado' => $id_empleado,
               'id_grupo_pago' => $id_grupo_pago,
               'id_periodo_pago_nomina' => $id_periodo_pago_nomina,    
                  
               ]);
    }
    //IMPRESIONES DE DOCUMENTOS COLILLA DE PAGO
     public function actionImprimircolilla($id)
    {
                                
        return $this->render('../formatos/colillaPago', [
            'model' => $this->findModel($id),
            
        ]);
    }
    
    //IMPRIMIR DOCUMENTO SOPORTE DE NOMINA
    public function actionImprimir_detalle_documento($id_nomina)
    {
        $model = \app\models\NominaElectronica::findOne($id_nomina);                        
        return $this->render('../formatos/documento_electronico_nomina', [
            'model' => $model,
            
        ]);
    }
    
        public function actionEditarcolillapagosabatino($id_programacion, $id, $id_grupo_pago, $fecha_desde, $fecha_hasta){
        
        $model= ProgramacionNomina::findone($id_programacion);
        if (Yii::$app->request->post()) { 
            if (isset($_POST["id_detalle"])) {
                $intIndice = 0;
                foreach ($_POST["id_detalle"] as $intCodigo) {
                    $table = ProgramacionNominaDetalle::find()->where(['=','id_detalle', $_POST["id_detalle"][$intIndice]])->one();
                    $salario = ConceptoSalarios::find()->where(['=','codigo_salario', $table->codigo_salario])->andWhere(['=','inicio_nomina', 1])->one();
                    $transporte = ConceptoSalarios::find()->where(['=','codigo_salario', $table->codigo_salario])->andWhere(['=','auxilio_transporte', 1])->one();
                    $eps = ConceptoSalarios::find()->where(['=','codigo_salario', $table->codigo_salario])->andWhere(['=','concepto_salud', 1])->one();
                    $pension = ConceptoSalarios::find()->where(['=','codigo_salario', $table->codigo_salario])->andWhere(['=','concepto_pension', 1])->one();
                    if($table){  
                        if ($salario){
                            $table->horas_periodo_reales = $_POST["horas_periodo_reales"][$intIndice];
                            $vlr_hora = $_POST["vlr_hora"][$intIndice];
                            $vlr_dia = $_POST["vlr_dia"][$intIndice];
                            $total_pagar =  $table->horas_periodo_reales * $vlr_hora; 
                            $table->dias_reales = $_POST["dias_reales"][$intIndice];
                            $table->vlr_devengado = round($total_pagar);
                            $model->horas_pago =  $table->horas_periodo_reales;
                            $model->dia_real_pagado =  $table->dias_reales;
                            $model->ibc_prestacional = round($total_pagar + $model->total_tiempo_extra + $model->total_incapacidad + $model->total_licencia + $model->total_recargo);
                            $model->total_devengado = round($total_pagar + $model->total_tiempo_extra + $model->total_incapacidad + $model->total_licencia + $model->total_recargo + $model->total_auxilio_transporte);
                           $model->save(false);
                                   
                        }
                        if($transporte){
                             $nro_dias = $_POST["dias_transporte"][$intIndice];
                             $vlr_dia = $_POST["vlr_dia"][$intIndice];
                             $total_pagar =  $nro_dias * $vlr_dia;
                             $table->auxilio_transporte = round($total_pagar);
                             $table->dias_transporte = $_POST["dias_transporte"][$intIndice];
                             $model->total_auxilio_transporte = round($total_pagar);
                             $model->save(false);
                        }
                        if($pension){
                            $porcentaje = $_POST["porcentaje"][$intIndice];
                            $calculo = ($model->ibc_prestacional * $porcentaje) /100;
                            $table->descuento_pension = round($calculo);
                            $table->vlr_deduccion = round($calculo);
                        }
                        if($eps){
                            $porcentaje = $_POST["porcentaje"][$intIndice];
                            $calculo = ($model->ibc_prestacional * $porcentaje) /100;
                            $table->descuento_salud = round($calculo);
                            $table->vlr_deduccion = round($calculo);
                        }
                      $table->save();     
                    }    
                    $intIndice++;
                } 
                $con = 0;
                $detalle = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $id_programacion])->all();
                foreach ($detalle as $deduccion):
                    $con = $con + $deduccion->vlr_deduccion;
                endforeach;        
                $model->total_deduccion = $con;
                $model->total_pagar = $model->total_devengado - $model->total_deduccion;
                $model->save(false);
                $this->redirect(["programacion-nomina/view", 'id' => $id, 'id_grupo_pago' => $id_grupo_pago, 'fecha_desde' => $fecha_desde,
                     'fecha_hasta' => $fecha_hasta,
                    ]);
            }
        }
        return $this->renderAjax('_editarcolillapagosabatino', [
            'id_programacion' => $id_programacion,
            'model' => $model, 
            ]);
    }  
    
    //PERMITE CREAR EL PERIODO DE PAGO
    public function actionCrear_nuevo_documento() {
        $model = new \app\models\FormCostoGastoEmpresa();
         if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()){
                if (isset($_POST["crear_periodo"])) {
                    if($model->tipo_nomina <> ''){
                        $table = new PeriodoNominaElectronica();
                        $table->fecha_inicio_periodo = $model->fecha_inicio;
                        $table->fecha_corte_periodo = $model->fecha_corte;
                        $table->user_name = Yii::$app->user->identity->username;
                        $table->type_document_id = $model->tipo_nomina;
                        $table->nota = 'Nomina del ' . $model->fecha_inicio . ' al ' . $model->fecha_corte . '.';
                        $table->save();
                        return $this->redirect(["documento_electronico"]); 
                    }else{
                        Yii::$app->getSession()->setFlash('error','Debe de seleccionar el tipo de nomina a crear. ');
                        return $this->redirect(['documento_electronico']);
                    }    
                }
            } 
         }
        return $this->renderAjax('crear_nuevo_periodo', [
            'model' => $model,       
        ]);    
    }
    
    //CARGAR EMPLEADOS PARA NOMINA
    public function actionCargar_empleados_nomina($id_periodo, $fecha_inicio, $fecha_corte)
    {
        $nomina = ProgramacionNomina::find()->where(['=','documento_generado', 0])->orderBy('id_empleado ASC')->all();
        $periodo = PeriodoNominaElectronica::findOne($id_periodo);
        $auxiliar = 0;
        $contador = 0;
        if(count($nomina) > 0){
            foreach ($nomina as $key => $items) {
                if($items->id_empleado <> $auxiliar){
                    $contador += 1;
                    $totales = ProgramacionNomina::find()->where(['=','id_empleado', $items->id_empleado])->andWhere(['=','documento_generado', 0])->all();
                    $tDevengado = 0; $tDeduccion = 0; $tPagar = 0;
                    foreach ($totales as $key => $total) {
                        $total->documento_generado = 1;
                        $total->save();
                    }
                    $table = new \app\models\NominaElectronica();
                    $table->id_periodo_pago = $items->periodoPagoNomina->periodoPago->id_periodo_pago;
                    $table->id_tipo_nomina = $items->id_tipo_nomina;
                    $table->id_contrato = $items->id_contrato;
                    $table->id_empleado = $items->id_empleado;
                    $table->codigo_documento = $items->empleado->tipoDocumento->codigo_interface_nomina;
                    $table->id_periodo_electronico = $id_periodo;
                    $table->id_grupo_pago = $items->id_grupo_pago;
                    $table->documento_empleado = $items->cedula_empleado;
                    $table->primer_nombre = $items->empleado->nombre1;
                    $table->segundo_nombre = $items->empleado->nombre2;
                    $table->primer_apellido = $items->empleado->apellido1;
                    $table->segundo_apellido = $items->empleado->apellido2;
                    $table->nombre_completo = $items->empleado->nombrecorto;
                    $table->email_empleado = $items->empleado->email;
                    $table->salario_contrato = $items->salario_contrato;
                    $table->type_worker_id = $items->contrato->tipoCotizante->codigo_api_nomina;
                    $table->sub_type_worker_id = $items->contrato->subtipoCotizante->codigo_api_nomina;
                    $table->codigo_municipio = $items->empleado->municipio->codigomunicipio;
                    $table->direccion_empleado = $items->empleado->direccion;
                    $table->codigo_forma_pago = $items->empleado->formaPago->codigo_api_nomina;
                    $table->nombre_banco = $items->empleado->bancoEmpleado->banco;
                    if($items->empleado->tipo_cuenta == 'S'){
                         $table->nombre_cuenta = 'Ahorro';
                    }else{
                        $table->nombre_cuenta = 'Corriente';
                    }
                    $table->numero_cuenta = $items->empleado->cuenta_bancaria;
                    $table->fecha_inicio_nomina = $fecha_inicio;
                    $table->fecha_final_nomina = $fecha_corte;
                    $table->fecha_inicio_contrato = $items->fecha_inicio_contrato;
                    $table->fecha_terminacion_contrato = $items->fecha_final_contrato;
                    $table->fecha_envio_nomina = date('Y-m-d');
                    $table->fecha_inicio_contrato = $items->fecha_inicio_contrato;
                    $table->user_name = Yii::$app->user->identity->username;
                    $table->save(false);
                    $auxiliar =  $items->id_empleado;
                    $periodo->cantidad_empleados = $contador;
                    $periodo->save();
                }else{
                   $auxiliar =  $items->id_empleado;
                }    
            
            }
            return $this->redirect(["documento_electronico"]); 
        }else{
            Yii::$app->getSession()->setFlash('info','No existen empleado con nominas pendientes para procesar.');
             return $this->redirect(["documento_electronico"]); 
        }    
        
    }
    
    //VISTA DE EMPLEADOS CON DOCUMENTOS ELECTRONICOS PARA GENERAR EL DETALLE
    public function actionVista_empleados($id_periodo, $token) {
        $form = new \app\models\FormFiltroDocumentoElectronico();
        $documento = null;
        $empleado = null;
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $documento = Html::encode($form->documento);
                $empleado = Html::encode($form->empleado);
                $table = \app\models\NominaElectronica::find()
                         ->andFilterWhere(['=','documento_empleado', $documento])
                         ->andFilterWhere(['like','nombre_completo', $empleado])
                        ->andWhere(['=','id_periodo_electronico', $id_periodo]);
                $table = $table->orderBy('id_nomina_electronica ASC');
                $tableexcel = $table->all();
                $count = clone $table;
                $to = $count->count();
                $pages = new Pagination([
                    'pageSize' => 40,
                    'totalCount' => $count->count()
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                   $this->actionExcelconsultaDocumentos($tableexcel);
                }
            }
        }else{
           $table = \app\models\NominaElectronica::find()->where(['=','id_periodo_electronico', $id_periodo])->orderBy('id_nomina_electronica ASC');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 40,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                    $this->actionExcelconsultaDocumentos($tableexcel);
                }  
        }
        if (isset($_POST["crear_documento_electronico"])) { ////entra al ciclo cuando presiona el boton crear documentos
            if (isset($_POST["documento_electronico"])) {
                $intIndice = 0;
                $contador = 0;
                foreach ($_POST["documento_electronico"] as $intCodigo) { //vector que cargar cada items
                    $contador += 1;
                    $conRegistro = \app\models\NominaElectronica::find()->where(['=','id_nomina_electronica', $intCodigo])->andWhere(['=','generado_detalle', 0])->one();//array que busca el empleado
                    if($conRegistro){
                        $buscarNomina = ProgramacionNomina::find()->where(['=','id_empleado', $conRegistro->id_empleado])->andWhere(['=','documento_detalle_generado', 0])->all();
                        foreach ($buscarNomina as $key => $datos) {

                            $detalle_nomina = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $datos->id_programacion])->all();
                            foreach ($detalle_nomina as $key => $detalle) {
                                $buscar = \app\models\NominaElectronicaDetalle::find()->where(['=','codigo_salario', $detalle->codigo_salario])->andWhere(['=','id_periodo_electronico', $id_periodo])
                                                                                      ->andWhere(['=','id_empleado', $conRegistro->id_empleado])->one();
                                
                                if(!$buscar){
                                    $table = new \app\models\NominaElectronicaDetalle();
                                    $table->id_nomina_electronica = $intCodigo;
                                    $table->codigo_salario = $detalle->codigo_salario;
                                    $table->id_empleado = $conRegistro->id_empleado;
                                    $table->descripcion = $detalle->codigoSalario->nombre_concepto;
                                    $table->devengado_deduccion = $detalle->codigoSalario->devengado_deduccion;
                                    $table->fecha_inicio = $conRegistro->fecha_inicio_nomina;
                                    $table->fecha_final = $conRegistro->fecha_final_nomina;
                                    if($table->devengado_deduccion == 1){ //ingresos del empleado
                                        if ($detalle->codigoSalario->id_agrupado == 1){ //salario basico
                                            $conRegistro->dias_trabajados = $detalle->dias_reales;
                                            $table->devengado = $detalle->vlr_devengado;
                                            $table->total_dias = $detalle->dias_reales;
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 2){ //auxilio de transporte
                                          $table->total_dias = $detalle->dias_reales;
                                          $table->auxilio_transporte = $detalle->auxilio_transporte; 
                                          $table->devengado = $detalle->auxilio_transporte; 
                                          
                                        }elseif ($detalle->codigoSalario->id_agrupado == 3){ //horas extras diurnas ordinaria
                                            $table->devengado = $detalle->vlr_devengado;
                                            $table->porcentaje = $detalle->codigoSalario->porcentaje_tiempo_extra;
                                            $table->total_dias =$detalle->nro_horas; 
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 9){ //incapacidades
                                            $codigo_incapacidad = Incapacidad::findOne($detalle->id_incapacidad);
                                            $table->valor_pago_incapacidad = $detalle->vlr_devengado;
                                            $table->devengado = $detalle->vlr_devengado;
                                            $table->dias_incapacidad = $detalle->dias_reales;
                                            $table->total_dias = $detalle->dias_reales;
                                            $table->inicio_incapacidad = $detalle->fecha_desde;
                                            $table->final_incapacidad = $detalle->fecha_hasta;
                                            $table->codigo_incapacidad = $codigo_incapacidad->codigo_incapacidad;
                                            $table->porcentaje = $detalle->porcentaje;
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 10 || $detalle->codigoSalario->id_agrupado == 8){ //licencias remuneradas y maternidad
                                            $table->valor_pago_licencia = $detalle->vlr_devengado;
                                            $table->devengado = $detalle->vlr_devengado;
                                            $table->dias_licencia = $detalle->dias_reales;
                                             $table->total_dias = $detalle->dias_reales;
                                            $table->inicio_licencia = $detalle->fecha_desde;
                                            $table->final_licencia = $detalle->fecha_hasta;
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 11){ //primas
                                            $table->valor_pago_prima = $detalle->vlr_devengado;
                                            $table->devengado = $detalle->vlr_devengado;
                                            $table->dias_prima = $detalle->dias_reales;
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 12){ //cesantias
                                            $table->valor_pago_cesantias = $detalle->vlr_devengado;
                                            $table->dias_cesantias = $detalle->dias_reales;
                                            $table->devengado = $detalle->vlr_devengado;
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 13){ //cesantias
                                            $table->valor_pago_intereses = $detalle->vlr_devengado;
                                            $table->devengado = $detalle->vlr_devengado;  
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 16 || $detalle->codigoSalario->id_agrupado == 15 || $detalle->codigoSalario->id_agrupado == 18){ //bonificacion no salaria y comisiones y reintegro
                                            $table->devengado = $detalle->vlr_devengado;
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 19){ //REINTEGRO EMPLEADO
                                            $table->devengado = $detalle->vlr_devengado;
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 20){ //VACACIONES
                                            $table->devengado = $detalle->vlr_devengado;
                                            $table->total_dias = $detalle->dias_reales;
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 21){ //licencias NO remuneradas
                                            $table->total_dias = $detalle->dias_reales;
                                            $table->dias_licencia_noremuneradas = $detalle->dias_reales;
                                            $table->inicio_licencia = $detalle->fecha_desde;
                                            $table->final_licencia = $detalle->fecha_hasta;
                                        }
                                    }else{// DEDUCCIONES DEL EMPLEADO
                                        if($detalle->codigoSalario->id_agrupado == 4){ //FONDO DE PENSION
                                            $table->porcentaje = $detalle->codigoSalario->porcentaje; 
                                            $table->deduccion_pension = $detalle->vlr_deduccion;
                                            $table->deduccion = $detalle->vlr_deduccion;
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 5){ //FONDO DE EPS
                                            $table->porcentaje = $detalle->codigoSalario->porcentaje; 
                                            $table->deduccion_eps = $detalle->vlr_deduccion;
                                            $table->deduccion = $detalle->vlr_deduccion;
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 6){ //FONDO solidarida
                                                    $table->porcentaje = $detalle->porcentaje; 
                                                    $table->deduccion_fondo_solidaridad = $detalle->vlr_deduccion;
                                                    
                                        }elseif ($detalle->codigoSalario->id_agrupado == 7 || $detalle->codigoSalario->id_agrupado == 17) { //otras deducciones del empleado y prestamos empresa
                                            $table->deduccion= $detalle->vlr_deduccion;
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 14) { //libranzas y bancos
                                            $table->deduccion= $detalle->vlr_deduccion; 
                                        }

                                    }
                                    $table->id_agrupado = $detalle->codigoSalario->id_agrupado;
                                    $table->id_periodo_electronico = $id_periodo;
                                    $table->save(false);
                                    $conRegistro->save(false);
                               }else{// Acumula informacion si el registro esta en la base de datos
                                    if($buscar->devengado_deduccion == 1){ //DEVENGADO DEL TRABAJADO
                                        if ($detalle->codigoSalario->id_agrupado == 1){ //salario basico
                                            $buscar->devengado += $detalle->vlr_devengado;
                                            $buscar->total_dias += $detalle->dias_reales;
                                            $conRegistro->dias_trabajados += $detalle->dias_reales;
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 2){ //auxilio de transporte
                                          $buscar->total_dias += $detalle->dias_reales;
                                          $buscar->auxilio_transporte += $detalle->auxilio_transporte; 
                                          $buscar->devengado += $detalle->auxilio_transporte; 
                                       
                                        }elseif ($detalle->codigoSalario->id_agrupado == 9){ //incapacidades
                                            $table = new \app\models\NominaElectronicaDetalle();
                                            $codigo_incapacidad = Incapacidad::findOne($detalle->id_incapacidad);
                                            $table->valor_pago_incapacidad = $detalle->vlr_devengado;
                                            $table->dias_incapacidad = $detalle->dias_reales;
                                            $table->total_dias = $detalle->dias_reales;
                                            $table->inicio_incapacidad = $detalle->fecha_desde;
                                            $table->final_incapacidad = $detalle->fecha_hasta;
                                            $table->id_incapacidad = $detalle->id_incapacidad;
                                            $table->codigo_incapacidad = $codigo_incapacidad->codigo_incapacidad;
                                            $table->porcentaje = $detalle->porcentaje;
                                            $table->save(false);
                                        }elseif ($detalle->codigoSalario->id_agrupado == 10 || $detalle->codigoSalario->id_agrupado == 8){ //licencias remuneradas
                                            $table = new \app\models\NominaElectronicaDetalle();
                                            $table->valor_pago_licencia = $detalle->vlr_devengado;
                                            $table->dias_licencia = $detalle->dias_reales;
                                            $table->total_dias = $detalle->dias_reales;
                                            $table->inicio_licencia = $detalle->fecha_desde;
                                            $table->final_licencia = $detalle->fecha_hasta;
                                            $table->save(false);
                                        
                                        }elseif ($detalle->codigoSalario->id_agrupado == 16 || $detalle->codigoSalario->id_agrupado == 15 || $detalle->codigoSalario->id_agrupado == 18 || $detalle->codigoSalario->id_agrupado == 19){ //bonificaciones y comisiones
                                            $buscar->devengado += $detalle->vlr_devengado;
                                            $buscar->total_dias += $detalle->dias_reales;
                                        }elseif ($detalle->codigoSalario->id_agrupado == 20 ){
                                            $buscar->devengado += $detalle->vlr_devengado;
                                                
                                        }elseif ($detalle->codigoSalario->id_agrupado == 21){ //licencias NO remuneradas
                                            $table = new \app\models\NominaElectronicaDetalle();
                                            $table->dias_licencia_noremuneradas = $detalle->dias_reales;
                                            $table->total_dias = $detalle->dias_reales;
                                            $table->inicio_licencia = $detalle->fecha_desde;
                                            $table->final_licencia = $detalle->fecha_hasta;
                                            $table->save(false);
                                        }    
                                    }else{ // acumulado de deducciones
                                        if($detalle->codigoSalario->id_agrupado == 4){ //FONDO DE PENSION
                                            $buscar->deduccion_pension += $detalle->vlr_deduccion;  
                                            $buscar->deduccion += $detalle->vlr_deduccion;
                                        }elseif ($detalle->codigoSalario->id_agrupado == 5){ //FONDO DE EPS
                                            $buscar->deduccion_eps += $detalle->vlr_deduccion;  
                                            $buscar->deduccion += $detalle->vlr_deduccion;
                                        }elseif ($detalle->codigoSalario->id_agrupado == 6){ //FONDO solidarida
                                            $buscar->deduccion_fondo_solidaridad += $detalle->vlr_deduccion;
                                            $buscar->deduccion += $detalle->vlr_deduccion;
                                        }elseif ($detalle->codigoSalario->id_agrupado == 14){ //descuentos y libranzas
                                            $buscar->deduccion += $detalle->vlr_deduccion; 
                                            
                                        }elseif ($detalle->codigoSalario->id_agrupado == 7 || $detalle->codigoSalario->id_agrupado == 17){ //otras deducciones del empleado y prestamos empresa
                                            $buscar->deduccion += $detalle->vlr_deduccion; 
                                         
                                        }elseif ($detalle->codigoSalario->id_agrupado == 14) { //libranzas y bancos
                                            $table->deduccion += $detalle->vlr_deduccion; 
                                        }
                                    }
                                    $buscar->save(false);
                                    $conRegistro->save(false);
                               }
                            }
                        //cierre en programacion turnos
                        $datos->documento_detalle_generado = 1;
                        $datos->save();    
                        }
                        //cierra en nomina electronica
                        $conRegistro->generado_detalle = 1;
                        $conRegistro->save();
                        
                    }else{
                        $conRegistro = \app\models\NominaElectronica::findOne($intCodigo);
                        Yii::$app->getSession()->setFlash('info','El empleado ('.$conRegistro->nombre_completo.'), ya se le genero el detalle de la Nomina para enviarlo a la DIAN.');
                        return $this->redirect(['vista_empleados','id_periodo' => $id_periodo, 'token' => $token]); 
                    }    
                }
                Yii::$app->getSession()->setFlash('success','Se procesaron ('.$contador.') registros para el documento electrónica de nomina.');
                return $this->redirect(['vista_empleados','id_periodo' => $id_periodo ,'token' => $token]);
            }else{
                Yii::$app->getSession()->setFlash('error','Debe de seleccionar al menos un registro. ');
            }
        }    
        return $this->render('importar_detalle_nomina', [
            'model' => $model, 
            'id_periodo' => $id_periodo,
            'form' => $form,
            'pagination' => $pages,
            'token' => $token,
        ]);    
    }
    
    //CERRAR PERIODO DE NOMINA ELECTRONICA
    public function actionCerrar_periodo_nomina($id_periodo) {
        $sw = 0;
        $periodo = PeriodoNominaElectronica::findOne($id_periodo);
        $documentos = \app\models\NominaElectronica::find()->where(['=','id_periodo_electronico', $id_periodo])->all();
        foreach ($documentos as $key => $validar) {
            if($validar->generado_detalle ==0){
                $sw = 1;
                break;
            }
        }
        if($sw == 0){
            $this->AcumularTotalesNominaElectronica($id_periodo);
            $this->GranTotalNominaElectronica($id_periodo);
            $this->GenerarConsecutivos($id_periodo);
            $periodo->cerrar_proceso = 1;
            $periodo->save();
            return $this->redirect(['documento_electronico']);
        }else{
            Yii::$app->getSession()->setFlash('error','El periodo no se puede cerrar porque hay nominas que no se han validado. Consulte con el administrador. ');
            return $this->redirect(['documento_electronico']);
        }
        
    }
    
    //PROCESO DE ACUMULA LOS TOTALES 
    protected function AcumularTotalesNominaElectronica($id_periodo) {
        $documento = \app\models\NominaElectronica::find()->where(['=','id_periodo_electronico', $id_periodo])->all();
        $devengado = 0; $deduccion = 0;
        foreach ($documento as $key => $datos) {
            $detalles = \app\models\NominaElectronicaDetalle::find()->where(['=','id_empleado', $datos->id_empleado])->andWhere(['=','id_periodo_electronico', $id_periodo])->all();
            if(count($detalles) > 0){
                foreach ($detalles as $key => $val) {
                     if($val->devengado_deduccion == 1){
                         $devengado += $val->devengado;
                     }else{
                         $deduccion += $val->deduccion;
                     }
                }
                $datos->total_devengado = $devengado;
                $datos->total_deduccion = $deduccion;
                $datos->total_pagar = $devengado - $deduccion;
                $datos->save();
                $devengado = 0; $deduccion = 0;
            }
        }
    }
    
    //PROCESO QUE TOLIZA EL VALOR DE LA NOMINA DEL MES
    protected function GranTotalNominaElectronica($id_periodo)
    {
        $periodo = PeriodoNominaElectronica::findOne($id_periodo);
        $nomina = \app\models\NominaElectronica::find()->where(['=','id_periodo_electronico', $id_periodo])->all(); 
        $total = 0; $devengado = 0; $deduccion = 0;
        foreach ($nomina as $key => $val) {
            $devengado += $val->total_devengado;
            $deduccion += $val->total_deduccion;
            $total += $val->total_pagar;
        }
        $periodo->total_nomina = $total;
        $periodo->devengado_nomina = $devengado;
        $periodo->deduccion_nomina = $deduccion;
        $periodo->save();
    }
    
    //GENERAR CONSECUTIVOS
    protected function GenerarConsecutivos($id_periodo)
    {
       $nomina = \app\models\NominaElectronica::find()->where(['=','id_periodo_electronico', $id_periodo])->all();
       $documento_electronico = \app\models\DocumentoElectronico::findOne(5);
       $numero = Consecutivo::findOne(23);
       foreach ($nomina as $key => $validar)
       {
           $codigo = $numero->consecutivo + 1;
           $validar->numero_nomina_electronica = $codigo;
           $validar->consecutivo = $documento_electronico->consecutivo;
           $validar->save();
           $numero->consecutivo = $codigo;
           $numero->save();
           
       }
    }
    
    
    //VISTA DEL DETALLE DEL DOCUMENTO ELECTRONICO
    public function actionDetalle_documento_electronico($id_nomina, $id_periodo, $token) 
    {
        $model = \app\models\NominaElectronica::findOne($id_nomina);
        $detalle_documento = \app\models\NominaElectronicaDetalle::find()->where(['=','id_nomina_electronica', $id_nomina])->orderBy('devengado_deduccion ASC')->all();     
        return $this->render('view_detalle_documento_electronico', [
            'model' => $model, 
            'id_nomina' => $id_nomina,
            'id_periodo' => $id_periodo,
            'detalle_documento' => $detalle_documento,
            'token' => $token,
        ]);    
        
    }
    
    //EXCELES
    public function actionExcelpago($id) {
        $nomina = ProgramacionNomina::find()->where(['=','id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
         $objPHPExcel = new \PHPExcel();
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
                    ->setCellValue('A1', 'NRO PAGO')
                    ->setCellValue('B1', 'GRUPO PAGO')
                    ->setCellValue('C1', 'TIPO PAGO')
                    ->setCellValue('D1', 'PERIODO PAGO')
                    ->setCellValue('E1', 'NRO CONTRATO')
                    ->setCellValue('F1', 'DOCUMENTO')
                    ->setCellValue('G1', 'EMPLEADO')   
                    ->setCellValue('H1', 'FECHA INICIO')
                    ->setCellValue('I1', 'FECHA CORTE')
                    ->setCellValue('J1', 'TOTAL DEVENGADO')
                    ->setCellValue('K1', 'TOTAL DEDUCCION')
                    ->setCellValue('L1', 'NETO PAGAR')
                    ->setCellValue('M1', 'IBP');
        $i = 2;
        
        foreach ($nomina as $val) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->nro_pago)
                    ->setCellValue('B' . $i, $val->grupoPago->grupo_pago)
                    ->setCellValue('C' . $i, $val->tipoNomina->tipo_pago)
                    ->setCellValue('D' . $i, $id)
                    ->setCellValue('E' . $i, $val->id_contrato)
                    ->setCellValue('F' . $i, $val->cedula_empleado)                    
                    ->setCellValue('G' . $i, $val->empleado->nombrecorto)
                    ->setCellValue('H' . $i, $val->fecha_desde)
                    ->setCellValue('I' . $i, $val->fecha_hasta)
                    ->setCellValue('J' . $i, round($val->total_devengado,0))
                    ->setCellValue('K' . $i, round($val->total_deduccion,0))
                    ->setCellValue('L' . $i, round($val->total_pagar,0))
                     ->setCellValue('M' . $i, round($val->ibc_prestacional,0));
            $i++;
        }
        $j = $i + 1;
               
        $objPHPExcel->getActiveSheet()->setTitle('Nomina_pagada');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Nomina general.xlsx"');
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
    
    public function actionExceldetallepago($id) {
         $detalle = ProgramacionNominaDetalle::find()->where(['=','id_periodo_pago_nomina', $id])->orderBy('id_programacion DESC')->all();
         $objPHPExcel = new \PHPExcel();
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
                                   
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID_PROGRAMACION')
                    ->setCellValue('B1', 'PERIODO PAGO')
                    ->setCellValue('C1', 'TIPO PAGO')
                    ->setCellValue('D1', 'GRUPO PAGO')
                    ->setCellValue('E1', 'EMPLEADO')
                    ->setCellValue('F1', 'DESDE')
                    ->setCellValue('G1', 'HASTA')
                    ->setCellValue('H1', 'CONCEPTO')   
                    ->setCellValue('I1', 'DEVENGADO')
                    ->setCellValue('J1', 'DEDUCCION');
                                    
        $i = 2;
       
        foreach ($detalle as $val) {
            $codigo_salario = $val->codigo_salario;
            $concepto = ConceptoSalarios::find()->where(['=','codigo_salario', $codigo_salario])->one();   
            if($concepto->auxilio_transporte == 1){
                $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A' . $i, $val->id_programacion)
                     ->setCellValue('B' . $i, $id)
                     ->setCellValue('C' . $i, $val->programacionNomina->tipoNomina->tipo_pago)
                     ->setCellValue('D' . $i, $val->programacionNomina->grupoPago->grupo_pago)    
                     ->setCellValue('E' . $i, $val->programacionNomina->empleado->nombrecorto)
                     ->setCellValue('F' . $i, $val->fecha_desde)
                     ->setCellValue('G' . $i, $val->fecha_hasta)
                     ->setCellValue('H' . $i, $val->codigoSalario->nombre_concepto)   
                     ->setCellValue('I' . $i, round($val->auxilio_transporte,0))
                     ->setCellValue('J' . $i, round($val->vlr_deduccion,0));
                $i++;
            }else{
               $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A' . $i, $val->id_programacion)
                     ->setCellValue('B' . $i, $id)
                     ->setCellValue('C' . $i, $val->programacionNomina->tipoNomina->tipo_pago)  
                     ->setCellValue('D' . $i, $val->programacionNomina->grupoPago->grupo_pago)  
                     ->setCellValue('E' . $i, $val->programacionNomina->empleado->nombrecorto)
                     ->setCellValue('F' . $i, $val->fecha_desde)
                     ->setCellValue('G' . $i, $val->fecha_hasta)
                     ->setCellValue('H' . $i, $val->codigoSalario->nombre_concepto)   
                     ->setCellValue('I' . $i, round($val->vlr_devengado,0))
                     ->setCellValue('J' . $i, round($val->vlr_deduccion,0));
                $i++; 
            }    
        }
        $k = $i + 1;
               
        $objPHPExcel->getActiveSheet()->setTitle('Detalle nomina');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Nomina detalle.xlsx"');
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
    
    public function actionExcelconsultapago($tableexcel) {
         $objPHPExcel = new \PHPExcel();
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
                            
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'NRO PAGO')
                    ->setCellValue('C1', 'PERIODO PAGO')
                    ->setCellValue('D1', 'TIPO PAGO')
                    ->setCellValue('E1', 'GRUPO PAGO')
                    ->setCellValue('F1', 'NRO CONTRATO')
                    ->setCellValue('G1', 'DOCUMENTO')
                    ->setCellValue('H1', 'EMPLEADO')   
                    ->setCellValue('I1', 'FECHA INICIO')
                    ->setCellValue('J1', 'FECHA CORTE')
                    ->setCellValue('K1', 'SALARIO')
                    ->setCellValue('L1', 'TOTAL DEVENGADO')
                    ->setCellValue('M1', 'TOTAL DEDUCCION')
                    ->setCellValue('N1', 'NETO PAGAR')
                    ->setCellValue('O1', 'IBP');
        $i = 2;
        
        foreach ($tableexcel as $val) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_programacion)
                    ->setCellValue('B' . $i, $val->nro_pago)
                    ->setCellValue('C' . $i, $val->id_periodo_pago_nomina)
                    ->setCellValue('D' . $i, $val->tipoNomina->tipo_pago)
                    ->setCellValue('E' . $i, $val->grupoPago->grupo_pago)
                    ->setCellValue('F' . $i, $val->id_contrato)                    
                    ->setCellValue('G' . $i, $val->cedula_empleado)
                    ->setCellValue('H' . $i, $val->empleado->nombrecorto)
                    ->setCellValue('I' . $i, $val->fecha_desde)
                    ->setCellValue('J' . $i, $val->fecha_hasta)
                    ->setCellValue('K' . $i, round($val->salario_contrato,0))
                    ->setCellValue('L' . $i, round($val->total_devengado,0))
                    ->setCellValue('M' . $i, round($val->total_deduccion,0))
                    ->setCellValue('N' . $i, round($val->total_pagar,0))
                    ->setCellValue('O' . $i, round($val->ibc_prestacional,0));
                   
            $i++;
        }
        $j = $i + 1;
               
        $objPHPExcel->getActiveSheet()->setTitle('Nominas_pagadas');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Nomina general.xlsx"');
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
    
    //CONSULA LOS DETALLES DE NOMINA
    public function actionDetalle_nomina($empleado, $fecha_inicio, $fecha_corte, $grupo_pago, $tipo_nomina){
       if($empleado && $fecha_inicio && $fecha_corte && $tipo_nomina){ //busca los detalles del empleado con un rango de fechas
           $vector = ProgramacionNomina::find()->where(['=','id_empleado', $empleado])->andWhere(['between','fecha_desde', $fecha_inicio, $fecha_corte])
                                               ->andWhere(['=','id_tipo_nomina', $tipo_nomina])->orderBy('id_programacion DESC')->all();
       }elseif ($grupo_pago && $fecha_inicio && $fecha_corte && $tipo_nomina) { //busca los detalles por meido del grupo de pago
            $vector = ProgramacionNomina::find()->where(['=','id_grupo_pago', $grupo_pago])->andWhere(['between','fecha_desde', $fecha_inicio, $fecha_corte])
                                               ->andWhere(['=','id_tipo_nomina', $tipo_nomina])->orderBy('id_programacion DESC')->all();
       }elseif ($empleado && $fecha_inicio && $fecha_corte && $tipo_nomina){
            $vector = ProgramacionNomina::find()->where(['=','id_empleado', $empleado])->andWhere(['between','fecha_desde', $fecha_inicio, $fecha_corte])
                                               ->andWhere(['=','id_tipo_nomina', $tipo_nomina])->orderBy('id_programacion DESC')->all();
       }elseif ($grupo_pago && $fecha_inicio && $fecha_corte && $tipo_nomina){
            $vector = ProgramacionNomina::find()->where(['=','id_grupo_pago', $grupo_pago])->andWhere(['between','fecha_desde', $fecha_inicio, $fecha_corte])
                                              ->andWhere(['=','id_tipo_nomina', $tipo_nomina])->orderBy('id_programacion DESC')->all();
       }elseif ($empleado && $fecha_inicio && $fecha_corte){
            $vector = ProgramacionNomina::find()->where(['=','id_empleado', $empleado])->andWhere(['between','fecha_desde', $fecha_inicio, $fecha_corte])
                                              ->orderBy('id_programacion DESC')->all();
       }elseif ($grupo_pago && $fecha_inicio && $fecha_corte){
            $vector = ProgramacionNomina::find()->where(['=','id_grupo_pago', $grupo_pago])->andWhere(['between','fecha_desde', $fecha_inicio, $fecha_corte])
                                               ->orderBy('id_programacion DESC')->all();
       }elseif ($empleado){
            $vector = ProgramacionNomina::find()->where(['=','id_empleado', $empleado])->orderBy('id_programacion DESC')->all();
       }elseif ($grupo_pago){
            $vector = ProgramacionNomina::find()->where(['=','id_grupo_pago', $grupo_pago])->orderBy('id_programacion DESC')->all();
       }elseif ($tipo_nomina){
           $vector = ProgramacionNomina::find()->where(['=','id_tipo_nomina', $tipo_nomina])->orderBy('id_programacion DESC')->all();
       }
       $objPHPExcel = new \PHPExcel();
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
        
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'NRO PAGO')
                    ->setCellValue('C1', 'PERIODO PAGO')
                    ->setCellValue('D1', 'TIPO PAGO')
                    ->setCellValue('E1', 'GRUPO PAGO')
                    ->setCellValue('F1', 'NRO CONTRATO')
                    ->setCellValue('G1', 'DOCUMENTO')
                    ->setCellValue('H1', 'EMPLEADO')   
                    ->setCellValue('I1', 'FECHA INICIO')
                    ->setCellValue('J1', 'FECHA CORTE')
                    ->setCellValue('K1', 'SALARIO')
                    ->setCellValue('L1', 'CODIDO SALARIO')
                    ->setCellValue('M1', 'CONCEPTO DE SALARIO')
                    ->setCellValue('N1', 'DIAS DE PAGO')
                    ->setCellValue('O1', 'DEVENGADO')
                    ->setCellValue('P1', 'DEDUCCION');
                  
        
        $i = 2;
        
        foreach ($vector as $val) {
            $vector_detalle = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $val->id_programacion])->all();
            foreach ($vector_detalle as $key => $detalle) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $val->id_programacion)
                        ->setCellValue('B' . $i, $val->nro_pago)
                        ->setCellValue('C' . $i, $val->id_periodo_pago_nomina)
                        ->setCellValue('D' . $i, $val->tipoNomina->tipo_pago)
                        ->setCellValue('E' . $i, $val->grupoPago->grupo_pago)
                        ->setCellValue('F' . $i, $val->id_contrato)                    
                        ->setCellValue('G' . $i, $val->cedula_empleado)
                        ->setCellValue('H' . $i, $val->empleado->nombrecorto)
                        ->setCellValue('I' . $i, $val->fecha_desde)
                        ->setCellValue('J' . $i, $val->fecha_hasta)
                        ->setCellValue('K' . $i, $val->salario_contrato)
                        ->setCellValue('L' . $i, $detalle->codigo_salario)
                        ->setCellValue('M' . $i, $detalle->codigoSalario->nombre_concepto)
                        ->setCellValue('N' . $i, $detalle->dias_reales);
                        if($detalle->codigo_salario == 20){
                            $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('O' . $i, $detalle->auxilio_transporte)
                            ->setCellValue('P' . $i, $detalle->vlr_deduccion);
                        }else{
                            $objPHPExcel->setActiveSheetIndex(0)
                           ->setCellValue('O' . $i, $detalle->vlr_devengado) 
                             ->setCellValue('P' . $i, $detalle->vlr_deduccion);
                        }    
                       

                $i++;
            } 
            $i = $i;
        }
        
               
        $objPHPExcel->getActiveSheet()->setTitle('Listados');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Detalle_nomina.xlsx"');
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
    
    protected function findModel($id)
    {
        if (($model = ProgramacionNomina::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
