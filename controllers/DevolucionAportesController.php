<?php

namespace app\controllers;

use Yii;

use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

// models

use app\models\DevolucionAportes;
use app\models\UsuarioDetalle;

/**
 * DevolucionAportesController implements the CRUD actions for DevolucionAportes model.
 */
class DevolucionAportesController extends Controller
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
     * Lists all DevolucionAportes models.
     * @return mixed
     */
     //CONSULTA DE ABONOS A CREDITOS
  public function actionIndex() 
    {
        if (!Yii::$app->user->identity) {
            return $this->redirect(['site/login']);
        }

        $tienePermiso = UsuarioDetalle::find()
            ->where(['codusuario' => Yii::$app->user->identity->codusuario, 'id_permiso' => 199])
            ->exists();

        if (!$tienePermiso) {
            return $this->redirect(['site/sinpermiso']);
        }

        $form = new \app\models\FormFiltroDevolucionAporte();

        // Asignamos $model como array vacío por defecto
        $model = [];

        $query = DevolucionAportes::find();

        // Si el formulario recibe datos por GET y valida correctamente
        if ($form->load(Yii::$app->request->get()) && $form->validate()) {
            $query->andFilterWhere(['id_empleado' => $form->id_empleado])
                  ->andFilterWhere(['between', 'fecha_inicio', $form->fecha_inicio, $form->fecha_corte])
                  ->andFilterWhere(['codigo_salario' => $form->concepto]); // Asegúrate que el campo se llame así en el Form
        }

        // Ordenamiento común para ambos casos
        $query->orderBy(['id_devolucion' => SORT_DESC]);

        // Exportación a Excel si enviaron el formulario por POST
        if (Yii::$app->request->isPost && isset($_POST['excel'])) {
            $tableexcel = (clone $query)->all(); // Clonamos para no afectar la paginación de la vista
            return $this->actionExcelconsultaEntregaAportes($tableexcel);
        }

        // Paginación
        $countQuery = clone $query;
        $pages = new Pagination([
            'pageSize' => 15,
            'totalCount' => $countQuery->count(),
        ]);

        // Resultados paginados (se asigna SIEMPRE a $model)
        $model = $query->offset($pages->offset)
                       ->limit($pages->limit)
                       ->all();

        return $this->render('index', [
            'model' => $model,
            'form' => $form,
            'pagination' => $pages, 
        ]);
    }

    /**
     * Displays a single DevolucionAportes model.
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
     * Finds the DevolucionAportes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DevolucionAportes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DevolucionAportes::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
      public function actionExcelconsultaEntregaAportes($tableexcel) {
         $objPHPExcel = new \PHPExcel();
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
      
                            
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'DOCUMENTO')
                    ->setCellValue('C1', 'EMPLEADO')
                    ->setCellValue('D1', 'DESDE')
                    ->setCellValue('E1', 'HASTA')
                    ->setCellValue('F1', 'FECHA HORA')
                    ->setCellValue('G1', 'CONCEPTO')
                    ->setCellValue('H1', 'VALOR APORTE')
                    ->setCellValue('I1', 'USER NAME');;
        $i = 2;
        
        foreach ($tableexcel as $detalle) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $detalle->id_devolucion) // Nota: ¿cedula_empleado está en el detalle o en la cabecera?
                ->setCellValue('B' . $i, $detalle->empleado->identificacion ?? 'N/A')
                ->setCellValue('C' . $i, $detalle->empleado->nombrecorto ?? 'N/A')    
                ->setCellValue('D' . $i, $detalle->fecha_inicio ?? 'N/A')
                ->setCellValue('E' . $i, $detalle->fecha_corte ?? 'N/A')
                ->setCellValue('F' . $i, $detalle->fecha_hora_registro)
                ->setCellValue('G' . $i, $detalle->codigoSalario->nombre_concepto ?? 'N/A')
                ->setCellValue('H' . $i, $detalle->total_devolucion)
                ->setCellValue('I' . $i, $detalle->user_name);

            // Incrementamos $i DENTRO del ciclo de detalles
            $i++;
            
        }
        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="devolucion_aportes.xlsx"');
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
