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
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $numero = Html::encode($form->numero);
                    $pendiente = Html::encode($form->pendiente);
                    $table = Facturaventa::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['>=', 'fechainicio', $desde])
                            ->andFilterWhere(['<=', 'fechainicio', $hasta])
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
        $resolucion = Resolucion::find()->where(['=', 'activo', 0])->andWhere(['=','abreviatura', 'FE'])->one();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $table = Cliente::find()->where(['=', 'idcliente', $model->idcliente])->one();
            $fecha = date( $model->fecha_inicio);
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
            $model->nrofacturaelectronica = $model->nrofacturaelectronica;
            $model->usuariosistema = Yii::$app->user->identity->username;   
            $model->consecutivo = $resolucion->consecutivo;
            $model->save(false);
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
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
        $resolucion = Resolucion::find()->where(['=', 'activo', 0])->andWhere(['=','abreviatura', 'FE'])->one();
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
            $facturalibre->nrofacturaelectronica = $model->nrofacturaelectronica;
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
            $ordenesproduccion = Ordenproduccion::find()->Where(['=', 'idordenproduccion', $table->idordenproduccion])->all();
            $ordenesproduccion = ArrayHelper::map($ordenesproduccion, "idordenproduccion", "ordenProduccion");
            if(Facturaventadetalle::find()->where(['=', 'idfactura', $id])->all() or $model->estado <> 0){
               Yii::$app->getSession()->setFlash('warning', 'No se puede modificar la información, tiene detalles asociados');
            }else {
              
                if($model->load(Yii::$app->request->post()) && $model->save(false)) {
                 return $this->redirect(['index']);
                } 
            }
        }
        

        return $this->render('update', [
            'model' => $model,
            'clientes' => ArrayHelper::map($clientes, "idcliente", "nombrecorto"),
            'ordenesproduccion' => $ordenesproduccion,
            'facturastipo' => ArrayHelper::map($facturastipo, "id_factura_venta_tipo", "concepto"),

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
        if ($model->autorizado == 1){
            $factura = Facturaventa::findOne($id);
            if ($factura->libre == 0){
                $ordenProduccion = Ordenproduccion::findOne($factura->idordenproduccion);
            }
            
            if ($factura->nrofactura == 0){
                $consecutivo = Consecutivo::findOne(1);// 1 factura de venta
                $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
                $factura->nrofactura = $consecutivo->consecutivo;
                $factura->save(false);
                $consecutivo->save(false);
                if ($factura->libre == 0){
                    $ordenProduccion->facturado = 1;
                    $ordenProduccion->save(false);
                }                
                //$this->afectarcantidadfacturada($id);//se resta o descuenta las cantidades facturadas en los productos por cliente
                return $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
            }else{
                Yii::$app->getSession()->setFlash('error', 'El registro ya fue generado.');
                return $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
            }
        }else{
            Yii::$app->getSession()->setFlash('error', 'El registro debe estar autorizado para poder imprimir la factura.');
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
    
    public function actionIndexconsulta() {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',40])->all()){
            $form = new FormFiltroConsultaFacturaventa();
            $idcliente = null;
            $desde = null;
            $hasta = null;
            $numero = null;
            $pendiente = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $numero = Html::encode($form->numero);
                    $pendiente = Html::encode($form->pendiente);
                    $table = Facturaventa::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['>=', 'fechainicio', $desde])
                            ->andFilterWhere(['<=', 'fechainicio', $hasta])
                            ->andFilterWhere(['=', 'nrofactura', $numero])
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
            ]);
        }else{
            return $this->redirect(['site/sinpermiso']);
        }
        }else{
            return $this->redirect(['site/login']);
        }
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
    
    //ENVIAR DOCUMETOS A LA DIA
    public function actionEnviar_documento_dian($id_factura, $token) {
        
        //INSTANCIAR VARIABLES
        $factura = Facturaventa::findOne($id_factura);
        $clientes = Cliente::findOne($factura->idcliente);
        $detalle = Facturaventadetalle::find()->where(['=','idfactura', $id_factura])->one();
        //variables encabezado
        $documentocliente = $clientes->cedulanit;
        $tipodocumento = $clientes->tipo->codigo_api;
        $nombrecliente = $clientes->nombrecorto;
        $direccioncliente = $clientes->direccioncliente;
        $telefono = $clientes->telefonocliente;
        $emailcliente = $clientes->email_envio_factura_dian;
        $ciudad = $clientes->municipio->municipio;
        $resolucion = $factura->resolucion->codigo_interfaz;
        $consecutivo = $factura->nrofactura;
        $formapago = $factura->formaPago->codigo_api; 
        $fechainicio = $factura->fecha_inicio;
        $observacion = $factura->observacion;
        $retefuente = "true";
        $rete_iva = "true";
        //variable detalle
        $codigoconcepto = $detalle->conceptoFactura->codigo_interfaz;
        $concepto = $detalle->conceptoFactura->concepto;
        $cantidad = $detalle->cantidad;
        $valor_unitario = $detalle->preciounitario;
        $subtotal = $detalle->total;
        
        //PROCESO D ELA API
        $curl = curl_init();
        $API_KEY = "XgSaK2H9kBgIG6wrYdRHpqX5ekEGB0iS2dc2877703daac9d27fe919ea661bac0fbqyFG3QVs454VEX9Fj1W9zYDZTrLGch"; //VARIABLE CON API KEY DE DESARROLLO O PRODUCCIÓN SEGÚN SEA EL CASO
        $dataHead = [
            "client" => [
                "document" => "$documentocliente",//NÚMERO DE DOCUMENTO DEL CLIENTE
                "document_type" => "$tipodocumento", //TIPO DE DOCUMENTO DEL CLIENTE: 0 - Registro Civil, 1 - Tarjeta Identidad, 2 - Cedula Ciudadania, 3 - Tarjeta Extranjeria, 4 - Cedula Extranjeria, 5 - Nit, 6 - Pasaporte, 7 - Documento de Extranjeria, 8 - Sin indentificación o para uso de la DIAN, 10 - Permiso Especial de Permanencia, 11 - Permiso por Protección Temporal
                "first_name" => "$nombrecliente", //NOMBRE CLIENTE
                "last_name_one" => "-", //PRIMER APELLIDO
                "last_name_two" => "N/A", //SEGUNDO APELLIDO
                "address" => "$direccioncliente", //DIRECCION
                "phone" => "$telefono",  //TELEFONO
                "email" => "$emailcliente", //EMAIL
                "city" => "$ciudad", //NOMBRE CIUDAD
                "rete_fuente" => "$retefuente", //retencion en la fuente
                "rete_iva" => "$rete_iva" //rete iva de la empresa
              
            ],
            "comment" => "$observacion", //OBSERVACIÓN PÚBLICA FACTURA QUE SE VISUALIZA EN LA REPRESENTACIÓN GRÁFICA
            "resolucion" => "$resolucion", //CÓDIGO DE LA RESOLUCIÓN QUE SE OBTIENE DESDE EL SISTEMA EN TABLAS>RESOLUCIONES
            "consecutivo" => "$consecutivo",//CONSECUTIVO QUE SE VÁ A CONSUMIR
            "forma_pago" => "$formapago",//FORMA PAGO DE FACTURA: 1 - EFECTIVO, 2 - T.DEBITO, 3 - T.CREDITO, 4 - CREDITO
            "date" => "$fechainicio" //FECHA FACTURA
          ];
        $dataHead = json_encode($dataHead);
        $dataBody = [
            [
                "product" => $codigoconcepto, //ID DE PRODUCTO
                "warehouse" => 1, //ID DE BODEGA
                "qty" => $cantidad, //CANTIDAD
                "concept" => "$concepto",//CONCEPTO DEL ITEM
                "average" => $valor_unitario, //VALOR UNITARIO
                "total" => $subtotal //TOTAL
            ]
        ];
        $dataBody = json_encode($dataBody);
          curl_setopt_array($curl, array(
          CURLOPT_URL => "http://begranda.com/equilibrium2/public/api/bill?key=$API_KEY",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => [
            "head"=>$dataHead,
            "body"=>$dataBody
          ],
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
                Yii::$app->getSession()->setFlash('success', 'La factura de venta electronica No ('. $consecutivo .') se envio con exito a la Dian.');
                $cufe = isset($data["data"]["cufe"]) ? $data["data"]["cufe"] : "";
                $factura->cufe = $cufe;
                $fechaRecepcion = isset($data["data"]["sentDetail"]["response"]["send_email_date_time"]) && !empty($data["data"]["sentDetail"]["response"]["send_email_date_time"]) ? $data["data"]["sentDetail"]["response"]["send_email_date_time"] : date("Y-m-d H:i:s");
                $factura->fecha_recepcion_dian = $fechaRecepcion;
            
                $factura->fecha_envio_begranda = date("Y-m-d H:i:s");
                $factura->save(false);
            }else{
                Yii::$app->getSession()->setFlash('error', 'Problemas de conexion en la Dian. Volver a reenviar la factura');
                $factura->reenviar_factura_dian = 1;
                $factura->save(false); 
            }
        } catch (Exception $ex) {
             Yii::$app->getSession()->setFlash('error', 'Error al enviar la factura: ' . $e->getMessage());
        }
        return $this->redirect(['facturaventa/view','id' => $id_factura, 'token' => $token]); 
      
    }
    
    
    //PERMITE REENVIAR LA FACTURA SI NO SE CONECTA A LA DIAN
    public function actionReenviar_documento_dian($id_factura, $token) {
        // Instanciar la factura desde la base de datos
        $factura = Facturaventa::findOne($id_factura);
        if (!$factura) {
            Yii::$app->getSession()->setFlash('error', 'Factura no encontrada.');
            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
        }
        //ASIGNACION DE VARIABLES
        $resolucion = $factura->resolucion->codigo_interfaz;
        $consecutivo = $factura->nrofactura;
        // URL y clave API
        $API_URL = "http://begranda.com/equilibrium2/public/api/send-electronic-invoice";
        $API_KEY = "XgSaK2H9kBgIG6wrYdRHpqX5ekEGB0iS2dc2877703daac9d27fe919ea661bac0fbqyFG3QVs454VEX9Fj1W9zYDZTrLGch"; // Reemplazar con tu clave API

        // Inicializar CURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "$API_URL?key=$API_KEY&consecutivo=$consecutivo&id_resolucion=$resolucion",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,  // Timeout extendido
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [],
        ]);

        // Ejecutar la solicitud CURL y verificar la respuesta
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Registrar la respuesta completa de la API para depuración
        Yii::info("Respuesta completa de la API desde Begranda: $response", __METHOD__);

        // Verificar errores de conexión o códigos HTTP inesperados
        if ($response === false || $httpCode !== 200) {
            $error = $response === false ? curl_error($curl) : "HTTP $httpCode";
            Yii::$app->getSession()->setFlash('error', 'Hubo un problema al comunicarse con la DIAN. Intenta reenviar más tarde.');
            Yii::error("Error en la solicitud CURL: $error", __METHOD__);
            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
        }

        // Decodificar la respuesta JSON
        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Yii::$app->getSession()->setFlash('error', 'Error al procesar la respuesta de la DIAN. Intenta reenviar más tarde.');
            Yii::error("Error al decodificar JSON: " . json_last_error_msg(), __METHOD__);
            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
        }

        // Comprobamos el 'status' de la respuesta para determinar éxito o error
        if (isset($data['status']) && $data['status'] == 'success') {
            // Si la respuesta es exitosa
            Yii::$app->getSession()->setFlash('success', "La factura de venta electrónica No ($consecutivo) se reenvió con éxito.");
            
            // Asignar CUFE y fecha de recepción solo si están disponibles en la respuesta
            $cufe = isset($data["data"]["cufe"]) ? $data["data"]["cufe"] : "";
            $factura->cufe = $cufe;
            $fechaRecepcion = isset($data["data"]["sentDetail"]["response"]["send_email_date_time"]) && !empty($data["data"]["sentDetail"]["response"]["send_email_date_time"]) ? $data["data"]["sentDetail"]["response"]["send_email_date_time"] : date("Y-m-d H:i:s");
            $factura->fecha_recepcion_dian = $fechaRecepcion;
            $factura->reenviar_factura = 0; // Marcar como no pendiente de reenvío
            $factura->save(false);
            Yii::info("Respuesta exitosa de la API: " . print_r($data, true), __METHOD__);
        } else {
            // Si el 'status' no es success o hay un mensaje de error
            $errorMessage = isset($data['message']) ? $data['message'] : 'Error desconocido';
            // Mostrar el mensaje específico de la API
            Yii::$app->getSession()->setFlash('error', "No se pudo reenviar la factura. Error: $errorMessage.");
            Yii::error("Error al reenviar factura No ($consecutivo): " . print_r($data, true), __METHOD__);
            $factura->reenviar_factura = 1; // Mantener la factura pendiente de reenvío
            $factura->save(false);
        }

        // Intentar guardar la factura en la base de datos
        if (!$factura->save(false)) {
            Yii::error("Error al guardar la factura No ($consecutivo): " . print_r($factura->errors, true), __METHOD__);
            Yii::$app->getSession()->setFlash('error', 'Hubo un error al guardar la factura.');
            return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
        }

        // Redirigir a la vista de la factura
        return $this->redirect(['facturaventa/view', 'id' => $id_factura, 'token' => $token]);
    }

    //PROCESO QUE BUSCA LA FACTURA DE LA API
    public function actionSearch_factura_dian($id_factura, $token) {
        
        //INSTANCIAR VARIABLES
        $factura = Facturaventa::findOne($id_factura);
        //variables encabezado
        $resolucion = $factura->resolucion->codigo_interfaz;
        $consecutivo = $factura->nrofactura;
             
        //PROCESO QUE BUSCA UNA FACTURA EN LA API
        $curl = curl_init();
        $API_KEY = "XgSaK2H9kBgIG6wrYdRHpqX5ekEGB0iS2dc2877703daac9d27fe919ea661bac0fbqyFG3QVs454VEX9Fj1W9zYDZTrLGch"; //VARIABLE CON API KEY DE DESARROLLO O PRODUCCIÓN SEGÚN SEA EL CASO
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
                    ->setCellValue('U1', 'Observacion');
        $i = 2;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->idfactura)
                    ->setCellValue('B' . $i, $val->nrofactura)
                    ->setCellValue('C' . $i, $val->nrofacturaelectronica)
                    ->setCellValue('D' . $i, $val->cliente->nombreClientes)
                    ->setCellValue('F' . $i, $val->idordenproduccion)
                    ->setCellValue('F' . $i, $val->fechainicio)
                    ->setCellValue('G' . $i, $val->fechavcto)
                    ->setCellValue('H' . $i, $val->formadepago)
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
