<?php

namespace app\controllers;

use app\models\Incapacidad;
use app\models\IncapacidadSearch;
use app\models\GrupoPago;
use app\models\GrupoPagoSearch;
use app\models\ConfiguracionIncapacidad;
use app\models\ConfiguracionIncapacidadSearch;
use app\models\SeguimientoIncapacidad;
use app\models\Empleado;
use app\models\Contrato;
use app\models\DiagnosticoIncapacidad;
use app\models\DiagnosticoIncapacidadSearch;
use app\models\FormFiltroIncapacidad;
use app\models\FormIncapacidadSeguimiento;
use app\models\FormIncapacidadSeguimientoEditar;
use app\models\UsuarioDetalle;
use app\models\EntidadSalud;
use app\models\FormIncapacidad;
use app\models\ConfiguracionSalario;
use app\models\ProgramacionNominaDetalle;
use app\models\TiempoServicio;
use app\models\FormFiltroPagoIncapacidad;
use app\models\Pagoincapacidadeps;
use app\models\PagoincacidadepsDetalle;
use app\models\FormPagoIncapacidad;
use \app\models\FormMaquinaBuscar;
use app\models\Consecutivo;
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
 * IncapacidadController implements the CRUD actions for Incapacidad model.
 */
class IncapacidadController extends Controller
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
     * Lists all Incapacidad models.
     * @return mixed
     */
   public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',81])->all()){
                $form = new FormFiltroIncapacidad();
                $id_empleado = null;
                $numero_incapacidad = null;
                $id_grupo_pago = null;
                $codigo_incapacidad = null; 
                $fecha_inicio = null;
                $fecha_final = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $id_empleado = Html::encode($form->id_empleado);
                        $numero_incapacidad = Html::encode($form->numero_incapacidad);
                        $id_grupo_pago = Html::encode($form->id_grupo_pago);
                        $codigo_incapacidad = Html::encode($form->codigo_incapacidad);
                        $fecha_inicio  = Html::encode($form->fecha_inicio);
                        $fecha_final = Html::encode($form->fecha_final);
                        $table = Incapacidad::find()
                                ->andFilterWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                                ->andFilterWhere(['=', 'codigo_incapacidad', $codigo_incapacidad])
                                ->andFilterWhere(['=', 'id_empleado', $id_empleado])                                                                                              
                                ->andFilterWhere(['=', 'numero_incapacidad', $numero_incapacidad])
                                ->andFilterWhere(['between', 'fecha_inicio', $fecha_inicio, $fecha_final]);

                        $table = $table->orderBy('id_incapacidad DESC');
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
                                $check = isset($_REQUEST['id_incapacidad DESC']);
                                $this->actionExcelconsulta($tableexcel);
                            }
                } else {
                        $form->getErrors();
                }                    
            } else {
                $table = Incapacidad::find()
                        ->orderBy('id_incapacidad DESC');
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
                if(isset($_POST['excel'])){
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
        }else{
             return $this->redirect(['site/sinpermiso']);
        }     
        }else{
           return $this->redirect(['site/login']);
        }
   }
   
    public function actionIndexpagoincapacidad() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 110])->all()) {
               $form = new FormFiltroPagoIncapacidad();
                $id_entidad_salud = null;
                $nro_pago = null;
                $fecha_desde = null;
                $fecha_hasta = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_entidad_salud = Html::encode($form->id_entidad_salud);
                        $nro_pago = Html::encode($form->nro_pago);
                        $fecha_desde = Html::encode($form->fecha_desde);
                        $fecha_hasta = Html::encode($form->fecha_hasta);
                        $table = Pagoincapacidadeps::find()
                                ->andFilterWhere(['=', 'id_entidad_salud', $id_entidad_salud])
                                ->andFilterWhere(['=', 'nro_pago', $nro_pago])
                                ->andFilterWhere(['>=', 'fecha_pago_entidad', $fecha_desde])
                                ->andFilterWhere(['<=', 'fecha_pago_entidad', $fecha_hasta]);
                        $table = $table->orderBy('id_pago DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 15,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_pago  DESC']);
                            $this->actionExcelPagoIncapacidad($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = Pagoincapacidadeps::find()
                             ->orderBy('id_pago DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 15,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelPagoIncapacidad($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('indexpagoincapacidad', [
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
   
   public function actionNuevo() {   
        $model = new FormIncapacidad();
               
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
         $empleado = Empleado::find()->all();
         $configuracionsalario = ConfiguracionSalario::find()->where(['=','estado', 1])->one();
        if ($model->load(Yii::$app->request->post())) {           
            if ($model->validate()) {
                 $configuracionincapacidad  = ConfiguracionIncapacidad::find()->where(['codigo_incapacidad'=>$model->codigo_incapacidad])->one();
                $codigo =  $configuracionincapacidad->codigo;
                //inicio de grabado
                if($empleado){
                    $empleado = Empleado::find()->where(['id_empleado'=>$model->id_empleado])->one();
                    $contrato = Contrato::find()->where(['=','id_empleado',$model->id_empleado])->andWhere(['=','contrato_activo',1])->one();
                    $diagnostico = DiagnosticoIncapacidad::find()->where(['=','id_codigo',$model->id_codigo])->one();
                    $fecha_contrato = date($contrato->fecha_inicio);
                    $fecha_inicio_inca = date($model->fecha_inicio);
                    $fecha_inicio_incapacidad = date($model->fecha_inicio);
                    $fecha_final_incapacidad = date($model->fecha_final);
                    //termina
                    if($fecha_contrato > $fecha_inicio_incapacidad){
                         Yii::$app->getSession()->setFlash('error', 'Error de digitalización, La fecha de inicio de la incapacidad No puede ser inferior a la fecha de inicio del contrato.');
                    }else{
                        if($fecha_inicio_inca > $fecha_final_incapacidad){
                             Yii::$app->getSession()->setFlash('error', 'Error de fechas, La fecha de inicio de la incapacidad No puede ser mayor que la fecha final de la licencia');          
                        }else{   
                            $licencia = \app\models\Licencia::find()->where(['=','id_empleado', $model->id_empleado])->andWhere([])->all();
                            $contLice = count($licencia);
                            if($contLice < 0){
                            }else{
                                foreach ($licencia as $val):
                                    $fecha_inicio_licencia = date($val->fecha_desde);
                                    $fecha_final_licencia = date($val->fecha_hasta);
                                    if($fecha_inicio_licencia == $fecha_inicio_incapacidad){
                                       Yii::$app->getSession()->setFlash('error', 'Error de fechas: la fecha de inicio de esta incapacidad conincide con al fecha de inicio de una licencia');          
                                    }
                                    if($fecha_final_incapacidad == $fecha_final_licencia){
                                         Yii::$app->getSession()->setFlash('error', 'Error de fechas: La fecha de final de esta incapacidad conincide con al fecha de fecha final de una licencia');          
                                    }
                                endforeach;  
                                $incapacidad_creadas = Incapacidad::find()->where(['=','id_empleado', $model->id_empleado])->all();
                                 foreach ($incapacidad_creadas as $inca):
                                    $fecha_inicio_inca = strtotime($inca->fecha_inicio);
                                    $fecha_final_inca = strtotime($inca->fecha_final);
                                    if($fecha_final_inca == $fecha_final_incapacidad){
                                         Yii::$app->getSession()->setFlash('error', 'Error de fechas: La fecha de final de esta incapacidad conincide con al fecha de fecha final de la incapacidad Nro: '. $inca->id_incapacidad.'');          
                                    }
                                    if ($fecha_inicio_incapacidad == $fecha_final_inca){
                                        Yii::$app->getSession()->setFlash('error', 'Error de fechas: La fecha de inicio de la incapacidad no puede ser igual a la fecha final de incapacidad Nro: '. $inca->id_incapacidad.''); 
                                    }
                                endforeach;  
                                $incapacidad = Incapacidad::find()->where(['=','id_empleado', $model->id_empleado])
                                                                ->andwhere(['=','fecha_inicio', $model->fecha_inicio])
                                                                ->andwhere(['=','fecha_final', $model->fecha_final])->one();
                                if($incapacidad){
                                    Yii::$app->getSession()->setFlash('error', 'Error de fechas: Existe una incapacidad creada con el mismo rango de fecha para este empleado.');          
                                }else{ 
                                    
                                        $table = new Incapacidad();
                                        $table->codigo_incapacidad = $model->codigo_incapacidad;
                                        $table->id_empleado = $model->id_empleado;
                                        $table->id_codigo = $model->id_codigo;
                                        $table->numero_incapacidad = $model->numero_incapacidad;
                                        $table->nombre_medico = $model->nombre_medico;
                                        $table->fecha_inicio = $model->fecha_inicio;
                                        $table->fecha_final = $model->fecha_final;
                                        $table->fecha_documento_fisico = $model->fecha_documento_fisico;
                                        $table->fecha_aplicacion = $model->fecha_aplicacion;
                                        $table->transcripcion = $model->transcripcion;
                                        $table->cobrar_administradora = $model->cobrar_administradora;
                                        $table->aplicar_adicional = $model->aplicar_adicional;
                                        if($table->aplicar_adicional){
                                            $table->estado_incapacidad_adicional = 1;
                                        }
                                        $table->pagar_empleado = $model->pagar_empleado;
                                        $table->prorroga = $model->prorroga;
                                        $table->fecha_inicio_empresa = $model->fecha_inicio;
                                        $table->fecha_final_empresa = $model->fecha_final;
                                        $table->fecha_inicio_administradora = $model->fecha_inicio;
                                        $table->fecha_final_administradora = $model->fecha_final;
                                        $table->usuariosistema = Yii::$app->user->identity->username;    
                                        $table->observacion = $model->observacion;
                                        $table->identificacion = $empleado->identificacion;
                                        $table->id_contrato = $contrato->id_contrato;
                                        $table->id_grupo_pago = $contrato->id_grupo_pago;
                                        $table->salario_mes_anterior = $contrato->salario;
                                        $table->salario = $contrato->salario;
                                        $table->id_entidad_salud = $contrato->id_entidad_salud;
                                        $table->codigo_diagnostico = $diagnostico->codigo_diagnostico;
                                        $total = strtotime($model->fecha_final ) - strtotime($model->fecha_inicio);
                                        $table->dias_incapacidad = round($total / 86400)+1; 
                                        $table->dias_acumulados =  $table->dias_incapacidad;
                                        $dias = round($total/ 86400)+1;
                                        
                                        //codigo que valide si el contrato es medio tiempo
                                       
                                       $contador = 0;
                                       $incapacidad = 0;
                                       $tiempo = TiempoServicio::findOne($contrato->id_tiempo);
                                        if($contrato->id_tiempo == 2){
                                            $contador = 1;
                                            $incapacidad = $tiempo->pago_incapacidad_general;
                                            $incapacidad_laboral= $tiempo->pago_incapacidad_laboral;
                                        }else{
                                            $incapacidad = $tiempo->pago_incapacidad_general;
                                            $incapacidad_laboral= $tiempo->pago_incapacidad_laboral;
                                        }
                                                

                                        $configuracio_empresa = \app\models\Matriculaempresa::findOne(1);
                                        $hora_norma_completa = 0; $hora_norma_medio = 0;
                                        $hora_norma_completa = $configuracio_empresa->horas_mensuales / 30;
                                        $hora_norma_medio = ($configuracio_empresa->horas_mensuales / 30) / 2;
                                        if($contador == 1){  //medio tiempo  
                                            $vlr_dia = (($contrato->salario / 30)* $tiempo->porcentaje)/100; 
                                            if($codigo == 1 ){
                                                if($incapacidad != 100){
                                                    if($dias > 2){
                                                        $table->vlr_liquidado = $dias * $vlr_dia;    
                                                        $table->vlr_hora = $vlr_dia / $hora_norma_completa;
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = $dias - 2;  
                                                        $table->vlr_cobro_administradora = round($table->dias_cobro_eps * $vlr_dia);
                                                        $table->vlr_saldo_administradora = $table->vlr_cobro_administradora;
                                                        $table->dias_administradora = $table->dias_cobro_eps;
                                                        $table->dias_empresa = $dias - $table->dias_cobro_eps;
                                                        $table->vlr_pago_empresa = round($table->dias_empresa * $vlr_dia);

                                                    }else{
                                                        $table->vlr_liquidado = round(($dias * $vlr_dia) * $incapacidad)/100; 
                                                        $table->vlr_hora =  ($vlr_dia *  $hora_norma_completa);
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = 0;  
                                                        $table->vlr_cobro_administradora = 0;
                                                        $table->vlr_saldo_administradora = 0;
                                                        $table->dias_administradora = 0;
                                                        $table->dias_empresa = $dias;
                                                        $table->vlr_pago_empresa = round(($dias * $vlr_dia) * $incapacidad)/100;
                                                    }
                                                }else{
                                                    if($dias > 2){
                                                        $table->vlr_liquidado = $dias * $vlr_dia;    
                                                        $table->vlr_hora = $vlr_dia / $hora_norma_completa;
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = round($dias - 2);  
                                                        $table->vlr_cobro_administradora = round($table->dias_cobro_eps * $vlr_dia);
                                                        $table->vlr_saldo_administradora = $table->vlr_cobro_administradora;
                                                        $table->dias_administradora = $table->dias_cobro_eps;
                                                        $table->dias_empresa = $dias - $table->dias_cobro_eps;
                                                        $table->vlr_pago_empresa = round($table->dias_empresa * $vlr_dia);

                                                    }else{
                                                       $table->vlr_liquidado = $dias * $vlr_dia; 
                                                        $table->vlr_hora =  $vlr_dia / $hora_norma_completa;
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = 0;  
                                                        $table->vlr_cobro_administradora = 0;
                                                        $table->vlr_saldo_administradora = 0;
                                                        $table->dias_administradora = 0;
                                                        $table->dias_empresa = $dias;
                                                        $table->vlr_pago_empresa = round($dias * $vlr_dia);
                                                    }
                                                }
                                            }//termina incapacidad general
                                            // codigo para calculo de incapacidades laborales.
                                            if($codigo == 2 ){
                                                if($incapacidad_laboral != 100){
                                                    $vlr_dia = ($contrato->salario / 30);  
                                                    if($dias > 1){
                                                       $vlr_dia = ($configuracionsalario->salario_minimo_actual/30);
                                                        $table->vlr_liquidado = $dias * $vlr_dia;    
                                                        $table->vlr_hora = $vlr_dia / $hora_norma_completa;
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = round($dias - 1);  
                                                        $table->vlr_cobro_administradora = round($table->dias_cobro_eps * $vlr_dia);
                                                        $table->vlr_saldo_administradora = $table->vlr_cobro_administradora;
                                                        $table->dias_administradora = $table->dias_cobro_eps;
                                                        $table->dias_empresa = $dias - $table->dias_cobro_eps;
                                                        $table->vlr_pago_empresa = round($table->dias_empresa * $vlr_dia);
                                                     }else{
                                                        $vlr_dia = ($contrato->salario/30);
                                                        $table->vlr_liquidado = round($dias * $vlr_dia); 
                                                        $table->vlr_hora = $vlr_dia / $hora_norma_completa;
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = 0;  
                                                        $table->vlr_cobro_administradora = 0;
                                                        $table->vlr_saldo_administradora = 0;
                                                        $table->dias_administradora = 0;
                                                        $table->dias_empresa = $dias;
                                                        $table->vlr_pago_empresa = round($dias * $vlr_dia);
                                                    }
                                                }else{
                                                    $vlr_dia = ($contrato->salario / 30); 
                                                    if($dias > 1){
                                                        $table->vlr_liquidado = $dias * $vlr_dia;    
                                                        $table->vlr_hora = $vlr_dia / $hora_norma_completa;
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = round($dias - 1);  
                                                        $table->vlr_cobro_administradora = round($table->dias_cobro_eps * $vlr_dia);
                                                        $table->vlr_saldo_administradora = $table->vlr_cobro_administradora;
                                                        $table->dias_administradora = $table->dias_cobro_eps;
                                                        $table->dias_empresa = $dias - $table->dias_cobro_eps;
                                                        $table->vlr_pago_empresa = round($table->dias_empresa * $vlr_dia);

                                                    }else{
                                                       $table->vlr_liquidado = $dias * $vlr_dia; 
                                                        $table->vlr_hora =  $vlr_dia / $hora_norma_completa;
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = 0;  
                                                        $table->vlr_cobro_administradora = 0;
                                                        $table->vlr_saldo_administradora = 0;
                                                        $table->dias_administradora = 0;
                                                        $table->dias_empresa = $dias;
                                                        $table->vlr_pago_empresa = round($dias * $vlr_dia);
                                                    }
                                                }    
                                            }  
                                        }else{ //tiempo completo  
                                            if($contrato->salario > $configuracionsalario->salario_incapacidad){
                                                $vlr_dia = (($configuracionsalario->salario_incapacidad /30)* $configuracionincapacidad->porcentaje)/100; 
                                            }else{
                                             $vlr_dia = ($configuracionsalario->salario_minimo_actual / 30);  
                                            }
                                            
                                            if($codigo == 1 ){//incapacidad general
                                                if($dias > 2){
                                                   $table->vlr_liquidado = $dias * $vlr_dia;    
                                                    $table->vlr_hora = $vlr_dia / $hora_norma_completa;
                                                    $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                    $table->dias_cobro_eps = round($dias - 2);  
                                                    $table->vlr_cobro_administradora = round($table->dias_cobro_eps * $vlr_dia);
                                                    $table->vlr_saldo_administradora = $table->vlr_cobro_administradora;
                                                    $table->dias_administradora = $table->dias_cobro_eps;
                                                    $table->dias_empresa = $dias - $table->dias_cobro_eps;
                                                    $table->vlr_pago_empresa = round($table->dias_empresa * $vlr_dia);
                                                    $table->ibc_total_incapacidad = round(($contrato->salario / 30) * $dias);

                                                }else{
                                                    $table->vlr_liquidado = round(($dias * $vlr_dia) * ($incapacidad))/100; 
                                                    $table->vlr_hora =  $vlr_dia / $hora_norma_completa;
                                                    $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                    $table->dias_cobro_eps = 0;  
                                                    $table->vlr_cobro_administradora = 0;
                                                    $table->vlr_saldo_administradora = 0;
                                                    $table->dias_administradora = 0;
                                                    $table->dias_empresa = $dias;
                                                    $table->vlr_pago_empresa = round(($dias * $vlr_dia) * $incapacidad)/100;
                                                    $table->ibc_total_incapacidad = round(($contrato->salario / 30) * $dias);
                                                }
                                             
                                            }//termina incapacidad general
                                            // codigo para calculo de incapacidades laborales.
                                            if($codigo == 2 ){
                                                $vlr_dia = ($contrato->salario / 30);  
                                                if($dias > 1){
                                                    $table->vlr_liquidado = $dias * $vlr_dia;    
                                                    $table->vlr_hora = $vlr_dia / $hora_norma_completa;
                                                    $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                    $table->dias_cobro_eps = round($dias - 1);  
                                                    $table->vlr_cobro_administradora = round($table->dias_cobro_eps * $vlr_dia);
                                                    $table->vlr_saldo_administradora = $table->vlr_cobro_administradora;
                                                    $table->dias_administradora = $table->dias_cobro_eps;
                                                    $table->dias_empresa = $dias - $table->dias_cobro_eps;
                                                    $table->vlr_pago_empresa = round($table->dias_empresa * $vlr_dia);
                                                    $table->ibc_total_incapacidad = round(($contrato->salario / 30) * $dias);
                                                 }else{
                                                    $table->vlr_liquidado = round($dias * $vlr_dia); 
                                                    $table->vlr_hora = $vlr_dia / $hora_norma_completa;
                                                    $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                    $table->dias_cobro_eps = 0;  
                                                    $table->vlr_cobro_administradora = 0;
                                                    $table->vlr_saldo_administradora = 0;
                                                    $table->dias_administradora = 0;
                                                    $table->dias_empresa = $dias;
                                                    $table->vlr_pago_empresa = round($dias * $vlr_dia);
                                                    $table->ibc_total_incapacidad = round(($contrato->salario / 30) * $dias);
                                                }
                                            }  
                                        } 
                                    }//termina calculo de incapacidades
                                   
                                    $table->save(false); 
                                    return $this->redirect(["incapacidad/index"]);
                                }    
                            }
                        }
                    }    
                }else {
                    Yii::$app->getSession()->setFlash('error', 'No existe el documento del empleado.');
                }
        }else{
           $model->getErrors();
        }
        return $this->render('form', [
                 'model' => $model,
                  ]);
    }
  
    /**
     * Displays a single Incapacidad model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    
    //nuevo pago de incapacidad
    
    public function actionNuevopagoincapacidad() {
        $model = new FormPagoIncapacidad();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {  
             if($model->validate()){
                 $table = new Pagoincapacidadeps();
                 if($table){
                     $table->id_entidad_salud = $model->id_entidad_salud;
                     $table->fecha_pago_entidad = $model->fecha_pago_entidad;
                     $table->idbanco = $model->idbanco;
                     $table->observacion = $model->observacion;
                     $table->usuariosistema = Yii::$app->user->identity->username;  
                    $table->save(false);
                    return $this->redirect(["incapacidad/indexpagoincapacidad"]);
                 }
                 
             }else{
                 $model->getErrors();
             }
        }
         return $this->render('formnuevopago', [
            'model' => $model,
        ]); 
    }
    
    //PROCESO DE LA VISTA
    public function actionView($id)
    {
       $seguimiento = SeguimientoIncapacidad::find()->where(['=','id_incapacidad',$id])->all();
       $registros = count($seguimiento);
        if(Yii::$app->request->post())
        {
            $intIndice = 0;
            if (isset($_POST["seleccion"])) {
                foreach ($_POST["seleccion"] as $intCodigo)
                {
                   $incapacidaddetalle = Incapacidad::findOne($intCodigo);                    
                    if(Incapacidad::deleteAll("id_incapacidad=:id_incapacidad", [":id_incapacidad" => $intCodigo]))
                    {                        
                    } 
                }
                 return $this->redirect(['incapacidad/view', 'id' => $id]);
            }
        }
        return $this->render('view', [
             'model' => $this->findModel($id),
            'seguimiento' => $seguimiento, 
             'registros' => $registros,
        ]);
    }
    
  ///VISTA DEL DETALLE DE PAGO
  public function actionViewdetallepago($id_pago){
      $model = Pagoincapacidadeps::findOne($id_pago);
      $detallepago = PagoincacidadepsDetalle::find()->where(['=','id_pago', $id_pago])->all();
       if (isset($_POST["actualizar_detalle"])) {
            $intIndice = 0; $saldo_actual = 0;
            foreach ($_POST["actualizar_detalle"] as $intCodigo) {                
                $table = PagoincacidadepsDetalle::findOne($intCodigo);
                $saldo_actual = $table->vlr_pago_administradora;
                $table->abono = $_POST["vlrabono"][$intIndice];
                $table->vlr_saldo = $saldo_actual - $table->abono;
                $table->save(false);
                $intIndice++;
            }
            $this->ActualizarSaldoPagoIncapacidad($id_pago);
            return $this->redirect(['viewdetallepago', 'id_pago' => $id_pago]);
        }
        return $this->render('viewdetallepago', [
            'model' => $model,
            'id_pago' => $id_pago,
            'detallepago' => $detallepago,
        ]);
  }  
  
  protected function ActualizarSaldoPagoIncapacidad($id_pago) {
      
      $detalle = PagoincacidadepsDetalle::find()->where(['=','id_pago', $id_pago])->all();
      $pago = Pagoincapacidadeps::findOne($id_pago);
      $total = 0;
      foreach ($detalle as $valor):
          $total += $valor->abono;
      endforeach;
      $pago->valor_pago = $total;
      $pago->save(false);
  }
  //NUEVO DETALLE DE INCAPACIDADES
   public function actionNuevodetalleincapacidad($id_eps, $id_pago)
    {
        $incapacidades = Incapacidad::find()->where(['>','vlr_saldo_administradora', 0])->andWhere(['=','id_entidad_salud', $id_eps])->orderBy('codigo_incapacidad')->all();
        $form = new FormMaquinaBuscar();
        $q = null;
        $mensaje = '';
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                if ($q){
                    $incapacidades = Incapacidad::find()
                            ->where(['=','identificacion',$q])
                            ->orwhere(['like','numero_incapacidad',$q])
                            ->orderBy('codigo_incapacidad asc')
                            ->all();
                }               
            } else {
                $form->getErrors();
            }                    
        } else {
            $incapacidades = Incapacidad::find()->where(['>','vlr_saldo_administradora', 0])->andWhere(['=','id_entidad_salud', $id_eps])->orderBy('codigo_incapacidad')->all();
        }
        if (isset($_POST["id_incapacidad"])) {
                $intIndice = 0;
                foreach ($_POST["id_incapacidad"] as $intCodigo) {
                    $table = new PagoincacidadepsDetalle();
                    $incapacidad = Incapacidad::find()->where(['id_incapacidad' => $intCodigo])->one();
                    $detalles = PagoincacidadepsDetalle::find()
                        ->where(['=', 'id_pago', $id_pago])
                        ->andWhere(['=', 'id_incapacidad', $incapacidad->id_incapacidad])
                        ->all();
                    $reg = count($detalles);
                    if ($reg == 0) {
                        $table->id_pago = $id_pago;
                        $table->id_incapacidad = $incapacidad->id_incapacidad;
                        $table->numero = $incapacidad->numero_incapacidad;
                        $table->codigo_incapacidad = $incapacidad->codigo_incapacidad;
                        $table->vlr_pago_administradora = $incapacidad->vlr_saldo_administradora;
                        $table->abono = $incapacidad->vlr_saldo_administradora;
                        $table->vlr_saldo = 0;
                        $table->insert(); 
                    }    
                }
                 $this->ActualizarSaldoPagoIncapacidad($id_pago);
              $this->redirect(["incapacidad/viewdetallepago", 'id_pago' => $id_pago]);
            }else{
                
            }
        return $this->render('_formlistadoincapacidad', [
            'incapacidades' => $incapacidades,            
            'mensaje' => $mensaje,
            'id_pago' => $id_pago,
            'id_eps' => $id_eps,
            'form' => $form,

        ]);
    }
  ///PROCESO QUE AUTORIZA
    public function actionAutorizado($id_pago) {
        $model = Pagoincapacidadeps::findOne($id_pago);
        $detalle = PagoincacidadepsDetalle::find()->where(['=','id_pago', $id_pago])->one();
        if (!$detalle){
            $this->redirect(["incapacidad/viewdetallepago", 'id_pago' => $id_pago]);
                Yii::$app->getSession()->setFlash('error', 'No se puede autorizar porque no existen detalles en el proceso.');
        }else{        
            if($model->nro_pago > 0){
                $this->redirect(["incapacidad/viewdetallepago", 'id_pago' => $id_pago]);
                Yii::$app->getSession()->setFlash('error', 'No se puede desautorizar porque ya esta generado el Numero de pago.');          
            }else{
                if ($model->autorizado == 0) {                        
                   $model->autorizado = 1;            
                   $model->update();
                   $this->redirect(["incapacidad/viewdetallepago", 'id_pago' => $id_pago]);  

                } else{
                    $model->autorizado = 0;
                    $model->update();
                    $this->redirect(["incapacidad/viewdetallepago", 'id_pago' => $id_pago]);  
                }
            }    
        }
        
    }
    // generar consecutivo
    
    public function actionGenerarconsecutivo($id_pago)
    {
        $model = Pagoincapacidadeps::findOne($id_pago);
        $mensaje = "";
        $pago = Pagoincapacidadeps::findOne($id_pago);
        if ($pago->nro_pago == 0){
                $consecutivo = Consecutivo::findOne(13);// PAGO DE INCAPACIDADES
                $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
                $pago->nro_pago = $consecutivo->consecutivo;
                $pago->save(false);
                $consecutivo->save(false);
                $this->SaldoIncapacidad($id_pago);
                $this->redirect(["incapacidad/viewdetallepago",'id_pago' => $id_pago]);
        }else{
                Yii::$app->getSession()->setFlash('error', 'El numero de pago ya fue generado para este proceso');
                $this->redirect(["incapacidad/viewdetallepago",'id_pago' => $id_pago]);
        }
    }
    
    //para actualizar saldo en la incapacidad
    
    //proceso para actualizar saldos en incapacidad
    protected function SaldoIncapacidad($id_pago) {
        $detalle = PagoincacidadepsDetalle::find()->where(['=','id_pago', $id_pago])->all();
        foreach ($detalle as $valor):
            $dato = 0;
            $incapacidad = Incapacidad::findOne($valor->id_incapacidad);
            if ($incapacidad){
                $incapacidad->vlr_saldo_administradora -= $valor->abono;
                $incapacidad->save(false);
            }
        endforeach;
    }
    //ELIMINAR DETALLES DE PAGO
    
     public function actionEliminardetalle($id_pago,$id_detalle)
    {                                
        $detalle = PagoincacidadepsDetalle::findOne($id_detalle);
        $detalle->delete();
        $this->ActualizarSaldoPagoIncapacidad($id_pago);
        $this->redirect(["viewdetallepago",'id_pago' => $id_pago]);        
    }
    
  // crear nuevo seguimiento a la incapacidad
    public function actionNuevoseguimiento($id_incapacidad)
    {
        $model = new FormIncapacidadSeguimiento();
        $incapacidades = Incapacidad::find()->all();         
        if ($model->load(Yii::$app->request->post())) {
            $incapacidad = Incapacidad::find()->where(['=','id_incapacidad',$model->id_incapacidad])->one();
            if (!$incapacidad){
                $table = new SeguimientoIncapacidad;
                $table->id_incapacidad = $id_incapacidad;
                $table->nota = $model->nota;
                $table->usuariosistema = Yii::$app->user->identity->username;   
                $table->save(false);
                $this->redirect(["incapacidad/view", 'id' => $id_incapacidad]);
            }else{                
                Yii::$app->getSession()->setFlash('error', 'El Número de la incapacidad no existe!');
            }
        }
        return $this->render('_formnuevoseguimiento', [
            'model' => $model,
          // '$incapacidades' => ArrayHelper::map($incapacidades, "id_incapacidad", ""),
            'id' => $id_incapacidad
        ]);
    }
    
  //editar el segumiento a la incapacidad
    public function actionEditarseguimiento($id_seguimiento) {
       
        $model = new FormIncapacidadSeguimientoEditar;
        $incapacidad = Incapacidad::find()->all();        
        $seguimiento = SeguimientoIncapacidad::findOne($id_seguimiento);
       if ($model->load(Yii::$app->request->post())) {                        
            $seguimiento->nota = $model->nota;
            $seguimiento->save(false);                                      
            return $this->redirect(['incapacidad/view','id' => $seguimiento->id_incapacidad]);
        }
        if (Yii::$app->request->get("id_seguimiento")) {
            $table = SeguimientoIncapacidad::find()->where(['id_seguimiento' => $id_seguimiento])->one();
            if ($table) {
                $model->nota = $table->nota;
            }    
        }
        return $this->render('_formeditarseguimiento', [
            'model' => $model,
            'seguimiento' => $seguimiento,
           ]);         
    } 
    
    
   public function actionUpdate($id)
    {
        $model = new FormIncapacidad();
      
       if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
         
        if ($model->load(Yii::$app->request->post())) {  
            if($model->validate()){
                
                $empleado = Empleado::find()->where(['id_empleado'=>$model->id_empleado])->one();
                $contrato = Contrato::find()->where(['=','id_empleado',$model->id_empleado])->andWhere(['=','contrato_activo',1])->one();
                $diagnostico = DiagnosticoIncapacidad::find()->where(['=','id_codigo',$model->id_codigo])->one();
                $detalle_nomina = ProgramacionNominaDetalle::find()->where(['=','id_incapacidad', $id])->one();
                $configuracionsalario = ConfiguracionSalario::find()->where(['=','estado', 1])->one();
                $configuracionincapacidad  = ConfiguracionIncapacidad::find()->where(['codigo_incapacidad'=>$model->codigo_incapacidad])->one();
                $codigo =  $configuracionincapacidad->codigo;
                $fecha_contrato = date($contrato->fecha_inicio);
                $fecha_inicio_inca = date($model->fecha_inicio);
                $fecha_inicio_incapacidad = date($model->fecha_inicio);
                $fecha_final_incapacidad = date($model->fecha_final);
                if($detalle_nomina){
                   Yii::$app->getSession()->setFlash('error', 'Error: la incapacidad no se puede modificar porque esta relacionada en el proceso de nomina!');           
                }else{
                    if($fecha_contrato > $fecha_inicio_incapacidad){
                         Yii::$app->getSession()->setFlash('error', 'Error de digitalización, La fecha de inicio de la licencia No puede ser inferior a la fecha de inicio del contrato.');
                    }else{
                        if($fecha_inicio_inca > $fecha_final_incapacidad){
                             Yii::$app->getSession()->setFlash('error', 'Error de fechas, La fecha de inicio de la incapacidad No puede ser mayor que la fecha final de la licencia');          
                        }else{  
                            $licencia = \app\models\Licencia::find()->where(['=','id_empleado', $model->id_empleado])->all();
                            $contLice = count($licencia);
                            if($contLice < 0){
                            }else{
                                 foreach ($licencia as $val):
                                    $fecha_inicio_licencia = date($val->fecha_desde);
                                    $fecha_final_licencia = date($val->fecha_hasta);
                                    if($fecha_inicio_licencia == ($fecha_inicio_incapacidad)){
                                       Yii::$app->getSession()->setFlash('error', 'Error de fechas: la fecha de inicio de esta incapacidad conincide con al fecha de inicio de una licencia');          
                                    }
                                    if($fecha_final_incapacidad == $fecha_final_licencia){
                                         Yii::$app->getSession()->setFlash('error', 'Error de fechas: La fecha de final de esta incapacidad conincide con al fecha de fecha final de una licencia');          
                                    }
                                endforeach;  
                                    $table = Incapacidad::find()->where(['id_incapacidad'=>$id])->one();
                                    if ($table) { 
                                        $table->codigo_incapacidad = $model->codigo_incapacidad;
                                        $table->id_empleado = $model->id_empleado;
                                        $table->id_codigo = $model->id_codigo;
                                        $table->numero_incapacidad = $model->numero_incapacidad;
                                        $table->nombre_medico = $model->nombre_medico;
                                        $table->fecha_inicio = $model->fecha_inicio;
                                        $table->fecha_final = $model->fecha_final;
                                        $table->fecha_documento_fisico = $model->fecha_documento_fisico;
                                        $table->fecha_aplicacion = $model->fecha_aplicacion;
                                        $table->transcripcion = $model->transcripcion;
                                        $table->cobrar_administradora = $model->cobrar_administradora;
                                        $table->aplicar_adicional = $model->aplicar_adicional;
                                        $table->pagar_empleado = $model->pagar_empleado;
                                        $table->prorroga = $model->prorroga;
                                        $table->fecha_inicio_empresa = $model->fecha_inicio;
                                        $table->fecha_final_empresa = $model->fecha_final;
                                        $table->fecha_inicio_administradora = $model->fecha_inicio;
                                        $table->fecha_final_administradora = $model->fecha_final;
                                        $table->usuariosistema = Yii::$app->user->identity->username;    
                                        $table->observacion = $model->observacion;
                                        $table->identificacion = $empleado->identificacion;
                                        $table->id_contrato = $contrato->id_contrato;
                                        $table->id_grupo_pago = $contrato->id_grupo_pago;
                                        $table->salario_mes_anterior = $contrato->salario;
                                        $table->salario = $contrato->salario;
                                        $table->id_entidad_salud = $contrato->id_entidad_salud;
                                        $table->codigo_diagnostico = $diagnostico->codigo_diagnostico;
                                        $total = strtotime($model->fecha_final ) - strtotime($model->fecha_inicio);
                                        $table->dias_incapacidad = round($total / 86400)+1; 
                                        $table->dias_acumulados =  $table->dias_incapacidad;
                                        $dias = round($total/ 86400)+1;
                                        if($table->aplicar_adicional){
                                            $table->estado_incapacidad_adicional = 1;
                                        }else{
                                            $table->estado_incapacidad_adicional = 0;
                                        }
                                        $tiempo = TiempoServicio::findOne($contrato->id_tiempo); 
                                        $contador = 0;
                                        $incapacidad = 0;
                                       
                                        if($contrato->id_tiempo == 2){
                                            $contador = 1;
                                            $incapacidad = $tiempo->pago_incapacidad_general;
                                            $incapacidad_laboral= $tiempo->pago_incapacidad_laboral;
                                        }else{
                                            $incapacidad = $tiempo->pago_incapacidad_general;
                                            $incapacidad_laboral= $tiempo->pago_incapacidad_laboral;
                                        }   

                                        $configuracio_empresa = \app\models\Matriculaempresa::findOne(1);
                                        $hora_norma_completa = 0; $hora_norma_medio = 0;
                                        $hora_norma_completa = $configuracio_empresa->horas_mensuales / 30;
                                        $hora_norma_medio = ($configuracio_empresa->horas_mensuales / 30) / 2;
                                        if($contador == 1){                                       
                                            $vlr_dia = ($configuracionsalario->salario_minimo_actual/30); 
                                            //incapacidad general
                                            if($codigo == 1 ){
                                                if($incapacidad != 100){
                                                    if($dias > 2){
                                                        $table->vlr_liquidado = $dias * $vlr_dia;    
                                                       $table->vlr_hora = $vlr_dia / $hora_norma_medio;
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = round($dias - 2);  
                                                        $table->vlr_cobro_administradora = round($table->dias_cobro_eps * $vlr_dia);
                                                        $table->vlr_saldo_administradora = $table->vlr_cobro_administradora;
                                                        $table->dias_administradora = $table->dias_cobro_eps;
                                                        $table->dias_empresa = $dias - $table->dias_cobro_eps;
                                                        $table->vlr_pago_empresa = round($table->dias_empresa * $vlr_dia);

                                                    }else{
                                                        $table->vlr_liquidado = round(($dias * $vlr_dia) * $incapacidad)/100; 
                                                        $table->vlr_hora =  ($vlr_dia * $hora_norma_medio);
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = 0;  
                                                        $table->vlr_cobro_administradora = 0;
                                                        $table->vlr_saldo_administradora = 0;
                                                        $table->dias_administradora = 0;
                                                        $table->dias_empresa = $dias;
                                                        $table->vlr_pago_empresa = round(($dias * $vlr_dia) * $incapacidad)/100;
                                                    }
                                                }else{
                                                    if($dias > 2){
                                                        $table->vlr_liquidado = $dias * $vlr_dia;    
                                                        $table->vlr_hora = $vlr_dia / $hora_norma_medio;
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = round($dias - 2);  
                                                        $table->vlr_cobro_administradora = round($table->dias_cobro_eps * $vlr_dia);
                                                        $table->vlr_saldo_administradora = $table->vlr_cobro_administradora;
                                                        $table->dias_administradora = $table->dias_cobro_eps;
                                                        $table->dias_empresa = $dias - $table->dias_cobro_eps;
                                                        $table->vlr_pago_empresa = round($table->dias_empresa * $vlr_dia);

                                                    }else{
                                                        $table->vlr_liquidado = $dias * $vlr_dia; 
                                                        $table->vlr_hora =  $vlr_dia / $hora_norma_medio;
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = 0;  
                                                        $table->vlr_cobro_administradora = 0;
                                                        $table->vlr_saldo_administradora = 0;
                                                        $table->dias_administradora = 0;
                                                        $table->dias_empresa = $dias;
                                                        $table->vlr_pago_empresa = round($dias * $vlr_dia);
                                                    }
                                                }
                                            }//termina incapacidad general
                                            // codigo para calculo de incapacidades laborales.
                                            if($codigo == 2 ){
                                                if($incapacidad_laboral != 100){
                                                    if($dias > 1){
                                                       $vlr_dia = ($configuracionsalario->salario_minimo_actual/30);
                                                        $table->vlr_liquidado = $dias * $vlr_dia;    
                                                        $table->vlr_hora = $vlr_dia / $hora_norma_completa;
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = round($dias - 1);  
                                                        $table->vlr_cobro_administradora = round($table->dias_cobro_eps * $vlr_dia);
                                                        $table->vlr_saldo_administradora = $table->vlr_cobro_administradora;
                                                        $table->dias_administradora = $table->dias_cobro_eps;
                                                        $table->dias_empresa = $dias - $table->dias_cobro_eps;
                                                        $table->vlr_pago_empresa = round($table->dias_empresa * $vlr_dia);
                                                     }else{
                                                        $vlr_dia = ($contrato->salario/30);
                                                        $table->vlr_liquidado = round($dias * $vlr_dia); 
                                                        $table->vlr_hora = $vlr_dia / $hora_norma_medio;
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = 0;  
                                                        $table->vlr_cobro_administradora = 0;
                                                        $table->vlr_saldo_administradora = 0;
                                                        $table->dias_administradora = 0;
                                                        $table->dias_empresa = $dias;
                                                        $table->vlr_pago_empresa = round($dias * $vlr_dia);
                                                    }
                                                }else{
                                                     $vlr_dia = ($configuracionsalario->salario_minimo_actual/30);
                                                    if($dias > 1){
                                                        $table->vlr_liquidado = $dias * $vlr_dia;    
                                                        $table->vlr_hora = $vlr_dia / $hora_norma_completa;
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = round($dias - 1);  
                                                        $table->vlr_cobro_administradora = round($table->dias_cobro_eps * $vlr_dia);
                                                        $table->vlr_saldo_administradora = $table->vlr_cobro_administradora;
                                                        $table->dias_administradora = $table->dias_cobro_eps;
                                                        $table->dias_empresa = $dias - $table->dias_cobro_eps;
                                                        $table->vlr_pago_empresa = round($table->dias_empresa * $vlr_dia);

                                                    }else{
                                                       $table->vlr_liquidado = $dias * $vlr_dia; 
                                                        $table->vlr_hora =  $vlr_dia / $hora_norma_completa;
                                                        $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                        $table->dias_cobro_eps = 0;  
                                                        $table->vlr_cobro_administradora = 0;
                                                        $table->vlr_saldo_administradora = 0;
                                                        $table->dias_administradora = 0;
                                                        $table->dias_empresa = $dias;
                                                        $table->vlr_pago_empresa = round($dias * $vlr_dia);
                                                    }
                                                }    
                                            }  
                                        }else{   
                                            if($contrato->salario <= $configuracionsalario->salario_incapacidad){
                                                $vlr_dia = (($configuracionsalario->salario_incapacidad/30)* $configuracionincapacidad->porcentaje)/100; 
                                            }else{
                                                $vlr_dia = (($contrato->salario/30)* $configuracionincapacidad->porcentaje)/100;  
                                            }
                                            if($codigo == 1 ){
                                                if($dias > 2){
                                                   $table->vlr_liquidado = $dias * $vlr_dia;    
                                                    $table->vlr_hora = $vlr_dia / $hora_norma_completa;
                                                    $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                    $table->dias_cobro_eps = round($dias - 2);  
                                                    $table->vlr_cobro_administradora = round($table->dias_cobro_eps * $vlr_dia);
                                                    $table->vlr_saldo_administradora = $table->vlr_cobro_administradora;
                                                    $table->dias_administradora = $table->dias_cobro_eps;
                                                    $table->dias_empresa = $dias - $table->dias_cobro_eps;
                                                    $table->vlr_pago_empresa = round($table->dias_empresa * $vlr_dia);
                                                    $table->ibc_total_incapacidad = round(($contrato->salario / 30)* ($dias));

                                                }else{
                                                    $table->vlr_liquidado = round(($dias * $vlr_dia) * $incapacidad)/100; 
                                                    $table->vlr_hora =  $vlr_dia / $hora_norma_completa;
                                                    $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                    $table->dias_cobro_eps = 0;  
                                                    $table->vlr_cobro_administradora = 0;
                                                    $table->vlr_saldo_administradora = 0;
                                                    $table->dias_administradora = 0;
                                                    $table->dias_empresa = $dias;
                                                    $table->vlr_pago_empresa = round(($dias * $vlr_dia) * $incapacidad)/100;
                                                    $table->ibc_total_incapacidad = round(($contrato->salario / 30)* ($dias));
                                                }
                                             
                                            }//termina incapacidad general
                                            // codigo para calculo de incapacidades laborales.
                                            if($codigo == 2 ){
                                                if($dias > 1){
                                                    $vlr_dia = ($configuracionsalario->salario_minimo_actual/30);
                                                    $table->vlr_liquidado = $dias * $vlr_dia;    
                                                    $table->vlr_hora = $vlr_dia / $hora_norma_completa;
                                                    $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                    $table->dias_cobro_eps = round($dias - 1);  
                                                    $table->vlr_cobro_administradora = round($table->dias_cobro_eps * $vlr_dia);
                                                    $table->vlr_saldo_administradora = $table->vlr_cobro_administradora;
                                                    $table->dias_administradora = $table->dias_cobro_eps;
                                                    $table->dias_empresa = $dias - $table->dias_cobro_eps;
                                                    $table->vlr_pago_empresa = round($table->dias_empresa * $vlr_dia);
                                                    $table->ibc_total_incapacidad = round(($contrato->salario / 30)* ($dias));
                                                 }else{
                                                    $table->vlr_liquidado = round($dias * $vlr_dia); 
                                                    $table->vlr_hora = $vlr_dia / $hora_norma_medio;
                                                    $table->porcentaje_pago =  $configuracionincapacidad->porcentaje;
                                                    $table->dias_cobro_eps = 0;  
                                                    $table->vlr_cobro_administradora = 0;
                                                    $table->vlr_saldo_administradora = 0;
                                                    $table->dias_administradora = 0;
                                                    $table->dias_empresa = $dias;
                                                    $table->vlr_pago_empresa = round($dias * $vlr_dia);
                                                    $table->ibc_total_incapacidad = round(($contrato->salario / 30)* ($dias));
                                                }
                                            }  
                                        } 
                                       $table->save(false); 
                                       return $this->redirect(['index']);                    
                                    }//termina el ciclo de la entrada de la table
                            }//valida si este empleado tiene licencias    
                        }//valida que la fecha de inicio se mayor que la fecha final de la incapacidad    
                    }//valida que no ingresen incapacidades menores al ingreso del contrato    
                }//termina el ciclo que valide si esta relacionado en nomina.
            }else{
                $model->getErrors();
            }  
            //termina el validate
                       
        }
        if (Yii::$app->request->get("id")) {
              
                 $table = Incapacidad::find()->where(['id_incapacidad' => $id])->one();            
                if ($table) {     
                    $model->codigo_incapacidad = $table->codigo_incapacidad;
                    $model->id_empleado = $table->id_empleado;
                    $model->id_codigo = $table->id_codigo;
                    $model->numero_incapacidad = $table->numero_incapacidad;
                    $model->nombre_medico = $table->nombre_medico;
                    $model->fecha_inicio = $table->fecha_inicio;
                    $model->fecha_final =  $table->fecha_final;
                    $model->fecha_documento_fisico = $table->fecha_documento_fisico;
                    $model->fecha_aplicacion = $table->fecha_aplicacion;
                    $model->observacion = $table->observacion;
                    $model->transcripcion = $table->transcripcion;
                    $model->pagar_empleado = $table->pagar_empleado;
                    $model->cobrar_administradora = $table->cobrar_administradora;
                    $model->aplicar_adicional = $table->aplicar_adicional;
                    $model->prorroga = $table->prorroga;
                }else{
                     return $this->redirect(['index']);
                }
        } else {
                return $this->redirect(['index']);    
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    
   //editar registro de pago
   public function actionEditarpagoincapacidad($id_pago) {
        $model = new FormPagoIncapacidad();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {  
            if($model->validate()){
                $table = Pagoincapacidadeps::find()->where(['id_pago'=>$id_pago])->one();
                if ($table){
                    $table->id_entidad_salud = $model->id_entidad_salud;
                    $table->idbanco = $model->idbanco;
                    $table->fecha_pago_entidad = $model->fecha_pago_entidad;
                    $table->observacion = $model->observacion;
                    $table->save(false);
                     return $this->redirect(['indexpagoincapacidad']);  
                } 
            }else{
                $model->getErrors();
            }
        }
         if (Yii::$app->request->get("id_pago")) {
             $table = Pagoincapacidadeps::findOne($id_pago);  
             if($table){
                 $model->id_entidad_salud = $table->id_entidad_salud;
                 $model->fecha_pago_entidad = $table->fecha_pago_entidad;
                 $model->idbanco = $table->idbanco;
                 $model->observacion = $table->observacion;
             }else{
                 return $this->redirect(['indexpagoincapacidad']);
             }
         }
         return $this->render('formnuevopago', [
            'model' => $model,
            'id_pago' => $id_pago,
            
        ]);
   }
    
   public function actionEliminar($id) {
        if (Yii::$app->request->post()) {
            $incapacidad = Incapacidad::findOne($id);
            if ((int) $id) {
                try {
                    Incapacidad::deleteAll("id_incapacidad=:id_incapacidad", [":id_incapacidad" => $id]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    $this->redirect(["incapacidad/index"]);
                } catch (IntegrityException $e) {
                    $this->redirect(["incapacidad/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar la incapacidad Nro: ' . $incapacidad->numero_incapacidad . ', tiene registros asociados en otros procesos');
                } catch (\Exception $e) {

                    $this->redirect(["incapacidad/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar la incapacidad Nro:  ' . $incapacidad->numero_incapacidad . ', tiene registros asociados en otros procesos');
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el registros, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("incapacidad/index") . "'>";
            }
        } else {
            return $this->redirect(["incapacidad/index"]);
        }
    }
     
    protected function findModel($id)
    {
        if (($model = Incapacidad::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);                               
        $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A1', 'Id')
                    ->setCellValue('B1', 'Documento')
                    ->setCellValue('C1', 'Empleado')
                    ->setCellValue('D1', 'Grupo pago')
                    ->setCellValue('E1', 'Tipo incapacidad')
                    ->setCellValue('F1', 'Numero incapacidad')
                    ->setCellValue('G1', 'Cod. Diagnostico')                    
                    ->setCellValue('H1', 'Nombre medico')
                    ->setCellValue('I1', 'F. Inicio')
                    ->setCellValue('J1', 'F. Final')
                    ->setCellValue('K1', 'Salario')
                    ->setCellValue('L1', 'Salario anterior')
                    ->setCellValue('M1', 'Dias incapacidad')
                    ->setCellValue('N1', 'Dia empresa')
                    ->setCellValue('O1', 'Dia administradora')
                    ->setCellValue('P1', 'Vlr liquidado')
                    ->setCellValue('Q1', 'Vlr cobro administradora')
                    ->setCellValue('R1', 'Pagar empleado')
                    ->setCellValue('S1', 'Prorroga')
                    ->setCellValue('T1', 'Transcripcion')
                    ->setCellValue('U1', 'Cobrar administradora')
                    ->setCellValue('V1', 'Pago empresa')
                    ->setCellValue('W1', 'Vlr hora')
                    ->setCellValue('X1', 'Usuario')
                    ->setCellValue('Y1', 'F. Proceso')
                    ->setCellValue('Z1', 'Observacion');
                   
        $i = 2  ;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A' . $i, $val->id_incapacidad)
                    ->setCellValue('B' . $i, $val->identificacion)
                    ->setCellValue('C' . $i, $val->empleado->nombrecorto)
                    ->setCellValue('D' . $i, $val->grupoPago->grupo_pago)
                    ->setCellValue('E' . $i, $val->codigoIncapacidad->nombre)
                    ->setCellValue('F' . $i, $val->numero_incapacidad)                    
                    ->setCellValue('G' . $i, $val->codigo_diagnostico)
                    ->setCellValue('H' . $i, $val->nombre_medico)
                    ->setCellValue('I' . $i, $val->fecha_inicio)
                    ->setCellValue('J' . $i, $val->fecha_final)
                    ->setCellValue('K' . $i, $val->salario)
                    ->setCellValue('L' . $i, $val->salario_mes_anterior)
                    ->setCellValue('M' . $i, $val->dias_incapacidad)
                    ->setCellValue('N' . $i, $val->dias_empresa)
                    ->setCellValue('O' . $i, $val->dias_administradora)
                    ->setCellValue('P' . $i, $val->vlr_liquidado)
                    ->setCellValue('Q' . $i, $val->vlr_cobro_administradora)
                    ->setCellValue('R' . $i, $val->pagarempleado)
                    ->setCellValue('S' . $i, $val->prorrogaIncapacidad)
                    ->setCellValue('T' . $i, $val->transcripcionincapacidad)
                    ->setCellValue('U' . $i, $val->vlr_saldo_administradora)
                    ->setCellValue('V' . $i, $val->vlr_pago_empresa)
                    ->setCellValue('W' . $i, $val->vlr_hora)
                    ->setCellValue('X' . $i, $val->usuariosistema)
                    ->setCellValue('Y' . $i, $val->fecha_creacion)
                    ->setCellValue('Z' . $i, $val->observacion);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Incapacidades');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Incapacidades.xlsx"');
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
    
     public function actionExcelPagoIncapacidad($tableexcel) {                
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
        $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'EPS')
                    ->setCellValue('C1', 'FECHA PAGO')
                    ->setCellValue('D1', 'FECHA REGISTRO')
                    ->setCellValue('E1', 'BANCO')
                    ->setCellValue('F1', 'VR. PAGO')
                    ->setCellValue('G1', 'USUARIO')                    
                    ->setCellValue('H1', 'AUTORIZADO')
                    ->setCellValue('I1', 'NRO PAGO')
                    ->setCellValue('J1', 'OBSERVACION');
                  
                   
        $i = 2  ;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A' . $i, $val->id_pago)
                    ->setCellValue('B' . $i, $val->entidadSalud->entidad)
                    ->setCellValue('C' . $i, $val->fecha_pago_entidad)
                    ->setCellValue('D' . $i, $val->fecha_registro)
                    ->setCellValue('E' . $i, $val->banco->entidad)
                    ->setCellValue('F' . $i, $val->valor_pago)                    
                    ->setCellValue('G' . $i, $val->usuariosistema)
                    ->setCellValue('H' . $i, $val->autorizadoPago)
                    ->setCellValue('I' . $i, $val->nro_pago)
                    ->setCellValue('J' . $i, $val->observacion);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Pagoincapacidades');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="PagoIncapacidades.xlsx"');
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
