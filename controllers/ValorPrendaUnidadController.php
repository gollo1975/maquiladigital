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
use app\models\ModelAplicarPorcentaje;
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
                $bodega = null;
                $tipo_servicio = null;
                $validar_eficiencia = 0;
                $modelo = null;
                $pages = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $validar_eficiencia = Html::encode($form->validar_eficiencia);
                        $id_operario = Html::encode($form->id_operario);
                        $idordenproduccion = Html::encode($form->idordenproduccion);
                        $operacion = Html::encode($form->operacion);
                        $dia_pago = Html::encode($form->dia_pago);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $bodega = Html::encode($form->id_planta);
                        $tipo_servicio =Html::encode($form->tipo_servicio);
                        $table = ValorPrendaUnidadDetalles::find()
                                ->andFilterWhere(['=', 'id_operario', $id_operario])
                                ->andFilterWhere(['=', 'idordenproduccion', $idordenproduccion])
                                ->andFilterWhere(['=', 'operacion', $operacion])
                                ->andFilterWhere(['between', 'dia_pago', $dia_pago, $fecha_corte])
                                ->andFilterWhere(['=', 'id_tipo', $tipo_servicio])
                                ->andFilterWhere(['=', 'id_planta', $bodega]);
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
                }             
                return $this->render('indexsoporte', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
                            'validar_eficiencia' => $validar_eficiencia,
                            'dia_pago' =>$dia_pago,
                            'fecha_corte' => $fecha_corte,
                            'id_operario' => $id_operario,
                            'bodega' => $bodega,
                            'tipo_servicio' => $tipo_servicio,
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
                $planta = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_operario = Html::encode($form->id_operario);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $documento = Html::encode($form->documento);
                        $planta = Html::encode($form->planta);
                        $table = \app\models\PagoNominaServicios::find()
                                ->andFilterWhere(['=', 'id_operario', $id_operario])
                                ->andFilterWhere(['between', 'fecha_inicio', $fecha_inicio, $fecha_corte])
                                ->andFilterWhere(['=', 'documento', $documento])
                                ->andFilterWhere(['=', 'id_planta', $planta])
                                ->andWhere(['=','autorizado', 1]);
                        $table = $table->orderBy('id_pago DESC');
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
    public function actionView($id, $idordenproduccion, $id_planta, $tipo_pago)
    {
        $detalles_pago = ValorPrendaUnidadDetalles::find()->where(['=','id_valor', $id])->orderBy('consecutivo desc')->all();
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
                    $this->TotalCantidades($id, $tipo_pago);
                    return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago]);
                }
            } 
           
     return $this->render('view', [
            'model' => $this->findModel($id),
            'idordenproduccion' => $idordenproduccion,
            'detalles_pago' => $detalles_pago,
            'id_planta' =>$id_planta,
            'tipo_pago' => $tipo_pago,
        ]);
    }
    
    public function actionCantidad_talla_confeccion($idordenproduccion, $id, $id_planta, $id_detalle) {
        $talla = \app\models\Ordenproducciondetalle::findOne($id_detalle);
        $detalle_op = \app\models\Ordenproducciondetalle::find()->where(['=','iddetalleorden', $id_detalle])->one();
        
        $listado_confeccion = ValorPrendaUnidadDetalles::find()->where(['=','idordenproduccion', $idordenproduccion])
                                                               ->andWhere(['=','id_valor', $id])
                                                               ->andWhere(['=','iddetalleorden', $id_detalle]);
        $listado_confeccion = $listado_confeccion->orderBy('consecutivo DESC');
        $tableexcel = $listado_confeccion->all();
        $count = clone $listado_confeccion;
        $to = $count->count();
        $pages = new Pagination([
            'pageSize' => 20,
            'totalCount' => $count->count()
        ]);
        $listado_confeccion = $listado_confeccion
                ->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
        if(count($listado_confeccion) > 0){
            return $this->render('maestro_cantidad_talla', [
                'model' => $this->findModel($id),
                'id_planta' =>$id_planta,
                'id_detalle' => $id_detalle,
                'talla' => $talla,
                'listado_confeccion' => $listado_confeccion,
                'detalle_op' => $detalle_op,
                'pagination' => $pages,
            ]);
        }else{
            Yii::$app->getSession()->setFlash('warning', 'No hay registros para mostrar de esta talla.');
            $conTallas = \app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
            return $this->render('search_tallas_ordenes', [
                'model' => $this->findModel($id),
                'id_planta' =>$id_planta,
                'conTallas' =>  $conTallas,

            ]);
            }
        
    }
    
   //VISTA QUE TRAE LAS OPERACIONES DE LA OP
    public function actionView_search_operaciones($idordenproduccion, $id, $id_planta){
        $form = new \app\models\ModeloBuscarOperario();
        $operario = null;
        $detalle_balanceo = 0;
        $fecha_entrada = null;
        $aplica_sabado = null;
        $modulo = null;
        $id_detalle = null;
        $nombre_modulo = \app\models\Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])->andWhere(['=','id_planta', $id_planta])->all();
        $empresa = \app\models\Matriculaempresa::findOne(1);
        $listado_tallas = \app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
        if ($form->load(Yii::$app->request->get())) {
            $operario = Html::encode($form->operario);
            $aplica_sabado = Html::encode($form->aplica_sabado);
            $modulo = Html::encode($form->modulo);
            $fecha_entrada = Html::encode($form->fecha_entrada);
            $id_detalle = Html::encode($form->id_detalle);
            if ($operario > 0 && $fecha_entrada != null && $modulo != null && $id_detalle != null) {
                $detalle_balanceo = \app\models\BalanceoDetalle::find()->where(['=','id_operario', $operario])
                                                                        ->andWhere(['=','idordenproduccion', $idordenproduccion])
                                                                        ->andWhere(['=','estado_operacion', 0])
                                                                        ->andWhere(['=','id_balanceo', $modulo])->all();
            }else{
                Yii::$app->getSession()->setFlash('warning', 'Debe seleccionar el OPERARIO, FECHA, NOMBRE DEL MODULO y TALLA para la busqueda.');
                return $this->redirect(['view_search_operaciones','id_planta' => $id_planta, 'idordenproduccion' => $idordenproduccion, 'id' =>$id, 'id_detalle' =>$id_detalle]);
            }
        }
        if (isset($_POST["envia_dato_confeccion"])) {
            if ($fecha_entrada != null) {
                $intIndice = 0; $cantidad = 0;
               foreach ($_POST["operaciones"] as $intCodigo) {
                    if ($_POST["cantidad"][$intIndice] > 0){
                        $total_unidades = 0; $cant = 0; $confeccionada = 0; $sumar_unidades = 0; $total_operacion = 0; $total_unidades_faltante = 0;
                        $conCantidad = \app\models\Ordenproducciondetalle::findOne($id_detalle);
                        $detalle = \app\models\BalanceoDetalle::findOne($intCodigo);//busca las operaciones en el balanceo
                        $detalle_valor_prenda = ValorPrendaUnidadDetalles::find()->where(['=','idproceso', $detalle->id_proceso])->andWhere(['=','iddetalleorden', $id_detalle])->all();
                        $balanceo_entrada = \app\models\Balanceo::findOne($modulo);
                        foreach ($detalle_valor_prenda as $detalle_valor):
                            $sumar_unidades += $detalle_valor->cantidad;
                        endforeach;
                        $total_unidades_faltante = $conCantidad->cantidad - $sumar_unidades; //totaliza las uniades faltantes
                        $cant = $_POST["cantidad"][$intIndice];
                        $confeccionada = $conCantidad->cantidad_confeccionada;
                        $total_unidades =  $confeccionada + $cant;
                        $total_operacion = $sumar_unidades + $cant;
                        if($total_operacion <= $conCantidad->cantidad){ //ciclo que valide no ingresar mas de las opraciones de la talla
                            if($total_unidades <= $conCantidad->cantidad_operaciones){
                                $valor_prenda = 0; $nota = '';
                                $tipo_proceso = ValorPrendaUnidad::findOne($id);
                                $operarios = Operarios::findOne($detalle->id_operario);//busca el operario
                                $con = ValorPrendaUnidadDetalles::find()->where(['=','id_operario', $operario])->andWhere(['=','dia_pago', $fecha_entrada])->one();
                                $table = new ValorPrendaUnidadDetalles();
                                $table->id_operario = $operario;
                                $table->idordenproduccion = $idordenproduccion;
                                $table->operacion = 1;
                                $table->hora_inicio_modulo = $balanceo_entrada->hora_inicio;
                                $table->dia_pago = $fecha_entrada;
                                $cantidad = $_POST["cantidad"][$intIndice];
                                $table->cantidad = $_POST["cantidad"][$intIndice];
                                if($operarios->vinculado == 0){ // operaria al contrato
                                    $valor_prenda = round($detalle->minutos * $empresa->vlr_minuto_contrato);
                                    $nota = 'Contrato';
                                    $valor_costo = $valor_prenda * $cantidad;
                                    $table->costo_dia_operaria = $valor_costo;
                                }else{ // operaria vinculada
                                    $valor_prenda = round($detalle->minutos * $empresa->vlr_minuto_vinculado);
                                    $nota = 'Vinculado';
                                    if($con){
                                        $table->control_fecha = 1;
                                    }
                                }
                                $table->vlr_prenda = $valor_prenda;
                                $table->vlr_pago = $valor_prenda * $cantidad;
                                $table->id_valor = $id;
                                $table->usuariosistema = Yii::$app->user->identity->username;
                                $table->observacion = $nota;
                                $table->id_planta = $id_planta;
                                $table->id_tipo = $tipo_proceso->idtipo;
                                $table->aplica_regla = 1;
                                if($aplica_sabado == 1){
                                    $table->aplica_sabado = 1;
                                }
                                $table->iddetalleorden = $id_detalle;
                                $table->idproceso = $detalle->id_proceso;
                                $table->save(false);
                                $this->SumarCantidadCostoConfeccion($id, $id_detalle, $idordenproduccion);
                                $this->CalcularEficienciaOperario($operario, $idordenproduccion, $id, $id_detalle);
                            }else{
                                Yii::$app->getSession()->setFlash('error', 'No se puede ingresar mas operaciones, favor validar que operaciones faltaron por ingresar.');
                            }    
                        }else{
                            Yii::$app->getSession()->setFlash('info', 'No se puede ingresar mas operaciones del codigo ('.$detalle->id_proceso.') porque supera la cantidad de prendas. Cantidad de prendas: ('.$conCantidad->cantidad.'), cantidad faltante: ('. $total_unidades_faltante .'). Favor validar la informacion de ingreso.');
                        }
                    }    
                    $intIndice++;  
               } 
            }else{
                Yii::$app->getSession()->setFlash('info', 'Debe de seleccionar la fecha de confeccion de la lista para enviar la información.');
                
            }   
        }
         return $this->render('view_search_operario', [
            'model' => $this->findModel($id),
            'id_planta' =>$id_planta,
            'form' => $form,
            'detalle_balanceo' =>  $detalle_balanceo,
            'id_detalle' => $id_detalle,
            'empresa' => $empresa,
            'nombre_modulo' => ArrayHelper::map($nombre_modulo, "id_balanceo", "nombreBalanceo"),
            'listado_tallas' => ArrayHelper::map($listado_tallas, "iddetalleorden", "listadoTalla"),
          
        ]);
    }
   
   //VISTA DE BUSQUEDA DE OPERACION POR TALLAS
    public function actionView_operacion_talla($id, $token) {
        $detalle_orden = \app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $id])->all();
        return $this->render('view_operacion_tallas', [
                'detalle_orden' =>$detalle_orden,
                'id' => $id,
                'token' => $token,
            ]);
    }
   
   //SUMAR UNIDADES CONFECCIONADAS Y VALOR COSTO DE CONFECCION
   protected function SumarCantidadCostoConfeccion($id, $id_detalle, $idordenproduccion) {
       $detalle_global = ValorPrendaUnidadDetalles::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
       $detalle_talla = ValorPrendaUnidadDetalles::find()->where(['=','iddetalleorden', $id_detalle])->all();
       $pago_prenda = ValorPrendaUnidad::findOne($id);
       $detalle_orden = \app\models\Ordenproducciondetalle::findOne($id_detalle);
       $cantidad = 0; $valor = 0;
      //graba global
        foreach ($detalle_global as $detalles):
           $cantidad += $detalles->cantidad;
           $valor += $detalles->costo_dia_operaria;
        endforeach;
        $pago_prenda->cantidad_operacion = $cantidad;
        $pago_prenda->total_confeccion = $valor;
        $pago_prenda->total_pagar = $valor;
        $pago_prenda->save();
        //guarda la cantidad confeccionada
        $cantidad = 0; $valor = 0; 
        foreach ($detalle_talla as $talla):
            $cantidad += $talla->cantidad;
            $valor += $talla->costo_dia_operaria;
        endforeach;
        $detalle_orden->cantidad_confeccionada = $cantidad;
        $detalle_orden->costo_confeccion = $valor;
        $detalle_orden->save();
   }   
    
    //PROCESO QUE CANCULA LA EFICIENCIA DEL OPERARIO
    protected function CalcularEficienciaOperario($operario, $idordenproduccion, $id, $id_detalle) {
        $table = ValorPrendaUnidadDetalles::find()->orderBy('consecutivo DESC')->one();
        $operarios = Operarios::findOne($operario);
        $hora_operario = $operarios->horarios->desde;
        $conMatricula = \app\models\Matriculaempresa::findOne(1);
        $auxiliar = $table->control_fecha;
        $balanceoModulo= \app\models\Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
        $totalHoras = 0; $totalTiempo = 0;
        $total_diario = 0; $sw = 0; $sumarh = 0; $sumarm = 0; $can_minutos = 0; $metaDiaria = 0; $cumplimiento = 0;
        if($operarios->vinculado == 1){
            $this->CostoOperarioVinculado($table, $auxiliar);
            foreach ($balanceoModulo as $modulo):
                if ($table->dia_pago == $modulo->fecha_inicio && $table->hora_inicio_modulo > $hora_operario) {
                    $horad = explode(":", $table->hora_inicio_modulo);
                    $horah = explode(":", $operarios->horarios->hasta);
                    $sumarh = $horah[0] - $horad[0];
                    $sumarm = $horah[1] + $horad[1];
                    $totalTiempo = $sumarh;
                    $totalTiempo = ($sumarh * 60) + $sumarm;
                    $totalTiempo = $totalTiempo / 60;
                    $can_minutos = $table->vlr_prenda / $conMatricula->vlr_minuto_vinculado;
                    $total_diario = round((60 / $can_minutos) * $totalTiempo, 0);
                    $cumplimiento = round(($table->cantidad / $total_diario) * 100, 2);
                    $metaDiaria = round((((60 / $can_minutos) * $totalTiempo) * $conMatricula->porcentaje_empresa) / 100);
                    $sw = 1;
                }
            endforeach;
            if ($sw == 0) {
                $can_minutos = $table->vlr_prenda / $conMatricula->vlr_minuto_vinculado;
                $total_diario = round((60 / $can_minutos) * $operarios->horarios->total_horas, 0);
                $cumplimiento = round(($table->cantidad / $total_diario) * 100, 2);
                $metaDiaria = round((((60 / $can_minutos) * $operarios->horarios->total_horas) * $conMatricula->porcentaje_empresa) / 100);
            }
            $table->porcentaje_cumplimiento = $cumplimiento;
            $table->meta_diaria = $metaDiaria;
            $table->save(false);
        }else{
            //calculo para hallar el % de cumplimiento
            foreach ($balanceoModulo as $modulo):
                if ($table->dia_pago == $modulo->fecha_inicio && $table->hora_inicio_modulo > $hora_operario) {
                    $horad = explode(":", $table->hora_inicio_modulo);
                    $horah = explode(":", $operarios->horarios->hasta);
                    $sumarh = $horah[0] - $horad[0];
                    $sumarm = $horah[1] + $horad[1];
                    $totalTiempo = ($sumarh * 60) + $sumarm;
                    $totalTiempo = $totalTiempo / 60;
                    $can_minutos = $table->vlr_prenda / $conMatricula->vlr_minuto_contrato;
                    $total_diario = round((60 / $can_minutos) * $totalTiempo, 0);
                    $cumplimiento = round(($table->cantidad / $total_diario) * 100, 2);
                    $metaDiaria = round((((60 / $can_minutos) * $totalTiempo) * $conMatricula->porcentaje_empresa) / 100);
                    $sw = 1;
                }
            endforeach;
            if ($sw == 0) {
                $can_minutos = $table->vlr_prenda / $conMatricula->vlr_minuto_contrato;
                $total_diario = round((60 / $can_minutos) * $operarios->horarios->total_horas, 0);
                $cumplimiento = round(($table->cantidad / $total_diario) * 100, 2);
                $metaDiaria = round((((60 / $can_minutos) * $operarios->horarios->total_horas) * $conMatricula->porcentaje_empresa) / 100);
            }
            $table->porcentaje_cumplimiento = $cumplimiento;
            $table->meta_diaria = $metaDiaria;
            $table->save(false);
        }
    }
    
    //PROCESO QUE BUSCA LAS TALLAS DE LA OP.
    
    public function actionSearch_tallas_ordenes($idordenproduccion, $id, $id_planta) {
        $conTallas = \app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
        return $this->render('search_tallas_ordenes', [
            'model' => $this->findModel($id),
            'id_planta' =>$id_planta,
            'conTallas' =>  $conTallas,
           
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
            $model->tipo_proceso_pago = $model->tipo_proceso_pago;
            $model->update();
            return $this->redirect(['index', 'id' => $model->id_valor]);
        }

        return $this->render('create', [
            'model' => $model,
            'orden' => ArrayHelper::map($orden, "idordenproduccion", "ordenValorPrenda"),
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
             $model->tipo_proceso_pago = $model->tipo_proceso_pago;
            $model->update();
            return $this->redirect(['index', 'id' => $model->id_valor]);
        }
        return $this->render('update', [
            'model' => $model,
            'orden' => ArrayHelper::map($orden, "idordenproduccion", "ordenValorPrenda"),
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
    public function actionBuscaroperaciones($id, $idordenproduccion, $id_planta, $id_tipo) {
        
        if (Yii::$app->request->post()) {
            if (isset($_POST["validaroperario"])) {
                if (isset($_POST["id_detalle"])) {
                    $intIndice = 0;
                    $empresa = \app\models\Matriculaempresa::find()->where(['=','id', 1])->one();
                    foreach ($_POST["id_detalle"] as $intCodigo):
                        $detalle_balanceo = \app\models\BalanceoDetalle::findOne($intCodigo);
                        $operarios = Operarios::find()->where(['=','id_operario', $detalle_balanceo->id_operario])->one();
                        $valor = 0;
                        if ($operarios->vinculado == 1){
                           $valor = number_format($detalle_balanceo->minutos * $empresa->vlr_minuto_vinculado, 0);
                           $vinculado = 'Vinculado';
                        }else{
                            $valor = number_format($detalle_balanceo->minutos * $empresa->vlr_minuto_contrato, 0);
                            $vinculado = 'No vinculado';
                        }
                        $prenda = new ValorPrendaUnidadDetalles();
                        $prenda->id_operario = $detalle_balanceo->id_operario;
                        $prenda->id_valor = $id;                
                        $prenda->idordenproduccion = $idordenproduccion;
                        $prenda->dia_pago= date('Y-m-d');
                        $prenda->operacion = 2;
                        $prenda->vlr_prenda = $valor;
                        $prenda->id_planta = $id_planta;
                         $prenda->id_tipo = $id_tipo  ;
                        $prenda->observacion = $vinculado;
                        $prenda->save(false);
                        $intIndice ++;
                    endforeach;
                    return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta]);
                }
            }
        }   
        return $this->renderAjax('_buscaroperacionesmodulo', [
          //  'model' => $model,
            'id' => $id,
            'idordenproduccion' => $idordenproduccion,
            'id_planta' => $id_planta,
            'id_tipo' => $id_tipo,
            ]);
    }
        
    
    
    //PROCESOS Y SUBPROCESOS
    
     public function actionNuevodetalle($id,$idordenproduccion, $id_planta, $tipo_pago)
    {              
        $valor_unidad = ValorPrendaUnidad::findOne($id);
        if($tipo_pago == 1){
            if($valor_unidad->cantidad_operacion > $valor_unidad->cantidad){
               $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta]); 
               Yii::$app->getSession()->setFlash('error', 'No se puede generar mas lineas porque la cantidad de operaciones  '.$valor_unidad->cantidad_operacion.' es mayor que la cantidad del lote '.$valor_unidad->cantidad .'.');  
            }else{
                if($valor_unidad->cantidad_procesada > $valor_unidad->cantidad){
                    $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tipo_pago' => $tipo_pago]); 
                    Yii::$app->getSession()->setFlash('error', 'No se puede generar mas lineas porque la cantidad de confeccion y/o Terminación '.$valor_unidad->cantidad_procesada.' es mayor o igual que la cantidad del lote '.$valor_unidad->cantidad.'.');
                }else{    
                    $model = new ValorPrendaUnidadDetalles();
                    $model->id_valor = $id;                
                    $model->idordenproduccion = $idordenproduccion;
                    $model->dia_pago= date('Y-m-d');
                    $model->id_planta = $id_planta;
                    $model->id_tipo = $valor_unidad->idtipo;
                    if($valor_unidad->id_proceso_confeccion <> 1){
                        $model->operacion = 2;
                    }
                    $model->save(false);
                    return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tipo_pago' => $tipo_pago]);
                }
            }
        }else{
            $model = new ValorPrendaUnidadDetalles();
            $model->id_valor = $id;                
            $model->idordenproduccion = $idordenproduccion;
            $model->dia_pago= date('Y-m-d');
            $model->id_planta = $id_planta;
            $model->id_tipo = $valor_unidad->idtipo;
            if($valor_unidad->id_proceso_confeccion <> 1){
                $model->operacion = 2;
            }
            $model->save(false);
            return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tipo_pago' => $tipo_pago]);
        }    
       
    }
    
    //PROCESO QUE BUSCA EL MODULO Y TRAE LAS EMPLEADOS
    
     public function actionNuevodetallemodular($id, $idordenproduccion, $id_planta)
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
                                 $valor_prenda->id_planta = $id_planta;
                                 $valor_prenda->save(false);
                              }
                        endforeach;
                    }else{
                         $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion,'id_planta' => $id_planta]);
                         Yii::$app->getSession()->setFlash('error', 'La orden de produccion Nro: '. $idordenproduccion. ', no tiene asignado empleados para las operaciones.'); 
                    }    
                }else{
                   $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta]);
                   Yii::$app->getSession()->setFlash('error', 'El modulo de balanceo Nro: '. $val->id_balanceo. ', no realizo confeccion el dia '.$fecha_corte.'. Favor hacer este proceso manual.'); 
                }    
            endforeach;
           return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta]);
        }else{
             $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta]);
             Yii::$app->getSession()->setFlash('error', 'La orden de produccion Nro: '. $idordenproduccion. ', no tiene balanceo creado en sistema.!');
        }
    }
   
    //proceso de carga el pago de nomina
    
    public function actionPagarserviciosoperarios() {
        
        $model = new \app\models\FormPagarServicioOperario();
        $planta = \app\models\PlantaEmpresa::find()->all();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()){
                if (isset($_POST["crearfechaspago"])) {
                   $datosPago = \app\models\PagoNominaServicios::find()->where(['=','fecha_inicio', $model->fecha_inicio])
                                                                      ->andWhere(['=','fecha_corte', $model->fecha_corte])
                                                                      ->andWhere(['=','id_planta', $model->id_planta])->one(); 
                    $fecha_inicio = $model->fecha_inicio;
                    $fecha_corte = $model->fecha_corte;     
                    $bodega = $model->id_planta;     
                    if($datosPago){
                       $this->redirect(["pageserviceoperario", 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' => $bodega]); 
                    }else{
                        $operario = Operarios::find()->where(['=','aplica_nomina_modulo', 1])
                                                     ->andWhere(['=','estado', 1])
                                                     ->andWhere(['=','id_planta', $model->id_planta ])->all();
                       
                        foreach ($operario as $operarios):
                            $tabla = new \app\models\PagoNominaServicios();
                            $tabla->id_operario = $operarios->id_operario;
                            $tabla->documento = $operarios->documento;
                            $tabla->operario = utf8_encode($operarios->nombrecompleto);
                            $tabla->fecha_inicio = $model->fecha_inicio;
                            $tabla->fecha_corte = $model->fecha_corte;
                            $tabla->observacion = $model->observacion;
                            $tabla->id_planta = $operarios->id_planta;
                            $tabla->usuariosistema = Yii::$app->user->identity->username;
                            $tabla->save(false);
                        endforeach;
                       
                         $this->redirect(["pageserviceoperario", 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' => $bodega]); 
                    }    
                }
                 
            }
        }
        
        return $this->renderAjax('pagarserviciosoperario', [
            'model' => $model,  
            'planta' => ArrayHelper::map($planta, "id_planta", "nombre_planta"),
        ]);      
    }
    
    //metodo que llama los pagos del servicio
    
    public function actionPageserviceoperario($fecha_inicio, $fecha_corte, $bodega) {
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
            return $this->render('pageserviceoperario', ['fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' => $bodega]); 
         }
         return $this->render('pageserviceoperario', ['fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' => $bodega]);
    }
    //METODO QUE ACTUALIZA SALDO DE LA NOMINA DE CONFECCION
    
    public function actionActualizarsaldo($fecha_corte, $fecha_inicio, $bodega){
        
        $pago = \app\models\PagoNominaServicios::find()->where(['=','fecha_inicio', $fecha_inicio])->andWhere(['=','fecha_corte', $fecha_corte])
                                                       ->andWhere(['=','id_planta', $bodega])->all(); 
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
        $this->redirect(["pageserviceoperario", 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' => $bodega]); 
    }
    
    //CODIGO QUE AUTORIZA LA NOMINA
    public function actionAutorizarnomina($fecha_corte, $fecha_inicio, $bodega) {
        $pago = \app\models\PagoNominaServicios::find()->where(['=','fecha_inicio', $fecha_inicio])->andWhere(['=','fecha_corte', $fecha_corte])
                                                       ->andWhere(['=','id_planta', $bodega])->orderBy('operario')->all();
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
       $this->redirect(["pageserviceoperario", 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' => $bodega]); 
    }
    
    
    //CODIGO QUE VA AL DETALLE DEL PAGO
    
    public function actionVistadetallepago($id_pago, $fecha_corte, $fecha_inicio, $autorizado, $bodega,  $token = 1) {
        $model = \app\models\PagoNominaServicios::findOne($id_pago);
        $detalle_pago = \app\models\PagoNominaServicioDetalle::find()->where(['=','id_pago', $model->id_pago])->orderBy('devengado asc')->all();
        return $this->render('vista_detalle_pago', [
                    'model' => $model,
                    'detalle_pago' => $detalle_pago,
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_corte' => $fecha_corte,
                    'autorizado' => $autorizado,
                    'id_pago' => $id_pago,
                    'bodega' => $bodega,
                    'token' => $token,
                    
        ]);
    }
    
    //PERMITE CONSULTAR LAS COLILLAS DE PAGO
    public function actionConsultadetallepago($id_pago, $fecha_corte, $fecha_inicio, $bodega, $autorizado, $token = 2) {
        $model = \app\models\PagoNominaServicios::findOne($id_pago);
        $detalle_pago = \app\models\PagoNominaServicioDetalle::find()->where(['=','id_pago', $model->id_pago])->orderBy('devengado asc')->all();
        return $this->render('vista_detalle_pago', [
                    'model' => $model,
                    'detalle_pago' => $detalle_pago,
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_corte' => $fecha_corte,
                    'autorizado' => $autorizado,
                    'id_pago' => $id_pago,
                    'bodega' => $bodega,
                    'token' => $token,
                    
        ]);
    }
    
    
    //ESTE CODIGO EDITAR EL DETALLE DEL PAGO
    public function actionEditarvistadetallepago($id_pago, $id_detalle, $fecha_inicio, $bodega, $fecha_corte, $autorizado) {
        
        $model = \app\models\PagoNominaServicioDetalle::findOne($id_detalle);
        if ($model->load(Yii::$app->request->post())) {
            $tabla = \app\models\PagoNominaServicioDetalle::findOne($id_detalle);
            $tabla->deduccion = $model->deduccion;
            $tabla->devengado = $model->devengado;
            $tabla->save(false);
            return $this->redirect(['valor-prenda-unidad/vistadetallepago','id_pago' => $id_pago, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'autorizado' => $autorizado, 'bodega' => $bodega]);
        }
        return $this->render('editar_vista_detalle_pago', [
            'fecha_corte' => $fecha_corte,
            'fecha_inicio' => $fecha_inicio,
            'model' => $model,
            'id_pago' => $id_pago,
            'bodega' => $bodega,
            'id_detalle' => $id_detalle,
            'autorizado' => $autorizado,
        ]);
    }
   // codigo que permite agregar mas concepto de salario
    
    public function actionImportarconceptosalarios($id_pago, $fecha_inicio, $fecha_corte, $bodega, $autorizado)
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
           $this->redirect(["valor-prenda-unidad/vistadetallepago", 'id_pago' => $id_pago, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' => $bodega, 'autorizado' => $autorizado]);
        }
        return $this->render('importarconceptosalarios', [
            'pilotoDetalle' => $pilotoDetalle,            
            'mensaje' => $mensaje,
            'id_pago' => $id_pago,
            'fecha_inicio' => $fecha_inicio,
            'fecha_corte' => $fecha_corte,
            'autorizado' => $autorizado,
            'bodega' => $bodega,
            'form' => $form,

        ]);
    }
    
    // PROCESO QUE ELIMINE EL DETALLE DEL PAGO
    
     public function actionEliminardetallepago($id_pago,$id_detalle, $fecha_inicio,$bodega, $fecha_corte, $autorizado)
    {                                
        $detalle = \app\models\PagoNominaServicioDetalle::findOne($id_detalle);
        $detalle->delete();
        $this->redirect(["vistadetallepago",'id_pago' => $id_pago, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' =>$bodega, 'autorizado' => $autorizado]);        
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
    
    public function actionEliminar($id,$detalle, $idordenproduccion, $id_planta, $tipo_pago)
    {                                
        $detalle = ValorPrendaUnidadDetalles::findOne($detalle);
        $detalle->delete();
        $this->Totalpagar($id);
        $this->TotalCantidades($id, $tipo_pago);
        $this->redirect(["view",'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta'=> $id_planta, 'tipo_pago' => $tipo_pago]);        
    }
    
    //ELIMINA EL REGISTRO D EPAGO
    
    public function actionEliminarpago($id, $fecha_inicio, $fecha_corte, $bodega)
    {                                
        try {
            $detalle = \app\models\PagoNominaServicios::findOne($id);
            $detalle->delete();
            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con éxito.');
            $this->redirect(["valor-prenda-unidad/pageserviceoperario",'fecha_inicio' => $fecha_inicio, 'fecha_corte' =>$fecha_corte, 'bodega' => $bodega]);
        } catch (IntegrityException $e) {
            $this->redirect(["valor-prenda-unidad/pageserviceoperario",'fecha_inicio' => $fecha_inicio, 'fecha_corte' =>$fecha_corte, 'bodega' => $bodega]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
            $this->redirect(["valor-prenda-unidad/pageserviceoperario",'fecha_inicio' => $fecha_inicio, 'fecha_corte' =>$fecha_corte, 'bodega' => $bodega]);
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
    protected function TotalCantidades($id, $tipo_pago) {
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
            if($tipo_pago == 1){   
                Yii::$app->getSession()->setFlash('error', 'La cantidad y/o operacion procesada es mayor que las unidades entradas en la orden Nro: '. $valor->idordenproduccion. '.');
            }    
        } 
       
    }
    
    public function actionAutorizado($id, $idordenproduccion, $id_planta, $tipo_pago) {
        $model = $this->findModel($id);
        $mensaje = "";
        if($tipo_pago == 1){
            if($model->cantidad_procesada > $model->cantidad  || $model->cantidad_operacion > $model->cantidad){
                $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago]);
                 Yii::$app->getSession()->setFlash('error', 'La cantidad y/o operacion procesada es mayor que las unidades entradas en la orden Nro: '. $model->idordenproduccion. '.');
            }else{  
                if ($model->autorizado == 0) {                        
                    $model->autorizado = 1;            
                    $model->update();
                    $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tipo_pago' => $tipo_pago]);  
                } else{
                    $model->autorizado = 0;
                    $model->update();
                    $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tipo_pago' => $tipo_pago]);  
                }
            }    
        }else{
            if ($model->autorizado == 0) {                        
                $model->autorizado = 1;
                $model->update();
                $this->redirect(["valor-prenda-unidad/search_tallas_ordenes", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago]);
                
            } else {
                $model->autorizado = 0;
                $model->update();
                $this->redirect(["valor-prenda-unidad/search_tallas_ordenes", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago]);
            }
        }    
    }
    
    public function actionCerrarpago($id, $idordenproduccion, $id_planta, $tipo_pago) {
            $model = $this->findModel($id);
            $orden = Ordenproduccion::findOne($idordenproduccion);
            $model->cerrar_pago =  1;
            $model->estado_valor = 1;
            $model->save(false);
            if ($tipo_pago == 1) {
                $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago]);
            } else {
                $this->redirect(["valor-prenda-unidad/search_tallas_ordenes", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago]);
            }
    }
    //cerrar el pago y la orden de produccion
    
    public function actionCerrarpagoorden($id, $idordenproduccion , $id_planta, $tipo_pago) {
           $model = $this->findModel($id);
           $orden = Ordenproduccion::findOne($idordenproduccion);
           $model->cerrar_pago = 1;
           $model->estado_valor = 1;
           $model->save(false);
           $orden->pagada = 1;
           $orden->save(false);
           if ($tipo_pago == 1) {
                $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago]);
            } else {
                $this->redirect(["valor-prenda-unidad/search_tallas_ordenes", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago]);
            }
    }
    
    //PRCESO QUE APLICA REGLA
    
    public function actionAplicar_regla($id, $idordenproduccion, $id_planta) {
        $valores = ValorPrendaUnidadDetalles::find()->where(['=','id_valor', $id])
                                                    ->andWhere(['=','aplica_regla', 0])->andWhere(['=','aplica_sabado', 0])->orderBy('id_operario ASC')->all();
        if (count($valores)== 0){
            $valores = ValorPrendaUnidadDetalles::find()->where(['=','id_valor', $id])
                                            ->andWhere(['=','aplica_sabado', 1])->andWhere(['=','aplica_regla', 0])->orderBy('id_operario ASC')->all(); 
        }
        if (isset($_POST["consecutivo"])) {
            $intIndice = 0;
            $auxiliar = 0; $suma = 0; $total = 0;
            $matricula = \app\models\Matriculaempresa::findOne(1);
            $regla = \app\models\ReglaComisiones::find()->where(['=','estado_regla', 1])->one();
            foreach ($_POST["consecutivo"] as $intCodigo) {
                $table = ValorPrendaUnidadDetalles::findOne($intCodigo);
                $auxiliar = $table->control_fecha;
                $operario = Operarios::findOne($table->id_operario);
                $consulta = ValorPrendaUnidadDetalles::find()->where(['=','id_operario', $operario->id_operario])
                                                             ->andWhere(['=','dia_pago', $table->dia_pago])->all();
                $contador = 0;
                if($table->aplica_regla == 0 && $table->aplica_sabado == 0){
                    foreach ($consulta as $valor){
                       $contador += $valor->porcentaje_cumplimiento;
                    }
                    if($contador > $regla->porcentaje_cumplimiento){
                        foreach ($consulta as $val):
                            $tabla = ValorPrendaUnidadDetalles::findOne($val->consecutivo);
                            if($operario->vinculado == 1){
                                $total =0;
                                $total = (($val->vlr_prenda / $matricula->vlr_minuto_vinculado) *($regla->valor_minuto_vinculado));
                                $tabla->vlr_prenda = round($total,0);
                                $tabla->vlr_pago = $tabla->cantidad * $total;
                                $tabla->aplica_regla = 1;
                                $tabla->save(false);
                            }else{
                                $total =0;
                                $total = (($val->vlr_prenda/$matricula->vlr_minuto_contrato) *($regla->valor_minuto_contrato));
                                $tabla->vlr_prenda = round($total, 0);
                                $tabla->vlr_pago = $tabla->cantidad * $total;
                                $tabla->costo_dia_operaria =  $tabla->vlr_pago;
                                $tabla->aplica_regla = 1;
                                $tabla->save(false);
                            } 
                             $intIndice++;   
                        endforeach;
                    }else{
                        $table->aplica_regla = 1;
                        $table->save(false);
                    }    
                }else{
                    $table->aplica_regla = 1;
                    $table->save(false);
                }
                $intIndice++;   
            }
            $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta]);
        }
        
        return $this->render('_aplicar_regla', [
            'valores' => $valores,            
            'id' => $id,
            'idordenproduccion' => $idordenproduccion,
            'id_planta' => $id_planta,

        ]);
    }
    
    //aplicar porcentaje a al prenda- genera comision adicional
    public function actionAplicarporcentajeprenda()
    {
        if (Yii::$app->user->identity){   
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso', 130])->all()){
                $form = new ModelAplicarPorcentaje();
                $planta = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $porcentaje = null;
                $tipo = null;
                $model = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $planta = Html::encode($form->planta);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $porcentaje = Html::encode($form->porcentaje);
                        $tipo = Html::encode($form->tipo_empleado);
                        $pagos = ValorPrendaUnidadDetalles::find()->where(['=','aplica_sabado', 1])
                                                                  ->andWhere(['=', 'dia_pago', $fecha_inicio])
                                                                  ->andWhere(['=', 'dia_pago', $fecha_corte])
                                                                  ->andWhere(['=', 'id_planta', $planta])
                                                                  ->andWhere(['=', 'aplicar_porcentaje', 0])->all();
                        $model = $pagos;
                    }else {
                        $form->getErrors();
                    }
                } 
                if (isset($_POST["aplicarporcentaje"])) {
                    if (isset($_POST["consecutivo"])) {
                        $intIndice = 0;
                        $calcular = 0;
                        $cont = 0;
                        foreach ($_POST["consecutivo"] as $intCodigo) {
                           $table = ValorPrendaUnidadDetalles::findOne($intCodigo);
                           if($tipo == 0){
                               $calcular = round(($table->vlr_pago * $porcentaje)/100);   
                               $table->vlr_pago = $calcular + $table->vlr_pago; 
                               $table->costo_dia_operaria = $table->vlr_pago;
                               $table->aplicar_porcentaje = 1;
                               $table->save(false);
                               $cont += 1;
                           }else{
                                $calcular = round(($table->vlr_pago * $porcentaje)/100);   
                                $table->vlr_pago = $calcular + $table->vlr_pago; 
                                $table->aplicar_porcentaje = 1;
                                $table->save(false);
                                $cont += 1;
                           }
                        $calcular = 0;  
                        $intIndice++; 
                        }
                        $pagos = ValorPrendaUnidadDetalles::find()->where(['=','aplica_sabado', 1])
                                                                  ->andWhere(['=', 'dia_pago', $fecha_inicio])
                                                                  ->andWhere(['=', 'dia_pago', $fecha_corte])
                                                                  ->andWhere(['=', 'aplicar_porcentaje', 0])->all();
                        $model = $pagos;
                        if($tipo == 0){
                            Yii::$app->getSession()->setFlash('info', 'Se aplico el porcentaje del '.  $porcentaje.'%  a ' . $cont . ' Operarios de la empresa.');
                                return $this->render('aplicarporcentajeprenda', [
                                  'form' => $form,
                                  'model' => $model,
                                  'tipo' => $tipo,
                                ]);
                        }else{
                            Yii::$app->getSession()->setFlash('info', 'Se aplico el porcentaje del '.  $porcentaje.'%  a ' . $cont . '  Operarios de la empresa.');
                                return $this->render('aplicarporcentajeprenda', [
                                  'form' => $form,
                                  'model' => $model,
                                  'tipo' => $tipo,
                                ]);
                        }
                    }
                }   
                //terminac el proceso de aplicar porcentaje
                return $this->render('aplicarporcentajeprenda', [
                       'form' => $form,
                       'model' => $model,
                       'tipo' => $tipo,
               ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
    }
    
     //BUSCAR OPERACIONES POR TALLA
   public function actionSearch_operacion_talla($token = 1) {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',132])->all()){
            $form = new \app\models\FormFiltroConsultaFichaoperacion();
            $idcliente = null;
            $ordenproduccion = null;
            $desde = null;
            $codigoproducto = null;
            $hasta = null;
            $clientes = \app\models\Cliente::find()->orderBy('nombrecorto ASC')->all();
            
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $ordenproduccion = Html::encode($form->ordenproduccion);
                    $codigoproducto = Html::encode($form->codigoproducto);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $table = Ordenproduccion::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['between', 'fechallegada', $desde, $hasta])
                            ->andFilterWhere(['=', 'idordenproduccion', $ordenproduccion])
                            ->andFilterWhere(['=', 'codigoproducto', $codigoproducto])
                            ->orderBy('idordenproduccion desc');
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
                    if(isset($_POST['excel'])){
                        //$table = $table->all();
                        $this->actionExcelconsultaficha($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = Ordenproduccion::find()
                        ->orderBy('idordenproduccion desc');
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
                if(isset($_POST['excel'])){
                        //$table = $table->all();
                        $this->actionExcelconsultaficha($tableexcel);
                }
            }

            return $this->render('index_operacion_tallas', [
                        'model' => $model,
                        'form' => $form,
                        'token' => $token,
                        'pagination' => $pages,
                        'clientes' => ArrayHelper::map($clientes, "idcliente", "nombrecorto"),
            ]);
         }else{
            return $this->redirect(['site/sinpermiso']);
        }
        }else{
            return $this->redirect(['site/login']);
        }
    }
        
   // LISTADO DE OPERACIONES POR TALLA
   public function actionListado_operaciones($id, $id_detalle, $token){
       $model = \app\models\Ordenproducciondetalle::find()->where(['=','iddetalleorden', $id_detalle])->one();
       $operaciones = \app\models\FlujoOperaciones::find()->where(['=','idordenproduccion', $id])->all();
       return $this->render('listado_operacion', [
                        'operaciones' => $operaciones,
                        'id'=> $id,   
                        'model' => $model,
                        'id_detalle' => $id_detalle,
                        'token' => $token,
                                   
            ]);
   }
   
    // LISTADO DE OPERACIONES POR TALLA
    public function actionListado_operarios($id, $id_detalle, $id_operacion, $token){
       $model = \app\models\FlujoOperaciones::find()->where(['=','idproceso', $id_operacion])->one();
       $operaciones = ValorPrendaUnidadDetalles::find()->where(['=','idordenproduccion', $id])
                                                ->andWhere(['=','iddetalleorden', $id_detalle])
                                                ->andwhere(['=','idproceso', $id_operacion])->all();
       return $this->render('listado_operarios', [
                        'operaciones' => $operaciones,
                        'id'=> $id,   
                        'model' => $model,
                        'id_detalle' => $id_detalle,
                        'id_operacion' => $id_operacion,
                        'token' => $token,                                   
            ]);
    }
   //maestro detalle de consulta por opreaciones
     public function actionMaestro_operaciones() {
        $model = new \app\models\FormFiltroMaestroOperaciones();
        //creando vectores de busqueda
        $modelo = [];
        $iddetalleorden = null;
        $orden = Ordenproduccion::find()->orderBy('idordenproduccion DESC')->all();
        $operarios = Operarios::find()->orderBy('nombrecompleto DESC')->all();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $iddetalleorden = $model->iddetalleorden;
            $table = ValorPrendaUnidadDetalles::find()
                        ->andFilterWhere(['=', 'id_operario', $model->id_operario])
                        ->andFilterWhere(['=', 'idordenproduccion', $model->idordenproduccion])
                        ->andFilterWhere(['=', 'iddetalleorden', $model->iddetalleorden]);
            $table = $table->orderBy('consecutivo DESC');
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
                    return $this->render('search_maestro_operaciones', [
                        'model' => $model,
                        'iddetalleorden' => $iddetalleorden,
                        'orden' => ArrayHelper::map($orden, 'idordenproduccion', 'OrdenValorPrenda'),
                        'operarios' => ArrayHelper::map($operarios, 'id_operario', 'nombrecompleto'),
                        'modelo' =>$modelo,
                        ]);

            }
        return $this->render('search_maestro_operaciones', [
                'model' =>$model,
                'iddetalleorden' => $iddetalleorden,
                'modelo' => $modelo,
                'orden' => ArrayHelper::map($orden, 'idordenproduccion', 'OrdenValorPrenda'),
                'operarios' => ArrayHelper::map($operarios, 'id_operario', 'nombrecompleto'),
        ]);
    }
    //llena el combo de las tallas
    //proceso que llena los rack dependiendo el piso
    public function actionLlenar_tallas($id){
        $rows = \app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $id])->all();

        echo "<option value='' required>Seleccione la talla...</option>";
        if(count($rows)>0){
            foreach($rows as $row){
                echo "<option value='$row->iddetalleorden' required>$row->listadoTalla</option>";
            }
        }
    }
    
    //EXPORTA A EXCEL LA CONSULTA DE TODOS LOS PAGOS
     public function actionPagoservicioconfeccion($fecha_corte, $fecha_inicio, $bodega) {        
        $model = \app\models\PagoNominaServicios::find()->where(['=','fecha_inicio', $fecha_inicio])
                                                        ->andWhere(['=','fecha_corte', $fecha_corte])
                                                        ->andWhere(['=','id_planta', $bodega])->orderBy([ 'operario' =>SORT_ASC ])->all();
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
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
                    ->setCellValue('M2', 'OBSERVACION')
                    ->setCellValue('N2', 'PLANTA');
                  
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
                    ->setCellValue('M' . $i, $val->observacion)
                    ->setCellValue('N' . $i, $val->planta->nombre_planta);

              
                   
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
    
   //EXPORTAR LOS OPERARIOS QUE HACEN LAS OPERARACIONES Y LA OPERACION
    public function actionExpotar_cantidad_confeccionada($id, $id_detalle, $id_operacion) {        
        $model = ValorPrendaUnidadDetalles::find()->where(['=','idordenproduccion', $id])
                                                ->andWhere(['=','iddetalleorden', $id_detalle])
                                                ->andwhere(['=','idproceso', $id_operacion])->all();
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
                    ->setCellValue('B2', 'DOCUMENTO')
                    ->setCellValue('C2', 'OPERARIO')
                    ->setCellValue('D2', 'FECHA CONFECCION')
                    ->setCellValue('E2', 'CANTIDAD')
                    ->setCellValue('F2', 'OP INTERNA')
                    ->setCellValue('G2', 'VR. OPERACION')
                    ->setCellValue('H2', 'VR PAGADO')
                    ->setCellValue('I2', '% CUMPLIMIENTO')
                    ->setCellValue('J2', 'CODIGO')
                    ->setCellValue('K2', 'NOMBRE OPERACION')
                    ->setCellValue('L2', 'PLANTA');
                                  
        $i = 3;
        foreach ($model as $val) {                            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->consecutivo)
                    ->setCellValue('B' . $i, $val->operarioProduccion->documento)
                    ->setCellValue('C' . $i, $val->operarioProduccion->nombrecompleto)
                    ->setCellValue('D' . $i, $val->dia_pago)
                    ->setCellValue('E' . $i, $val->cantidad)
                    ->setCellValue('F' . $i, $val->idordenproduccion)
                    ->setCellValue('G' . $i, $val->vlr_prenda)
                    ->setCellValue('H' . $i, $val->vlr_pago)
                    ->setCellValue('I' . $i, $val->porcentaje_cumplimiento)
                    ->setCellValue('J' . $i, $val->idproceso)
                    ->setCellValue('K' . $i, $val->operaciones->proceso)
                    ->setCellValue('L' . $i, $val->planta->nombre_planta);
                   
            $i++;                        
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="Listado_operarios_confeccion.xlsx"');
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
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
                    ->setCellValue('M2', 'OBSERVACION')
                    ->setCellValue('N2', 'PLANTA');
                  
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
                    ->setCellValue('M' . $i, $val->observacion)
                    ->setCellValue('N' . $i, $val->planta->nombre_planta);
              
                   
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
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
                    ->setCellValue('I2', 'VR. COSTO')
                    ->setCellValue('J2', '% CUMPLIMIENTO')
                    ->setCellValue('K2', 'USUARIO')
                    ->setCellValue('L2', 'PLANTA')
                    ->setCellValue('M2', 'OBSERVACION');
                  
        $i = 3;
        $confeccion = 'CONFECCION';
        $operaciones = 'OPERACIONES';
        $ajuste = 'AJUSTE';
        foreach ($model as $val) {                            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $ficha->idordenproduccion)
                    ->setCellValue('B' . $i, $val->operarioProduccion->documento)
                    ->setCellValue('C' . $i, $val->operarioProduccion->nombrecompleto);
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
                    ->setCellValue('I' . $i, $val->costo_dia_operaria)
                    ->setCellValue('J' . $i, $val->porcentaje_cumplimiento)
                    ->setCellValue('K' . $i, $val->usuariosistema)
                    ->setCellValue('L' . $i, $val->planta->nombre_planta)
                    ->setCellValue('M' . $i, $val->observacion);
              
                   
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
    
    public function actionGenerar_excel_talla($id, $id_detalle) {        
        $ficha = ValorPrendaUnidad::findOne($id);
        $model = ValorPrendaUnidadDetalles::find()->where(['=','id_valor',$id])->andWhere(['=','iddetalleorden', $id_detalle])->orderBy('id_operario ASC')->all();
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
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
                    ->setCellValue('I2', 'VR. COSTO')
                    ->setCellValue('J2', '% CUMPLIMIENTO')
                    ->setCellValue('K2', 'USUARIO')
                    ->setCellValue('L2', 'PLANTA')
                    ->setCellValue('M2', 'TALLA')
                    ->setCellValue('N2', 'OPERACION')
                    ->setCellValue('O2', 'OBSERVACION');
                  
        $i = 3;
        $confeccion = 'CONFECCION';
        $operaciones = 'OPERACIONES';
        $ajuste = 'AJUSTE';
        foreach ($model as $val) {                            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $ficha->idordenproduccion)
                    ->setCellValue('B' . $i, $val->operarioProduccion->documento)
                    ->setCellValue('C' . $i, $val->operarioProduccion->nombrecompleto);
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
                    ->setCellValue('I' . $i, $val->costo_dia_operaria)
                    ->setCellValue('J' . $i, $val->porcentaje_cumplimiento)
                    ->setCellValue('K' . $i, $val->usuariosistema)
                    ->setCellValue('L' . $i, $val->planta->nombre_planta)
                    ->setCellValue('M' . $i, $val->detalleOrdenProduccion->productodetalle->prendatipo->talla->talla)
                    ->setCellValue('N' . $i, $val->operaciones->proceso)
                    ->setCellValue('O' . $i, $val->observacion);
              
                   
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

        $objPHPExcel->getActiveSheet()->setTitle('Listados');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="Total_pago_talla.xlsx"');
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
                    ->setCellValue('C' . $i, $val->operarioProduccion->nombrecompleto);
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
