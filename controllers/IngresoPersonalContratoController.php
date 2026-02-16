<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\helpers\Html;

//models
use app\models\IngresoPersonalContrato;
use app\models\IngresoPersonalContratoDetalle;
use app\models\UsuarioDetalle;

/**
 * IngresoPersonalContratoController implements the CRUD actions for IngresoPersonalContrato model.
 */
class IngresoPersonalContratoController extends Controller
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
     * Lists all IngresoPersonalContrato models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',193])->all()){
                $form = new \app\models\FormFiltroIngresoPersonalContrato();
                $fecha_inicio = null;
                $fecha_corte = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $table = IngresoPersonalContrato::find()
                                ->where(['between','fecha_inicio', $fecha_inicio, $fecha_corte]);
                        $table = $table->orderBy('id_ingreso DESC');
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
                            
                } else {
                        $form->getErrors();
                }                    
            } else {
                $table = IngresoPersonalContrato::find()->orderBy('id_ingreso DESC');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 15,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
               
            }
            $to = $count->count();
            return $this->render('index', [
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
   
   //PERMITE BUSCAR LOS OTROS SI
   public function actionIndex_search() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',194])->all()){
                $form = new \app\models\FormFiltroIngresoPersonalContrato();
                $fecha_inicio = null;
                $fecha_corte = null;
                $id_empleado = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $id_empleado = Html::encode($form->id_empleado);
                        $table = \app\models\FormatoContratoObralabor::find()
                                ->andFilterWhere(['between','fecha_inicio_periodo', $fecha_inicio, $fecha_corte])
                                ->andFilterWhere(['=','id_empleado', $id_empleado]);
                        $table = $table->orderBy('id DESC');
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
                            
                } else {
                        $form->getErrors();
                }                    
            } else {
                $table = \app\models\FormatoContratoObralabor::find()->orderBy('id DESC');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 15,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
               
            }
            $to = $count->count();
            return $this->render('index_search', [
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
   
   /**
     * Displays a single IngresoPersonalContrato model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
      public function actionView($id, $fecha_inicio, $fecha_corte)
    {
        $form = new \app\models\FormFiltroConsultaAdicionPermanente();
        $id_empleado = null; 
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {   
                $id_empleado = Html::encode($form->id_empleado);
               
                $table = \app\models\IngresoPersonalContratoDetalle::find()
                                ->andFilterWhere(['=','id_empleado',$id_empleado])
                                ->andWhere(['=','id_ingreso',$id]);
                        $table = $table->orderBy('id DESC');
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
                           
               
           }else {
               $form->geterrores;
           }
        }else{
            $table = \app\models\IngresoPersonalContratoDetalle::find()->where(['=','id_ingreso', $id])
                                                       ->orderBy('id_empleado DESC');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 15,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                
        }   
        return $this->render('view', [
            'modelo' => $this->findModel($id),
            'model' => $model,
            'id' => $id,
            'form' => $form,
            'fecha_inicio' => $fecha_inicio,
            'fecha_corte' => $fecha_corte,
             'pagination' => $pages,
            
        ]);
    }
    
     //vista de las ingresos y deduciones
     public function actionVista($id, $id_detalle, $fecha_corte, $fecha_inicio) {
        $model = IngresoPersonalContratoDetalle::findOne($id_detalle);
         
         return $this->render('vista', [
            'model' => $model, 'id'=>$id,
             'fecha_corte' => $fecha_corte,
             'fecha_inicio' => $fecha_corte,
        ]);
    }  

    /**
     * Creates a new IngresoPersonalContrato model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new IngresoPersonalContrato();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->user_name = Yii::$app->user->identity->username;
            $model->fecha_hora_proceso = date('Y-m-d H:i:s');
            $model->save();
           return $this->redirect(['view', 'id' => $model->id_ingreso, 'fecha_inicio' =>$model->fecha_inicio, 'fecha_corte' => $model->fecha_corte]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    
    //crear ingresos al empleado
     public function actionCreateadicion($id, $fecha_corte, $fecha_inicio) {        
        $model = new IngresoPersonalContratoDetalle();        
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
            if ($model->validate()) {
                $sqlContrato = \app\models\Contrato::find()->where([
                                        'id_empleado' => $model->id_empleado,
                                        'contrato_activo' => 1])->one();
                if(!$sqlContrato){
                    Yii::$app->getSession()->setFlash('error', 'Este empleado no tiene contrato activo.');
                    return $this->redirect(["ingreso-personal-contrato/create_adicion", 'id' =>$id, 'fecha_corte' => $fecha_corte,'fecha_inicio' => $fecha_inicio]);
                }
                $table = new IngresoPersonalContratoDetalle();
                $table->id_empleado = $model->id_empleado;
                $table->id_ingreso = $id;
                $table->id_empleado = $model->id_empleado;
                $table->documento = $sqlContrato->identificacion;
                $table->id_contrato = $sqlContrato->id_contrato;
                $table->nombre_completo = $sqlContrato->empleado->nombrecorto;
                $table->valor_unitario = $model->valor_unitario;
                $table->operacion = $model->operacion;
                $table->cantidad = $model->cantidad;
                $table->total_pagar = round($model->valor_unitario * $model->cantidad);
                $table->fecha_inicio = $fecha_inicio;
                $table->total_dias = $model->total_dias;
                if ($table->save(false)) {
                    return $this->redirect(["ingreso-personal-contrato/view", 'id' =>$id, 'fecha_corte' => $fecha_corte,'fecha_inicio' => $fecha_inicio]);
                } else {
                    $msg = "error";
                }
            } else {
                $model->getErrors();
            }
        }
        return $this->render('_formadicion', ['model' => $model, 'id'=> $id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio]);
    }

    /**
     * Updates an existing IngresoPersonalContrato model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_ingreso]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    
     //permite modificar las adiciones y descuento de la tabla adicionpagopermanente
     public function actionUpdatevista($id, $id_detalle, $fecha_corte, $fecha_inicio)
    {
       $model = IngresoPersonalContratoDetalle::findOne($id_detalle);
       if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
      
        if ($model->load(Yii::$app->request->post())) { 
            $sqlContrato = \app\models\Contrato::find()->where([
                                        'id_empleado' => $model->id_empleado,
                                        'contrato_activo' => 1])->one();
            $table = IngresoPersonalContratoDetalle::find()->where(['id' => $id_detalle])->one();
            if(!$sqlContrato && !$table){
                Yii::$app->getSession()->setFlash('error', 'Este empleado no tiene contrato activo o el documento no existe para actualizar');
                return $this->redirect(["ingreso-personal-contrato/updatevista", 'id' =>$id, 'fecha_corte' => $fecha_corte,'fecha_inicio' => $fecha_inicio]);
            }
            
                    
            $table->id_empleado = $model->id_empleado;
            $table->documento = $sqlContrato->identificacion;
            $table->nombre_completo = $sqlContrato->empleado->nombrecorto;
            $table->id_contrato = $sqlContrato->id_contrato;
            $table->cantidad = $model->cantidad;
            $table->valor_unitario = $model->valor_unitario;
            $table->total_pagar = round($model->cantidad * $model->valor_unitario);
            $table->total_dias = $model->total_dias;
            $table->save(false);
            return $this->redirect(['view','id' =>$id, 'fecha_corte' => $fecha_corte,'fecha_inicio' => $fecha_inicio]); 
            
        }
       
        return $this->render('updatevista', [
            'model' => $model,
            'id'=>$id,
            'fecha_corte' => $fecha_corte,
            'fecha_inicio' => $fecha_inicio,
        ]);
    }

    /**
     * Deletes an existing IngresoPersonalContrato model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
  public function actionEliminar_adicional($id, $id_detalle, $fecha_inicio, $fecha_corte) 
    {  
        if (Yii::$app->request->post()) {
            if ((int) $id_detalle) {
                try {
                    
                    IngresoPersonalContratoDetalle::deleteAll("id=:id", [":id" => $id_detalle]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    return $this->redirect(["ingreso-personal-contrato/view",'id' => $id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]);
                } catch (IntegrityException $e) {
                     Yii::$app->getSession()->setFlash('error', 'Error al eliminar el registro porque esta asociado en otro proceso');
                       return $this->redirect(["ingreso-personal-contrato/view",'id' => $id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]);
                } catch (\Exception $e) {

                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el registro porque esta asociado en otro proceso.');
                      return $this->redirect(["ingreso-personal-contrato/view",'id' => $id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]);
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el registros, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("ingreso-personal-contrato/index") . "'>";
            }
        } else {
            return $this->redirect(["ingreso-personal-contrato/index"]);
        }
    }
    
    
  public function actionImportar_conceptos_excel($id, $fecha_inicio, $fecha_corte)
    {
    $model = new \yii\base\DynamicModel(['fileImport']);
    $model->addRule(['fileImport'], 'required')
          ->addRule(['fileImport'], 'file', ['extensions' => ['csv', 'txt'], 'checkExtensionByMimeType' => false]);

    if (Yii::$app->request->isPost) {
        $model->fileImport = \yii\web\UploadedFile::getInstance($model, 'fileImport');

        if ($model->validate()) {
            $path = $model->fileImport->tempName;
            $transaction = Yii::$app->db->beginTransaction();
            $filaActual = 1; // Empezamos en 1 por el encabezado
            $registrosGuardados = 0;
            $erroresFilas = [];

            try {
                $handle = fopen($path, "r");
                if ($handle === false) throw new \Exception("No se pudo abrir el archivo.");

                // Saltar encabezado
                fgetcsv($handle, 1000, ";");

                while (($datos = fgetcsv($handle, 1000, ";")) !== false) {
                     $filaActual++;

                    if (empty($datos) || !isset($datos[0]) || trim($datos[0]) == '') continue;

                    // 1. Limpieza de datos: Lo mantenemos como STRING para cumplir la regla del modelo
                    $documento_excel = (string)preg_replace('/[^0-9]/', '', $datos[0]);

                    // Limpiamos los demás datos
                    $operacion_excel = preg_replace('/[^a-zA-Z0-9 ]/', '', trim($datos[2] ?? ''));
                    $cantidad_excel = (float)str_replace(',', '.', $datos[3] ?? '0');
                    $valor_unitario_excel = (float)str_replace(',', '.', $datos[4] ?? '0');
                    $total_dias_excel = (float)str_replace(',', '.', $datos[5] ?? '0');

                    if (strlen($documento_excel) === 0) {
                        $erroresFilas[] = "Fila $filaActual: Documento vacío o inválido.";
                        continue;
                    }

                    // 2. Buscar Contrato (Yii manejará la conversión de tipo en la consulta SQL automáticamente)
                    $contrato = \app\models\Contrato::find()
                        ->where(['identificacion' => $documento_excel, 'contrato_activo' => 1])
                        ->one();

                    if (!$contrato) {
                        $erroresFilas[] = "Fila $filaActual: No se halló contrato activo para la identificación $documento_excel.";
                        continue;
                    }

                    // 3. Validar si ya existe este detalle para evitar duplicados
                    $existe = IngresoPersonalContratoDetalle::find()->where([
                        'id_empleado'  => $contrato->id_empleado,
                        'id_contrato'  => $contrato->id_contrato,
                        'fecha_inicio' => $fecha_inicio,
                        'operacion'    => $operacion_excel
                    ])->exists();

                    if ($existe) continue;

                    // 4. Guardar Registro
                    $pago = new IngresoPersonalContratoDetalle();
                    $pago->id_ingreso      = $id;
                    $pago->id_empleado     = $contrato->id_empleado;
                    $pago->id_contrato     = $contrato->id_contrato;
                    $pago->documento       = $documento_excel;
                    $pago->nombre_completo = $contrato->empleado->nombrecorto ?? 'SIN NOMBRE';
                    $pago->operacion       = strtoupper($operacion_excel);
                    $pago->cantidad        = $cantidad_excel;
                    $pago->valor_unitario  = $valor_unitario_excel;
                    $pago->total_pagar     = round($cantidad_excel * $valor_unitario_excel);
                    $pago->fecha_inicio    = $fecha_inicio;
                    $pago->total_dias      = $total_dias_excel;
                    if ($pago->save()) {
                        $registrosGuardados++;
                    } else {
                        $erroresFilas[] = "Fila $filaActual: " . implode(", ", $pago->getErrorSummary(true));
                    }
                }
                fclose($handle);

                if ($registrosGuardados > 0) {
                    $transaction->commit();
                    $msg = "Se importaron $registrosGuardados registros.";
                    if (!empty($erroresFilas)) $msg .= " (Algunas filas fallaron).";
                    Yii::$app->session->setFlash('success', $msg);
                } else {
                    $transaction->rollBack();
                    $detalleErrores = !empty($erroresFilas) ? "<br>Detalles: " . implode(" | ", array_slice($erroresFilas, 0, 3)) : "";
                    Yii::$app->session->setFlash('warning', "No se procesaron datos. Verifique identificación y contratos activos." . $detalleErrores);
                }

                return $this->redirect(['view', 'id' => $id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio]);

            } catch (\Exception $e) {
                if ($transaction->isActive) $transaction->rollBack();
                Yii::$app->session->setFlash('error', "Error crítico: " . $e->getMessage());
            }
        }
    }
        return $this->render('subir_archivo_excel', ['model' => $model, 'id' => $id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio]);
 }
 
  //cerrar el proceso de ingresos y deduciones
    public function actionProceso_cerrado($id) {
        $model = $this->findModel($id);
        if(IngresoPersonalContrato::find()->where(['=','id_ingreso', $id])->one()){
            $sqlPersonal = IngresoPersonalContratoDetalle::find()->where(['id_ingreso' => $id])->sum('total_pagar');
            $model->estado_proceso = 1;
            $model->total_pagar = $sqlPersonal;
            $model->save();
            return $this->redirect(["ingreso-personal-contrato/index"]);
        }else{
           Yii::$app->getSession()->setFlash('error', 'Debe de ingresar los registros al proceso.');
           return $this->redirect(['ingreso-personal-contrato/index']);
        }    
    }

    //CREA CONTRATO OTRO SI EN FORMATO PDF
    public function actionCrear_otro_contrato($id, $fecha_inicio, $fecha_corte, $id_empleado, $id_detalle) {
        
        $SqlDetalle = IngresoPersonalContratoDetalle::findOne($id_detalle);
        
        $conDetalle = \app\models\FormatoContratoObralabor::find()->where([
                                                            'id_contrato' => $SqlDetalle->id_contrato,
                                                            'id_empleado' => $SqlDetalle->id_empleado,
                                                            'id_ingreso' => $id])->one();
        if($conDetalle){
            Yii::$app->session->setFlash('error', "Ya se creo el OTRO SI  al empleado " .$SqlDetalle->nombre_completo ." exitosamente.");
            return $this->redirect(['view','id' => $id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]);
        }
        
        $sqlDetalleContrato = IngresoPersonalContratoDetalle::find()->where([
                                                                    'id_ingreso' => $id,
                                                                    'id_empleado' => $id_empleado,
                                                                    'fecha_inicio' => $fecha_inicio])->sum('total_pagar'); //busca todas las operaciones
        
        if($SqlDetalle){
            $table = new \app\models\FormatoContratoObralabor();
            $table->id_contrato = $SqlDetalle->id_contrato;
            $table->id_empleado = $SqlDetalle->id_empleado;
            $table->id_formato_contenido = 9; //queda quemado porque es de una tabla de configuracion
            //calcular la fecha final
            $fecha = date($fecha_inicio);
            $nueva_fecha = strtotime ( '+'.$SqlDetalle->total_dias.' day' , strtotime ( $fecha )-1 ) ;
            $nueva_fecha = date ( 'Y-m-d' , $nueva_fecha );
            $table->fecha_inicio_periodo = $fecha_inicio;
            $table->fecha_corte_labor = $nueva_fecha;
            $table->fecha_corte_periodo = $fecha_corte;
            $table->dias_trabajo = $SqlDetalle->total_dias;
            $table->id_ingreso = $id;
            $table->fecha_hora_creacion = date('Y-m-d H:i:s');
            $table->user_name = Yii::$app->user->identity->username;
            $table->total_pagar = $sqlDetalleContrato;
            $SqlDetalle->save();
            if($table->save()){
                Yii::$app->session->setFlash('success', "Se creo el OTRO SI  al empleado " .$SqlDetalle->nombre_completo ." exitosamente.");
            }else{
                 Yii::$app->session->setFlash('error', "El registro no se guardó. Valide la información.");
            }    
            return $this->redirect(['view','id' => $id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]);   
        }else{
            return $this->redirect(['view','id' => $id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]);
        }    
    }
    
    
    //IMPRESIONES
    public function actionImprimir_otrosi($codigo)
    {
        $model = \app\models\FormatoContratoObralabor::findOne($codigo);
        return $this->render('../formatos/reporte_contrato_otrosi_labor', [
             'model' => $model,
            
        ]);
    }
    
    /**
     * Finds the IngresoPersonalContrato model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IngresoPersonalContrato the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = IngresoPersonalContrato::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    //excelles
     public function actionExportar_registros($id) {      
        
        $detalle = \app\models\IngresoPersonalContratoDetalle::find()->where(['=','id_ingreso', $id])->orderBy('id_empleado DESC')->all();
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
       
                               
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'DOCUMENTO')
                    ->setCellValue('C1', 'EMPLEADO')
                    ->setCellValue('D1', 'NOMBRE DE LA OPERACION')
                    ->setCellValue('E1', 'FECHA INICIO')                    
                    ->setCellValue('F1', 'FECHA CORTE')
                    ->setCellValue('G1', 'FECHA HORA PROCESO')
                    ->setCellValue('H1', 'CANTIDAD')
                    ->setCellValue('I1', 'VALOR UNITARIO')
                    ->setCellValue('J1', 'VALOR PAGADO')
                    ->setCellValue('K1', 'USER NAME');
                 
        $i = 2;
        
        foreach ($detalle as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id)
                    ->setCellValue('B' . $i, $val->empleado->identificacion)
                    ->setCellValue('C' . $i, $val->empleado->nombrecorto)
                    ->setCellValue('D' . $i, $val->operacion)
                    ->setCellValue('E' . $i, $val->ingreso->fecha_inicio)
                    ->setCellValue('F' . $i, $val->ingreso->fecha_corte)                    
                    ->setCellValue('G' . $i, $val->ingreso->fecha_hora_proceso)
                    ->setCellValue('H' . $i, $val->cantidad)
                    ->setCellValue('I' . $i, $val->valor_unitario)
                    ->setCellValue('J' . $i, $val->total_pagar)
                    ->setCellValue('K' . $i, $val->ingreso->user_name);
                  
                   
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="contrato_personal.xlsx"');
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
