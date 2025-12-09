<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\filters\AccessControl;
//model
use app\models\OrdenProduccionInsumos;
use app\models\OrdenProduccionInsumosSearch;
use app\models\UsuarioDetalle;


/**
 * OrdenProduccionInsumosController implements the CRUD actions for OrdenProduccionInsumos model.
 */
class OrdenProduccionInsumosController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
       // Usamos ArrayHelper::merge para facilitar la combinación de configuraciones
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
     * Lists all OrdenProduccionInsumos models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',156])->all()){
                $form = new \app\models\FormFiltroOrdenInsumo();
                $op_cliente = null;
                $op_interna = null;
                $hasta = null;
                $desde = null; $numero_orden = null;
                $tipo_orden = null;
                $referencia = null;
                $tiposOrden = \yii\helpers\ArrayHelper::map(\app\models\Ordenproducciontipo::find()->all(), 'idtipo', 'tipo');
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $op_cliente = Html::encode($form->op_cliente);
                        $op_interna = Html::encode($form->op_interna);
                        $desde = Html::encode($form->desde);
                        $hasta = Html::encode($form->hasta);
                        echo $referencia = Html::encode($form->referencia);
                        $numero_orden = Html::encode($form->numero_orden);
                        $tipo_orden = Html::encode($form->tipo_orden);
                        $table = OrdenProduccionInsumos::find()
                                ->andFilterWhere(['=', 'orden_produccion_cliente', $op_cliente])                                                                                              
                                ->andFilterWhere(['=', 'idordenproduccion', $op_interna])
                                ->andFilterWhere(['=','idtipo', $tipo_orden])
                                ->andFilterWhere(['=','numero_orden', $numero_orden])
                                ->andFilterWhere(['=','codigo_producto', $referencia])
                                ->andFilterWhere(['between','fecha_creada', $desde, $hasta]);
 
                        $table = $table->orderBy('id_entrega DESC');
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
                $table = OrdenProduccionInsumos::find()
                        ->orderBy('id_entrega DESC');
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
                        'tiposOrden' => $tiposOrden,
            ]);
        }else{
             return $this->redirect(['site/sinpermiso']);
        }     
        }else{
           return $this->redirect(['site/login']);
        }
   }

    /**
     * Displays a single OrdenProduccionInsumos model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $listado = \app\models\OrdenProduccionInsumoDetalle::find()->where(['=','id_entrega', $id])->orderBy('iddetalleorden')->all();
        $tallas = \app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $model->idordenproduccion])->all();
        //PROCESO QUE ELIMINA LOS DETALLES DE INSUMOS
        if (Yii::$app->request->post()) {
            if(isset($_POST["eliminar_todo"])){
                if(isset($_POST["seleccion"])){
                    $intIndice = 0;
                    foreach ($_POST["seleccion"] as $intCodigo):
                        $table = \app\models\OrdenProduccionInsumoDetalle::findOne ($intCodigo);
                        if($table){
                            $table->delete();
                            $intIndice++;
                        }else{
                            $intIndice++;
                        }
                    endforeach;
                     return $this->redirect(['orden-produccion-insumos/view','id' => $id]);
                }else{
                    Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro de los insumos.');
                    return $this->redirect(['orden-produccion-insumos/view','id' => $id]);
                }
            } 
        }
        if (Yii::$app->request->post()) {
            if(isset($_POST["actualizar_cantidades"])){
                // --- 1. PROCESAR UNIDADES (Si existe el array unidades[]) ---
                if(isset($_POST["unidades"])){ 
                    // La clave ($intCodigo) es el ID del detalle. El valor ($unidades) es la cantidad ingresada.
                    foreach ($_POST["unidades"] as $intCodigo => $unidades):
                        $listado = \app\models\OrdenProduccionInsumoDetalle::findOne($intCodigo);
                        if($listado !== null && $unidades > 0){
                            $listado->unidades = (int) $unidades; // Asignar la cantidad directa
                            $listado->save();
                        }
                    endforeach;
                }

                // --- 2. PROCESAR METROS (Si existe el array metros[]) ---
                if(isset($_POST["metros"])){ 
                    // La clave ($intCodigo) es el ID del detalle. El valor ($metros) es la cantidad ingresada.
                    foreach ($_POST["metros"] as $intCodigo => $metros):
                        $listado = \app\models\OrdenProduccionInsumoDetalle::findOne($intCodigo);
                      
                        if($listado !== null && $metros > 0){
                            // Lógica de actualización para METROS
                            $listado->metros = (float) $metros; // Asignar la cantidad directa
                            $listado->save();
                        }
                    endforeach;
                }
                $this->ActualizaCantidades($id);
                $this->TotalCostoInsumos($id);
                 return $this->redirect(['orden-produccion-insumos/view','id' => $id]);
            }
        }    
        return $this->render('view', [
           'model' => $model,
            'listado' => $listado,
            'tallas' => $tallas,
        ]);
    }
   
    protected function ActualizaCantidades($id) {
        $detalle = \app\models\OrdenProduccionInsumoDetalle::find()->where(['id_entrega' => $id])->all();
        $total = 0;
        foreach ($detalle as $val) {
            if($val->unidades > 0){
                $total = $val->cantidad * $val->unidades;
                $val->cantidad_despachada = $total;
                $val->total_insumo = $total * $val->valor_unidad;
                $val->save();
            }
            //proceso para metros
            if($val->metros > 0){
                $total = round($val->cantidad * $val->metros);
                $val->cantidad_despachada = $total;
                $val->total_insumo = $total * $val->valor_unidad;
                $val->save();
            }
            
        }
    }
   
    //PEMRITE VER EL LISTADO DE INSUMOS
    public function actionVer_insumos($id, $tipo_orden, $tallas, $referencia) {
        
        $model =\app\models\ReferenciaInsumos::find()->where(['=','idtipo', $tipo_orden])->andWhere(['=','codigo', $referencia])->orderBy('id_insumos ASC')->all();
        if (Yii::$app->request->post()) {
            if (isset($_POST["enviar_insumos_orden"])) {
                if (isset($_POST["listado_insumos"])) {
                    $intIndice = 0; $stock = 0;
                    foreach ($_POST["listado_insumos"] as $intCodigo):
                       $detalleTallas = \app\models\Ordenproducciondetalle::findOne($tallas);
                       $insumos = \app\models\ReferenciaInsumos::findOne($intCodigo);
                        $inventario = \app\models\Insumos::find()->where(['=','id_insumos', $insumos->id_insumos])->one();
                       $BusquedaItem = \app\models\OrdenProduccionInsumoDetalle::find()->where(['=','id_insumos', $insumos->id_insumos])->andWhere(['=','iddetalleorden', $tallas])->one();
                       if(!$BusquedaItem){
                            $table = new \app\models\OrdenProduccionInsumoDetalle();
                            $table->id_entrega = $id;
                            $table->id_detalle = $intCodigo;
                            $table->id_insumos = $insumos->id_insumos;
                            $table->cantidad = $detalleTallas->cantidad;
                            if($detalleTallas->cantidad > $inventario->stock_real){
                                $table->faltan_insumos = 1;
                                $table->cantidad_faltante = $inventario->stock_real - $detalleTallas->cantidad;
                            }
                            if($insumos->total_unidades > 0){
                                $table->unidades = $insumos->total_unidades;
                                $table->metros = $insumos->total_unidades * $detalleTallas->cantidad;
                            }
                            $table->iddetalleorden = $tallas;
                            $table->save(false);
                            $intIndice++;
                       }else{
                          $intIndice++; 
                       }    
                    endforeach;
                    return $this->redirect(['orden-produccion-insumos/view','id' => $id]);
                }else{
                    Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro.');
                    return $this->redirect(['orden-produccion-insumos/view','id' => $id]);
                }     
           }
        }
        if(count($model) > 0 ){
            return $this->renderAjax('ver_insumos', [
                'id' => $id,
                'model' => $model,
                'tallas' => $tallas,
            ]); 
        }else{
            Yii::$app->getSession()->setFlash('error', 'Esta referencia NO tiene configurado los insumos para este proceso. Valide la informacion.');
            return $this->redirect(['orden-produccion-insumos/view','id' => $id]);
        }    
    }
    
   
    //AGREGAR LOS INSUMOS A LAS TALLAS DE LA OP
    public function actionCrear_insumos_tallas($id,$id_orden)
    {
        $form = new \app\models\FormMaquinaBuscar();
        $q = null;
        if ($form->load(Yii::$app->request->get())) {
           if ($form->validate()) {
                $q = Html::encode($form->q);   
                $referencia = \app\models\Insumos::find()
                            ->where(['like','descripcion',$q])
                            ->orwhere(['=','codigo_insumo',$q])
                            ->andWhere(['>','stock_real', 0])
                            ->andWhere(['=','id_grupo', 1]);
                $referencia = $referencia->orderBy('descripcion ASC');                    
                $count = clone $referencia;
                $to = $count->count();
                $pages = new Pagination([
                    'pageSize' => 10,
                    'totalCount' => $count->count()
                ]);
                $referencia = $referencia
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();  
           }else{
               $form->getErrors();
           }    
        }else{
            $referencia = \app\models\Insumos::find()->where(['=','id_grupo', 1])->andwhere(['>','stock_real', 0])->orderBy('descripcion ASC');
            $count = clone $referencia;
            $pages = new Pagination([
                'pageSize' => 10,
                'totalCount' => $count->count(),
            ]);
            $referencia = $referencia
                    ->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();
        }
         if (isset($_POST["enviar_insumos"])) {
            if(isset($_POST["listado_insumos"])){
                $intIndice = 0;
                foreach ($_POST["listado_insumos"] as $intCodigo) {
                    $insumo = \app\models\Insumos::findOne($intCodigo);
                    $tallas = \app\models\Ordenproducciondetalle::find()->where(['idordenproduccion' => $id_orden])->all();
                    foreach ($tallas as $val) {
                        $total_unidades = 0;
                        foreach ($tallas as $datos){
                            $total_unidades += $datos->cantidad;
                        }
                        if($total_unidades <= $insumo->stock_real){
                            $registroBuscado = \app\models\OrdenProduccionInsumoDetalle::find()->where(['id_entrega' =>  $id,
                                                                                                'id_insumos' => $insumo->id_insumos,
                                                                                                'iddetalleorden' => $val->iddetalleorden])->one();
                            if(!$registroBuscado){
                                $table = new \app\models\OrdenProduccionInsumoDetalle();
                                $table->id_entrega = $id;
                                $table->id_insumos = $intCodigo;
                                $table->iddetalleorden = $val->iddetalleorden;
                                $table->cantidad = $val->cantidad;
                                $table->cantidad_despachada = $val->cantidad;
                                $table->valor_unidad = $insumo->precio_unitario;
                                $table->total_insumo = $val->cantidad * $insumo->precio_unitario;
                                $table->save();
                                Yii::$app->getSession()->setFlash('success', 'Registro guardado exitosamente.');
                            }    
                        }else{
                             Yii::$app->getSession()->setFlash('warning', 'El insumo: '. $insumo->descripcion .' No cumple con las existencias requeridas para el despacho. Valide el stock de insumos.');
                        }
                    }
                        
                    $intIndice++;
                }
                return $this->redirect(["orden-produccion-insumos/view", 'id' => $id]);
            }else{
                Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro de los insumos.');
                return $this->redirect(["orden-produccion-insumos/crear_insumos_tallas", 'id' => $id, 'id_orden' => $id_orden]);
            }
        }
        return $this->render('_form_nuevo_insumo', [
            'pagination' => $pages,
            'id' => $id,
            'form' => $form,
            'id_orden' => $id_orden,
            'referencia' => $referencia,
        
        ]);
    }
    
   
    //EDITAR INSUMOS
     public function actionEditar_linea_insumo($id, $id_detalle) {
        $model = new \app\models\FormEditarInsumo();
        $table = \app\models\OrdenProduccionInsumoDetalle::findOne($id_detalle);
        $total_metro = 1829; $dato = 0;
        if ($model->load(Yii::$app->request->post())) {
            if (isset($_POST["actualizarItem"])) { 
                if($model->numero > 0){
                    if($model->convertir == 0){
                        $table->metros = $model->cantidad * $model->numero;
                        $table->unidades = $model->numero;
                    }else{
                        $dato = $total_metro / $model->numero;
                        $table->unidades = ''.number_format(((1 * $model->cantidad)/$dato),0);
                        $table->metros = $table->unidades;
                    }
                    $table->save(false);
                    return $this->redirect(['orden-produccion-insumos/view','id' => $id]);
                }    
            }    
        }
         if (Yii::$app->request->get($id, $id_detalle)) {
            $model->cantidad = $table->cantidad;
         }
        return $this->renderAjax('editar_insumo_orden', [
            'model' => $model,
            'id' => $id,
            'table' => $table,
        ]);
       
    }
    
    //AUTORIZAR PROCESO Y DESAUTORIZAR
    public function actionAutorizado($id) {
       $orden = OrdenProduccionInsumos::findOne($id);
       $detalle_orden = \app\models\OrdenProduccionInsumoDetalle::find()->where(['=','id_entrega', $id])->all();
       foreach ($detalle_orden as $detalle) {
           if($detalle->faltan_insumos == 1){
               Yii::$app->getSession()->setFlash('error','No tiene inventario suficiente para cubrir esta Orden. Favor valide la información con inventarios.');
               return $this->redirect(['orden-produccion-insumos/view','id' => $id]);
           }
       }
       if($orden->autorizado == 0){
           $orden->autorizado = 1;
           $orden->save();
           return $this->redirect(['orden-produccion-insumos/view','id' => $id]);
       }else{
           $orden->autorizado = 0;
           $orden->save();
           return $this->redirect(['orden-produccion-insumos/view','id' => $id]);
       }
    }
    //PERMITE CREAR LOS CONSECUTIVOS
    public function actionGenerar_consecutivo($id) {
        $orden = OrdenProduccionInsumos::findOne($id);
        $dato = \app\models\Consecutivo::findOne(24);
        $orden->numero_orden = $dato->consecutivo + 1;
        $orden->save();
        $dato->consecutivo = $orden->numero_orden;
        $dato->save();
        return $this->redirect(['orden-produccion-insumos/view','id' => $id]);
    }
    
    //PROCESO QUE TOTALIZA LA ORDEN
    protected function TotalCostoInsumos($id) {
        $orden = OrdenProduccionInsumos::findOne($id);
        $detalle = \app\models\OrdenProduccionInsumoDetalle::find()->where(['=','id_entrega', $id])->all();
        $total = 0; $costo = 0; $total_insumo = 0; $insumo = 0; 
        $configuacion_insumos = \app\models\ConfiguracionInsumos::findOne(1);
        if($configuacion_insumos->aplica_insumos_referencia == 1){
            foreach ($detalle as $dato) {
                if($dato->metros > 0){
                    $costo = $dato->insumos->precio_unitario * $dato->metros;
                    $insumo = $dato->metros;
                }else{
                    $costo = $dato->insumos->precio_unitario * $dato->cantidad;
                    $insumo = $dato->cantidad;
                }

                $total += $costo;
                $total_insumo += $insumo;
            }
            $orden->total_costo = $total;
            $orden->total_insumos = $total_insumo;
            $orden->save(false);
        }else{
            foreach ($detalle as $dato) {
                $costo += $dato->total_insumo;
                $insumo += $dato->cantidad_despachada;
            } 
            $orden->total_costo = $costo;
            $orden->total_insumos = $insumo;
            $orden->save();
        }    
    } 
    
    //DESCARGAR INVENTARIOS
    public function actionEnviar_insumos_modulo($id) {
       $ordenInsumo = OrdenProduccionInsumos::findOne($id);
       if($ordenInsumo->exportar_insumos == 0){
            $orden = \app\models\OrdenProduccionInsumoDetalle::find()->where(['=','id_entrega', $id])->orderBy('id_insumos ASC')->all(); 
            $configuracion_insumos = \app\models\ConfiguracionInsumos::findOne(1);
            $con = 0;
            if($configuracion_insumos->aplica_insumos_referencia == 1){
                foreach ($orden as $datos) {
                    $insumo = \app\models\Insumos::findOne($datos->id_insumos);
                    if($insumo){
                         if($datos->metros > 0){
                            $insumo->stock_real -= $datos->metros;
                         }else{
                            $insumo->stock_real -= $datos->cantidad;
                         }
                        $insumo->save(false);
                        $con++; 
                        $this->ActualizaSaldosInsumos($datos);
                    }
                }
            }else{
                foreach ($orden as $datos) {
                    $insumo = \app\models\Insumos::findOne($datos->id_insumos);
                    if($insumo){
                         $insumo->stock_real -= $datos->cantidad_despachada;
                    }
                    $insumo->save(false);
                    $con++; 
                    $this->ActualizaSaldosInsumos($datos);
                }
            }    
            $ordenInsumo->exportar_insumos = 1;
            $ordenInsumo->save();
            Yii::$app->getSession()->setFlash('success', 'Se enviaron (' .$con.') registros de insumos para al modulo de inventario.');
            return $this->redirect(['orden-produccion-insumos/view','id' => $id]);
       }else{
            Yii::$app->getSession()->setFlash('error','Esta orden de ('.$ordenInsumo->tipo->tipo.') ya se le exportaron los insumos.');
            return $this->redirect(['orden-produccion-insumos/view','id' => $id]); 
       }     
    }
    
    //PROCESO QUE TOTALIZA SALDOS
    protected function ActualizaSaldosInsumos($datos) {
        $insumo = \app\models\Insumos::findOne($datos->id_insumos);
        $subtotal = 0; $iva = 0; $total = 0;
        if($insumo->aplica_inventario == 1){
            if($insumo->aplica_iva == 1){
                $subtotal = $insumo->stock_real * $insumo->precio_unitario;
                $iva = round(($subtotal * $insumo->porcentaje_iva)/100);
                $total = $subtotal+ $iva;
                $insumo->subtotal = $subtotal;
                $insumo->total_iva = $iva;
                $insumo->total_materia_prima = $total; 
                $insumo->save(false);
            }
        }    
    }
    
    public function actionEliminar_insumos($id, $id_detalle) {
      $detalle = \app\models\OrdenProduccionInsumoDetalle::findOne($id_detalle);
      $detalle->delete();
      $this->DescargarValorUnidades($id);
      return $this->redirect(['orden-produccion-insumos/view','id' => $id]);
    }
    
    
    //PROCESO QUE DESCARGAR LAS UNIDADES CUANDO SE ELININA
    protected function DescargarValorUnidades($id) {
         $detalle = \app\models\OrdenProduccionInsumoDetalle::find()->where(['id_entrega' => $id])->all();
         $model = \app\models\OrdenProduccionInsumos::findOne($id);
         $cantidad = 0;
         $total = 0;
         foreach ($detalle as $val) {
             $cantidad += $val->cantidad_despachada;
             $total += $val->total_insumo;
         }
         $model->total_insumos = $cantidad;
         $model->total_costo = $total;
         $model->save();
                 
    }
    
    //REPORTES
    public function actionReporte_orden_insumo($id) {
        $model = OrdenProduccionInsumos::findOne($id);
        return $this->render('../formatos/reporte_orden_insumos', [
            'model' => $model,
            'id_entrega' => $id]);
    }
    
    
    /**
     * Finds the OrdenProduccionInsumos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrdenProduccionInsumos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrdenProduccionInsumos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    //PROCESO QUE EXPORTA A EXCEL TODAS LAS MERDIDAS DE LA OP
    
      public function actionExportar_excel($id) {
        $medida = \app\models\OrdenProduccionInsumoDetalle::find()->where(['=','id_entrega', $id])->orderBy('iddetalleorden')->all();
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
       
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'No ORDEN')
                    ->setCellValue('B1', 'OP CLIENTE')
                    ->setCellValue('C1', 'OP INTERNA')
                    ->setCellValue('D1', 'REFERENCIA)')
                    ->setCellValue('E1', 'CLIENTE')
                    ->setCellValue('F1', 'F. CREACION')
                    ->setCellValue('G1', 'CODIGO')
                    ->setCellValue('H1', 'NOMBRE INSUMO')
                    ->setCellValue('I1', 'TALLA')
                    ->setCellValue('J1', 'UNIDADES X TALLA')
                    ->setCellValue('K1', 'UNIDADES ENTREGADAS')
                    ->setCellValue('L1', 'UNIDADES X INSUMO')
                    ->setCellValue('M1', 'CONSUMO X METRO')
                    ->setCellValue('M1', 'TOTAL INSUMO');
                    
        $i = 3;
        foreach ($medida as $val) {
            if($val->iddetalleorden )
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->entrega->numero_orden)
                    ->setCellValue('B' . $i, $val->entrega->orden_produccion_cliente)
                    ->setCellValue('C' . $i, $val->entrega->idordenproduccion)
                    ->setCellValue('D' . $i, $val->entrega->codigo_producto)                    
                    ->setCellValue('E' . $i, $val->entrega->ordenproduccion->cliente->nombrecorto)                    
                    ->setCellValue('F' . $i, $val->entrega->fecha_creada)
                    ->setCellValue('G' . $i, $val->insumos->codigo_insumo)
                    ->setCellValue('H' . $i, $val->insumos->descripcion)
                    ->setCellValue('I' . $i, $val->ordenDetalle->productodetalle->prendatipo->talla->talla)
                    ->setCellValue('J' . $i, $val->cantidad)
                    ->setCellValue('K' . $i, $val->cantidad_despachada)
                    ->setCellValue('L' . $i, $val->unidades)
                    ->setCellValue('M' . $i, $val->metros)
                    ->setCellValue('M' . $i, $val->total_insumo);
                   $i++;
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Insumos.xlsx"');
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
