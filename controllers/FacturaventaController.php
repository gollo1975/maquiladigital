<?php

namespace app\controllers;

use Yii;
use Codeception\Module\Cli;
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
use GuzzleHttp;
use yii\base\Request;

use app\models\Facturaventa;
use app\models\FacturaventaSearch;
use app\models\Facturaventadetalle;
use app\models\FormFiltroConsultaFacturaventa;
use app\models\FormFacturaventalibre;
use app\models\FormFacturaventanuevodetallelibre;
use app\models\Productodetalle;
use app\models\Facturaventatipo;
use app\models\Matriculaempresa;
use app\models\Cliente;
use app\models\Ordenproduccion;
use app\models\UsuarioDetalle;
use app\models\Consecutivo;
use app\models\Ordenproducciondetalle;
use app\models\Resolucion;
use app\models\Producto;


/**
 * FacturaventaController implements the CRUD actions for Facturaventa model.
 */
class FacturaventaController extends Controller
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
     * Lists all Facturaventa models.
     * @return mixed
     */
    public function actionIndex($token = 0) {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',26])->all()){
            $form = new FormFiltroConsultaFacturaventa();
            $idcliente = null;
            $desde = null;
            $hasta = null;
            $numero = null;
            $pendiente = null;
            $tipo_servicio = null; $ordenProduccion = null;
            $ordenproduccion = Ordenproduccion::find()->orderBy('idordenproduccion DESC')->all();
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $numero = Html::encode($form->numero);
                    $pendiente = Html::encode($form->pendiente);
                    $ordenProduccion = Html::encode($form->ordenProduccion);
                    $tipo_servicio = Html::encode($form->tipo_servicio);
                    $table = Facturaventa::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['between', 'fecha_inicio', $desde, $hasta])
                            ->andFilterWhere(['=', 'id_factura_venta_tipo', $tipo_servicio])
                            ->andFilterWhere(['=', 'idordenproduccion', $ordenProduccion])
                            ->andFilterWhere(['=', 'nrofactura', $numero]);
                    if ($pendiente == 1){
                        $table = $table->andFilterWhere(['>', 'saldo', $pendiente]);
                    }        
                    $table = $table->orderBy('idfactura desc');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 20,
                        'totalCount' => $count->count()
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){
                        
                        $this->actionExcelconsulta($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = Facturaventa::find()
                        ->orderBy('idfactura desc');
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 20,
                    'totalCount' => $count->count(),
                ]);
                $tableexcel = $table->all();
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){                    
                    $this->actionExcelconsulta($tableexcel);
                }
            }
            $to = $count->count();
            return $this->render('index', [
                        'model' => $model,
                        'form' => $form,
                        'pagination' => $pages,
                        'token' => $token,
                        'ordenproduccion' => ArrayHelper::map($ordenproduccion, 'idordenproduccion', 'OrdenProduccion'),
            ]);
        }else{
            return $this->redirect(['site/sinpermiso']);
        }
        }else{
            return $this->redirect(['site/login']);
        }
    }
   
    //CONSULTA DE FACTURAS
    public function actionIndexconsulta() {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',40])->all()){
            $form = new FormFiltroConsultaFacturaventa();
            $idcliente = null;
            $desde = null;
            $hasta = null;
            $numero = null;
            $pendiente = null;
            $tipo_servicio = null; $ordenProduccion = null;
            $ordenproduccion = Ordenproduccion::find()->orderBy('idordenproduccion DESC')->all();
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $numero = Html::encode($form->numero);
                    $pendiente = Html::encode($form->pendiente);
                    $tipo_servicio = Html::encode($form->tipo_servicio);
                    $ordenProduccion = Html::encode($form->ordenProduccion);
                    $table = Facturaventa::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['between', 'fecha_inicio', $desde, $hasta])
                            ->andFilterWhere(['=', 'nrofactura', $numero])
                             ->andFilterWhere(['=', 'idordenproduccion', $ordenProduccion])
                            ->andFilterWhere(['=', 'id_factura_venta_tipo', $tipo_servicio])
                            ->andWhere(['>', 'nrofactura', 0]);
                    if ($pendiente == 1){
                        $table = $table->andFilterWhere(['>', 'saldo', $pendiente]);
                    }        
                    $table = $table->orderBy('idfactura desc');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 20,
                        'totalCount' => $count->count()
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){
                        
                        $this->actionExcelconsulta($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = Facturaventa::find()->andWhere(['>', 'nrofactura', 0])
                        ->orderBy('idfactura desc');
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 20,
                    'totalCount' => $count->count(),
                ]);
                $tableexcel = $table->all();
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){                    
                    $this->actionExcelconsulta($tableexcel);
                }
            }
            $to = $count->count();
            return $this->render('index_consulta', [
                        'model' => $model,
                        'form' => $form,
                        'pagination' => $pages,
                        'ordenproduccion' => ArrayHelper::map($ordenproduccion, 'idordenproduccion', 'OrdenProduccion'),
            ]);
        }else{
            return $this->redirect(['site/sinpermiso']);
        }
        }else{
            return $this->redirect(['site/login']);
        }
    }
    /**
     * Displays a single Facturaventa model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token)
    {
        $modeldetalles = Facturaventadetalle::find()->Where(['=', 'idfactura', $id])->all();
        $modeldetalle = new Facturaventadetalle();
        $mensaje = "";                        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modeldetalle' => $modeldetalle,
            'modeldetalles' => $modeldetalles,
            'mensaje' => $mensaje,
            'token' => $token,
        ]);
    }

    /**
     * Creates a new Facturaventa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Facturaventa();
        $clientes = Cliente::find()->orderBy('nombrecorto ASC')->all();
        $facturastipo = Facturaventatipo::find()->all();
        $ordenesproduccion = Ordenproduccion::find()->Where(['=', 'autorizado', 1])->andWhere(['=', 'facturado', 0])->all();
        $resolucion = Resolucion::find()->where(['=', 'activo', 0])->andWhere(['=','id_documento', 1])->one();
        if(!$resolucion){
            Yii::$app->getSession()->setFlash('error', 'La tabla de soluciones no puede ser vacia. Favor valide la resolucion de factura.');
            return $this->redirect(['index']);
        }
        $sw = 0;
        $fecha_actual = date('Y-m-d');
        
        if ($model->load(Yii::$app->request->post())){
            $model->save();
            $table = Cliente::find()->where(['=', 'idcliente', $model->idcliente])->one();
            $fecha = date('Y-m-d');
            $nuevafecha = strtotime ( '+'.$table->plazopago.' day' , strtotime ( $fecha ) ) ;
            $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
            $model->idresolucion = $resolucion->idresolucion;
            $model->numero_resolucion = $resolucion->nroresolucion;
            $model->fecha_vencimiento = $nuevafecha;
            $model->id_forma_pago = $table->id_forma_pago;
            $model->plazopago = $table->plazopago;
            $model->porcentajefuente = 0;
            $model->porcentajeiva = 0;
            $model->porcentajereteiva = 0;
            $model->subtotal = 0;
            $model->retencionfuente = 0;
            $model->retencioniva = 0;
            $model->impuestoiva = 0;
            $model->saldo = 0;
            $model->totalpagar = 0;
            $model->valorletras = "-" ;
            $model->usuariosistema = Yii::$app->user->identity->username;   
            $model->consecutivo = $resolucion->consecutivo;
            $model->fecha_inicio = $fecha;
            $model->save(false);
            return $this->redirect(['view','id' => $model->idfactura, 'token' => 0]);

        }
        $fecha_actual_dia = date('Y-m-d');
        if($fecha_actual_dia >= $resolucion->fecha_notificacion){
            $sw = 1; //aviso que se le esta venciendo la resolucion
        }


        return $this->render('create', [
            'model' => $model,
            'sw' => $sw,
            'resolucion' => $resolucion,
            'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreclientes"),
            'ordenesproduccion' => ArrayHelper::map($ordenesproduccion, "idordenproduccion", "idordenproduccion"),
            'facturastipo' => ArrayHelper::map($facturastipo, "id_factura_venta_tipo", "concepto"),
        ]);
    }
    
    public function actionCreatelibre()
    {
        $model = new FormFacturaventalibre();
        $clientes = Cliente::find()->orderBy('nombrecorto ASC')->all();
        $facturastipo = Facturaventatipo::find()->all();
        $resolucion = Resolucion::find()->where(['=', 'activo', 0])->andWhere(['=','id_documento', 1])->one();
        if(!$resolucion){
            Yii::$app->getSession()->setFlash('error', 'La tabla de soluciones no puede ser vacia. Favor valide la resolucion de factura.');
            return $this->redirect(['index']);
        } 
        if ($model->load(Yii::$app->request->post())) {            
            $table = Cliente::find()->where(['=', 'idcliente', $model->idcliente])->one();
            $fecha = date( $model->fechainicio);
            $nuevafecha = strtotime ( '+'.$table->plazopago.' day' , strtotime ( $fecha ) ) ;
            $nuevafecha = date ( 'Y-m-j' , $nuevafecha );
            $facturalibre = new Facturaventa;
            $facturalibre->fecha_inicio = $model->fechainicio;
            $facturalibre->idcliente = $model->idcliente;
            $facturalibre->observacion = $model->observacion;
            $facturalibre->idresolucion = $resolucion->idresolucion;
            $facturalibre->numero_resolucion = $resolucion->nroresolucion;
            $facturalibre->fecha_vencimiento = $nuevafecha;
            $facturalibre->id_forma_pago = $table->id_forma_pago;
            $facturalibre->plazopago = $table->plazopago;
            $facturalibre->porcentajefuente = 0;
            $facturalibre->porcentajeiva = 0;
            $facturalibre->porcentajereteiva = 0;
            $facturalibre->subtotal = 0;
            $facturalibre->retencionfuente = 0;
            $facturalibre->retencioniva = 0;
            $facturalibre->impuestoiva = 0;
            $facturalibre->saldo = 0;
            $facturalibre->totalpagar = 0;
            $facturalibre->valorletras = "-" ;
            $facturalibre->usuariosistema = Yii::$app->user->identity->username;
            $facturalibre->idresolucion = $resolucion->idresolucion;
            $facturalibre->libre = 1;
            $facturalibre->id_factura_venta_tipo = $model->id_factura_venta_tipo;
            $facturalibre->consecutivo = $resolucion->consecutivo;
            $facturalibre->save(false);
            return $this->redirect(['index']);
        }

        return $this->render('_formlibre', [
            'model' => $model,
            'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreclientes"),
            'facturastipo' => ArrayHelper::map($facturastipo, "id_factura_venta_tipo", "concepto"),
        ]);
    }

    /**
     * Updates an existing Facturaventa model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->libre == 1){
            return $this->redirect(['updatelibre', 'id' => $id]);
        }else{
          
            $clientes = Cliente::find()->all();
            $table = Facturaventa::find()->where(['idfactura' => $id])->one();
            $facturastipo = Facturaventatipo::find()->all();
            $ordenesproduccion = Ordenproduccion::find()->Where(['=', 'idcliente', $table->idcliente])->orderBy('idordenproduccion DESC')->all();
            $ordenesproduccion = ArrayHelper::map($ordenesproduccion, "idordenproduccion", "ordenProduccion");
            if(Facturaventadetalle::find()->where(['=', 'idfactura', $id])->all() or $model->estado <> 0){
               Yii::$app->getSession()->setFlash('warning', 'No se puede modificar la información, tiene detalles asociados');
            }else {
            
                if($model->load(Yii::$app->request->post())){
                    $model->idordenproduccion = $model->idordenproduccion;
                    $fecha = date($model->fecha_inicio);
                    $model->fecha_inicio = $fecha;
                    $clientes = Cliente::findOne($model->idcliente);
                    if($model->idcliente != $table->idcliente){
                        $resolucion = Resolucion::find()->where(['=', 'activo', 0])->andWhere(['=','id_documento', 1])->one();
                        $fecha = date($model->fecha_inicio);
                        $nuevafecha = strtotime ( '+'.$clientes->plazopago.' day' , strtotime ($fecha) ) ;
                        $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
                        $model->idresolucion = $resolucion->idresolucion;
                        $model->numero_resolucion = $resolucion->nroresolucion;
                        $model->fecha_vencimiento = $nuevafecha;
                        $model->id_forma_pago = $clientes->id_forma_pago;
                        $model->plazopago = $clientes->plazopago;
                        $model->save(false);
                    }else{
                      
                       $nuevafecha = strtotime ( '+'.$clientes->plazopago.' day' , strtotime ($fecha) ) ;
                       $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
                       $model->fecha_vencimiento = $nuevafecha;
                       $model->save(false); 
                    }
                    return $this->redirect(['view','id' => $id, 'token' => 0]);

                }    
                  
            }
           
        }
        return $this->render('update', [
            'model' => $model,
            'clientes' => ArrayHelper::map($clientes, "idcliente", "nombrecorto"),
            'ordenesproduccion' => $ordenesproduccion,
            'facturastipo' => ArrayHelper::map($facturastipo, "id_factura_venta_tipo", "concepto"),
            'sw' => 0,

        ]);
    }
    
    public function actionUpdatelibre($id) {
        $model = new FormFacturaventalibre;
        $clientes = Cliente::find()->all();
        $facturastipo = Facturaventatipo::find()->all();
        $table = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if(Facturaventadetalle::find()->where(['=', 'idfactura', $id])->all() or $table->estado <> 0){
           Yii::$app->getSession()->setFlash('warning', 'No se puede modificar la información, tiene detalles asociados');
        }
        else{
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                
                if ($table) {
                    $table->idcliente = $model->idcliente;
                    $table->fecha_inicio = $model->fechainicio;                    
                    $table->observacion = $model->observacion;
                    $table->id_factura_venta_tipo = $model->id_factura_venta_tipo;
                    $table->nrofacturaelectronica = $model->nrofacturaelectronica;
                    if ($table->save(false)) {
                        $msg = "El registro ha sido actualizado correctamente";
                        return $this->redirect(["index"]);
                    } else {
                        $msg = "El registro no sufrio ningun cambio";
                        return $this->redirect(["index"]);
                    }
                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                }
            } else {
                $model->getErrors();
            }
        }
        }
        if (Yii::$app->request->get("id")) {
            $table = $this->findModel($id);
            if ($table) {
                $model->idcliente = $table->idcliente;
                $model->fechainicio = $table->fecha_inicio;
                $model->id_factura_venta_tipo = $table->id_factura_venta_tipo;                
                $model->observacion = $table->observacion;      
                $model->nrofacturaelectronica = $table->nrofacturaelectronica;      
            } else {
                return $this->redirect(["index"]);
            }
        } else {
            return $this->redirect(["index"]);
        }
        return $this->render('_formlibre', [
            'model' => $model,
            'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreclientes"),
            'facturastipo' => ArrayHelper::map($facturastipo, "id_factura_venta_tipo", "concepto"),
        ]);
    }

    /**
     * Deletes an existing Facturaventa model.
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
            $this->redirect(["facturaventa/index"]);
        } catch (IntegrityException $e) {
            $this->redirect(["facturaventa/index"]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar la factura de venta, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar la factura de venta, tiene registros asociados en otros procesos');
            $this->redirect(["facturaventa/index"]);
        }
    }

    /**
     * Finds the Facturaventa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Facturaventa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionNuevodetalles($idordenproduccion,$idfactura, $token)
    {
        $factura = Facturaventa::findOne($idfactura);
        $conceptos = \app\models\ConceptoFacturacion::find()->where(['=', 'codigo_interfaz', $factura->id_factura_venta_tipo])->all();
        if(Yii::$app->request->post()) {
            if (isset($_POST["iddetalleorden"])) {
                $intIndice = 0;
                foreach ($_POST["iddetalleorden"] as $intCodigo) {
                    $orden = Ordenproduccion::findOne($idordenproduccion);
                    $detalleOrden = Ordenproducciondetalle::find()->where(['=','idordenproduccion', $idordenproduccion])->one();
                    $items = \app\models\ConceptoFacturacion::findOne($intCodigo);
                    if(!$detalleOrden){
                       Yii::$app->getSession()->setFlash('error', 'Esta orden de produccion no tiene tallas asociadas para el proceso.'); 
                       $this->redirect(["facturaventa/view", 'id' => $idfactura, 'token' => $token]);
                    }else{
                         $detalleFactura = Facturaventadetalle::find()->where(['=','id', $intCodigo])->andWhere(['=','idfactura', $idfactura])->one();
                        if($detalleFactura){
                            Yii::$app->getSession()->setFlash('warning', 'Este concepto ya esta agregado en el detalle de la factura.'); 
                            return $this->redirect(["facturaventa/view", 'id' => $idfactura, 'token' => $token]);
                        }else{
                            $table = new Facturaventadetalle();
                            $table->idfactura = $idfactura;
                            $table->codigoproducto = $detalleOrden->codigoproducto;
                            $table->cantidad = $orden->cantidad;
                            $table->preciounitario = $detalleOrden->vlrprecio;
                            $table->porcentaje_iva= $items->porcentaje_iva;
                            $table->porcentaje_retefuente = $items->porcentaje_retencion;
                            $table->total = round($orden->cantidad * $detalleOrden->vlrprecio);
                            $table->id = $intCodigo;
                            $table->save(false);
                            $iddetallefactura = Facturaventadetalle::find()->orderBy('iddetallefactura DESC')->one();
                            $iddetallefactura = $iddetallefactura->iddetallefactura;
                            $this->ActualizaTotales($iddetallefactura, $idfactura);
                        }    
                    }        
                }
                $this->redirect(["facturaventa/view", 'id' => $idfactura, 'token' => $token]);
            }else{
                Yii::$app->getSession()->setFlash('warning', 'Debe de seleccionar al menos un registro.');
            }
        }

        return $this->render('_formnuevodetalles', [
            'conceptos' => $conceptos,
            'idfactura' => $idfactura,
            'token' => $token,

        ]);
    }
    
    public function actionNuevodetallelibre($id, $token) {
        $model = new FormFacturaventanuevodetallelibre;
        $factura = Facturaventa::findOne($id);        
        $conceptos = \app\models\ConceptoFacturacion::find()->where(['=','codigo_interfaz', 3])->all();      
        if ($model->load(Yii::$app->request->post())) {
            $buscar = \app\models\ConceptoFacturacion::findOne($model->idproducto); 
            $table = new Facturaventadetalle();
            $table->idfactura = $id;
            $table->id = $model->idproducto;
            $table->cantidad = $model->cantidad;
            $table->preciounitario = $model->valor;
            $table->porcentaje_iva = $buscar->porcentaje_iva;
            $table->porcentaje_retefuente = $buscar->porcentaje_retencion;
            $table->total = $table->preciounitario * $table->cantidad;                
            $table->save(false);                
            $idfactura = $factura->idfactura;
            $detalle = Facturaventadetalle::find()->orderBy('iddetallefactura DESC')->one();
            $iddetallefactura = $detalle->iddetallefactura;
            $this->ActualizaTotales($iddetallefactura, $idfactura);
            return $this->redirect(['view','id' => $id, 'token' => $token]);
        }
        return $this->renderAjax('_formnuevodetallelibre', [
            'model' => $model,
            'conceptos' => ArrayHelper::map($conceptos, "id", "concepto"),
        ]);        
    }

    public function actionEditardetalle($token)
    {
        $iddetallefactura = Html::encode($_POST["iddetallefactura"]);
        $idfactura = Html::encode($_POST["idfactura"]);

        if(Yii::$app->request->post()){
            if((int) $iddetallefactura)
            {
                $table = Facturaventadetalle::findOne($iddetallefactura);
                if ($table) {
                    $table->cantidad = Html::encode($_POST["cantidad"]);
                    $table->preciounitario = Html::encode($_POST["preciounitario"]);
                    $table->total = Html::encode($_POST["cantidad"]) * Html::encode($_POST["preciounitario"]);
                    $table->idfactura = Html::encode($_POST["idfactura"]);
                    $table->save(false);
                    $this->ActualizaTotales($iddetallefactura, $idfactura);
                    $this->redirect(["facturaventa/view",'id' => $idfactura, 'token' => $token]);
                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }
            }
        }
    }
    
    //PROCESO QUE TOTALIZA LOS REGISTRO
    protected function ActualizaTotales($iddetallefactura, $idfactura) {
        $factura = Facturaventa::findOne($idfactura);
        $detalle = Facturaventadetalle::findOne($iddetallefactura);
        $cliente = Cliente::find()->where(['=','idcliente', $factura->idcliente])->one();
        $empresa = Matriculaempresa::findOne(1);
        //actualiza detalle de la factura
         $retencion = 0; $iva = 0; $reteiva = 0;
         $iva = round(($detalle->total * $detalle->porcentaje_iva)/100);
         if($cliente->retencionfuente == 1){
             $retencion = round(($detalle->total * $detalle->porcentaje_retefuente)/100);
             $detalle->valor_retencion = $retencion;
         }else{
             $detalle->valor_retencion = 0;
         }
         $detalle->valor_iva = $iva;
         $detalle->total_linea = round(($detalle->total + $iva) - $retencion);
         $detalle->save(false);
         //PROCESO QUE ACTUALIZA LA FACTURA
         $detalleFactura = Facturaventadetalle::findOne($iddetallefactura);
         $factura->subtotal = $detalleFactura->total;
         $factura->porcentajefuente = $detalleFactura->porcentaje_retefuente;
         $factura->porcentajeiva = $detalleFactura->porcentaje_iva;
         $factura->retencionfuente = $detalleFactura->valor_retencion;
         $factura->impuestoiva = $detalleFactura->valor_iva;
         if ($cliente->retencioniva == 1){
             $reteiva = round(($detalleFactura->valor_iva * $empresa->porcentajereteiva)/100);
             $factura->retencioniva = $reteiva;
         }else{
             $factura->retencioniva = 0;
         }
         $factura->totalpagar = ($factura->subtotal +  $factura->impuestoiva) - ($factura->retencionfuente + $factura->retencioniva);
         $factura->saldo = $factura->totalpagar;
         $factura->save(false);
    }

    public function actionEliminardetalle($token)
    {
        if(Yii::$app->request->post())
        {
            $iddetallefactura = Html::encode($_POST["iddetallefactura"]);
            $idfactura = Html::encode($_POST["idfactura"]);
            if((int) $iddetallefactura)
            {
                $facturaDetalle = Facturaventadetalle::findOne($iddetallefactura);
                $total = $facturaDetalle->total;
                if(Facturaventadetalle::deleteAll("iddetallefactura=:iddetallefactura", [":iddetallefactura" => $iddetallefactura]))
                {
                    $factura = Facturaventa::findOne($idfactura);
                    $factura->porcentajeiva = 0;
                    $factura->porcentajereteiva = 0;
                    $factura->impuestoiva = 0;
                    //calculo de retefuente, reteiva
                    $factura->porcentajefuente = 0;
                    $factura->retencionfuente = 0;
                    $factura->retencioniva = 0;
                    $factura->totalpagar = 0;
                    $factura->saldo = 0;
                    $factura->subtotal = 0;
                    $factura->save(false);
                    $this->redirect(["facturaventa/view",'id' => $idfactura, 'token' => $token]);
                }
                else
                {
                    echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("facturaventa/index")."'>";
                }
            }
            else
            {
                echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("facturaventa/index")."'>";
            }
        }
        else
        {
            return $this->redirect(["facturaventa/index"]);
        }
    }

    public function actionAutorizado($id, $token)
    {
        $model = $this->findModel($id);
        $mensaje = "";
        if ($model->autorizado == 0){
            $detalles = Facturaventadetalle::find()
                ->where(['=', 'idfactura', $id])
                ->all();
            $reg = count($detalles);
            if ($reg <> 0) {
                $model->autorizado = 1;
                $model->save(false);
                return $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
            }else{
                Yii::$app->getSession()->setFlash('error', 'Para autorizar el registro, debe tener ordenes relacionados en la factura de venta.');
                return $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
            }
        } else {
            $factura = Facturaventa::findOne($id);
            if ($factura->nrofactura == 0){
                $model->autorizado = 0;
                $model->save(false);
                return $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
            }else {
                Yii::$app->getSession()->setFlash('error', 'No se puede desautorizar el registro, ya fue generado el número de factura.');
                return $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
            }
        }
    }

    public function actionGenerarnro($id, $token)
    {
        $model = $this->findModel($id);
        $mensaje = "";
        $factura = Facturaventa::findOne($id);
        $resolucion = Resolucion::findOne($factura->idresolucion);
        if ($factura->libre == 0){
            $ordenProduccion = Ordenproduccion::findOne($factura->idordenproduccion);
        }

        if ($factura->nrofactura == 0){
            $consecutivo = Consecutivo::findOne(1);// 1 factura de venta
            $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
            if($consecutivo->consecutivo <= $resolucion->final_rango){
                $factura->nrofactura = $consecutivo->consecutivo;
                $factura->save(false);
                $consecutivo->save(false);
                if ($factura->libre == 0 && $factura->tipo_facturacion == 0){
                    $ordenProduccion->facturado = 1;
                   $ordenProduccion->save(false);
                }            
                return $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
            }else{
                Yii::$app->getSession()->setFlash('error', 'Los consecutivos de facturación de esta resolución ya fueron consumidos en su totalidad. Debe de sacar otrsa resolución en la DIAN.'); 
                return $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
            }
        }else{
            Yii::$app->getSession()->setFlash('error', 'El registro ya fue generado.');
            return $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
        }
        
    }

    public function actionOrdenp($id){
        $rows = Ordenproduccion::find()->where(['idcliente' => $id])->andWhere(['autorizado' => 1])->andWhere(['facturado' => 0])->orderBy('idordenproduccion desc')->all();

        echo "<option value='' required>Seleccione...</option>";
        if(count($rows)>0){
            foreach($rows as $row){
                echo "<option value='$row->idordenproduccion' required>$row->ordenProduccion</option>";
            }
        }
    }

    protected function findModel($id)
    {
        if (($model = Facturaventa::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
       
    public function actionImprimir($id)
    {
                                
        return $this->render('../formatos/facturaVenta', [
            'model' => $this->findModel($id),
            
        ]);
    }
    
   
    
    public function actionViewconsulta($id)
    {
        $modeldetalles = Facturaventadetalle::find()->Where(['=', 'idfactura', $id])->all();
        $modeldetalle = new Facturaventadetalle();
        $mensaje = "";                        
        return $this->render('view_consulta', [
            'model' => $this->findModel($id),
            'modeldetalle' => $modeldetalle,
            'modeldetalles' => $modeldetalles,
            'mensaje' => $mensaje,
        ]);
    }
    
    
    
    // INICIO NUEVA FUNCION FACTURACION ELECTRONICA
    public function actionEnviar_documento_dian($id_factura, $token)
    {
        $factura = Facturaventa::findOne($id_factura);
        if (!$factura) {
            Yii::$app->session->setFlash('error', 'Factura no encontrada.');
            return $this->redirect(['facturaventa/index']);
        }

        //CONFIGURACION DE DOCUMENTOS
        $confi = \app\models\ConfiguracionDocumentoElectronico::findOne(1);
        if ($confi->aplica_factura_electronica == 0) {
            Yii::$app->session->setFlash('error', 'No esta autorizado para enviar facturas electronicas.');
            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
        }
        
        $cliente = Cliente::findOne($factura->idcliente);
        if (!$cliente) {
            Yii::$app->session->setFlash('error', 'Cliente no encontrado.');
            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
        }

        $detalle = Facturaventadetalle::find()->where(['idfactura' => $id_factura])->one();
        if (!$detalle) {
            Yii::$app->session->setFlash('error', 'No hay detalle de factura.');
            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
        }
        $nombre_empresa = Matriculaempresa::findOne(1);

        if($factura->cliente->autoretenedor == 1){ // si es autoretenedor
            $autoretendor = 9;
        } else {
            $autoretendor = 117;
        }

        if($factura->id_forma_pago == 1){ // efectivo
            $formapago = 1;
        } else {
            $formapago = 2;
        }

        // ENDPOINT
        $API_URL = Yii::$app->params['API_ENDPOINT_URL'];


        $apiBearerToken = $confi->llave_api_token;
        // TODO: mover a env/params en producción

        $fmt = fn($n) => number_format((float)$n, 2, '.', '');

        /* =========================
           number (DEBE IR EN RANGO)
           ========================= */
        // Permite probar por URL: &test_number=990000024

        // $testNumber = (int)Yii::$app->request->get('test_number', 0);

        $number = (int)($factura->nrofactura ?? 0);

        // Si viene fuera del rango, fuerza uno válido para pruebas

        $type_document_id  = 1;            // TODO: tipo real según documento
        $prefix            = $factura->consecutivo;        // TODO: traer de resolución en BD
        $resolution_number = $factura->numero_resolucion; // TODO: traer de resolución en BD/proveedor

        $date = $factura->fecha_inicio ? date('Y-m-d', strtotime($factura->fecha_inicio)) : date('Y-m-d');
        $time = $factura->fecha_inicio ? date('H:i:s', strtotime($factura->fecha_inicio)) : date('H:i:s');

        /* =========================
           municipality_id_fact
           ========================= */
        // ✅ Probar por URL: &test_muni=12590
        // $testMuni = (int)Yii::$app->request->get('test_muni', 0);

        // 1) Si viene por URL, manda ese sí o sí
        $municipality_id_fact = $factura->cliente->municipio->codefacturador;

        /* =========================
           CUSTOMER (con fallbacks)
           ========================= */
        $customer = [
            "identification_number"           => (string)($cliente->cedulanit),
            "name"                            => (string)($cliente->nombrecorto),
            "phone"                           => (string)($cliente->telefonocliente),
            "address"                         => (string)($cliente->direccioncliente),
            "email"                           => (string)($cliente->email_envio_factura_dian),
            // "merchant_registration"           => (string)($cliente->merchant_registration ), registro mercantil, por el momento es opcional
            "type_document_identification_id" => (int)($cliente->tipo->codigo_api),
            "type_organization_id"            => (int)($cliente->tiporegimen),
            "municipality_id_fact"            => $municipality_id_fact, // ✅ campo que falla
            "type_regime_id"                  => (int)($cliente->tiporegimen),
            "type_liability_id"               => $autoretendor,
            "dv"                              => (int)($cliente->dv),
        ];

        /* =========================
           DETALLE
           ========================= */
        $qty        = (float)($detalle->cantidad);

        $unit_price = (float)($detalle->preciounitario);

        // $line_total = (float)($detalle->total);


        $tax_id = 1; // TODO: IVA real

        $tax_totals = [[
            "tax_id"         => $tax_id,
            "tax_amount"     => $factura->impuestoiva,
            "percent"        => $factura->porcentajeiva,
            "taxable_amount" => $factura->subtotal,
        ]];

        $with_holding_tax_total = [];

        if ($factura->retencionfuente > 0) {
            $with_holding_tax_total[] = [
                "tax_id"         => 6, 
                "taxable_amount" => $fmt($factura->subtotal),
                "percent"        => $factura->porcentajefuente,
                "tax_amount"     => $fmt($factura->retencionfuente),
            ];
        }

        if ($factura->retencioniva > 0) {
            $with_holding_tax_total[] = [
                "tax_id"         => 5, 
                "taxable_amount" => $fmt($factura->subtotal),
                "percent"        => $factura->porcentajereteiva,
                "tax_amount"     => $fmt($factura->retencioniva),
            ];
        }


        $subtotal = $factura->subtotal;
        $iva = $factura->impuestoiva;

        $legal_monetary_totals = [
        "line_extension_amount"   => $fmt($subtotal),
        "tax_exclusive_amount"    => $fmt($subtotal),
        "tax_inclusive_amount"    => $fmt($subtotal + $iva),
        "allowance_total_amount"  => $fmt(0),
        "charge_total_amount"     => $fmt(0),
        "payable_amount"          => $fmt($subtotal + $iva), 
    ];

        $invoice_lines = [[
            "unit_measure_id"             => "70", // TODO: unidad real
            "invoiced_quantity"           => $fmt($qty),
            "line_extension_amount"       => $factura->subtotal,
            "free_of_charge_indicator"    => false,
            "allowance_charges"           => [],
            "tax_totals"                  => $tax_totals,
            "with_holding_tax_total"      => $with_holding_tax_total,
            "description"                 => (string)($detalle->conceptoFactura->concepto),
            "code"                        => (string)($detalle->codigoproducto),
            "type_item_identification_id" => 1,
            "price_amount"                => $unit_price,
            "base_quantity"               => 1,
        ]];

        $payment_form = [
            "payment_form_id"   => $formapago,
            "payment_method_id" => $factura->formaPago->codigo_medio_pago_dian,
            "payment_due_date"  => $factura->fecha_vencimiento,
            "duration_measure"  => $factura->plazopago,
        ];

        /* =========================
           PAYLOAD FINAL
           ========================= */
        $payload = [
            "number"                 => $number,
            "type_document_id"       => $type_document_id,
            "prefix"                 => $prefix,
            "sendmail"               => true,
            "resolution_number"      => $resolution_number,
            "customer"               => $customer,
            "tax_totals"             => $tax_totals,
            "legal_monetary_totals"  => $legal_monetary_totals,
            "invoice_lines"          => $invoice_lines,
            "with_holding_tax_total" => $with_holding_tax_total,
            "establishment_name"     => $nombre_empresa->razonsocialmatricula,
            "establishment_address"  => $nombre_empresa->direccionmatricula,
            "establishment_phone"    => $nombre_empresa->celularmatricula,
            "establishment_email"    => $nombre_empresa->emailmatricula,
            "notes"                  => $factura->observacion,
            "date"                   => $date,
            "time"                   => $time,
            "payment_form"           => $payment_form
        ];

        // ✅ LOG JSON ENVIADO COMPLETO
        Yii::info(
            "JSON ENVIADO A DIAN:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'invoice.debug.json'
        );

        /* =========================
           CURL
           ========================= */
        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $API_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonPayload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $apiBearerToken,
            ],
            CURLOPT_TIMEOUT => 60,
        ]);

        try {
            $response = curl_exec($curl);
            $info = curl_getinfo($curl);

            if (curl_errno($curl)) {
                $err = curl_error($curl);
                curl_close($curl);
                throw new \Exception("cURL: " . $err);
            }

            $headerSize = $info['header_size'] ?? 0;
            $rawBody    = $headerSize ? substr($response, $headerSize) : $response;
            $httpCode   = (int)($info['http_code'] ?? 0);
            curl_close($curl);

            Yii::info("HTTP_CODE={$httpCode}\nBODY:\n{$rawBody}", 'invoice.debug.response');

            $data = json_decode($rawBody, true);
            if (!is_array($data)) {
                throw new \Exception("API devolvió no-JSON. HTTP {$httpCode}. Body: {$rawBody}");
            }

            if ($httpCode < 200 || $httpCode >= 300) {
                $msg = $data['message'] ?? 'Error API';
                $errors = $data['errors'] ?? [];

                if (!empty($errors)) {
                    Yii::error([
                        'http_code' => $httpCode,
                        'message'   => $msg,
                        'errors'    => $errors,
                    ], 'invoice.debug.validation_errors');

                    $flat = [];
                    foreach ($errors as $field => $arr) {
                        $flat[] = $field . ': ' . (is_array($arr) ? implode(' | ', $arr) : $arr);
                    }
                    $msg .= " | " . implode(' || ', $flat);
                }

                throw new \Exception($msg);
            }


            $cufe = $data['cufe'] ?? $data['data']['cufe'] ?? null;
            $qr   = $data['qrstr'] ?? $data['data']['qrstr'] ?? ($data['QRStr'] ?? null);

            $factura->fecha_envio_begranda = date("Y-m-d H:i:s");

            if ($cufe) {
                $factura->cufe = $cufe;
                $factura->fecha_recepcion_dian = date("Y-m-d H:i:s");
            }

            if ($qr) {
                $factura->qrstr = $qr;
            }

            $factura->save(false);

            Yii::$app->session->setFlash('success', "Factura enviada OK. No ({$number}).");
            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);



        } catch (\Exception $e) {
            Yii::error("ERROR ENVÍO DIAN: " . $e->getMessage(), 'invoice.debug.error');
            Yii::$app->session->setFlash('error', 'Error al enviar factura: ' . $e->getMessage());
            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
        }


    }

// FIN NUEVA FUNCION FACTURACION ELECTRONICA

    public function actionReenviar_documento_dian($id_factura, $token)
    {
        // 1. Recuperar la factura
        $factura = Facturaventa::findOne($id_factura);
        if (!$factura) {
            Yii::$app->session->setFlash('error', 'Factura no encontrada.');
            return $this->redirect(['facturaventa/index']);
        }
        
        //buscar configuraicon
        $confi = \app\models\ConfiguracionDocumentoElectronico::findOne(1);

        // 2. Consultar el estado de la factura en el endpoint /state_check
        $prefix = $factura->consecutivo;
        $number = (int)($factura->nrofactura ?? 0);
        $API_URL = Yii::$app->params['API_ENDPOINT_URL'];
        $apiBearerToken = $confi->llave_api_token;

        // URL del endpoint para consultar el estado
        $url = "{$API_URL}/state_check/{$prefix}/{$number}";
        
        // Comprobaciones y log: asegurar que el número y prefijo son los esperados
        if ((int)$number <= 0) {
            throw new \Exception("Número de factura inválido para reenvío: {$number} (Factura id={$id_factura})");
        }

       // Yii::info("STATE_CHECK REQUEST URL={$url} factura_id={$id_factura} prefix={$prefix} number={$number}", 'invoice.debug.state_check_request');
        //Yii::$app->session->setFlash('info', "Consultando: {$url} (prefix={$prefix}, number={$number})");


        // Hacer la petición al endpoint
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiBearerToken,
                'Accept: application/json',
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 60,
        ]);
        
        try {
            // Ejecutar la solicitud cURL
            $response = curl_exec($curl);
            $info = curl_getinfo($curl);

            if (curl_errno($curl)) {
                $err = curl_error($curl);
                curl_close($curl);
                throw new \Exception("cURL Error: " . $err);
            }

            $headerSize = $info['header_size'] ?? 0;
            $rawBody    = $headerSize ? substr($response, $headerSize) : $response;
            $httpCode   = (int)($info['http_code'] ?? 0);
            curl_close($curl);

            // Decodificar la respuesta JSON
            $data = json_decode($rawBody, true);
            if (!is_array($data)) {
                Yii::error("STATE_CHECK NO-JSON RESPONSE for factura_id={$id_factura} prefix={$prefix} number={$number} HTTP={$httpCode} BODY={$rawBody}", 'invoice.debug.state_check');
                throw new \Exception("API devolvió no-JSON en state_check para factura {$prefix}/{$number}. HTTP {$httpCode}. Body guardado en logs.");
            }

            // Registrar la respuesta de state_check para diagnóstico
            Yii::info("STATE_CHECK HTTP_CODE={$httpCode}\nBODY:\n{$rawBody}", 'invoice.debug.state_check');

            // Verificar que esté listo para reenviar: preferimos `state_document` (0 = listo). Hacemos fallback a `state` por compatibilidad
            $stateForResend = $data['state_document'] ?? $data['state'] ?? null;
            // Asegurarnos de comparar como entero (la API puede devolver "0" como string)
            if ((int)$stateForResend !== 0) {
                Yii::$app->session->setFlash('error', 'la factura ya se encuentra en la DIAN.');
                return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
            }

            // 3. Recuperar el json de request_api y validar
            $request_api = $data['request_api'] ?? null;
            if (empty($request_api)) {
                throw new \Exception("No hay request_api para reenviar.");
            }

            // Realizar el envío de la factura: usamos el endpoint base ($API_URL) que recibe el payload original
            $send_url = $API_URL;
            Yii::info("Reenviando factura {$number} al endpoint: {$send_url}", 'invoice.debug.resend');
            
            // Hacer la solicitud para reenviar la factura
            $jsonPayload = json_encode($request_api, JSON_UNESCAPED_UNICODE);
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $send_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $jsonPayload,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $apiBearerToken,
                    'Accept: application/json',
                    'Content-Type: application/json',
                ],
                CURLOPT_TIMEOUT => 60,
            ]);

            // Ejecutar la solicitud para reenviar la factura
            $response = curl_exec($curl);
            $info = curl_getinfo($curl);

            if (curl_errno($curl)) {
                $err = curl_error($curl);
                curl_close($curl);
                throw new \Exception("cURL Error: " . $err);
            }

            $headerSize = $info['header_size'] ?? 0;
            $rawBody    = $headerSize ? substr($response, $headerSize) : $response;
            $httpCode   = (int)($info['http_code'] ?? 0);
            curl_close($curl);

            $data = json_decode($rawBody, true);
            if (!is_array($data)) {
                throw new \Exception("API devolvió no-JSON. HTTP {$httpCode}. Body: {$rawBody}");
            }

            // Si la respuesta es exitosa (HTTP 200), hacer la actualización del estado
            if ($httpCode === 200) {
                // 4. Actualizar el estado en el proveedor (endpoint /state/{prefix}/{number})
                // El proveedor espera: POST {$API_URL}/state/{$prefix}/{$number} con JSON {"type_document_id": <id>}
                $state_url = "{$API_URL}/state/{$prefix}/{$number}";

                // Preparar payload para actualizar el estado: proveedor espera {"state_document_id": 1}
                $stateDocumentToSend = $data['state_document_id'] ?? 1;
                $state_payload = json_encode(['state_document_id' => (int)$stateDocumentToSend], JSON_UNESCAPED_UNICODE);

                Yii::info("STATE UPDATE URL={$state_url} PAYLOAD={$state_payload}", 'invoice.debug.state_update');

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $state_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $state_payload,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $apiBearerToken,
                        'Accept: application/json',
                        'Content-Type: application/json',
                    ],
                    CURLOPT_TIMEOUT => 60,
                ]);
                $response = curl_exec($curl);
                $info = curl_getinfo($curl);

                if (curl_errno($curl)) {
                    $err = curl_error($curl);
                    curl_close($curl);
                    throw new \Exception("cURL Error (update state): " . $err);
                }

                $headerSize = $info['header_size'] ?? 0;
                $rawBody    = $headerSize ? substr($response, $headerSize) : $response;
                $httpCode   = (int)($info['http_code'] ?? 0);
                curl_close($curl);

                Yii::info("STATE_UPDATE HTTP_CODE={$httpCode}\nBODY:\n{$rawBody}", 'invoice.debug.state_update_response');

                // Procesar la respuesta
                if ($httpCode === 200) {
                    // Actualizar el estado en el registro local si la columna existe
                    if ($factura->hasAttribute('state_document_id')) {
                        $factura->state_document_id = 1;
                    } elseif ($factura->hasAttribute('state_document')) {
                        $factura->state_document = 1;
                    } else {
                        // Si no existe la columna, guardar la respuesta y marcar para reintento
                        if ($factura->hasAttribute('raw_state_update_response')) {
                            $factura->raw_state_update_response = $rawBody ?? '';
                        }
                        if ($factura->hasAttribute('attempt_count')) {
                            $factura->attempt_count = ($factura->attempt_count ?? 0) + 1;
                        }
                        

                        Yii::error("No column state_document_id on Facturaventa factura_id={$factura->idfactura}. Guardado raw_state_update_response y marcado para reenvío.", 'invoice.debug.state_update');

                    }

                    $factura->save(false);
                    Yii::$app->session->setFlash('success', "Factura reenviada y estado actualizado correctamente.");
                } else {
                    Yii::$app->session->setFlash('error', "Error al actualizar el estado de la factura.");
                }
            } else {
                Yii::$app->session->setFlash('error', "Error al reenviar la factura.");
            }

            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);

        } catch (\Exception $e) {
            Yii::error("ERROR REENVÍO DIAN: " . $e->getMessage(), 'invoice.debug.error');
            Yii::$app->session->setFlash('error', 'Error al reenviar factura: ' . $e->getMessage());
            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
        }
    }

//******NUEVO PROCESO DE REENVIO A LA DIAN*****//


//PROCESO DE ENVIO ACTUAL
//ENVIAR DOCUMENTOS A LA DIAN

//public function actionEnviar_documento_dian($id_factura, $token) {
//    // Inicializar variables necesarias
//    $factura = Facturaventa::findOne($id_factura);
//    $clientes = Cliente::findOne($factura->idcliente);
//    $detalle = Facturaventadetalle::find()->where(['=','idfactura', $id_factura])->one();
//
//    // Preparar datos para la API
//    $documentocliente = $clientes->cedulanit;
//    $tipodocumento = $clientes->tipo->codigo_api;
//    if($tipodocumento == 5){
//       $nombre_completo = $clientes->nombrecorto; 
//       $nombre_cliente = '.'; 
//       $apellido_cliente = '.';
//    }else{
//        $nombre_completo = $clientes->nombrecorto;
//        $nombre_cliente = '.'; 
//        $apellido_cliente = '.';
//    }
//    $direccioncliente = $clientes->direccioncliente;
//    $telefono = $clientes->telefonocliente;
//    $emailcliente = $clientes->email_envio_factura_dian;
//    $ciudad = $clientes->municipio->municipio;
//    $resolucion = $factura->resolucion->codigo_interfaz;
//    $consecutivo = $factura->nrofactura;
//    $formapago = $factura->formaPago->codigo_api; 
//    $fechainicio = $factura->fecha_inicio;
//    $observacion = $factura->observacion;
//    $codigoconcepto = $detalle->conceptoFactura->codigo_interfaz;
//    $concepto = $detalle->conceptoFactura->concepto;
//    $cantidad = $detalle->cantidad;
//    $valor_unitario = $detalle->preciounitario;
//    $subtotal = $detalle->total;
//   
//    if($clientes->retencioniva == 1){
//        $rete_iva = true;
//    }else{
//        $rete_iva = false; 
//    }
//    if($clientes->retencionfuente == 1){
//        $rete_fuente = true;
//    }else{
//        $rete_fuente = false;
//    }
//    
//
//    // Configurar cURL
//    $curl = curl_init();
//    $API_KEY = Yii::$app->params['API_KEY_PRODUCCION']; //api_key de produccion
//    $dataHead = json_encode([
//        "client" => [
//            "document" => "$documentocliente",
//            "document_type" => "$tipodocumento",
//            "first_name" => "$nombre_completo",
//            "last_name_one" => "$nombre_cliente",
//            "last_name_two" => "$apellido_cliente",
//            "address" => "$direccioncliente",
//            "phone" => "$telefono",
//            "email" => "$emailcliente",
//            "city" => "$ciudad"
//        ],
//        "observacion" => "$observacion",
//        "rete_iva" => "$rete_iva",
//        "rete_fuente" => "$rete_fuente",
//        "resolucion" => "$resolucion",
//        "consecutivo" => "$consecutivo",
//        "forma_pago" => "$formapago",
//        "date" => "$fechainicio"
//    ]);
//    $dataBody = json_encode([
//        [
//            "product" => $codigoconcepto,
//            "warehouse" => 1,
//            "qty" => $cantidad,
//            "concept" => "$concepto",
//            "average" => $valor_unitario,
//            "total" => $subtotal
//        ]
//    ]);
//
//    curl_setopt_array($curl, [
//        CURLOPT_URL => "http://begranda.com/equilibrium2/public/api/bill?key=$API_KEY",
//        CURLOPT_RETURNTRANSFER => true,
//        CURLOPT_CUSTOMREQUEST => 'POST',
//        CURLOPT_POSTFIELDS => [
//            "head" => $dataHead,
//            "body" => $dataBody
//        ],
//    ]);
//
//    try {
//        $response = curl_exec($curl);
//        if (curl_errno($curl)) {
//            throw new Exception(curl_error($curl));
//        }
//        curl_close($curl);
//        
//        $data = json_decode($response, true);
//        if ($data === null) {
//            throw new Exception('Error al decodificar la respuesta JSON');
//        }
//        
//        // Validar y extraer el CUFE
//        if (isset($data['add']['fe']['cufe'])) {
//            $cufe = $data['add']['fe']['cufe'];
//            $fechaRecepcion = isset($data["data"]["sentDetail"]["response"]["send_email_date_time"]) && !empty($data["data"]["sentDetail"]["response"]["send_email_date_time"]) ? $data["data"]["sentDetail"]["response"]["send_email_date_time"] : date("Y-m-d H:i:s");
//            $factura->fecha_recepcion_dian = $fechaRecepcion;
//            $factura->fecha_envio_begranda = date("Y-m-d H:i:s");
//            $factura->save(false);
//            if($cufe){
//                $factura->cufe = $cufe;
//                $qrstr = $data['add']['fe']['sentDetail']['response']['QRStr'];
//                $factura->qrstr = $qrstr;
//                $factura->save(false);
//                Yii::$app->getSession()->setFlash('success', "La factura de venta electrónica No ($consecutivo) se envió con éxito a la DIAN.");
//            }else{
//               $factura->fecha_envio_begranda = date("Y-m-d H:i:s");
//               $factura->save(false);
//               Yii::$app->getSession()->setFlash('warning', "La factura de venta electrónica No ($consecutivo) NO se envió a la DIAN. Favor reenviar el documento nuevamente.");
//               return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
//            } 
//            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
//        } else {
//            $factura->fecha_envio_begranda = date("Y-m-d H:i:s");
//            $factura->save(false);
//           Yii::$app->getSession()->setFlash('error', "La factura no se envio a la Dian y se encuentra en la API de comunicacion."); 
//           return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
//        }
//    } catch (Exception $e) {
//        Yii::$app->getSession()->setFlash('error', 'Error al enviar la factura: ' . $e->getMessage());
//    }
//
//    return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
//}
    
//   //PERMITE REENVIAR LA FACTURA SI NO SE CONECTA A LA DIAN
//    public function actionReenviar_documento_dian($id_factura, $token) {
//        // Instanciar la factura desde la base de datos
//        $factura = Facturaventa::findOne($id_factura);
//        if (!$factura) {
//            Yii::$app->getSession()->setFlash('error', 'Factura no encontrada.');
//            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
//        }
//        //ASIGNACION DE VARIABLES
//        $resolucion = $factura->resolucion->codigo_interfaz;
//        $consecutivo = $factura->nrofactura;
//        // URL y clave API
//        $API_URL = "http://begranda.com/equilibrium2/public/api/send-electronic-invoice";
//        $API_KEY = Yii::$app->params['API_KEY_PRODUCCION']; //api_key de produccion
//
//        // Inicializar CURL
//        $curl = curl_init();
//        curl_setopt_array($curl, [
//            CURLOPT_URL => "$API_URL?key=$API_KEY&consecutivo=$consecutivo&id_resolucion=$resolucion",
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 60,  // Timeout extendido
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS => [],
//        ]);
//
//        // Ejecutar la solicitud CURL y verificar la respuesta
//        $response = curl_exec($curl);
//        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//        curl_close($curl);
//
//        // Registrar la respuesta completa de la API para depuración
//        Yii::info("Respuesta completa de la API desde Begranda: $response", __METHOD__);
//
//        // Verificar errores de conexión o códigos HTTP inesperados
//        if ($response === false || $httpCode !== 200) {
//            $error = $response === false ? curl_error($curl) : "HTTP $httpCode";
//            Yii::$app->getSession()->setFlash('error', 'Hubo un problema al comunicarse con la DIAN. Intenta reenviar más tarde.');
//            Yii::error("Error en la solicitud CURL: $error", __METHOD__);
//            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
//        }
//
//        // Decodificar la respuesta JSON
//        $data = json_decode($response, true);
//
//        if (json_last_error() !== JSON_ERROR_NONE) {
//            Yii::$app->getSession()->setFlash('error', 'Error al procesar la respuesta de la DIAN. Intenta reenviar más tarde.');
//            Yii::error("Error al decodificar JSON: " . json_last_error_msg(), __METHOD__);
//            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
//        }
//
//        // Comprobamos el 'status' de la respuesta para determinar éxito o error
//        if (isset($data['status']) && $data['status'] == 'success') {
//            // Si la respuesta es exitosa
//            Yii::$app->getSession()->setFlash('success', "La factura de venta electrónica No ($consecutivo) se reenvió con éxito.");
//            
//            // Asignar CUFE y fecha de recepción solo si están disponibles en la respuesta
//            $cufe = isset($data["data"]["cufe"]) ? $data["data"]["cufe"] : "";
//            $qrstr = isset($data['data']['sentDetail']['response']['QRStr']) ? $data["data"]['sentDetail']['response']['QRStr'] : "";
//            $factura->cufe = $cufe;
//            $fechaRecepcion = isset($data["data"]["sentDetail"]["response"]["send_email_date_time"]) && !empty($data["data"]["sentDetail"]["response"]["send_email_date_time"]) ? $data["data"]["sentDetail"]["response"]["send_email_date_time"] : date("Y-m-d H:i:s");
//            $factura->fecha_recepcion_dian = $fechaRecepcion;
//            $factura->reenviar_factura = 0; // Marcar como no pendiente de reenvío
//            $factura->qrstr = $qrstr;
//            $factura->save(false);
//            Yii::info("Respuesta exitosa de la API: " . print_r($data, true), __METHOD__);
//        } else {
//            // Si el 'status' no es success o hay un mensaje de error
//            $errorMessage = isset($data['message']) ? $data['message'] : 'Error desconocido';
//            // Mostrar el mensaje específico de la API
//            Yii::$app->getSession()->setFlash('error', "No se pudo reenviar la factura. Error: $errorMessage.");
//            Yii::error("Error al reenviar factura No ($consecutivo): " . print_r($data, true), __METHOD__);
//            $factura->reenviar_factura = 1; // Mantener la factura pendiente de reenvío
//            $factura->save(false);
//        }
//
//        // Intentar guardar la factura en la base de datos
//        if (!$factura->save(false)) {
//            Yii::error("Error al guardar la factura No ($consecutivo): " . print_r($factura->errors, true), __METHOD__);
//            Yii::$app->getSession()->setFlash('error', 'Hubo un error al guardar la factura.');
//            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
//        }
//
//        // Redirigir a la vista de la factura
//        return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
//    }

    //PROCESO QUE BUSCA LA FACTURA DE LA API
    public function actionSearch_factura_dian($id_factura, $token) {
        
        //INSTANCIAR VARIABLES
        $factura = Facturaventa::findOne($id_factura);
        //variables encabezado
        $resolucion = $factura->resolucion->codigo_interfaz;
        $consecutivo = $factura->nrofactura;
             
        //PROCESO QUE BUSCA UNA FACTURA EN LA API
        $curl = curl_init();
        $API_KEY = Yii::$app->params['API_KEY_PRODUCCION']; //api_key de produccion
        $consecutivo_factura = "$consecutivo"; //CONSECUTIVO FACTURA
        $codigo_resolucion = "$resolucion"; //CÓDIGO DE LA RESOLUCIÓN QUE SE OBTIENE DESDE EL SISTEMA EN TABLAS>RESOLUCIONES

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://begranda.com/equilibrium2/public/api/invoice?key=$API_KEY&eq-consecutivo=$consecutivo_factura&eq-id_resolucion=$codigo_resolucion",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_POSTFIELDS => [],
        ));

        try{
            $response = curl_exec($curl); 
            if (curl_errno($curl)) {
                throw new Exception(curl_error($curl));
            }
            curl_close($curl);
            $data = json_decode($response, true);
            if ($data === null) {
                throw new Exception('Error al decodificar la respuesta JSON');
            }
            $data = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if($data == 200){
                $cufe = isset($data["data"]["qr"]) ? $data["data"]["qr"] : "";
                Yii::$app->getSession()->setFlash('info', 'La factura venta electronica No ('. $consecutivo .') se consulto con exito.');
                try {
                   
                } catch (Exception $e) {
                    // Manejar la excepción, por ejemplo, registrar un error o mostrar un mensaje al usuario
                    Yii::$app->getSession()->setFlash('error', 'Error al obtener el CUFE: ' . $e->getMessage());
                }
            }else{
                Yii::$app->getSession()->setFlash('error', 'Problemas de comunicacion en la consulta');
                
            }
        } catch (Exception $ex) {
             Yii::$app->getSession()->setFlash('error', 'Error al enviar la factura: ' . $e->getMessage());
        }
       return $this->redirect(['facturaventa/view','id' => $id_factura, 'token' => $token]); 
    }
    
    public function actionDesactivado($token, $id){
       return $this->redirect(['facturaventa/view','id' => $id, 'token' => $token]);  
    }
    
    ///exceles
    
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
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Id')
                    ->setCellValue('B1', 'N° Factura')
                    ->setCellValue('C1', 'N° Factura')
                    ->setCellValue('D1', 'Cliente')
                    ->setCellValue('E1', 'Id Orden Produccion')
                    ->setCellValue('F1', 'Fecha Inicio')
                    ->setCellValue('G1', 'Fecha Vencimiento')
                    ->setCellValue('H1', 'Forma Pago')
                    ->setCellValue('I1', 'Plazo Pago')
                    ->setCellValue('J1', '% Iva')
                    ->setCellValue('K1', '% ReteFuente')
                    ->setCellValue('L1', '% ReteIva')
                    ->setCellValue('M1', 'Iva')
                    ->setCellValue('N1', 'ReteFuente')
                    ->setCellValue('O1', 'ReteIva')
                    ->setCellValue('P1', 'Subtotal')                      
                    ->setCellValue('Q1', 'Total')
                    ->setCellValue('R1', 'Saldo')
                    ->setCellValue('S1', 'Autorizado')
                    ->setCellValue('T1', 'Estado')
                    ->setCellValue('U1', 'Observacion')
                    ->setCellValue('v1', 'Servicio');
        $i = 2;
        
        foreach ($tableexcel as $val) {
               $tipoOrdenProduccion = '';                    
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->idfactura)
                    ->setCellValue('B' . $i, $val->nrofactura)
                    ->setCellValue('C' . $i, $val->nrofacturaelectronica)
                    ->setCellValue('D' . $i, $val->cliente->nombreClientes)
                    ->setCellValue('F' . $i, $val->idordenproduccion)
                    ->setCellValue('F' . $i, $val->fecha_inicio)
                    ->setCellValue('G' . $i, $val->fecha_vencimiento)
                    ->setCellValue('H' . $i, $val->formaPago->concepto)
                    ->setCellValue('I' . $i, $val->plazopago)
                    ->setCellValue('J' . $i, $val->porcentajeiva)
                    ->setCellValue('K' . $i, $val->porcentajefuente)
                    ->setCellValue('L' . $i, $val->porcentajereteiva)
                    ->setCellValue('M' . $i, round($val->impuestoiva,0))
                    ->setCellValue('N' . $i, round($val->retencionfuente,0))
                    ->setCellValue('O' . $i, round($val->retencioniva,0))
                    ->setCellValue('P' . $i, round($val->subtotal,0))                    
                    ->setCellValue('Q' . $i, round($val->totalpagar,0))
                    ->setCellValue('R' . $i, round($val->saldo,0))
                    ->setCellValue('S' . $i, $val->autorizar)
                    ->setCellValue('T' . $i, $val->estados)
                    ->setCellValue('U' . $i, $val->observacion);
                    
                    if (!is_null($val->ordenproduccion) && !is_null($val->ordenproduccion->tipo)) {
                        $tipoOrdenProduccion = $val->ordenproduccion->tipo->tipo;
                    }  
                    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('V' . $i, $tipoOrdenProduccion);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('facturas_de_venta');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="facturas_de_venta.xlsx"');
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
