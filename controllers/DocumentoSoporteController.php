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
        $model = new DocumentoSoporte();
        $Acceso = 0;
        $conCompra = Compra::find()->orderBy('id_compra DESC')->all();
        $resolucion = \app\models\Resolucion::find()->where(['=','activo', 0])->andWhere(['=','id_documento', 2])->one();
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
    public function actionEnviar_documento_soporte_dian($id) {
        //VECTORES
       $documento = DocumentoSoporte::findOne($id);
       $documento_detalle = \app\models\DocumentoSoporteDetalle::find()->where(['=','id_documento_soporte', $id])->one();
       $proveedor = Proveedor::findOne($documento->idproveedor);
       $resolucion = \app\models\Resolucion::find()->where(['=','idresolucion', $documento->idresolucion])->andWhere(['=','activo', 0])->one();
       //ASIGNACIONES
       $documento_proveedor = $proveedor->cedulanit;
       $tipo_documento = $proveedor->tipo->codigo_api;
       $nombres = $proveedor->nombrecorto;
       $direccion_proveedor = $proveedor->direccionproveedor;
       $telefono = $proveedor->telefonoproveedor;
       $email_proveedor = $proveedor->emailproveedor;
       $ciudad = $proveedor->municipio->municipio;
       $documento_compra = $documento->documento_compra;
       $forma_pago = $documento->formaPago->codigo_api_ds;
       $observacion = $documento->observacion;
       $resolucion = $resolucion->codigo_interfaz;
       $consecutivo = $documento->numero_soporte;
       
       //valida la informacion
       if($email_proveedor === '' || $direccion_proveedor === ''){
            Yii::$app->getSession()->setFlash('error', 'Los campos DIRECCION  Y EMAIL no pueden ser vacios.'); 
            return $this->redirect(['view', 'id' => $id]);
       }else{
       
            //datos de retencion
            if($documento_detalle->porcentaje_retencion == 6){
               $codigo_cuenta =  '6068315';
            }else{
                if($documento_detalle->porcentaje_retencion == 4){
                     $codigo_cuenta =  '6068314';
                }else{
                    $codigo_cuenta =  '';
                }     
            }
            //DATOS DEL DETALLE 
            $cantidad = $documento_detalle->cantidad;
            $valor_unitario = $documento_detalle->valor_unitario;
            $codigo_concepto = $documento_detalle->concepto->codigo_interface;                

            // Configurar cURL
             $curl = curl_init();
           //  $API_KEY = Yii::$app->params['API_KEY_DESARROLLO']; //ley de desarrollo
             $API_KEY = Yii::$app->params['API_KEY_PRODUCCION']; //Key de produccion
             $dataHead = json_encode([
                 "client" => [
                     "document" => "$documento_proveedor",
                     "document_type" => "$tipo_documento",
                     "first_name" => "$nombres",
                     "last_name_one" => ".",
                     "last_name_two" => ".", 
                     "address" => "$direccion_proveedor",
                     "phone" => "$telefono",
                     "email" => "$email_proveedor",
                     "city" => "$ciudad",
                     "cuenta_compra" => "$codigo_cuenta"
                 ],
                 "billProvider" => "$documento_compra",
                  "warehouse" => 1,
                 "conceptId" => "$resolucion",
                 "formaPago" => "$forma_pago",
                 "observacion" => "$observacion",
             ]);

           $dataBody = json_encode([
                 [
                     "product" => "$codigo_concepto",
                     "qty" => "$cantidad",
                     "discount" => "0",
                     "cost" => "$valor_unitario",
                 ]
             ]);

             //ENVIA LA INFORMACION
             curl_setopt_array($curl, [
                 CURLOPT_URL => "http://begranda.com/equilibrium2/public/api/load-inventory?key=$API_KEY",
                 CURLOPT_RETURNTRANSFER => true,
                 CURLOPT_CUSTOMREQUEST => 'POST',
                 CURLOPT_POSTFIELDS => [
                     "head" => $dataHead,
                     "body" => $dataBody
                 ],
             ]);

             // RECUPERA EL RESPONSE
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
                if (isset($data['data']['dse']['cude'])) {
                    $cuds = $data['data']['dse']['cude'];
                    $documento->cuds = $cuds;
                    $fechaRecepcion = isset($data["data"]["sentDetail"]["response"]["send_email_date_time"]) && !empty($data["data"]["sentDetail"]["response"]["send_email_date_time"]) ? $data["data"]["sentDetail"]["response"]["send_email_date_time"] : date("Y-m-d H:i:s");
                    $documento->fecha_recepcion_dian = $fechaRecepcion;
                    $documento->fecha_envio_api = date("Y-m-d H:i:s");
                    $qrstr = $data['data']['dse']['sentDetail']['response']['QRStr'];
                    $documento->qrstr = $qrstr;
                    $documento->save(false);
                    Yii::$app->getSession()->setFlash('success', "El documento de soporte No ($consecutivo) se envió con éxito a la DIAN.");
                     return $this->redirect(['view','id' => $id]);
                } else {
                       Yii::$app->getSession()->setFlash('error', "No se genero el CUDE del este documento");
                       return $this->redirect(['view','id' => $id]);
                }
                
             } catch (Exception $e) {
                  Yii::$app->getSession()->setFlash('error', 'Error al enviar el DOCUMENTO DE SOPORTE: ' . $e->getMessage());
             }
            return $this->redirect(['view','id' => $id]);
       }//fin condicional    

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
