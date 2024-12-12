<?php

namespace app\controllers;

use app\models\Consecutivo;
use app\models\Conceptonota;
use app\models\Facturaventa;
use app\models\Notacreditodetalle;
use app\models\Cliente;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use Yii;
use app\models\Notacredito;
use app\models\NotacreditoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use app\models\UsuarioDetalle;

/**
 * NotacreditoController implements the CRUD actions for Notacredito model.
 */
class NotacreditoController extends Controller
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
     * Lists all Notacredito models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',27])->all()){
                $searchModel = new NotacreditoSearch();
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

    /**
     * Displays a single Notacredito model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $modeldetalles = Notacreditodetalle::find()->Where(['=', 'idnotacredito', $id])->all();
        $modeldetalle = new Notacreditodetalle();
        $mensaje = "";
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modeldetalle' => $modeldetalle,
            'modeldetalles' => $modeldetalles,
            'mensaje' => $mensaje,

        ]);
    }

    /**
     * Creates a new Notacredito model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Notacredito();
        $clientes = Cliente::find()->all();
        $conceptonotacredito = Conceptonota::find()->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $model->usuariosistema = Yii::$app->user->identity->username;
            $model->update();
            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
            'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreClientes"),
            'conceptonotacredito' => ArrayHelper::map($conceptonotacredito, "idconceptonota", "concepto"),

        ]);
    }

    /**
     * Updates an existing Notacredito model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $clientes = Cliente::find()->all();
        $conceptonotacredito = Conceptonota::find()->all();
        if(Notacreditodetalle::find()->where(['=', 'idnotacredito', $id])->all()){
           Yii::$app->getSession()->setFlash('warning', 'No se puede modificar la información, tiene detalles asociados');
        }
        else if($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreClientes"),
            'conceptonotacredito' => ArrayHelper::map($conceptonotacredito, "idconceptonota", "concepto"),
        ]);
    }

    /**
     * Deletes an existing Notacredito model.
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
            $this->redirect(["notacredito/index"]);
        } catch (IntegrityException $e) {
            $this->redirect(["notacredito/index"]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar la nota crédito, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar la nota crédito, tiene registros asociados en otros procesos');
            $this->redirect(["notacredito/index"]);
        }
    }

    public function actionNuevodetalles($idcliente,$idnotacredito)
    {

        $listado = Facturaventa::find()
            ->where(['=', 'idcliente', $idcliente])
            ->andWhere(['=', 'autorizado', 1])->andWhere(['<>', 'nrofactura', 0])
            ->andWhere(['>', 'saldo', 0])
             ->orderBy('idfactura DESC')->all();
        $form = new \app\models\FormMaquinaBuscar();
        $q = null;
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                if ($q){
                    $listado = Facturaventa::find()
                            ->where(['=','nrofactura', $q])
                            ->andwhere(['>','saldo', 0])
                            ->orderBy('idfactura DESC')->all();
                }               
            } else {
                $form->getErrors();
            }                    

        } else {
            $listado = Facturaventa::find()
                ->where(['=', 'idcliente', $idcliente])
                ->andWhere(['=', 'autorizado', 1])->andWhere(['<>', 'nrofactura', 0])
                ->andWhere(['>', 'saldo', 0])
                ->orderBy('idfactura DESC')->all();
        }
        if(Yii::$app->request->post()) {
            if (isset($_POST["idfactura"])) {
                $intIndice = 0;
                foreach ($_POST["idfactura"] as $intCodigo) {
                    
                    $factura = Facturaventa::find()->where(['idfactura' => $intCodigo])->one();
                    $detalle_factura = \app\models\Facturaventadetalle::find()->where(['=','idfactura', $factura->idfactura])->one();
                    $detalles = Notacreditodetalle::find()
                        ->where(['=', 'idfactura', $factura->idfactura])
                        ->andWhere(['=', 'idnotacredito', $idnotacredito])
                        ->all();
                    $reg = count($detalles);
                    if ($reg == 0) {
                        $notaCredito = Notacredito::findOne($idnotacredito);
                        $Motivo = \app\models\ConceptoNotaCreditoDevolucion::find()->where(['=','id_concepto', $notaCredito->id_concepto])
                                                                                   ->andWhere(['=','codigo_interno', 3])->one();
                        $table = new Notacreditodetalle();
                        $table->idfactura = $factura->idfactura;
                        $table->nrofactura = $factura->nrofactura;
                        $table->saldo_factura = round($factura->saldo);
                        $table->idnotacredito = $idnotacredito;
                       
                        if ($Motivo){
                            $table->cantidad = $detalle_factura->cantidad;
                            $table->precio_unitario = $detalle_factura->preciounitario;
                            $table->porcentaje_iva = $detalle_factura->porcentaje_iva;
                            $table->porcentaje_retefuente =  $detalle_factura->porcentaje_retefuente;
                            $table->id = $detalle_factura->id;
                            $table->usuariosistema = Yii::$app->user->identity->username;
                            $table->save(false);
                            $notaCredito->fecha_factura_venta = $factura->fecha_inicio;
                            $notaCredito->cufe = $factura->cufe;
                            $notaCredito->save();
                            $detalle_nota = Notacreditodetalle::find()->orderBy('iddetallenota DESC')->one(); 
                            $id_detalle =  $detalle_nota->iddetallenota;
                            $id = $idnotacredito;
                            $this->TotalizarImpuesto($id_detalle, $id);
                        }else{
                            $table->precio_unitario = $detalle_factura->preciounitario;
                            $table->porcentaje_iva = $detalle_factura->porcentaje_iva;
                            $table->porcentaje_retefuente =  $detalle_factura->porcentaje_retefuente;
                            $table->id = $detalle_factura->id;
                            $table->usuariosistema = Yii::$app->user->identity->username;
                            $table->save(false);
                            $notaCredito->fecha_factura_venta = $factura->fecha_inicio;
                            $notaCredito->cufe = $factura->cufe;
                            $notaCredito->save();
                        }
                    }
                }
                return $this->redirect(["notacredito/view", 'id' => $idnotacredito]);
            } else {
                Yii::$app->getSession()->setFlash('warning', 'Debe de seleccionar al menos un registro.');
            }
        }
        return $this->render('_formnuevodetalles', [
            'notacreditoFactura' => $listado,
            'idnotacredito' => $idnotacredito,
            'idcliente' => $idcliente,
            'form' => $form,

        ]);
    }
  ///EDITAR LA INFORMACION DEL DETALLE DE LA NOTA CREDITO
    
    public function actionEditardetalle($id, $id_detalle)
    {
        $model = new \app\models\FormConsultaNotaCredito();
        $table = Notacreditodetalle::findOne($id_detalle);
        if ($model->load(Yii::$app->request->post())){
            if (isset($_POST["adicionar_cantidades"])){
                if($model->validate()){
                    if(Notacreditodetalle::findOne($id_detalle)){;
                        $table->cantidad = $model->nueva_cantidad;
                        $table->precio_unitario = $model->valor_unitario;
                        $table->save(false);
                        $this->TotalizarImpuesto($id_detalle, $id);
                        return $this->redirect(["view",'id' => $id]); 
                    }    
                }else{
                   $model->getErrors();
                }    
            }  
        }
        if (Yii::$app->request->get()) {
            $model->nueva_cantidad = $table->cantidad;
            $model->valor_unitario =$table->precio_unitario;
        }
        return $this->renderAjax('_form_adicionar_cantidades', [
            'model' => $model,
            'id'=> $id,
        ]);
    }   
    //TOTAL IMPUESTO EN EL DETALLE DE LA NOTA CREDITO
    protected function TotalizarImpuesto($id_detalle, $id) {
        $empresa = \app\models\Matriculaempresa::findOne(1);
        $nota = Notacredito::findOne($id);
        $detalle = Notacreditodetalle::findOne($id_detalle);
        $subtotal_nota = round($detalle->precio_unitario * $detalle->cantidad);
        $valor_retencion = round(($subtotal_nota * $detalle->porcentaje_retefuente)/100);
        $valor_iva = round(($subtotal_nota * $detalle->porcentaje_iva)/100);
        if($nota->cliente->retencioniva == 1){
            $valor_reteiva = round(($valor_iva * $empresa->porcentajereteiva)/100);
        }else{
            $valor_reteiva = 0;
        }    
        $total_nota = ($subtotal_nota + $valor_iva) - ($valor_reteiva + $valor_retencion);
        //asignar variables para guardar
        $detalle->valor_iva = $valor_iva;
        $detalle->valor_retencion = $valor_retencion;
        $detalle->valor_reteiva = $valor_reteiva;
        $detalle->valor_nota_credito = $subtotal_nota;
        $detalle->total_nota = $total_nota;
        $detalle->save();
    }
     
    public function actionEliminardetalle()
    {
        if(Yii::$app->request->post())
        {
            $iddetallenota = Html::encode($_POST["iddetallenota"]);
            $idnotacredito = Html::encode($_POST["idnotacredito"]);
            if((int) $iddetallenota) {
                $notacreditoDetalle = Notacreditodetalle::findOne($iddetallenota);
                $total = $notacreditoDetalle->saldo_factura;
                if(Notacreditodetalle::deleteAll("iddetallenota=:iddetallenota", [":iddetallenota" => $iddetallenota]))
                {
                    $this->ActualizarSaldo($idnotacredito);
                    $this->redirect(["notacredito/view",'id' => $idnotacredito]);
                }else{
                    echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("notacredito/index")."'>";
                }
            }else{
                echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("notacredito/index")."'>";
            }
        }else {
            return $this->redirect(["notacredito/index"]);
        }
    }
    
    //ACTUALIZAR SALDOS
    protected function ActualizarSaldo($idnotacredito) {
        $nota = Notacredito::findOne($idnotacredito);
        $nota->valor = 0;
        $nota->iva = 0;
        $nota->retefuente = 0;
        $nota->reteiva = 0;
        $nota->total = 0;
        $nota->save();
    }

    
    public function actionAutorizado($id)
    {
        $model = $this->findModel($id);
        $detalles = Notacreditodetalle::find()->where(['=', 'idnotacredito', $id])->One();
        if($detalles){
            if ($detalles->total_nota <= $detalles->saldo_factura){
                $model->valor = $detalles->valor_nota_credito;
                $model->iva = $detalles->valor_iva;
                $model->reteiva = $detalles->valor_reteiva;
                $model->retefuente = $detalles->valor_retencion;
                $model->total = $detalles->total_nota;
                if($model->autorizado == 0){
                    $model->autorizado = 1;
                }else{
                    $model->autorizado = 0;
                }
                $model->save();
                $this->redirect(["notacredito/view",'id' => $id]);
               
            }else{
                Yii::$app->getSession()->setFlash('error', 'EL valor de la nota crédito por $'.number_format($detalles->total_nota,0).' no puede ser mayor al saldo de la factura $'.number_format($detalles->saldo_factura,0));
                $this->redirect(["notacredito/view",'id' => $id]);
            }
        }else{
            Yii::$app->getSession()->setFlash('error', 'Para autorizar el registro debe tener productos relacionados en la nota de crédito.');
            $this->redirect(["notacredito/view",'id' => $id]);
        }
        
    }

    public function actionGenerar_documento($id)
    {
        $model = $this->findModel($id);
        $detalle = Notacreditodetalle::find()->where(['=','idnotacredito', $id])->one(); //busca numero de factura
        $factura = Facturaventa::findOne($detalle->idfactura);
       //generar consecutivo numero de la nota credito
        $codigo = Consecutivo::findOne(2);//2 nota credito
        $codigo->consecutivo = $codigo->consecutivo + 1;
        $codigo->consecutivo = $codigo->consecutivo;
        $codigo->save();
        //factura saldo
        if($model->motivoNota->codigo_interno == 4){
           $factura->saldo = $factura->saldo - $model->total;
           $factura->estado = 4; 
        }else{
           $factura->saldo = $factura->saldo - $model->total;
           $factura->estado = 3;  
        }
        $factura->save(false);
        $model->numero = $codigo->consecutivo;
        $model->fechapago = date('Y-m-d');
        if($model->save()){
            Yii::$app->getSession()->setFlash('info', 'El consecutivo de la nota credito se genero con Exito.');
            return $this->redirect(["notacredito/view",'id' => $id]);
        }      
    }
    
    //ENVIAR DOCUMENTO NOTA CREDITO DIAN
    public function actionEnviar_documento_dian($id)
    {
        $nota = Notacredito::findOne($id);
        $detalle_nota = Notacreditodetalle::find()->where(['=','idnotacredito', $id])->one();
        $factura = Facturaventa::findOne($detalle_nota->idfactura);
        //asignacion variable;
       $consecutivo = $detalle_nota->nrofactura;
       $resolucion = $factura->resolucion->codigo_interfaz; 
       $observacion = $nota->observacion;
       $id_detalle_factura = $nota->id_detalle_factura_api;
       // $observacion = $nota->observacion;
        //$cantidad_devolver = $detalle_nota->cantidad;
        //$detalle_codigo = $detalle_nota->id;
        $curl = curl_init();
        $API_KEY = "XgSaK2H9kBgIG6wrYdRHpqX5ekEGB0iS2dc2877703daac9d27fe919ea661bac0fbqyFG3QVs454VEX9Fj1W9zYDZTrLGch"; //VARIABLE CON API KEY DE DESARROLLO O PRODUCCIÓN SEGÚN SEA EL CASO
        $consecutivo_factura = "$consecutivo"; //CONSECUTIVO FACTURA
        $codigo_resolucion = "$resolucion"; //CÓDIGO DE LA RESOLUCIÓN QUE SE OBTIENE DESDE EL SISTEMA EN TABLAS>RESOLUCIONES
        //buscar informacion en la api
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
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        Yii::info("Respuesta completa de la API desde Begranda: $response", __METHOD__);
        
        if ($response === false || $httpCode !== 200) {
            $error = $response === false ? curl_error($curl) : "HTTP $httpCode";
            Yii::$app->getSession()->setFlash('error', 'Hubo un problema al comunicarse con la DIAN. Intenta reenviar más tarde.');
            Yii::error("Error en la solicitud CURL: $error", __METHOD__);
            return $this->redirect(['nota-credito/view', 'id' => $id]);
        }
        
        $data = json_decode($response, true);
       
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Yii::$app->getSession()->setFlash('error', 'Error al procesar la respuesta de la DIAN. Intenta reenviar más tarde.');
            Yii::error("Error al decodificar JSON: " . json_last_error_msg(), __METHOD__);
            return $this->redirect(['nota-credito/view', 'id' => $id]);
        }
       try {
            // Obtener el arreglo de detalles
            $detalles = $data['data'];
            $detallesClave = array_keys($detalles);
            $detallesClave = $detallesClave[0];
            $detalles = $detalles[$detallesClave]['details'];

            // Verificar si 'detalles' es un arreglo y no está vacío
            if (is_array($detalles) && count($detalles) > 0) {
                // Iterar sobre cada detalle y extraer el ID
                foreach ($detalles as $detalle) {
                    $id = $detalle['id'];
                    $nota->id_detalle_factura_api = $id;
                    $nota->save();
                }
                // se envia el body y head de la nota credito
                $nota = Notacredito::findOne($id);
                $id_detalle_factura = $nota->id_detalle_factura_api;
                $cantidad = $detalle_nota->cantidad;
                //
                $curl = curl_init();
                $API_KEY = "XgSaK2H9kBgIG6wrYdRHpqX5ekEGB0iS2dc2877703daac9d27fe919ea661bac0fbqyFG3QVs454VEX9Fj1W9zYDZTrLGch";
                $dataHead = json_encode([
                    "consecutivo_factura" => "$consecutivo ",
                    "codigo_resolucion" => "$resolucion",
                    "observacion" => "$observacion",
                 
                ]);
                $dataBody = json_encode([
                    [
                        "detalle_factura" => $id_detalle_factura,
                        "cantidad" => $cantidad,
                    ]
                ]);
                
                //ENVIO DE LOS DATOS
                curl_setopt_array($curl, [
                    CURLOPT_URL => "http://begranda.com/equilibrium2/public/api/bill?key=$API_KEY",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => [
                        "head" => $dataHead,
                        "body" => $dataBody
                    ],
                ]);
                // SE RECUPERA LA DATA
                 try {
                    $response = curl_exec($curl);
                    if (curl_errno($curl)) {
                        throw new Exception(curl_error($curl));
                    }
                    curl_close($curl);

                    $data = json_decode($response, true);
                    if ($data === null) {
                        throw new Exception('Error al decodificar la respuesta JSON');
                    }

                    // Validar y extraer el CUFE
                    if (isset($data['add']['fe']['cufe'])) {
                        $cude = $data['add']['fe']['cufe'];
                        $nota->cude = $cude;
                        $fechaRecepcion = isset($data["data"]["sentDetail"]["response"]["send_email_date_time"]) && !empty($data["data"]["sentDetail"]["response"]["send_email_date_time"]) ? $data["data"]["sentDetail"]["response"]["send_email_date_time"] : date("Y-m-d H:i:s");
                        $nota->fecha_recepcion_dian = $fechaRecepcion;
                        $nota->fecha_envio_api = date("Y-m-d H:i:s");
                        $qrstr = $data['add']['fe']['sentDetail']['response']['QRStr'];
                        $nota->qrstr = $qrstr;
                        $nota->save(false);
                        Yii::$app->getSession()->setFlash('success', "La Nota credito  No ($nota->numero) se envió con éxito a la DIAN.");
                    } else {
                        throw new Exception('El CUDE no se encontró en la respuesta.');
                    }
                } catch (Exception $e) {
                    Yii::$app->getSession()->setFlash('error', 'Error al enviar los datos de la Nota credito: ' . $e->getMessage());
                }
                
            } else { //RESPUESTA DEL ID EN AL API
                throw new Exception("El arreglo 'details' no existe o está vacío.");
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
      //  return $this->redirect(['facturaventa/view','id' => $id_factura, 'token' => $token]); 

    }
    
    
 
    /**
     * Finds the Notacredito model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Notacredito the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Notacredito::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionImprimir($id)
    {                                
        return $this->render('../formatos/notaCredito', [
            'model' => $this->findModel($id),
            
        ]);
    }
}
