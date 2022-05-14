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
    public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',25])->all()){
                $searchModel = new OrdenproduccionSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
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
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_balanceo = Html::encode($form->id_balanceo);
                        $idordenproduccion = Html::encode($form->idordenproduccion);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $table = CantidadPrendaTerminadas::find()
                                ->andFilterWhere(['=', 'id_balanceo', $id_balanceo])
                                ->andFilterWhere(['=', 'idordenproduccion', $idordenproduccion])
                                ->andFilterWhere(['>=', 'fecha_entrada', $fecha_inicio])
                                ->andFilterWhere(['<=', 'fecha_entrada', $fecha_corte]);
                        $table = $table->orderBy('id_entrada DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 100,
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
                } else {
                    $table = CantidadPrendaTerminadas::find()
                             ->orderBy('id_entrada DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 100,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelConsultaUnidades($tableexcel);
                    }
                }
                $to = $count->count();
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
                            'pageSize' => 40,
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
                        'pageSize' => 40,
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
                            'pageSize' => 100,
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
                        'pageSize' => 100,
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
    public function actionView($id) {
        $modeldetalles = Ordenproducciondetalle::find()->Where(['=', 'idordenproduccion', $id])->all();
        $modeldetalle = new Ordenproducciondetalle();
        $mensaje = "";
        $otrosCostosProduccion = \app\models\OtrosCostosProduccion::find()->where(['=','idordenproduccion', $id])->orderBy('id_proveedor DESC')->all();
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
                $this->redirect(["orden-produccion/view", 'id' => $id]);
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
             return $this->redirect(['view', 'id' => $id]);
        }
        return $this->render('view', [
                    'model' => $this->findModel($id),
                    'modeldetalle' => $modeldetalle,
                    'modeldetalles' => $modeldetalles,
                    'mensaje' => $mensaje,
                    'otrosCostosProduccion' => $otrosCostosProduccion,
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
    
    /**
     * Creates a new Ordenproduccion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Ordenproduccion();
        $clientes = Cliente::find()->all();
        $codigos = Producto::find()->orderBy('idproducto desc')->all(); 
        $ordenproducciontipos = Ordenproducciontipo::find()->all();
        if ($model->load(Yii::$app->request->post())&& $model->save()) {
            $model->totalorden = 0;
            $model->estado = 0;
            $model->autorizado = 0;
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
            return $this->redirect(['index']);
        }

        return $this->render('create', [
                    'model' => $model,
              'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreClientes"),
                    'ordenproducciontipos' => ArrayHelper::map($ordenproducciontipos, "idtipo", "tipo"),
                    'codigos' => ArrayHelper::map($codigos, "codigo", "codigonombre"),
                    
        ]);
    }
    
    //NUEVA ORDER PARA TERCERO
    
     public function actionNuevaordentercero() {
        $model = new OrdenProduccionTercero();
        $clientes = Cliente::find()->all();
        $ordenproducciontipos = Ordenproducciontipo::find()->all();
        $codigos = Producto::find()->orderBy('idproducto desc')->all();        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $orden = Ordenproduccion::find()->where(['=','idcliente', $model->idcliente])
                                           ->andWhere(['=','codigoproducto', $model->codigo_producto])
                                           ->orderBy('idordenproduccion DESC')->one();
            $model->idordenproduccion = $orden->idordenproduccion;
            $model->usuariosistema = Yii::$app->user->identity->username;
            $model->autorizado = 0;
            $model->update();
            return $this->redirect(['indextercero']);
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
        $clientes = Cliente::find()->all();
        $orden = Ordenproduccion::find()->orderBy('idordenproduccion asc')->all();        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->usuariosistema = Yii::$app->user->identity->username;
            $orden = Ordenproduccion::findOne($model->idordenproduccion);
            $model->codigo_producto = $orden->codigoproducto;
            $model->update();
            return $this->redirect(['indexentradasalida']);
        }

        return $this->render('createsalida', [
                    'model' => $model,
                    'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreClientes"),
                    'orden' => ArrayHelper::map($orden, "idordenproduccion", "ordenproduccion"),
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
        $clientes = Cliente::find()->all();
        $ordenproducciontipos = Ordenproducciontipo::find()->all();
        $codigos = Producto::find()->where(['=','idcliente',$model->idcliente])->all();
        if (Ordenproducciondetalle::find()->where(['=', 'idordenproduccion', $id])->all() or $model->facturado == 1) {
            Yii::$app->getSession()->setFlash('warning', 'No se puede modificar la información, tiene detalles asociados');
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
                
               return $this->redirect(['index']);
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
        $clientes = Cliente::find()->all();
        $orden = Ordenproduccion::find()->orderBy('idordenproduccion DESC')->all(); 
        if (SalidaEntradaProduccionDetalle::find()->where(['=', 'id_salida', $id])->all()) {
            Yii::$app->getSession()->setFlash('warning', 'No se puede modificar la información, tiene detalles asociados');
        } else {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $orden_produccion = Ordenproduccion::find()->where(['=','idordenproduccion', $model->idordenproduccion])->one();
                $model->codigo_producto = $orden_produccion->codigoproducto;
                $model->update();
                return $this->redirect(['indexentradasalida']);
            }
        }
        return $this->render('updatesalida', [
                    'model' => $model,
                    'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreClientes"),
                  'orden' => ArrayHelper::map($orden, "idordenproduccion", "ordenproduccion"),
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
                return $this->redirect(['indextercero']);
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
        $detalle_piloto = \app\models\PilotoDetalleProduccion::find()->where(['=','idordenproduccion', $id])
                                                                     ->andWhere(['=','iddetalleorden', $iddetalle])   
                                                           ->orderBy('id_proceso DESC')->all(); 
        if (isset($_POST["actualizarLinea"])) {
            $intIndice = 0;
            foreach ($_POST["listado_piloto"] as $intCodigo) { 
                $table = PilotoDetalleProduccion::findOne($intCodigo);
                $table->concepto = $_POST["concepto"][$intIndice];
                $table->medida_ficha_tecnica = $_POST["medidafichatecnica"][$intIndice];
                $table->medida_confeccion = $_POST["medidaconfeccion"][$intIndice];
                if($table->medida_ficha_tecnica < $table->medida_confeccion){
                     $valor = $table->medida_confeccion - $table->medida_ficha_tecnica; 
                     $table->tolerancia = $valor;
                     if($valor > 1){
                         $table->observacion = 'Medidas fuera de la tolerancia'; 
                     }else{
                            $table->observacion = 'Medidas dentro de la tolerancia';
                     }
                }else{
                    $valor = $table->medida_confeccion - $table->medida_ficha_tecnica; 
                     $table->tolerancia = $valor;
                     if($valor < -1){
                        $table->observacion = 'Medidas fuera de la tolerancia'; 
                     }else{
                        $table->observacion = 'Medidas dentro de la tolerancia';
                     }
                }
                $table->save(false);
                $intIndice++;
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
            
        ]);
    }
    
    //PERMITE CREAR UNA LINEA EN LAS PILOTOS
    
    public function actionNuevalineamedida($iddetalle, $id) {
            $model = new PilotoDetalleProduccion();
            $model->iddetalleorden = $iddetalle;                
            $model->idordenproduccion = $id;
            $model->fecha_registro= date('Y-m-d');
            $model->medida_ficha_tecnica = 0;
            $model->medida_confeccion = 0;
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
                $table->iddetalleorden = $iddetalle;
                $table->idordenproduccion = $id;
                $table->concepto = $detalle->concepto;
                $table->medida_ficha_tecnica = 0;
                $table->medida_confeccion = 0;
                $table->fecha_registro = date('Y-m-d');
                $table->usuariosistema = Yii::$app->user->identity->username;
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

        ]);
    }
    
    public function actionAutorizado($id) {
        $model = $this->findModel($id);
        $mensaje = "";
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
                $this->redirect(["orden-produccion/view", 'id' => $id]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Para autorizar el registro, debe tener productos relacionados en la orden de producción.');
                $this->redirect(["orden-produccion/view", 'id' => $id]);
            }
        } else {
            $model->autorizado = 0;
            $model->update();
            $this->redirect(["orden-produccion/view", 'id' => $id]);
        }
    }
    
    public function actionAutorizadosalidaentrada($id) {
        $model = SalidaEntradaProduccion::findOne($id);
        $entrada = SalidaEntradaProduccion::findOne($id);
        if($model->total_cantidad > $model->ordenproduccion->cantidad){
           Yii::$app->getSession()->setFlash('warning', 'Las unidades de entrada no pueden ser mayores que la cantidad del lote.'); 
           $this->redirect(["orden-produccion/viewsalida", 'id' => $id]);
        }else{
            if ($model->autorizado == 0) {
                $model->autorizado = 1;
                $model->update();
                $this->redirect(["orden-produccion/viewsalida", 'id' => $id]);
            } else {
                $model->autorizado = 0;
                $model->update();
                $this->redirect(["orden-produccion/viewsalida", 'id' => $id]);
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
    public function actionNuevodetalles($idordenproduccion, $idcliente) {
        $ordenProduccion = Ordenproduccion::findOne($idordenproduccion);
        //$productosCliente = Productodetalle::find()->where(['=', 'idcliente', $idcliente])->andWhere(['=', 'idtipo', $ordenProduccion->idtipo])->andWhere(['>', 'stock', 0])->all();
        $productocodigo = Producto::find()->where(['=','idcliente',$idcliente])->andWhere(['=','codigo',$ordenProduccion->codigoproducto])->one();        
        if ($productocodigo){
            $productosCliente = Productodetalle::find()->where(['=', 'idproducto', $productocodigo->idproducto])->all();            
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
        $this->redirect(["orden-produccion/view", 'id' => $idordenproduccion]); 
        }                                       
        return $this->render('_formnuevodetalles', [
                    'productosCliente' => $productosCliente,
                    'idordenproduccion' => $idordenproduccion,
                    'ordenProduccion' => $ordenProduccion,
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
                        $table->insert(); 
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
    
    public function actionEditardetalle() {
        $iddetalleorden = Html::encode($_POST["iddetalleorden"]);
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
                                            
                    $table->update();
                    $ordenProduccion->update();
                    $this->Actualizarcantidad($idordenproduccion);
                    $this->redirect(["orden-produccion/view", 'id' => $idordenproduccion]);
                                            
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
    public function actionEditardetalles($idordenproduccion) {
        $mds = Ordenproducciondetalle::find()->where(['=', 'idordenproduccion', $idordenproduccion])->all();
        $error = 0;
        if (isset($_POST["iddetalleorden"])) {
            $intIndice = 0;
            foreach ($_POST["iddetalleorden"] as $intCodigo) {
                if ($_POST["cantidad"][$intIndice] > 0) {
                    $table = Ordenproducciondetalle::findOne($intCodigo);
                    $subtotal = $table->subtotal;
                    $cantidad = $table->cantidad;
                    $table->cantidad = $_POST["cantidad"][$intIndice];
                    $table->vlrprecio = $_POST["vlrprecio"][$intIndice];
                    $table->subtotal = $_POST["cantidad"][$intIndice] * $_POST["vlrprecio"][$intIndice];                    
                    $table->update();                        
                }
                $intIndice++;
            }
            $this->Actualizartotal($idordenproduccion);
            $this->Actualizarcantidad($idordenproduccion);
            $this->redirect(["orden-produccion/view", 'id' => $idordenproduccion]);            
        }
        return $this->render('_formeditardetalles', [
                    'mds' => $mds,
                    'idordenproduccion' => $idordenproduccion,
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
                    $table->update();
                    $this->ActualizarValorTercero($id);
                    $this->redirect(["orden-produccion/viewtercero", 'id' => $id]);
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
        $orden = Ordenproduccion::findOne($idordenproduccion);
        if (isset($_POST["id"])) {
            $intIndice = 0;
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

    public function actionEliminardetalle() {
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
                    $this->redirect(["orden-produccion/view", 'id' => $idordenproduccion]);
                } catch (IntegrityException $e) {
                    $this->redirect(["orden-produccion/view", 'id' => $idordenproduccion]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en ficha de operaciones');
                } catch (\Exception $e) {
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en ficha de operaciones');
                    $this->redirect(["orden-produccion/view", 'id' => $idordenproduccion]);
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
    
   
    public function actionEliminardetalles($idordenproduccion) {
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
                    $this->redirect(["orden-produccion/view", 'id' => $idordenproduccion]);
                }
                
            } else {
                $mensaje = "Debe seleccionar al menos un registro";
            }
        }
        return $this->render('_formeliminardetalles', [
                    'mds' => $mds,
                    'idordenproduccion' => $idordenproduccion,
                    'mensaje' => $mensaje,
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
            $cantidad = $cantidad + $val->cantidad;
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
            $total = $total + $val->subtotal;
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
            $ordenproduccion = null;
            $idtipo = null;
            $codigoproducto = null;
            $clientes = Cliente::find()->all();
            $ordenproducciontipos = Ordenproducciontipo::find()->all();
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $ordenproduccion = Html::encode($form->ordenproduccion);
                    $idtipo = Html::encode($form->idtipo);
                    $codigoproducto = Html::encode($form->codigoproducto);
                    $table = Ordenproduccion::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['like', 'ordenproduccion', $ordenproduccion])
                            ->andFilterWhere(['=', 'codigoproducto', $codigoproducto])
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
                        ->where(['=', 'idtipo', 1])
                        ->orWhere(['=', 'idtipo', 4])
                        ->andWhere(['=','aplicar_balanceo', 1])
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
            //$cont = count($procesos);
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
    
    public function actionEliminar($id,$detalle)
    {                                
        $detalle = \app\models\OtrosCostosProduccion::findOne($detalle);
        $detalle->delete();
        $this->redirect(["view",'id' => $id]);        
    }
    
    //VISTA DE CANTIDADES CONFECCINADAS POR TALLAS
    
    public function actionVistatallas($iddetalleorden, $modulo)
    {
      $detalletallas = Ordenproducciondetalle::findOne($iddetalleorden);  
      $cantidades = CantidadPrendaTerminadas::find()->where(['=','iddetalleorden', $iddetalleorden])->orderBy('id_entrada DESC')->all();
      $cantidad_preparacion = CantidadPrendaTerminadasPreparacion::find()->where(['=','iddetalleorden', $iddetalleorden])->orderBy('id_proceso DESC')->all();
       return $this->render('vistatallas', [
                    'detalletallas' => $detalletallas, 
                    'cantidades' => $cantidades,
                    'cantidad_preparacion' => $cantidad_preparacion,
                    'modulo' => $modulo,
                    'iddetalleorden' => $iddetalleorden,
           
                    
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
           echo $tabla->cantidad_operada = $cantidadoperada / $cont;
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
                echo "<option value='$row->idordenproduccion' required>$row->ordenproduccion</option>";
            }
        }
    }
    
    //codigo que permite subir las prendas terminas
    public function actionSubirprendaterminada($id_balanceo, $idordenproduccion, $id_proceso_confeccion)
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
                                    $table->usuariosistema = Yii::$app->user->identity->username;
                                    $table->observacion = $model->observacion;
                                    $table->iddetalleorden = $intCodigo;
                                    $table->id_proceso_confeccion = $id_proceso_confeccion;
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
            $ordenproduccionext = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $codigoproducto = Html::encode($form->codigoproducto);
                    $facturado = Html::encode($form->facturado);
                    $tipo = Html::encode($form->tipo);
                    $ordenproduccionint = Html::encode($form->ordenproduccionint);
                    $ordenproduccionext = Html::encode($form->ordenproduccionext);
                    $table = Ordenproduccion::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['>=', 'fechallegada', $desde])
                            ->andFilterWhere(['<=', 'fechallegada', $hasta])
                            ->andFilterWhere(['=', 'facturado', $facturado])
                            ->andFilterWhere(['=', 'idtipo', $tipo])
                            ->andFilterWhere(['=', 'ordenproduccion', $ordenproduccionint])
                            ->andFilterWhere(['=', 'codigoproducto', $codigoproducto])
                            ->andFilterWhere(['=', 'ordenproduccionext', $ordenproduccionext]);
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
                            ->andFilterWhere(['like', 'ordenproduccion', $ordenproduccion])
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
              
        return $this->render('view_consulta', [
                    'model' => $this->findModel($id),
                    'modeldetalle' => $modeldetalle,
                    'modeldetalles' => $modeldetalles,
                    'mensaje' => $mensaje,
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
       
        return $this->render('eficienciafecha', [
                        'unidades' => $unidades,
                        'id_balanceo' => $id_balanceo,
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
    
      public function actionNuevocostoproduccion($id)
    {
        $compras = \app\models\Compra::find()->where(['=','autorizado', 1])->andWhere(['=','id_tipo_compra', 1])->orderBy('id_proveedor, factura DESC')->all();
        $form = new \app\models\FormCompraBuscar();
        $factura = null;
        $id_proveedor = null;
        $mensaje = '';
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $factura = Html::encode($form->factura);                                
                $id_proveedor = Html::encode($form->id_proveedor);
                if ($factura or $id_proveedor){
                    $compras = \app\models\compra::find()
                            ->where(['like','factura',$factura])
                            ->orwhere(['=','id_proveedor',$id_proveedor])
                            ->andWhere(['=', 'id_tipo_compra', 1])
                            ->orderBy('id_proveedor, factura DESC')
                            ->all();
                }               
            } else {
                $form->getErrors();
            }                    
        } else {
            $compras = \app\models\Compra::find()->where(['=','autorizado', 1])->andWhere(['=','id_tipo_compra', 1])->orderBy('id_proveedor DESC')->all();
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
               $this->redirect(["orden-produccion/view", 'id' => $id]);
        }else{
                
        }
        return $this->render('_nuevocostoproduccion', [
            'compras' => $compras,            
            'mensaje' => $mensaje,
            'id' => $id,
            'form' => $form,

        ]);
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
                    ->setCellValue('B1', 'Cod Producto')
                    ->setCellValue('C1', 'Cliente')
                    ->setCellValue('D1', 'Orden Prod Int')
                    ->setCellValue('E1', 'Orden Prod Ext')
                    ->setCellValue('F1', 'Fecha Llegada')
                    ->setCellValue('G1', 'Fecha Proceso')
                    ->setCellValue('H1', 'Fecha Entrega')
                    ->setCellValue('I1', 'Cantidad')
                    ->setCellValue('J1', 'Tipo')
                    ->setCellValue('K1', 'Total')
                    ->setCellValue('L1', 'Autorizado')
                    ->setCellValue('M1', 'Facturado')
                    ->setCellValue('N1', 'Observacion');
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
                    ->setCellValue('K' . $i, round($val->totalorden,0))
                    ->setCellValue('L' . $i, $val->autorizar)
                    ->setCellValue('M' . $i, $val->facturar)
                    ->setCellValue('N' . $i, $val->observacion);
            $i++;
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
                    ->setCellValue('J1', 'OBSERVACION');
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
                    ->setCellValue('J' . $i, $val->observacion);
                  
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
                    ->setCellValue('A' . $i, $val->id)
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
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'CODIGO')
                    ->setCellValue('B1', 'OP')
                    ->setCellValue('C1', 'TALLA')
                    ->setCellValue('D1', 'CONCEPTO)')
                    ->setCellValue('E1', 'CLIENTE')
                    ->setCellValue('F1', 'MEDIDA FICHA')
                    ->setCellValue('G1', 'MEDIDA CONFECCION')
                    ->setCellValue('H1', 'TOLERANCIA')
                    ->setCellValue('I1', 'FECHA REGISTRO')
                    ->setCellValue('J1', 'USUARIO')
                    ->setCellValue('K1', 'APLICADO')
                    ->setCellValue('L1', 'OBSERVACION');
        $i = 3;
        foreach ($medida as $val) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_proceso)
                    ->setCellValue('B' . $i, $orden->idordenproduccion)
                    ->setCellValue('C' . $i, $val->detalleorden->productodetalle->prendatipo->prenda.' / '.$val->detalleorden->productodetalle->prendatipo->talla->talla)
                    ->setCellValue('D' . $i, $val->concepto)                    
                    ->setCellValue('E' . $i, $orden->cliente->nombrecorto)                    
                    ->setCellValue('F' . $i, $val->medida_ficha_tecnica)
                    ->setCellValue('G' . $i, $val->medida_confeccion)
                    ->setCellValue('H' . $i, $val->tolerancia)
                    ->setCellValue('I' . $i, $val->fecha_registro)
                    ->setCellValue('J' . $i, $val->usuariosistema)
                    ->setCellValue('K' . $i, $val->aplicadoproceso)
                    ->setCellValue('L' . $i, $val->observacion);
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
