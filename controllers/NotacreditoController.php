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
        $nota         = Notacredito::findOne($id);
        $detalle_nota = Notacreditodetalle::find()->where(['idnotacredito' => $id])->one();
        $factura      = $detalle_nota ? Facturaventa::findOne($detalle_nota->idfactura) : null;

        if (!$nota || !$detalle_nota || !$factura) {
            Yii::$app->session->setFlash('error', 'Datos incompletos para emitir la Nota Crédito.');
            return $this->redirect(['notacredito/view', 'id' => $id]);
        }

        $cliente = Cliente::findOne($factura->idcliente);
        $empresa = \app\models\Matriculaempresa::findOne(1);
        $confi = \app\models\ConfiguracionDocumentoElectronico::findOne(1);

        if (!$cliente || !$empresa) {
            Yii::$app->session->setFlash('error', 'No se encontró cliente o empresa para emitir la Nota Crédito.');
            return $this->redirect(['notacredito/view', 'id' => $id]);
        }
        
        // Cálculos Básicos
        $codigo_respuesta = $nota->motivoNota->codeconceptoapi ?? null;
        if (!$codigo_respuesta) {
            Yii::$app->session->setFlash('error', 'La Nota Crédito no tiene motivo configurado.');
            return $this->redirect(['notacredito/view', 'id' => $id]);
        }

        $emailempresa = $empresa->emailmatricula;
        $email_cc_list = [
            ["email" => $emailempresa]
        ];
        
        // ========================
        // CÁLCULO DE MONTOS - FUNCIONA PARA PARCIAL Y COMPLETA
        // ========================
        $qty_devuelta        = (float)$detalle_nota->cantidad;
        $precio_unitario     = round((float)$detalle_nota->precio_unitario, 2);
        $subtotal_devolucion = round($qty_devuelta * $precio_unitario, 2);
        $porcentaje_iva      = round((float)$factura->porcentajeiva, 2);
        $iva_devolucion      = round($subtotal_devolucion * ($porcentaje_iva / 100), 2);
        
        $subtotal_factura = round((float)$factura->subtotal, 2);
        $iva_factura      = round((float)$factura->impuestoiva, 2);
        
        if (abs($subtotal_devolucion - $subtotal_factura) < 1.00) {
            $subtotal_devolucion = $subtotal_factura;
            $iva_devolucion      = $iva_factura;
            $tipo_devolucion     = 'COMPLETA';
        } else {
            $tipo_devolucion = 'PARCIAL';
        }
        //FUNCION DL REDONDEO
        $round2 = function($val) { return round((float)$val, 2); };
        
        //Obtenemos los porcentajes, asegurando que si no existen sean 0
        $porcentaje_rf = (float)($factura->porcentajefuente ?? 0);
        $porcentaje_ri = (float)($empresa->porcentajereteiva ?? 0);
        
        $retefuente_calc = $round2($subtotal_devolucion * ($porcentaje_rf / 100));
        $reteiva_calc    = $round2($iva_devolucion * ($porcentaje_ri / 100));
        
        
        $withholding_tax_totals = [];
        
        if ($retefuente_calc > 0) {
            $withholding_tax_totals[] = [
                "tax_id" => 6, 
                "tax_amount" => $retefuente_calc,
                "percent" => $porcentaje_rf,
                "taxable_amount" => $subtotal_devolucion,
            ];
        }
        if ($reteiva_calc > 0) {
            $withholding_tax_totals[] = [
                "tax_id" => 5, 
                "tax_amount" => $reteiva_calc,
                "percent" => $porcentaje_ri,
                "taxable_amount" => $iva_devolucion,
            ];
        }
        // 2. LUEGO: Define las variables de impuestos (Aquí es donde se define $tax_totals)
       $tax_totals = [[
            "tax_id" => 1, 
            "tax_amount" => $iva_devolucion, 
            "percent" => $porcentaje_iva, 
            "taxable_amount" => $subtotal_devolucion
        ]];
        
        // 5. Calcular el total a pagar final
        $tax_inclusive_amount = round($subtotal_devolucion + $iva_devolucion - $retefuente_calc - $reteiva_calc, 2);
        
        

    // 6. Construir el payload
    $payload = [
        "billing_reference"              => ["number" => $factura->consecutivo . $factura->nrofactura, "uuid" => $factura->cufe, "issue_date" => date('Y-m-d', strtotime($factura->fecha_inicio))],
        "discrepancyresponsecode"        => (int)$codigo_respuesta,
        "discrepancyresponsedescription" => (string)$nota->observacion,
        "prefix"                         => 'NC',
        "sendmail"                       => true,
        "sendmailtome"                   => false,
        "email_cc_list"                  => $email_cc_list,
        "number"                         => (int)$nota->numero,
        "date"                           => date('Y-m-d', strtotime($nota->fecha)),
        "time"                           => date('H:i:s', strtotime($nota->fecha)),
        "customer" => [
            "identification_number"      => (string)$cliente->cedulanit, "dv" => (int)$cliente->dv, "name" => (string)$cliente->nombrecorto,
            "phone"                      => (string)$cliente->telefonocliente, "address" => (string)$cliente->direccioncliente, "email" => (string)$cliente->email_envio_factura_dian,
            "type_document_identification_id" => (int)($cliente->tipo->codigo_api ?? 0), "type_organization_id" => (int)$cliente->tiporegimen, "type_regime_id" => (int)$cliente->tiporegimen,
        ],
        "tax_totals" => $tax_totals,
        "legal_monetary_totals" => [
            "line_extension_amount" => $subtotal_devolucion, 
            "tax_exclusive_amount"  => $subtotal_devolucion, 
            "tax_inclusive_amount"  => $tax_inclusive_amount, 
            "payable_amount"        => $tax_inclusive_amount
        ],
        "credit_note_lines" => [[
            "unit_measure_id"       => 70, 
            "invoiced_quantity"     => $qty_devuelta, 
            "line_extension_amount" => $subtotal_devolucion,
            "tax_totals"            => $tax_totals, 
            "description"           => (string)$nota->observacion, 
            "price_amount"          => $precio_unitario
        ]]
    ];

    if (!empty($withholding_tax_totals)) {
        $payload["withholding_tax_totals"] = $withholding_tax_totals;
    }
       
       // ========================
        // MODO DEBUG - ESTRUCTURADO
        // ========================
        if ($confi->debug ?? false) {
            $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Debug Nota Crédito</title></head><body>';
            $html .= '<div style="font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; border: 1px solid #ccc; padding: 20px; border-radius: 8px;">';

            // Encabezado Empresa
            $html .= '<div style="border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px;">';
            $html .= '<h1 style="margin:0; color:#333;">' . $empresa->razonsocialmatricula . '</h1>';
            $html .= '<p style="margin:0;">NIT: ' . $empresa->nitmatricula . ' | ' . $empresa->direccionmatricula . '</p>';
            $html .= '</div>';

            // Info General y Cliente
            $html .= '<div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 14px;">';
            $html .= '<div><strong>Nota Crédito No:</strong> ' . $nota->numero . '<br><strong>Fecha:</strong> ' . $nota->fecha . '</div>';
            $html .= '<div><strong>Cliente:</strong> ' . $cliente->nombrecorto . '<br><strong>NIT/CC:</strong> ' . $cliente->cedulanit . '</div>';
            $html .= '</div>';

            // Detalle de Cálculos
            $html .= '<h3 style="background: #f4f4f4; padding: 10px; border-left: 5px solid #007bff;">🧮 Detalle de Cálculos</h3>';
            $html .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">';
            $html .= '<tr style="background:#eee;"><th style="padding:10px; text-align:left;">Concepto</th><th style="padding:10px; text-align:right;">Valor</th></tr>';
            $html .= '<tr><td style="padding:8px;">Subtotal Devolución</td><td style="text-align:right;">$' . number_format($subtotal_devolucion, 2) . '</td></tr>';
            $html .= '<tr><td style="padding:8px;">IVA (' . $porcentaje_iva . '%)</td><td style="text-align:right;">$' . number_format($iva_devolucion, 2) . '</td></tr>';

            foreach ($withholding_tax_totals as $ret) {
                $nombre = ($ret['tax_id'] == 6) ? 'ReteFuente' : 'ReteIVA';
                $html .= '<tr><td style="padding:8px;">' . $nombre . ' (' . $ret['percent'] . '%)</td><td style="text-align:right; color:#d9534f;">-$' . number_format($ret['tax_amount'], 2) . '</td></tr>';
            }

            $html .= '<tr style="border-top: 2px solid #333; font-weight:bold; font-size:16px;">';
            $html .= '<td style="padding:10px;">Total a Aplicar</td><td style="text-align:right;">$' . number_format($tax_inclusive_amount, 2) . '</td></tr>';
            $html .= '</table>';

            // Payload JSON
            $html .= '<h3 style="background: #f4f4f4; padding: 10px; border-left: 5px solid #28a745;">📄 JSON Final para API</h3>';
            $html .= '<pre style="background: #272822; color: #f8f8f2; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px;">';
            $html .= htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $html .= '</pre>';

            $html .= '<div style="margin-top: 20px; text-align: center;">';
            $html .= '<a href="javascript:history.back()" style="padding: 10px 20px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px;">Volver al Sistema</a>';
            $html .= '</div>';

            $html .= '</div></body></html>';
            echo $html;
            Yii::$app->end();
        }

        // ========================
        // ENVÍO A API
        // ========================
        $API_URL = Yii::$app->params['API_NOTACREDITO_ENDPOINT'];
        $API_KEY = $confi->llave_api_token;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $API_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $API_KEY,
            ],
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_CONNECTTIMEOUT => 20,
        ]);

        try {
            $response = curl_exec($curl);
            $info     = curl_getinfo($curl);

            if ($response === false) {
                $curlError = curl_error($curl);
                curl_close($curl);
                throw new \Exception("Error cURL: " . $curlError);
            }

            curl_close($curl);

            if (!is_string($response)) {
                $response = json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            $data_envio = json_decode($response, true);

            Yii::info([
                'evento'         => 'RESPUESTA_API_NOTA_CREDITO',
                'http_code'      => $info['http_code'] ?? null,
                'respuesta_raw'  => $response,
                'respuesta_json' => $data_envio,
            ], 'api.nota_credito');

            if (!is_array($data_envio)) {
                throw new \Exception("La API no devolvió JSON válido. Respuesta: " . substr($response, 0, 300));
            }

            // Buscar CUDE en rutas comunes
            $cude = $data_envio['data']['fe']['cude']
                 ?? $data_envio['data']['cude']
                 ?? $data_envio['data']['document']['cude']
                 ?? $data_envio['cude']
                 ?? null;

            $qr = $data_envio['qrstr']
               ?? $data_envio['data']['qrstr']
               ?? $data_envio['QRStr']
               ?? null;

            if ($qr) $nota->qrstr = $qr;

            if (!empty($cude)) {
                $nota->cude = $cude;
                $nota->fecha_recepcion_dian = $data_envio['data']['sentDetail']['response']['send_email_date_time']
                                           ?? $data_envio['data']['sentDetail']['response']['datetime']
                                           ?? date("Y-m-d H:i:s");
                $nota->fecha_envio_api = date("Y-m-d H:i:s");
                $nota->save(false);

                $mensaje_exito = "La Nota Crédito No. {$numero_nc} fue enviada exitosamente a la DIAN. ";
                $mensaje_exito .= "Tipo: {$tipo_devolucion}. ";
                $mensaje_exito .= "Monto devuelto: $" . number_format($tax_inclusive_amount, 2) . " ";

                if ($tipo_devolucion === 'PARCIAL') {
                    $mensaje_exito .= "({$qty_devuelta} unidades @ $" . number_format($precio_unitario, 2) . ")";
                } else {
                    $mensaje_exito .= "(Anulación total de la factura)";
                }

                Yii::$app->getSession()->setFlash('success', $mensaje_exito);
                return $this->redirect(['notacredito/view', 'id' => $id]);
            }

            // Sin CUDE: leer mensaje de error
            $mensajeError = $data_envio['message']
                         ?? $data_envio['error']
                         ?? $data_envio['errors']
                         ?? $data_envio['data']['errors']
                         ?? $data_envio['data']['message']
                         ?? null;

            if (is_array($mensajeError)) {
                $mensajeError = json_encode($mensajeError, JSON_UNESCAPED_UNICODE);
            } else {
                $mensajeError = (string)($mensajeError ?? '');
            }

            Yii::warning([
                'evento'            => 'RESPUESTA_SIN_CUDE',
                'http_code'         => $info['http_code'] ?? null,
                'respuesta_json'    => $data_envio,
                'mensaje_detectado' => $mensajeError,
            ], 'api.nota_credito');

            Yii::$app->getSession()->setFlash(
                'warning',
                "La API respondió pero no entregó CUDE. " . 
                ($mensajeError ? "Detalle: {$mensajeError}" : "Revisa runtime/logs/app.log (categoría api.nota_credito).")
            );

            return $this->redirect(['notacredito/view', 'id' => $id]);

        } catch (\Exception $e) {

            Yii::error([
                'evento' => 'ERROR_ENVIO_NOTA_CREDITO',
                'error'  => $e->getMessage(),
                'trace'  => $e->getTraceAsString(),
            ], 'api.nota_credito');

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
