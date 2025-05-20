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

//models
use app\models\Mensajeria;
use app\models\UsuarioDetalle;
/**
 * MensajeriaController implements the CRUD actions for Mensajeria model.
 */
class MensajeriaController extends Controller
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
     * Lists all Mensajeria models.
     * @return mixed
     */
   public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',157])->all()){
                $form = new \app\models\FormFiltroMensajeria();
                $proveedor= null;
                $desde = null;
                $hasta = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $proveedor = Html::encode($form->proveedor);
                        $desde = Html::encode($form->desde);
                        $hasta = Html::encode($form->hasta);
                       $table = Mensajeria::find()
                                ->andFilterWhere(['=', 'idproveedor', $proveedor])                                                                                              
                                ->andFilterWhere(['between','fecha_proceso', $desde, $hasta]);
                        $table = $table->orderBy('id_codigo DESC');
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
                                $check = isset($_REQUEST['id_codigo DESC']);
                                $this->actionExcelconsultaMensajeria($tableexcel);
                            }
                } else {
                        $form->getErrors();
                }                    
            } else {
                $table = Mensajeria::find()
                        ->orderBy('id_codigo DESC');
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
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                    $this->actionExcelconsultaMensajeria($tableexcel);
                }
           
                if (isset($_POST["cerrar_pago_mensajeria"])) { ////entra al ciclo cuando presiona el boton cerrar pagos
                   if (isset($_POST["listado_mensajeria"])) {
                       foreach ($_POST["listado_mensajeria"] as $intCodigo) {    
                           $table = Mensajeria::findOne($intCodigo);
                           if ($table){
                               $table->cerrado = 1;
                               $table->save(false);
                           }
                       }
                       return $this->redirect(['index']);
                    }else{
                         Yii::$app->getSession()->setFlash('warning', 'Debe de seleccionar al menos un registro para ejecutar el proceso..');
                    }
                } 
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

    /**
     * Displays a single Mensajeria model.
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
     * Creates a new Mensajeria model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Mensajeria();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $precio = \app\models\PrecioMensajeria::findOne($model->id_precio);
            $model->user_name = Yii::$app->user->identity->username;
            $model->valor_precio = $precio->valor_precio;
            $model->save();
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Mensajeria model.
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
           $precio = \app\models\PrecioMensajeria::findOne($model->id_precio);
           $model->valor_precio = $precio->valor_precio;
           $model->save();
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

  
    /**
     * Finds the Mensajeria model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Mensajeria the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Mensajeria::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
     //ARCHIVOS DE EXCEL
      public function actionExcelconsultaMensajeria($tableexcel) {                
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'DOCUMENTO')
                    ->setCellValue('C1', 'PROVEEDOR')
                    ->setCellValue('D1', 'FECHA PROCESO')
                    ->setCellValue('E1', 'FECHA REGISTRO')
                    ->setCellValue('F1', 'NOMBRE DE LA RUTA')
                    ->setCellValue('G1', 'VALOR PRECIO')
                    ->setCellValue('H1', 'USER NAME');
        $i = 2 ; 
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_codigo)
                    ->setCellValue('B' . $i, $val->proveedor->cedulanit)
                    ->setCellValue('C' . $i, $val->proveedor->nombrecorto)
                    ->setCellValue('D' . $i, $val->fecha_proceso)
                    ->setCellValue('E' . $i, $val->fecha_registro)
                    ->setCellValue('F' . $i, $val->precio->concepto)
                    ->setCellValue('G' . $i, $val->valor_precio)
                    ->setCellValue('H' . $i, $val->user_name);                    
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Mensajeria.xlsx"');
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
