<?php

namespace app\controllers;
//clases
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
//models
use app\models\NovedadOperario;
use app\models\NovedadOperarioSearch;
use app\models\TipoNovedad;
use app\models\UsuarioDetalle;
use app\models\FormFiltroNovedadOperarios;

/**
 * NovedadOperarioController implements the CRUD actions for NovedadOperario model.
 */
class NovedadOperarioController extends Controller
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
     * Lists all NovedadOperario models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 125])->all()) {
                $form = new FormFiltroNovedadOperarios();
                $id_operario = null;
                $documento = null;
                $autorizado = null;
                $cerrado = null;
                $desde = null;
                $hasta = null;
                $tipo_novedad = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_operario = Html::encode($form->id_operario);
                        $documento = Html::encode($form->documento);
                        $cerrado = Html::encode($form->cerrado);
                        $autorizado = Html::encode($form->autorizado);
                        $desde = Html::encode($form->desde);
                        $hasta = Html::encode($form->hasta);
                        $tipo_novedad = Html::encode($form->tipo_novedad);
                        $table = NovedadOperario::find()
                                ->andFilterWhere(['=', 'id_operario', $id_operario])
                                ->andFilterWhere(['=', 'documento', $documento])
                                ->andFilterWhere(['=', 'cerrado', $cerrado])
                                ->andFilterWhere(['=','autorizado', $autorizado])
                                ->andFilterWhere(['>=','fecha_inicio_permiso', $desde]) 
                                ->andFilterWhere(['<=','fecha_inicio_permiso', $hasta])
                                ->andFilterWhere(['=','id_tipo_novedad', $tipo_novedad]); 
                        $table = $table->orderBy('id_novedad DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 30,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_novedad DESC']);
                            $this->actionExcelconsultaNovedadOperarios($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = NovedadOperario::find()
                             ->orderBy('id_novedad DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 30,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelconsultaNovedadOperarios($tableexcel);
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
     * Displays a single NovedadOperario model.
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
     * Creates a new NovedadOperario model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new NovedadOperario();

        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $operario = \app\models\Operarios::findOne($model->id_operario);
                $table = new NovedadOperario();
                $table->id_tipo_novedad = $model->id_tipo_novedad;
                $table->id_operario = $model->id_operario;
                $table->documento = $operario->documento;
                $table->fecha_inicio_permiso = $model->fecha_inicio_permiso;
                $table->fecha_final_permiso = $model->fecha_final_permiso;
                $table->hora_inicio_permiso = $model->hora_inicio_permiso;      
                $table->hora_final_permiso = $model->hora_final_permiso;
                $table->observacion = $model->observacion;
                $table->usuario =  Yii::$app->user->identity->username;
                if($table->save(false)){;
                   return $this->redirect(["novedad-operario/index"]);
                }else{
                    Yii::$app->getSession()->setFlash('error', 'Error al grabar el registro en la base de datos');
                }   

            } else {
                $model->getErrors();
            } 
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing NovedadOperario model.
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
            if ($model->validate()) {
               $table = NovedadOperario::find()->where(['id_novedad' => $id])->one();
               $operario = \app\models\Operarios::findOne($model->id_operario);
               if ($table) {
                   $table->id_tipo_novedad = $model->id_tipo_novedad;
                   $table->id_operario = $model->id_operario;
                   $table->documento = $operario->documento;
                   $table->fecha_inicio_permiso = $model->fecha_inicio_permiso;
                   $table->fecha_final_permiso = $model->fecha_final_permiso;
                   $table->hora_inicio_permiso = $model->hora_inicio_permiso;
                   $table->hora_final_permiso= $model->hora_final_permiso;
                   $table->observacion = $model->observacion;
                   $table->save(false);
                    return $this->redirect(["novedad-operario/index"]);
               }
            }else{
                $model->getErrors();
            } 
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionAutorizar($id) {
        $model = $this->findModel($id);
        if($model->autorizado == 0 ){
            $model->autorizado = 1;
            $model->update();
            return $this->redirect(["novedad-operario/view",'id' => $id]);
        }else{
            $model->autorizado = 0;
            $model->update();
            return $this->redirect(["novedad-operario/view",'id' => $id]);
        }
    }   
    //CIERRA EL PROCESO Y GENERA EL NRO DE LA NOVEDAD
    
    public function actionCerrarproceso($id) {
        $model = $this->findModel($id);
        $numero = \app\models\Consecutivo::findOne(15);
        $model->nro_novedad = $numero->consecutivo + 1;
        $model->cerrado = 1;
        $model->update();
        $numero->consecutivo = $model->nro_novedad;
        $numero->update();
        return $this->redirect(["novedad-operario/view",'id' => $id]);
    }
    
    //IMPRIMIR NOVEDAD
    
     public function actionImprimirnovedad($id) {
         $model = NovedadOperario::find()->where(['=','id_novedad', $id])->one();
        return $this->render('../formatos/novedadoperario', [
                    'model' => $model,
        ]);
    }
    /**
     * Finds the NovedadOperario model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NovedadOperario the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    
    protected function findModel($id)
    {
        if (($model = NovedadOperario::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    //EXPORTAR LAS NOVEDADES
    
    public function actionExcelconsultaNovedadOperarios($tableexcel) {                
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
                     ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'NUMERO')
                    ->setCellValue('C1', 'TIPO NOVEDAD')
                    ->setCellValue('D1', 'DOCUMENTO')
                    ->setCellValue('E1', 'OPERARIO')
                    ->setCellValue('F1', 'FECHA INICIO')
                    ->setCellValue('G1', 'FECHA FINAL ')                    
                    ->setCellValue('H1', 'HORA INICIO')
                    ->setCellValue('I1', 'HORA FINAL')
                    ->setCellValue('J1', 'FECHA PROCESO')
                    ->setCellValue('K1', 'USUARIO')
                    ->setCellValue('L1', 'AUT.')
                    ->setCellValue('M1', 'CERRADO')
                    ->setCellValue('N1', 'OBSERVACION');;
                   
        $i = 2  ;
        
        foreach ($tableexcel as $val) {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $val->id_novedad)
            ->setCellValue('B' . $i, $val->nro_novedad)        
            ->setCellValue('C' . $i, $val->tipoNovedad->novedad)
            ->setCellValue('D' . $i, $val->documento)
            ->setCellValue('E' . $i, $val->operario->nombrecompleto)
            ->setCellValue('F' . $i, $val->fecha_inicio_permiso)
            ->setCellValue('G' . $i, $val->fecha_final_permiso)
            ->setCellValue('H' . $i, $val->hora_inicio_permiso)                    
            ->setCellValue('I' . $i, $val->hora_final_permiso)
            ->setCellValue('J' . $i, $val->fecha_registro)
            ->setCellValue('K' . $i, $val->usuario)
            ->setCellValue('L' . $i, $val->estadoAutorizado)
            ->setCellValue('M' . $i, $val->procesoCerrado)
            ->setCellValue('N' . $i, $val->observacion);
            
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Novedades');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Novedades_operarios.xlsx"');
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
