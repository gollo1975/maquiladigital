<?php

namespace app\controllers;

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
//MODELS
use app\models\AsignacionProducto;
use app\models\AsignacionProductoSearch;
use app\models\UsuarioDetalle;
use app\models\FormFiltroAsignacionProducto;
use app\models\CostoProducto;
use app\models\AsignacionProductoDetalle;
use app\models\Consecutivo;

/**
 * AsignacionProductoController implements the CRUD actions for AsignacionProducto model.
 */
class AsignacionProductoController extends Controller
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
     * Lists all AsignacionProducto models.
     * @return mixed
     */
     public function actionIndex($token = 0) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',123])->all()){
                $form = new FormFiltroAsignacionProducto();
                $fecha_asignacion = null;
                $fecha_corte = null;
                $proveedor = null;
                $documento = null;
                $tipoOrden = null;
                $orden_produccion = null;
                $autorizado = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $proveedor = Html::encode($form->proveedor);
                        $fecha_asignacion = Html::encode($form->fecha_asignacion);
                        $documento = Html::encode($form->documento);
                        $tipoOrden = Html::encode($form->tipoOrden);
                        $orden_produccion = Html::encode($form->orden_produccion);
                        $autorizado = Html::encode($form->autorizado);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $table = AsignacionProducto::find()
                                ->andFilterWhere(['=', 'idproveedor', $proveedor])
                                ->andFilterWhere(['>=', 'fecha_asignacion', $fecha_asignacion])
                                ->andFilterWhere(['<=', 'fecha_asignacion', $fecha_corte])   
                                ->andFilterWhere(['=', 'documento', $documento])  
                                ->andFilterWhere(['=', 'orden_produccion', $orden_produccion])
                                ->andFilterWhere(['=', 'idtipo', $tipoOrden])
                                ->andFilterWhere(['=', 'autorizado', $autorizado]);  
                       $table = $table->orderBy('id_asignacion DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 40,
                            'totalCount' => $count->count()
                        ]);
                        $model = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                            if(isset($_POST['excel'])){                            
                                $check = isset($_REQUEST['id_asignacion DESC']);
                                $this->actionExcelconsultaAsignacion($tableexcel);
                            }
                } else {
                        $form->getErrors();
                }                    
            } else {
                $table = AsignacionProducto::find()
                        ->orderBy('id_asignacion DESC');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 40,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                    $this->actionExcelconsultaAsignacion($tableexcel);
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
     * Displays a single AsignacionProducto model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token)
    {
        
        $detalle_orden = AsignacionProductoDetalle::find()->where(['=','id_asignacion', $id])->orderBy('idtalla desc')->all();
         if (Yii::$app->request->post()) {
            if (isset($_POST["eliminardetalle"])) {
                if (isset($_POST["detalle"])) {
                    foreach ($_POST["detalle"] as $intCodigo) {
                        try {
                            $eliminar = AsignacionProductoDetalle::findOne($intCodigo);
                            $eliminar->delete();
                            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                            $this->ActualizarCantidades($id);
                            $this->redirect(["asignacion-producto/view", 'id' => $id, 'token' => $token]);
                        } catch (IntegrityException $e) {
                          
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                        } catch (\Exception $e) {
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');

                        }
                    }
                } else {
                    Yii::$app->getSession()->setFlash('warning', 'Debe seleccionar al menos un registro.');
                }    
             }
        }    
        if(count($detalle_orden) > 0){
            return $this->render('view', [
                'model' => $this->findModel($id),
                'detalle_orden' => $detalle_orden,
                'token' => $token,
            ]);
        }else{
            Yii::$app->getSession()->setFlash('warning', 'No se le ha asignado referencias a este proveedor.');
             return $this->redirect(['index']);
        }
    }    

    /**
     * Creates a new AsignacionProducto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AsignacionProducto();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $provedor = \app\models\Proveedor::findOne($model->idproveedor);
            $model->documento = $provedor->cedulanit;
            $model->razon_social = $provedor->nombrecorto;
            $model->usuario = Yii::$app->user->identity->username;
            $model->save(false);
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AsignacionProducto model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $provedor = \app\models\Proveedor::findOne($model->idproveedor);
            $model->documento = $provedor->cedulanit;
            $model->razon_social = $provedor->nombrecorto;
            $model->usuario_editado = Yii::$app->user->identity->username;
            $model->fecha_editado = date('Y-m-d');
            $model->save(false);
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    //PERMITE BUSCAR LOS PRODUCTOS PARA ASIGNAR
    
       public function actionBuscarproducto($id, $token)
       {
      $productos = \app\models\OrdenFabricacion::find()->where(['=','asignado_taller', 0])->orderBy('fecha_fabricacion ASC')->all();
       if (Yii::$app->request->post()) {  
            if (isset($_POST["productoasignado"])) { 
                $asignacion = AsignacionProducto::findOne($id);
                $empresa = \app\models\Matriculaempresa::findOne(1);
                foreach ($_POST["id_orden"] as $intCodigo):
                    $tallas = \app\models\OrdenFabricacionTallas::find()->where(['=','id_orden_fabricacion', $intCodigo])->all();
                    if($tallas){
                        foreach ($tallas as $talla){
                            $table = new \app\models\AsignacionProductoDetalle();
                            $table->id_asignacion = $id;
                            $table->id_detalle = $talla->id_detalle;
                            $table->codigo_producto = $talla->ordenFabricacion->codigo_producto;
                            $table->referencia = $talla->ordenFabricacion->referencia->referencia;
                            $table->idtalla= $talla->idtalla;
                            $table->cantidad= $talla->cantidad_vendida;
                            if($asignacion->idtipo == 1){
                                $table->valor_minuto = $empresa->valor_minuto_confeccion;
                            }else{
                                $table->valor_minuto = $empresa->valor_minuto_terminacion;
                            }
                            $table->fecha_proceso = date('Y-m-d');
                            $table->usuario = Yii::$app->user->identity->username;
                            $table->insert();
                        }    
                    }  
                endforeach;
                $this->ActualizarCantidades($id);
               return $this->redirect(['index']);
            }
        }
        $detalle = AsignacionProductoDetalle::find()->where(['=','id_asignacion', $id])->all();
        if(count($detalle) <= 0){
            return $this->renderAjax('_crearasignacionproducto', [
                 'id' => $id,
                 'productos' => $productos,
                 'token' => $token,
             ]); 
        }else{
           Yii::$app->getSession()->setFlash('info', 'La seleccion .'); 
            return $this->redirect(['index']);
        }    
    }
    //actualiza cantidades
    protected function ActualizarCantidades($id) {
        $asignacion = AsignacionProducto::findOne($id);
        $detalles = AsignacionProductoDetalle::find()->where(['=','id_asignacion', $id])->all();
        $valor =0; $total = 0;
        foreach ($detalles as $detalle):
            $valor += $detalle->cantidad;
            $total += $detalle->subtotal_producto;
        endforeach;
        $asignacion->unidades = $valor;
        $asignacion->total_orden = $total;
        $asignacion->save(false);
    }
    /**
     * Deletes an existing AsignacionProducto model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    //PROCESO DE AUTORIZACION
      public function actionAutorizar($id, $token) {
        $model = AsignacionProducto::findOne($id);
        if($model->autorizado == 0){
            $model->autorizado = 1;
            $model->update();
            $this->redirect(["asignacion-producto/view", 'id' => $id, 'token' => $token]);
        }else{
            $model->autorizado = 0;
            $model->update();
            $this->redirect(["asignacion-producto/view", 'id' => $id, 'token' => $token]);
        }
    }
    
    //CIERRE EL PROCESO DE LA OP
    public function actionGenerardocumento($id, $id_orden, $token)
     {
        $model = $this->findModel($id);
        if ($model->autorizado == 1){            
            if ($model->orden_produccion == 0){
                $consecutivo = Consecutivo::findOne(14);// 5 asignacion
                $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
                $model->orden_produccion = $consecutivo->consecutivo;
                $model->update();
                $consecutivo->update();                
                $orden = \app\models\OrdenFabricacion::find()->where(['=','id_orden_fabricacion', $id_orden])->one();
                $orden->asignado_taller = 1;
                $orden->update();
                $this->redirect(["asignacion-producto/view",'id' => $id, 'token' => $token]);
            }else{
                Yii::$app->getSession()->setFlash('error', 'Ya se genero el documento al proveedor.');
                 $this->redirect(["asignacion-producto/view",'id' => $id, 'token' => $token]);
            }
        }else{
            Yii::$app->getSession()->setFlash('error', 'El registro debe estar autorizado para poder imprimir la compra.');
            $this->redirect(["asignacion-producto/view",'id' => $id, 'token' => $token]);
        }
    }
    
    //IMPRIMIR ORDEN DE PRODUCCION
     public function actionImprimirordenproduccion($id)
    {
                                
        return $this->render('../formatos/OrdenProduccionProducto', [
            'model' => $this->findModel($id),
            
        ]);
    }
    
    //PROCESO QUE EDITA LAS CANTIDAD DE TALLAS
      public function actionEditardetalleasignacion($id, $token)
    {
        $detalle = AsignacionProductoDetalle::find()->where(['=', 'id_asignacion', $id])->all();
        if (isset($_POST["id_detalle"])) {
            $intIndice = 0;
            $cant = 0;
            foreach ($_POST["id_detalle"] as $intCodigo) {
               $table = AsignacionProductoDetalle::findOne($intCodigo);
                $table->tiempo_confeccion = $_POST["sam"][$intIndice];
               $table->cantidad = $_POST["cantidad"][$intIndice];
               $table->save(false);
               $this->ActualizarNuevaCantidad($id);
               $intIndice++;
            }
            $this->redirect(["asignacion-producto/view",'id' => $id, 'token' => $token]);
        }
        return $this->render('_editardetalleasignacion', [
            'detalle' => $detalle,
            'id' => $id,
            'token' => $token,
        ]);
    }
    
    protected function ActualizarNuevaCantidad($id) {
        
        $detalle = AsignacionProductoDetalle::find()->where(['=','id_asignacion', $id])->all();
        $asignacion = AsignacionProducto::findOne($id);
        $total = 0; $granTotal = 0; $unidades = 0; $valor_prenda = 0; 
        foreach ($detalle as $valor):
            $valor_prenda = round($valor->tiempo_confeccion * $valor->valor_minuto);
            $total = round($valor->cantidad * $valor_prenda);
            $valor->subtotal_producto = $total; 
            $valor->update();
            $granTotal += $total;
            $unidades += $valor->cantidad;
        endforeach;
        $asignacion->total_orden = $granTotal;
        $asignacion->unidades = $unidades;
        $asignacion->update();
    }

    /**
     * Finds the AsignacionProducto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AsignacionProducto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AsignacionProducto::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
     public function actionImprimiordenproduccion($id) {
         $model = AsignacionProducto::findOne($id);
                 
        return $this->render('../formatos/reporteOrdenProduccion', [
                    'model' => $model,
        ]);
    }
    
    //CONSULTA DE ORDEN DE PRODUCCION
    
    public function actionExcelconsultaAsignacion($tableexcel) {                
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
                     ->setCellValue('A1', 'OP INTERNA')
                    ->setCellValue('B1', 'ORDEN PRODUCCION')
                    ->setCellValue('C1', 'DOCUMENTO')
                    ->setCellValue('D1', 'PROVEEDOR')
                    ->setCellValue('E1', 'FECHA ASIGNACION')
                    ->setCellValue('F1', 'PROCESO')
                    ->setCellValue('G1', 'UNIDADES')                    
                    ->setCellValue('H1', 'TOTAL ORDEN')
                    ->setCellValue('I1', 'USUARIO')
                    ->setCellValue('J1', 'OBSERVACION')
                    ->setCellValue('K1', 'PRODUCTO')
                    ->setCellValue('L1', 'TALLAS')
                    ->setCellValue('M1', 'UNIDADES')
                    ->setCellValue('N1', 'SUBTOTAL');;
                   
        $i = 2  ;
        
        foreach ($tableexcel as $asignar) {
            $detalle = AsignacionProductoDetalle::find()->where(['=','id_asignacion', $asignar->id_asignacion])->all();
            foreach ($detalle as $val){
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $val->asignacion->id_asignacion)
                ->setCellValue('B' . $i, $val->asignacion->orden_produccion)
                ->setCellValue('C' . $i, $val->asignacion->documento)
                ->setCellValue('D' . $i, $val->asignacion->razon_social)
                ->setCellValue('E' . $i, $val->asignacion->fecha_asignacion)
                ->setCellValue('F' . $i, $val->asignacion->tipo->tipo)
                ->setCellValue('G' . $i, $val->asignacion->unidades)                    
                ->setCellValue('H' . $i, $val->asignacion->total_orden)
                ->setCellValue('I' . $i, $val->asignacion->usuario_editado)
                ->setCellValue('J' . $i, $val->asignacion->observacion)
                ->setCellValue('K' . $i, $val->referencia)
                ->setCellValue('L' . $i, $val->talla->talla)
                ->setCellValue('M' . $i, $val->cantidad)
                ->setCellValue('N' . $i, $val->subtotal_producto);
                $i++;
            }        
        }

        $objPHPExcel->getActiveSheet()->setTitle('Detalle');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Asignacion_referencias.xlsx"');
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
