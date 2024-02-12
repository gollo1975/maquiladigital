<?php

namespace app\controllers;

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

//models
use app\models\EntradaMateriaPrima;
use app\models\EntradaMateriaPrimaDetalle;
use app\models\Insumos;
use app\models\Proveedor;
use app\models\EntradaMateriaPrimaSearch;
use app\models\UsuarioDetalle;


/**
 * EntradaMateriaPrimaController implements the CRUD actions for EntradaMateriaPrima model.
 */
class EntradaMateriaPrimaController extends Controller
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
     * Lists all EntradaMateriaPrima models.
     * @return mixed
     */
    public function actionIndex($token = 0) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso', 132])->all()){
                $form = new \app\models\FiltroBusquedaEntradaMateria();
                $id_entrada= null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $proveedor = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_entrada = Html::encode($form->id_entrada);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $proveedor = Html::encode($form->proveedor);
                        $table = EntradaMateriaPrima::find()
                                    ->andFilterWhere(['between', 'fecha_proceso', $fecha_inicio, $fecha_corte])
                                    ->andFilterWhere(['=', 'idproveedor', $proveedor]);
                        $table = $table->orderBy('id_entrada DESC');
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
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_entrada  DESC']);
                            $this->actionExcelConsultaEntrada($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = EntradaMateriaPrima::find()
                            ->orderBy('id_entrada DESC');
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
                    if (isset($_POST['excel'])) {
                        $this->actionExcelConsultaEntrada($tableexcel);
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

    ///entradas por codigo de barra
    public function actionCodigo_barra_ingreso($id) {
        $form = new \app\models\ModeloEntradaProducto();
        $model = \app\models\EntradaMateriaPrimaDetalle::find()->where(['=','id_entrada', $id])->all();
        $codigo_producto = 0;
        if ($form->load(Yii::$app->request->get())) {
            $codigo_producto = Html::encode($form->codigo_producto);
            if ($codigo_producto > 0) {
                $table = \app\models\Insumos::find()->Where(['=','codigo_insumo', $codigo_producto])->one();
                if($table){
                    $conDato = \app\models\EntradaMateriaPrimaDetalle::find()->where(['=','codigo_producto', $codigo_producto])
                                                                      ->andWhere(['=','id_entrada', $id])->one();
                    if(!$conDato){
                        $entrada = new \app\models\EntradaMateriaPrimaDetalle();
                        $entrada->id_entrada = $id;
                        $entrada->id_insumos = $table->id_insumos;
                        $entrada->codigo_producto = $codigo_producto;
                        $entrada->fecha_vencimiento = date('Y-m-d');
                        $entrada->porcentaje_iva = $table->porcentaje_iva;
                        $entrada->valor_unitario = $table->precio_unitario;
                        $entrada->save(false);
                        $model = EntradaMateriaPrimaDetalle::find()->where(['=','id_entrada', $id])->all(); 
                        $this->redirect(["entrada-materia-prima/codigo_barra_ingreso",'id' => $id]);
                        if (isset($_POST['excel'])) {
                                $check = isset($_REQUEST['id_entrada  DESC']);
                                $this->actionExcelConsultaEntrada($tableexcel);
                        }
                    }else{
                        Yii::$app->getSession()->setFlash('success', 'El código digitado ya se en cuentra agregado a esta entrada.');
                     return $this->redirect(['/entrada-materia-prima/codigo_barra_ingreso','id' =>$id]);
                    }    
                }else{
                     Yii::$app->getSession()->setFlash('info', 'El código del producto no se encuentra en el sistema.');
                     return $this->redirect(['/entrada-materia-prima/codigo_barra_ingreso','id' =>$id]);
                }
            }else{
                Yii::$app->getSession()->setFlash('warning', 'Debe digitar codigo del producto a buscar.');
                return $this->redirect(['/entrada-materia-prima/codigo_barra_ingreso','id' =>$id]);
            }    
        }
        if(isset($_POST["actualizarlineas"])){
            if(isset($_POST["detalle_entrada"])){
                $intIndice = 0;
                $auxiliar = 0;
                $iva = 0 ;
                foreach ($_POST["detalle_entrada"] as $intCodigo):
                    $table = EntradaMateriaPrimaDetalle::findOne($intCodigo);
                    $table->actualizar_precio = $_POST["actualizar_precio"]["$intIndice"];
                    $table->cantidad = $_POST["cantidad"]["$intIndice"];
                    $table->fecha_vencimiento = $_POST["fecha_vcto"]["$intIndice"];
                    if($_POST["actualizar_precio"]["$intIndice"] == 1){
                       $table->valor_unitario = $_POST["valor_unitario"]["$intIndice"];
                       $auxiliar =  $table->cantidad * $table->valor_unitario;
                       $iva = round(($auxiliar * $table->porcentaje_iva)/100);
                    }else{
                       $auxiliar =  $table->cantidad * $table->valor_unitario;
                       $iva = round(($auxiliar * $table->porcentaje_iva)/100);
                    }
                    $table->total_iva = $iva;
                    $table->subtotal = $auxiliar;
                    $table->total_entrada = $iva + $auxiliar;
                    $table->save(false);
                    $auxiliar = 0;
                    $iva = 0;   
                    $intIndice++;
                endforeach;
                $this->ActualizarLineas($id);
                return $this->redirect(['/entrada-materia-prima/codigo_barra_ingreso','id' =>$id]);
            }
            
        }
        return $this->render('codigo_barra', [
                    'model' => $model,
                    'form' => $form,
                    'id' => $id,
        ]);
        
    }
    //proceso que suma los totales
    protected function ActualizarLineas($id) {
        $entrada = EntradaMateriaPrima::findOne($id);
        $detalle = EntradaMateriaPrimaDetalle::find()->where(['=','id_entrada', $id])->all();
        $subtotal = 0; $iva = 0; $total = 0;
        foreach ($detalle as $detalles):
            $subtotal += $detalles->subtotal;
            $iva += $detalles->total_iva;
            $total += $detalles->total_entrada;
        endforeach;
        $entrada->subtotal = $subtotal;
        $entrada->impuesto = $iva;
        $entrada->total_salida = $total;
        $entrada->save(false);
    }
    
    //ELIMINAR DETALLES  
    public function actionEliminar_manual($id, $detalle_manual)
    {                                
        $detalle = EntradaMateriaPrimaDetalle::findOne($detalle_manual);
        $detalle->delete();
        $this->ActualizarLineas($id);
        $this->redirect(["codigo_barra_ingreso",'id' => $id]);        
    } 
    
    //AUTORIZAR ENTRADA SIN OC
     public function actionAutorizado($id) {
        $model = $this->findModel($id);
        if ($model->autorizado == 0) {                        
                $model->autorizado = 1;            
               $model->update();
               $this->redirect(["entrada-materia-prima/codigo_barra_ingreso", 'id' => $id]);  

        } else{
                $model->autorizado = 0;
                $model->update();
                $this->redirect(["entrada-materia-prima/codigo_barra_ingreso", 'id' => $id]);  
        }    
    }
    
     //actualizar inventario
     public function actionActualizar_inventario($id) {
        $model = $this->findModel($id);
        $detalle = EntradaMateriaPrimaDetalle::find()->where(['=','id_entrada', $id])->all(); // carga el detalle
        $codigo = 0;
        foreach ($detalle as $detalles):
            $inventario = Insumos::find()->where(['=','id_insumos', $detalles->id_insumos])->one();
            if($inventario){
                $codigo = $inventario->id_insumos;
                $inventario->fecha_vencimiento = $detalles->fecha_vencimiento;
                if($detalles->actualizar_precio == 1){
                   $inventario->precio_unitario  = $detalles->valor_unitario;
                   $inventario->stock_inicial += $detalles->cantidad; 
                   $inventario->stock_real += $detalles->cantidad;
                } else {
                   $inventario->stock_inicial += $detalles->cantidad;   
                   $inventario->stock_real += $detalles->cantidad;
                } 
                $inventario->save(false);
                $this->ActualizarCostoInventario($codigo);
            }
        endforeach;
        $model->enviar_materia_prima = 1;
        $model->save();
        $this->redirect(["entrada-materia-prima/codigo_barra_ingreso", 'id' => $id]);
    }
    
    //proceso para multiplicar inventario
    protected function ActualizarCostoInventario($codigo) {
        $iva = 0; $subtotal = 0;
        $inventario = Insumos::find()->where(['=','id_insumos', $codigo])->one();
        $subtotal = round($inventario->stock_real * $inventario->precio_unitario);
        $iva = round(($subtotal * $inventario->porcentaje_iva)/100);
        $inventario->subtotal = $subtotal;
        $inventario->total_iva = $iva;
        $inventario->total_materia_prima = $subtotal + $iva;
        $inventario->save(false);
    }
    
    /**
     * Displays a single EntradaMateriaPrima model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EntradaMateriaPrima model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
     public function actionCreate()
    {
        $model = new EntradaMateriaPrima();
        $proveedores = \app\models\Proveedor::find()->orderBy('nombrecorto ASC')->all(); 
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->user_name_crear= Yii::$app->user->identity->username;
            $model->update();
            return $this->redirect(['codigo_barra_ingreso', 'id' => $model->id_entrada]);
        }

        return $this->render('create', [
            'model' => $model,
            'proveedores' => ArrayHelper::map($proveedores, "idproveedor", "nombrecorto"),
        ]);
    }

    /**
     * Updates an existing EntradaMateriaPrima model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_entrada]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EntradaMateriaPrima model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EntradaMateriaPrima model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EntradaMateriaPrima the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EntradaMateriaPrima::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionExcelconsultaEntrada($tableexcel) {                
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
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'DOCUMENTO')
                    ->setCellValue('C1', 'PROVEEDOR')
                    ->setCellValue('D1', 'FECHA ENTRADA')
                    ->setCellValue('E1', 'FECHA REGISTRO')
                    ->setCellValue('F1', 'CODIGO PRODUCTO')
                    ->setCellValue('G1', 'PRODUCTO')
                    ->setCellValue('H1', 'FECHA VCTO')
                    ->setCellValue('I1', 'ACT. PRECIO')
                    ->setCellValue('J1', 'CANT. ENTRADAS')
                    ->setCellValue('K1', 'VR. UNITARIO')
                    ->setCellValue('L1', 'SUBTOTAL')
                    ->setCellValue('M1', 'IVA')
                    ->setCellValue('N1', 'TOTAL')
                    ->setCellValue('O1', 'AUTORIZADO')
                    ->setCellValue('P1', 'ENVIADO')
                    ->setCellValue('Q1', 'USER NAME CREADOR')
                    ->setCellValue('R1', 'USER NAME EDITADO')
                    ->setCellValue('S1', 'OBSERVACION');
        $i = 2;
        
        foreach ($tableexcel as $val) {
            $detalle = EntradaMateriaPrimaDetalle::find()->where(['=','id_entrada', $val->id_entrada])->all();
            foreach ($detalle as $detalles){
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $val->id_entrada)
                        ->setCellValue('B' . $i, $val->proveedor->nombrecorto)
                        ->setCellValue('C' . $i, $val->numero_soporte)
                        ->setCellValue('D' . $i, $val->fecha_proceso)
                        ->setCellValue('E' . $i, $val->fecha_registro)
                        ->setCellValue('F' . $i, $detalles->codigo_producto)
                        ->setCellValue('G' . $i, $detalles->insumos->descripcion)
                        ->setCellValue('H' . $i, $detalles->fecha_vencimiento)
                        ->setCellValue('I' . $i, $detalles->actualizarPrecio)
                        ->setCellValue('J' . $i, $detalles->cantidad)
                        ->setCellValue('K' . $i, $detalles->valor_unitario)
                        ->setCellValue('L' . $i, $detalles->subtotal)
                        ->setCellValue('M' . $i, $detalles->total_iva)
                        ->setCellValue('N' . $i, $detalles->total_entrada)
                        ->setCellValue('O' . $i, $val->autorizadoEntrada)
                        ->setCellValue('P' . $i, $val->enviarMateria)
                        ->setCellValue('Q' . $i, $val->user_name_crear)
                        ->setCellValue('R' . $i, $val->user_name_edit)
                        ->setCellValue('S' . $i, $val->observacion);
                $i++;
            }    
        }

        $objPHPExcel->getActiveSheet()->setTitle('Entradas');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Entrada_Materia_Prima.xlsx"');
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
