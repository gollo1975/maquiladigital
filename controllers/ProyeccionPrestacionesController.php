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
use yii\data\ActiveDataProvider;

use app\models\ProyeccionPrestaciones;
use app\models\ProyeccionPrestacionesDetalle;
use app\models\Empleado;
use app\models\Contrato;
use app\models\UsuarioDetalle;

/**
 * ProyeccionPrestacionesController implements the CRUD actions for ProyeccionPrestaciones model.
 */
class ProyeccionPrestacionesController extends Controller
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
     * Lists all ProyeccionPrestaciones models.
     * @return mixed
     */
    //PROYECCIONES DE PRESTACIONES SOCIALES
    public function actionIndex() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 171])->all()) {
                $form = new \app\models\FormFiltroBuscarNomina();
                
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $desde = Html::encode($form->desde);
                        $hasta = Html::encode($form->hasta);
                        
                        $table = ProyeccionPrestaciones::find()
                                                 ->andFilterWhere(['between', 'fecha_inicio', $desde, $hasta]);
                                                
                        $table = $table->orderBy('id_proyeccion DESC');
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
                    $table = ProyeccionPrestaciones::find()->orderBy('id_proyeccion DESC');
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
                //$to = $count->count();
                return $this->render('index', [
                            'model' => $model,
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
     * Displays a single ProyeccionPrestaciones model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        // Preparamos el provider para la vista
        $dataProvider = new ActiveDataProvider([
            'query' => ProyeccionPrestacionesDetalle::find()->where(['id_proyeccion' => $id]),
            'pagination' => ['pageSize' => 50],
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider, // Pasamos el provider en lugar del array
        ]);
    }

    /**
     * Creates a new ProyeccionPrestaciones model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    //CREAR PROYECCIONES PRESTACIONES
    public function actionCrear_proyeccion_prestaciones() {
        $model = new \app\models\FormCostoGastoEmpresa();
         if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()){
                if (isset($_POST["crear_proyeccion"])) {
                   
                        $table = new ProyeccionPrestaciones();
                        $table->fecha_inicio = $model->fecha_inicio;
                        $table->fecha_corte = $model->fecha_corte;
                        $table->user_name = Yii::$app->user->identity->username;
                        $table->fecha_hora_registro = date('Y-m-d H:i:s');
                        $table->save();
                        return $this->redirect(["index"]); 
                }
            } 
         }
        return $this->renderAjax('crear_proyeccion_prestaciones', [
            'model' => $model,       
        ]);    
    }

    //LIST TODOS LOS CONTRATOS ACTIVOS
    public function actionGenerar_proyeccion_prestacional($id, $fecha_inicio, $fecha_corte) {
        //. buscamos los contratos
        $conContratos = Contrato::find()->where(['contrato_activo' => 1])
                                        ->andWhere(['<=','fecha_inicio' , $fecha_corte])
                                        ->all();
        if(!$conContratos){
            Yii::$app->getSession()->setFlash('error','No existen contractos activos en este rango de fechas.');
            return $this->redirect(["index"]); 
        }
            
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $con = 0;
            $rows = [];
            foreach ($conContratos as $filaContrato) {
                
                //Validar si la fecha de inicio del contrato es menor que la fecha_inicio
                $fecha_inicio_contrato = $filaContrato->fecha_inicio;
                
                if(strtotime($fecha_inicio_contrato) < strtotime($fecha_inicio)){
                    $totalDias = $this->calcularDiasComerciales($fecha_inicio, $fecha_corte);
                    $tiempoExtra = $this->CalcularDevengadoEmpleado($fecha_inicio, $fecha_corte, $filaContrato);
                }else{
                    $fecha_inicio = $fecha_inicio_contrato;
                    $totalDias = $this->calcularDiasComerciales($fecha_inicio, $fecha_corte);
                    $tiempoExtra = $this->CalcularDevengadoEmpleado($fecha_inicio, $fecha_corte, $filaContrato);
                }
                
                $salarioPromedio = round(($tiempoExtra / $totalDias) * 30);
                $salarioPromedio = $filaContrato->salario + $salarioPromedio;
                
                $exists = ProyeccionPrestacionesDetalle::find()
                    ->where(['id_proyeccion' => $id, 'id_contrato' => $filaContrato->id_contrato])
                    ->exists();

                if ($exists) continue; // Salta al siguiente si ya existe
                $rows[] = [
                    $id,
                    $filaContrato->id_empleado,
                    $filaContrato->id_contrato,
                    $filaContrato->identificacion,
                    $filaContrato->empleado->nombrecorto,
                    $fecha_inicio,
                    $fecha_corte,
                    $filaContrato->fecha_inicio, 
                    $totalDias,
                    $salarioPromedio
                ];
                $con++;
            }

            if (!empty($rows)) {
                Yii::$app->db->createCommand()->batchInsert(
                        ProyeccionPrestacionesDetalle::tableName(),
                    ['id_proyeccion', 'id_empleado', 'id_contrato', 'cedula_empleado', 'nombre_empleado', 'fecha_inicio', 'fecha_corte','fecha_inicio_contrato','numero_dias','salario_promedio'],
                    $rows
                )->execute();
            }
            // --- ESTO ES LO QUE FALTABA ---
            $transaction->commit();
            Yii::$app->getSession()->setFlash('success', 'Se cargaron ' . $con . ' Registros exitosamente.');

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', 'Ocurrió un error: ' . $e->getMessage());
        }  
        
        return $this->redirect(["index"]); 
        
    }
    
    //PROCESO QUE CALCULA LOS DIAS
    protected function calcularDiasComerciales($fecha_inicio, $fecha_corte)
    {
        $f1 = new \DateTime($fecha_inicio);
        $f2 = new \DateTime($fecha_corte);

        $d1 = (int)$f1->format('d');
        $m1 = (int)$f1->format('m');
        $y1 = (int)$f1->format('y');

        $d2 = (int)$f2->format('d');
        $m2 = (int)$f2->format('m');
        $y2 = (int)$f2->format('y');

        // Ajuste: si el día es 31, lo tomamos como 30
        $d1 = ($d1 == 31) ? 30 : $d1;
        $d2 = ($d2 == 31) ? 30 : $d2;

        // Cálculo total de días
        $totalDias = (($y2 - $y1) * 360) + (($m2 - $m1) * 30) + ($d2 - $d1) + 1;

        return $totalDias;
    }
    
    //PROCESO QUE CALCULA EL DEVENGADO DE LA PERSONA
    protected function CalcularDevengadoEmpleado($fecha_inicio, $fecha_corte, $filaContrato) {
         $sumas = \app\models\ProgramacionNomina::find()
                ->select([
                    'SUM(total_tiempo_extra) AS total_tiempo'
                    
                ])
                ->where(['>=', 'fecha_desde', $fecha_inicio])
                ->andWhere(['id_contrato' => $filaContrato->id_contrato])
                ->asArray() // Esto es importante para recibir los resultados como un array asociativo
                ->one();

            // Acceso a los resultados:
           return $tiempoExtra = $sumas['total_tiempo'] ?? 0;
           
    }
    
    public function actionGenerarMasivo()
    {
        $ids = Yii::$app->request->post('seleccion'); // IDs de los registros seleccionados
        $tipo = Yii::$app->request->post('tipo');     // 'primas', 'cesantias' o 'vacaciones'
        $id = Yii::$app->request->post('id');  

        if (!$ids) {
            Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un empleado.');
            return $this->redirect(['view', 'id' => $id]); // Ajusta según tu lógica
        }

        $models = \app\models\ProyeccionPrestacionesDetalle::find()
                ->where(['id_detalle' => $ids]) // $ids es el array que viene del post
                ->all();

        foreach ($ids as $idDetalle) {
            
            // Aquí ejecutas tu lógica según el tipo
            switch ($tipo) {
                case 'primas':
                    $this->calcularPrima($models);
                    break;
                case 'cesantias':
                    $this->calcularCesantias($models);
                    break;
                case 'vacaciones':
                    $this->calcularVacaciones($models);
                    break;
            }
        }
        // Al finalizar los cálculos, consolidamos automáticamente
        $modelPadre = ProyeccionPrestaciones::findOne($id);
        if ($modelPadre) {
            $modelPadre->consolidarTotales();
        }
        
        Yii::$app->getSession()->setFlash('success', 'Proceso de ' . $tipo . ' finalizado correctamente.');
        return $this->redirect(['view', 'id' => $id]);
    }
    
    //PROCESO PARA PRIMAS
    protected function calcularPrima($models) 
    {
        // 1. Obtener la configuración una sola vez fuera del ciclo
        $config = \app\models\ConfiguracionSalario::find()->where(['estado' => 1])->one();
        if (!$config) {
            Yii::$app->getSession()->setFlash('error', 'No existe una configuración de salario activa.');
            return;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            
            $topeAuxilio = $config->salario_minimo_actual * 2;
            foreach ($models as $model) {
                // 2. Lógica de cálculo
                // Aseguramos acceso al salario y dias (asumiendo que el modelo los tiene)
                if ($model->contrato->salario < $topeAuxilio) {
                    // Incluye auxilio de transporte si gana menos de 2 SMMLV
                    $base = $model->salario_promedio + $config->auxilio_transporte_actual;
                } else {
                    $base = $model->salario_promedio;
                }
                // Aplicamos la fórmula: (Base * días laborados) / 360
                $model->valor_prima = (int) round(($base * $model->numero_dias) / 360);
                
                $model->recalcularTotalLinea();
                
                // 3. Guardar el registro
                if (!$model->save()) {
                    throw new \Exception("Error al guardar prima para " . $model->nombre_empleado);
                }
                
            }
            $transaction->commit();
           
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', 'Ocurrió un error: ' . $e->getMessage());
        }     
    }
    
    //PROCESO PARA CESANTIAS
    protected function calcularCesantias($models) 
    {
        // 1. Obtener la configuración una sola vez fuera del ciclo
        $config = \app\models\ConfiguracionSalario::find()->where(['estado' => 1])->one();
        if (!$config) {
            Yii::$app->getSession()->setFlash('error', 'No existe una configuración de salario activa.');
            return;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            
            $topeAuxilio = $config->salario_minimo_actual * 2;
            foreach ($models as $model) {
                // 2. Lógica de cálculo
                // Aseguramos acceso al salario y dias (asumiendo que el modelo los tiene)
                if ($model->contrato->salario < $topeAuxilio) {
                    // Incluye auxilio de transporte si gana menos de 2 SMMLV
                    $base = $model->salario_promedio + $config->auxilio_transporte_actual;
                } else {
                    $base = $model->salario_promedio;
                }
                
                // Aplicamos la fórmula: (Base * días laborados) / 360
                $model->valor_cesantia = (int) round(($base * $model->numero_dias) / 360);
                $intereses = ($model->valor_cesantia * $model->numero_dias * 0.12) / 360;
                $model->valor_intereses = (int) round($intereses);
                
                $model->recalcularTotalLinea();
                
                // 3. Guardar el registro
                if (!$model->save()) {
                    throw new \Exception("Error al guardar prima para " . $model->nombre_empleado);
                }
            }
            $transaction->commit();
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', 'Ocurrió un error: ' . $e->getMessage());
        }     
    }
    
    //PROCESO PARA VACACIONES
    protected function calcularVacaciones($models) 
    {
        // 1. Obtener la configuración una sola vez fuera del ciclo
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            
            
            foreach ($models as $model) {
                // 2. Lógica de cálculo
                // Aseguramos acceso al salario y dias (asumiendo que el modelo los tiene)
                
                $base = $model->contrato->salario;
                
                // Aplicamos la fórmula
                $model->valor_vacacion = (int) round(($base * $model->numero_dias) / 720);
                
                $model->recalcularTotalLinea();
                
                // 3. Guardar el registro
                if (!$model->save()) {
                    throw new \Exception("Error al guardar vacacion para " . $model->nombre_empleado);
                }
            }
            $transaction->commit();
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', 'Ocurrió un error: ' . $e->getMessage());
        }     
    }
    
    
      

    /**
     * Finds the ProyeccionPrestaciones model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProyeccionPrestaciones the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProyeccionPrestaciones::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionExportarExcel($id)
    {
        $modelPadre = ProyeccionPrestaciones::findOne($id);
        $detalles = ProyeccionPrestacionesDetalle::find()
            ->where(['id_proyeccion' => $id])
            ->all();

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setTitle("Proyección de Prestaciones")
            ->setSubject("Detalle Proyección");

        // Activar la primera hoja
        $sheet = $objPHPExcel->setActiveSheetIndex(0);

        // 1. Escribir Cabeceras
        $headers = ['Cédula', 'Nombre', 'Fecha Inicio', 'Prima', 'Cesantía', 'Intereses', 'Vacación', 'Total'];
        $column = 'A';
        $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
        for ($col = 'A'; $col !== 'I'; $col++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        }
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // 2. Llenar los datos
        $rowNumber = 2;
        foreach ($detalles as $row) {
            $sheet->setCellValue('A' . $rowNumber, $row->cedula_empleado);
            $sheet->setCellValue('B' . $rowNumber, $row->nombre_empleado);
            $sheet->setCellValue('C' . $rowNumber, $row->fecha_inicio);
            $sheet->setCellValue('D' . $rowNumber, $row->valor_prima);
            $sheet->setCellValue('E' . $rowNumber, $row->valor_cesantia);
            $sheet->setCellValue('F' . $rowNumber, $row->valor_intereses);
            $sheet->setCellValue('G' . $rowNumber, $row->valor_vacacion);
            $sheet->setCellValue('H' . $rowNumber, $row->total_linea);
            $rowNumber++;
        }

        // 3. Forzar la descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Proyeccion_' . $id . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
