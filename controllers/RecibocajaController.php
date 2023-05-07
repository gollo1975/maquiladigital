<?php

namespace app\controllers;
//Models
use app\models\Recibocaja;
use app\models\RecibocajaSearch;
use app\models\Recibocajadetalle;
use app\models\Facturaventa;
use app\models\Consecutivo;
use app\models\Cliente;
use app\models\Municipio;
use app\models\TipoRecibo;
use app\models\UsuarioDetalle;
use app\models\Banco;
use app\models\FormRecibocajalibre;
use app\models\FormRecibocajanuevodetallelibre;
use app\models\FormFiltroConsultaRecibocaja;

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


/**
 * RecibocajaController implements the CRUD actions for Recibocaja model.
 */
class RecibocajaController extends Controller
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
     * Lists all Recibocaja models.
     * @return mixed
     */
  public function actionIndex($token = 0) {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',23])->all()){
            $form = new FormFiltroConsultaRecibocaja();
            $idcliente = null;
            $desde = null;
            $hasta = null;
            $numero = null;
            $tipo = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $numero = Html::encode($form->numero);
                    $tipo = Html::encode($form->tipo);
                    $table = Recibocaja::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['>=', 'fechapago', $desde])
                            ->andFilterWhere(['<=', 'fechapago', $hasta])
                            ->andFilterWhere(['=', 'numero', $numero])
                            ->andFilterWhere(['=', 'idtiporecibo', $tipo]);
                    $table = $table->orderBy('idrecibo desc');
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
                        //$table = $table->all();
                        $this->actionExcelconsulta($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = Recibocaja::find()
                        ->orderBy('idrecibo desc');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 20,
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
    /**
     * Displays a single Recibocaja model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token)
    {
        $modeldetalles = Recibocajadetalle::find()->Where(['=', 'idrecibo', $id])->all();
        $modeldetalle = new Recibocajadetalle();
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
     * Creates a new Recibocaja model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */        
    public function actionCreate()
    {
        $model = new Recibocaja();
        $clientes = Cliente::find()->orderBy('nombrecorto ASC')->all();
        $municipios = Municipio::find()->all();
        $bancos = Banco::find()->all();
        $tipoRecibos = TipoRecibo::find()->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->valorletras = "-";
            $model->usuariosistema = Yii::$app->user->identity->username;
            $model->update();
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreclientes"),
            'tiporecibos' => ArrayHelper::map($tipoRecibos, "idtiporecibo", "concepto"),
            'municipios' => ArrayHelper::map($municipios, "idmunicipio", "municipioCompleto"),
            'bancos' => ArrayHelper::map($bancos, "idbanco", "entidad"),
        ]);
    }
    
    public function actionCreatelibre()
    {
        $model = new FormRecibocajalibre;
        $clientes = Cliente::find()->orderBy('nombrecorto ASC')->all();
        $municipios = Municipio::find()->all();
        $bancos = Banco::find()->all();
        $tipoRecibos = TipoRecibo::find()->all();
        if ($model->load(Yii::$app->request->post())) {
            $table = new Recibocaja;
            $table->idcliente = $model->idcliente;
            $table->idbanco = $model->idbanco;            
            $table->observacion = $model->observacion;
            $table->fechapago = $model->fechapago;
            $table->idtiporecibo = $model->idtiporecibo;
            $table->idmunicipio = $model->idmunicipio;
            $table->valorletras = "-";
            $table->usuariosistema = Yii::$app->user->identity->username;
            $table->libre = 1;
            $table->insert();
            return $this->redirect(['index']);
        }

        return $this->render('_formlibre', [
            'model' => $model,
            'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreclientes"),
            'tiporecibos' => ArrayHelper::map($tipoRecibos, "idtiporecibo", "concepto"),
            'municipios' => ArrayHelper::map($municipios, "idmunicipio", "municipioCompleto"),
            'bancos' => ArrayHelper::map($bancos, "idbanco", "entidad"),
        ]);
    }

    /**
     * Updates an existing Recibocaja model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $clientes = Cliente::find()->all();
        $municipios = Municipio::find()->all();
        $bancos = Banco::find()->all();
        $tipoRecibos = TipoRecibo::find()->all();
        if($model->libre == 1){
                return $this->redirect(['updatelibre', 'id' => $id]);
            }
        if(Recibocajadetalle::find()->where(['=', 'idrecibo', $id])->all() or $model->estado <> 0){
           Yii::$app->getSession()->setFlash('warning', 'No se puede modificar la información, tiene detalles asociados');
        }
        else             
            if($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreclientes"),
            'tiporecibos' => ArrayHelper::map($tipoRecibos, "idtiporecibo", "concepto"),
            'municipios' => ArrayHelper::map($municipios, "idmunicipio", "municipioCompleto"),
            'bancos' => ArrayHelper::map($bancos, "idbanco", "entidad"),
        ]);
    }
    
    public function actionUpdatelibre($id) {
        $model = new FormRecibocajalibre;
        $clientes = Cliente::find()->all();
        $municipios = Municipio::find()->all();
        $bancos = Banco::find()->all();
        $tipoRecibos = TipoRecibo::find()->all();
        $table = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if(Recibocajadetalle::find()->where(['=', 'idrecibo', $id])->all() or $table->estado <> 0){
           Yii::$app->getSession()->setFlash('warning', 'No se puede modificar la información, tiene detalles asociados');
           //return $this->redirect(['updatelibre', 'id' => $id]);
        }
        else{
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                
                if ($table) {
                    $table->idcliente = $model->idcliente;
                    $table->idbanco = $model->idbanco;                    
                    $table->observacion = $model->observacion;
                    $table->fechapago = $model->fechapago;
                    $table->idtiporecibo = $model->idtiporecibo;
                    $table->idmunicipio = $model->idmunicipio;
                    if ($table->update()) {
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
                $model->idbanco = $table->idbanco;
                $model->fechapago = $table->fechapago;                
                $model->observacion = $table->observacion;
                $model->idmunicipio = $table->idmunicipio;
                $model->idtiporecibo = $table->idtiporecibo;
            } else {
                return $this->redirect(["index"]);
            }
        } else {
            return $this->redirect(["index"]);
        }
        return $this->render('_formlibre', [
            'model' => $model,
            'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreclientes"),
            'tiporecibos' => ArrayHelper::map($tipoRecibos, "idtiporecibo", "concepto"),
            'municipios' => ArrayHelper::map($municipios, "idmunicipio", "municipioCompleto"),
            'bancos' => ArrayHelper::map($bancos, "idbanco", "entidad"),
        ]);
    }

    /**
     * Deletes an existing Recibocaja model.
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
            $this->redirect(["recibocaja/index"]);
        } catch (IntegrityException $e) {
            $this->redirect(["recibocaja/index"]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el recibo de caja, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el recibo de caja, tiene registros asociados en otros procesos');
            $this->redirect(["recibocaja/index"]);
        }
    }

    /**
     * Finds the Recibocaja model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Recibocaja the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionNuevodetalles($idcliente,$idrecibo, $token)
    {
            $reciboFactura = Facturaventa::find()
            ->where(['=', 'idcliente', $idcliente])
            ->andWhere(['=', 'autorizado', 1])->andWhere(['<>', 'nrofactura', 0])
            ->andWhere(['>', 'saldo', 0])->orderBy('idfactura DESC')
            ->all();
        $mensaje = "";
        if(Yii::$app->request->post()) {
            if (isset($_POST["idfactura"])) {
                $intIndice = 0;
               
                foreach ($_POST["idfactura"] as $intCodigo) {
                    $table = new Recibocajadetalle();
                    $factura = Facturaventa::find()->where(['idfactura' => $intCodigo])->one();
                    $detalles = Recibocajadetalle::find()
                        ->where(['=', 'idfactura', $factura->idfactura])
                        ->andWhere(['=', 'idrecibo', $idrecibo])
                        ->all();
                    $reg = count($detalles);
                    if ($reg == 0) {
                        $table->idfactura = $factura->idfactura;
                        $table->nrofacturaelectronica = $factura->nrofacturaelectronica;
                        $table->vlrabono = $factura->saldo;
                        $table->vlrsaldo = $factura->saldo;
                        $table->retefuente = $factura->retencionfuente;
                        $table->reteiva = $factura->retencioniva;
                        $table->idrecibo = $idrecibo;
                        $table->insert();
                        $recibo = Recibocaja::findOne($idrecibo);
                    }
                }
                $this->redirect(["recibocaja/view", 'id' => $idrecibo, 'token' => $token]);
            } else {
                $mensaje = "Debe seleccionar al menos un registro";
            }
           
        }
        return $this->render('_formnuevodetalles', [
            'reciboFactura' => $reciboFactura,
            'idrecibo' => $idrecibo,
            'token' => $token,
            'mensaje' => $mensaje,

        ]);
    }
    
    public function actionNuevodetallelibre($id, $token) {
        $model = new FormRecibocajanuevodetallelibre;        
        if ($model->load(Yii::$app->request->post())) {
            $table = new Recibocajadetalle();
            $table->vlrabono = $model->vlrabono;
            $table->idrecibo = $id;
            $table->vlrsaldo = 0;
            $table->retefuente = 0;
            $table->reteiva = 0;
            $table->reteica = 0;
            $table->save(false);
            return $this->redirect(['view','id' => $id, 'token' => $token]);
        }
        return $this->renderAjax('_formnuevodetallelibre', [
            'model' => $model,       
            'token' => $token,
        ]);        
    }

    public function actionEditardetalle($token)
    {
        var_dump($iddetallerecibo = Html::encode($_POST["iddetallerecibo"]));
        $idrecibo = Html::encode($_POST["idrecibo"]);
        if(Yii::$app->request->post()){
            if((int) $iddetallerecibo)
            {
                $table = Recibocajadetalle::findOne($iddetallerecibo);
                if ($table) {
                    $table->vlrabono = Html::encode($_POST["vlrabono"]);
                    $table->idrecibo = Html::encode($_POST["idrecibo"]);
                    $table->save(false);
                   $this->redirect(["recibocaja/view",'id' => $idrecibo, 'token' => $token]);

                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }
            }
        }
    }

    public function actionEditardetalles($idrecibo, $token)
    {
        $mds = Recibocajadetalle::find()->where(['=', 'idrecibo', $idrecibo])->all();

        if (isset($_POST["iddetallerecibo"])) {
            $intIndice = 0;
            foreach ($_POST["iddetallerecibo"] as $intCodigo) {
                if($_POST["vlrabono"][$intIndice] > 0 ){
                    $table = Recibocajadetalle::findOne($intCodigo);
                    $total = $table->vlrabono;
                    $table->vlrabono = $_POST["vlrabono"][$intIndice];
                    $table->update();

                }
                $intIndice++;
            }
            $this->redirect(["recibocaja/view",'id' => $idrecibo, 'token' => $token]);
        }
        return $this->render('_formeditardetalles', [
            'mds' => $mds,
            'idrecibo' => $idrecibo,
            'token' => $token,
        ]);
    }

    public function actionEliminardetalle($token)
    {
        if(Yii::$app->request->post())
        {
            $iddetallerecibo = Html::encode($_POST["iddetallerecibo"]);
            $idrecibo = Html::encode($_POST["idrecibo"]);
            if((int) $iddetallerecibo)
            {
                $reciboDetalle = Recibocajadetalle::findOne($iddetallerecibo);
                $total = $reciboDetalle->vlrabono;
                if(Recibocajadetalle::deleteAll("iddetallerecibo=:iddetallerecibo", [":iddetallerecibo" => $iddetallerecibo]))
                {                   
                    $this->redirect(["recibocaja/view",'id' => $idrecibo, 'token' => $token]);
                }
                else
                {
                    echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("recibocaja/index")."'>";
                }
            }
            else
            {
                echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("recibocaja/index")."'>";
            }
        }
        else
        {
            return $this->redirect(["recibocaja/index"]);
        }
    }

    public function actionEliminardetalles($idrecibo, $token)
    {
        $mds = Recibocajadetalle::find()->where(['=', 'idrecibo', $idrecibo])->all();
        $mensaje = "";
        if(Yii::$app->request->post())
        {
            $intIndice = 0;

            if (isset($_POST["seleccion"])) {
                foreach ($_POST["seleccion"] as $intCodigo)
                {
                    $reciboDetalle = Recibocajadetalle::findOne($intCodigo);
                    $total = $reciboDetalle->vlrabono;
                    if(Recibocajadetalle::deleteAll("iddetallerecibo=:iddetallerecibo", [":iddetallerecibo" => $intCodigo]))
                    {
                        /*$recibo = Recibocaja::findOne($idrecibo);
                        $recibo->valorpagado = $recibo->valorpagado - $total;
                        $recibo->update();*/
                    }
                }
                $this->redirect(["recibocaja/view",'id' => $idrecibo, 'token' => $token]);
            }else {
                $mensaje = "Debe seleccionar al menos un registro";
            }
        }
        return $this->render('_formeliminardetalles', [
            'mds' => $mds,
            'idrecibo' => $idrecibo,
            'mensaje' => $mensaje,
            'token' => $token,
        ]);
    }

    public function actionAutorizado($id, $token)
    {
        $model = $this->findModel($id);
        $mensaje = "";
        if ($model->autorizado == 0){
            $detalles = Recibocajadetalle::find()
                ->where(['=', 'idrecibo', $id])
                ->all();
            $reg = count($detalles);
            if ($reg <> 0) {
                $error = 0;
                if($model->libre == 1){
                    $error = 0;
                }               
                if ($error == 0){
                    $model->autorizado = 1;
                    $model->save(false);
                    $this->redirect(["recibocaja/view",'id' => $id, 'token' => $token]);
                }else{
                    Yii::$app->getSession()->setFlash('error', 'Los abonos no pueden ser mayores a los saldos.');
                    $this->redirect(["recibocaja/view",'id' => $id, 'token' => $token]);
                }
            }else{
                Yii::$app->getSession()->setFlash('error', 'Para autorizar el registro, debe tener facturas relacionados en el recibo de caja.');
                $this->redirect(["recibocaja/view",'id' => $id, 'token' => $token]);
            }
        } else {
            if ($model->valorpagado <> 0) {
                Yii::$app->getSession()->setFlash('error', 'No se puede desautorizar el registro, ya fue pagado.');
                $this->redirect(["recibocaja/view",'id' => $id, 'token' => $token]);
            } else {
                $model->autorizado = 0;
                $model->save(false);
                $this->redirect(["recibocaja/view",'id' => $id, 'token' => $token]);
            }
        }
    }

    public function actionPagar($id, $token)
    {
        $model = $this->findModel($id);
        $mensaje = "";
        if ($model->autorizado == 1){
            if ($model->valorpagado == 0){
                $recibodetalles = Recibocajadetalle::find()
                    ->where(['idrecibo' => $id])
                    ->all();
                $total = 0;
                $error = 0;
                if ($model->libre == 0){
                }else{
                    $error = 0;
                }                
                if ($error == 0){
                    if ($model->libre == 0){
                        foreach ($recibodetalles as $val) {
                            $recibodetalle = Recibocajadetalle::findOne($val->iddetallerecibo);
                            $recibodetalle->vlrsaldo = ($recibodetalle->vlrsaldo) - ($recibodetalle->vlrabono);
                            $total = $total + $val->vlrabono;
                            $recibodetalle->update();
                            $factura = Facturaventa::findOne($val->idfactura);
                            $factura->saldo = $recibodetalle->vlrsaldo;
                            if($factura->saldo <= 0){
                                $factura->estado = 2; //estado 0 = abieto, estado 1 = abono, estado 2 = pagada, estado 3 = anulada por notacredito (saldo 0 en la factura), estado 4 = descuento por nota credito
                            }elseif ($factura->saldo >= 0){
                                $factura->estado = 1; //estado 0 = abieto, estado 1 = abono, estado 2 = pagada, estado 3 = anulada por notacredito (saldo 0 en la factura), estado 4 = descuento por nota credito
                            }
                            $factura->save(false);
                        }
                        $model->valorpagado = $total;
                        //$model->fechapago = date('Y-m-d');
                        //generar consecutivo numero de la nota credito
                        $consecutivo = Consecutivo::findOne(3);//2 recibo de caja
                        $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
                        $model->numero = $consecutivo->consecutivo;
                        $model->save(false);
                        $consecutivo->update();
                        //fin generar consecutivo
                        $this->redirect(["recibocaja/view",'id' => $id, 'token' => $token]);
                    }else{
                        foreach ($recibodetalles as $val) {                            
                            $total = $total + $val->vlrabono;                            
                        }
                        $model->valorpagado = $total;                        
                        $consecutivo = Consecutivo::findOne(3);//2 recibo de caja
                        $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
                        $model->numero = $consecutivo->consecutivo;
                        $model->save(false);
                        $consecutivo->update();
                        //fin generar consecutivo
                        $this->redirect(["recibocaja/view",'id' => $id, 'token' => $token]);
                    }
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Los abonos no pueden ser mayores a los saldos.');
                    $this->redirect(["recibocaja/view",'id' => $id, 'token' => $token]);
                }
            }else{
                Yii::$app->getSession()->setFlash('error', 'Ya se realizo el pago del recibo de caja.');
                $this->redirect(["recibocaja/view",'id' => $id, 'token' => $token]);
            }
        } else {
            Yii::$app->getSession()->setFlash('error', 'Para pagar el recibo de caja debe estar autorizado');
            $this->redirect(["recibocaja/view",'id' => $id, 'token' => $token]);
        }
    }

    protected function findModel($id)
    {
        if (($model = Recibocaja::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionImprimir($id)
    {                                
        return $this->render('../formatos/reciboCaja', [
            'model' => $this->findModel($id),
            
        ]);
    }
    
    public function actionIndexconsulta() {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',44])->all()){
            $form = new FormFiltroConsultaRecibocaja();
            $idcliente = null;
            $desde = null;
            $hasta = null;
            $numero = null;
            $tipo = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $numero = Html::encode($form->numero);
                    $tipo = Html::encode($form->tipo);
                    $table = Recibocaja::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['>=', 'fechapago', $desde])
                            ->andFilterWhere(['<=', 'fechapago', $hasta])
                            ->andFilterWhere(['=', 'numero', $numero])
                            ->andFilterWhere(['=', 'idtiporecibo', $tipo]);
                    $table = $table->orderBy('idrecibo desc');
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
                        //$table = $table->all();
                        $this->actionExcelconsulta($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = Recibocaja::find()
                        ->orderBy('idrecibo desc');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 20,
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
    
    public function actionViewconsulta($id)
    {
        $modeldetalles = Recibocajadetalle::find()->Where(['=', 'idrecibo', $id])->all();
        $modeldetalle = new Recibocajadetalle();
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
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Id')
                    ->setCellValue('B1', 'N° Recibo')
                    ->setCellValue('C1', 'Cliente')
                    ->setCellValue('D1', 'Fecha Recibo')
                    ->setCellValue('E1', 'Fecha Pago')
                    ->setCellValue('F1', 'Tipo')
                    ->setCellValue('G1', 'Municipio')
                    ->setCellValue('H1', 'Vlr Pagado')
                    ->setCellValue('I1', 'Autorizado')
                    ->setCellValue('J1', 'Observacion');
        $i = 2;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->idrecibo)
                    ->setCellValue('B' . $i, $val->numero)
                    ->setCellValue('C' . $i, $val->cliente->nombreClientes)
                    ->setCellValue('D' . $i, $val->fecharecibo)
                    ->setCellValue('E' . $i, $val->fechapago)
                    ->setCellValue('F' . $i, $val->tiporecibo->concepto)
                    ->setCellValue('G' . $i, $val->municipio->municipio.' - '.$val->municipio->departamento->departamento)
                    ->setCellValue('H' . $i, round($val->valorpagado,0))
                    ->setCellValue('I' . $i, $val->autorizar)
                    ->setCellValue('J' . $i, $val->observacion);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('recibo_de_caja');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="recibo_de_caja.xlsx"');
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
