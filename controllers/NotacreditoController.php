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
        $documentos = \app\models\DocumentoElectronico::find()->where(['=','codigo_interface', 4])->all();

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {

            $model->usuariosistema = Yii::$app->user->identity->username;
            $model->save(false);
            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
            'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreClientes"),
            'documentos' => ArrayHelper::map($documentos, "id_documento", "nombre_documento"),

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
        $documentos = \app\models\DocumentoElectronico::find()->where(['=','codigo_interface', 4])->all();
        if(Notacreditodetalle::find()->where(['=', 'idnotacredito', $id])->all()){
           Yii::$app->getSession()->setFlash('warning', 'No se puede modificar la información, tiene detalles asociados');
        }
        else if($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'clientes' => ArrayHelper::map($clientes, "idcliente", "nombreClientes"),
            'documentos' => ArrayHelper::map($documentos, "id_documento", "nombre_documento"),
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
                            $notaCredito->qrstr = $factura->qrstr;
                            $notaCredito->save();
                            $detalle_nota = Notacreditodetalle::find()->orderBy('iddetallenota DESC')->one(); 
                            $id_detalle =  $detalle_nota->iddetallenota;
                            $id = $idnotacredito;
                            $this->TotalizarImpuesto($id_detalle, $id);
                        }else{
                            $table->precio_unitario = $detalle_factura->preciounitario;
                            $table->cantidad = $detalle_factura->cantidad;
                            $table->porcentaje_iva = $detalle_factura->porcentaje_iva;
                            $table->porcentaje_retefuente =  $detalle_factura->porcentaje_retefuente;
                            $table->id = $detalle_factura->id;
                            $table->usuariosistema = Yii::$app->user->identity->username;
                            $table->save(false);
                            $notaCredito->fecha_factura_venta = $factura->fecha_inicio;
                            $notaCredito->cufe = $factura->cufe;
                            $notaCredito->qrstr = $factura->qrstr;
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
        if($nota->cliente->retencionfuente == 1){
             $valor_retencion = round(($subtotal_nota * $detalle->porcentaje_retefuente)/100);
        }else{
           $valor_retencion = 0; 
        }     
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
    public function actionEnviar_nota_credito_dian($id)
{
    $nota = Notacredito::findOne($id);
    $detalle_nota = Notacreditodetalle::find()->where(['idnotacredito' => $id])->one();
    $factura = $detalle_nota ? Facturaventa::findOne($detalle_nota->idfactura) : null;

    if (!$nota || !$detalle_nota || !$factura) {
        Yii::$app->session->setFlash('error', 'Datos incompletos para emitir la Nota Crédito.');
        return $this->redirect(['notacredito/view', 'id' => $id]);
    }

    $cliente = Cliente::findOne($factura->idcliente);
    $empresa = \app\models\Matriculaempresa::findOne(1);

    if (!$cliente || !$empresa) {
        Yii::$app->session->setFlash('error', 'No se encontró cliente o empresa para emitir la Nota Crédito.');
        return $this->redirect(['notacredito/view', 'id' => $id]);
    }

   // $fmt = fn($n) => number_format((float)$n, 2, '.', '');
    
    $emailempresa = $empresa->emailmatricula;
        
    $email_cc_list = [
                [
                    "email" => $emailempresa
                ]
            ];

    // ========================
    // DATOS BÁSICOS
    // ========================
    $resolucion = $factura->numero_resolucion; // Ajusta si sale de DB
    $observacion = (string)$nota->observacion;
    $confi = \app\models\ConfiguracionDocumentoElectronico::findOne(1);

    $date = date('Y-m-d', strtotime($nota->fecha));
    $time = date('H:i:s', strtotime($nota->fecha));

    $prefix = 'NC'; // Ajusta si sale de DB
    $numero_nc = $nota->numero;

    $type_document_id = $nota->id_documento; // NC
    
    $municipality_id_fact = $factura->cliente->municipio->codefacturador;

    // IMPORTANTE: puede ser null si no está relacionada la tabla motivoNota
    $codigo_respuesta = $nota->motivoNota->codeconceptoapi;

    if (!$codigo_respuesta) {
        Yii::$app->session->setFlash('error', 'La Nota Crédito no tiene motivo configurado (concepto discrepancia).');
        return $this->redirect(['notacredito/view', 'id' => $id]);
    }

    // ========================
    // CUSTOMER
    // ========================
    $customer = [
        "identification_number"           => (string)$cliente->cedulanit,
        "dv"                              => (int)$cliente->dv,
        "name"                            => (string)$cliente->nombrecorto,
        "phone"                           => (string)$cliente->telefonocliente,
        "address"                         => (string)$cliente->direccioncliente,
        "email"                           => (string)$cliente->email_envio_factura_dian,
        "type_document_identification_id" => (int)($cliente->tipo->codigo_api ?? 0),
        "type_organization_id"            => (int)$cliente->tiporegimen,
       // "municipality_id"                 => 12601,//$municipality_id_fact,
        "type_regime_id"                  => (int)$cliente->tiporegimen,
    ];

    // ========================
    // REFERENCIA FACTURA
    // ========================
    $billing_reference = [
        "number"     => $factura->consecutivo . $factura->nrofactura,
        "uuid"       => $factura->cufe, // CUFE factura original
        "issue_date" => date('Y-m-d', strtotime($factura->fecha_inicio))
    ];

    // ========================
    // DETALLE NOTA CRÉDITO
    // ========================
    $qty = (float)$detalle_nota->cantidad;
    $unit_price = (float)$detalle_nota->precio_unitario;
    $subtotal = $factura->subtotal;

    $porcIva = (float)$factura->porcentajeiva;
    $iva = $subtotal * ($porcIva / 100);

    $tax_totals = [[
        "tax_id" => 1,
        "tax_amount" => $iva,
        "percent" => $porcIva,
        "taxable_amount" => $subtotal,
    ]];

    $credit_note_lines = [[
        "unit_measure_id"             => 70,
        "invoiced_quantity"           => $qty,
        "line_extension_amount"       => $subtotal,
        "free_of_charge_indicator"    => false,
        "tax_totals"                  => $tax_totals,
        "description"                 => $observacion,
        "notes"                       => $observacion,
        "code"                        => "NC-" . $detalle_nota->idfactura,
        "type_item_identification_id" => 1,
        "price_amount"                => $unit_price,
        "base_quantity"               => 1,
    ]];

    // ========================
    // RETENCIONES
    // ========================
    $with_holding_tax_total = [];

    if ((float)$factura->retencionfuente > 0) {
        $with_holding_tax_total[] = [
            "tax_id" => 6,
            "taxable_amount" => $subtotal,
            "percent" => (float)$factura->porcentajefuente,
            "tax_amount" => $factura->retencionfuente,
        ];
    }

    if ((float)$factura->retencioniva > 0) {
        $with_holding_tax_total[] = [
            "tax_id" => 5,
            "taxable_amount" => $subtotal,
            "percent" => (float)$factura->porcentajereteiva,
            "tax_amount" => $factura->retencioniva,
        ];
    }

    // ========================
    // TOTALES
    // ========================
        $legal_monetary_totals = [
        "line_extension_amount"   => $subtotal,
        "tax_exclusive_amount"    => $subtotal,
        "tax_inclusive_amount"    => $subtotal + $iva,
        "allowance_total_amount"  => 0,
        "charge_total_amount"     => 0,
        "payable_amount"          => $nota->total, 
    ];

    // ========================
    // PAYLOAD FINAL
    // ========================
    $payload = [
        "billing_reference"              => $billing_reference,
        "discrepancyresponsecode"        => $codigo_respuesta,
        "discrepancyresponsedescription" => $observacion,
        "notes"                          => $observacion,
        "prefix"                         => $prefix,
        "sendmail"                       => true,
        "sendmailtome"                   => false,
        "email_cc_list"                  => $email_cc_list,
        "resolution_number"              => $resolucion,
        "number"                         => $numero_nc,
        "type_document_id"               => $type_document_id,
        "date"                           => $date,
        "time"                           => $time,
        "establishment_name"             => (string)$empresa->razonsocialmatricula,
        "establishment_address"          => (string)$empresa->direccionmatricula,
        "establishment_phone"            => (string)$empresa->celularmatricula,
        "establishment_email"            => $empresa->emailmatricula,
        "customer"                       => $customer,
        "tax_totals"                     => $tax_totals,
        "legal_monetary_totals"          => $legal_monetary_totals,
        "credit_note_lines"              => $credit_note_lines,
        "with_holding_tax_total"         => $with_holding_tax_total,
    ];

    // ========================
    // ENVÍO A API
    // ========================
    $API_URL = Yii::$app->params['API_NOTACREDITO_ENDPOINT'];
    $API_KEY = $confi->llave_api_token; // Bearer token

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $API_URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $API_KEY,
        ],
        CURLOPT_TIMEOUT => 60,
        CURLOPT_CONNECTTIMEOUT => 20,
    ]);

    try {
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);

        if ($response === false) {
            $curlError = curl_error($curl);
            curl_close($curl);
            throw new \Exception("Error cURL: " . $curlError);
        }

        curl_close($curl);

        // asegurar string para substr
        if (!is_string($response)) {
            $response = json_encode($response, JSON_UNESCAPED_UNICODE);
        }

        $data_envio = json_decode($response, true);

        // LOG SIEMPRE
        Yii::info(
            [
                'evento' => 'ENVIO_NOTA_CREDITO',
                'http_code' => $info['http_code'] ?? null,
                'curl_info' => $info,
                // 'payload_enviado' => $payload, // <-- comenta si es sensible
                'respuesta_raw' => $response,
                'respuesta_json' => $data_envio,
            ],
            'api.nota_credito'
        );

        if (!is_array($data_envio)) {
            throw new \Exception("La API no devolvió JSON válido. Respuesta: " . substr($response, 0, 300));
        }

        // Buscar CUDE en rutas comunes
        $cude = $data_envio['data']['fe']['cude'] ?? null;
        $qr   = $data_envio['qrstr'] ?? $data_envio['data']['qrstr'] ?? ($data_envio['QRStr'] ?? null);
        
        if ($qr) {
            $nota->qrstr = $qr;
        }
        
        if (!$cude) $cude = $data_envio['data']['cude'] ?? null;
        if (!$cude) $cude = $data_envio['data']['document']['cude'] ?? null;
        if (!$cude) $cude = $data_envio['cude'] ?? null;

        if (!empty($cude)) {
            $nota->cude = $cude;

            $nota->fecha_recepcion_dian =
            $data_envio['data']['sentDetail']['response']['send_email_date_time'] ?? ($data_envio['data']['sentDetail']['response']['datetime'] ?? date("Y-m-d H:i:s"));

            $nota->fecha_envio_api = date("Y-m-d H:i:s");
            $nota->save(false);

            Yii::$app->getSession()->setFlash('success', "La Nota Crédito No. {$numero_nc} fue enviada exitosamente a la DIAN.");
            return $this->redirect(['notacredito/view', 'id' => $id]);
        }
        

        // Si NO hay CUDE: leer mensaje sin romper por arrays
        $mensajeError = null;

        if (isset($data_envio['message'])) $mensajeError = $data_envio['message'];
        elseif (isset($data_envio['error'])) $mensajeError = $data_envio['error'];
        elseif (isset($data_envio['errors'])) $mensajeError = $data_envio['errors'];
        elseif (isset($data_envio['data']['errors'])) $mensajeError = $data_envio['data']['errors'];
        elseif (isset($data_envio['data']['message'])) $mensajeError = $data_envio['data']['message'];

        if (is_array($mensajeError)) {
            $mensajeError = json_encode($mensajeError, JSON_UNESCAPED_UNICODE);
        } elseif ($mensajeError !== null) {
            $mensajeError = (string)$mensajeError;
        }

        Yii::warning(
            [
                'evento' => 'RESPUESTA_SIN_CUDE',
                'http_code' => $info['http_code'] ?? null,
                'respuesta_json' => $data_envio,
                'mensaje_detectado' => $mensajeError,
            ],
            'api.nota_credito'
        );

        Yii::$app->getSession()->setFlash(
            'warning',
            "La API respondió pero no entregó CUDE. " . ($mensajeError ? "Detalle: {$mensajeError}" : "Revisa runtime/logs/app.log (categoría api.nota_credito).")
        );

        return $this->redirect(['notacredito/view', 'id' => $id]);

    } catch (\Exception $e) {

        Yii::error(
            [
                'evento' => 'ERROR_ENVIO_NOTA_CREDITO',
                'error' => $e->getMessage(),
            ],
            'api.nota_credito'
        );

        Yii::$app->getSession()->setFlash('error', "Error al enviar la Nota Crédito: " . $e->getMessage());
        return $this->redirect(['notacredito/view', 'id' => $id]);
    }
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
