<?php

namespace app\controllers;

use app\models\Ordenproducciondetalleproceso;
use app\models\ProcesoProduccion;
use app\models\Ordenproduccion;
use app\models\Ordenproducciondetalle;
use app\models\OrdenproduccionSearch;
use app\models\Ordenproducciontipo;
use app\models\Cliente;
use app\models\CantidadPrendaTerminadas;
use app\models\FormFiltroOrdenProduccionProceso;
use app\models\FormFiltroConsultaFichaoperacion;
use app\models\FormFiltroConsultaOrdenproduccion;
use app\models\FormFiltroProcesosOperaciones;
use app\models\FormPrendasTerminadas;
use app\models\FlujoOperaciones;
use app\models\Producto;
use app\models\Productodetalle;
use app\models\Balanceo;
use app\models\BalanceoDetalle;
use app\models\UsuarioDetalle;
use app\models\FormFiltroConsultaUnidadConfeccionada;
use app\models\FormFiltroEntradaSalida;
use app\models\SalidaEntradaProduccion;
use app\models\SalidaEntradaProduccionDetalle;
use app\models\FormFiltroOrdenTercero;
use app\models\OrdenProduccionTercero;
use app\models\OrdenProduccionTerceroDetalle;
use app\models\CantidadPrendaTerminadasPreparacion;
use app\models\ReprocesoProduccionPrendas;
use app\models\PilotoDetalleProduccion;
use app\models\EficienciaBalanceo;
use app\models\Color;
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
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;



/**
 * OrdenProduccionController implements the CRUD actions for Ordenproduccion model.
 */
class OrdenProduccionController extends Controller {

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
    public function actionIndex($token = 0) {
         if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',25])->all()){
            $form = new FormFiltroConsultaOrdenproduccion();
            $idcliente = null;
            $desde = null;
            $hasta = null;
            $codigoproducto = null;
            $facturado = null;
            $tipo = null;
            $ordenproduccionint = null;
            $ordenproduccioncliente = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $codigoproducto = Html::encode($form->codigoproducto);
                    $facturado = Html::encode($form->facturado);
                    $tipo = Html::encode($form->tipo);
                    $ordenproduccionint = Html::encode($form->ordenproduccionint);
                    $ordenproduccioncliente = Html::encode($form->ordenproduccioncliente);
                    $table = Ordenproduccion::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['>=', 'fechallegada', $desde])
                            ->andFilterWhere(['<=', 'fechallegada', $hasta])
                            ->andFilterWhere(['=', 'facturado', $facturado])
                            ->andFilterWhere(['=', 'idtipo', $tipo])
                            ->andFilterWhere(['=', 'id_tipo_producto', $ordenproduccionint])
                            ->andFilterWhere(['=', 'codigoproducto', $codigoproducto])
                            ->andFilterWhere(['=', 'ordenproduccion', $ordenproduccioncliente]);
                    $table = $table->orderBy('idordenproduccion desc');
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
                        $this->actionExcelconsulta($tableexcel);
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
                    'pageSize' => 40,
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
                        'token' => $token,
            ]);
        }else{
            return $this->redirect(['site/sinpermiso']);
        }
        }else{
            return $this->redirect(['site/login']);
        }
    }

    //INDEX PARA ASIGNAR TALLA A UNA PLANTA
    
    //PANEL DE PROCESO-CAPACIDAD INSTALADA
    public function actionPanel_procesos() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',149])->all()){
                $form = new \app\models\FormFiltroCapacidadInstalada();
                $minutos = null;
                $horario = null;
                $totalMinutosCarga = null;
                $tipo_servicio = null;
                $total_minutos_carga = null; $capacidad = null;
                $programacionOrdenes = Ordenproduccion::find()->where(['=','facturado', 0])->orderBy('idordenproduccion ASC')->all();
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $horario = Html::encode($form->horario);
                        $tipo_servicio = Html::encode($form->tipo_servicio);
                        if($tipo_servicio == '' && $horario == ''){
                            Yii::$app->getSession()->setFlash('error', 'Debe de seleccionar el HORARIO y el TIPO DE SERVICIO para generar la capacidad instalada..');
                            return $this->redirect(['orden-produccion/panel_procesos']);
                        }else{
                            $horario = \app\models\Horario::findOne($horario);
                            $conOperario = \app\models\Operarios::find()->where(['=','idtipo', $tipo_servicio])->andWhere(['=','estado', 1])->all();
                            $ordenes = Ordenproduccion::find()->where(['=','facturado', 0])->andWhere(['=','idtipo', $tipo_servicio])->all();
                            $totalOperaciones = 0; $tiempoOperacion = 0;
                            $totalConfeccion = 0; $granTotalMinutosConsumidos = 0;
                            $totalMinutosAsignados = 0;
                            $minutosCarga = 0; $tiempoConsumido = 0;
                            foreach ($ordenes as $key => $orden) {
                                $totalMinutosAsignados += $orden->cantidad * $orden->duracion;
                                $minutosCarga += $orden->cantidad * $orden->duracion; 
                                $operaciones = FlujoOperaciones::find()->where(['=','idordenproduccion', $orden->idordenproduccion])->all();
                                foreach ($operaciones as $key => $listado) {
                                    $totalOperaciones += 1;
                                    $tiempoOperacion = $listado->minutos;
                                    $detalle_valor_prenda = \app\models\ValorPrendaUnidadDetalles::find()->where(['=','idordenproduccion', $orden->idordenproduccion])->andWhere(['=','idproceso', $listado->idproceso])->all();
                                    foreach ($detalle_valor_prenda as $key => $detalle) {
                                         $totalConfeccion += $detalle->cantidad;  
                                    }
                                    $granTotalMinutosConsumidos += $totalConfeccion * $tiempoOperacion;
                                    $totalConfeccion = 0;
                                }
                            }
                            $totalMinutosCarga = round($totalMinutosAsignados - $granTotalMinutosConsumidos);
                            $minutos = $horario->total_horas * 60; 
                            $capacidad = count($conOperario) * $minutos;
                            
                            
                        }

                    }
                }
                if(isset($_POST["crear_nueva_fecha"])){
                    if(isset($_POST['listado_programacion'])){
                        $intIndice = 0;
                        foreach ($_POST["listado_programacion"] as $intCodigo):
                            $auxiliar = $_POST["nueva_fecha"][$intIndice];
                            if($auxiliar <> ''){
                                $orden = Ordenproduccion::findOne ($intCodigo);
                                $orden->fechaentrega = $_POST["nueva_fecha"][$intIndice];
                                $orden->save();
                                $intIndice++;
                                return $this->redirect(['orden-produccion/panel_procesos']);
                            }
                            $intIndice++;
                        endforeach;
                    }
                }    
                return $this->render('panel_procesos', [
                                'form' => $form,
                                'total_minutos_carga' => $total_minutos_carga,
                                'minutos' => $minutos,
                                'capacidad' => $capacidad,
                                'totalMinutosCarga' => $totalMinutosCarga,
                                'programacionOrdenes' => $programacionOrdenes,
                                                                
                    ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }
    }
    
    //ASIGNACION
    public function actionIndex_asignacion() {
         if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',133])->all()){
            $form = new FormFiltroConsultaOrdenproduccion();
            $idcliente = null;
            $desde = null;
            $hasta = null;
            $codigoproducto = null;
            $facturado = null;
            $tipo = null;
            $ordenproduccionint = null;
            $ordenproduccioncliente = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $codigoproducto = Html::encode($form->codigoproducto);
                    $facturado = Html::encode($form->facturado);
                    $tipo = Html::encode($form->tipo);
                    $ordenproduccionint = Html::encode($form->ordenproduccionint);
                    $ordenproduccioncliente = Html::encode($form->ordenproduccioncliente);
                    $table = Ordenproduccion::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['>=', 'fechallegada', $desde])
                            ->andFilterWhere(['<=', 'fechallegada', $hasta])
                            ->andFilterWhere(['=', 'facturado', $facturado])
                            ->andFilterWhere(['=', 'idtipo', $tipo])
                            ->andFilterWhere(['=', 'idordenproduccion', $ordenproduccionint])
                            ->andFilterWhere(['=', 'codigoproducto', $codigoproducto])
                            ->andFilterWhere(['=', 'ordenproduccion', $ordenproduccioncliente]);
                    $table = $table->orderBy('idordenproduccion desc');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 60,
                        'totalCount' => $count->count()
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){
                        //$table = $table->all();
                        $this->actionExcelconsulta($tableexcel);
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
                    'pageSize' => 60,
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
            return $this->render('index_asignacion', [
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
    //INDEX DE CONSULTA DE UNDIADES CONFECCIONADAS
    
    public function actionConsultaunidadconfeccionada() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 108])->all()) {
                $form = new FormFiltroConsultaUnidadConfeccionada();
                $id_balanceo = null;
                $idordenproduccion = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $planta = null;
                $pages = null;
                $modelo = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_balanceo = Html::encode($form->id_balanceo);
                        $idordenproduccion = Html::encode($form->idordenproduccion);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $planta = Html::encode($form->planta);
                        $table = CantidadPrendaTerminadas::find()
                                ->andFilterWhere(['=', 'id_balanceo', $id_balanceo])
                                ->andFilterWhere(['=', 'idordenproduccion', $idordenproduccion])
                                ->andFilterWhere(['>=', 'fecha_entrada', $fecha_inicio])
                                ->andFilterWhere(['<=', 'fecha_entrada', $fecha_corte])
                                ->andFilterWhere(['=', 'id_planta', $planta]);
                        $table = $table->orderBy('id_entrada DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 50,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_entrada  DESC']);
                            $this->actionExcelConsultaUnidades($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } 
                return $this->render('consultaunidadconfeccionada', [
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
    
    // INDEX DE ENTRADA Y SALIDAS DE LA OP DEL CLIENTE
     public function actionIndexentradasalida() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 109])->all()) {
                $form = new FormFiltroEntradaSalida();
                $idcliente = null;
                $idordenproduccion = null;
                $fecha_desde = null;
                $fecha_hasta = null;
                $tipo_proceso = null;
                $codigo_producto = null;
                 $tipo_entrada = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $idcliente = Html::encode($form->idcliente);
                        $idordenproduccion = Html::encode($form->idordenproduccion);
                        $fecha_desde = Html::encode($form->fecha_desde);
                        $fecha_hasta = Html::encode($form->fecha_hasta);
                        $tipo_proceso = Html::encode($form->tipo_proceso);
                        $codigo_producto = Html::encode($form->codigo_producto);
                        $tipo_entrada = Html::encode($form->id_entrada_tipo);
                        $table = SalidaEntradaProduccion::find()
                                ->andFilterWhere(['=', 'idcliente', $idcliente])
                                ->andFilterWhere(['=', 'idordenproduccion', $idordenproduccion])
                                ->andFilterWhere(['>=', 'fecha_entrada_salida', $fecha_desde])
                                ->andFilterWhere(['<=', 'fecha_entrada_salida', $fecha_hasta])
                                ->andFilterWhere(['=', 'codigo_producto', $codigo_producto])
                                ->andFilterWhere(['=', 'tipo_proceso', $tipo_proceso])
                                 ->andFilterWhere(['=', 'id_entrada_tipo', $tipo_entrada]);
                        $table = $table->orderBy('id_salida DESC');
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
                            $check = isset($_REQUEST['id_salida  DESC']);
                            $this->actionExcelEntradaSalida($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = SalidaEntradaProduccion::find()
                             ->orderBy('id_salida DESC');
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
                        $this->actionExcelEntradaSalida($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('indexentradasalida', [
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
    
    //orden de produccion para tercero
    
    public function actionIndextercero() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 112])->all()) {
                $form = new FormFiltroOrdenTercero();
                $idproveedor = null;
                $idordenproduccion = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $idtipo = null;
                $idcliente = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $idproveedor = Html::encode($form->idproveedor);
                        $idordenproduccion = Html::encode($form->idordenproduccion);
                        $idtipo = Html::encode($form->idtipo);
                        $idcliente = Html::encode($form->idcliente);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $table = OrdenProduccionTercero::find()
                                ->andFilterWhere(['=', 'idtipo', $idtipo])
                                ->andFilterWhere(['=', 'idordenproduccion', $idordenproduccion])
                                ->andFilterWhere(['>=', 'fecha_proceso', $fecha_inicio])
                                ->andFilterWhere(['=', 'idproveedor', $idproveedor])
                                ->andFilterWhere(['=', 'idcliente', $idcliente])
                                ->andFilterWhere(['<=', 'fecha_proceso', $fecha_corte]);
                        $table = $table->orderBy('id_orden_tercero DESC');
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
                            $check = isset($_REQUEST['id_orden_tercero  DESC']);
                            $this->actionExcelOrdenTercero($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = OrdenProduccionTercero::find()
                             ->orderBy('id_orden_tercero DESC');
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
                        $this->actionExcelOrdenTercero($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('indextercero', [
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
    
    //proceso de permite crear la consulta de reprocesos
      public function actionSearchreprocesos() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 116])->all()) {
                $form = new \app\models\FormFiltroReprocesos();
                $id_operario = null;
                $idordenproduccion = null;
                $fecha_inicio = null;
                $fecha_final = null;
                $id_proceso = null;
                $id_balanceo = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_operario = Html::encode($form->id_operario);
                        $idordenproduccion = Html::encode($form->idordenproduccion);
                        $id_proceso = Html::encode($form->id_proceso);
                        $id_balanceo = Html::encode($form->id_balanceo);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_final = Html::encode($form->fecha_final);
                        $table = ReprocesoProduccionPrendas::find()
                                ->andFilterWhere(['=', 'id_operario', $id_operario])
                                ->andFilterWhere(['=', 'idordenproduccion', $idordenproduccion])
                                ->andFilterWhere(['>=', 'fecha_registro', $fecha_inicio])
                                ->andFilterWhere(['<=', 'fecha_registro', $fecha_final])
                                ->andFilterWhere(['=', 'id_balanceo', $id_balanceo])
                                ->andFilterWhere(['=', 'id_proceso', $id_proceso]);
                        $table = $table->orderBy('id_reproceso DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 100,
                            'totalCount' => $count->count()
                        ]);
                        $model = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_reproceso  DESC']);
                            $this->actionExcelReprocesos($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = ReprocesoProduccionPrendas::find()
                             ->orderBy('id_reproceso DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 100,
                        'totalCount' => $count->count(),
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelReprocesos($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('searchreprocesos', [
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
    
    //vista para orden de produccion
    public function actionView($id, $token) {
        $modeldetalles = Ordenproducciondetalle::find()->Where(['=', 'idordenproduccion', $id])->all();
        $modeldetalle = new Ordenproducciondetalle();
        $mensaje = "";
        $remision = \app\models\Remision::find()->where(['=','idordenproduccion' , $id])->one();
        $novedad_orden = \app\models\NovedadOrdenProduccion::find()->where(['=','idordenproduccion', $id])->all();
        $otrosCostosProduccion = \app\models\OtrosCostosProduccion::find()->where(['=','idordenproduccion', $id])->orderBy('id_proveedor DESC')->all();
        if (isset($_POST["eliminar"])) {
            if (isset($_POST["seleccion"])) {
                foreach ($_POST["seleccion"] as $intCodigo) {
                    try {
                            $eliminar = Ordenproducciondetalle::findOne($intCodigo);
                            $eliminar->delete();
                            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                            $this->redirect(["orden-produccion/view", 'id' => $id, 'token' => $token, 'remision' => $remision]);
                        } catch (IntegrityException $e) {
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                        } catch (\Exception $e) {
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                        }
                }
                $this->Actualizartotal($id);
                $this->Actualizarcantidad($id);
                $this->redirect(["orden-produccion/view", 'id' => $id, 'token' => $token, 'remision' => $remision]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro.');                
            }                        
        }  
        if (isset($_POST["detalle_costo"])) {
             $intIndice = 0;
             foreach ($_POST["detalle_costo"] as $intCodigo) {  
                 $table = \app\models\OtrosCostosProduccion::findOne($intCodigo); 
                 $table->vlr_costo = $_POST["vlr_costo"][$intIndice];
                 $table->save();
                 $intIndice++;
             }
             return $this->redirect(['view', 'id' => $id, 'token' => $token, 'remision' => $remision]);
        }
        return $this->render('view', [
                    'model' => $this->findModel($id),
                    'modeldetalle' => $modeldetalle,
                    'modeldetalles' => $modeldetalles,
                    'mensaje' => $mensaje,
                    'otrosCostosProduccion' => $otrosCostosProduccion,
                    'novedad_orden' => $novedad_orden,
                    'token' => $token ,
                    'remision' => $remision,
        ]);
    }
   
    // vista para salida de produccion
    public function actionViewsalida($id) {
        $modeldetalles = SalidaEntradaProduccionDetalle::find()->Where(['=', 'id_salida', $id])->all();
        $modeldetalle = new SalidaEntradaProduccionDetalle();
        $mensaje = "";
        if (isset($_POST["eliminarsalida"])) {
            if (isset($_POST["seleccion"])) {
                foreach ($_POST["seleccion"] as $intCodigo) {
                    try {
                            $eliminar = Ordenproducciondetalle::findOne($intCodigo);
                            $eliminar->delete();
                            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                            $this->redirect(["orden-produccion/view", 'id' => $id]);
                        } catch (IntegrityException $e) {
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                        } catch (\Exception $e) {
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                        }
                }
                $this->redirect(["orden-produccion/viewsalida", 'id' => $id]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro.');                
            }                        
        }       
        return $this->render('viewsalida', [
                    'model' => SalidaEntradaProduccion::findOne($id),
                    'modeldetalle' => $modeldetalle,
                    'modeldetalles' => $modeldetalles,
                    'mensaje' => $mensaje,
        ]);
    }
    
    // vista para ordenes de tercero
    
    public function actionViewtercero($id) {
        $modeldetalles = OrdenProduccionTerceroDetalle::find()->Where(['=', 'id_orden_tercero', $id])->all();
        $modeldetalle = new OrdenProduccionTerceroDetalle();
        $mensaje = "";
        $model = OrdenProduccionTercero::findOne($id);
        if (isset($_POST["eliminar"])) {
            if (isset($_POST["seleccion"])) {
                foreach ($_POST["seleccion"] as $intCodigo) {
                    try {
                            $eliminar = Ordenproducciondetalle::findOne($intCodigo);
                            $eliminar->delete();
                            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                            $this->redirect(["orden-produccion/view", 'id' => $id]);
                        } catch (IntegrityException $e) {
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                        } catch (\Exception $e) {
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                        }
                }
                $this->Actualizartotal($id);
                $this->Actualizarcantidad($id);
                $this->redirect(["orden-produccion/viewtercero", 'id' => $id]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro.');                
            }                        
        }       
        return $this->render('viewtercero', [
                    'model' => $model,
                    'modeldetalle' => $modeldetalle,
                    'modeldetalles' => $modeldetalles,
                    'mensaje' => $mensaje,
        ]);
    }
    
    //vista para enviar la informacion al balanceo o a la carpeta de 
     public function actionViewreprocesos($id, $idordenproduccion, $indicador) {
        $model = Balanceo::findOne($id); 
        $flujo_operaciones = FlujoOperaciones::find()->where(['=', 'idordenproduccion', $idordenproduccion])->orderBy('operacion, orden_aleatorio asc')->all();
        $balanceo_detalle = BalanceoDetalle::find()->where(['=', 'id_balanceo', $id])->orderBy('id_operario asc')->all();
        $operarios = \app\models\Operarios::find()->where(['=','estado', 1])->orderBy('nombrecompleto ASC');
        return $this->render('viewconsultabalanceo', [
                'flujo_operaciones' => $flujo_operaciones,
                'balanceo_detalle' => $balanceo_detalle,
                'idordenproduccion' => $idordenproduccion,
                'operarios'=> $operarios,
                'indicador' => $indicador,
        ]);
     }
    
    //VISTA DE ASIGNACION DE TALLAS
    public function actionView_asignacion($id) {
        $modeldetalles = Ordenproducciondetalle::find()->where(['=','idordenproduccion', $id])->all();
      return $this->render('view_asignacion', [
                    'model' => $this->findModel($id),
                    'id' => $id,
                    'modeldetalles' => $modeldetalles,
        ]);  
    }
    
    //ASIGNAR TALLA A PLANTA DE PRODUCCION
    public function actionAsignacion_talla($id, $id_detalle) {
        $model = new \app\models\ModelAsignacionTalla();
        $table = Ordenproducciondetalle::findOne($id_detalle);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if (isset($_POST["actualizar_planta"])) { 
                    if($model->todas == '1'){
                        $detalle = Ordenproducciondetalle::find()->where(['=','idordenproduccion', $id])->all();
                        foreach ($detalle as $detalles) {
                            $detalles->id_planta = $model->planta;
                            $detalles->save(false);
                        }
                        $this->redirect(["orden-produccion/view_asignacion", 'id' => $id]);
                    }else {
                        $table->id_planta = $model->planta;
                        $table->save(false);
                        $this->redirect(["orden-produccion/view_asignacion", 'id' => $id]);
                    }    
                    
                } 
            }else{
                $model->getErrors(); 
            }    
        }
         if (Yii::$app->request->get()) {
            $model->planta = $table->id_planta;
         }
        return $this->renderAjax('asignacion_talla', [
            'model' => $model,
            'id_detalle' => $id_detalle,
            'id' => $id,
           
        ]);
       
    }
    
    /**
     * Creates a new Ordenproduccion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Ordenproduccion();
        $clientes = Cliente::find()->orderBy('nombrecorto ASC')->all();
        $codigos = Producto::find()->orderBy('idproducto desc')->all(); 
        $ordenproducciontipos = Ordenproducciontipo::find()->all();
        if ($model->load(Yii::$app->request->post())&& $model->save()) {
            $model->totalorden = 0;
            $model->estado = 0;
            $model->autorizado = 0;
            $model->lavanderia = $model->lavanderia;
            $model->usuariosistema = Yii::$app->user->identity->username;
            $model->update();
            $valor = $model->exportacion;
            $campo = $model->porcentaje_exportacion;
              if($valor == 1){
                  $model->porcentaje_exportacion = 0;
                  $model->update();
              }else{
                  if($valor == 2 && $campo <= 0){
                      Yii::$app->getSession()->setFlash('warning', 'No ingreso el  porcentaje de exportacion ');
                      return $this->redirect(['index']); 
                  }
              }
            $orden = Ordenproduccion::find()->orderBy('idordenproduccion DESC')->one();  
            return $this->redirect(['view', 'id' => $orden->idordenproduccion, 'token' => 0]);
        }

        return $this->render('create', [
               'model' => $model,
                'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreClientes"),
                'ordenproducciontipos' => ArrayHelper::map($ordenproducciontipos, "idtipo", "tipo"),
                'codigos' => ArrayHelper::map($codigos, "codigo", "codigonombre"),
                    
        ]);
    }
    
    //CREA NUEVA ORDEN DE PRODUCCION AUTOMATICA
    public function actionCrear_nueva_orden_produccion($id) {
         if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 154])->all()) {
                $model = new \app\models\FormCrearNuevaOrden();
                if ($model->load(Yii::$app->request->post())) {
                    if (isset($_POST["nueva_orden_produccion"])) {
                        if($model->observacion <> '' && $model->tipo_servicio <> ''){
                            $buscar = Ordenproduccion::findOne($id);
                            if($buscar){
                                $sw = 0;
                                $controlOP = Ordenproduccion::find()->where(['=','codigoproducto', $buscar->codigoproducto])->all();
                                foreach ($controlOP as $control) {
                                    if($model->tipo_servicio == $control->idtipo){
                                        $sw = 1;
                                        Yii::$app->getSession()->setFlash('error', 'Ya esta creada este tipo de orden de produccion. Valide la informacion.');
                                       return $this->redirect(['orden-produccion/index']);  
                                    }
                                }
                                if($sw == 0){
                                    $detalleOrden = Ordenproducciondetalle::find()->where(['=','idordenproduccion', $id])->all();
                                    $table = new Ordenproduccion();
                                    $table->idcliente = $buscar->idcliente;
                                    $table->codigoproducto = $buscar->codigoproducto;
                                    $table->fechallegada = $buscar->fechallegada;
                                    $table->fechaprocesada = $buscar->fechaprocesada;
                                    $table->fechaentrega = $buscar->fechaentrega;
                                    $table->observacion = $model->observacion;
                                    $table->ordenproduccion = $buscar->ordenproduccion;
                                    $table->idtipo = $model->tipo_servicio;
                                    $table->usuariosistema = Yii::$app->user->identity->username;
                                    $table->ordenproduccionext = 0;
                                    $table->aplicar_balanceo = 1;
                                    $table->exportacion = 1;
                                    $table->porcentaje_exportacion = 0;
                                    $table->lavanderia = $buscar->lavanderia;
                                    $table->id_tipo_producto = $buscar->id_tipo_producto;
                                    $table->save(false);
                                    $ultimoNumero = Ordenproduccion::find()->orderBy('idordenproduccion DESC')->one();
                                    //grabar detalle
                                    foreach ($detalleOrden as $key => $detalles) {
                                        $detalle = new Ordenproducciondetalle();
                                        $detalle->idproductodetalle = $detalles->idproductodetalle;
                                        $detalle->codigoproducto = $detalles->codigoproducto;
                                        $detalle->cantidad = $detalles->cantidad;
                                        $detalle->idordenproduccion = $ultimoNumero->idordenproduccion;
                                        $detalle->id_planta = $detalles->id_planta;
                                        $detalle->save(false);
                                    }
                                    return $this->redirect(['orden-produccion/view','id' => $ultimoNumero->idordenproduccion, 'token' => 0]);
                                }
                               
                            }else{
                                Yii::$app->getSession()->setFlash('error', 'La orden de produccion NO se encontro. Valide la informacion.');
                                return $this->redirect(['orden-produccion/index']); 
                            }
                        }else{
                            Yii::$app->getSession()->setFlash('error', 'El campo TIPO ORDEN Y OBSERVACIONES no pueden ser vacios.');
                            return $this->redirect(['orden-produccion/index']);
                        }
                    }
                }
                return $this->renderAjax('crear_nueva_orden_produccion',[
                   'model' =>$model,
               ]);
            }else{
                return $this->redirect(['site/sinpermiso']); 
            }  
        }else{
           return $this->redirect(['site/login']); 
        }        
    }
    
    //NUEVA ORDER PARA TERCERO
    
     public function actionNuevaordentercero() {
        $model = new OrdenProduccionTercero();
        $clientes = Cliente::find()->orderBy('nombrecorto ASC')->all();
        $ordenproducciontipos = Ordenproducciontipo::find()->all();
        $codigos = Producto::find()->orderBy('idproducto desc')->all();        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $orden = Ordenproduccion::find()->where(['=','idcliente', $model->idcliente])
                                           ->andWhere(['=','codigoproducto', $model->codigo_producto])
                                           ->orderBy('idordenproduccion DESC')->one();
                       
            $model->idordenproduccion = $orden->idordenproduccion;
            $model->usuariosistema = Yii::$app->user->identity->username;
            $model->autorizado = 0;
            $model->save();
            $regitro = OrdenProduccionTercero::find()->orderBy('id_orden_tercero DESC')->one();
           return $this->redirect(['viewtercero','id' => $regitro->id_orden_tercero]);
        }

        return $this->render('_formnewtercero', [
                    'model' => $model,
                    'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreClientes"),
                    'ordenproducciontipos' => ArrayHelper::map($ordenproducciontipos, "idtipo", "tipo"),
                    'codigos' => ArrayHelper::map($codigos, "codigo", "codigonombre"),
        ]);
    }
    
    // nuevo salida / entrada
    
    public function actionCreatesalida() {
        $model = new SalidaEntradaProduccion();
        $clientes = Cliente::find()->orderBy('nombrecorto ASC')->all();
        $orden = Ordenproduccion::find()->where(['=','cerrar_orden', 0])->orderBy('idordenproduccion DESC')->all();        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
           $tipo = \app\models\TipoEntrada::findOne($model->id_entrada_tipo);
            $model->usuariosistema = Yii::$app->user->identity->username;
            $orden = Ordenproduccion::findOne($model->idordenproduccion);
            $model->codigo_producto = $orden->codigoproducto;
            if($tipo->genera_cobro == 1){
                $model->servicio_cobrado = 0; 
            }else{
                $model->servicio_cobrado = 1;
            }
            $model->save();
            $salida = SalidaEntradaProduccion::find()->orderBy('id_salida DESC')->one();
            return $this->redirect(['viewsalida','id' => $salida->id_salida]);
        }

        return $this->render('createsalida', [
                    'model' => $model,
                    'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreClientes"),
                    'orden' => ArrayHelper::map($orden, "idordenproduccion", "ordenProduccion"),
        ]);
    }
    
     
    /**
     * Updates an existing Ordenproduccion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $clientes = Cliente::find()->orderBy('nombrecorto ASC')->all();
        $ordenproducciontipos = Ordenproducciontipo::find()->all();
        $codigos = Producto::find()->where(['=','idcliente',$model->idcliente])->all();
        if (Balanceo::find()->where(['=', 'idordenproduccion', $id])->all()) {
            Yii::$app->getSession()->setFlash('warning', 'No se puede modificar la orden de produccion, ya esta en proceso de balanceo.');
        } else {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $valor =  $model->exportacion;
                $campo = $model->porcentaje_exportacion;
                if($valor == 1){
                 $model->porcentaje_exportacion = 0;
                 $model->update();
                }else{
                    if($valor == 2 && $campo <= 0 ){
                        Yii::$app->getSession()->setFlash('warning', 'Debe de ingresar el porcentaje de exportacion ');
                        return $this->redirect(['update','id' => $id]);
                      
                    }
                }
                
               return $this->redirect(['view','id' => $id, 'token' => 0]);
            }
        }
        return $this->render('update', [
                    'model' => $model,
                    'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreClientes"),
                    'ordenproducciontipos' => ArrayHelper::map($ordenproducciontipos, "idtipo", "tipo"),
                    'codigos' => ArrayHelper::map($codigos, "codigo", "codigonombre"),
        ]);
    }
    
    //actualiza el registro de la orden de salida
    public function actionUpdatesalida($id) {
        $model = SalidaEntradaProduccion::findOne($id);
        $clientes = Cliente::find()->orderBy('nombrecorto ASC')->all();
        $orden = Ordenproduccion::find()->orderBy('idordenproduccion DESC')->all(); 
        if (SalidaEntradaProduccionDetalle::find()->where(['=', 'id_salida', $id])->all()) {
            Yii::$app->getSession()->setFlash('warning', 'No se puede modificar la información, tiene detalles asociados');
        } else {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $tipo = \app\models\TipoEntrada::findOne($model->id_entrada_tipo);
                $orden_produccion = Ordenproduccion::find()->where(['=','idordenproduccion', $model->idordenproduccion])->one();
                $model->codigo_producto = $orden_produccion->codigoproducto;
                if($tipo->genera_cobro == 1){
                    $model->servicio_cobrado = 0; 
                }else{
                    $model->servicio_cobrado = 1;
                }
                $model->save();
                return $this->redirect(['viewsalida','id' => $id]);
            }
        }
        return $this->render('updatesalida', [
                    'model' => $model,
                    'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreClientes"),
                  'orden' => ArrayHelper::map($orden, "idordenproduccion", "salidaOrden"),
        ]);
    }
    
    //PERMITE MODIFICAR LA ORDEN DE TERCERO
    public function actionEditarordentercero($id) {
        $model = OrdenProduccionTercero::findOne($id);
        $clientes = Cliente::find()->all();
        $codigos = Producto::find()->orderBy('idproducto desc')->all(); 
        $ordenproducciontipos = Ordenproducciontipo::find()->all();
        if (OrdenProduccionTerceroDetalle::find()->where(['=', 'id_orden_tercero', $id])->all()) {
            Yii::$app->getSession()->setFlash('warning', 'No se puede modificar la información, tiene detalles asociados');
        } else {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $orden_produccion = Ordenproduccion::find()->where(['=','codigoproducto', $model->codigo_producto])->andWhere(['=','idcliente', $model->idcliente])->one();
                $model->idordenproduccion = $orden_produccion->idordenproduccion;
                $model->save(false);
                return $this->redirect(['viewtercero','id' => $id]);
            }
        }
        return $this->render('_formnewtercero', [
                    'model' => $model,
            'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreClientes"),
                    'ordenproducciontipos' => ArrayHelper::map($ordenproducciontipos, "idtipo", "tipo"),
                    'codigos' => ArrayHelper::map($codigos, "codigo", "codigonombre"),
        ]);
    }

    /**
     * Deletes an existing Ordenproduccion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        try {
            $this->findModel($id)->delete();
            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
            $this->redirect(["orden-produccion/index"]);
        } catch (IntegrityException $e) {
            $this->redirect(["orden-produccion/index"]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar la orden de producción, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar la orden de producción, tiene registros asociados en otros procesos');
            $this->redirect(["orden-produccion/index"]);
        }
    }
    
    //PROCESO QUE PERMITE LLAMAR AL PROCESO DE PILOTOS
    
    public function actionNewpilotoproduccion($id, $iddetalle) {
        $sw = 0;
        $orden = Ordenproduccion::findOne($id);
        $detalle_piloto = \app\models\PilotoDetalleProduccion::find()->where(['=','idordenproduccion', $id])
                                                                     ->andWhere(['=','iddetalleorden', $iddetalle])   
                                                           ->orderBy('id_proceso DESC')->all(); 
        if (isset($_POST["actualizarLinea"])) {
            $intIndice = 0;
            $valor = 0;
            foreach ($_POST["listado_piloto"] as $intCodigo) { 
                 $table = PilotoDetalleProduccion::findOne($intCodigo);
                if($orden->proceso_lavanderia == 0 && $orden->lavanderia == 1){
                    if($_POST["medida_ficha_al"][$intIndice] <> null && $_POST["medida_confeccion_al"][$intIndice] <> null){
                        $table->concepto = $_POST["concepto"][$intIndice];
                        $table->medida_ficha_al = $_POST["medida_ficha_al"][$intIndice];
                        $table->medida_confeccion_al = $_POST["medida_confeccion_al"][$intIndice];
                        if($_POST["medida_ficha_al"][$intIndice] >= $_POST["medida_confeccion_al"][$intIndice]){
                            $valor =  $_POST["medida_confeccion_al"][$intIndice] - $_POST["medida_ficha_al"][$intIndice]; 
                            $table->tolerancia_al = $valor;
                            if($valor < -1){ 
                                $table->observacion_al = 'Medidas fuera de la tolerancia';
                            }else{
                                 $table->observacion_al = 'Medidas dentro de la tolerancia';  
                            }
                        }else{
                            $valor =  $_POST["medida_confeccion_al"][$intIndice] - $_POST["medida_ficha_al"][$intIndice]  ; 
                            $table->tolerancia_al = $valor;
                            if($valor > 1){ 
                                $table->observacion_al = 'Medidas fuera de la tolerancia';
                            }else{
                                 $table->observacion_al = 'Medidas dentro de la tolerancia';  
                            } 
                        }    
                    }else{
                         Yii::$app->getSession()->setFlash('warning', 'No se pueden tener campos vacios. Digite el cero.');
                    }   
                     $table->save(false);
                     $intIndice++;
                }else{
                    if($_POST["medida_ficha_dl"][$intIndice] <> null && $_POST["medida_confeccion_dl"][$intIndice]<> null){
                        $table->concepto = $_POST["concepto"][$intIndice];
                        $table->medida_ficha_dl = $_POST["medida_ficha_dl"][$intIndice];
                        $table->medida_confeccion_dl = $_POST["medida_confeccion_dl"][$intIndice];
                        if($_POST["medida_ficha_dl"][$intIndice] >= $_POST["medida_confeccion_dl"][$intIndice]){
                            $valor =  $_POST["medida_confeccion_dl"][$intIndice] - $_POST["medida_ficha_dl"][$intIndice]; 
                            $table->tolerancia_dl = $valor;
                            if($valor < -1){ 
                                $table->observacion_dl = 'Medidas fuera de la tolerancia';
                            }else{
                                 $table->observacion_dl = 'Medidas dentro de la tolerancia';  
                            }
                        }else{
                          $valor =  $_POST["medida_confeccion_dl"][$intIndice] - $_POST["medida_ficha_dl"][$intIndice]  ; 
                            $table->tolerancia_dl = $valor;
                            if($valor > 1){ 
                                $table->observacion_dl = 'Medidas fuera de la tolerancia';
                            }else{
                                 $table->observacion_dl = 'Medidas dentro de la tolerancia';  
                            } 
                        }
                    }else{
                         Yii::$app->getSession()->setFlash('warning', 'No se pueden tener campos vacios. Digite el cero.');
                    } 
                     $table->save(false);
                    $intIndice++;
                }    
               
            }
            return $this->redirect(['newpilotoproduccion', 'id' => $id, 'iddetalle' => $iddetalle]);
        }
        if(isset($_POST['aplicarregistro'])){  
            if(isset($_REQUEST['id_proceso'])){                            
                $intIndice = 0;
                foreach ($_POST["id_proceso"] as $intCodigo) {
                    if ($_POST["id_proceso"][$intIndice]) {
                        $id_detalle = $_POST["id_proceso"][$intIndice];
                        $piloto = PilotoDetalleProduccion::findOne($id_detalle);
                        if($piloto->aplicado == 0){
                            $piloto->aplicado = 1;
                             $piloto->save(false);
                        }else{
                            $piloto->aplicado = 0;
                            $piloto->save(false);
                        }            
                    }
                    $intIndice++;
                }
            }
           return $this->redirect(['newpilotoproduccion', 'id' => $id, 'iddetalle' => $iddetalle]);
        }
        return $this->render('newpilotoproduccion', [
             'id' => $id,
             'iddetalle' => $iddetalle,
             'detalle_piloto' => $detalle_piloto,
            'orden' => $orden,
            
        ]);
    }
    
    //PERMITE CREAR UNA LINEA EN LAS PILOTOS
    
    public function actionNuevalineamedida($iddetalle, $id) {
            $model = new PilotoDetalleProduccion();
            $model->iddetalleorden = $iddetalle;                
            $model->idordenproduccion = $id;
            $model->medida_ficha_al = 0;
            $model->medida_ficha_dl = 0;
            $model->medida_confeccion_al = 0;
            $model->medida_confeccion_dl = 0;
            $model->fecha_registro= date('Y-m-d');
            $model->usuariosistema = Yii::$app->user->identity->username;
            $model->insert(false);
            $detalle_piloto = PilotoDetalleProduccion::find()->where(['=','idordenproduccion', $id])
                                                                     ->andWhere(['=','iddetalleorden', $iddetalle])   
                                                           ->orderBy('id_proceso DESC')->all();
            return $this->redirect(['newpilotoproduccion', 'id' => $id, 'iddetalle' => $iddetalle]);
    }
    //PROCESO DE ENVIA TODAS LAS OPERACIONES A LAS OTRAS TALLAS
    
      public function actionImportarmedidapiloto($id, $iddetalle)
    {
        $orden = Ordenproduccion::findOne($id);
        $pilotoDetalle = PilotoDetalleProduccion::find()->Where(['=','idordenproduccion', $id])
                                                        ->andWhere(['=','aplicado', 1])
                                                        ->orderBy('id_proceso asc')->all();
        $form = new \app\models\FormMaquinaBuscar();
        $q = null;
        $mensaje = '';
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                if ($q){
                    $pilotoDetalle = PilotoDetalleProduccion::find()
                            ->where(['like','concepto',$q])
                            ->orwhere(['like','id_proceso',$q])
                            ->orderBy('concepto asc')
                            ->all();
                }               
            } else {
                $form->getErrors();
            }                    
        } else {
            $pilotoDetalle = PilotoDetalleProduccion::find()->andWhere(['=','idordenproduccion', $id])
                                                        ->andWhere(['=','aplicado', 1])
                                                        ->orderBy('id_proceso asc')->all();
        }
        if (isset($_POST["id_proceso"])) {
            $intIndice = 0;
            foreach ($_POST["id_proceso"] as $intCodigo) {
                $table = new PilotoDetalleProduccion();
                $detalle = PilotoDetalleProduccion::find()->where(['id_proceso' => $intCodigo])->one();
                if($orden->proceso_lavanderia == 0 && $orden->lavanderia == 1){
                    $table->iddetalleorden = $iddetalle;
                    $table->idordenproduccion = $id;
                    $table->concepto = $detalle->concepto;
                    $table->medida_ficha_al = 0;
                    $table->medida_confeccion_al = 0;
                    $table->fecha_registro = date('Y-m-d');
                    $table->usuariosistema = Yii::$app->user->identity->username;
                }else{
                   $table->iddetalleorden = $iddetalle;
                    $table->idordenproduccion = $id;
                    $table->concepto = $detalle->concepto;
                    $table->medida_ficha_dl = 0;
                    $table->medida_confeccion_dl= 0;
                    $table->fecha_registro = date('Y-m-d');
                    $table->usuariosistema = Yii::$app->user->identity->username; 
                }    
                $table->save(false);                                                
            }
           $this->redirect(["orden-produccion/newpilotoproduccion", 'id' => $id, 'iddetalle' => $iddetalle]);
        }else{
           
        }
        return $this->render('importarmedidaproduccion', [
            'pilotoDetalle' => $pilotoDetalle,            
            'mensaje' => $mensaje,
            'id' => $id,
            'iddetalle' => $iddetalle,
            'form' => $form,
            'orden' => $orden,

        ]);
    }
    
    public function actionAutorizado($id, $token) {
        $model = $this->findModel($id);
        if($model->cerrar_orden == 0){
            if ($model->autorizado == 0) {
                $detalles = Ordenproducciondetalle::find()
                        ->where(['=', 'idordenproduccion', $id])
                        ->all();
                $totalcantidad = 0;
                foreach ($detalles as $val){
                    $totalcantidad = $totalcantidad + $val->cantidad;
                }
                $reg = count($detalles);
                if ($reg <> 0) {
                    $model->autorizado = 1;
                    $model->cantidad = $totalcantidad;
                    $model->update();
                    return $this->redirect(["orden-produccion/view", 'id' => $id, 'token' => $token]);
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Para autorizar el registro, debe tener productos relacionados en la orden de producción.');
                    return $this->redirect(["orden-produccion/view", 'id' => $id, 'token' => $token]);
                }
            } else {
                $model->autorizado = 0;
                $model->update();
                return $this->redirect(["orden-produccion/view", 'id' => $id, 'token' => $token]);
            }
        }else{
             Yii::$app->getSession()->setFlash('warning', 'La orden de producción no se puede Desautorizar porque ya se cerro el proceso de balanceo.');
             return $this->redirect(["orden-produccion/view", 'id' => $id, 'token' => $token]);
        }    
    }
    //AUTORIZAR LA NOVEDAD DE PRODUCCION
    public function actionAutorizadonovedad($id_novedad, $id, $token) {
        $model = \app\models\NovedadOrdenProduccion::findOne($id_novedad);
        if($model->autorizado == 0){
            $model->autorizado = 1;
            $model->update();
            return $this->redirect(["orden-produccion/view", 'id' => $id, 'token' => $token]);
        }else{
            $model->autorizado = 0;
            $model->update();
            return $this->redirect(["orden-produccion/view", 'id' => $id, 'token' => $token]);
        }
    }
    
    public function actionAutorizadosalidaentrada($id) {
        $model = SalidaEntradaProduccion::findOne($id);
        $entrada = SalidaEntradaProduccion::findOne($id);
        if($model->total_cantidad > $model->ordenproduccion->cantidad){
           Yii::$app->getSession()->setFlash('warning', 'Las unidades de entrada no pueden ser mayores que la cantidad del lote.'); 
           return $this->redirect(["orden-produccion/viewsalida", 'id' => $id]);
        }else{
            if ($model->autorizado == 0) {
                $model->autorizado = 1;
                $model->update();
                return $this->redirect(["orden-produccion/viewsalida", 'id' => $id]);
            } else {
                $model->autorizado = 0;
                $model->update();
                return $this->redirect(["orden-produccion/viewsalida", 'id' => $id]);
            }
        }    
    }
    
   // proceso par autorizar las ordenes de tercero
    public function actionAutorizadotercero($id) {
        $model = OrdenProduccionTercero::findOne($id);
        $detalle = OrdenProduccionTerceroDetalle::find()->where(['=','id_orden_tercero', $id])->one();
        if($detalle){
            if ($model->autorizado == 0) {
                $model->autorizado = 1;
                $model->update();
                $this->redirect(["orden-produccion/viewtercero", 'id' => $id]);
            } else {
                $model->autorizado = 0;
                $model->update();
                $this->redirect(["orden-produccion/viewtercero", 'id' => $id]);
            }
        }else{
             $this->redirect(["orden-produccion/viewtercero", 'id' => $id]);
            Yii::$app->getSession()->setFlash('info', 'No hay registros en el detalle de la orden de producción para tercero.');
        }    
    }
  // nuevo detalle para las ordenes de produccion
    public function actionNuevodetalles($idordenproduccion, $idcliente, $token) {
        $ordenProduccion = Ordenproduccion::findOne($idordenproduccion);
        //$productosCliente = Productodetalle::find()->where(['=', 'idcliente', $idcliente])->andWhere(['=', 'idtipo', $ordenProduccion->idtipo])->andWhere(['>', 'stock', 0])->all();
        $productocodigo = Producto::find()->where(['=','idcliente',$idcliente])->andWhere(['=','codigo',$ordenProduccion->codigoproducto])->orderBy('idproducto DESC')->one();        
        if ($productocodigo){
            $productosCliente = Productodetalle::find()->where(['=', 'idproducto', $productocodigo->idproducto])->orderBy('idproducto ASC')->all();            
        }else{
            Yii::$app->getSession()->setFlash('error', 'No tiene productos asociados al cliente, por favor verifique si el cliente tiene productos asociados y/o esta mal configurado la orden de produccion, edite la orden');            
            $productosCliente = Productodetalle::find()->where(['=','idproductodetalle',0])->all();            
        }                
        $ponderacion = 0;
        $error = 0;
        $totalorden = 0;
        $cantidad = 0;
        if (isset($_POST["idproductodetalle"])) {
            $intIndice = 0;
            foreach ($_POST["idproductodetalle"] as $intCodigo) {                   
                $detalles = Ordenproducciondetalle::find()
                        ->where(['=', 'idordenproduccion', $idordenproduccion])
                        ->andWhere(['=', 'idproductodetalle', $intCodigo])
                        ->all();
                $reg = count($detalles);
                if ($reg == 0) {                        
                    $table = new Ordenproducciondetalle();
                    $table->idproductodetalle = $_POST["idproductodetalle"][$intIndice];
                    $table->cantidad = $_POST["cantidad"][$intIndice];
                    $table->vlrprecio = $_POST["vlrventa"][$intIndice];
                    $table->codigoproducto = $_POST["codigoproducto"][$intIndice];
                    $table->subtotal = $_POST["cantidad"][$intIndice] * $_POST["vlrventa"][$intIndice];
                    $table->idordenproduccion = $idordenproduccion;
                    $table->ponderacion = $ordenProduccion->ponderacion;
                    $table->insert();                    
                }
                  $intIndice++;  
            }                    
        $this->Actualizartotal($idordenproduccion);
        $this->Actualizarcantidad($idordenproduccion);
        $this->redirect(["orden-produccion/view", 'id' => $idordenproduccion, 'token' => $token]); 
        }                                       
        return $this->render('_formnuevodetalles', [
                    'productosCliente' => $productosCliente,
                    'idordenproduccion' => $idordenproduccion,
                    'ordenProduccion' => $ordenProduccion,
                    'token' => $token,
        ]);
    }
    
    // nuevo detalle para los ordenes de produccion para tercero
    
    public function actionNuevodetallestercero($idordenproduccion, $idcliente, $id) {
        $ordenProduccion = OrdenProduccionTercero::findOne($id);
        $productocodigo = Producto::find()->where(['=','idcliente', $idcliente])->andWhere(['=','codigo',$ordenProduccion->codigo_producto])->one();        
        $detalle_orden = Ordenproducciondetalle::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
            
        $ponderacion = 0;
        $error = 0;
        $totalorden = 0;
        $cantidad = 0;
        if (isset($_POST["idproductodetalle"])) {
            $intIndice = 0;
            foreach ($_POST["idproductodetalle"] as $intCodigo) {                       
                $detalles = OrdenProduccionTerceroDetalle::find()
                        ->where(['=', 'id_orden_tercero', $id])
                        ->andWhere(['=', 'idproductodetalle', $intCodigo])
                        ->all();
                $reg = count($detalles);
                if ($reg == 0) {  
                    if($_POST["cantidad"][$intIndice] > 0){
                        $table = new OrdenProduccionTerceroDetalle();
                        $table->id_orden_tercero = $id;
                        $table->idproductodetalle = $_POST["idproductodetalle"][$intIndice];
                        $table->cantidad = $_POST["cantidad"][$intIndice];
                        $table->vlr_minuto = $ordenProduccion->vlr_minuto;
                        $table->total_pagar =  ($table->cantidad *  $table->vlr_minuto) * $ordenProduccion->cantidad_minutos ;
                        $table->insert(false); 
                    }    
                }
                  $intIndice++;  
            }                    
        $this->ActualizarValorTercero($id);
        $this->redirect(["orden-produccion/viewtercero", 'id' => $id, 'idordenproduccion' => $idordenproduccion]); 
        }                                       
        return $this->render('_formnuevodetallestercero', [
                    'id' => $id,
                    'idordenproduccion' => $idordenproduccion,
                    'detalle_orden' => $detalle_orden,
        ]);
    }
    
    //nueva linea de entrada o salida
    
    public function actionNuevalinea($id, $idordenproduccion, $idcliente) {
        $detalle_orden = Ordenproducciondetalle::find()->where(['=','idordenproduccion', $idordenproduccion])->all();        
        if (isset($_POST["idproductodetalle"])) {
            $intIndice = 0;
            foreach ($_POST["idproductodetalle"] as $intCodigo) {  
                if($_POST["entradasalida"][$intIndice] > 0){
                    $table = new SalidaEntradaProduccionDetalle();
                    $table->idproductodetalle = $_POST["idproductodetalle"][$intIndice];
                    $table->cantidad = $_POST["entradasalida"][$intIndice];
                    $table->id_salida = $id;
                    $table->insert(); 
                }    
                $intIndice++;  
            }                    
            $this->ActualizarCantidadEntradaSalida($id);
           $this->redirect(["orden-produccion/viewsalida", 'idordenproduccion' => $idordenproduccion, 'id' => $id]); 
        }                                       
        return $this->render('_formnuevalinea', [
                    'detalle_orden' => $detalle_orden,
                    'idordenproduccion' => $idordenproduccion,
                    'id' => $id,
        ]);
    }
    
 //proceso que actualiza las cantidades
    protected function ActualizarCantidadEntradaSalida($id)
    {
     $salida_entrada = SalidaEntradaProduccion::findOne($id);    
     $salida = SalidaEntradaProduccionDetalle::find()->where(['=','id_salida', $id])->all();
     $suma = 0;
     foreach ($salida as $valor):
         $suma += $valor->cantidad;
     endforeach;
     $salida_entrada->total_cantidad = $suma;
     $salida_entrada->save(false);
    }
    
    public function actionEditardetalleorden($token) {
        $iddetalleorden = Html::encode($_POST["id_detalleorden"]);
        $idordenproduccion = Html::encode($_POST["idordenproduccion"]);
        $error = 0;
        if (Yii::$app->request->post()) {
            if ((int) $iddetalleorden) {
                $table = Ordenproducciondetalle::findOne($iddetalleorden);
                $producto = Producto::findOne($table->idproductodetalle);
                if ($table) {
                   $table->cantidad = Html::encode($_POST["cantidad"]);
                    $table->vlrprecio = Html::encode($_POST["vlrprecio"]);
                    $table->subtotal = Html::encode($_POST["cantidad"]) * Html::encode($_POST["vlrprecio"]);
                    $table->idordenproduccion = Html::encode($_POST["idordenproduccion"]);

                    $ordenProduccion = Ordenproduccion::findOne($table->idordenproduccion);
                    $ordenProduccion->totalorden = $ordenProduccion->totalorden - Html::encode($_POST["subtotal"]);
                    $ordenProduccion->totalorden = $ordenProduccion->totalorden + $table->subtotal;
                                            
                    $table->save(false);
                    $ordenProduccion->update();
                    $this->Actualizarcantidad($idordenproduccion);
                    $this->redirect(["orden-produccion/view", 'id' => $idordenproduccion, 'token' => $token]);
                                            
                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }
            }
        }
        
    }
    //ESTE PROCESO EDITA UN REGISTRO DE LA ORDEN DE TERCERO
  public function actionEditardetalletercero() {
        $id_detalle = Html::encode($_POST["id_detalle"]);
        $id = Html::encode($_POST["id"]);
        $error = 0;
        if (Yii::$app->request->post()) {
            if ((int) $id_detalle) {
                $table = OrdenProduccionTerceroDetalle::findOne($id_detalle);
                $total = 0;
                if ($table) {
                    $orden = OrdenProduccionTercero::findOne($id);
                    $table->cantidad = Html::encode($_POST["cantidad"]);
                    $table->total_pagar = ($table->vlr_minuto * $orden->cantidad_minutos) * $table->cantidad;
                    $table->save(false);
                    $this->ActualizarValorTercero($id);
                    $this->redirect(["orden-produccion/viewtercero", 'id' => $id]);
                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }
            }
        }
    }  
    
    //Editar detalles de la salida y entrada
    public function actionEditardetallesalida($id) {
        $mds = SalidaEntradaProduccionDetalle::find()->where(['=', 'id_salida', $id])->all();
         $entrada = SalidaEntradaProduccion::findOne($id);
        $error = 0;
        if (isset($_POST["consecutivo"])) {
            $intIndice = 0;
            foreach ($_POST["consecutivo"] as $intCodigo) {
                if ($_POST["cantidad"][$intIndice] > 0) {
                    $table = SalidaEntradaProduccionDetalle::findOne($intCodigo);
                    $table->cantidad = $_POST["cantidad"][$intIndice];
                    $table->update();                        
                }
                $intIndice++;
            }
         
            $this->ActualizarCantidadEntradaSalida($id);
            $this->redirect(["orden-produccion/viewsalida", 'id' => $id]);            
        }
        return $this->render('_formeditardetallesalida', [
                    'mds' => $mds,
                    'id' => $id,
                    'entrada' => $entrada,
        ]);
    }
    
    //EDITAR ENTRADA DE PRENDAS CONFECCIONADAS
    public function actionEditarentrada($id_proceso_confeccion)
    {
        $identrada = Html::encode($_POST["identrada"]);
        $iddetalleorden = Html::encode($_POST["iddetalleorden"]);
        $error = 0;
        if (Yii::$app->request->post()) {
            if ((int) $identrada) {
                if($id_proceso_confeccion == 1){
                     $table = CantidadPrendaTerminadas::findOne($identrada);
                }else{
                    $table = CantidadPrendaTerminadasPreparacion::findOne($identrada);
                }     
                if ($table) {
                    $table->cantidad_terminada = Html::encode($_POST["cantidad_terminada"]);
                    $table->observacion = Html::encode($_POST["observacion"]);
                   $table->update();
                   $this->redirect(["orden-produccion/vistatallas", 'iddetalleorden' => $iddetalleorden]);
                                            
                } else {
                    Yii::$app->getSession()->setFlash('warnig', 'No se encotro ningun registo para actualizar.');
                }
            }
        }
        
    }
    
   //EDITAR DETALLES ORDEN DE PRODUCCION
    public function actionEditardetalles($idordenproduccion, $token) {
        $mds = Ordenproducciondetalle::find()->where(['=', 'idordenproduccion', $idordenproduccion])->all();
        $error = 0;
        if (isset($_POST["id_detalleorden"])) {
            $intIndice = 0;
            foreach ($_POST["id_detalleorden"] as $intCodigo) {
                if ($_POST["cantidad"][$intIndice] > 0) {
                    $table = Ordenproducciondetalle::findOne($intCodigo);
                    $table->cantidad = $_POST["cantidad"][$intIndice];
                    $table->vlrprecio = $_POST["vlrprecio"][$intIndice];
                    $table->subtotal = $_POST["cantidad"][$intIndice] * $_POST["vlrprecio"][$intIndice];                    
                    $table->save(false);                        
                }
                $intIndice++;
            }
            $this->Actualizartotal($idordenproduccion);
            $this->Actualizarcantidad($idordenproduccion);
            $this->redirect(["orden-produccion/view", 'id' => $idordenproduccion, 'token' => $token]);            
        }
        return $this->render('_formeditardetalles', [
                    'mds' => $mds,
                    'idordenproduccion' => $idordenproduccion,
                    'token' => $token,    
        ]);
    }
    
    //EDITAR DETALLES ORDEN DE PRODUCCION TERCERO
    public function actionEditardetallestercero($id) {
        $mds = OrdenProduccionTerceroDetalle::find()->where(['=', 'id_orden_tercero', $id])->all();
        $orden_tercero = OrdenProduccionTercero::findOne($id);
        $error = 0;
        if (isset($_POST["id_detalle"])) {
            $intIndice = 0;
            foreach ($_POST["id_detalle"] as $intCodigo) {
                if ($_POST["cantidad"][$intIndice] > 0) {
                    $table = OrdenProduccionTerceroDetalle::findOne($intCodigo);
                    $table->cantidad = $_POST["cantidad"][$intIndice];
                    $table->total_pagar = round(($_POST["cantidad"][$intIndice] * $orden_tercero->vlr_minuto) *$orden_tercero->cantidad_minutos,0) ;                    
                    $table->update();                        
                }
                $intIndice++;
            }
              $this->ActualizarValorTercero($id);
            $this->redirect(["orden-produccion/viewtercero", 'id' => $id]);            
        }
        return $this->render('_formeditardetallestercero', [
                    'mds' => $mds,
                    'id' => $id,
                    'orden_tercero' => $orden_tercero,
        ]);
    }
    
    ///PERMITE MODIFICAR UNA LINEA DEL DETALLE DE ENTRADA Y SALIDA
   public function actionEditardetallesalidaunico() {
        $consecutivo = Html::encode($_POST["consecutivo"]);
        $id = Html::encode($_POST["id_salida"]);
        $error = 0;
        if (Yii::$app->request->post()) {
            if ((int) $consecutivo) {
                $table = SalidaEntradaProduccionDetalle::findOne($consecutivo);
                if ($table) {
                    $table->cantidad = Html::encode($_POST["cantidad"]);
                    $table->update();
                    $this->ActualizarCantidadEntradaSalida($id);
                    $this->redirect(["orden-produccion/viewsalida", 'id' => $id]);
                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }
            }
        }
    }
    
  
    //editar flujo de operaciones
    
    public function actionEditarflujooperaciones($idordenproduccion) {
        $mds = FlujoOperaciones::find()->where(['=', 'idordenproduccion', $idordenproduccion])->orderBy('pieza ASC, operacion ASC, orden_aleatorio ASC')->all();
        $error = 0;
        $detalle_Op = Ordenproducciondetalle::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
        $orden = Ordenproduccion::findOne($idordenproduccion);
        if (isset($_POST["id"])) {
            $intIndice = 0; $registro = 0;
            $suma_balanceo = 0;
            $suma_preparacion = 0;
            $sam_operativo = 0;
            foreach ($_POST["id"] as $intCodigo) {
                    $table = FlujoOperaciones::findOne($intCodigo);
                    $table->orden_aleatorio = $_POST["orden_aleatorio"][$intIndice];
                    $table->operacion = $_POST["operacionflujo"][$intIndice];
                    $table->id_tipo = $_POST["id_tipo"][$intIndice];
                    $table->pieza = $_POST["pieza"][$intIndice];
                    $table->save(false); 
                    if($_POST["operacionflujo"][$intIndice] == 0){
                         $suma_balanceo += $_POST["sam_balanceo"][$intIndice]; 
                    }else{
                        $suma_preparacion += $_POST["sam_balanceo"][$intIndice];
                    }
                    $sam_operativo += $_POST["sam_balanceo"][$intIndice];
                    $orden->sam_balanceo = $suma_balanceo;
                    $orden->sam_preparacion = $suma_preparacion;
                    $orden->sam_operativo = $sam_operativo;
                    $orden->save(false);
                $intIndice++;
            }
           
                $registro = count($mds);
                foreach ($detalle_Op as $detalle):
                    $detalle->cantidad_operaciones = $detalle->cantidad * $registro;
                    $detalle->save();
                endforeach;
            
            
          $this->redirect(["orden-produccion/view_balanceo", 'id' => $idordenproduccion]);            
        }
        return $this->render('_formeditarflujooperaciones', [
                    'mds' => $mds,
                    'idordenproduccion' => $idordenproduccion,
        ]);
    }
          
    //balanceo de prendas
    
    public function actionBalanceoprenda($idordenproduccion) {
        $mds = FlujoOperaciones::find()->where(['=', 'idordenproduccion', $idordenproduccion])->orderBy('id_tipo DESC')->all();
        $error = 0;
        if (isset($_POST["id"])) {
            $intIndice = 0;
            foreach ($_POST["id"] as $intCodigo) {
                if ($_POST["orden_aleatorio"][$intIndice] > 0) {
                    $table = FlujoOperaciones::findOne($intCodigo);
                    $table->orden_aleatorio = $_POST["orden_aleatorio"][$intIndice];
                    $table->update();                        
                }
                $intIndice++;
            }
            $this->redirect(["orden-produccion/view_balanceo", 'id' => $idordenproduccion]);            
        }
        return $this->render('_formbalanceoprenda', [
                    'mds' => $mds,
                    'idordenproduccion' => $idordenproduccion,
        ]);
    }

    public function actionEliminardetalle($token) {
        if (Yii::$app->request->post()) {
            $iddetalleorden = Html::encode($_POST["iddetalleorden"]);
            $idordenproduccion = Html::encode($_POST["idordenproduccion"]);
            if ((int) $iddetalleorden) {
                $ordenProduccionDetalle = OrdenProduccionDetalle::findOne($iddetalleorden);
                $subtotal = $ordenProduccionDetalle->subtotal;
                
                try {
                    OrdenProduccionDetalle::deleteAll("iddetalleorden=:iddetalleorden", [":iddetalleorden" => $iddetalleorden]);
                    $this->Actualizartotal($idordenproduccion);
                    $this->Actualizarcantidad($idordenproduccion);
                    $this->redirect(["orden-produccion/view", 'id' => $idordenproduccion, 'token' => $token]);
                } catch (IntegrityException $e) {
                    $this->redirect(["orden-produccion/view", 'id' => $idordenproduccion, 'token' => $token]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en ficha de operaciones');
                } catch (\Exception $e) {
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en ficha de operaciones');
                    $this->redirect(["orden-produccion/view", 'id' => $idordenproduccion, 'token' => $token]);
                }
                
                
            } else {
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("orden-produccion/index") . "'>";
            }
        } else {
            return $this->redirect(["orden-produccion/index"]);
        }
    }
    
    //ELIMINA LOS DETALLES DE LA ENTRADA  Y SALIDA
    
    public function actionEliminardetallesalida() {
        if (Yii::$app->request->post()) {
            $consecutivo = Html::encode($_POST["consecutivo"]);
            $id = Html::encode($_POST["id_salida"]);
            if ((int) $id) {
                $Detalle = SalidaEntradaProduccionDetalle::findOne($consecutivo);
                try {
                    SalidaEntradaProduccionDetalle::deleteAll("consecutivo=:consecutivo", [":consecutivo" => $consecutivo]);
                    $this->ActualizarCantidadEntradaSalida($id);
                    $this->redirect(["orden-produccion/viewsalida", 'id' => $id]);
                } catch (IntegrityException $e) {
                    $this->redirect(["orden-produccion/viewsalida", 'id' => $id]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en ficha de operaciones');
                } catch (\Exception $e) {
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en ficha de operaciones');
                    $this->redirect(["orden-produccion/viewsalida", 'id' => $id]);
                }
            } else {
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("orden-produccion/index") . "'>";
            }
        } else {
            return $this->redirect(["orden-produccion/index"]);
        }
    }
    
    //ELIMINA EL DETALLE DE LAS PILOTOS
    public function actionEliminardetallepiloto($id_proceso, $id, $iddetalle) {
       if (Yii::$app->request->post()) {
            $piloto = PilotoDetalleProduccion::findOne($id_proceso);
            if ((int) $id_proceso) {
                try {
                    PilotoDetalleProduccion::deleteAll("id_proceso=:id_proceso", [":id_proceso" => $id_proceso]);
                                    
                    $this->redirect(["orden-produccion/newpilotoproduccion",'iddetalle'=>$iddetalle, 'id'=>$id]);
                } catch (IntegrityException $e) {
                    $this->redirect(["orden-produccion/newpilotoproduccion",'iddetalle'=>$iddetalle, 'id'=>$id]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar al eliminar el registro.!');
                } catch (\Exception $e) {

                    $this->redirect(["orden-produccion/newpilotoproduccion",'iddetalle'=>$iddetalle, 'id'=>$id]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar al eliminar el registro.!');
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el registros, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute(["orden-produccion/newpilotoproduccion",'iddetalle'=>$iddetalle, 'id'=>$id]) . "'>";
            }
        } else {
             $this->redirect(["orden-produccion/newpilotoproduccion",'iddetalle'=>$iddetalle, 'id'=>$id]);
        }
    }
  
    
    //ELIMINAR DETALLES DE LA ORDEN DE PRODUCCION PARA TERCERO
    
   
    public function actionEliminardetalles($idordenproduccion, $token) {
        $mds = Ordenproducciondetalle::find()->where(['=', 'idordenproduccion', $idordenproduccion])->all();
        $mensaje = "";
        $error = 0;
        if (Yii::$app->request->post()) {
            $intIndice = 0;

            if (isset($_POST["seleccion"])) {
                foreach ($_POST["seleccion"] as $intCodigo) {
                    $ordenProduccionDetalle = OrdenProduccionDetalle::findOne($intCodigo);
                    $subtotal = $ordenProduccionDetalle->subtotal;
                    $cantidad = $ordenProduccionDetalle->cantidad;
                    
                    try {
                        OrdenProduccionDetalle::findOne($intCodigo)->delete();
                        $this->Actualizartotal($idordenproduccion);
                        $this->Actualizarcantidad($idordenproduccion);
                        //$this->redirect(["orden-produccion/view", 'id' => $idordenproduccion]);
                    } catch (IntegrityException $e) {
                        //$this->redirect(["orden-produccion/view", 'id' => $idordenproduccion]);
                        Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en ficha de operaciones');
                        $error = 1;
                    } catch (\Exception $e) {
                        Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en ficha de operaciones');
                        $error = 1;
                    }
                    
                }
                if($error == 1){
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en ficha de operaciones');
                }else{
                    $this->redirect(["orden-produccion/view", 'id' => $idordenproduccion, 'token' => $token]);
                }
                
            } else {
                $mensaje = "Debe seleccionar al menos un registro";
            }
        }
        return $this->render('_formeliminardetalles', [
                    'mds' => $mds,
                    'idordenproduccion' => $idordenproduccion,
                    'mensaje' => $mensaje,
                    'token' => $token,
        ]);
    }

    //ELIMINAR DETALLES MAXIVO DE LA ORDEN DE TERCERO
    
    public function actionEliminardetallesordenterceromasivo($id) {
        $mds = OrdenProduccionTerceroDetalle::find()->where(['=', 'id_orden_tercero', $id])->all();
        $orden_tercero = OrdenProduccionTercero::findOne($id);
        $mensaje = "";
        
        if (Yii::$app->request->post()) {
            $intIndice = 0;

            if (isset($_POST["seleccion"])) {
                foreach ($_POST["seleccion"] as $intCodigo) {
                    try {
                        OrdenProduccionTerceroDetalle::findOne($intCodigo)->delete();
                        $this->ActualizarValorTercero($id);
                        $this->redirect(["orden-produccion/viewtercero", 'id' => $id]);
                    } catch (IntegrityException $e) {
                        //$this->redirect(["orden-produccion/view", 'id' => $idordenproduccion]);
                        Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados.');
                       
                    } catch (\Exception $e) {
                        Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados.');
                        
                    }
                    
                }
                
            } else {
                $mensaje = "Debe seleccionar al menos un registro para ejecutar el proceso.";
            }
        }
        return $this->render('_formeliminardetallesordentercero', [
                    'mds' => $mds,
                    'id' => $id,
                    'mensaje' => $mensaje,
                    'orden_tercero' => $orden_tercero,
        ]);
    }
    
    public function actionEliminardetalletercero() {
        if (Yii::$app->request->post()) {
            $id_detalle = Html::encode($_POST["id_detalle_orden"]);
            $id = Html::encode($_POST["id"]);
            if ((int) $id) {
                $Detalle = OrdenProduccionTerceroDetalle::findOne($id_detalle);
                try {
                    OrdenProduccionTerceroDetalle::deleteAll("id_detalle=:id_detalle", [":id_detalle" => $id_detalle]);
                    $this->ActualizarValorTercero($id);
                    $this->redirect(["orden-produccion/viewtercero", 'id' => $id]);
                } catch (IntegrityException $e) {
                    $this->redirect(["orden-produccion/viewtercero", 'id' => $id]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otro proceso');
                } catch (\Exception $e) {
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados  en otro proceso');
                    $this->redirect(["orden-produccion/viewtercero", 'id' => $id]);
                }
                
                
            } else {
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("orden-produccion/indextercero") . "'>";
            }
        } else {
            return $this->redirect(["orden-produccion/indextercero"]);
        }
    }
    
    //CERRAR PROCESO DE LAVANDERIA
    public function actionCerrar_medidas_pilotos($id) {
         $model = new \app\models\FormCerrarMedidasPilotos();
         $orden = Ordenproduccion::findOne($id);
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) { 
              if (isset($_POST["enviar_proceso"])) {
                  $orden->proceso_lavanderia = $model->proceso_lavanderia;
                  $orden->proceso_sin_lavanderia = $model->proceso_sin_lavanderia;
                  $orden->save();
                 return $this->redirect(["orden-produccion/view_detalle", 'id' => $id]);
              }
            
        }
        if (Yii::$app->request->get("id")) {
            $model->proceso_lavanderia = $orden->proceso_lavanderia;                
            $model->proceso_sin_lavanderia = $orden->proceso_sin_lavanderia; 
        }
        return $this->renderAjax('cerrar_medida_pilotos', [
            'model' => $model,
            'id' => $id,
            ]);
    }
    
   
   //IMPRIMIR ORDEN DE CONFECCION
    public function actionImprimir($id) {

        return $this->render('../formatos/ordenProduccion', [
                    'model' => $this->findModel($id),
        ]);
    }
     public function actionImprimirpilotos($id) {
       $orden = Ordenproduccion::findOne($id);
       $piloto = PilotoDetalleProduccion::find()->where(['=','idordenproduccion', $id])->one();
       if($piloto){  
            return $this->render('../formatos/reporteentregapilotos', [
                    'model' => $this->findModel($id),
          ]);
       }else{
          $this->redirect(["orden-produccion/view_detalle", 'id' => $id]);
          Yii::$app->getSession()->setFlash('warning', 'La referencia ('.$orden->codigoproducto.') no se le ha creado el proceso de medidas a las pilotos por sistemas. Favor validar con producción.');
       }
       
    }
    //IMPRIME MEDIDAS DE PILOTOS SIN LAVANDERIA
     public function actionImprimir_pilotos_dl($id) {
        $model = OrdenProduccion::findOne($id);
        return $this->render('../formatos/reporte_medidas_sinlavanderia', [
                    'model' => $model,
        ]);
    }
    
     public function actionImprimirtercero($id) {
        $model = OrdenProduccionTercero::findOne($id);
        return $this->render('../formatos/ordenproducciontercero', [
                    'model' => $model,
        ]);
    }
     public function actionImprimirsalida($id) {
        $model = SalidaEntradaProduccion::findOne($id);       
        return $this->render('../formatos/salidaEntrada', [
                    'model' => $model,
        ]);
    }
    
    public function actionImprimirficha($id,$iddetalleorden) {

        return $this->render('../formatos/fichaOperaciones', [
                    'model' => $this->findModel($id),
                    'iddetalleorden' => $iddetalleorden
        ]);
    }
    
   public function actionImprimirexportacion($id) {
        return $this->render('../formatos/reporteexportacion', [
                    'model' => $this->findModel($id),
        ]);
    }
    
    //IMPRIME EL INFORME DE NOVEDAD
    
    public function actionImprimirnovedadorden($id_novedad, $id)
    {                                
       
         return $this->render('../formatos/impresionnovedadproduccion', [
              'model' => \app\models\NovedadOrdenProduccion::findOne($id_novedad),
             'id' => $id,
             'id_novedad' => $id_novedad,
        ]);
    }
    
    
    protected function findModel($id) {
        if (($model = Ordenproduccion::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    protected function Actualizarcantidad($idordenproduccion) {
        $ordenProduccion = Ordenproduccion::findOne($idordenproduccion);
        $ordenproducciondetalle = Ordenproducciondetalle::find()->where(['=','idordenproduccion',$idordenproduccion])->all();
        $cantidad = 0;
        foreach ($ordenproducciondetalle as $val) {
            $cantidad += $val->cantidad;
        }        
        $ordenProduccion->cantidad = $cantidad;
        $ordenProduccion->faltante = $cantidad;
        $ordenProduccion->update();
    }
    
    protected function Actualizartotal($idordenproduccion) {
        $ordenProduccion = Ordenproduccion::findOne($idordenproduccion);
        $ordenproducciondetalle = Ordenproducciondetalle::find()->where(['=','idordenproduccion',$idordenproduccion])->all();
        $total = 0;
        foreach ($ordenproducciondetalle as $val) {
            $total += $val->subtotal;
        }        
        $ordenProduccion->totalorden = round($total,0);
        $ordenProduccion->update();
    }
    
   //SUBPROCESO QUE ACTUALIZA EL VALOR A PAGAR LA ORDEN PARA EL TERCERO
    protected function ActualizarValorTercero($id) {
        $ordenProduccion = OrdenProduccionTercero::findOne($id);
        $ordenproducciondetalle = OrdenProduccionTerceroDetalle::find()->where(['=','id_orden_tercero',$id])->all();
        $total = 0; $total_unidad = 0;
        foreach ($ordenproducciondetalle as $val) {
            $total = $total + $val->total_pagar;
            $total_unidad += $val->cantidad;
        }        
        $ordenProduccion->total_pagar = round($total,0);
        $ordenProduccion->cantidad_unidades = round($total_unidad,0);
        $ordenProduccion->update();
    }
    
    public function actionProceso() {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',28])->all()){
            $form = new FormFiltroOrdenProduccionProceso();
            $idcliente = null;
            $ordenproduccion = null; $grupo = null;
            $idtipo = null;
            $codigoproducto = null; $orden= null;
            $clientes = Cliente::find()->all();
            $ordenproducciontipos = Ordenproducciontipo::find()->all();
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $ordenproduccion = Html::encode($form->ordenproduccion);
                    $idtipo = Html::encode($form->idtipo);
                    $codigoproducto = Html::encode($form->codigoproducto);
                    $grupo = Html::encode($form->grupo);
                    $orden = Html::encode($form->orden);
                    $table = Ordenproduccion::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['like', 'idordenproduccion', $ordenproduccion])
                            ->andFilterWhere(['=', 'codigoproducto', $codigoproducto])
                              ->andFilterWhere(['=', 'id_tipo_producto', $grupo])
                            ->andFilterWhere(['=', 'ordenproduccion', $orden])
                            ->andFilterWhere(['=', 'idtipo', $idtipo])
                            ->orderBy('idordenproduccion desc');
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
                } else {
                    $form->getErrors();
                }
            } else {
                $table = Ordenproduccion::find()
                        ->orderBy('idordenproduccion desc');
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 40,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
            }

            return $this->render('ordenproduccionproceso', [
                        'model' => $model,
                        'form' => $form,
                        'pagination' => $pages,
                        'clientes' => ArrayHelper::map($clientes, "idcliente", "nombrecorto"),
                        'ordenproducciontipos' => ArrayHelper::map($ordenproducciontipos, "idtipo", "tipo"),
            ]);
         }else{
            return $this->redirect(['site/sinpermiso']);
        }
        }else{
            return $this->redirect(['site/login']);
        }
    }
    
    //CODIGO QUE PERMITE COMENZAR EL PROCESO DE BALANCEO
    public function actionProduccionbalanceo() {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',98])->all()){
            $balan = Balanceo::find()->all();
            $form = new FormFiltroOrdenProduccionProceso();
            $idcliente = null;
            $ordenproduccion = null;
            $idtipo = null;
            $codigoproducto = null;
            $clientes = Cliente::find()->all();
            $ordenproducciontipos = Ordenproducciontipo::find()->where(['=','ver_registro', 1])->all();
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $ordenproduccion = Html::encode($form->ordenproduccion);
                    $idtipo = Html::encode($form->idtipo);
                    $codigoproducto = Html::encode($form->codigoproducto);
                    $table = Ordenproduccion::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['like', 'idordenproduccion', $ordenproduccion])
                            ->andFilterWhere(['=', 'codigoproducto', $codigoproducto])
                            ->andFilterWhere(['=', 'idtipo', $idtipo])
                            ->andFilterWhere(['=','aplicar_balanceo', 1])
                            ->orderBy('idordenproduccion desc');
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
                } else {
                    $form->getErrors();
                }
            } else {
                $table = Ordenproduccion::find()
                       ->Where(['=','aplicar_balanceo', 1])->andWhere(['=','cerrar_orden', 0])
                        ->orderBy('idordenproduccion desc');
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 40,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
            }
            return $this->render('produccionbalanceo', [
                        'model' => $model,
                        'form' => $form,
                        'pagination' => $pages,
                        'balan' => $balan,
                        'clientes' => ArrayHelper::map($clientes, "idcliente", "nombrecorto"),
                        'ordenproducciontipos' => ArrayHelper::map($ordenproducciontipos, "idtipo", "tipo"),
            ]);
         }else{
            return $this->redirect(['site/sinpermiso']);
        }
        }else{
            return $this->redirect(['site/login']);
        }
    }
    
    //INDEZ QUE ME PERMITE VER LOS REPROCESOS
    
     public function actionIndexreprocesoproduccion() {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',115])->all()){
            $balan = Balanceo::find()->all();
            $form = new FormFiltroOrdenProduccionProceso();
            $idcliente = null;
            $ordenproduccion = null;
            $idtipo = null;
            $codigoproducto = null;
            $clientes = Cliente::find()->all();
            $ordenproducciontipos = Ordenproducciontipo::find()->where(['=','ver_registro', 1])->all();
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $ordenproduccion = Html::encode($form->ordenproduccion);
                    $idtipo = Html::encode($form->idtipo);
                    $codigoproducto = Html::encode($form->codigoproducto);
                    $table = Ordenproduccion::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['like', 'idordenproduccion', $ordenproduccion])
                            ->andFilterWhere(['=', 'codigoproducto', $codigoproducto])
                            ->andFilterWhere(['=', 'idtipo', $idtipo])
                            ->andFilterWhere(['=','aplicar_balanceo', 1])
                           
                            ->orderBy('idordenproduccion desc');
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
                } else {
                    $form->getErrors();
                }
            } else {
                $table = Ordenproduccion::find()
                       ->where(['=','aplicar_balanceo', 1])
                        ->orderBy('idordenproduccion desc');
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 40,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
            }

            return $this->render('indexreprocesoproduccion', [
                        'model' => $model,
                        'form' => $form,
                        'pagination' => $pages,
                        'balan' => $balan,
                        'clientes' => ArrayHelper::map($clientes, "idcliente", "nombrecorto"),
                        'ordenproducciontipos' => ArrayHelper::map($ordenproducciontipos, "idtipo", "tipo"),
            ]);
         }else{
            return $this->redirect(['site/sinpermiso']);
        }
        }else{
            return $this->redirect(['site/login']);
        }
    }

    public function actionNuevo_detalle_proceso($id, $iddetalleorden) {
        $detalleorden = Ordenproducciondetalle::findOne($iddetalleorden);
        $formul = new FormFiltroProcesosOperaciones();
        $idproceso = null;
        $proceso = null;        
        if ($formul->load(Yii::$app->request->get())) {
            if ($formul->validate()) {
                if ($formul->validate()) {
                    $idproceso = Html::encode($formul->id);
                    $proceso = Html::encode($formul->proceso);                                        
                    $procesos = ProcesoProduccion::find()
                            ->andFilterWhere(['=', 'idproceso', $idproceso])
                            ->andFilterWhere(['like', 'proceso', $proceso]);                            
                    $procesos = $procesos->orderBy('proceso desc');                    
                    $count = clone $procesos;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 60,
                        'totalCount' => $count->count()
                    ]);
                    $procesos = $procesos
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();                    
                } else {
                    $formul->getErrors();
                }
            }
        }else {
            $procesos = ProcesoProduccion::find()->orderBy('proceso ASC');
            $count = clone $procesos;
            $pages = new Pagination([
                'pageSize' => 60,
                'totalCount' => $count->count(),
            ]);
            $procesos = $procesos
                    ->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();
        }
        
        if (isset($_POST["guardar"])) {
        if (isset($_POST["idproceso"])) {
            $intIndice = 0;
            foreach ($_POST["idproceso"] as $intCodigo) {
                if ($_POST["duracion"][$intIndice] > 0) {
                    $detalles = Ordenproducciondetalleproceso::find()
                            ->where(['=', 'idproceso', $intCodigo])
                            ->andWhere(['=', 'iddetalleorden', $iddetalleorden])
                            ->all();
                    $reg = count($detalles);
                    if ($reg == 0) {
                        if($_POST["id_tipo"][$intIndice] > 0){
                            $table = new Ordenproducciondetalleproceso();
                            $table->idproceso = $intCodigo;
                            $table->proceso = $_POST["proceso"][$intIndice];
                            $table->duracion = $_POST["duracion"][$intIndice];
                            $table->ponderacion = $_POST["ponderacion"][$intIndice];
                            $table->cantidad_operada = 0;
                            $table->total = $_POST["duracion"][$intIndice] + ($_POST["duracion"][$intIndice] * $_POST["ponderacion"][$intIndice] / 100);
                            $table->totalproceso = $detalleorden->cantidad * $table->total;
                            $table->iddetalleorden = $iddetalleorden;
                            $table->id_tipo = $_POST["id_tipo"][$intIndice];
                            $table->insert();
                        }    
                    }
                }
                $intIndice++;
            }
            $this->porcentajeproceso($iddetalleorden);
            $this->progresoproceso($iddetalleorden, $detalleorden->idordenproduccion);
            $this->progresocantidad($iddetalleorden, $detalleorden->idordenproduccion);
            //se replica los procesos a detalles que contengan el mismo codigo de producto, para agilizar la insercion de cada uno de las operaciones por detalle            
            $detallesordenproduccion = Ordenproducciondetalle::find()
                    ->where(['<>', 'iddetalleorden', $iddetalleorden])
                    ->andWhere(['idordenproduccion' => $detalleorden->idordenproduccion])
                    ->all();
            foreach ($detallesordenproduccion as $dato) {
                if ($dato->codigoproducto == $detalleorden->codigoproducto) {
                    $detallesprocesos = Ordenproducciondetalleproceso::find()->where(['iddetalleorden' => $iddetalleorden])->all();
                    foreach ($detallesprocesos as $val) {
                        $detallesp = Ordenproducciondetalleproceso::find()
                                ->where(['=', 'idproceso', $val->idproceso])
                                ->andWhere(['=', 'iddetalleorden', $dato->iddetalleorden])
                                ->all();
                        $reg2 = count($detallesp);
                        if ($reg2 == 0) {
                            $tableprocesos = new Ordenproducciondetalleproceso();
                            $tableprocesos->idproceso = $val->idproceso;
                            $tableprocesos->proceso = $val->proceso;
                            $tableprocesos->duracion = $val->duracion;
                            $tableprocesos->ponderacion = $val->ponderacion;
                            $tableprocesos->total = $val->total;
                            $tableprocesos->cantidad_operada = 0;
                            $tableprocesos->totalproceso = $dato->cantidad * $tableprocesos->total;
                            $tableprocesos->iddetalleorden = $dato->iddetalleorden;
                            $tableprocesos->id_tipo = $val->id_tipo;
                            $tableprocesos->insert();
                        }
                    }
                    $this->porcentajeproceso($dato->iddetalleorden);
                    $this->progresoproceso($dato->iddetalleorden, $dato->idordenproduccion);
                    $this->progresocantidad($dato->iddetalleorden, $dato->idordenproduccion);
                }
            }
            $this->redirect(["orden-produccion/view_detalle", 'id' => $id]);
        }
        }
        if (isset($_POST["guardarynuevo"])) {
        if (isset($_POST["idproceso"])) {
            $intIndice = 0;
            foreach ($_POST["idproceso"] as $intCodigo) {
                if ($_POST["duracion"][$intIndice] > 0) {
                    $detalles = Ordenproducciondetalleproceso::find()
                            ->where(['=', 'idproceso', $intCodigo])
                            ->andWhere(['=', 'iddetalleorden', $iddetalleorden])
                            ->all();
                    $reg = count($detalles);
                    if ($reg == 0) {
                        if($_POST["id_tipo"][$intIndice] > 0){
                            $table = new Ordenproducciondetalleproceso();
                            $table->idproceso = $intCodigo;
                            $table->proceso = $_POST["proceso"][$intIndice];
                            $table->duracion = $_POST["duracion"][$intIndice];
                            $table->ponderacion = $_POST["ponderacion"][$intIndice];
                            $table->cantidad_operada = 0;
                            $table->total = $_POST["duracion"][$intIndice] + ($_POST["duracion"][$intIndice] * $_POST["ponderacion"][$intIndice] / 100);
                            $table->totalproceso = $detalleorden->cantidad * $table->total;
                            $table->iddetalleorden = $iddetalleorden;
                            $table->id_tipo = $_POST["id_tipo"][$intIndice];
                            $table->insert();
                        }    
                    }
                }
                $intIndice++;
            }
            $this->porcentajeproceso($iddetalleorden);
            $this->progresoproceso($iddetalleorden, $detalleorden->idordenproduccion);
            $this->progresocantidad($iddetalleorden, $detalleorden->idordenproduccion);
            //se replica los procesos a detalles que contengan el mismo codigo de producto, para agilizar la insercion de cada uno de las operaciones por detalle            
            $detallesordenproduccion = Ordenproducciondetalle::find()
                    ->where(['<>', 'iddetalleorden', $iddetalleorden])
                    ->andWhere(['idordenproduccion' => $detalleorden->idordenproduccion])
                    ->all();
            foreach ($detallesordenproduccion as $dato) {
                if ($dato->codigoproducto == $detalleorden->codigoproducto) {
                    $detallesprocesos = Ordenproducciondetalleproceso::find()->where(['iddetalleorden' => $iddetalleorden])->all();
                    foreach ($detallesprocesos as $val) {
                        $detallesp = Ordenproducciondetalleproceso::find()
                                ->where(['=', 'idproceso', $val->idproceso])
                                ->andWhere(['=', 'iddetalleorden', $dato->iddetalleorden])
                                ->all();
                        $reg2 = count($detallesp);
                        if ($reg2 == 0) {
                            $tableprocesos = new Ordenproducciondetalleproceso();
                            $tableprocesos->idproceso = $val->idproceso;
                            $tableprocesos->proceso = $val->proceso;
                            $tableprocesos->duracion = $val->duracion;
                            $tableprocesos->ponderacion = $val->ponderacion;
                            $tableprocesos->total = $val->total;
                            $tableprocesos->cantidad_operada = 0;
                            $tableprocesos->totalproceso = $dato->cantidad * $tableprocesos->total;
                            $tableprocesos->iddetalleorden = $dato->iddetalleorden;
                            $tableprocesos->id_tipo = $val->id_tipo;
                            $tableprocesos->insert();
                        }
                    }
                    $this->porcentajeproceso($dato->iddetalleorden);
                    $this->progresoproceso($dato->iddetalleorden, $dato->idordenproduccion);
                    $this->progresocantidad($dato->iddetalleorden, $dato->idordenproduccion);
                }
            }
            //$this->redirect(["orden-produccion/view_detalle", 'id' => $id]);
        }
        }
        return $this->render('_formnuevodetalleproceso', [
                    'procesos' => $procesos,
                    //'cont' => $cont,
                    'formul' => $formul,
                    'pagination' => $pages,
                    'id' => $id,
                    'iddetalleorden' => $iddetalleorden,
        ]);
    
    }
    
   //proceso que genera el porcentaje de produccion en la grafica despues de subir las unidades ESTE ES EL PROCESO
    public function actionDetalle_proceso($idordenproduccion, $iddetalleorden) {
        $procesos = Ordenproducciondetalleproceso::find()->Where(['=', 'iddetalleorden', $iddetalleorden])->orderBy('proceso asc')->all();
        $detalle = Ordenproducciondetalle::findOne($iddetalleorden);
        $error = 0;
        $cont = count($procesos);
        if (Yii::$app->request->post()) {
            if (isset($_POST["editar"])) {
                if (isset($_POST["iddetalleproceso1"])) {
                    $intIndice = 0;
                    foreach ($_POST["iddetalleproceso1"] as $intCodigo) {
                        if ($_POST["duracion"][$intIndice] > 0) {
                            $table = Ordenproducciondetalleproceso::findOne($intCodigo);
                            $table->duracion = $_POST["duracion"][$intIndice];
                            $table->ponderacion = $_POST["ponderacion"][$intIndice];
                            if ($_POST["cantidad_operada_todo"] <= 0){
                                $table->cantidad_operada = $_POST["cantidad_operada"][$intIndice];
                            }else{
                                $table->cantidad_operada = $_POST["cantidad_operada_todo"];
                            }
                            
                            $table->total = $_POST["duracion"][$intIndice] + ($_POST["duracion"][$intIndice] * $_POST["ponderacion"][$intIndice] / 100);
                            $table->totalproceso = $detalle->cantidad * $table->total;
                            if ($_POST["cantidad_operada"][$intIndice] <= $detalle->cantidad) {//se valida que la cantidad a operada no sea mayor a la cantidad a operar
                                $table->update();
                            } else {
                                $error = 1;
                            }
                        }
                        $intIndice++;
                    }
                    if ($error == 1) {
                        Yii::$app->getSession()->setFlash('error', 'El valor de la cantidad no puede ser mayor a la cantidad operada '.$detalle->cantidad);
                    } else {
                        $this->redirect(["orden-produccion/view_detalle", 'id' => $idordenproduccion]);
                    }
                    $this->progresocantidad($iddetalleorden, $idordenproduccion);
                }
                //se replica los procesos a detalles que contengan el mismo codigo de producto, para agilizar la insercion de cada uno de las operaciones por detalle            
                $detallesordenproduccion = Ordenproducciondetalle::find()
                        ->where(['<>', 'iddetalleorden', $iddetalleorden])
                        ->andWhere(['idordenproduccion' => $idordenproduccion])
                        ->all();
                foreach ($detallesordenproduccion as $dato) {
                    if ($dato->codigoproducto == $detalle->codigoproducto) {
                        $detallesprocesos = Ordenproducciondetalleproceso::find()->where(['iddetalleorden' => $dato->iddetalleorden])->all();
                        foreach ($detallesprocesos as $val) {
                            $detallesp = Ordenproducciondetalleproceso::find()
                                    ->where(['=', 'idproceso', $val->idproceso])
                                    ->andWhere(['=', 'iddetalleorden', $dato->iddetalleorden])
                                    ->all();
                            $reg2 = count($detallesp);
                            if ($reg2!= 0) {
                                $datoaguardar = Ordenproducciondetalleproceso::find()->where(['=','idproceso',$val->idproceso])->andWhere(['=','iddetalleorden',$iddetalleorden])->one();
                                $tableprocesos = Ordenproducciondetalleproceso::findOne($val->iddetalleproceso);
                                $tableprocesos->duracion = $datoaguardar->duracion;
                                $tableprocesos->ponderacion = $datoaguardar->ponderacion;
                                $tableprocesos->total = $datoaguardar->total;
                                $tableprocesos->totalproceso = $datoaguardar->totalproceso;
                                $tableprocesos->update();
                            }
                        }
                //fin replicacion ediccion    
                    }
                }
            }
            if (isset($_POST["eliminar"])) {
                if (isset($_POST["iddetalleproceso2"])) {
                    foreach ($_POST["iddetalleproceso2"] as $intCodigo) {
                        $proceso = Ordenproducciondetalleproceso::find()->where(['=','iddetalleproceso',$intCodigo])->one();
                        $detallesordenes = Ordenproducciondetalle::find()->where(['=','idordenproduccion',$idordenproduccion])->all();
                        foreach ($detallesordenes as $val){
                            $detallesproceso = Ordenproducciondetalleproceso::find()->where(['=','iddetalleorden',$val->iddetalleorden])->andwhere(['=','idproceso',$proceso->idproceso])->one();
                            if ($detallesproceso){
                                $detallesproceso->delete();
                            }
                        }
                        /*if (Ordenproducciondetalleproceso::deleteAll("iddetalleproceso=:iddetalleproceso", [":iddetalleproceso" => $intCodigo])) {
                            
                        }*/
                    }
                    $this->porcentajeproceso($iddetalleorden);
                    $this->progresoproceso($iddetalleorden, $idordenproduccion);
                    $this->progresocantidad($iddetalleorden, $idordenproduccion);
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro.');
                    $this->redirect(["orden-produccion/view_detalle", 'id' => $idordenproduccion]);
                }
            }
            if (isset($_POST["ac"])) {//abrir/cerrar en la ejecucion del proceso si esta terminado o no ha sido terminado
                if (isset($_POST["iddetalleproceso1"])) {
                    $intIndice = 0;
                    foreach ($_POST["iddetalleproceso1"] as $intCodigo) {
                        if ($_POST["estado"][$intIndice] >= 0) {
                            $table = Ordenproducciondetalleproceso::findOne($intCodigo);
                            $table->estado = $_POST["estado"][$intIndice];
                            $table->update();
                        }
                        $intIndice++;
                    }
                }
            }
            if (isset($_POST["acabrir"])) {//abrir/cerrar en la ejecucion del proceso si esta terminado o no ha sido terminado
                if (isset($_POST["iddetalleproceso1"])) {
                    $intIndice = 0;
                    foreach ($_POST["iddetalleproceso1"] as $intCodigo) {
                        if ($_POST["estado"][$intIndice] >= 0) {
                            $table = Ordenproducciondetalleproceso::findOne($intCodigo);                            
                            $table->estado = 0;
                            $table->update();
                        }
                        $intIndice++;
                    }
                }
            }
            if (isset($_POST["accerrar"])) {//abrir/cerrar en la ejecucion del proceso si esta terminado o no ha sido terminado
                if (isset($_POST["iddetalleproceso1"])) {
                    $intIndice = 0;
                    foreach ($_POST["iddetalleproceso1"] as $intCodigo) {
                        if ($_POST["estado"][$intIndice] >= 0) {
                            $table = Ordenproducciondetalleproceso::findOne($intCodigo);                            
                            $table->estado = 1;
                            $table->update();
                        }
                        $intIndice++;
                    }
                }
            }
            
            $this->porcentajeproceso($iddetalleorden);
            $this->progresoproceso($iddetalleorden, $idordenproduccion);
            $this->progresocantidad($iddetalleorden, $idordenproduccion);
            $this->redirect(["orden-produccion/view_detalle", 'id' => $idordenproduccion]);
        }
        return $this->renderAjax('_formdetalleproceso', [
                    'procesos' => $procesos,
                    'cont' => $cont,
                    'idordenproduccion' => $idordenproduccion,
                    'iddetalleorden' => $iddetalleorden,
        ]);
    }

    public function actionView_detalle($id) {
        $modeldetalles = Ordenproducciondetalle::find()->Where(['=', 'idordenproduccion', $id])->all();
        $modeldetalle = new Ordenproducciondetalle();
        $detalle_piloto = PilotoDetalleProduccion::find()->where(['=','idordenproduccion', $id])->orderBy('id_proceso DESC')->all(); 
        return $this->render('view_detalle', [
                    'model' => $this->findModel($id),
                    'modeldetalle' => $modeldetalle,                    
                    'modeldetalles' => $modeldetalles,
                    'detalle_piloto' => $detalle_piloto,
        ]);
    }  
    
    //VISTA PARA EL DETALLE DE BALANCEO
    public function actionView_balanceo($id) {
        $modeldetalles = Ordenproducciondetalle::find()->Where(['=', 'idordenproduccion', $id])->all();
        $ordendetalle = Ordenproducciondetalle::find()->Where(['=', 'idordenproduccion', $id])->one();
        $operaciones = Ordenproducciondetalleproceso::find()->Where(['=','iddetalleorden', $ordendetalle->iddetalleorden])
                                                                        ->orderBy('id_tipo DESC')
                                                                       ->all();
        $modulos = Balanceo::find()->where(['=','idordenproduccion', $id])->all();
        $cantidad_confeccionada = CantidadPrendaTerminadas::find()->where(['=','idordenproduccion', $id])->orderBy('fecha_entrada asc')->all();
        $modeldetalle = new Ordenproducciondetalle();
        if (Yii::$app->request->post()) {
            if (isset($_POST["eliminarflujo"])) {
                if (isset($_POST["id"])) {
                    foreach ($_POST["id"] as $intCodigo) {
                        try {
                            $eliminar = FlujoOperaciones::findOne($intCodigo);
                            $eliminar->delete();
                            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                            $this->redirect(["orden-produccion/view_balanceo", 'id' => $id]);
                        } catch (IntegrityException $e) {
                          
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                        } catch (\Exception $e) {
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');

                        }
                    }
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro.');
                }    
             }
        }        
       
        return $this->render('view_balanceo', [
                    'model' => $this->findModel($id),
                    'modeldetalle' => $modeldetalle,                    
                    'modeldetalles' => $modeldetalles,
                    'operaciones' => $operaciones,
                    'modulos' => $modulos,
                    'cantidad_confeccionada' => $cantidad_confeccionada,
                     
        ]);
    }        
    //ELIMINAR UN DETALLE DE LOS COSTOS DE PRODUCCION
    
    public function actionEliminar($id,$detalle, $token)
    {                                
        $detalle = \app\models\OtrosCostosProduccion::findOne($detalle);
        $detalle->delete();
        $this->redirect(["view",'id' => $id, 'token' => $token]);        
    }
    
    //ELIMINAR UNA NOVEDAD DE LA PRODUCCION
    public function actionEliminarnovedadproduccion($id,$id_novedad, $token)
    {                                
        $novedad = \app\models\NovedadOrdenProduccion::findOne($id_novedad);
        $novedad->delete();
        $this->redirect(["view",'id' => $id, 'token' => $token]);        
    }
    
    //EDITAR NOVEDA DE PRODUCCION
    public function actionEditarnovedadproduccion($id, $id_novedad, $token) {
        $model = new \app\models\NovedadOrdenProduccion();
         
        if ($model->load(Yii::$app->request->post())) {  
            $tabla = \app\models\NovedadOrdenProduccion::findOne($id_novedad);
            $tabla->novedad = $model->novedad;
            $tabla->save(false);
            return $this->redirect(['orden-produccion/view','id' => $id, 'token' => $token]);
        }
        if (Yii::$app->request->get("id_novedad")) {
             $novedad = \app\models\NovedadOrdenProduccion::findOne($id_novedad);
             if($novedad){
                 $model->novedad = $novedad->novedad;
             }
         }
          return $this->render('editarnovedadproduccion', [
            'model' => $model,
            'id' => $id,
            'id_novedad' => $id_novedad,  
           ]);         
    }
    
    //VISTA DE CANTIDADES CONFECCINADAS POR TALLAS
    
    public function actionVistatallas($iddetalleorden)
    {
      $detalletallas = Ordenproducciondetalle::findOne($iddetalleorden);  
      $cantidades = CantidadPrendaTerminadas::find()->where(['=','iddetalleorden', $iddetalleorden])->orderBy('id_entrada DESC')->all();
      $cantidad_preparacion = CantidadPrendaTerminadasPreparacion::find()->where(['=','iddetalleorden', $iddetalleorden])->orderBy('id_proceso DESC')->all();
       return $this->render('vistatallas', [
                    'detalletallas' => $detalletallas, 
                    'cantidades' => $cantidades,
                    'cantidad_preparacion' => $cantidad_preparacion,
                   
        ]);
      
    }
    //PROCESO QUE MUESTRA LA VISTA DE LA NOVEDAD DE PRODUCCION
    public function actionVistanovedadorden($id, $id_novedad, $token) {
     $model = \app\models\NovedadOrdenProduccion::findOne($id_novedad);
     return $this->render('viewnovedadproduccion', [
                    'model' => $model, 
                    'id' => $id,
                    'id_novedad' => $id_novedad,
                    'token' => $token,
                   
        ]);
     
    }
    
    //VISTA QUE MUESTRA EL BALACEO-PREPACION DE UNA TALLA
     public function actionVistatallasbalanceopreparacion($iddetalleorden, $modulo)
    {
      $detalletallas = Ordenproducciondetalle::findOne($iddetalleorden);  
      $cantidades = CantidadPrendaTerminadas::find()->where(['=','iddetalleorden', $iddetalleorden])->orderBy('id_entrada DESC')->all();
      $cantidad_preparacion = CantidadPrendaTerminadasPreparacion::find()->where(['=','iddetalleorden', $iddetalleorden])->orderBy('id_proceso DESC')->all();
       return $this->render('vistatallasbalanceopreparacion', [
                    'detalletallas' => $detalletallas, 
                    'cantidades' => $cantidades,
                    'cantidad_preparacion' => $cantidad_preparacion,
                    'modulo' => $modulo,
                   
        ]);
      
    }
    
    
    //VISTA PARA EL REPROCESO DE PRODUCCION
    
      public function actionView_reproceso_produccion($id)
      {
        $modeldetalles = Ordenproducciondetalle::find()->Where(['=', 'idordenproduccion', $id])->all();
        $ordendetalle = Ordenproducciondetalle::find()->Where(['=', 'idordenproduccion', $id])->one();
        $operaciones = Ordenproducciondetalleproceso::find()->Where(['=','iddetalleorden', $ordendetalle->iddetalleorden])
                                                                    ->orderBy('id_tipo DESC')
                                                                   ->all();
        $modulos = Balanceo::find()->where(['=','idordenproduccion', $id])->all();
        return $this->render('view_reproceso_produccion', [
                    'model' => $this->findModel($id),
                    'modeldetalles' => $modeldetalles,
                    'operaciones' => $operaciones,
                    'modulos' => $modulos,
                     
        ]);
      
    }
    //PROCESO QUE CIERRA EL MODULO DE REPROCESO
     public function actionCerrarmodulo($id, $id_balanceo)
    {
        $balanceo = Balanceo::findOne($id_balanceo);
        $balanceo->activo_reproceso = 1;
       $balanceo->save(false);
        $modeldetalles = Ordenproducciondetalle::find()->Where(['=', 'idordenproduccion', $id])->all();
        $ordendetalle = Ordenproducciondetalle::find()->Where(['=', 'idordenproduccion', $id])->one();
        $operaciones = Ordenproducciondetalleproceso::find()->Where(['=','iddetalleorden', $ordendetalle->iddetalleorden])
                                                                    ->orderBy('id_tipo DESC')
                                                                   ->all();
       $modulos = Balanceo::find()->where(['=','idordenproduccion', $id])->all(); 
       return $this->redirect(["orden-produccion/detalle_reproceso_prenda", 
                    'id_balanceo' => $id_balanceo,
                    'model' => $this->findModel($id),
                    'modeldetalles' => $modeldetalles,
                    'operaciones' => $operaciones,
                    'modulos' => $modulos,
                    'id' => $id,
       ]);
        
    }
    
    protected function progresoproceso($iddetalleorden, $idordenproduccion) {
        $tabla = Ordenproducciondetalle::findOne(['=', 'iddetalleorden', $iddetalleorden]);
        $procesos = Ordenproducciondetalleproceso::find()->where(['=', 'iddetalleorden', $iddetalleorden])->all();
        $progreso = 0;
        $totalprogresodetalle = 0;
        $totalprocesodetalle = 0;
        $cantidadefectiva = 0;
        $sumacantxoperar = 0;
        $totalsegxdetalle = 0;
        foreach ($procesos as $val) {
            if ($val->estado == 1) {
                $cantidadefectiva = $cantidadefectiva + $tabla->cantidad;
                $totalprogresodetalle = $totalprogresodetalle + $val->porcentajeproceso;
            }
        }
        $tsegundosproceso = (new \yii\db\Query())->from('ordenproducciondetalleproceso');
        $sumsegproc = $tsegundosproceso->where(['=', 'iddetalleorden', $iddetalleorden])->sum('totalproceso');
        $total = $totalprogresodetalle;
        $tabla->porcentaje_proceso = $total;
        $sumacantxoperar = $tabla->cantidad * count($procesos);
        if ($sumacantxoperar == 0) {
            $sumacantxoperar = 1;
        }
        $totalsegxdetalle = ($sumsegproc * $cantidadefectiva) / $sumacantxoperar;
        $tabla->cantidad_efectiva = $cantidadefectiva;
        $tabla->totalsegundos = $totalsegxdetalle;
        $tabla->save(false);
        $totaldetallesseg = Ordenproducciondetalle::find()->where(['=', 'idordenproduccion', $idordenproduccion])->all();
        $tdetallesseg = 0;
        $ts = 0;
        foreach ($totaldetallesseg as $value) {
            $tdetallesseg = $tdetallesseg + $value->totalsegundos;            
            $procesosx = Ordenproducciondetalleproceso::find()->where(['=', 'iddetalleorden', $value->iddetalleorden])->all();
            foreach ($procesosx as $v) {
                $ts = $ts + $v->totalproceso;
            }
        }                
      
        $orden = Ordenproduccion::findOne($idordenproduccion);
        $ordendetalle = Ordenproducciondetalle::find()->where(['=','idordenproduccion',$idordenproduccion])->all();
        $reg = count($ordendetalle);
        $porc = 0;
        $porci = 0;
        foreach ($ordendetalle as $val){
            
            $porci = $val->cantidad / $orden->cantidad * $val->porcentaje_proceso; 
            $porc = $porc + $porci;
            
        }
        $orden->porcentaje_proceso = $porc;
        $orden->save(false);
    }

    protected function progresocantidad($iddetalleorden, $idordenproduccion) {
        $tabla = Ordenproducciondetalle::findOne(['=', 'iddetalleorden', $iddetalleorden]);
        $procesos = Ordenproducciondetalleproceso::find()->where(['=', 'iddetalleorden', $iddetalleorden])->all();                        
        $cantidadoperada = 0;                        
        $porcentaje = 0;
        $porcentajesuma = 0;
        $cont = 0;
        $totalsegundosgeneral = 0;
        foreach ($procesos as $val) {
            $totalsegundosgeneral = $totalsegundosgeneral + $val->totalproceso;
        }
        foreach ($procesos as $val) {
            if ($val->cantidad_operada > 0) {                
                $cantidadoperada = $cantidadoperada + $val->cantidad_operada;
                $porcentaje = ($val->total * $val->cantidad_operada) / $totalsegundosgeneral * 100;
                $porcentajesuma = $porcentajesuma + $porcentaje;
                $cont++;
            }            
        }
        $porcentajecantidad = $porcentajesuma;
        $tabla->porcentaje_cantidad = $porcentajecantidad;        
        if ($cont == 0){
            $tabla->cantidad_operada = $cantidadoperada;
        } else {
            $tabla->cantidad_operada = $cantidadoperada / $cont;
        } 
        $tabla->save(false);
        $orden = Ordenproduccion::findOne($idordenproduccion);
        $detalle = Ordenproducciondetalle::find()->where(['=','idordenproduccion',$idordenproduccion])->all();
        $porc = 0;
        $porci = 0;
        foreach ($detalle as $val){
            $porci = $val->cantidad_operada / $orden->cantidad * $val->porcentaje_cantidad; 
            $porc = $porc + $porci;
        }
        $orden->porcentaje_cantidad = $porc;
        $orden->save(false);
    }

    protected function porcentajeproceso($iddetalleorden) {
        $detalleorden = Ordenproducciondetalle::findOne($iddetalleorden);
        $detallesprocesos = Ordenproducciondetalleproceso::find()->where(['=', 'iddetalleorden', $iddetalleorden])->all();
        $totalproceso = 0;
        //suma de segundos de todos los procesos
        $totalsegundos = (new \yii\db\Query())->from('ordenproducciondetalleproceso');
        $sumseg = $totalsegundos->where(['=', 'iddetalleorden', $iddetalleorden])->sum('totalproceso');
        //suma de segundos por cada ficha
        $totalsegundosficha = (new \yii\db\Query())->from('ordenproducciondetalleproceso');
        $sumsegficha = $totalsegundosficha->where(['=', 'iddetalleorden', $iddetalleorden])->sum('total');
        $detalleorden->segundosficha = $sumsegficha;
        $detalleorden->save(false);
        foreach ($detallesprocesos as $val) {
            $tabla = Ordenproducciondetalleproceso::findOne($val->iddetalleproceso);
            $tabla->porcentajeproceso = $val->totalproceso / $sumseg * 100;
            $tabla->save(false);            
        }
        $ordenproduccion = Ordenproduccion::findOne($detalleorden->idordenproduccion);
        $ordenproduccion->segundosficha = $sumsegficha;
        $ordenproduccion->save(false);
    }
    
    public function actionProductos($id){
        $rows = Producto::find()->where(['=','idcliente', $id])->orderBy('idproducto desc')->all();

        echo "<option value='' required>Seleccione un codigo...</option>";
        if(count($rows)>0){
            foreach($rows as $row){
                echo "<option value='$row->codigo' required>$row->codigonombre</option>";
            }
        }
    }
    
    public function actionOrdenes($id){
        $rows = Ordenproduccion::find()->where(['=','idcliente', $id])->orderBy('idordenproduccion desc')->all();

        echo "<option value='' required>Seleccione una orden...</option>";
        if(count($rows)>0){
            foreach($rows as $row){
                echo "<option value='$row->idordenproduccion' required>$row->salidaOrden</option>";
            }
        }
    }
    
    //codigo que permite subir las prendas terminas
    public function actionSubirprendaterminada($id_balanceo, $idordenproduccion, $id_proceso_confeccion, $id_planta)
    {
        $model = new FormPrendasTerminadas();
        $suma = 0;
        $balanceo = Balanceo::findOne($id_balanceo);
        $total = 0;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if (isset($_POST["enviarcantidad"])) { 
                    if (isset($_POST["id_detalle_orden"])) {
                        $intIndice = 0;
                        foreach ($_POST["id_detalle_orden"] as $intCodigo):
                           $iddetalleorden = $intCodigo;
                            $orden_detalle = Ordenproducciondetalle::find()->where(['=','iddetalleorden', $intCodigo])->one();
                            $total = $orden_detalle->faltante + $model->cantidad_terminada;
                            if($total <= $orden_detalle->cantidad){
                                if($model->cantidad_terminada > 0 && $model->fecha_entrada != '' ){ 
                                    $table = new CantidadPrendaTerminadas();
                                    $table->id_balanceo = $id_balanceo;
                                    $table->idordenproduccion = $idordenproduccion;
                                    $table->cantidad_terminada = $model->cantidad_terminada;
                                    $table->fecha_entrada = $model->fecha_entrada;
                                    $table->nro_operarios = $model->nro_operarios;
                                    $table->hora_corte_entrada = $model->hora_corte_entrada;
                                    $table->usuariosistema = Yii::$app->user->identity->username;
                                    $table->observacion = $model->observacion;
                                    $table->iddetalleorden = $intCodigo;
                                    $table->id_proceso_confeccion = $id_proceso_confeccion;
                                     $table->id_planta = $id_planta;
                                    $table->insert();
                                    $intIndice ++;
                                }else{
                                   Yii::$app->getSession()->setFlash('warning', 'Campos vacios en el ingreso.'); 
                                }    
                            }else{
                                Yii::$app->getSession()->setFlash('error', 'Favor validar la cantidad de prendas confeccionadas.!');
                            }    
                        endforeach;
                        $detalle_proceso = Ordenproducciondetalleproceso::find()->where(['=','iddetalleorden', $intCodigo])->all();
                        $contar = 0;
                        foreach ($detalle_proceso as $entrarcantidad):
                           $contar = $entrarcantidad->cantidad_operada; 
                           $entrarcantidad->cantidad_operada = $model->cantidad_terminada + $contar;
                           $entrarcantidad->save(false);
                           $contar = 0;
                        endforeach;
                        $orden = Ordenproduccion::findOne($idordenproduccion);
                        $unidades = 0;
                        $orden_detalle = Ordenproducciondetalle::find()->where(['=','iddetalleorden', $intCodigo])->one();
                        $cantidad = CantidadPrendaTerminadas::find()->where(['=','iddetalleorden', $intCodigo])->all();
                        $ordenunidad = CantidadPrendaTerminadas::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
                        $suma = 0;
                        $cantidad_real = 0;
                        foreach ($cantidad as $detalle):
                            $suma +=$detalle->cantidad_terminada; 
                        endforeach;
                        $orden_detalle->faltante = $suma;
                        $orden_detalle->save(false);
                        foreach ($ordenunidad as $cant):
                            $unidades += $cant->cantidad_terminada; 
                        endforeach;
                        $cantidad_real= $orden->cantidad;
                        $orden->faltante = $cantidad_real - $unidades;
                        $orden->save(false);
                       $this->ActualizaPorcentajeCantidad($iddetalleorden, $idordenproduccion);                    
                       return $this->redirect(['view_balanceo','id' => $idordenproduccion]);
                    }
                }
            }else{
                $model->getErrors();
            }   
        }
        if (Yii::$app->request->get($id_balanceo, $idordenproduccion)) {
            $model->nro_operarios = $balanceo->cantidad_empleados;
            $model->id_proceso_confeccion = $id_proceso_confeccion;
        }
        return $this->renderAjax('_subirprendaterminada', [
            'model' => $model,       
            'idordenproduccion' => $idordenproduccion,
            'balanceo' => $balanceo,
            'id_proceso_confeccion' => $id_proceso_confeccion,
            'id_planta' => $id_planta,
            
        ]);      
    }
    
    // PROCESO QUE CREA LAS NOVEDADES DE LAS ORDENES DE PRODUCCION
    public function actionCrearnovedadordenproduccion($id, $token) {
        
        $model = new \app\models\FormNovedadOrden();
        
        if ($model->load(Yii::$app->request->post())) {
          //  if ($model->validate()){
                if (isset($_POST["enviarnovedadorden"])) {
                    $table = new \app\models\NovedadOrdenProduccion();
                    $table->novedad = $model->novedad;
                    $table->idordenproduccion = $id;
                    $table->usuariosistema = Yii::$app->user->identity->username;
                    $table->autorizado =0;
                    $table->save(false);
                    return $this->redirect(['view','id' => $id, 'token' => $token]);
                }
          //  }
        }
        if (Yii::$app->request->get($id)) {
            $model->idordenproduccion = $id;
            
        }
        
       return $this->renderAjax('crearnovedadordenproduccion', [
            'model' => $model,       
            'id' => $id,
           'token' => $token,
            
        ]);      
       
    }
        
    protected function ActualizaPorcentajeCantidad($iddetalleorden, $idordenproduccion) {
        //actualiza detale
        $canti_operada = 0; $cantidad = 0; $porcentaje = 0;
        $orden_detalle_actualizada = Ordenproducciondetalle::find()->where(['=','iddetalleorden', $iddetalleorden])->one();
        $canti_operada = $orden_detalle_actualizada->faltante;
        $cantidad = $orden_detalle_actualizada->cantidad;
        $porcentaje = number_format(($canti_operada * 100)/ $cantidad,4);
        $orden_detalle_actualizada->porcentaje_cantidad = $porcentaje;
        $orden_detalle_actualizada->cantidad_operada = $canti_operada;
        $orden_detalle_actualizada->save(false);
        //actuliza orden produccion
        $orden = Ordenproduccion::findOne($idordenproduccion);
        $sumadetalle = Ordenproducciondetalle::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
        $contador = 0;
        $total_porcentaje = 0;
        foreach ($sumadetalle as $sumar):
            $contador += $sumar->cantidad_operada;
        endforeach;
        $total_porcentaje = number_format(($contador * 100)/ $orden->cantidad,4);
        $orden->porcentaje_cantidad = $total_porcentaje;
        $orden->save(false);
    }
    
    
    public function actionIndexconsulta() {
    if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',95])->all()){
            $form = new FormFiltroConsultaOrdenproduccion();
            $idcliente = null;
            $desde = null;
            $hasta = null;
            $codigoproducto = null;
            $facturado = null;
            $tipo = null;
            $ordenproduccionint = null;
            $ordenproduccioncliente = null;
            $mostrar_resultado = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $codigoproducto = Html::encode($form->codigoproducto);
                    $facturado = Html::encode($form->facturado);
                    $tipo = Html::encode($form->tipo);
                    $ordenproduccionint = Html::encode($form->ordenproduccionint);
                    $ordenproduccioncliente = Html::encode($form->ordenproduccioncliente);
                    $mostrar_resultado = Html::encode($form->mostrar_resultado);
                    $table = Ordenproduccion::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['>=', 'fechallegada', $desde])
                            ->andFilterWhere(['<=', 'fechallegada', $hasta])
                            ->andFilterWhere(['=', 'facturado', $facturado])
                            ->andFilterWhere(['=', 'idtipo', $tipo])
                            ->andFilterWhere(['=', 'idordenproduccion', $ordenproduccionint])
                            ->andFilterWhere(['=', 'codigoproducto', $codigoproducto])
                            ->andFilterWhere(['=', 'ordenproduccion', $ordenproduccioncliente]);
                    $table = $table->orderBy('idordenproduccion desc');
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
                        //$table = $table->all();
                        $this->actionExcelconsulta($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = Ordenproduccion::find()->where(['=','facturado', 0])
                        ->orderBy('idordenproduccion desc');
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
            return $this->render('index_consulta', [
                        'model' => $model,
                        'form' => $form,
                        'pagination' => $pages,
                        'mostrar_resultado' => $mostrar_resultado,
            ]);
        }else{
            return $this->redirect(['site/sinpermiso']);
        }
        }else{
            return $this->redirect(['site/login']);
        }
    }
    
    public function actionIndexconsultaficha() {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',47])->all()){
            $form = new FormFiltroConsultaFichaoperacion();
            $idcliente = null;
            $ordenproduccion = null;
            $idtipo = null;
            $codigoproducto = null;
            $condicion = 0;
            $clientes = Cliente::find()->orderBy('nombrecorto ASC')->all();
            $ordenproducciontipos = Ordenproducciontipo::find()->all();
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $ordenproduccion = Html::encode($form->ordenproduccion);
                    $idtipo = Html::encode($form->idtipo);
                    $codigoproducto = Html::encode($form->codigoproducto);
                    $table = Ordenproduccion::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['=', 'idordenproduccion', $ordenproduccion])
                            ->andFilterWhere(['=', 'codigoproducto', $codigoproducto])
                            ->andFilterWhere(['=', 'idtipo', $idtipo])
                            ->orderBy('idordenproduccion desc');
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
                    'pageSize' => 40,
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

            return $this->render('index_consulta_ficha', [
                        'model' => $model,
                        'form' => $form,
                        'condicion' => $condicion,
                        'pagination' => $pages,
                        'clientes' => ArrayHelper::map($clientes, "idcliente", "nombrecorto"),
                        'ordenproducciontipos' => ArrayHelper::map($ordenproducciontipos, "idtipo", "tipo"),
            ]);
         }else{
            return $this->redirect(['site/sinpermiso']);
        }
        }else{
            return $this->redirect(['site/login']);
        }
    }
    
    public function actionIndexoperacionprenda() {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',113])->all()){
            $form = new \app\models\FormFiltroConsultaOperaciones();
            $idproceso = null;
            $ordenproduccion = null;
            $id_tipo = null;
            $condicion = 1;
            $operaciones = ProcesoProduccion::find()->orderBy('proceso ASC')->all();
            $maquinas = \app\models\TiposMaquinas::find()->orderBy('descripcion ASC')->all();
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idproceso = Html::encode($form->idproceso);
                    $ordenproduccion = Html::encode($form->idordenproduccion);
                    $id_tipo = Html::encode($form->id_tipo);
                    $table = FlujoOperaciones::find()
                            ->andFilterWhere(['=', 'idproceso', $idproceso])
                            ->andFilterWhere(['=', 'idordenproduccion', $ordenproduccion])
                            ->andFilterWhere(['=', 'id_tipo', $id_tipo])
                            ->orderBy('fecha_creacion desc');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 50,
                        'totalCount' => $count->count()
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){
                        //$table = $table->all();
                        $this->actionExcelconsultaoperacionesprenda($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = FlujoOperaciones::find()
                        ->orderBy('fecha_creacion desc');
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
                        $this->actionExcelconsultaoperacionesprenda($tableexcel);
                }
            }

            return $this->render('indexfichaoperaciones', [
                        'model' => $model,
                        'form' => $form,
                        'pagination' => $pages,
                        'condicion' => $condicion, 
                        'operaciones' => ArrayHelper::map($operaciones, "idproceso", "proceso"),
                        'maquinas' => ArrayHelper::map($maquinas, "id_tipo", "descripcion"),
            ]);
         }else{
            return $this->redirect(['site/sinpermiso']);
        }
        }else{
            return $this->redirect(['site/login']);
        }
    }
    
    public function actionViewconsulta($id) {
        $modeldetalles = Ordenproducciondetalle::find()->Where(['=', 'idordenproduccion', $id])->all();
        $modeldetalle = new Ordenproducciondetalle();
        $mensaje = "";
        $novedad_orden = \app\models\NovedadOrdenProduccion::find()->where(['=','idordenproduccion', $id])->all();
        $otrosCostosProduccion = \app\models\OtrosCostosProduccion::find()->where(['=','idordenproduccion', $id])->orderBy('id_proveedor DESC')->all();
              
        return $this->render('view_consulta', [
                    'model' => $this->findModel($id),
                    'modeldetalle' => $modeldetalle,
                    'modeldetalles' => $modeldetalles,
                    'mensaje' => $mensaje,
                    'novedad_orden' => $novedad_orden,
                    'otrosCostosProduccion' => $otrosCostosProduccion,
                     
        ]);
    }
    
    public function actionViewconsultaficha($id, $condicion) {
        $modeldetalles = Ordenproducciondetalle::find()->Where(['=', 'idordenproduccion', $id])->all();
        $modulos = Balanceo::find()->where(['=','idordenproduccion', $id])->orderBy('id_balanceo DESC')->all();
        $modeldetalle = new Ordenproducciondetalle();
        return $this->render('view_consulta_ficha', [
                    'model' => $this->findModel($id),
                    'modeldetalle' => $modeldetalle,                    
                    'modeldetalles' => $modeldetalles,
                    'modulos' => $modulos,
                    'condicion' => $condicion,
                    
        ]);
    }
    
  //PROCESO PARA IR A LA EFICIENCIA DEL MODULO
    
    public function actionEficienciamodulo($id_balanceo){
        $unidades= CantidadPrendaTerminadas::find()->where(['=','id_balanceo',$id_balanceo])->groupBy('fecha_entrada')->all(); 
        $modulos = Balanceo::find()->where(['=','id_balanceo', $id_balanceo])->one();
        $eficiencia = EficienciaBalanceo::find()->where(['=','id_balanceo', $id_balanceo])->all();
         $auxiliar= ''; $total = 0; $totalEficiencia = 0;
        if(count($eficiencia) == 0){
            foreach ($unidades as $fechas):
                $auxiliar = $fechas->fecha_entrada;
                $cantidad = CantidadPrendaTerminadas::find()->where(['=','fecha_entrada', $auxiliar])->andWhere(['=','id_balanceo', $modulos->id_balanceo])->all();
                $total = 0;
                foreach ($cantidad as $cantidades):
                   $total += $cantidades->cantidad_terminada;
                endforeach;
                $table = new EficienciaBalanceo();
                $table->id_balanceo = $id_balanceo;
                $table->fecha_confeccion = $fechas->fecha_entrada;
                $table->nro_operarios = $fechas->nro_operarios;
                $table->minutos_balanceo = $modulos->tiempo_balanceo;
                $table->usuario = Yii::$app->user->identity->username;
                $table->unidades_confeccionadas = $total;
                if($modulos->fecha_inicio === $auxiliar){
                    $table->horas_inicio = $modulos->total_horas;
                }
                if($modulos->fecha_cierre_modulo === $auxiliar){
                    $table->horas_finales = $modulos->hora_final_modulo;
                }
                if($table->horas_inicio == 0 && $table->horas_finales == 0){
                        $table->unidades_por_operarios = round((60 / $table->minutos_balanceo) * $modulos->horario->total_horas);
                }else{
                    if($table->horas_inicio <> 0 && $table->horas_finales == 0){
                         $table->unidades_por_operarios = round((60 / $table->minutos_balanceo) * $table->horas_inicio);
                    }else{
                            $table->unidades_por_operarios = round((60 / $table->minutos_balanceo) * $table->horas_finales);
                    } 
                }
                $table->cantidad_por_dia = $table->unidades_por_operarios *  $table->nro_operarios;
                $table->porcentaje_cumplimiento = round(($total * 100)/ $table->cantidad_por_dia,2);
                $table->save(false);
            endforeach;
        }else{
            foreach ($unidades as $fechas):
                $auxiliar = $fechas->fecha_entrada;
                $buscar = EficienciaBalanceo::find()->where(['=','fecha_confeccion', $auxiliar])->andWhere(['=','id_balanceo', $modulos->id_balanceo])->one();
                //PROCESO QUE ACTUALIZA NUEVAMENTE LA EFICIENCIA
                if($buscar){
                    $total = 0;
                     $cantidad = CantidadPrendaTerminadas::find()->where(['=','fecha_entrada', $auxiliar])->andWhere(['=','id_balanceo', $modulos->id_balanceo])->all();
                    foreach ($cantidad as $cantidades):
                     $total += $cantidades->cantidad_terminada;
                    endforeach;
                    //BUSCA EL ID
                    $actualizar = EficienciaBalanceo::findOne($buscar->id_eficiencia);
                    if($modulos->fecha_inicio === $auxiliar){
                       $actualizar->horas_inicio = $modulos->total_horas;
                    }
                    if($modulos->fecha_cierre_modulo === $auxiliar){
                        $actualizar->horas_finales = $modulos->hora_final_modulo;
                    }
                    if($actualizar->horas_inicio == 0 && $actualizar->horas_finales == 0){
                        $actualizar->unidades_por_operarios = round((60 / $actualizar->minutos_balanceo) * $modulos->horario->total_horas);
                    }else{
                        if($actualizar->horas_inicio <> 0 && $actualizar->horas_finales == 0){
                         $actualizar->unidades_por_operarios = round((60 / $actualizar->minutos_balanceo) * $actualizar->horas_inicio);
                        }else{
                            $actualizar->unidades_por_operarios = round((60 / $actualizar->minutos_balanceo) * $actualizar->horas_finales);
                        } 
                    }
                    $actualizar->cantidad_por_dia = $actualizar->unidades_por_operarios *  $actualizar->nro_operarios;
                    $actualizar->porcentaje_cumplimiento = round(($total * 100)/ $actualizar->cantidad_por_dia,2);
                    $actualizar->unidades_confeccionadas = $total;
                    $actualizar->save(false);
                    
                }else{
                    $total = 0;
                     $cantidad = CantidadPrendaTerminadas::find()->where(['=','fecha_entrada', $auxiliar])->andWhere(['=','id_balanceo', $modulos->id_balanceo])->all();
                    foreach ($cantidad as $cantidades):
                       $total += $cantidades->cantidad_terminada;
                    endforeach;
                    $table = new EficienciaBalanceo();
                    $table->id_balanceo = $id_balanceo;
                    $table->fecha_confeccion = $fechas->fecha_entrada;
                    $table->nro_operarios = $fechas->nro_operarios;
                    $table->minutos_balanceo = $modulos->tiempo_balanceo;
                    $table->usuario = Yii::$app->user->identity->username;
                    $table->unidades_confeccionadas = $total;
                    if($modulos->fecha_inicio === $auxiliar){
                       $table->horas_inicio = $modulos->total_horas;
                    }
                    if($modulos->fecha_cierre_modulo === $auxiliar){
                        $table->horas_finales = $modulos->hora_final_modulo;
                    }
                    if($table->horas_inicio == 0 && $table->horas_finales == 0){
                        $table->unidades_por_operarios = round((60 / $table->minutos_balanceo) * $modulos->horario->total_horas);
                    }else{
                        if($table->horas_inicio <> 0 && $table->horas_finales == 0){
                         $table->unidades_por_operarios = round((60 / $table->minutos_balanceo) * $table->horas_inicio);
                        }else{
                            $table->unidades_por_operarios = round((60 / $table->minutos_balanceo) * $table->horas_finales);
                        } 
                    }
                    $table->cantidad_por_dia = $table->unidades_por_operarios *  $table->nro_operarios;
                    $table->porcentaje_cumplimiento = round(($total * 100)/ $table->cantidad_por_dia,2);
                    $table->save(false);
                }  
            endforeach;    
        }     
        $eficiencia = EficienciaBalanceo::find()->where(['=','id_balanceo', $id_balanceo])->all();
        $con = 0;
        foreach ($eficiencia as $eficiencias):
            $totalEficiencia += $eficiencias->porcentaje_cumplimiento;
            $con += 1;
        endforeach;
        if($totalEficiencia == 0){
            $modulos->total_eficiencia = 0;
        }else{
             $modulos->total_eficiencia = round($totalEficiencia /$con, 2);
        }
        $modulos->save(false);
        $eficiencia = EficienciaBalanceo::find()->where(['=','id_balanceo', $id_balanceo])->orderBy('fecha_confeccion desc')->all();
        
        return $this->render('eficienciafecha', [
                        'unidades' => $unidades,
                        'id_balanceo' => $id_balanceo,
                        'eficiencia' => $eficiencia,
         ]);    
       
    }
    
    // PROCESO PARA SUBIR LOS REPROCESOS AL MODULO Y LA OPERACION
    
     public function actionDetalle_reproceso_prenda($id_balanceo, $id){
       $balanceo_detalle = BalanceoDetalle::find()->where(['=', 'id_balanceo', $id_balanceo])->andWhere(['=','estado_operacion', 0])->orderBy('id_operario asc')->all();
       $reproceso_confeccion = \app\models\ReprocesoProduccionPrendas::find()->where(['=','id_balanceo', $id_balanceo])
                                                                 ->andWhere(['=','tipo_reproceso', 1])->orderBy('idproductodetalle ASC')->all();
       $reproceso_terminacion = \app\models\ReprocesoProduccionPrendas::find()->where(['=','id_balanceo', $id_balanceo])
                                                                 ->andWhere(['=','tipo_reproceso', 2])->orderBy('idproductodetalle ASC')->all(); 
        if (isset($_POST["iddetalle"])) {
            $intIndice = 0;
            foreach ($_POST["iddetalle"] as $intCodigo) {
                if($_POST["cantidad"][$intIndice] > 0){
                   $detalle = BalanceoDetalle::find()->where(['=','id_detalle', $intCodigo])->one();
                   $table = new \app\models\ReprocesoProduccionPrendas();
                   $table->id_detalle = $intCodigo;
                   $table->id_proceso = $detalle->id_proceso;
                   $table->id_balanceo = $id_balanceo ;
                   $table->id_operario = $detalle->id_operario;
                   $table->idordenproduccion = $id;
                   $table->cantidad = $_POST["cantidad"][$intIndice];
                   $table->idproductodetalle = $_POST["id_talla"];
                   $table->tipo_reproceso = $_POST["tipo_reproceso"][$intIndice];
                   $table->fecha_registro = date('Y-m-d'); 
                   $table->observacion = $_POST["observacion"][$intIndice];
                   $table->usuariosistema = Yii::$app->user->identity->username;
                   $table->insert();
                }    
                $intIndice++;
            }
            //permite volver a cargar la consulta
            $reproceso_confeccion = \app\models\ReprocesoProduccionPrendas::find()->where(['=','id_balanceo', $id_balanceo])
                                                                 ->andWhere(['=','tipo_reproceso', 1])->orderBy('idproductodetalle ASC')->all();
            $reproceso_terminacion = \app\models\ReprocesoProduccionPrendas::find()->where(['=','id_balanceo', $id_balanceo])
                                                                 ->andWhere(['=','tipo_reproceso', 2])->orderBy('idproductodetalle ASC')->all(); 
            return $this->render('detalle_reproceso_prenda', [
                        'balanceo_detalle' => $balanceo_detalle,
                        'id_balanceo' => $id_balanceo,
                        'reproceso_confeccion' => $reproceso_confeccion,
                        'reproceso_terminacion' => $reproceso_terminacion,
                        'id' => $id,
            ]);   
       }     
        return $this->render('detalle_reproceso_prenda', [
                        'balanceo_detalle' => $balanceo_detalle,
                        'id_balanceo' => $id_balanceo,
                        'id' => $id,
                        'reproceso_confeccion' => $reproceso_confeccion,
                        'reproceso_terminacion' => $reproceso_terminacion, 
            ]);    
       
    }
    
    public function actionDetalle_proceso_consulta($idordenproduccion, $iddetalleorden) {
        $procesos = Ordenproducciondetalleproceso::find()->Where(['=', 'iddetalleorden', $iddetalleorden])->orderBy('proceso asc')->all();
        $detalle = Ordenproducciondetalle::findOne($iddetalleorden);
        $error = 0;
        $cont = count($procesos);
        
        return $this->renderAjax('_formdetalleprocesoconsulta', [
                    'procesos' => $procesos,
                    'cont' => $cont,
                    'idordenproduccion' => $idordenproduccion,
                    'iddetalleorden' => $iddetalleorden,
        ]);
    }
    
    public function actionRecoger_preparacion($iddetalleorden, $modulo, $id) {
        $detalle_balanceo = BalanceoDetalle::find()->where(['=','id_balanceo', $modulo])->andWhere(['=','ordenamiento', 0])->orderBy('id_operario ASC,id_proceso DESC')->all();
        $detalletallas = Ordenproducciondetalle::findOne($iddetalleorden);
        if (isset($_POST["id_proceso"])) {
            $intIndice = 0;
            foreach ($_POST["id_proceso"] as $intCodigo){
                if($_POST["cantidad"][$intIndice]>0){
                     $tabla = new CantidadPrendaTerminadasPreparacion();
                     $tabla->id_balanceo = $modulo;
                     $tabla->idordenproduccion = $id;
                     $tabla->iddetalleorden = $iddetalleorden;
                     $tabla->id_proceso_confeccion = 2;
                     $tabla->id_operario = $_POST["id_operario"][$intIndice];
                     $tabla->cantidad_terminada = $_POST["cantidad"][$intIndice];
                     $tabla->nro_operarios = 1;
                     $tabla->id_proceso = $intCodigo;
                     $tabla->total_operaciones = $_POST["total_operaciones"][$intIndice]; 
                     $tabla->fecha_entrada = $_POST["fecha_entrada"][$intIndice];
                     $tabla->usuariosistema = Yii::$app->user->identity->username;
                     $tabla->observacion = $_POST["observacion"][$intIndice];
                     $tabla->insert(false);
                }
                $intIndice++;      
            }
            return $this->render('recoger_prenda_preparada', [
                        'iddetalleorden' => $iddetalleorden,
                        'detalletallas' => $detalletallas,
                        'detalle_balanceo' => $detalle_balanceo,
                        'modulo' => $modulo,
                        'id' => $id,
                         ]);
        }
        return $this->render('recoger_prenda_preparada', [
            'iddetalleorden' => $iddetalleorden,
            'detalletallas' => $detalletallas,
            'detalle_balanceo' => $detalle_balanceo,
            'modulo' => $modulo,
            'id' => $id,
        ]);
    }
    //PROCESO QUE INSERTAR UNA NUEVA FACTURA AL COSTO
    
      public function actionNuevocostoproduccion($id, $token)
    {
        $compras = \app\models\Compra::find()->where(['=','autorizado', 1])->andWhere(['=','id_tipo_compra', 1])->orderBy('id_proveedor ASC, factura DESC')->all();
        $form = new \app\models\FormCompraBuscar();
        $factura = null;
        $id_proveedor = null;
        $mensaje = '';
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $factura = Html::encode($form->factura);                                
                $id_proveedor = Html::encode($form->id_proveedor);
                    $compras = \app\models\compra::find()
                            ->andFilterWhere(['=','factura',$factura])
                            ->andFilterWhere(['=','id_proveedor', $id_proveedor])
                            ->andWhere(['=', 'id_tipo_compra', 1]);
                    $compras = $compras->orderBy('id_proveedor DESC, factura DESC');                
                    $count = clone $compras;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 20,
                        'totalCount' => $count->count()
                    ]);
                    $compras = $compras
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();         
                               
            } else {
                $form->getErrors();
            }                    
        } else {
            $compras = \app\models\Compra::find()->where(['=','autorizado', 1])->andWhere(['=','id_tipo_compra', 1])->orderBy('id_proveedor ASC, factura DESC');
            $count = clone $compras;
            $pages = new Pagination([
                'pageSize' => 20,
                'totalCount' => $count->count(),
            ]);
            $compras = $compras
                    ->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();
        }
        if (isset($_POST["id_compra"])) {
                $intIndice = 0;
                foreach ($_POST["id_compra"] as $intCodigo) {
                    $compra = \app\models\Compra::find()->where(['id_compra' => $intCodigo])->one();
                    $detalle_costo = \app\models\OtrosCostosProduccion::find()
                    ->where(['=', 'idordenproduccion', $id])
                    ->andWhere(['=', 'id_compra', $compra->id_compra])
                    ->all();
                    if(count($detalle_costo) == 0){
                        $table = new \app\models\OtrosCostosProduccion();
                        $table->idordenproduccion = $id;
                        $table->id_compra = $compra->id_compra;
                        $table->id_proveedor = $compra->id_proveedor;
                        $table->vlr_costo = $compra->subtotal;
                        $table->nrofactura = $compra->factura;
                        $table->fecha_proceso = date('Y-m-d');
                        $table->fecha_compra = $compra->fechainicio;
                        $table->usuariosistema = Yii::$app->user->identity->username;
                        $table->insert(); 
                    }    
                }
               $this->redirect(["orden-produccion/view", 'id' => $id, 'token' => $token]);
        }else{
                
        }
        return $this->render('_nuevocostoproduccion', [
            'compras' => $compras,            
            'mensaje' => $mensaje,
            'pagination' => $pages,
            'id' => $id,
            'form' => $form,
            'token' => $token,

        ]);
    }
    
    //PROCESO QUE IMPORTA LAS OPERACIONES A UNA OP.
    public function actionImportaroperacionesprenda($id, $iddetalleorden)
    {
        $detalle_proceso = Ordenproducciondetalleproceso::find()->where(['=','iddetalleorden', $iddetalleorden])->one();
        if(!$detalle_proceso){
            $form = new \app\models\FormImportarOperaciones();
            $orden_produccion = null;
            $model = null;
            $buscar = 0;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $orden_produccion = Html::encode($form->orden_produccion);
                    $buscar = Html::encode($form->buscar);
                    if($buscar == 0){
                        $orden = Ordenproducciondetalle::find()->where(['=','idordenproduccion', $orden_produccion])->one();
                    }else{
                       $orden = \app\models\SalidaBodegaOperaciones::find()->where(['=','id_salida_bodega', $orden_produccion])->one(); 
                    }    
                    if ($orden){
                        if($buscar == 0){
                            $detalle = Ordenproducciondetalleproceso::find()->where(['=','iddetalleorden', $orden->iddetalleorden])->orderBy('proceso ASC')->all(); 
                        }else{
                           $detalle = \app\models\SalidaBodegaOperaciones::find()->where(['=','id_salida_bodega', $orden->id_salida_bodega])->orderBy('idproceso ASC')->all(); 
                        }    
                         $model = $detalle;
                    }else{
                        Yii::$app->getSession()->setFlash('warning', 'La orden de produccion / Salida de bodega que digito NO existe en la base de datos. ');
                        return $this->render('importaroperacionesprenda', [
                                        'form' => $form,
                                        'model' => $model,
                                        'id' => $id,
                                        'iddetalleorden' => $iddetalleorden,
                                        'buscar' => $buscar,
                                        ]);
                    }
                }else{
                    $form->getErrors();
                }
            }   
            if (isset($_POST["importaroperaciones"])) {
                if (isset($_POST["operaciones"])) {
                    $intIndice = 0;
                    $cont = 0;
                    foreach ($_POST["operaciones"] as $intCodigo) {
                        if($buscar == 0){
                            $proceso = Ordenproducciondetalleproceso::findOne($intCodigo);
                            if($proceso){
                                $table = new Ordenproducciondetalleproceso();
                                $table->proceso = $proceso->proceso;
                                $table->duracion = $proceso->duracion;
                                $table->total = $proceso->total;
                                $table->idproceso = $proceso->idproceso;
                                $table->iddetalleorden = $iddetalleorden;
                                $table->id_tipo = $proceso->id_tipo;
                                $table->cantidad_operada = 0;
                                $cont += 1;
                                $table->insert();
                            }
                            $intIndice++; 
                        }else{
                            $proceso = \app\models\SalidaBodegaOperaciones::findOne($intCodigo);
                            if($proceso){
                                $table = new Ordenproducciondetalleproceso();
                                $table->proceso = $proceso->proceso->proceso;
                                $table->duracion = $proceso->segundos;
                                $table->total = $proceso->segundos;
                                $table->idproceso = $proceso->idproceso;
                                $table->iddetalleorden = $iddetalleorden;
                                $table->id_tipo = $proceso->id_tipo;
                                $table->cantidad_operada = 0;
                                $cont += 1;
                                $table->insert();
                            }
                            $intIndice++;  
                        }    
                    }
                    Yii::$app->getSession()->setFlash('info', 'Se importaron '.  $cont. ' registros de forma exitosa. ');
                   return $this->render('importaroperacionesprenda', [
                                        'form' => $form,
                                        'model' => $model,
                                        'id' => $id,
                                        'iddetalleorden' => $iddetalleorden,
                                        'buscar' => $buscar,
                                        ]);
                }
            }            
            return $this->render('importaroperacionesprenda', [
                           'form' => $form,
                           'model' => $model,
                           'id' => $id,
                           'iddetalleorden' => $iddetalleorden,
                           'buscar' => $buscar,
                           ]);
        }else{
             $this->redirect(["orden-produccion/view_detalle", 'id' => $id]);
        }    
    }
    //crear reision
    
    public function actionCrearemisionorden($id, $token) {
        $model = new \app\models\ModelCrearColorRemision();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if (isset($_POST["crearemision"])) { 
                    $table = new \app\models\Remision();
                    $color = Color::find()->where(['=','id', $model->color])->one();
                    $table->idordenproduccion = $id;
                    $table->id_color = $model->color;
                    $table->total_tulas = 0;
                    $table->total_exportacion = 0;
                    $table->totalsegundas = 0;
                    $table->total_colombia = 0;
                    $table->total_confeccion = 0;
                    $table->total_despachadas = 0;
                    $table->fecha_entrega = date('Y-m-d');
                    $table->color = $color->color;
                    $table->save(false);
                    $remision = \app\models\Remision::find()->where(['=','idordenproduccion', $id])->orderBy('id_remision DESC')->one();
                    $id_remision = $remision->id_remision;
                    $this->redirect(["/remision/remision", 'id' => $id, 'token' => $token, 'id_remision' => $id_remision]);
                }
            }else{
                 $model->getErrors();
            }    
             
        }
        return $this->renderAjax('crearemisionorden', [
            'id' => $id,
            'model' => $model,
            'token' => $token,
        ]);
    }
    
    //permite ver la remisiones
    public function actionListadoremisiones($id, $token) {
        $model = \app\models\Remision::find()->where(['=','idordenproduccion', $id])->orderBy('id_remision DESC')->all();
        if (Yii::$app->request->post()) {
           
        }
        return $this->renderAjax('listadoremision', [
            'id' => $id,
            'model' => $model,
            'token' => $token,
        ]); 
    }
    
    //PERMITE VER MAS INFORMAICON DE LA OP
    //permite ver la remisiones
    public function actionVer_informacion($id) {
        $model = Ordenproduccion::find()->where(['=','idordenproduccion', $id])->all();
        if (Yii::$app->request->post()) {
           
        }
       
        return $this->renderAjax('ver_informacion_orden', [
            'id' => $id,
            'model' => $model,
        ]); 
    }
    
    //PERMITE CREAR INSUMOS A UNA OP
    public function actionCagar_insumos_orden($id, $token) {
       $orden = Ordenproduccion::findOne($id);
       if($orden){
          
           if(\app\models\OrdenProduccionInsumos::find()->where(['=','idordenproduccion', $id])->andWhere(['=','idtipo', $orden->idtipo])->one()){
               Yii::$app->getSession()->setFlash('error', 'Ya se GENERARON los insumos a la orden de '.$orden->tipo->tipo.'.');
               return $this->redirect(["/orden-produccion/view", 'id' => $id, 'token' => $token]);
           }else{
               $insumos = new \app\models\OrdenProduccionInsumos();
               $insumos->idordenproduccion = $id;
               $insumos->idtipo = $orden->idtipo;
               $insumos->codigo_producto = $orden->codigoproducto;
               $insumos->orden_produccion_cliente = $orden->ordenproduccion;
               $insumos->user_name = Yii::$app->user->identity->username;
               $insumos->fecha_creada = date('Y-m-d');
               $insumos->save(false);
               $id_entrada = \app\models\OrdenProduccionInsumos::find()->where(['=','idordenproduccion', $id])->orderBy('id_entrega DESC')->one();
               return $this->redirect(['orden-produccion-insumos/view', 'id' => $id_entrada->id_entrega]);
           }
       }       
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
        
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Id')
                    ->setCellValue('B1', 'Cod Producto')
                    ->setCellValue('C1', 'Cliente')
                    ->setCellValue('D1', 'Orden Prod Int')
                    ->setCellValue('E1', 'Orden Prod Ext')
                    ->setCellValue('F1', 'Orden Cliente')
                    ->setCellValue('G1', 'Fecha Llegada')
                    ->setCellValue('H1', 'Fecha Proceso')
                    ->setCellValue('I1', 'Fecha Entrega')
                    ->setCellValue('J1', 'Cantidad')
                    ->setCellValue('K1', 'Tipo')
                    ->setCellValue('L1', 'Total')
                    ->setCellValue('M1', 'Autorizado')
                    ->setCellValue('N1', 'Facturado')
                    ->setCellValue('O1', 'Planta')
                    ->setCellValue('P1', 'Talla')
                    ->setCellValue('Q1', 'Cantidad')
                    ->setCellValue('R1', 'Precio')
                    ->setCellValue('S1', 'Subtotal')
                    ->setCellValue('T1', 'Porcentaje proceso')
                    ->setCellValue('U1', 'Observaciones');
        $i = 2;
        
        foreach ($tableexcel as $val) {
            $detalle = Ordenproducciondetalle::find()->where(['=','idordenproduccion', $val->idordenproduccion])->all();  
            foreach ($detalle as $detalles) {
                
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $val->idordenproduccion)
                        ->setCellValue('B' . $i, $val->codigoproducto)
                        ->setCellValue('C' . $i, $val->cliente->nombreClientes)
                        ->setCellValue('D' . $i, $val->ordenproduccion)
                        ->setCellValue('E' . $i, $val->ordenproduccionext)
                        ->setCellValue('F' . $i, $val->ordenproduccion)
                        ->setCellValue('G' . $i, $val->fechallegada)
                        ->setCellValue('H' . $i, $val->fechaprocesada)
                        ->setCellValue('I' . $i, $val->fechaentrega)
                        ->setCellValue('J' . $i, $val->cantidad)
                        ->setCellValue('K' . $i, $val->tipo->tipo)
                        ->setCellValue('L' . $i, round($val->totalorden,0))
                        ->setCellValue('M' . $i, $val->autorizar)
                        ->setCellValue('N' . $i, $val->facturar)
                        ->setCellValue('O' . $i, $detalles->plantaProduccion->nombre_planta)
                        ->setCellValue('P' . $i, $detalles->productodetalle->prendatipo->prenda.'/'. $detalles->productodetalle->prendatipo->talla->talla)
                        ->setCellValue('Q' . $i, $detalles->cantidad)
                        ->setCellValue('R' . $i, $detalles->vlrprecio)
                        ->setCellValue('S' . $i, $detalles->subtotal)
                        ->setCellValue('T' . $i, $detalles->porcentaje_proceso)
                        ->setCellValue('U' . $i, $val->observacion);
                      
                $i++;
            }
           $i = $i; 
        }

        $objPHPExcel->getActiveSheet()->setTitle('ordenes_produccion');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ordenes_produccion.xlsx"');
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
    
    //EXCEL PARA DESCARGAR LAS ORDENES DE SALIDA O ENTRADA
    
    public function actionExcelEntradaSalida($tableexcel) {                
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
                    ->setCellValue('B1', 'CLIENTE')
                    ->setCellValue('C1', 'OP')
                    ->setCellValue('D1', 'CODIGO')
                    ->setCellValue('E1', 'TIPO PROCESO')
                    ->setCellValue('F1', 'FECHA ENTRADA')
                    ->setCellValue('G1', 'UNIDADES')
                    ->setCellValue('H1', 'TIPO ENTRADA')
                    ->setCellValue('I1', 'TULAS')
                    ->setCellValue('J1', 'FECHA CREACION')
                    ->setCellValue('K1', 'USUARIO')
                    ->setCellValue('L1', 'OBSERVACION');
                    
        $i = 2;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_salida)
                    ->setCellValue('B' . $i, $val->cliente->nombreClientes)
                    ->setCellValue('C' . $i, $val->idordenproduccion)
                    ->setCellValue('D' . $i, $val->codigo_producto)
                    ->setCellValue('E' . $i, $val->tipoProceso)
                    ->setCellValue('F' . $i, $val->fecha_entrada_salida)
                    ->setCellValue('G' . $i, $val->total_cantidad)
                    ->setCellValue('H' . $i, $val->numero_tulas)
                    ->setCellValue('I' . $i, $val->tipoentrada->concepto)
                    ->setCellValue('J' . $i, $val->fecha_proceso)
                    ->setCellValue('K' . $i, $val->usuariosistema)
                    ->setCellValue('L' . $i, $val->observacion);
           $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Salidades_entradas_ordenes');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Salida/Entrada_Ordenes.xlsx"');
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
    public function actionExcelconsultaficha($tableexcel) {                
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
                    ->setCellValue('B1', 'Cod Producto')
                    ->setCellValue('C1', 'Cliente')
                    ->setCellValue('D1', 'Orden Prod Int')
                    ->setCellValue('E1', 'Orden Prod Ext')
                    ->setCellValue('F1', 'Fecha Llegada')
                    ->setCellValue('G1', 'Fecha Proceso')
                    ->setCellValue('H1', 'Fecha Entrega')
                    ->setCellValue('I1', 'Cantidad')
                    ->setCellValue('J1', 'Tipo')                    
                    ->setCellValue('K1', 'Porcentaje');
        $i = 2;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->idordenproduccion)
                    ->setCellValue('B' . $i, $val->codigoproducto)
                    ->setCellValue('C' . $i, $val->cliente->nombreClientes)
                    ->setCellValue('D' . $i, $val->ordenproduccion)
                    ->setCellValue('E' . $i, $val->ordenproduccionext)
                    ->setCellValue('F' . $i, $val->fechallegada)
                    ->setCellValue('G' . $i, $val->fechaprocesada)
                    ->setCellValue('H' . $i, $val->fechaentrega)
                    ->setCellValue('I' . $i, $val->cantidad)
                    ->setCellValue('J' . $i, $val->tipo->tipo)
                    ->setCellValue('K' . $i, 'Proceso '.round($val->porcentaje_proceso,1).' % - Cantidad '.round($val->porcentaje_cantidad,1).' %');
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Ficha_operaciones');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Ficha_operaciones.xlsx"');
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
    
    public function actionExcelconsultaUnidades($tableexcel) {                
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
                               
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'NRO BALANCEO')
                    ->setCellValue('C1', 'ORD. PRODUCCION')
                    ->setCellValue('D1', 'CLIENTE')
                    ->setCellValue('E1', 'REFERENCIA')
                    ->setCellValue('F1', 'CANTIDADES')
                    ->setCellValue('G1', 'FACTURADO')
                    ->setCellValue('H1', 'FECHA PROCESO')
                    ->setCellValue('I1', 'USUARIO')
                    ->setCellValue('J1', 'OBSERVACION')
                     ->setCellValue('J1', 'PLANTA');
        $i = 2;
        $facturado = 0;
        foreach ($tableexcel as $val) {
           $facturado = round($val->detalleorden->vlrprecio * $val->cantidad_terminada);                      
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_entrada)
                    ->setCellValue('B' . $i, $val->id_balanceo)
                    ->setCellValue('C' . $i, $val->idordenproduccion)
                    ->setCellValue('D' . $i, $val->ordenproduccion->cliente->nombreClientes)
                    ->setCellValue('E' . $i, $val->detalleorden->productodetalle->prendatipo->prenda .' / '. $val->detalleorden->productodetalle->prendatipo->talla->talla)
                    ->setCellValue('F' . $i, $val->cantidad_terminada)
                     ->setCellValue('G' . $i, $facturado)
                    ->setCellValue('H' . $i, $val->fecha_entrada)
                    ->setCellValue('I' . $i, $val->usuariosistema)
                    ->setCellValue('J' . $i, $val->observacion)
                     ->setCellValue('J' . $i, $val->planta->nombre_planta);
                  
            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('Unidades_confeccionadas');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Cantidad_unidades.xlsx"');
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
    public function actionExcelOrdenTercero($tableexcel) {                
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
                               
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'NRO ORDEN')
                    ->setCellValue('B1', 'PROVEEDOR')
                    ->setCellValue('C1', 'CLIENTE')
                    ->setCellValue('D1', 'OP CLIENTE')
                    ->setCellValue('E1', 'PROCESO')
                    ->setCellValue('F1', 'REFERENCIA')
                    ->setCellValue('G1', 'FECHA PROCESO')
                    ->setCellValue('H1', 'FECHA REGISTRO')
                    ->setCellValue('I1', 'VR. MINUTO')
                    ->setCellValue('J1', 'TOTAL MINUTOS')
                    ->setCellValue('K1', 'TOTAL UNIDADES')
                     ->setCellValue('L1', 'TOTAL PAGAR')
                     ->setCellValue('M1', 'USUARIO')
                     ->setCellValue('N1', 'AUTORIZADO')
                     ->setCellValue('O1', 'OBSERVACION');
                    
        $i = 2;
        foreach ($tableexcel as $val) {
         
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_orden_tercero)
                    ->setCellValue('B' . $i, $val->proveedor->nombrecorto)
                    ->setCellValue('C' . $i, $val->cliente->nombrecorto)
                    ->setCellValue('D' . $i, $val->idordenproduccion)
                    ->setCellValue('E' . $i, $val->tipo->tipo)
                    ->setCellValue('F' . $i, $val->codigo_producto)
                    ->setCellValue('G' . $i, $val->fecha_proceso)
                    ->setCellValue('H' . $i, $val->fecha_registro)
                    ->setCellValue('I' . $i, $val->vlr_minuto)
                    ->setCellValue('J' . $i, $val->cantidad_minutos)
                    ->setCellValue('K' . $i, $val->cantidad_unidades)
                    ->setCellValue('L' . $i, $val->total_pagar)
                    ->setCellValue('M' . $i, $val->usuariosistema)
                    ->setCellValue('N' . $i, $val->autorizadoTercero)
                    ->setCellValue('O' . $i, $val->observacion);
                    
                  
            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('Orden_prodccion_tercero');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Orden_produccion.xlsx"');
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
    
    public function actionExceloperaciones($id,$iddetalleorden) {
        $orden = Ordenproduccion::findOne($id);
        $ordendetalle = Ordenproducciondetalle::findOne($iddetalleorden);
        $ordendetalleproceso = Ordenproducciondetalleproceso::find()->where(['=','iddetalleorden',$iddetalleorden])->all();
        $items = count($ordendetalleproceso);
        $totalsegundos = 0;
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
        $objPHPExcel->getActiveSheet()->getStyle('3')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('4')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('6')->getFont()->setBold(true);
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
                    ->setCellValue('A1', 'FICHA OPERACIONES')
                    ->setCellValue('A2', 'NIT:')
                    ->setCellValue('B2', $orden->cliente->cedulanit . '-' . $orden->cliente->dv)
                    ->setCellValue('C2', 'FECHA LLEGADA:')
                    ->setCellValue('D2', $orden->fechallegada)
                    ->setCellValue('A3', 'CLIENTE:')
                    ->setCellValue('B3', $orden->cliente->nombrecorto)
                    ->setCellValue('C3', 'FECHA ENTREGA:')
                    ->setCellValue('D3', $orden->fechaentrega)
                    ->setCellValue('A4', 'COD PRODUCTO:')
                    ->setCellValue('B4', $orden->codigoproducto)
                    ->setCellValue('C4', 'ORDEN PRODUCCION:')
                    ->setCellValue('D4', $orden->ordenproduccion)
                    ->setCellValue('A5', 'PRODUCTO:')
                    ->setCellValue('B5', $ordendetalle->productodetalle->prendatipo->prenda.' / '.$ordendetalle->productodetalle->prendatipo->talla->talla)
                    ->setCellValue('C5', 'TIPO ORDEN:')
                    ->setCellValue('D5', $orden->tipo->tipo)
                    ->setCellValue('A6', 'ID')
                    ->setCellValue('B6', 'PROCESO')
                    ->setCellValue('C6', 'DURACION(SEG)')
                    ->setCellValue('D6', 'TOTAL OPERACION')
                    ->setCellValue('E6', 'PONDERACION (SEG)')
                    ->setCellValue('F6', 'TOTAL (SEG)') 
                    ->setCellValue('G6', 'MAQUINA');
                   
        $i = 7;
        
        foreach ($ordendetalleproceso as $val) {
            $totalsegundos = $totalsegundos + $val->total;                      
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->iddetalleproceso)
                    ->setCellValue('B' . $i, $val->proceso)
                    ->setCellValue('C' . $i, $val->duracion)
                    ->setCellValue('D' . $i, round(60 / $val->duracion * 60))
                    ->setCellValue('E' . $i, $val->ponderacion)                    
                    ->setCellValue('F' . $i, $val->total)                    
                     ->setCellValue('G' . $i, $val->tipomaquina->descripcion);
                    
                   
                        
            $i++;
        }
        $j = $i + 1;
        $objPHPExcel->getActiveSheet()->getStyle($j)->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('C' . $j, 'Items: '.$items)
                ->setCellValue('D' . $j, 'Total Segundos: '. $totalsegundos)
                ->setCellValue('E' . $j, 'Total Minutos: '. round($totalsegundos / 60),1);
        
        $objPHPExcel->getActiveSheet()->setTitle('ficha_operaciones');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ficha_operaciones.xlsx"');
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
    
    public function actionCantidadconfeccionada($iddetalleorden, $id_proceso_confeccion) {                
        $cantidades = CantidadPrendaTerminadas::find()->where(['=','iddetalleorden', $iddetalleorden])->all();
        $preparacion = CantidadPrendaTerminadasPreparacion::find()->where(['=','iddetalleorden', $iddetalleorden])->orderBy('id_entrada DESC')->all();
        $detalletallas = Ordenproducciondetalle::findOne($iddetalleorden);
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setLastModifiedBy("EMPRESA")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        if ($id_proceso_confeccion == 1){
            $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
            $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);


            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', 'ID')
                        ->setCellValue('B1', 'F. ENTRADA')
                        ->setCellValue('C1', 'F. REGISTRO')
                        ->setCellValue('D1', 'CANT.')
                        ->setCellValue('E1', 'USUARIO')
                        ->setCellValue('F1', 'OBSERVACION');
            $i = 2;
        }else{
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

            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', 'ID')
                        ->setCellValue('B1', 'No BALANCEO')
                        ->setCellValue('C1', 'ORDEN PROD.')
                        ->setCellValue('D1', 'TALLA')
                        ->setCellValue('E1', 'OPERACION')
                        ->setCellValue('F1', 'OPERARIO')
                        ->setCellValue('G1', 'F. ENTRADA')
                        ->setCellValue('H1', 'F. REGISTRO')
                        ->setCellValue('I1', 'UNIDADES.')
                        ->setCellValue('J1', 'USUARIO')
                        ->setCellValue('K1', 'OBSERVACION');
            $i = 2;
        }    
        if ($id_proceso_confeccion == 1){
            foreach ($cantidades as $val) {

                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $val->id_entrada)
                        ->setCellValue('B' . $i, $val->fecha_entrada)
                        ->setCellValue('C' . $i, $val->fecha_procesada)
                        ->setCellValue('D' . $i, $val->cantidad_terminada)
                        ->setCellValue('E' . $i, $val->usuariosistema)
                        ->setCellValue('F' . $i, $val->observacion);
                $i++;
            }
        }else{
            foreach ($preparacion as $val) {

                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $val->id_entrada)
                        ->setCellValue('B' . $i, $val->id_balanceo)
                        ->setCellValue('C' . $i, $val->idordenproduccion)
                        ->setCellValue('D' . $i, $val->detalleorden->productodetalle->prendatipo->prenda.'/'. $val->detalleorden->productodetalle->prendatipo->talla->talla)
                        ->setCellValue('E' . $i, $val->proceso->proceso)
                        ->setCellValue('F' . $i, $val->operario->nombrecompleto)
                        ->setCellValue('G' . $i, $val->fecha_entrada)
                        ->setCellValue('H' . $i, $val->fecha_procesada)
                        ->setCellValue('I' . $i, $val->cantidad_terminada)
                        ->setCellValue('J' . $i, $val->usuariosistema)
                        ->setCellValue('K' . $i, $val->observacion);
                $i++;
            }
        }    

        $objPHPExcel->getActiveSheet()->setTitle('Cantidad x tallas');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Cantidades_Talla.xlsx"');
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
    
    //permite exportar los reprocesos de la op
    public function actionReprocesosexcelconfeccion($id) {                
        $reprocesos = \app\models\ReprocesoProduccionPrendas::find()->where(['=','idordenproduccion', $id])
                                                                   ->andWhere(['=','tipo_reproceso', 1])->orderBy('idproductodetalle ASC')->all();
       
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
                        ->setCellValue('A1', 'ID')
                        ->setCellValue('B1', 'OPERARIO')
                        ->setCellValue('C1', 'TALLA')
                        ->setCellValue('D1', 'OPERACION')
                        ->setCellValue('E1', 'CLIENTE')
                        ->setCellValue('F1', 'MODULO')
                        ->setCellValue('G1', 'ORDEN PROD.')
                        ->setCellValue('H1', 'UNIDADES')
                        ->setCellValue('I1', 'TIEMPO')
                        ->setCellValue('J1', 'PROCESO')
                        ->setCellValue('K1', 'F. PROCESO')
                        ->setCellValue('L1', 'USUARIO')
                        ->setCellValue('M1', 'OBSERVACION');
            $i = 2;
         
            foreach ($reprocesos as $val) {

                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $val->id_reproceso)
                        ->setCellValue('B' . $i, $val->operario->nombrecompleto)
                        ->setCellValue('C' . $i, $val->productodetalle->prendatipo->prenda .'/'.$val->productodetalle->prendatipo->talla->talla)
                        ->setCellValue('D' . $i, $val->proceso->proceso)
                        ->setCellValue('E' . $i, $val->ordenproduccion->cliente->nombrecorto)
                        ->setCellValue('F' . $i, $val->id_balanceo)
                        ->setCellValue('G' . $i, $val->idordenproduccion)
                        ->setCellValue('H' . $i, $val->cantidad)
                        ->setCellValue('I' . $i, $val->detalle->minutos);
                        if($val->tipo_reproceso == 1){
                         $objPHPExcel->setActiveSheetIndex(0)
                                  ->setCellValue('J' . $i, 'CONFECCION');
                                 
                     }else{
                         $objPHPExcel->setActiveSheetIndex(0)
                                  ->setCellValue('J' . $i, 'TERMINACION');
                     }
                      $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('K' . $i, $val->fecha_registro)
                        ->setCellValue('L' . $i, $val->usuariosistema)
                        ->setCellValue('M' . $i, $val->observacion);
                $i++;
            }
           

        $objPHPExcel->getActiveSheet()->setTitle('Reprocesos');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reprocesos.xlsx"');
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
    }// fin del exportar
   
    //permite exportar los reprocesos de terminacion
     public function actionReprocesosexcelterminacion($id) {                
        $reprocesos = \app\models\ReprocesoProduccionPrendas::find()->where(['=','idordenproduccion', $id])
                                                                   ->andWhere(['=','tipo_reproceso', 2])->orderBy('idproductodetalle ASC')->all();
       
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
                        ->setCellValue('B1', 'OPERARIO')
                        ->setCellValue('C1', 'TALLA')
                        ->setCellValue('D1', 'OPERACION')
                        ->setCellValue('E1', 'CLIENTE')
                        ->setCellValue('F1', 'MODULO')
                        ->setCellValue('G1', 'ORDEN PROD.')
                        ->setCellValue('H1', 'UNIDADES')
                        ->setCellValue('I1', 'PROCESO')
                        ->setCellValue('J1', 'F. PROCESO')
                        ->setCellValue('K1', 'USUARIO')
                         ->setCellValue('L1', 'OBSERVACION');
            $i = 2;
         
            foreach ($reprocesos as $val) {

                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $val->id_reproceso)
                        ->setCellValue('B' . $i, $val->operario->nombrecompleto)
                        ->setCellValue('C' . $i, $val->productodetalle->prendatipo->prenda .'/'.$val->productodetalle->prendatipo->talla->talla)
                        ->setCellValue('D' . $i, $val->proceso->proceso)
                        ->setCellValue('E' . $i, $val->ordenproduccion->cliente->nombrecorto)
                        ->setCellValue('F' . $i, $val->id_balanceo)
                        ->setCellValue('G' . $i, $val->idordenproduccion)
                        ->setCellValue('H' . $i, $val->cantidad);
                        if($val->tipo_reproceso == 1){
                         $objPHPExcel->setActiveSheetIndex(0)
                                  ->setCellValue('I' . $i, 'CONFECCION');
                                 
                     }else{
                         $objPHPExcel->setActiveSheetIndex(0)
                                  ->setCellValue('I' . $i, 'TERMINACION');
                     }
                      $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('J' . $i, $val->fecha_registro)
                        ->setCellValue('K' . $i, $val->usuariosistema)
                        ->setCellValue('L' . $i, $val->observacion);
                $i++;
            }
           

        $objPHPExcel->getActiveSheet()->setTitle('Reprocesos');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reprocesos.xlsx"');
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
    }// fin del exportar
    
    
    //permite esportar las operaciones por prenda
     public function actionExcelconsultaoperacionesprenda($tableexcel) {                
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
                    ->setCellValue('A1', 'CODIGO')
                    ->setCellValue('B1', 'OPERACION')
                    ->setCellValue('C1', 'MAQUINA')
                    ->setCellValue('D1', 'OP CLIENTE')
                    ->setCellValue('E1', 'CLIENTE')
                    ->setCellValue('F1', 'PRODUCTO')
                    ->setCellValue('G1', 'OP INTERNA')
                    ->setCellValue('H1', 'SEGUNDOS')
                    ->setCellValue('I1', 'MINUTOS')
                    ->setCellValue('J1', 'TIPO OPERACION')
                    ->setCellValue('K1', 'FECHA CREACION')
                    ->setCellValue('L1', 'USUARIO');
                   
        $i = 2;
        foreach ($tableexcel as $val) {
         
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->idproceso)
                    ->setCellValue('B' . $i, $val->proceso->proceso)
                    ->setCellValue('C' . $i, $val->tipomaquina->descripcion)
                    ->setCellValue('D' . $i, $val->ordenproduccion->ordenproduccion)
                    ->setCellValue('E' . $i, $val->ordenproduccion->cliente->nombrecorto)
                    ->setCellValue('F' . $i, $val->ordenproduccion->codigoproducto)
                    ->setCellValue('G' . $i, $val->idordenproduccion)
                    ->setCellValue('H' . $i, $val->segundos)
                    ->setCellValue('I' . $i, $val->minutos);
                     if($val->operacion == 0){
                         $objPHPExcel->setActiveSheetIndex(0)
                                  ->setCellValue('J' . $i, 'BALACEO');
                                 
                     }else{
                         $objPHPExcel->setActiveSheetIndex(0)
                                  ->setCellValue('J' . $i, 'PREPARACION');
                     }
                     $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('K' . $i, $val->fecha_creacion)
                    ->setCellValue('L' . $i, $val->usuariosistema);
            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('Listado operaciones');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Operaciones x prenda.xlsx"');
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
    
    //PERMITE EXPORTAR A EXCEL LOS REPROCESOS
    
    public function actionExcelReprocesos($tableexcel) {                
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
                    ->setCellValue('B1', 'OP')
                    ->setCellValue('C1', 'BALANCEO')
                    ->setCellValue('D1', 'OPERARIO')
                    ->setCellValue('E1', 'OPERACIONES')
                    ->setCellValue('F1', 'PRODUCTO/TALLA')
                    ->setCellValue('G1', 'CLIENTE')
                    ->setCellValue('H1', 'CANT.')
                   ->setCellValue('I1', 'TIEMPO')
                   ->setCellValue('J1', 'PROCESO')
                    ->setCellValue('K1', 'F. REGISTRO')
                    ->setCellValue('L1', 'USUARIO');
                   
        $i = 2;
        foreach ($tableexcel as $val) {
         
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_reproceso)
                    ->setCellValue('B' . $i, $val->idordenproduccion)
                    ->setCellValue('C' . $i, $val->id_balanceo)
                    ->setCellValue('D' . $i, $val->operario->nombrecompleto)     
                    ->setCellValue('E' . $i,  $val->proceso->proceso) 
                    ->setCellValue('F' . $i, $val->productodetalle->prendatipo->prenda.' / '.$val->productodetalle->prendatipo->talla->talla)
                    ->setCellValue('G' . $i, $val->ordenproduccion->cliente->nombrecorto)
                    ->setCellValue('H' . $i, $val->cantidad)
                    ->setCellValue('I' . $i, $val->detalle->minutos);    
                   if($val->tipo_reproceso == 1){
                         $objPHPExcel->setActiveSheetIndex(0)
                                  ->setCellValue('J' . $i, 'CONFECCION');
                                 
                     }else{
                         $objPHPExcel->setActiveSheetIndex(0)
                                  ->setCellValue('J' . $i, 'TERMINACION');
                     }
                     $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('K' . $i, $val->fecha_registro)    
                    ->setCellValue('L' . $i, $val->usuariosistema);
            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('Total Reprocesos');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reprocesos.xlsx"');
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
    
    //PROCESO PARA EXPORTAR A EXCEL TODAS LAS OPERACIONES
    
      public function actionExceloperaciones_iniciales($id) {
        $flujo = FlujoOperaciones::find()->where(['=','idordenproduccion', $id])->orderBy('pieza ASC, operacion ASC, orden_aleatorio ASC')->all();
        $orden = Ordenproduccion::findOne($id);
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
                    ->setCellValue('A1', 'CODIGO')
                    ->setCellValue('B1', 'OPERACION')
                    ->setCellValue('C1', 'T. MAQUINA')
                    ->setCellValue('D1', 'OP)')
                    ->setCellValue('E1', 'CLIENTE')
                    ->setCellValue('F1', 'SEGUNDOS')
                    ->setCellValue('G1', 'MINUTOS')
                    ->setCellValue('H1', 'ORDENAMIENTO')
                    ->setCellValue('I1', 'OPERACION')
                    ->setCellValue('J1', 'PIEZA')
                    ->setCellValue('K1', 'F. CREACION')
                    ->setCellValue('L1', 'USUARIO');
        $i = 3;
        
        foreach ($flujo as $val) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->proceso->idproceso)
                    ->setCellValue('B' . $i, $val->proceso->proceso)
                    ->setCellValue('C' . $i, $val->tipomaquina->descripcion)
                    ->setCellValue('D' . $i, $id)                    
                    ->setCellValue('E' . $i, $orden->cliente->nombrecorto)                    
                    ->setCellValue('F' . $i, $val->segundos)
                    ->setCellValue('G' . $i, $val->minutos)
                    ->setCellValue('H' . $i, $val->orden_aleatorio)
                    ->setCellValue('I' . $i, $val->operacionPrenda)
                    ->setCellValue('J' . $i, $val->piezaPrenda)
                    ->setCellValue('K' . $i, $val->fecha_creacion)
                    ->setCellValue('L' . $i, $val->usuariosistema);
            $i++;
        }
        $j = $i + 1;
        $objPHPExcel->getActiveSheet()->getStyle($j)->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B' . $j, 'Sam Operativo: '. $orden->sam_operativo);
                $j = $j+1;
          $objPHPExcel->getActiveSheet()->getStyle($j)->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)                
                ->setCellValue('B' . $j, 'Sam Balanceo: '. $orden->sam_balanceo);
        $j = $j+1;
          $objPHPExcel->getActiveSheet()->getStyle($j)->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0) 
                ->setCellValue('B' . $j, 'Sam Preparacion: '. $orden->sam_preparacion);
        
        $objPHPExcel->getActiveSheet()->setTitle('Operaciones x orden');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Operaciones.xlsx"');
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
    
    //PROCESO QUE EXPORTA A EXCEL TODAS LAS MERDIDAS DE LA OP
    
      public function actionGenerarexcelmedidas($id) {
        $medida = PilotoDetalleProduccion::find()->where(['=','idordenproduccion', $id])->orderBy('iddetalleorden ASC')->all();
        $orden = Ordenproduccion::findOne($id);
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
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'CODIGO')
                    ->setCellValue('B1', 'OP')
                    ->setCellValue('C1', 'TALLA')
                    ->setCellValue('D1', 'CONCEPTO)')
                    ->setCellValue('E1', 'CLIENTE')
                    ->setCellValue('F1', 'MEDIDA FICHA AL')
                    ->setCellValue('G1', 'MEDIDA FICHA DL')
                    ->setCellValue('H1', 'MEDIDA CONFECCION AL')
                    ->setCellValue('I1', 'MEDIDA CONFECCION DL')
                    ->setCellValue('J1', 'TOLERANCIA AL')
                    ->setCellValue('K1', 'TOLERANCIA DL')
                    ->setCellValue('L1', 'FECHA REGISTRO')
                    ->setCellValue('M1', 'USUARIO')
                    ->setCellValue('N1', 'APLICADO')
                    ->setCellValue('O1', 'OBSERVACION AL')
                    ->setCellValue('P1', 'OBSERVACION DL');
        $i = 3;
        foreach ($medida as $val) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_proceso)
                    ->setCellValue('B' . $i, $orden->idordenproduccion)
                    ->setCellValue('C' . $i, $val->detalleorden->productodetalle->prendatipo->prenda.' / '.$val->detalleorden->productodetalle->prendatipo->talla->talla)
                    ->setCellValue('D' . $i, $val->concepto)                    
                    ->setCellValue('E' . $i, $orden->cliente->nombrecorto)                    
                    ->setCellValue('F' . $i, $val->medida_ficha_al)
                    ->setCellValue('G' . $i, $val->medida_ficha_dl)
                    ->setCellValue('H' . $i, $val->medida_confeccion_al)
                    ->setCellValue('I' . $i, $val->medida_confeccion_dl)
                    ->setCellValue('J' . $i, $val->tolerancia_al)
                    ->setCellValue('K' . $i, $val->tolerancia_dl)
                    ->setCellValue('L' . $i, $val->fecha_registro)
                    ->setCellValue('M' . $i, $val->usuariosistema)
                    ->setCellValue('N' . $i, $val->aplicadoproceso)
                    ->setCellValue('O' . $i, $val->observacion_al)
                    ->setCellValue('P' . $i, $val->observacion_dl);
            $i++;
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Medida_piltos.xlsx"');
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
