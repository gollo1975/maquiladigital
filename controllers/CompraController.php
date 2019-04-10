<?php

namespace app\controllers;

use Yii;
use app\models\Compra;
use app\models\CompraTipo;
use app\models\CompraConcepto;
use app\models\Proveedor;
use app\models\CompraSearch;
use app\models\UsuarioDetalle;
use app\models\Consecutivo;
use app\models\Matriculaempresa;
use app\models\FormFiltroConsultaCompra;
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

/**
 * CompraController implements the CRUD actions for Compra model.
 */
class CompraController extends Controller
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
     * Lists all Compra models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',35])->all()){
                $searchModel = new CompraSearch();
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
     * Displays a single Compra model.
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
     * Creates a new Compra model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Compra();
        $proveedores = Proveedor::find()->all();
        $conceptos = CompraConcepto::find()->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->usuariosistema = Yii::$app->user->identity->username;            
            $model->update();
            $this->calculo($model->id_compra);
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'proveedores' => ArrayHelper::map($proveedores, "idproveedor", "nombreProveedores"),
            'conceptos' => ArrayHelper::map($conceptos, "id_compra_concepto", "concepto"),
        ]);
    }

    /**
     * Updates an existing Compra model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $proveedores = Proveedor::find()->all();
        $conceptos = CompraConcepto::find()->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->calculo($id);
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'proveedores' => ArrayHelper::map($proveedores, "idproveedor", "nombreProveedores"),
            'conceptos' => ArrayHelper::map($conceptos, "id_compra_concepto", "concepto"),
        ]);
    }

    /**
     * Deletes an existing Compra model.
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
            $this->redirect(["compra/index"]);
        } catch (IntegrityException $e) {
            $this->redirect(["compra/index"]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar la compra, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar la compra, tiene registros asociados en otros procesos');
            $this->redirect(["compra/index"]);
        }
    }

    /**
     * Finds the Compra model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Compra the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Compra::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionAutorizado($id) {
        $model = $this->findModel($id);
        $mensaje = "";
        if ($model->autorizado == 0) {                        
            $model->autorizado = 1;            
            $model->update();
            $this->redirect(["compra/view", 'id' => $id]);            
        } else {
            if($model->numero == 0){
                $model->autorizado = 0;
                $model->update();
                $this->redirect(["compra/view", 'id' => $id]);
            }else{
                Yii::$app->getSession()->setFlash('error', 'El registro ya fue generado, no se puede desautorizar.');
                $this->redirect(["compra/view",'id' => $id]);
            }
            
        }
    }
    
    protected function calculo($id)
    {
        $model = $this->findModel($id);
        $proveedor = Proveedor::findOne($model->id_proveedor);
        $concepto = CompraConcepto::findOne($model->id_compra_concepto);
        $configuracion = Matriculaempresa::findOne(1);
        $porcentajeiva = 0;
        $porcentajereteiva = 0;
        $porcentajeretefuente = 0;
        $porcentajebaseaiu = 0;
        $baseaiu = 0;
        if ($proveedor->tiporegimen == 1){ //comun
            if ($concepto->base_aiu <> 0){ //calculo para la base aiu y el iva
                $baseaiu = round($model->subtotal * $concepto->base_aiu / 100);
                $porcentajebaseaiu = $concepto->base_aiu;
                $impuestoiva = round($baseaiu * $concepto->porcentaje_iva / 100); //calculo iva
                $porcentajeiva = $concepto->porcentaje_iva;
                if ($baseaiu >= $concepto->base_retencion){
                    $retencionfuente = round($baseaiu * $concepto->porcentaje_retefuente / 100);
                    $porcentajeretefuente = $concepto->porcentaje_retefuente;                    
                }else{
                    $retencionfuente = 0;
                    $porcentajeretefuente = 0;
                }                
            }
            else{
                $impuestoiva = round($model->subtotal * $concepto->porcentaje_iva / 100); //calculo iva
                $porcentajeiva = $concepto->porcentaje_iva; 
                $baseaiu = 0;
                if ($concepto->base_retencion == 100){ //calculo retefuente cuando es el 100%
                    $retencionfuente = round($model->subtotal * $concepto->porcentaje_retefuente / 100);
                    if($retencionfuente == 0){
                        $porcentajeretefuente = 0;
                    }else{
                        $porcentajeretefuente = $concepto->porcentaje_retefuente;                    
                    }
                }else{
                    if ($model->subtotal >= $concepto->base_retencion){ //calculo retefuente cuando cumple con la base de retencion
                        $retencionfuente = round($model->subtotal * $concepto->porcentaje_retefuente / 100);
                        if($retencionfuente == 0){
                            $porcentajeretefuente = 0;
                        }else{
                            $porcentajeretefuente = $concepto->porcentaje_retefuente;                    
                        }
                    }else{
                        $retencionfuente = 0;
                    }
                }
            }
            
            
        }
        if ($proveedor->tiporegimen == 2){ //simplificado
            $impuestoiva = 0;            
            if ($concepto->base_retencion == 100){
                $retencionfuente = round($model->subtotal * $concepto->porcentaje_retefuente / 100);
                if($retencionfuente == 0){
                    $porcentajeretefuente = 0;
                }else{
                    $porcentajeretefuente = $concepto->porcentaje_retefuente;                    
                }
            }else{
                if ($model->subtotal >= $concepto->base_retencion){
                    $retencionfuente = round($model->subtotal * $concepto->porcentaje_retefuente / 100);
                    if($retencionfuente == 0){
                        $porcentajeretefuente = 0;
                    }else{
                        $porcentajeretefuente = $concepto->porcentaje_retefuente;                    
                    }
                }else{
                    $retencionfuente = 0;
                }
            }
        }
        if ($configuracion->gran_contribuyente == 1 && $configuracion->agente_retenedor == 1) { //calculo para el reteiva
            $retencioniva = round($impuestoiva * $concepto->porcentaje_reteiva / 100);
            $porcentajereteiva = $concepto->porcentaje_reteiva;
        }else{
            if ($configuracion->gran_contribuyente == 0 && $configuracion->agente_retenedor == 1) {
                $retencioniva = round($impuestoiva * $concepto->porcentaje_reteiva / 100);
                $porcentajereteiva = $concepto->porcentaje_reteiva;
            }else{
                $retencioniva = 0;                
            }    
        }        
        $model->porcentajeiva = $porcentajeiva;
        $model->porcentajefuente = $porcentajeretefuente;
        $model->porcentajereteiva = $porcentajereteiva;
        $model->porcentajeaiu = $porcentajebaseaiu;
        $model->base_aiu = $baseaiu;
        $model->impuestoiva = $impuestoiva;
        $model->retencionfuente = $retencionfuente;
        $model->retencioniva = $retencioniva;
        $model->total = $model->subtotal + $impuestoiva - $retencionfuente - $retencioniva;
        $model->saldo = $model->total;
        $model->save(false);
        return ;
    }
    
    public function actionGenerarnro($id)
    {
        $model = $this->findModel($id);
        $mensaje = "";
        if ($model->autorizado == 1){            
            if ($model->numero == 0){
                $consecutivo = Consecutivo::findOne(5);// 5 compras
                $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
                $model->numero = $consecutivo->consecutivo;
                $model->update();
                $consecutivo->update();                
                $this->redirect(["compra/view",'id' => $id]);
            }else{
                Yii::$app->getSession()->setFlash('error', 'El registro ya fue generado.');
                $this->redirect(["compra/view",'id' => $id]);
            }
        }else{
            Yii::$app->getSession()->setFlash('error', 'El registro debe estar autorizado para poder imprimir la compra.');
            $this->redirect(["compra/view",'id' => $id]);
        }
    }
    
    public function actionIndexconsulta() {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',41])->all()){
            $form = new FormFiltroConsultaCompra();
            $idproveedor = null;
            $desde = null;
            $hasta = null;
            $numero = null;
            $pendiente = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idproveedor = Html::encode($form->idproveedor);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $numero = Html::encode($form->numero);
                    $pendiente = Html::encode($form->pendiente);
                    $table = Compra::find()
                            ->andFilterWhere(['=', 'id_proveedor', $idproveedor])
                            ->andFilterWhere(['>=', 'fechainicio', $desde])
                            ->andFilterWhere(['<=', 'fechainicio', $hasta])
                            ->andFilterWhere(['=', 'factura', $numero]);
                    if ($pendiente == 1){
                        $table = $table->andFilterWhere(['>', 'saldo', $pendiente]);
                    }        
                    $table = $table->orderBy('id_compra desc');
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
                    if(isset($_POST['excel'])){
                        //$table = $table->all();
                        $this->actionExcelconsulta($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = Compra::find()
                        ->orderBy('id_compra desc');
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
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                    $this->actionExcelconsulta($tableexcel);
                }
            }
            $to = $count->count();
            return $this->render('index_consulta', [
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
    
    public function actionViewconsulta($id)
    {
                                
        return $this->render('view_consulta', [
            'model' => $this->findModel($id),            
        ]);
    }
    
    public function actionExcelconsulta($tableexcel) {                
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);                       
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Id')
                    ->setCellValue('B1', 'N° Factura')
                    ->setCellValue('C1', 'Proveedor')
                    ->setCellValue('D1', 'Concepto')
                    ->setCellValue('E1', 'Fecha Inicio')
                    ->setCellValue('F1', 'Fecha Vencimiento')                    
                    ->setCellValue('G1', '% Iva')
                    ->setCellValue('H1', '% ReteFuente')
                    ->setCellValue('I1', '% ReteIva')
                    ->setCellValue('J1', '% Aiu')
                    ->setCellValue('K1', 'Iva')
                    ->setCellValue('L1', 'ReteFuente')
                    ->setCellValue('M1', 'ReteIva')
                    ->setCellValue('N1', 'Base Aiu')
                    ->setCellValue('O1', 'Subtotal')                      
                    ->setCellValue('P1', 'Total')
                    ->setCellValue('Q1', 'Saldo')
                    ->setCellValue('R1', 'Autorizado')
                    ->setCellValue('S1', 'Estado')
                    ->setCellValue('T1', 'Observacion');
        $i = 2;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_compra)
                    ->setCellValue('B' . $i, $val->factura)
                    ->setCellValue('C' . $i, $val->proveedor->nombreProveedores)
                    ->setCellValue('D' . $i, $val->compraConcepto->concepto)
                    ->setCellValue('E' . $i, $val->fechainicio)
                    ->setCellValue('F' . $i, $val->fechavencimiento)                    
                    ->setCellValue('I' . $i, $val->porcentajeiva)
                    ->setCellValue('J' . $i, $val->porcentajefuente)
                    ->setCellValue('K' . $i, $val->porcentajereteiva)
                    ->setCellValue('K' . $i, $val->porcentajeaiu)
                    ->setCellValue('L' . $i, round($val->impuestoiva,0))
                    ->setCellValue('M' . $i, round($val->retencionfuente,0))
                    ->setCellValue('N' . $i, round($val->retencioniva,0))
                    ->setCellValue('N' . $i, round($val->base_aiu,0))
                    ->setCellValue('O' . $i, round($val->subtotal,0))                    
                    ->setCellValue('P' . $i, round($val->total,0))
                    ->setCellValue('Q' . $i, round($val->saldo,0))
                    ->setCellValue('R' . $i, $val->autorizar)
                    ->setCellValue('S' . $i, $val->estados)
                    ->setCellValue('T' . $i, $val->observacion);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('compras');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="compras.xlsx"');
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
