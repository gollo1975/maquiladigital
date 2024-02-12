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
use yii\db\Expression;
use yii\db\Query;
//modelos
use app\models\Insumos;
use app\models\InsumosSearch;
use app\models\UsuarioDetalle;


/**
 * InsumosController implements the CRUD actions for Insumos model.
 */
class InsumosController extends Controller
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
     * Lists all Insumos models.
     * @return mixed
     */
    public function actionIndex($token = 0) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',102])->all()){
                $form = new \app\models\FiltroBusquedaMateriaPrima();
                $codigo = null;
                $materia_prima = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $medida = null;
                $codigo_barra = null;
                $aplica_inventario = null;
                $busqueda_vcto = null;
                $nombre_proveedor = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $codigo = Html::encode($form->codigo);
                        $materia_prima = Html::encode($form->materia_prima);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $medida = Html::encode($form->medida);
                        $codigo_barra = Html::encode($form->codigo_barra);
                        $aplica_inventario = Html::encode($form->aplica_inventario);
                        $busqueda_vcto = Html::encode($form->busqueda_vcto);
                        $nombre_proveedor = Html::encode($form->nombre_proveedor);
                        if ($busqueda_vcto == 0){
                            $table = Insumos::find()
                                    ->andFilterWhere(['=', 'codigo_insumo', $codigo])
                                    ->andFilterWhere(['like', 'descripcion', $materia_prima])
                                    ->andFilterWhere(['>=', 'fecha_entrada', $fecha_inicio])
                                    ->andFilterWhere(['<=', 'fecha_entrada', $fecha_corte])
                                    ->andFilterWhere(['=', 'idproveedor', $nombre_proveedor])
                                    ->andFilterWhere(['=', 'id_tipo_medida', $medida])
                                    ->andFilterWhere(['=', 'codigo_ean', $codigo_barra])
                                    ->andFilterWhere(['=', 'aplica_inventario', $aplica_inventario]);
                        }else{
                            $table = Insumos::find()
                                    ->andFilterWhere(['=', 'codigo_insumo', $codigo])
                                    ->andFilterWhere(['like', 'descripcion', $materia_prima])
                                    ->andFilterWhere(['>=', 'fecha_vencimiento', $fecha_inicio])
                                    ->andFilterWhere(['<=', 'fecha_vencimiento', $fecha_corte])
                                    ->andFilterWhere(['=', 'idproveedor', $nombre_proveedor])
                                    ->andFilterWhere(['=', 'id_tipo_medida', $medida])
                                    ->andFilterWhere(['=', 'codigo_ean', $codigo_barra])
                                    ->andFilterWhere(['=', 'aplica_inventario', $aplica_inventario]);
                        }    
                        $table = $table->orderBy('id_insumos DESC');
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
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_insumos  DESC']);
                            $this->actionExcelConsultaInsumo($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = Insumos::find()
                            ->orderBy('id_insumos desc');
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
                    if (isset($_POST['excel'])) {
                        $this->actionExcelConsultaInsumo($tableexcel);
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
     * Displays a single Insumos model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'token' => $token,
        ]);
    }

    /**
     * Creates a new Insumos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Insumos();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $sumar = 0;
            $subtotal = 0;
            if(Insumos::find()->where(['=','codigo_insumo', $model->codigo_insumo])->one()){
                 Yii::$app->getSession()->setFlash('error', 'Este CODIGO ya esta siendo procesado por otro PROVEEDOR. Favor validar la información.');
            }else{
                $impuesto = \app\models\Impuestos::findOne($model->id_impuesto);
                $table = new Insumos();
                $table->codigo_insumo = $model->codigo_insumo;
                $table->descripcion = $model->descripcion;
                $table->fecha_entrada = $model->fecha_entrada;
                $table->fecha_vencimiento = $model->fecha_vencimiento;
                $table->id_tipo_medida = $model->id_tipo_medida;
                $table->idproveedor = $model->idproveedor;
                $table->aplica_inventario = $model->aplica_inventario;
                $table->aplica_iva = $model->aplica_iva;
                $table->stock_inicial = $model->stock_inicial;
                $table->stock_real = $model->stock_inicial;
                $table->id_impuesto = $model->id_impuesto;
                $table->porcentaje_iva = $impuesto->valor;
                $table->precio_unitario = $model->precio_unitario;                    
                if($table->aplica_iva == 1){
                    $subtotal = round($model->precio_unitario *   $model->stock_inicial);
                    $sumar = round(($subtotal * $impuesto->valor)/100);
                    $table->total_iva = $sumar;
                    $table->subtotal = $subtotal;
                    $table->total_materia_prima = round( $subtotal + $sumar);
                }else{
                    $subtotal = round($model->precio_unitario *   $model->stock_inicial);
                    $table->total_iva = 0;
                    $table->subtotal = round($subtotal);
                    $table->total_materia_prima = round($subtotal);
                 }
                $table->usuariosistema = Yii::$app->user->identity->username;
                $table->observacion = $model->observacion;
                $table->codigo_ean = $model->codigo_insumo;
                $table->inventario_inicial = $model->inventario_inicial;
                $table->save(false);
                return $this->redirect(['index']);
            }    
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Insumos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $sumar = 0;
            $subtotal = 0;
            $impuesto = \app\models\Impuestos::findOne($model->id_impuesto);
            $table = Insumos::findOne($id);
            $table->codigo_insumo = $model->codigo_insumo;
            $table->descripcion = $model->descripcion;
            $table->fecha_entrada = $model->fecha_entrada;
            $table->fecha_vencimiento = $model->fecha_vencimiento;
            $table->id_tipo_medida = $model->id_tipo_medida;
            $table->idproveedor = $model->idproveedor;
            $table->aplica_inventario = $model->aplica_inventario;
            $table->aplica_iva = $model->aplica_iva;
            $table->stock_inicial = $model->stock_inicial;
            $table->stock_real = $model->stock_inicial;
            $table->id_impuesto = $model->id_impuesto;
            $table->porcentaje_iva = $impuesto->valor;
            $table->precio_unitario = $model->precio_unitario;                    
            if($table->aplica_iva == 1){
                $subtotal = round($model->precio_unitario *   $model->stock_inicial);
                $sumar = round(($subtotal * $impuesto->valor)/100);
                $table->total_iva = $sumar;
                $table->subtotal = $subtotal;
                $table->total_materia_prima = round( $subtotal + $sumar);
            }else{
                $subtotal = round($model->precio_unitario *   $model->stock_inicial);
                $table->total_iva = 0;
                $table->subtotal = round($subtotal);
                $table->total_materia_prima = round($subtotal);
             }
            $table->usuariosistema = Yii::$app->user->identity->username;
            $table->observacion = $model->observacion;
            $table->codigo_ean = $model->codigo_insumo;
            $table->inventario_inicial = $model->inventario_inicial;
            $table->save(false);
            return $this->redirect(['index']);
        }
        if (Yii::$app->request->get("id")) {
            $table = Insumos::find()->where(['id_insumos' =>$id])->one();
            $model->codigo_insumo = $table->codigo_insumo;
            $model->descripcion = $table->descripcion;
            $model->fecha_entrada = $table->fecha_entrada;
            $model->fecha_vencimiento = $table->fecha_vencimiento;
            $model->id_tipo_medida = $table->id_tipo_medida;
            $model->precio_unitario =  $table->precio_unitario;
            $model->aplica_iva = $table->aplica_iva;
            $model->id_insumos =  $table->id_impuesto;
            $model->stock_inicial = $table->stock_inicial;       
            $model->aplica_inventario = $table->aplica_inventario;
            $model->observacion =  $table->observacion;
            $model->inventario_inicial = $table->inventario_inicial;
            $model->idproveedor = $table->idproveedor;
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Insumos model.
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
            $this->redirect(["insumos/index"]);
        } catch (IntegrityException $e) {
            $this->redirect(["insumos/index"]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el codigó, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el codigó, tiene registros asociados en otros procesos');
            $this->redirect(["insumos/index"]);
        }
    }

    /**
     * Finds the Insumos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Insumos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Insumos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    //exportar a excel
     public function actionExcelconsultaInsumo($tableexcel) {                
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
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'CODIGO')
                    ->setCellValue('C1', 'MATERIA PRIMA')
                    ->setCellValue('D1', 'MEDIDA')
                    ->setCellValue('E1', 'FECHA ENTRADA')
                    ->setCellValue('F1', 'FECHA VCTO')
                    ->setCellValue('G1', 'APLICA INVENTARIO')
                    ->setCellValue('H1', 'UNIDADES')
                    ->setCellValue('I1', 'STOCK')
                    ->setCellValue('J1', 'APLICA IVA')
                    ->setCellValue('K1', 'PORCENTAJE')
                    ->setCellValue('L1', 'PRECIO UNITARIO')
                    ->setCellValue('M1', 'SUBTOTAL')
                    ->setCellValue('N1', 'IMPUESTO')
                    ->setCellValue('O1', 'TOTAL INVENTARIO')
                    ->setCellValue('P1', 'USER CREADOR')
                    ->setCellValue('Q1', 'OBSERVACION');
        $i = 2;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_insumos)
                    ->setCellValue('B' . $i, $val->codigo_insumo)
                    ->setCellValue('C' . $i, $val->descripcion)
                    ->setCellValue('D' . $i, $val->tipomedida->medida)
                    ->setCellValue('E' . $i, $val->fecha_entrada)
                    ->setCellValue('F' . $i, $val->fecha_vencimiento)
                    ->setCellValue('G' . $i, $val->aplicaInventario)
                    ->setCellValue('H' . $i, $val->stock_inicial)
                    ->setCellValue('I' . $i, $val->stock_real)
                    ->setCellValue('J' . $i, $val->aplicaIva)
                    ->setCellValue('K' . $i, $val->porcentaje_iva)
                    ->setCellValue('L' . $i, $val->precio_unitario)
                    ->setCellValue('M' . $i, $val->subtotal)
                    ->setCellValue('N' . $i, $val->total_iva)
                    ->setCellValue('O' . $i, $val->total_materia_prima)
                    ->setCellValue('P' . $i, $val->usuariosistema)
                    ->setCellValue('Q' . $i, $val->observacion);
           $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Materias_prima.xlsx"');
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
