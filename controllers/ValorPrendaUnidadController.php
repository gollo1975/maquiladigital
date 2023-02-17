<?php

namespace app\controllers;

use app\models\ValorPrendaUnidad;
use app\models\ValorPrendaUnidadSearch;
use app\models\UsuarioDetalle;
use app\models\Ordenproduccion;
use app\models\ValorPrendaUnidadDetalles;
use app\models\Operarios;
use app\models\FormFiltroValorPrenda;
use app\models\FormFiltroResumePagoPrenda;
//clases
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

/**
 * ValorPrendaUnidadController implements the CRUD actions for ValorPrendaUnidad model.
 */
class ValorPrendaUnidadController extends Controller
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
     * Lists all ValorPrendaUnidad models.
     * @return mixed
     */
   public function actionIndex() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 106])->all()) {
                $form = new FormFiltroValorPrenda();
                $idtipo = null;
                $idordenproduccion = null;
                $estado_valor = null;
                $cerrar_pago = null;
                $autorizado = null;
                $planta = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $idtipo = Html::encode($form->idtipo);
                        $idordenproduccion = Html::encode($form->idordenproduccion);
                        $estado_valor = Html::encode($form->estado_valor);
                        $cerrar_pago = Html::encode($form->cerrar_pago);
                        $autorizado = Html::encode($form->autorizado);
                        $planta = Html::encode($form->planta);
                        $table = ValorPrendaUnidad::find()
                                ->andFilterWhere(['=', 'idtipo', $idtipo])
                                ->andFilterWhere(['=', 'idordenproduccion', $idordenproduccion])
                                ->andFilterWhere(['=', 'estado_valor', $estado_valor])
                                ->andFilterWhere(['=', 'cerrar_pago', $cerrar_pago])
                                ->andFilterWhere(['=', 'id_planta', $planta])
                                ->andFilterWhere(['=', 'autorizado', $autorizado]);
                        $table = $table->orderBy('id_valor DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 25,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_valor  DESC']);
                            $this->actionExcelconsultaValorPrenda($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = ValorPrendaUnidad::find()
                             ->orderBy('id_valor DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 25,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelconsultaValorPrenda($tableexcel);
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
    
   //index de consulta o pago
    public function actionIndexsoporte() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 107])->all()) {
                $form = new FormFiltroResumePagoPrenda();
                $id_operario = null;
                $idordenproduccion = null;
                $operacion = null;
                $dia_pago = '';
                $fecha_corte = '';
                $registro = NULL;
                $validar_eficiencia = 0;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $validar_eficiencia = Html::encode($form->validar_eficiencia);
                        $id_operario = Html::encode($form->id_operario);
                        $idordenproduccion = Html::encode($form->idordenproduccion);
                        $operacion = Html::encode($form->operacion);
                        $dia_pago = Html::encode($form->dia_pago);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $registro = Html::encode($form->exportado);
                        $table = ValorPrendaUnidadDetalles::find()
                                ->andFilterWhere(['=', 'id_operario', $id_operario])
                                ->andFilterWhere(['=', 'idordenproduccion', $idordenproduccion])
                                ->andFilterWhere(['=', 'operacion', $operacion])
                                ->andFilterWhere(['>=', 'dia_pago', $dia_pago])
                                ->andFilterWhere(['<=', 'dia_pago', $fecha_corte])
                                ->andFilterWhere(['=', 'exportado', $registro]);
                        $table = $table->orderBy('consecutivo DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 120,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['consecutivo  DESC']);
                            $this->actionExcelResumeValorPrenda($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $validar_eficiencia = Html::encode($form->validar_eficiencia);
                    $table = ValorPrendaUnidadDetalles::find()
                             ->orderBy('consecutivo DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 120,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelResumeValorPrenda($tableexcel);
                    }
                }
                 if(isset($_POST['cerrar_abrir'])){                            
                        if(isset($_REQUEST['consecutivo'])){                            
                            $intIndice = 0;
                            foreach ($_POST["consecutivo"] as $intCodigo) {
                                if ($_POST["consecutivo"][$intIndice]) {                                
                                   $codigo = $_POST["consecutivo"][$intIndice];
                                   $this->actionCerrarAbrirRegistro($codigo);
                                }
                                $intIndice++;
                            }
                        }
                       $this->redirect(["valor-prenda-unidad/indexsoporte"]);
                 }
                $to = $count->count();
                return $this->render('indexsoporte', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
                            'validar_eficiencia' => $validar_eficiencia,
                            'dia_pago' =>$dia_pago,
                            'fecha_corte' => $fecha_corte,
                            'id_operario' => $id_operario,
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    } 
    
    //PERMITE CONSULTAR LOS PAGOS DE SERVICIOS
    
      public function actionSearchpageprenda() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 119])->all()) {
                $form = new \app\models\FormFiltroSearchPagePrenda();
                $id_operario = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $documento = NULL;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_operario = Html::encode($form->id_operario);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $documento = Html::encode($form->documento);
                        $table = \app\models\PagoNominaServicios::find()
                                ->andFilterWhere(['=', 'id_operario', $id_operario])
                                ->andFilterWhere(['=', 'fecha_inicio', $fecha_inicio])
                                ->andFilterWhere(['=', 'fecha_corte', $fecha_corte])
                                ->andFilterWhere(['=', 'documento', $documento])
                                ->andWhere(['=','autorizado', 1]);
                        $table = $table->orderBy('operario ASC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 120,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_pago  DESC']);
                            $this->actionExcelPagoPrendaServicio($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = \app\models\PagoNominaServicios::find()->where(['=','autorizado', 1])
                             ->orderBy('id_pago DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 120,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelPagoPrendaServicio($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('searchpageprenda', [
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
    
    public function actionCerrarAbrirRegistro($codigo) {
        $detalle = ValorPrendaUnidadDetalles::findOne($codigo);
        if($detalle->exportado == 0){
          $detalle->exportado = 1;
        }else{
            $detalle->exportado = 0;
        }  
        $detalle->save(false);
    }
    //VISTA
    public function actionView($id, $idordenproduccion)
    {
        $detalles_pago = ValorPrendaUnidadDetalles::find()->where(['=','id_valor', $id])->orderBy('consecutivo desc')->all();
        if (Yii::$app->request->post()) {
            if (isset($_POST["aplicaregla"])) {
                if (isset($_POST["consecutivo"])) {
                    $intIndice = 0;
                    $auxiliar = 0; $suma = 0; $total = 0;
                    $matricula = \app\models\Matriculaempresa::findOne(1);
                    $regla = \app\models\ReglaComisiones::find()->where(['=','estado_regla', 1])->one();
                    foreach ($_POST["consecutivo"] as $intCodigo) {
                        $table = ValorPrendaUnidadDetalles::findOne($intCodigo);
                        $auxiliar = $table->control_fecha;
                        $operario = Operarios::findOne($table->id_operario);
                        $consulta = ValorPrendaUnidadDetalles::find()->where(['=','id_operario', $operario->id_operario])->andWhere(['=','idordenproduccion', $idordenproduccion])->all();
                        $contador = 0;
                        if($table->aplica_regla == 0){
                            foreach ($consulta as $valor){
                               $contador += $valor->porcentaje_cumplimiento;
                            }
                            echo $contador;
                            if($contador == 1 ){
                                if($contador > $regla->porcentaje_cumplimiento){
                                    if($operario->vinculado == 1){
                                        $total =0;
                                        $total = round(($table->vlr_prenda/$matricula->vlr_minuto_vinculado) *($regla->valor_minuto_vinculado));
                                        $table->vlr_prenda = $total;
                                        $table->vlr_pago = $table->cantidad * $total;
                                    }else{
                                        $total =0;
                                        $total = round(($table->vlr_prenda/$matricula->vlr_minuto_contrato) *($regla->valor_minuto_contrato));
                                        $table->vlr_prenda = $total;
                                        $table->vlr_pago = $table->cantidad * $total;
                                        $table->costo_dia_operaria =  $table->vlr_pago;
                                      
                                    } 
                                }
                            }else{
                                if($contador > $regla->porcentaje_cumplimiento){
                                    if($operario->vinculado == 1){
                                        $total =0;
                                        $total = round(($table->vlr_prenda / $matricula->vlr_minuto_vinculado) *($regla->valor_minuto_vinculado));
                                        $table->vlr_prenda = $total;
                                        $table->vlr_pago = $table->cantidad * $total;
                                        
                                    }else{
                                        $total =0;
                                        $total = round(($table->vlr_prenda/$matricula->vlr_minuto_contrato) *($regla->valor_minuto_contrato));
                                        $table->vlr_prenda = $total;
                                        $table->vlr_pago = $table->cantidad * $total;
                                        $table->costo_dia_operaria =  $table->vlr_pago;
                                    } 
                                }
                            }
                            $table->aplica_regla = 1;
                            $table->save(false);
                              
                        }    
                        $intIndice++;   
                    }
                    return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion]);
                } else {
                    Yii::$app->getSession()->setFlash('warning', 'Debe seleccionar al menos un registro.');
                   return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion]);
                }    
             }
        }        
        //proceso para actualizar
           if (!isset($_POST["actualizarlinea"])) { 
                if (isset($_POST["detalle_pago_prenda"])) {
                    $intIndice = 0;
                    $salario = 0;
                    $auxiliar = 0;
                    foreach ($_POST["detalle_pago_prenda"] as $intCodigo) { 
                        $table = ValorPrendaUnidadDetalles::findOne($intCodigo);
                        $table->id_operario = $_POST["id_operario"][$intIndice];
                        $table->operacion = $_POST["operacion"][$intIndice];
                        $table->dia_pago = $_POST["dia_pago"][$intIndice];
                        $table->cantidad = $_POST["cantidad"][$intIndice];
                        $table->control_fecha = $_POST["control_fecha"][$intIndice];
                        $table->aplica_sabado = $_POST["aplica_sabado"][$intIndice];
                        $auxiliar = $table->control_fecha;
                        $operario = Operarios::find()->where(['=','id_operario', $_POST["id_operario"][$intIndice]])->one();
                        $valor_unidad = ValorPrendaUnidad::find()->where(['=','id_valor', $id])->andWhere(['=','idordenproduccion', $idordenproduccion])->one();
                        $vlr_unidad = 0;
                        if($table->aplica_regla == 0){
                            if($operario){
                              $conMatricula = \app\models\Matriculaempresa::findOne(1);
                                if($operario->vinculado == 1){
                                $vlr_unidad = $valor_unidad->vlr_vinculado;
                                if($_POST["vlr_prenda"][$intIndice] == ''){
                                  $table->vlr_prenda = $vlr_unidad;
                                    if($valor_unidad->debitar_salario_dia == 1){ 
                                        $salario = round($operario->salario_base /30);
                                        $table->vlr_pago = (($table->vlr_prenda * $table->cantidad) - $salario);
                                           $this->CostoOperarioVinculado($table, $auxiliar);
                                    }else{
                                        $table->vlr_pago = $table->vlr_prenda * $table->cantidad;
                                        $this->CostoOperarioVinculado($table, $auxiliar);
                                    }    
                                }else{
                                    $table->vlr_prenda = $_POST["vlr_prenda"][$intIndice];
                                    if($valor_unidad->debitar_salario_dia == 1){ 
                                        $salario = round($operario->salario_base /30);
                                        $table->vlr_pago = (($table->vlr_prenda * $table->cantidad) - $salario);
                                           $this->CostoOperarioVinculado($table, $auxiliar);

                                    }else{
                                        $table->vlr_pago = $_POST["vlr_prenda"][$intIndice] * $table->cantidad; 
                                        $this->CostoOperarioVinculado($table, $auxiliar);

                                    }    
                                }
                               //calculo para hallar el % de cumplimiento
                               $balanceoModulo= \app\models\Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
                               $totalHoras = 0; $totalTiempo = 0; $total_diario = 0;
                               $sw = 0; $sumarh = 0; $sumarm = 0; $can_minutos =0; $metaDiaria = 0;
                               $cumplimiento = 0;
                               foreach ($balanceoModulo as $modulo):
                                    if($table->dia_pago == $modulo->fecha_inicio && $table->hora_inicio_modulo >  $operario->horarios->desde){
                                        $horad = explode(":", $table->hora_inicio_modulo);
                                        $horah = explode(":", $operario->horarios->hasta);
                                        $sumarh = $horah[0] - $horad[0];
                                        $sumarm = $horah[1] + $horad[1];
                                        $totalTiempo = $sumarh;
                                        $totalTiempo = ($sumarh * 60) + $sumarm;
                                        $totalTiempo = $totalTiempo/60;
                                        $can_minutos = $table->vlr_prenda / $conMatricula->vlr_minuto_vinculado; 
                                        $total_diario = round((60/$can_minutos)* $totalTiempo,0);
                                        $cumplimiento = round(($table->cantidad / $total_diario)*100, 2);
                                        $metaDiaria = round((((60/$can_minutos)* $totalTiempo) * $conMatricula->porcentaje_empresa)/100); 
                                        $sw = 1;
                                    }
                               endforeach;
                               if ($sw == 0){
                                    $can_minutos = $table->vlr_prenda / $conMatricula->vlr_minuto_vinculado; 
                                    $total_diario = round((60/$can_minutos)* $operario->horarios->total_horas,0);
                                    $cumplimiento = round(($table->cantidad / $total_diario)*100, 2);
                                    $metaDiaria = round((((60/$can_minutos)* $operario->horarios->total_horas) * $conMatricula->porcentaje_empresa)/100);
                                }
                               $table->usuariosistema = Yii::$app->user->identity->username;
                               $table->observacion = 'Vinculado';
                               $table->porcentaje_cumplimiento = $cumplimiento;
                               $table->meta_diaria = $metaDiaria;
                               $table->save(false);
                               $intIndice++;
                            }else{
                                   $vlr_unidad = $valor_unidad->vlr_contrato; 
                                   if($_POST["vlr_prenda"][$intIndice] == ''){
                                        $table->vlr_prenda = $vlr_unidad;
                                        $table->vlr_pago = $table->vlr_prenda * $table->cantidad;
                                        $table->costo_dia_operaria = $table->vlr_pago;
                                   }else{
                                        $table->vlr_prenda = $_POST["vlr_prenda"][$intIndice];
                                        $table->vlr_pago = $_POST["vlr_prenda"][$intIndice] * $table->cantidad; 
                                        $table->costo_dia_operaria = $table->vlr_pago;
                                   }
                                   //calculo para hallar el % de cumplimiento
                                    $balanceoModulo= \app\models\Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
                                    $totalHoras = 0; $totalTiempo = 0; $total_diario = 0;
                                    $sw = 0; $sumarh = 0; $sumarm = 0; $can_minutos =0; $metaDiaria = 0;
                                    $cumplimiento = 0;
                                    foreach ($balanceoModulo as $modulo):
                                         if($table->dia_pago == $modulo->fecha_inicio && $table->hora_inicio_modulo > $operario->horarios->desde){
                                             $horad = explode(":", $table->hora_inicio_modulo);
                                             $horah = explode(":", $operario->horarios->hasta);
                                             $sumarh = $horah[0] - $horad[0];
                                             $sumarm = $horah[1] + $horad[1];
                                             $totalTiempo = ($sumarh * 60) + $sumarm;
                                             $totalTiempo = $totalTiempo/60;
                                             $can_minutos = $table->vlr_prenda / $conMatricula->vlr_minuto_contrato; 
                                             $total_diario = round((60/$can_minutos)* $totalTiempo,0);
                                             $cumplimiento = round(($table->cantidad / $total_diario)*100, 2);
                                             $metaDiaria = round((((60/$can_minutos)* $totalTiempo) * $conMatricula->porcentaje_empresa)/100); 
                                             $sw = 1;
                                         }
                                    endforeach;
                                    if($sw == 0){
                                        $can_minutos = $table->vlr_prenda / $conMatricula->vlr_minuto_contrato; 
                                        $total_diario = round((60/$can_minutos)* $operario->horarios->total_horas,0);
                                        $cumplimiento = round(($table->cantidad / $total_diario)*100, 2);
                                        $metaDiaria = round((((60/$can_minutos)* $operario->horarios->total_horas) * $conMatricula->porcentaje_empresa)/100);
                                    }    
                                    $table->usuariosistema = Yii::$app->user->identity->username;
                                    $table->observacion = 'No vinculado';
                                    $table->porcentaje_cumplimiento = $cumplimiento;
                                    $table->meta_diaria = $metaDiaria;
                                    $table->save(false);
                                    $intIndice++;
                               }  
                            }
                        }
                    }
                    $this->Totalpagar($id);
                    $this->TotalCantidades($id);
                    return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion]);
                }
            } 
           
     return $this->render('view', [
            'model' => $this->findModel($id),
            'idordenproduccion' => $idordenproduccion,
            'detalles_pago' => $detalles_pago,
        ]);
    }
  
    // PROCESO QUE BUSCA EN COSTO DEL PERSONAL VINCULADO
    protected function CostoOperarioVinculado($table, $auxiliar)
        {
            $valorCesantia = 0; $valorPrima = 0; $vlrDia = 0; $valorInteres = 0;
            $totalDia = 0; $valorVacacion = 0; $valorArl = 0; $auxilioT = 0;
            $operario = Operarios::findOne($table->id_operario);
            $vlrDia = round($operario->salario_base / 30);
            $porcentaje = \app\models\Matriculaempresa::findOne(1);
            $auxilio = \app\models\ConfiguracionSalario::find()->where(['=','estado', 1])->one();
            $auxilioT = round($auxilio->auxilio_transporte_actual / 30);
            $valorPrima = round($vlrDia * $porcentaje->porcentaje_prima)/100;
            $valorCesantia = round($vlrDia * $porcentaje->porcentaje_cesantias)/100;
            $valorInteres = round($vlrDia * $porcentaje->porcentaje_intereses)/100;
            $valorVacacion = round($vlrDia * $porcentaje->porcentaje_vacacion)/100;
            $valorArl = round($vlrDia * $operario->arl->arl)/100;
            $totalDia = $valorPrima + $valorCesantia + $valorInteres + $valorVacacion + $valorArl + $vlrDia + $auxilioT;
            if ($auxiliar == 1){
                $table->costo_dia_operaria = 0;
            }else{
               $table->costo_dia_operaria = $totalDia; 
            }
            $table->save(false);
    }
    
    //PROCESO QUE APLICA REGLA
    public function actionAplicarregla($id, $idordenproduccion) {
       
        
   }
    
    /**
     * Creates a new ValorPrendaUnidad model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ValorPrendaUnidad();
        $orden = Ordenproduccion::find()->where(['=','pagada', 0])->orderBy('idordenproduccion desc')->all();  
        if ($model->load(Yii::$app->request->post()) && $model->save()){
            $model->usuariosistema = Yii::$app->user->identity->username;
            $model->estado_valor = 0; 
            $ordenproduccion = Ordenproduccion::findOne($model->idordenproduccion);
            $model->cantidad = $ordenproduccion->cantidad;
            $model->update();
            return $this->redirect(['index', 'id' => $model->id_valor]);
        }

        return $this->render('create', [
            'model' => $model,
            'orden' => ArrayHelper::map($orden, "idordenproduccion", "idordenproduccion"),
        ]);
    }

    /**
     * Updates an existing ValorPrendaUnidad model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $orden = Ordenproduccion::find()->where(['=','pagada', 0])->orderBy('idordenproduccion desc')->all();  
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $ordenproduccion = Ordenproduccion::findOne($model->idordenproduccion);
            $model->usuario_editado = Yii::$app->user->identity->username;
            $fecha = date('Y-m-d h:i:s');
            $model->fecha_editado = $fecha;
            $model->cantidad = $ordenproduccion->cantidad;
            $model->update();
            return $this->redirect(['index', 'id' => $model->id_valor]);
        }
        return $this->render('update', [
            'model' => $model,
            'orden' => ArrayHelper::map($orden, "idordenproduccion", "idordenproduccion"),
        ]);
    }

    /**
     * Deletes an existing ValorPrendaUnidad model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
            $this->redirect(["valor-prenda-unidad/index"]);
        } catch (IntegrityException $e) {
            $this->redirect(["valor-prenda-unidad/index"]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
            $this->redirect(["valor-prenda-unidad/index"]);
        }
    }

    protected function findModel($id)
    {
        if (($model = ValorPrendaUnidad::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
   
    //MODAL QUE BUSCA LAS OPERACIONES DE LOS OPERARIOS EN PREPARACION
    public function actionBuscaroperaciones($id, $idordenproduccion) {
        
        if (Yii::$app->request->post()) {
            if (isset($_POST["validaroperario"])) {
                if (isset($_POST["idoperario"])) {
                    $intIndice = 0;
                    $empresa = \app\models\Matriculaempresa::find()->where(['=','id', 1])->one();
                    foreach ($_POST["idoperario"] as $intCodigo):
                        $balanceo = \app\models\Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])
                                             ->andWhere(['=','id_proceso_confeccion', 2])
                                             ->one();
                        $balanceo1 = \app\models\Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])
                                              ->andWhere(['=','id_proceso_confeccion', 3])->one();
                        if($balanceo){
                            $detalle_balanceo = \app\models\BalanceoDetalle::find()->where(['=','id_balanceo', $balanceo->id_balanceo])
                                                                               ->andWhere(['=','id_operario',  $intCodigo])
                                                                               ->andWhere(['=','estado_operacion', 0])
                                                                               ->orderBy('id_operario DESC')->all();
                        }else{
                            $detalle_balanceo = \app\models\BalanceoDetalle::find()->where(['=','id_balanceo', $balanceo1->id_balanceo])
                                                                               ->andWhere(['=','id_operario',  $intCodigo])
                                                                               ->andWhere(['=','estado_operacion', 0])
                                                                               ->orderBy('id_operario DESC')->all();
                        }    
                        $operarios = Operarios::findOne($intCodigo);
                        $total = 0;
                        foreach ($detalle_balanceo as $val):
                            $total += $val->minutos;
                        endforeach;
                        $valor = 0;
                        if ($operarios->vinculado == 1){
                           $valor = number_format($total * $empresa->vlr_minuto_vinculado, 0);
                           $vinculado = 'Vinculado';
                        }else{
                            $valor = number_format($total * $empresa->vlr_minuto_contrato, 0);
                            $vinculado = 'No vinculado';
                        }
                        $prenda = new ValorPrendaUnidadDetalles();
                        $prenda->id_operario = $intCodigo;
                        $prenda->id_valor = $id;                
                        $prenda->idordenproduccion = $idordenproduccion;
                        $prenda->dia_pago= date('Y-m-d');
                        $prenda->operacion = 2;
                        $prenda->vlr_prenda = $valor;
                        $prenda->observacion = $vinculado;
                        $prenda->save(false);
                        $intIndice ++;
                    endforeach;
                    return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion]);
                }
            }
        }   
        return $this->renderAjax('_buscaroperacionesmodulo', [
          //  'model' => $model,
            'id' => $id,
            'idordenproduccion' => $idordenproduccion,
            ]);
    }
        
    
    
    //PROCESOS Y SUBPROCESOS
    
     public function actionNuevodetalle($id,$idordenproduccion)
    {              
        $valor_unidad = ValorPrendaUnidad::findOne($id);
        if($valor_unidad->cantidad_operacion > $valor_unidad->cantidad){
           $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion]); 
           Yii::$app->getSession()->setFlash('error', 'No se puede generar mas lineas porque la cantidad de operaciones  '.$valor_unidad->cantidad_operacion.' es mayor que la cantidad del lote '.$valor_unidad->cantidad .'.');  
        }else{
            if($valor_unidad->cantidad_procesada > $valor_unidad->cantidad){
                $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion]); 
                Yii::$app->getSession()->setFlash('error', 'No se puede generar mas lineas porque la cantidad de confeccion y/o Terminación '.$valor_unidad->cantidad_procesada.' es mayor o igual que la cantidad del lote '.$valor_unidad->cantidad.'.');
            }else{    
                $model = new ValorPrendaUnidadDetalles();
                $model->id_valor = $id;                
                 $model->idordenproduccion = $idordenproduccion;
                $model->dia_pago= date('Y-m-d');
                if($valor_unidad->id_proceso_confeccion <> 1){
                    $model->operacion = 2;
                }
                $model->save(false);
                return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion]);
            }
        }
       
    }
    
    //PROCESO QUE BUSCA EL MODULO Y TRAE LAS EMPLEADOS
    
     public function actionNuevodetallemodular($id, $idordenproduccion)
    {              
       $fecha_corte = date('Y-m-d'); 
        $balanceo = \app\models\Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])->orderBy('id_balanceo asc')->all();
        if ($balanceo){
            foreach ($balanceo as $val):
                //este bloque busca las unidades confeccionadas por fecha
                $cantidad = \app\models\CantidadPrendaTerminadas::find()->where(['=','id_balanceo', $val->id_balanceo])->andWhere(['=','fecha_entrada', $fecha_corte])->all();
                if($cantidad){
                    $suma = 0; $total = 0;
                    foreach ($cantidad as $contar):
                       $suma += $contar->cantidad_terminada; 
                    endforeach;
                    $total = round($suma / $val->cantidad_empleados); 
                    //este proceso busca los operarios que estan en el modulo
                    $detalle_balanceo = \app\models\BalanceoDetalle::find()->where(['=','id_balanceo', $val->id_balanceo])->orderBy('id_operario asc')->all();
                    $operario = 0; $variable = 0;
                    if($detalle_balanceo){
                        foreach ($detalle_balanceo as $detalle):
                               $operario = $detalle->id_operario;
                               if($variable <> $operario){
                                $operario = $detalle->id_operario; 
                                 $variable = $operario;
                                 $valor_prenda = new ValorPrendaUnidadDetalles();
                                 $valor_prenda->id_operario = $operario;
                                 $valor_prenda->id_valor = $id;
                                 $valor_prenda->dia_pago= $fecha_corte;
                                 $valor_prenda->hora_inicio_modulo = $val->hora_inicio;
                                 $valor_prenda->idordenproduccion = $idordenproduccion;
                                 $valor_prenda->cantidad = $total; 
                                 $valor_prenda->save(false);
                              }
                        endforeach;
                    }else{
                         $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion]);
                         Yii::$app->getSession()->setFlash('error', 'La orden de produccion Nro: '. $idordenproduccion. ', no tiene asignado empleados para las operaciones.'); 
                    }    
                }else{
                   $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion]);
                   Yii::$app->getSession()->setFlash('error', 'El modulo de balanceo Nro: '. $val->id_balanceo. ', no realizo confeccion el dia '.$fecha_corte.'. Favor hacer este proceso manual.'); 
                }    
            endforeach;
           return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion]);
        }else{
             $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion]);
             Yii::$app->getSession()->setFlash('error', 'La orden de produccion Nro: '. $idordenproduccion. ', no tiene balanceo creado en sistema.!');
        }
       // return $this->redirect(['view', 'id' => $id]);
    }
   
    //proceso de carga el pago de nomina
    
    public function actionPagarserviciosoperarios() {
        
        $model = new \app\models\FormPagarServicioOperario();
      
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()){
                if (isset($_POST["crearfechaspago"])) {
                   $datosPago = \app\models\PagoNominaServicios::find()->where(['=','fecha_inicio', $model->fecha_inicio])
                                                                      ->andWhere(['=','fecha_corte', $model->fecha_corte])->one(); 
                    $fecha_inicio = $model->fecha_inicio;
                    $fecha_corte = $model->fecha_corte;                   
                    if($datosPago){
                       $this->redirect(["pageserviceoperario", 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]); 
                    }else{
                        $operario = Operarios::find()->where(['=','aplica_nomina_modulo', 1])
                                                     ->andWhere(['=','estado', 1])->all();
                       
                        foreach ($operario as $operarios):
                            $tabla = new \app\models\PagoNominaServicios();
                            $tabla->id_operario = $operarios->id_operario;
                            $tabla->documento = $operarios->documento;
                            $tabla->operario = $operarios->nombrecompleto;
                            $tabla->fecha_inicio = $model->fecha_inicio;
                            $tabla->fecha_corte = $model->fecha_corte;
                            $tabla->observacion = $model->observacion;
                            $tabla->usuariosistema = Yii::$app->user->identity->username;
                            $tabla->save(false);
                        endforeach;
                       
                         $this->redirect(["pageserviceoperario", 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]); 
                    }    
                }
                 
            }
        }
        
        return $this->renderAjax('pagarserviciosoperario', [
            'model' => $model,       
        ]);      
    }
    
    //metodo que llama los pagos del servicio
    
    public function actionPageserviceoperario($fecha_inicio, $fecha_corte) {
        if (isset($_POST["id_pago"])) {
            $intIndice = 0;
            $matricula = \app\models\Matriculaempresa::findOne(1);
            $configuracion_salario = \app\models\ConfiguracionSalario::find()->where(['=','estado', 1])->one();
            foreach ($_POST["id_pago"] as $intCodigo):
                $pago = \app\models\PagoNominaServicios::findOne($intCodigo);
                $buscarPagos = ValorPrendaUnidadDetalles::find()->where(['=','id_operario', $pago->id_operario])
                                                           ->andWhere(['>=','dia_pago', $fecha_inicio])
                                                           ->andWhere(['<=','dia_pago', $fecha_corte]) ->all();
                $contador = 0; $con = 0;
                $auxiliar = '';
                foreach ($buscarPagos as $valores):
                    $contador += $valores->vlr_pago;
                    if ($auxiliar <> $valores->dia_pago){
                        $con += 1; 
                        $auxiliar = $valores->dia_pago;
                    }else{
                        $auxiliar = $valores->dia_pago;
                    }
                endforeach;
                $pago->Total_pagar = $contador;
                $pago->total_dias = $con;
                $pago->save(false);
                //codigo para insertar devengados
                $buscar = \app\models\PagoNominaServicioDetalle::find()->where(['=','id_pago', $intCodigo])->one();
                if (!$buscar){
                    $detalle_pago = new \app\models\PagoNominaServicioDetalle();
                    $detalle_pago->id_pago = $intCodigo;
                    $detalle_pago->codigo_salario = $matricula->codigo_salario;
                    $detalle_pago->devengado = $contador;
                    $detalle_pago->save(false);
                    //codigo para insertar creditos
                    $credito = \app\models\CreditoOperarios::find()->where(['=','id_operario', $pago->id_operario])
                                                                   ->andWhere(['>','saldo_credito', 0])
                                                                   ->andWhere(['=','estado_credito', 1])->all();
                    if($credito){
                        foreach ($credito as $descuento):
                            $configuracion = \app\models\ConfiguracionCredito::find()->where(['=','codigo_credito', $descuento->codigo_credito])->one();
                            $detalle_credito = new \app\models\PagoNominaServicioDetalle();
                            $detalle_credito->id_pago = $intCodigo;
                            $detalle_credito->codigo_salario = $configuracion->codigo_salario;
                            $detalle_credito->deduccion = $descuento->vlr_cuota;
                            $detalle_credito->id_credito = $descuento->id_credito;
                            $detalle_credito->save(false);   
                        endforeach;
                    }
                   //codigo que inserta el auxilio de transporte
                    if($matricula->aplica_auxilio == 1){
                        $pagoBuscar = \app\models\PagoNominaServicios::findOne($intCodigo);
                        if($pagoBuscar->Total_pagar > $matricula->base_auxilio){
                            $detalle_auxilio = new \app\models\PagoNominaServicioDetalle();
                            $detalle_auxilio->id_pago = $intCodigo;
                            $detalle_auxilio->codigo_salario = $matricula->codigo_salario_auxilio;
                            $detalle_auxilio->devengado = round(($configuracion_salario->auxilio_transporte_actual / 30) * $pagoBuscar->total_dias);
                            $detalle_auxilio->save(false);   
                        }
                    }
                }    
            endforeach;
            return $this->render('pageserviceoperario', ['fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]); 
         }
         return $this->render('pageserviceoperario', ['fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]);
    }
    //METODO QUE ACTUALIZA SALDO DE LA NOMINA DE CONFECCION
    
    public function actionActualizarsaldo($fecha_corte, $fecha_inicio){
        
        $pago = \app\models\PagoNominaServicios::find()->where(['=','fecha_inicio', $fecha_inicio])->andWhere(['=','fecha_corte', $fecha_corte])->all(); 
        foreach ($pago as $pagoNomina):
            $pagoDetalle = \app\models\PagoNominaServicioDetalle::find()->where(['=','id_pago', $pagoNomina->id_pago])->all();
            $deduccion = 0;
            $devengado = 0;
            foreach ($pagoDetalle as $detalle):
                 $devengado += $detalle->devengado;
                 $deduccion += $detalle->deduccion;
            endforeach;
            $pagoNomina->devengado = $devengado;
            $pagoNomina->deduccion = $deduccion;
            $pagoNomina->Total_pagar = $devengado - $deduccion;
            $pagoNomina->save(false);
        endforeach;
        $this->redirect(["pageserviceoperario", 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]); 
    }
    
    //CODIGO QUE AUTORIZA LA NOMINA
    public function actionAutorizarnomina($fecha_corte, $fecha_inicio) {
        $pago = \app\models\PagoNominaServicios::find()->where(['=','fecha_inicio', $fecha_inicio])->andWhere(['=','fecha_corte', $fecha_corte])->orderBy('operario')->all();
        foreach ($pago as $autorizar):
            $autorizar->autorizado = 1;
            $autorizar->save(false);
            //codigo que actualiza el estado de exportado
            $detalle = ValorPrendaUnidadDetalles::find()->where(['>=','dia_pago', $fecha_inicio])->andWhere(['<=','dia_pago', $fecha_corte])
                                                    ->andWhere(['=','id_operario', $autorizar->id_operario])->all();
            if(count($detalle)> 0){
                foreach ($detalle as $valor):
                    $valor->exportado = 1;
                   $valor->save(false);
                endforeach;
            }
            // codigo que busca si tiene credito todos
            $detallePago = \app\models\PagoNominaServicioDetalle::find()->where(['=','id_pago', $autorizar->id_pago])->all();  
            foreach ($detallePago as $detalle):
                  $credito = \app\models\CreditoOperarios::find()->where(['=','id_credito', $detalle->id_credito])->one();
                  if ($credito){
                      $table_abono = new \app\models\AbonoCreditoOperarios();
                      $table_abono->id_credito = $credito->id_credito;
                      $table_abono->vlr_abono = $detalle->deduccion;
                      $table_abono->saldo = $credito->saldo_credito - $table_abono->vlr_abono;
                      $table_abono->cuota_pendiente = ($credito->numero_cuotas - $credito->numero_cuota_actual) - 1;
                      $table_abono->observacion = $autorizar->observacion;
                      $table_abono->usuariosistema = Yii::$app->user->identity->username;
                      $table_abono->save(false);
                     $credito->numero_cuota_actual = $credito->numero_cuota_actual + 1;
                     $credito->saldo_credito = $credito->saldo_credito - $table_abono->vlr_abono;
                     $credito->save(false);
                  }
            endforeach;
        endforeach;
       $this->redirect(["pageserviceoperario", 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]); 
    }
    
    
    //CODIGO QUE VA AL DETALLE DEL PAGO
    
    public function actionVistadetallepago($id_pago, $fecha_corte, $fecha_inicio, $autorizado,  $token = 1) {
        $model = \app\models\PagoNominaServicios::findOne($id_pago);
        $detalle_pago = \app\models\PagoNominaServicioDetalle::find()->where(['=','id_pago', $model->id_pago])->orderBy('devengado asc')->all();
        return $this->render('vista_detalle_pago', [
                    'model' => $model,
                    'detalle_pago' => $detalle_pago,
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_corte' => $fecha_corte,
                    'autorizado' => $autorizado,
                    'id_pago' => $id_pago,
                    'token' => $token,
                    
        ]);
    }
    
    //PERMITE CONSULTAR LAS COLILLAS DE PAGO
    public function actionConsultadetallepago($id_pago, $fecha_corte, $fecha_inicio, $autorizado, $token = 2) {
        $model = \app\models\PagoNominaServicios::findOne($id_pago);
        $detalle_pago = \app\models\PagoNominaServicioDetalle::find()->where(['=','id_pago', $model->id_pago])->orderBy('devengado asc')->all();
        return $this->render('vista_detalle_pago', [
                    'model' => $model,
                    'detalle_pago' => $detalle_pago,
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_corte' => $fecha_corte,
                    'autorizado' => $autorizado,
                    'id_pago' => $id_pago,
                    'token' => $token,
                    
        ]);
    }
    
    
    //ESTE CODIGO EDITAR EL DETALLE DEL PAGO
    public function actionEditarvistadetallepago($id_pago, $id_detalle, $fecha_inicio, $fecha_corte, $autorizado) {
        
        $model = \app\models\PagoNominaServicioDetalle::findOne($id_detalle);
        if ($model->load(Yii::$app->request->post())) {
            $tabla = \app\models\PagoNominaServicioDetalle::findOne($id_detalle);
            $tabla->deduccion = $model->deduccion;
            $tabla->devengado = $model->devengado;
            $tabla->save(false);
            return $this->redirect(['valor-prenda-unidad/vistadetallepago','id_pago' => $id_pago, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'autorizado' => $autorizado]);
        }
        return $this->render('editar_vista_detalle_pago', [
            'fecha_corte' => $fecha_corte,
            'fecha_inicio' => $fecha_inicio,
            'model' => $model,
            'id_pago' => $id_pago,
            'id_detalle' => $id_detalle,
            'autorizado' => $autorizado,
        ]);
    }
   // codigo que permite agregar mas concepto de salario
    
    public function actionImportarconceptosalarios($id_pago, $fecha_inicio, $fecha_corte, $autorizado)
    {
        $pilotoDetalle = \app\models\ConceptoSalarios::find()->Where(['=','adicion', 1])
                                                        ->andWhere(['=','tipo_adicion', 1])
                                                        ->andWhere(['=','debito_credito', 0]) 
                                                        ->orderBy('nombre_concepto asc')->all();
        $form = new \app\models\FormMaquinaBuscar();
        $q = null;
        $mensaje = '';
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                if ($q){
                    $pilotoDetalle = \app\models\ConceptoSalarios::find()
                            ->where(['like','nombre_concepto',$q])
                            ->orwhere(['like','codigo_salario',$q])
                            ->orderBy('nombre_concepto asc')
                            ->all();
                }               
            } else {
                $form->getErrors();
            }                    
        } else {
            $pilotoDetalle = \app\models\ConceptoSalarios::find()->Where(['=','adicion', 1])
                                                        ->andWhere(['=','tipo_adicion', 1])
                                                        ->andWhere(['=','debito_credito', 0]) 
                                                        ->orderBy('nombre_concepto asc')->all();
        }
        if (isset($_POST["codigo_salario"])) {
           $intIndice = 0;
            foreach ($_POST["codigo_salario"] as $intCodigo) {
                $table = new \app\models\PagoNominaServicioDetalle();
               // $detalle = PilotoDetalleProduccion::find()->where(['id_proceso' => $intCodigo])->one();
                $table->id_pago = $id_pago;
                $table->codigo_salario = $intCodigo;
                $table->devengado = 0;
                $table->deduccion = 0;
                $table->save(false);                                                
            }
           $this->redirect(["valor-prenda-unidad/vistadetallepago", 'id_pago' => $id_pago, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'autorizado' => $autorizado]);
        }
        return $this->render('importarconceptosalarios', [
            'pilotoDetalle' => $pilotoDetalle,            
            'mensaje' => $mensaje,
            'id_pago' => $id_pago,
            'fecha_inicio' => $fecha_inicio,
            'fecha_corte' => $fecha_corte,
            'autorizado' => $autorizado,
            'form' => $form,

        ]);
    }
    
    // PROCESO QUE ELIMINE EL DETALLE DEL PAGO
    
     public function actionEliminardetallepago($id_pago,$id_detalle, $fecha_inicio, $fecha_corte, $autorizado)
    {                                
        $detalle = \app\models\PagoNominaServicioDetalle::findOne($id_detalle);
        $detalle->delete();
        $this->redirect(["vistadetallepago",'id_pago' => $id_pago, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'autorizado' => $autorizado]);        
    }
    //proceso que imprime la colilla de confeccion
    
    public function actionImprimircolillaconfeccion($id_pago, $fecha_inicio, $fecha_corte)
    {                                
      //   $model = \app\models\PagoNominaServicios::findOne($id_pago);
        
         return $this->render('../formatos/colillapagoconfeccion', [
              'model' => \app\models\PagoNominaServicios::findOne($id_pago),
             'fecha_inicio' => $fecha_inicio,
             'fecha_corte' => $fecha_corte,
        ]);
    }
    
    public function actionEliminar($id,$detalle, $idordenproduccion)
    {                                
        $detalle = ValorPrendaUnidadDetalles::findOne($detalle);
        $detalle->delete();
        $this->Totalpagar($id);
        $this->TotalCantidades($id);
        $this->redirect(["view",'id' => $id, 'idordenproduccion' => $idordenproduccion]);        
    }
    
    //ELIMINA EL REGISTRO D EPAGO
    
    public function actionEliminarpago($id, $fecha_inicio, $fecha_corte)
    {                                
        try {
            $detalle = \app\models\PagoNominaServicios::findOne($id);
            $detalle->delete();
            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con éxito.');
            $this->redirect(["valor-prenda-unidad/pageserviceoperario",'fecha_inicio' => $fecha_inicio, 'fecha_corte' =>$fecha_corte]);
        } catch (IntegrityException $e) {
            $this->redirect(["valor-prenda-unidad/pageserviceoperario",'fecha_inicio' => $fecha_inicio, 'fecha_corte' =>$fecha_corte]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
            $this->redirect(["valor-prenda-unidad/pageserviceoperario",'fecha_inicio' => $fecha_inicio, 'fecha_corte' =>$fecha_corte]);
        }
    }
    
    protected function Totalpagar($id) {
        $valor = ValorPrendaUnidad::findOne($id);
        $detalle = ValorPrendaUnidadDetalles::find()->where(['=','id_valor', $id])->all();
        $suma=0; 
        $ajuste = 0;
        $operacion = 0;
        foreach ($detalle as $val):
            if($val->operacion == 0){
               $suma += $val->costo_dia_operaria;
            }else{
                if($val->operacion == 1){
                    $operacion += $val->costo_dia_operaria;
                }else{
                    $ajuste += $val->costo_dia_operaria;
                }
            }   
        endforeach;
        $valor->total_confeccion = $suma;
        $valor->total_operacion = $operacion;
        $valor->total_ajuste = $ajuste;
        $valor->total_pagar = $suma + $operacion + $ajuste;
        $valor->save(false);
    }
    //actualiza las cantidades
    protected function TotalCantidades($id) {
        $valor = ValorPrendaUnidad::findOne($id);
        $detalle = ValorPrendaUnidadDetalles::find()->where(['=','id_valor', $id])->all();
        $suma=0; $operacion = 0;
            foreach ($detalle as $val):
                if($val->operacion == 0){
                   $suma += $val->cantidad;
                }else{
                    if(($val->operacion == 1)){
                       $operacion += $val->cantidad; 
                    }
                }
            endforeach;
            $valor->cantidad_procesada = $suma;
            $valor->cantidad_operacion = $operacion;
            $valor->save(false);
        if($valor->cantidad_procesada > $valor->cantidad  || $valor->cantidad_operacion > $valor->cantidad){
                Yii::$app->getSession()->setFlash('error', 'La cantidad y/o operacion procesada es mayor que las unidades entradas en la orden Nro: '. $valor->idordenproduccion. '.');
        } 
       
    }
    
    public function actionAutorizado($id, $idordenproduccion) {
        $model = $this->findModel($id);
        $mensaje = "";
        if($model->cantidad_procesada > $model->cantidad  || $model->cantidad_operacion > $model->cantidad){
            $this->redirect(["valor-prenda-unidad/view", 'id' => $id]);
             Yii::$app->getSession()->setFlash('error', 'La cantidad y/o operacion procesada es mayor que las unidades entradas en la orden Nro: '. $model->idordenproduccion. '.');
        }else{  
            if ($model->autorizado == 0) {                        
                $model->autorizado = 1;            
               $model->update();
               $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion]);  

            } else{
                $model->autorizado = 0;
                $model->update();
                $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion]); 
            }
        }    
    }
    
    public function actionCerrarpago($id, $idordenproduccion) {
           $model = $this->findModel($id);
           $orden = Ordenproduccion::findOne($idordenproduccion);
           $model->cerrar_pago =  1;
           $model->estado_valor = 1;
           $model->save(false);
           $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion]);
    }
    //cerrar el pago y la orden de produccion
    
    public function actionCerrarpagoorden($id, $idordenproduccion) {
           $model = $this->findModel($id);
           $orden = Ordenproduccion::findOne($idordenproduccion);
           $model->cerrar_pago = 1;
           $model->estado_valor = 1;
           $model->save(false);
           $orden->pagada = 1;
           $orden->save(false);
           $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion]);
    }
    
    //EXPORTA A EXCEL LA CONSULTA DE TODOS LOS PAGOS
    
    public function actionPagoservicioconfeccion($fecha_corte, $fecha_inicio) {        
        $model = \app\models\PagoNominaServicios::find()->where(['=','fecha_inicio', $fecha_inicio])->andWhere(['=','fecha_corte', $fecha_corte])->orderBy([ 'operario' =>SORT_ASC ])->all();
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->mergeCells("a".(1).":l".(1));
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A2', 'No PAGO')
                    ->setCellValue('B2', 'DOCUMENTO')
                    ->setCellValue('C2', 'OPERARIO')
                    ->setCellValue('D2', 'FECHA INICIO')
                    ->setCellValue('E2', 'FECHA CORTE')
                    ->setCellValue('F2', 'FECHA PROCESO')
                    ->setCellValue('G2', 'No DIAS')
                    ->setCellValue('H2', 'USUARIO')
                    ->setCellValue('I2', 'AUTORIZADO')
                    ->setCellValue('J2', 'DEVENGADO')
                    ->setCellValue('K2', 'DEDUCCION')
                    ->setCellValue('L2', 'TOTAL PAGAR')
                    ->setCellValue('M2', 'OBSERVACION');
                  
        $i = 3;
        foreach ($model as $val) {                            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_pago)
                    ->setCellValue('B' . $i, $val->documento)
                    ->setCellValue('C' . $i, $val->operario)
                    ->setCellValue('D' . $i, $val->fecha_inicio)
                    ->setCellValue('E' . $i, $val->fecha_corte)
                    ->setCellValue('F' . $i, $val->fecha_registro)
                    ->setCellValue('G' . $i, $val->total_dias)
                    ->setCellValue('H' . $i, $val->usuariosistema)
                    ->setCellValue('I' . $i, $val->autorizado)
                    ->setCellValue('J' . $i, $val->devengado)
                    ->setCellValue('K' . $i, $val->deduccion)
                    ->setCellValue('L' . $i, $val->Total_pagar)
                    ->setCellValue('M' . $i, $val->observacion);
              
                   
            $i++;                        
        }

        $objPHPExcel->getActiveSheet()->setTitle('Total pagar');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="Valor_Nomina.xlsx"');
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
    
    //EXCEL QUE ESPORTAR LOS PAGOS DE NOMINA
    public function actionExcelPagoPrendaServicio($tableexcel) {        
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->mergeCells("a".(1).":l".(1));
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A2', 'No PAGO')
                    ->setCellValue('B2', 'DOCUMENTO')
                    ->setCellValue('C2', 'OPERARIO')
                    ->setCellValue('D2', 'FECHA INICIO')
                    ->setCellValue('E2', 'FECHA CORTE')
                    ->setCellValue('F2', 'FECHA PROCESO')
                    ->setCellValue('G2', 'No DIAS')
                    ->setCellValue('H2', 'USUARIO')
                    ->setCellValue('I2', 'AUTORIZADO')
                    ->setCellValue('J2', 'DEVENGADO')
                    ->setCellValue('K2', 'DEDUCCION')
                    ->setCellValue('L2', 'TOTAL PAGAR')
                    ->setCellValue('M2', 'OBSERVACION');
                  
        $i = 3;
        foreach ($tableexcel as $val) {                            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_pago)
                    ->setCellValue('B' . $i, $val->documento)
                    ->setCellValue('C' . $i, $val->operario)
                    ->setCellValue('D' . $i, $val->fecha_inicio)
                    ->setCellValue('E' . $i, $val->fecha_corte)
                    ->setCellValue('F' . $i, $val->fecha_registro)
                    ->setCellValue('G' . $i, $val->total_dias)
                    ->setCellValue('H' . $i, $val->usuariosistema)
                    ->setCellValue('I' . $i, $val->autorizado)
                    ->setCellValue('J' . $i, $val->devengado)
                    ->setCellValue('K' . $i, $val->deduccion)
                    ->setCellValue('L' . $i, $val->Total_pagar)
                    ->setCellValue('M' . $i, $val->observacion);
              
                   
            $i++;                        
        }

        $objPHPExcel->getActiveSheet()->setTitle('Total pagar');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="Valor_Nomina.xlsx"');
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
    
    public function actionGenerarexcel($id) {        
        $ficha = ValorPrendaUnidad::findOne($id);
        $model = ValorPrendaUnidadDetalles::find()->where(['=','id_valor',$id])->orderBy([ 'id_operario' =>SORT_ASC ])->all();
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
        $objPHPExcel->getActiveSheet()->mergeCells("a".(1).":l".(1));
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'PAGO DE OPERACIONES')
                    ->setCellValue('A2', 'ORDEN')
                    ->setCellValue('B2', 'DOCUMENTO')
                    ->setCellValue('C2', 'OPERARIO(A)')
                    ->setCellValue('D2', 'OPERACION')
                    ->setCellValue('E2', 'DIA PAGO')
                    ->setCellValue('F2', 'CANTIDAD')
                    ->setCellValue('G2', 'VR. PRENDA')
                    ->setCellValue('H2', 'VR. PAGO')
                    ->setCellValue('I2', '% CUMPLIMIENTO')
                    ->setCellValue('J2', 'USUARIO')
                    ->setCellValue('K2', 'OBSERVACION');
                  
        $i = 3;
        $confeccion = 'CONFECCION';
        $operaciones = 'OPERACIONES';
        $ajuste = 'AJUSTE';
        foreach ($model as $val) {                            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $ficha->idordenproduccion)
                    ->setCellValue('B' . $i, $val->operario->documento)
                    ->setCellValue('C' . $i, $val->operario->nombrecompleto);
                    if($val->operacion == 0){
                         $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('D' . $i, $confeccion);
                    }else{
                        if($val->operacion == 1){
                             $objPHPExcel->setActiveSheetIndex(0)
                             ->setCellValue('D' . $i, $operaciones);
                        }else{
                             $objPHPExcel->setActiveSheetIndex(0)
                             ->setCellValue('D' . $i, $ajuste);
                        }
                    } 
                     $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('E' . $i, $val->dia_pago)
                    ->setCellValue('F' . $i, $val->cantidad)
                    ->setCellValue('G' . $i, $val->vlr_prenda)
                    ->setCellValue('H' . $i, $val->vlr_pago)
                    ->setCellValue('I' . $i, $val->porcentaje_cumplimiento)
                    ->setCellValue('J' . $i, $val->usuariosistema)
                    ->setCellValue('K' . $i, $val->observacion);
              
                   
            $i++;                        
        }
        //promedio por dia
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("           
           SELECT SUM(valor_prenda_unidad_detalles.vlr_pago) AS Total, valor_prenda_unidad_detalles.id_operario FROM valor_prenda_unidad_detalles WHERE id_valor = ".$id."  GROUP BY id_operario");
        $result = $command->queryAll();
        $i = 3;
       /* foreach ($result as $promedio){
            $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('K' . $i, $promedio['Total'])
                     ->setCellValue('L' . $i, $promedio['id_operario']);   
            $i++;            
        }*/
        //fin promedio por dia

        $objPHPExcel->getActiveSheet()->setTitle('Total_pago_prendas');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="Total_pago_prendas.xlsx"');
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
    
    public function actionExcelResumeValorPrenda($tableexcel) {                
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
  
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'ORD. PRODUCCION')
                    ->setCellValue('C1', 'OPERARIO')
                    ->setCellValue('D1', 'OPERACION')
                    ->setCellValue('E1', 'FECHA PROCESO')                    
                    ->setCellValue('F1', 'CANT.')
                    ->setCellValue('G1', 'VR. PRENDA')
                    ->setCellValue('H1', 'TOTAL PAGADO')
                    ->setCellValue('I1', 'USUARIO')
                    ->setCellValue('J1', 'ESTADO_REGISTRO')
                    ->setCellValue('K1', '% CUMPLIMIENTO')
                     ->setCellValue('L1', 'OBSERVACION');
                   
        $i = 2;
        $confeccion = 'CONFECCION';
        $operaciones = 'OPERACIONES';
        $ajuste = 'AJUSTE';
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->consecutivo)
                    ->setCellValue('B' . $i, $val->idordenproduccion)
                    ->setCellValue('C' . $i, $val->operario->nombrecompleto);
                        if($val->operacion == 1){
                             $objPHPExcel->setActiveSheetIndex(0)
                          ->setCellValue('D' . $i, $confeccion);
                        }else{
                            if($val->operacion == 2){
                                 $objPHPExcel->setActiveSheetIndex(0)
                                 ->setCellValue('D' . $i, $operaciones);
                            }else{
                                 $objPHPExcel->setActiveSheetIndex(0)
                                 ->setCellValue('D' . $i, $ajuste);
                            }
                        } 
                     $objPHPExcel->setActiveSheetIndex(0)                    
                    ->setCellValue('E' . $i, $val->dia_pago)
                    ->setCellValue('F' . $i, $val->cantidad)  
                    ->setCellValue('G' . $i, $val->vlr_prenda)
                    ->setCellValue('H' . $i, $val->vlr_pago)
                    ->setCellValue('I' . $i, $val->usuariosistema)
                    ->setCellValue('J' . $i, $val->registroPagado)
                     ->setCellValue('K' . $i, $val->porcentaje_cumplimiento)
                    ->setCellValue('L' . $i, $val->observacion);
                  
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Resumen pago');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Resumen_pago.xlsx"');
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
    
     public function actionExcelconsultaValorPrenda($tableexcel) {                
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
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'ORDEN PROD.')
                    ->setCellValue('C1', 'CLIENTE')
                    ->setCellValue('D1', 'SERVICIO')
                    ->setCellValue('E1', 'VR. VINCULADO')                    
                    ->setCellValue('F1', 'VR. CONTRATO')
                    ->setCellValue('G1', 'TOTAL CONFECCION')
                    ->setCellValue('H1', 'CANT. PROCESADA')
                    ->setCellValue('I1', 'TOTAL AJUSTE')
                    ->setCellValue('J1', 'TOTAL OPERACION')
                    ->setCellValue('K1', 'CANT. OPERACION')
                    ->setCellValue('L1', 'TOTAL PAGAR')
                    ->setCellValue('M1', 'CANTIDAD') 
                    ->setCellValue('N1', 'AUTORIZADO')
                    ->setCellValue('O1', 'CERRADO')
                    ->setCellValue('P1', 'ACTIVO')
                    ->setCellValue('Q1', 'PLANTA/BODEGA')
                    ->setCellValue('R1', 'F. PROCESO')
                    ->setCellValue('S1', 'USUARIO EDITADO')
                    ->setCellValue('T1', 'F. EDITADO');
        $i = 2;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_valor)
                    ->setCellValue('B' . $i, $val->idordenproduccion)
                    ->setCellValue('C' . $i, $val->ordenproduccion->cliente->nombrecorto)
                    ->setCellValue('D' . $i, $val->tipo->tipo)                    
                    ->setCellValue('E' . $i, $val->vlr_vinculado)
                    ->setCellValue('F' . $i, $val->vlr_contrato)  
                    ->setCellValue('G' . $i, $val->total_confeccion)
                    ->setCellValue('H' . $i, $val->cantidad_procesada)
                    ->setCellValue('I' . $i, $val->total_ajuste)
                    ->setCellValue('J' . $i, $val->total_operacion)
                    ->setCellValue('K' . $i, $val->cantidad_operacion)
                    ->setCellValue('L' . $i, $val->total_pagar)
                    ->setCellValue('M' . $i, $val->cantidad)
                    ->setCellValue('N' . $i, $val->autorizadoPago)
                    ->setCellValue('O' . $i, $val->cerradoPago)
                    ->setCellValue('P' . $i, $val->estadovalor)
                    ->setCellValue('Q' . $i, $val->planta->nombre_planta)
                    ->setCellValue('R' . $i, $val->fecha_proceso)
                    ->setCellValue('S' . $i, $val->usuario_editado)
                    ->setCellValue('T' . $i, $val->fecha_editado);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Valor_prenda');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="valor_prendas.xlsx"');
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
