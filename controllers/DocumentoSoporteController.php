<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\base\BaseObject; 

//models
use app\models\DocumentoSoporte;
use app\models\DocumentoSoporteSearch;
use app\models\UsuarioDetalle;
use app\models\Proveedor;
use app\models\Compra;


/**
 * DocumentoSoporteController implements the CRUD actions for DocumentoSoporte model.
 */
class DocumentoSoporteController extends Controller
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
     * Lists all DocumentoSoporte models.
     * @return mixed
     */
     public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',148])->all()){
                $form = new \app\models\FormFiltroDocumentoSoporte();
                $proveedor = null;
                $numero_compra = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $numero_soporte = null;
                $conProveedor = Proveedor::find()->orderBy('nombrecorto ASC')->all();
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $proveedor = Html::encode($form->proveedor);
                        $numero_compra = Html::encode($form->numero_compra);
                        $numero_soporte = Html::encode($form->numero_soporte);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $table = DocumentoSoporte::find()
                                ->andFilterWhere(['=', 'idproveedor', $proveedor])
                                ->andFilterWhere(['like', 'documento_compra', $numero_compra])
                                ->andFilterWhere(['=', 'numero_soporte', $numero_soporte])
                                ->andFilterWhere(['between', 'fecha_elaboracion', $fecha_inicio, $fecha_corte]);
                        $table = $table->orderBy('id_documento_soporte desc');
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
                            $this->actionExcelconsultaDocumentos($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = DocumentoSoporte::find()
                            ->orderBy('id_documento_soporte desc');
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 15,
                        'totalCount' => $count->count(),
                    ]);
                    $tableexcel = $table->all();
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){                    
                            $this->actionExcelconsultaDocumentos($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'model' => $model,
                            'form' => $form,
                            'pagination' => $pages,
                            'conProveedor' => \yii\helpers\ArrayHelper::map($conProveedor, 'idproveedor', 'nombrecorto'),
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }
    }

    /**
     * Displays a single DocumentoSoporte model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $detalles = \app\models\DocumentoSoporteDetalle::find()->where(['=','id_documento_soporte', $id])->all();
        //ACTUALIZA LINEAS DEL DOCUMENTO SOPORTE
        if(isset($_POST["actualizarlineas"])){
            if(isset($_POST["listado"])){
                $intIndice = 0;
                foreach ($_POST["listado"] as $intCodigo):
                    $documento = DocumentoSoporte::findOne($id);
                    $codigo = $_POST["id_concepto"][$intIndice];
                    $porcentaje = $_POST["id_retencion"][$intIndice];
                    if($porcentaje > 0){
                        $retenciones = \app\models\RetencionFuente::findOne($porcentaje);
                    }    
                    $concepto = \app\models\ConceptoDocumentoSoporte::findOne($codigo);
                    $table = \app\models\DocumentoSoporteDetalle::findOne($intCodigo);
                    $table->id_concepto = $codigo;
                    $table->descripcion = $concepto->concepto;
                    $table->cantidad = $_POST["cantidad"][$intIndice];
                    $table->valor_unitario = $_POST["valor_unitario"][$intIndice];
                    $table->id_retencion = $_POST["id_retencion"][$intIndice];
                    $suma = ($table->cantidad *  $table->valor_unitario);
                    if($porcentaje > 0){
                       $table->porcentaje_retencion = $retenciones->porcentaje; 
                       $subtotal = round(($suma * $table->porcentaje_retencion)/100);
                       $table->valor_retencion = $subtotal;
                       $table->total_pagar = $suma - $subtotal;
                    }else{
                        $table->porcentaje_retencion = 0;
                         $table->valor_retencion = 0;
                         $table->total_pagar = $suma;
                    }
                    $table->save(false);
                    $documento->valor_pagar = $table->total_pagar;
                    $documento->save();
                    $intIndice++;
                endforeach;
                
               return $this->redirect(['view','id' =>$id]);
            }
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
            'detalles' => $detalles,
        ]);
    }

    /**
     * Creates a new DocumentoSoporte model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($sw, $Token)
    {
        $config = \app\models\ConfiguracionDocumentoElectronico::findOne(1);
        if($config->aplica_documento_soporte == 0){
            Yii::$app->getSession()->setFlash('error', 'No esta autorizado para generar documentos soportes. Comunicate con un asesor.'); 
            return $this->redirect(['index']);
        }
        $model = new DocumentoSoporte();
        $Acceso = 0;
        $conCompra = Compra::find()->orderBy('id_compra DESC')->all();
        $resolucion = \app\models\Resolucion::find()->where(['=','activo', 0])->andWhere(['=','id_documento', 2])->one();
        if(!$resolucion){
            Yii::$app->getSession()->setFlash('error', 'Debe de configurar la resolucion de documentos soporte o se encuentra INACTIVA.'); 
            return $this->redirect(['index']);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if($sw == 1){
                $compra = Compra::findOne($model->id_compra);
                $model->documento_compra = $compra->factura;
            }
            $model->idresolucion = $resolucion->idresolucion;
            $model->consecutivo = $resolucion->consecutivo;
            $model->user_name = Yii::$app->user->identity->username;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id_documento_soporte]);
        }
        $fecha_actual_dia = date('Y-m-d');
        if($fecha_actual_dia >= $resolucion->fecha_notificacion){
            $Acceso = 1; //aviso que se le esta venciendo la resolucion
        }
        return $this->render('create', [
            'model' => $model,
            'sw' => $sw,
            'Acceso' => $Acceso,
            'Token' => $Token,
            'resolucion' => $resolucion,
            'conCompra' => \yii\helpers\ArrayHelper::map($conCompra, 'id_compra', 'Compras'),
        ]);
    }

    //proceso que llena el combo de cta de cobro
     
    public function actionCargarcompras($id){
        $rows = Compra::find()->where(['=','id_proveedor', $id])->andWhere(['=','genera_documento_soporte', 1])->andWhere(['=','documento_generado', 0])
                                              ->orderBy('fechainicio ASC')->all();

        echo "<option value='' required>Seleccione el documento...</option>";
        if(count($rows)>0){
            foreach($rows as $row){
                echo "<option value='$row->id_compra' required>$row->Compras</option>";
            }
        }
    }
     
    /**
     * Updates an existing DocumentoSoporte model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $sw, $Token)
    {
        $model = $this->findModel($id);
        $resolucion = \app\models\Resolucion::find()->where(['=','activo', 0])->andWhere(['=','id_documento', 2])->one();
        $Acceso = 0;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_documento_soporte]);
        }
        
        $fecha_actual_dia = date('Y-m-d');
        if($fecha_actual_dia >= $resolucion->fecha_notificacion){
            $Acceso = 1; //aviso que se le esta venciendo la resolucion
        }
        return $this->render('update', [
            'model' => $model,
            'sw' => $sw,
            'Token' => $Token,
            'Acceso' => $Acceso,
            'resolucion' => $resolucion,
        ]);
    }

    //CREA UNA NUEVA LINEA
    public function actionNueva_linea($id, $token) {
        if($token == 0){
            $table = new \app\models\DocumentoSoporteDetalle();
            $table->id_documento_soporte = $id;
            $table->cantidad = 1;
            $table->save(false);
             return $this->redirect(['view', 'id' => $id]);
        }else{
            $model = $this->findModel($id);
            $compra = Compra::findOne($model->id_compra);
            $table = new \app\models\DocumentoSoporteDetalle();
            $table->id_documento_soporte = $id;
            $table->cantidad = 1;
            $table->valor_unitario = $compra->subtotal;
            $table->porcentaje_retencion = $compra->porcentajefuente;
            $table->save(false);
            return $this->redirect(['view', 'id' => $id]);
        }    
       
    }
    
    /**
     * Deletes an existing DocumentoSoporte model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $id_detalle)
    {
        $dato = \app\models\DocumentoSoporteDetalle::findOne($id_detalle);
        $dato->delete();
        return $this->redirect(['view', 'id' => $id]);
    }
    
    //PROCESO QUE AUTORIZA
    public function actionAutorizado($id) {
        $model = $this->findModel($id);
        if($model->valor_pagar != null){
            if(\app\models\DocumentoSoporteDetalle::find()->where(['=','id_documento_soporte', $id])->one()){
                if($model->autorizado == 0){
                    $model->autorizado = 1;
                    $model->save();
                    return $this->redirect(['view', 'id' => $id]);
                }else{
                    $model->autorizado = 0;
                    $model->save();
                    return $this->redirect(['view', 'id' => $id]);
                }
            }else{
                Yii::$app->getSession()->setFlash('error', 'Debe de creaun una nueva linea para subir el registro completo'); 
                return $this->redirect(['view', 'id' => $id]);
            }
        }else{
             Yii::$app->getSession()->setFlash('error', 'El campo Total documento no puede ser vacio, debe de actualizar el detalle del documento soporte!'); 
                return $this->redirect(['view', 'id' => $id]);
        }    
    }
    
    //proceso que genera el consecutivo
    public function actionGenerar_documento($id) {
        $model = $this->findModel($id);
        $codigo = \app\models\Consecutivo::findOne(22);
        $numero = $codigo->consecutivo + 1;
        $codigo->consecutivo = $numero;
        $codigo->save();
        //model
        $model->numero_soporte = $numero;
        $model->save();
        if($model->id_compra != ''){
            $compra = Compra::findOne($model->id_compra);
            $compra->documento_generado = 1;
            $compra->save();
        }
        return $this->redirect(['view', 'id' => $id]);
    }
    
    //ENVIAR DOCUMENTO A LA DIAN
    public function actionEnviar_documento_soporte_dian($id) 
        {
            $documento = DocumentoSoporte::findOne($id);
            if (!$documento) {
                Yii::$app->session->setFlash('error', 'Documento de soporte no encontrado.');
                return $this->redirect(['index']);
            }

            // Función de redondeo que SIEMPRE retorna float con 2 decimales
            $round2 = function($value) {
                return (float)number_format((float)$value, 2, '.', '');
            };

            // VALIDAR FECHA (debe ser hoy)
            $fecha_actual = date('Y-m-d');
            $fecha_documento = date('Y-m-d', strtotime($documento->fecha_elaboracion));
            if ($fecha_actual !== $fecha_documento) {
                Yii::$app->session->setFlash('error', 'La fecha de envío debe ser igual a la fecha del documento.');
                return $this->redirect(['view', 'id' => $id]); 
            }

            // CONFIGURACIÓN DE DOCUMENTOS
            $confi = \app\models\ConfiguracionDocumentoElectronico::findOne(1);

            $proveedor = Proveedor::findOne($documento->idproveedor);
            if (!$proveedor) {
                Yii::$app->session->setFlash('error', 'Proveedor no encontrado.');
                return $this->redirect(['view', 'id' => $id]);
            }

            // VALIDACIONES
            if (empty($proveedor->emailproveedor) || empty($proveedor->direccionproveedor)) {
                Yii::$app->session->setFlash('error', 'Los campos DIRECCIÓN y EMAIL del proveedor no pueden estar vacíos.'); 
                return $this->redirect(['view', 'id' => $id]);
            }

            if (!$proveedor->municipio || !$proveedor->municipio->codefacturador) {
                Yii::$app->session->setFlash('error', 'El municipio del proveedor no está codificado.');
                return $this->redirect(['view', 'id' => $id]);
            }

            $documento_detalle = \app\models\DocumentoSoporteDetalle::find()
                ->where(['id_documento_soporte' => $id])
                ->one();

            if (!$documento_detalle) {
                Yii::$app->session->setFlash('error', 'No hay detalle del documento.');
                return $this->redirect(['view', 'id' => $id]);
            }

            $nombre_empresa = \app\models\Matriculaempresa::findOne(1);
            $resolucion = \app\models\Resolucion::find()
                ->where(['idresolucion' => $documento->idresolucion])
                ->andWhere(['activo' => 0])
                ->one();

            if (!$resolucion) {
                Yii::$app->session->setFlash('error', 'Resolución no encontrada.');
                return $this->redirect(['view', 'id' => $id]);
            }

            // ENDPOINT
            $API_URL = Yii::$app->params['API_DOCUMENTO_SOPORTE'];
            $apiBearerToken = $confi->llave_api_token;

            // DATOS BÁSICOS
            $number = (int)($documento->numero_soporte ?? 0);
            $type_document_id = 11; // Documento Soporte
            $prefix = $resolucion->consecutivo ?? 'ds';
            $resolution_number = $resolucion->nroresolucion;

            $date = $documento->fecha_elaboracion ? date('Y-m-d', strtotime($documento->fecha_elaboracion)) : date('Y-m-d');
            $time = $documento->fecha_elaboracion ? date('H:i:s', strtotime($documento->fecha_elaboracion)) : date('H:i:s');

            $telefono_proveedor = $proveedor->telefonoproveedor;
            if (!$telefono_proveedor) {
                Yii::$app->session->setFlash('error', 'El teléfono del proveedor es obligatorio.');
                return $this->redirect(['view', 'id' => $id]);
            }

            // SELLER (PROVEEDOR) - Siempre tipo documento 6 (NIT) para Documento Soporte
            $seller = [
                "identification_number" => (string)($proveedor->cedulanit),
                "dv" => (int)($proveedor->dv ?? 0),
                "name" => (string)($proveedor->nombrecorto),
                "phone" => (string)($telefono_proveedor),
                "address" => (string)($proveedor->direccionproveedor),
                "email" => (string)($proveedor->emailproveedor),
                "merchant_registration" => (string)($proveedor->merchant_registration ?? '0000-00'),
                "postal_zone_code" => (int)($proveedor->municipio->postal_zone_code ?? 630001),
                "type_document_identification_id" => 6, // Siempre NIT para Documento Soporte
                "type_organization_id" => (int)($proveedor->tiporegimen ?? 2),
                "municipality_id" => (int)($proveedor->municipio->codigo_api_nomina),
                "type_liability_id" => (int)($proveedor->autoretenedor == 1 ? 9 : 117),
                "type_regime_id" => (int)($proveedor->tiporegimen ?? 1),
            ];

            // CÁLCULOS DESDE LA LÍNEA
            $qty = $round2((float)($documento_detalle->cantidad));
            $unit_price = $round2((float)($documento_detalle->valor_unitario));

            // Calcular el subtotal de la línea
            $line_subtotal = $round2($qty * $unit_price);

            $tax_id = 1; // IVA

            // RETENCIONES (si aplica)
            $line_with_holding_tax_total = [];
            if ($documento_detalle->valor_retencion > 0 || $documento_detalle->porcentaje_retencion > 0) {
                $porcentaje_fuente = $round2((float)$documento_detalle->porcentaje_retencion);
                $retefuente_amount = $round2($line_subtotal * ($porcentaje_fuente / 100));

                $line_with_holding_tax_total[] = [
                    "tax_id"         => 6, 
                    "taxable_amount" => $round2($line_subtotal),
                    "percent"        => $round2($porcentaje_fuente),
                    "tax_amount"     => $round2($retefuente_amount),
                ];
            }

            // LÍNEAS DEL DOCUMENTO
            $invoice_lines = [[
                "unit_measure_id" => 70,
                "invoiced_quantity" => $round2($qty),
                "line_extension_amount" => $round2($line_subtotal),
                "free_of_charge_indicator" => false,
                "tax_totals" => [[
                    "tax_id" => 1,
                    "tax_amount" => $round2(0),
                    "percent" => $round2(0),
                    "taxable_amount" => $round2($line_subtotal),
                ]],
                "description" => (string)($documento_detalle->descripcion),
                "notes" => (string)($documento->observacion ?? ''),
                "code" => (string)($documento_detalle->id_detalle),
                "type_item_identification_id" => 1,
                "price_amount" => $round2($unit_price),
                "base_quantity" => $round2($qty),
                "type_generation_transmition_id" => 1,
                "start_date" => $date,
            ]];

            // TOTALES GENERALES
            $tax_totals = [[
                "tax_id" => $tax_id,
                "tax_amount" => $round2(0),
                "percent" => $round2(0),
                "taxable_amount" => $round2($line_subtotal),
            ]];

            $total_con_iva = $round2($line_subtotal);

            $legal_monetary_totals = [
                "line_extension_amount" => $round2($line_subtotal),
                "tax_exclusive_amount" => $round2($line_subtotal),
                "tax_inclusive_amount" => $round2($total_con_iva),
                "charge_total_amount" => $round2(0),
                "payable_amount" => $round2($total_con_iva),
            ];

            // FORMA DE PAGO
            $plazo_dias = (int)($documento->proveedor->plazopago ?? 0);
            $es_credito = $plazo_dias > 0;
            
            $fecha_inicio = strtotime('+' . $plazo_dias . ' day', strtotime($documento->fecha_elaboracion));
            $fecha_vencimiento = date('Y-m-d', $fecha_inicio);

            $payment_form = [
                "payment_form_id" => $es_credito ? 2 : 1, // 1 = Contado, 2 = Crédito
                "payment_method_id" => (int)($documento->formaPago->codigo_medio_pago_dian ?? 10),
                "payment_due_date" => $fecha_vencimiento,
                "duration_measure" => $plazo_dias,
            ];



            // PAYLOAD FINAL
            $payload = [
                "number" => $number,
                "type_document_id" => $type_document_id,
                "date" => $date,
                "time" => $time,
                "notes" => (string)($documento->observacion ?? 'SIN OBSERVACIONES'),
                "sendmail" => true,
                "email_cc_list" => [
                    [
                        "email" => "$nombre_empresa->emailmatricula"
                    ]
                ],
                "resolution_number" => $resolution_number,
                "prefix" => $prefix,
                "establishment_name" => $nombre_empresa->razonsocialmatricula,
                "tarifaica" => (string)($nombre_empresa->tarifaica ?? '0'),
                "actividadeconomica" => (string)($resolucion->codigoactividad),
                "seller" => $seller,
                "payment_form" => $payment_form,
                "legal_monetary_totals" => $legal_monetary_totals,
                "tax_totals" => $tax_totals,
                "with_holding_tax_total" => $line_with_holding_tax_total,
                "invoice_lines" => $invoice_lines,
            ];

            // Solo agregar retenciones si existen
            if (!empty($line_with_holding_tax_total)) {
                $payload["line_with_holding_tax_total"] = $line_with_holding_tax_total;
            }

            Yii::info(
                "JSON DOCUMENTO SOPORTE ENVIADO A DIAN:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                'document_support.debug.json'
            );

            $DEBUG_MODE = false;

            if ($DEBUG_MODE) {
                $subtotal_bd = $round2($documento_detalle->valor_unitario ?? 0);
                $iva_bd = $round2($documento->impuesto_iva ?? 0);

                $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Verificación Documento Soporte</title></head><body>';
                $html .= '<div style="font-family: Arial, sans-serif; max-width: 1400px; margin: 20px auto; padding: 20px;">';
                $html .= '<h2 style="color: #d9534f;">🔍 MODO DEBUG - VERIFICACIÓN DOCUMENTO SOPORTE</h2>';
                $html .= '<p style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;"><strong>⚠️ Advertencia:</strong> El documento NO fue enviado a la DIAN. Esta es solo una verificación.</p>';

                $html .= '<div style="background: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 2px solid #007bff;">';
                $html .= '<h3 style="margin-top: 0; color: #007bff;">📋 Información del Documento</h3>';
                $html .= '<p><strong>Número:</strong> ' . $prefix . '-' . $number . '</p>';
                $html .= '<p><strong>Proveedor:</strong> ' . $proveedor->nombrecorto . ' (NIT: ' . $proveedor->cedulanit . ')</p>';
                $html .= '<p><strong>Fecha:</strong> ' . $date . ' ' . $time . '</p>';
                $html .= '</div>';

                $html .= '<div style="background: #f0f0f0; padding: 20px; margin-bottom: 20px; border-radius: 5px;">';
                $html .= '<h3 style="color: #333; margin-top: 0;">📊 Comparación: Base de Datos vs Calculado</h3>';
                $html .= '<table style="width: 100%; border-collapse: collapse; background: white;">';
                $html .= '<thead><tr style="background: #007bff; color: white;">';
                $html .= '<th style="padding: 10px; text-align: left;">Concepto</th>';
                $html .= '<th style="padding: 10px; text-align: right;">Valor en BD</th>';
                $html .= '<th style="padding: 10px; text-align: right;">Valor Calculado</th>';
                $html .= '<th style="padding: 10px; text-align: center;">Estado</th>';
                $html .= '</tr></thead><tbody>';

                $dif_subtotal = abs($subtotal_bd - $line_subtotal);
                $ok_subtotal = $dif_subtotal < 0.02;
                $html .= '<tr style="background: ' . ($ok_subtotal ? '#d4edda' : '#fff3cd') . ';">';
                $html .= '<td style="padding: 8px;"><strong>Subtotal</strong></td>';
                $html .= '<td style="padding: 8px; text-align: right;">$' . number_format($subtotal_bd, 2) . '</td>';
                $html .= '<td style="padding: 8px; text-align: right;">$' . number_format($line_subtotal, 2) . '</td>';
                $html .= '<td style="padding: 8px; text-align: center;">' . ($ok_subtotal ? '✅' : '⚠️') . '</td>';
                $html .= '</tr>';

                $html .= '</tbody></table></div>';

                $html .= '<div style="background: #e7f3ff; padding: 20px; margin-bottom: 20px; border-radius: 5px; border-left: 4px solid #007bff;">';
                $html .= '<h3 style="color: #004085; margin-top: 0;">🧮 Cálculos Detallados</h3>';
                $html .= '<table style="width: 100%; border-collapse: collapse;">';
                $html .= '<tr><td style="padding: 5px;">Cantidad:</td><td style="text-align: right; font-family: monospace;">' . number_format($qty, 2) . ' unidades</td></tr>';
                $html .= '<tr><td style="padding: 5px;">Precio Unitario:</td><td style="text-align: right; font-family: monospace;">$' . number_format($unit_price, 2) . '</td></tr>';
                $html .= '<tr style="border-top: 2px solid #007bff;"><td style="padding: 5px; padding-top: 10px;"><strong>Subtotal:</strong></td><td style="text-align: right; font-family: monospace; padding-top: 10px;"><strong>$' . number_format($line_subtotal, 2) . '</strong></td></tr>';
                $html .= '<tr style="border-top: 2px solid #007bff; background: #d1ecf1;"><td style="padding: 10px;"><strong>Total:</strong></td><td style="text-align: right; font-family: monospace; font-size: 1.2em;"><strong>$' . number_format($total_con_iva, 2) . '</strong></td></tr>';
                $html .= '</table></div>';

                $html .= '<h3 style="color: #333;">📄 JSON que se enviaría a la DIAN:</h3>';
                $json_pretty = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                $html .= '<div style="background: #2d2d2d; color: #f8f8f2; padding: 20px; border-radius: 5px; overflow-x: auto;">';
                $html .= '<pre style="margin: 0; font-family: Consolas, Monaco, monospace; font-size: 11px;">' . htmlspecialchars($json_pretty) . '</pre>';
                $html .= '</div>';

                $html .= '<div style="margin-top: 20px; text-align: center;">';
                $html .= '<a href="' . \yii\helpers\Url::to(['view', 'id' => $id]) . '" style="display: inline-block; padding: 12px 30px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">← Volver al documento</a>';
                $html .= '</div>';

                $html .= '</div></body></html>';

                echo $html;
                Yii::$app->end();
            }

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
                CURLOPT_TIMEOUT => 120,
                CURLOPT_SSL_VERIFYPEER => false, 
                CURLOPT_SSL_VERIFYHOST => false, 
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
                $rawBody = $headerSize ? substr($response, $headerSize) : $response;
                $httpCode = (int)($info['http_code'] ?? 0);
                curl_close($curl);

                Yii::info("HTTP_CODE={$httpCode}\nBODY:\n{$rawBody}", 'document_support.debug.response');

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
                            'message' => $msg,
                            'errors' => $errors,
                        ], 'document_support.debug.validation_errors');

                        $flat = [];
                        foreach ($errors as $field => $arr) {
                            $flat[] = $field . ': ' . (is_array($arr) ? implode(' | ', $arr) : $arr);
                        }
                        $msg .= " | " . implode(' || ', $flat);
                    }

                    throw new \Exception($msg);
                }

                $cuds = $data['cuds'] ?? $data['data']['cuds'] ?? null;
                $qr = $data['qrstr'] ?? $data['data']['qrstr'] ?? null;

                $documento->fecha_envio_api = date("Y-m-d H:i:s");

                if ($cuds) {
                    $documento->cuds = $cuds;
                    $documento->fecha_recepcion_dian = date("Y-m-d H:i:s");
                }

                if ($qr) {
                    $documento->qrstr = $qr;
                }

                $documento->save(false);

                Yii::$app->session->setFlash('success', "Documento de Soporte No ({$number}) fue enviado exitosamente a la DIAN.");
                return $this->redirect(['view', 'id' => $id]);

            } catch (\Exception $e) {
                Yii::error("ERROR ENVÍO DOCUMENTO SOPORTE: " . $e->getMessage(), 'document_support.debug.error');
                Yii::$app->session->setFlash('error', 'Error al enviar documento: ' . $e->getMessage());
                return $this->redirect(['view', 'id' => $id]);
            }
        }
    
    /**
     * Finds the DocumentoSoporte model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DocumentoSoporte the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DocumentoSoporte::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionImprimir_documento_soporte($id) {
        return $this->render('../formatos/documento_soporte', [
            'model' => $this->findModel($id),
            
        ]);
    }
    
     ///exceles
    
    public function actionExcelconsultaDocumentos($tableexcel) {                
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
       
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Id')
                    ->setCellValue('B1', 'Numero')
                    ->setCellValue('C1', 'Proveedor')
                    ->setCellValue('D1', 'Numero de compra')
                    ->setCellValue('E1', 'Fecha elaboracion')
                    ->setCellValue('F1', 'Fecha hora registro')
                    ->setCellValue('G1', 'Fecha recepcion Dian')
                    ->setCellValue('H1', 'Fecha envio API')
                    ->setCellValue('I1', 'Cantidad')
                    ->setCellValue('J1', 'Vlr unitario')
                    ->setCellValue('K1', 'Porcenatje retencion')
                    ->setCellValue('L1', 'Valor retencion')
                    ->setCellValue('M1', 'Total pagar')
                    ->setCellValue('N1', 'Oservaciones')
                    ->setCellValue('O1', 'Concepto de compra')                      
                    ->setCellValue('P1', 'Forma de pago')
                    ->setCellValue('Q1', 'Cuds');
        $i = 2;
        
        foreach ($tableexcel as $val) {
            $detalle = \app\models\DocumentoSoporteDetalle::find()->where(['=','id_documento_soporte', $val->id_documento_soporte])->all();
            foreach ($detalle as $key => $detalles) {
                                
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $val->id_documento_soporte)
                        ->setCellValue('B' . $i, $val->numero_soporte)
                        ->setCellValue('C' . $i, $val->proveedor->nombrecorto)
                        ->setCellValue('D' . $i, $val->documento_compra)
                        ->setCellValue('E' . $i, $val->fecha_elaboracion)
                        ->setCellValue('F' . $i, $val->fecha_hora_registro)
                        ->setCellValue('G' . $i, $val->fecha_recepcion_dian)
                        ->setCellValue('H' . $i, $val->fecha_envio_api)
                        ->setCellValue('I' . $i, $detalles->cantidad)
                        ->setCellValue('J' . $i, $detalles->valor_unitario)
                        ->setCellValue('K' . $i, $detalles->porcentaje_retencion)
                        ->setCellValue('L' . $i, $detalles->valor_retencion)
                        ->setCellValue('M' . $i, $val->valor_pagar)
                        ->setCellValue('N' . $i, $val->observacion)
                        ->setCellValue('O' . $i, $detalles->descripcion)
                        ->setCellValue('P' . $i, $val->formaPago->concepto)
                        ->setCellValue('Q' . $i, $val->cuds);
                $i++;
            }
            $i = $i;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listados');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Documento_soporte.xlsx"');
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
