<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\Html;
use yii\data\Pagination;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
//models
use app\models\Despachos;
use app\models\UsuarioDetalle;
use app\models\SalidaEntradaProduccion;
use app\models\Municipio;


/**
 * DespachosController implements the CRUD actions for Despachos model.
 */
class DespachosController extends Controller
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
     * Lists all Despachos models.
     * @return mixed
     */
   public function actionIndex() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 142])->all()) {
                $form = new \app\models\FormFiltroDespachos();
                $fecha_inicio = null;
                $fecha_corte = null;
                $referencia = null;
                $proveedor = null;
                $salida = null; 
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $proveedor = Html::encode($form->proveedor);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $referencia = Html::encode($form->referencia);
                        $salida = Html::encode($form->salida);
                        $table = Despachos::find()
                                ->andFilterWhere(['like', 'codigo_producto', $referencia])
                                ->andFilterWhere(['=', 'id_salida', $salida])
                                ->andFilterWhere(['=', 'idproveedor', $proveedor])
                                ->andFilterWhere(['between', 'fecha_despacho', $fecha_inicio, $fecha_corte]);
                        $table = $table->orderBy('id_despacho DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 15,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id DESC']);
                            $this->actionExcelconsultaDespachos($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = Despachos::find()
                             ->orderBy('id_despacho DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 15,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelconsultaDespachos($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }

    /**
     * Displays a single Despachos model.
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
     * Creates a new Despachos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Despachos();
        $conSalida = \app\models\SalidaEntradaProduccion::find()->where(['=','id_entrada_tipo', 8])
                                                        ->orWhere(['=','id_entrada_tipo', 9])
                                                        ->orWhere(['=','id_entrada_tipo', 10])
                                                        ->orWhere(['=','id_entrada_tipo', 11])
                                                         ->andWhere(['=','servicio_cobrado', 0])->orderBy('id_salida DESC')->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $salida = SalidaEntradaProduccion::findOne($model->id_salida);
            $proveedor = \app\models\Proveedor::findOne($model->idproveedor);
            $muniOrigen = Municipio::findOne($model->ciudad_origen);
            $muniDestino = Municipio::findOne($model->ciudad_destino);
            $model->user_name = Yii::$app->user->identity->username;
            $model->nombre_proveedor = $proveedor->nombrecorto;
            $model->codigo_producto = $salida->codigo_producto;
            $model->municipio_origen = $muniOrigen->municipio;
            $model->municipio_destino = $muniDestino->municipio;
            $model->tulas_reales = $salida->numero_tulas;
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id_despacho]);
        }

        return $this->render('create', [
            'model' => $model,
            'conSalida' => ArrayHelper::map($conSalida, 'id_salida','nombreReferencia'),
        ]);
    }

    /**
     * Updates an existing Despachos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $conSalida = \app\models\SalidaEntradaProduccion::find()->where(['=','id_entrada_tipo', 8])
                                                        ->orWhere(['=','id_entrada_tipo', 9])
                                                        ->orWhere(['=','id_entrada_tipo', 10])
                                                        ->orWhere(['=','id_entrada_tipo', 11])
                                                         ->orWhere(['=','servicio_cobrado', 0])->orderBy('id_salida DESC')->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $salida = SalidaEntradaProduccion::findOne($model->id_salida);
            $proveedor = \app\models\Proveedor::findOne($model->idproveedor);
            $muniOrigen = Municipio::findOne($model->ciudad_origen);
            $muniDestino = Municipio::findOne($model->ciudad_destino);
            $model->nombre_proveedor = $proveedor->nombrecorto;
            $model->codigo_producto = $salida->codigo_producto;
            $model->municipio_origen = $muniOrigen->municipio;
            $model->municipio_destino = $muniDestino->municipio;
            $model->tulas_reales = $salida->numero_tulas;
            $model->save(false);
            
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'conSalida' => ArrayHelper::map($conSalida, 'id_salida','nombreReferencia'),
        ]);
    }
   //AUTORIZA EL PROCESO DE DESPACHO
   public function actionAutorizado($id) {
        $despacho = Despachos::findOne($id);
        if($despacho->autorizado == 0){
            $despacho->autorizado = 1;
            $despacho->save();
        }else{
            $despacho->autorizado = 0;
            $despacho->save();
        } 
        return $this->redirect(['despachos/view', 'id' => $id]);
    }
    
    //CIERRA EL DESPACHO AL PROVEEDOR
     public function actionCerrar_despacho($id, $id_salida) {
        $model = Despachos::findOne($id);
        $salida = SalidaEntradaProduccion::findOne($id_salida);
         //generar consecutivo
        $registro = \app\models\Consecutivo::findOne(20);
        $valor = $registro->consecutivo + 1;
        $model->numero_despacho = $valor;
        $model->proceso_cerrado = 1;
        $model->save();
        //actualiza consecutivo
        $registro->consecutivo = $valor;
        $registro->save();
        //actualiza la salida 
        $salida->servicio_cobrado = 1;
        $salida->save();
        return $this->redirect(['despachos/view', 'id' => $id]); 
    }
     
      public function actionImprimir_despacho($id)
    {
        return $this->render('../formatos/reporte_despacho_flete', [
            'model' => $this->findModel($id),
            
        ]);
    }


    /**
     * Finds the Despachos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Despachos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Despachos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    //DESCARGA LOS ABONOS A CREDITOS
     public function actionExcelconsultaDespachos($tableexcel) {                
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
              $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'NUMERO DESPACHO')
                    ->setCellValue('C1', 'NUMERO SALIDA')
                    ->setCellValue('D1', 'PROVEEDOR')
                    ->setCellValue('E1', 'TIPO SALIDA')
                    ->setCellValue('F1', 'REFERENCIA')
                    ->setCellValue('G1', 'CIUDAD ORIGEN')                    
                    ->setCellValue('H1', 'CIUDAD DESTINO')
                    ->setCellValue('I1', 'TULAS FACT.')
                    ->setCellValue('J1', 'TULAS REALES')
                    ->setCellValue('K1', 'VALOR FLETE')
                    ->setCellValue('L1', 'FECHA PROCESO')
                    ->setCellValue('M1', 'FECHA REGISTRO')
                    ->setCellValue('N1', 'AUTORIZADO')
                    ->setCellValue('O1', 'CERRADO')
                    ->setCellValue('P1', 'OBSERVACION');
                     
                   
        $i = 2  ;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_salida)
                    ->setCellValue('B' . $i, $val->numero_despacho)
                    ->setCellValue('C' . $i, $val->id_salida)
                    ->setCellValue('D' . $i, $val->nombre_proveedor)
                    ->setCellValue('E' . $i, $val->tipoEntrada->concepto)
                    ->setCellValue('F' . $i, $val->codigo_producto)                    
                    ->setCellValue('G' . $i, $val->municipio_destino)
                    ->setCellValue('H' . $i, $val->municipio_origen)
                    ->setCellValue('I' . $i, $val->total_tulas)
                    ->setCellValue('J' . $i, $val->tulas_reales)
                    ->setCellValue('K' . $i, $val->valor_flete)
                    ->setCellValue('L' . $i, $val->fecha_despacho)
                    ->setCellValue('M' . $i, $val->fecha_registro)
                    ->setCellValue('N' . $i, $val->autorizadoRegistro)
                    ->setCellValue('O' . $i, $val->procesoCerrado)
                    ->setCellValue('P' . $i, $val->observacion);
                   
                  
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Despachos_Fletes.xlsx"');
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
