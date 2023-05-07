<?php

namespace app\controllers;

use app\models\Consecutivo;
use app\models\Ordenproducciondetalle;
use app\models\Resolucion;
use app\models\Producto;
use Codeception\Module\Cli;
use Yii;
use app\models\Facturaventa;
use app\models\FacturaventaSearch;
use app\models\Facturaventadetalle;
use app\models\FormFiltroConsultaFacturaventa;
use app\models\FormFacturaventalibre;
use app\models\FormFacturaventanuevodetallelibre;
use app\models\Productodetalle;
use app\models\Facturaventatipo;
use app\models\Matriculaempresa;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Cliente;
use app\models\Ordenproduccion;
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
        $resolucion = Resolucion::find()->where(['=', 'activo', 1])->one();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $table = Cliente::find()->where(['=', 'idcliente', $model->idcliente])->one();
            $fecha = date( $model->fechainicio);
            $nuevafecha = strtotime ( '+'.$table->plazopago.' day' , strtotime ( $fecha ) ) ;
            $nuevafecha = date ( 'Y-m-d' , $nuevafecha );

            $model->nrofactura = 0;
            $model->fechavcto = $nuevafecha;
            $model->formapago = $table->formapago;
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
            $model->update();
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
        $resolucion = Resolucion::find()->where(['=', 'activo', 1])->one();
        if ($model->load(Yii::$app->request->post())) {            
            $table = Cliente::find()->where(['=', 'idcliente', $model->idcliente])->one();
            $fecha = date( $model->fechainicio);
            $nuevafecha = strtotime ( '+'.$table->plazopago.' day' , strtotime ( $fecha ) ) ;
            $nuevafecha = date ( 'Y-m-j' , $nuevafecha );
            $facturalibre = new Facturaventa;
            $facturalibre->fechainicio = $model->fechainicio;
            $facturalibre->idcliente = $model->idcliente;
            $facturalibre->observacion = $model->observacion;
            $facturalibre->nrofactura = 0;
            $facturalibre->nrofacturaelectronica = $model->nrofacturaelectronica;
            $facturalibre->fechavcto = $nuevafecha;
            $facturalibre->formapago = $table->formapago;
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
            }
            else if($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index']);
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
                    $table->fechainicio = $model->fechainicio;                    
                    $table->observacion = $model->observacion;
                    $table->id_factura_venta_tipo = $model->id_factura_venta_tipo;
                    $table->nrofacturaelectronica = $model->nrofacturaelectronica;
                    if ($table->update(false)) {
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
                $model->fechainicio = $table->fechainicio;
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
        $facturaOrden = Ordenproducciondetalle::find()->where(['=', 'idordenproduccion', $idordenproduccion])->all();
        $mensaje = "";
        if(Yii::$app->request->post()) {
            if (isset($_POST["iddetalleorden"])) {
                $intIndice = 0;
                foreach ($_POST["iddetalleorden"] as $intCodigo) {
                    $table = new Facturaventadetalle();
                    $ordenProducciondetalle = Ordenproducciondetalle::find()->where(['iddetalleorden' => $intCodigo])->one();
                    $detalles = Facturaventadetalle::find()
                        ->where(['=', 'idfactura', $idfactura])
                        ->andWhere(['=', 'idproductodetalle', $ordenProducciondetalle->idproductodetalle])
                        ->all();
                    $reg = count($detalles);
                    if ($reg == 0) {
                        $table->idproductodetalle = $ordenProducciondetalle->idproductodetalle;
                        $table->cantidad = $ordenProducciondetalle->cantidad;
                        $table->preciounitario = $ordenProducciondetalle->vlrprecio;
                        $table->codigoproducto = $ordenProducciondetalle->codigoproducto;
                        $table->total = $ordenProducciondetalle->subtotal;
                        $table->idfactura = $idfactura;
                        $table->insert();
                        $factura = Facturaventa::findOne($idfactura);
                        $factura->subtotal = round($factura->subtotal + $table->total);
                        $config = Matriculaempresa::findOne(1);
                        $cliente = Cliente::findOne($factura->idcliente);
                        $factura->porcentajeiva = round($config->porcentajeiva);
                        $factura->porcentajereteiva = $config->porcentajereteiva;
                        $factura->impuestoiva = round($factura->subtotal * $factura->porcentajeiva / 100);
                        if ($factura->subtotal >= $config->retefuente){
                            if ($cliente->retencioniva == 1){
                                $factura->porcentajefuente = $factura->facturaventatipo->porcentaje_retefuente;
                                $factura->retencionfuente = round($factura->subtotal * $factura->porcentajefuente / 100);
                            }
                            if ($cliente->retencionfuente == 1){
                                $factura->porcentajefuente = $factura->facturaventatipo->porcentaje_retefuente;
                                $factura->retencionfuente = round($factura->subtotal * $factura->porcentajefuente / 100);
                            }
                        }else{
                            $factura->retencionfuente = 0;
                        }
                        if ($cliente->autoretenedor == 1){
                            $factura->retencioniva = round($factura->impuestoiva * $config->porcentajereteiva / 100);
                        }else{
                            $factura->retencioniva = 0;                            
                        }
                        $factura->totalpagar = round($factura->subtotal + $factura->impuestoiva - $factura->retencionfuente - $factura->retencioniva);
                        $factura->saldo = $factura->totalpagar;
                        $factura->update();
                    }
                }
                $this->redirect(["facturaventa/view", 'id' => $idfactura, 'token' => $token]);
            }else{
                $mensaje = "Debe seleccionar al menos un registro";
            }
        }

        return $this->render('_formnuevodetalles', [
            'facturaOrden' => $facturaOrden,
            'idfactura' => $idfactura,
            'mensaje' => $mensaje,
            'token' => $token,

        ]);
    }
    
    public function actionNuevodetallelibre($id, $token) {
        $model = new FormFacturaventanuevodetallelibre;
        $factura = Facturaventa::findOne($id);        
        $productos = Producto::find()->where(['=','idcliente',$factura->idcliente])->all();        
        if ($model->load(Yii::$app->request->post())) {
            $productodetalle = Productodetalle::find()->where(['=','idproducto',$model->idproducto])->all();
            foreach ($productodetalle as $val){
                $producto = Producto::findOne($model->idproducto);                
                $table = new Facturaventadetalle();
                $table->idfactura = $id;
                $table->idproductodetalle = $val->idproductodetalle;
                $table->codigoproducto = $producto->codigo;                                
                $table->cantidad = 1;
                $table->preciounitario = $model->valor;
                $table->total = $table->preciounitario * $table->cantidad;                
                $table->save(false);                
            }
            $this->calculos($id);
            return $this->redirect(['view','id' => $id, 'token' => $token]);
        }
        return $this->renderAjax('_formnuevodetallelibre', [
            'model' => $model,
            'token' => $token,
            'productos' => ArrayHelper::map($productos, "idproducto", "codigonombre"),
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

                    $factura = Facturaventa::findOne($table->idfactura);
                    $factura->subtotal = $factura->subtotal - Html::encode($_POST["total"]);
                    $factura->subtotal = $factura->subtotal + $table->total;

                    $config = Matriculaempresa::findOne(1);
                    $cliente = Cliente::findOne($factura->idcliente);
                    $factura->porcentajeiva = $config->porcentajeiva;
                    $factura->porcentajereteiva = $config->porcentajereteiva;
                    $factura->impuestoiva = $factura->subtotal * $factura->porcentajeiva / 100;
                    if ($factura->subtotal >= $config->retefuente){
                        if ($cliente->retencioniva == 1){
                            $factura->porcentajefuente = $factura->facturaventatipo->porcentaje_retefuente;
                            $factura->retencionfuente = $factura->subtotal * $factura->porcentajefuente / 100;
                        }
                        if ($cliente->retencionfuente == 1){
                            $factura->porcentajefuente = $factura->facturaventatipo->porcentaje_retefuente;
                            $factura->retencionfuente = round($factura->subtotal * $factura->porcentajefuente / 100);
                        }
                    }else{
                        $factura->retencionfuente = 0;
                    }
                    if ($cliente->autoretenedor == 1){
                        $factura->retencioniva = $factura->impuestoiva * $config->porcentajereteiva / 100;
                    }else{
                        $factura->retencioniva = 0;
                    }
                    $factura->totalpagar = round($factura->subtotal + $factura->impuestoiva - $factura->retencionfuente - $factura->retencioniva,0);
                    $factura->saldo = $factura->totalpagar;
                    $factura->save(false);

                    $this->redirect(["facturaventa/view",'id' => $idfactura, 'token' => $token]);

                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }
            }
        }
    }

    public function actionEditardetalles($idfactura, $token)
    {
        $mds = Facturaventadetalle::find()->where(['=', 'idfactura', $idfactura])->all();

        if (isset($_POST["iddetallefactura"])) {
            $intIndice = 0;
            foreach ($_POST["iddetallefactura"] as $intCodigo) {
                if($_POST["cantidad"][$intIndice] > 0 ){
                    $table = Facturaventadetalle::findOne($intCodigo);
                    $total = $table->total;
                    $table->cantidad = $_POST["cantidad"][$intIndice];
                    $table->preciounitario = $_POST["preciounitario"][$intIndice];
                    $table->total = $_POST["cantidad"][$intIndice] * $_POST["preciounitario"][$intIndice];
                    $table->update();
                    $factura = Facturaventa::findOne($idfactura);
                    $factura->subtotal = $factura->subtotal - $total;
                    $factura->subtotal = $factura->subtotal + $table->total;

                    $config = Matriculaempresa::findOne(1);
                    $cliente = Cliente::findOne($factura->idcliente);
                    $factura->porcentajeiva = $config->porcentajeiva;
                    $factura->porcentajereteiva = $config->porcentajereteiva;
                    $factura->impuestoiva = $factura->subtotal * $factura->porcentajeiva / 100;
                    if ($factura->subtotal >= $config->retefuente){
                        if ($cliente->retencioniva == 1){
                            $factura->porcentajefuente = $factura->facturaventatipo->porcentaje_retefuente;
                            $factura->retencionfuente = $factura->subtotal * $factura->porcentajefuente / 100;
                        }
                        if ($cliente->retencionfuente == 1){
                            $factura->porcentajefuente = $factura->facturaventatipo->porcentaje_retefuente;
                            $factura->retencionfuente = round($factura->subtotal * $factura->porcentajefuente / 100);
                        }
                    }else{
                        $factura->retencionfuente = 0;
                    }
                    if ($cliente->autoretenedor == 1){
                        $factura->retencioniva = $factura->impuestoiva * $factura->facturaventatipo->porcentaje_retefuente / 100;
                    }else{
                        $factura->retencioniva = 0;
                    }
                    $factura->totalpagar = round($factura->subtotal + $factura->impuestoiva - $factura->retencionfuente - $factura->retencioniva,0);
                    $factura->saldo = $factura->totalpagar;
                    $factura->update();
                }
                $intIndice++;
            }
            $this->redirect(["facturaventa/view",'id' => $idfactura, 'token' => $token]);
        }
        return $this->render('_formeditardetalles', [
            'mds' => $mds,
            'idfactura' => $idfactura,
            'token' => $token,
        ]);
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
                    $factura->subtotal = $factura->subtotal - $total;

                    $config = Matriculaempresa::findOne(1);
                    $cliente = Cliente::findOne($factura->idcliente);
                    $factura->porcentajeiva = $config->porcentajeiva;
                    $factura->porcentajereteiva = $config->porcentajereteiva;
                    $factura->impuestoiva = $factura->subtotal * $factura->porcentajeiva / 100;
                    //calculo de retefuente, reteiva
                    if ($factura->subtotal >= $config->retefuente){
                        if ($cliente->retencioniva == 1){
                            $factura->porcentajefuente = $factura->facturaventatipo->porcentaje_retefuente;
                            $factura->retencionfuente = $factura->subtotal * $factura->porcentajefuente / 100;
                        }
                        if ($cliente->retencionfuente == 1){
                            $factura->porcentajefuente = $factura->facturaventatipo->porcentaje_retefuente;
                            $factura->retencionfuente = round($factura->subtotal * $factura->porcentajefuente / 100);
                        }
                    }else{
                        $factura->retencionfuente = 0;
                    }
                    if ($cliente->autoretenedor == 1){
                        $factura->retencioniva = $factura->impuestoiva * $config->porcentajereteiva / 100;
                    }else{
                        $factura->retencioniva = 0;
                    }
                    $factura->totalpagar = round($factura->subtotal + $factura->impuestoiva - $factura->retencionfuente - $factura->retencioniva,0);
                    $factura->saldo = $factura->totalpagar;
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

    public function actionEliminardetalles($idfactura, $token)
    {
        $mds = Facturaventadetalle::find()->where(['=', 'idfactura', $idfactura])->all();
        $mensaje = "";
        if(Yii::$app->request->post())
        {
            $intIndice = 0;

            if (isset($_POST["seleccion"])) {
                foreach ($_POST["seleccion"] as $intCodigo)
                {
                    $facturaDetalle = Facturaventadetalle::findOne($intCodigo);
                    $total = $facturaDetalle->total;
                    if(Facturaventadetalle::deleteAll("iddetallefactura=:iddetallefactura", [":iddetallefactura" => $intCodigo]))
                    {
                        $factura = Facturaventa::findOne($idfactura);
                        $factura->subtotal = $factura->subtotal - $total;

                        $config = Matriculaempresa::findOne(1);
                        $cliente = Cliente::findOne($factura->idcliente);
                        $factura->porcentajeiva = $config->porcentajeiva;
                        $factura->porcentajereteiva = $config->porcentajereteiva;
                        $factura->impuestoiva = $factura->subtotal * $factura->porcentajeiva / 100;
                        //calculo reteiva,retefuente
                        if ($factura->subtotal >= $config->retefuente){
                            if ($cliente->retencioniva == 1){
                                $factura->porcentajefuente = $factura->facturaventatipo->porcentaje_retefuente;
                                $factura->retencionfuente = $factura->subtotal * $factura->porcentajefuente / 100;
                            }
                            if ($cliente->retencionfuente == 1){
                                $factura->porcentajefuente = $factura->facturaventatipo->porcentaje_retefuente;
                                $factura->retencionfuente = round($factura->subtotal * $factura->porcentajefuente / 100);
                            }
                        }else{
                            $factura->retencionfuente = 0;
                        }
                        if ($cliente->autoretenedor == 1){
                            $factura->retencioniva = $factura->impuestoiva * $config->porcentajereteiva / 100;
                        }else{
                            $factura->retencioniva = 0;                           
                        }
                        $factura->totalpagar = round($factura->subtotal + $factura->impuestoiva - $factura->retencionfuente - $factura->retencioniva,0);
                        $factura->saldo = $factura->totalpagar;
                        $factura->update();
                    }
                }
                $this->redirect(["facturaventa/view",'id' => $idfactura, 'token' => $token]);
            }else {
                $mensaje = "Debe seleccionar al menos un registro";
            }
        }
        return $this->render('_formeliminardetalles', [
            'mds' => $mds,
            'idfactura' => $idfactura,
            'mensaje' => $mensaje,
            'token' => $token,
        ]);
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
                $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
            }else{
                Yii::$app->getSession()->setFlash('error', 'Para autorizar el registro, debe tener ordenes relacionados en la factura de venta.');
                $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
            }
        } else {
            $factura = Facturaventa::findOne($id);
            if ($factura->nrofactura == 0){
                $model->autorizado = 0;
                $model->save(false);
                $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
            }else {
                Yii::$app->getSession()->setFlash('error', 'No se puede desautorizar el registro, ya fue generado el número de factura.');
                $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
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
                $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
            }else{
                Yii::$app->getSession()->setFlash('error', 'El registro ya fue generado.');
                $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
            }
        }else{
            Yii::$app->getSession()->setFlash('error', 'El registro debe estar autorizado para poder imprimir la factura.');
            $this->redirect(["facturaventa/view",'id' => $id, 'token' => $token]);
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
    
    protected function afectarcantidadfacturada($id)
    {        
        $detalles = Facturaventadetalle::find()->where(['idfactura' => $id])->all();
        foreach ($detalles as $dato) {
            $producto = Producto::findOne($dato->idproducto);            
            $producto->stock = $producto->stock - $dato->cantidad;
            $producto->update();
        }
    }
    
    protected function calculos($id)
    {        
        $detalles = Facturaventadetalle::find()->where(['idfactura' => $id])->all();
        $subtotal = 0;
        foreach ($detalles as $val) {
            $subtotal = $subtotal + $val->total;
        }
        $factura = Facturaventa::findOne($id);
        $factura->subtotal = round($subtotal);
        $config = Matriculaempresa::findOne(1);
        $cliente = Cliente::findOne($factura->idcliente);
        $factura->porcentajeiva = round($config->porcentajeiva);
        $factura->porcentajereteiva = $config->porcentajereteiva;
        $factura->impuestoiva = round($factura->subtotal * $factura->porcentajeiva / 100);
        if ($factura->subtotal >= $config->retefuente){
            if ($cliente->retencioniva == 1){
                $factura->porcentajefuente = $factura->facturaventatipo->porcentaje_retefuente;
                $factura->retencionfuente = round($factura->subtotal * $factura->porcentajefuente / 100);
            }
            if ($cliente->retencionfuente == 1){
                $factura->porcentajefuente = $factura->facturaventatipo->porcentaje_retefuente;
                $factura->retencionfuente = round($factura->subtotal * $factura->porcentajefuente / 100);
            }
        }else{
            $factura->retencionfuente = 0;
        }
        if ($cliente->autoretenedor == 1){
            $factura->retencioniva = round($factura->impuestoiva * $config->porcentajereteiva / 100);
        }else{
            if ($cliente->retencionfuente == 1){
                $factura->retencioniva = round($factura->impuestoiva * $config->porcentajereteiva / 100);
            }else {
                $factura->retencioniva = 0;
            }

        }
        $factura->totalpagar = round($factura->subtotal + $factura->impuestoiva - $factura->retencionfuente - $factura->retencioniva);
        $factura->saldo = $factura->totalpagar;
        $factura->save(false);
        
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
