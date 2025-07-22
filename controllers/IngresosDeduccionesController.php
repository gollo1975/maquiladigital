<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\helpers\Html;
//models
use app\models\IngresosDeducciones;
use app\models\UsuarioDetalle;
use app\models\IngresosDeduccionesDetalle;
use app\models\Empleado;

/**
 * IngresosDeduccionesController implements the CRUD actions for IngresosDeducciones model.
 */
class IngresosDeduccionesController extends Controller
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
     * Lists all IngresosDeducciones models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',164])->all()){
                $form = new \app\models\FormFiltroIngresoDeducciones();
                $fecha_inicio = null;
                $fecha_corte = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $table = IngresosDeducciones::find()
                                ->where(['between','fecha_inicio', $fecha_inicio, $fecha_corte]);
                        $table = $table->orderBy('id_ingreso DESC');
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
                $table = IngresosDeducciones::find()->orderBy('id_ingreso DESC');
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
            ]);
            
        }else{
             return $this->redirect(['site/sinpermiso']);
        }     
        }else{
           return $this->redirect(['site/login']);
        }
   }

    /**
     * Displays a single IngresosDeducciones model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $fecha_inicio, $fecha_corte)
    {
        $form = new \app\models\FormFiltroConsultaAdicionPermanente();
        $id_empleado = null; 
        $codigo_salario = null;
        $tipoadicion = null;
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {   
                $id_empleado = Html::encode($form->id_empleado);
                $codigo_salario = Html::encode($form->codigo_salario);
                $tipoadicion = Html::encode($form->tipo_adicion);
                $table = IngresosDeduccionesDetalle::find()
                                ->andFilterWhere(['=','id_empleado',$id_empleado])
                                ->andFilterWhere(['=', 'suma_resta', $tipoadicion])
                                ->andFilterWhere(['=', 'codigo_salario', $codigo_salario])
                                ->andWhere(['=','id_ingreso',$id]);
                        $table = $table->orderBy('id_detalle DESC');
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
                           
               
           }else {
               $form->geterrores;
           }
        }else{
            $table = IngresosDeduccionesDetalle::find()->where(['=','id_ingreso', $id])
                                                       ->orderBy('id_empleado DESC');
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
        return $this->render('view', [
            'modelo' => $this->findModel($id),
            'model' => $model,
            'id' => $id,
            'form' => $form,
            'fecha_inicio' => $fecha_inicio,
            'fecha_corte' => $fecha_corte,
             'pagination' => $pages,
            
        ]);
    }

    //vista de las ingresos y deduciones
     public function actionVista($id, $id_detalle, $fecha_corte, $fecha_inicio) {
        $model = IngresosDeduccionesDetalle::findOne($id_detalle);
         
         return $this->render('vista', [
            'model' => $model, 'id'=>$id,
             'fecha_corte' => $fecha_corte,
             'fecha_inicio' => $fecha_corte,
        ]);
    }  
    
    /**
     * Creates a new IngresosDeducciones model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new IngresosDeducciones();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->user_name = Yii::$app->user->identity->username;
            $model->fecha_hora_proceso = date('Y-m-d H:i:s');
            $model->save();
            return $this->redirect(['view', 'id' => $model->id_ingreso, 'fecha_inicio' =>$model->fecha_inicio, 'fecha_corte' => $model->fecha_corte]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    
    //crear ingresos al empleado
     public function actionCreateadicion($id, $fecha_corte, $fecha_inicio) {        
        $model = new IngresosDeduccionesDetalle();        
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
            if ($model->validate()) {
                $table = new IngresosDeduccionesDetalle();
                $table->id_empleado = $model->id_empleado;
                $table->id_ingreso = $id;
                $table->codigo_salario = $model->codigo_salario;
                $table->suma_resta = 1;
                $table->valor_pagado = $model->valor_pagado;
                $table->observacion = $model->observacion;
                $table->fecha_inicio = $fecha_inicio;
                $table->fecha_corte = $fecha_corte;
                if ($table->save(false)) {
                    $this->redirect(["ingresos-deducciones/view", 'id' =>$id, 'fecha_corte' => $fecha_corte,'fecha_inicio' => $fecha_inicio]);
                } else {
                    $msg = "error";
                }
            } else {
                $model->getErrors();
            }
        }
        return $this->render('_formadicion', ['model' => $model, 'id'=> $id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio]);
    }

    //crear descuentos a los empleados
        
     public function actionCreatedescuento($id, $fecha_corte, $fecha_inicio) {        
        $model = new IngresosDeduccionesDetalle();        
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
            if ($model->validate()) {
                $table = new IngresosDeduccionesDetalle();
                $table->id_empleado = $model->id_empleado;
                $table->id_ingreso = $id;
                $table->codigo_salario = $model->codigo_salario;
                $table->suma_resta = 2;
                $table->fecha_inicio = $fecha_inicio;
                $table->fecha_corte = $fecha_corte;
                $table->valor_pagado = $model->valor_pagado;
                $table->observacion = $model->observacion;
                if ($table->save(false)) {
                    $this->redirect(["ingresos-deducciones/view", 'id'=> $id, 'fecha_corte' => $fecha_corte,'fecha_inicio' => $fecha_inicio]);
                } else {
                    $msg = "error";
                }
            } else {
                $model->getErrors();
            }
        }
        return $this->render('_formdescuento', [
            'model' => $model,
            'id'=> $id,
            'fecha_corte' => $fecha_corte,
            'fecha_inicio' => $fecha_inicio]);
    }
    /**
     * Updates an existing IngresosDeducciones model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_ingreso,'fecha_inicio' => $model->fecha_inicio, 'fecha_corte' => $model->fecha_corte]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
     //permite modificar las adiciones y descuento de la tabla adicionpagopermanente
     public function actionUpdatevista($id, $id_detalle, $fecha_corte, $fecha_inicio)
    {
       $model = IngresosDeduccionesDetalle::findOne($id_detalle);
       if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
      
        if ($model->load(Yii::$app->request->post())) {            
            $table = IngresosDeduccionesDetalle::find()->where(['id_detalle' =>$id_detalle])->one();
            if ($table) {
                $table->codigo_salario = $model->codigo_salario;
                $table->valor_pagado = $model->valor_pagado;
                $table->observacion = $model->observacion;
                $table->id_empleado = $model->id_empleado;
                $table->save(false);
                return $this->redirect(['view','id' =>$id, 'fecha_corte' => $fecha_corte,'fecha_inicio' => $fecha_inicio]); 
            }
        }
       
        return $this->render('updatevista', [
            'model' => $model,
            'id'=>$id,
            'fecha_corte' => $fecha_corte,
            'fecha_inicio' => $fecha_inicio,
        ]);
    }
    
    /**
     * Deletes an existing IngresosDeducciones model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEliminar_adicional($id, $id_detalle, $fecha_inicio, $fecha_corte) 
    {
        if (Yii::$app->request->post()) {
            if ((int) $id_detalle) {
                try {
                    IngresosDeduccionesDetalle::deleteAll("id_detalle=:id_detalle", [":id_detalle" => $id_detalle]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    return $this->redirect(["ingresos-deducciones/view",'id' => $id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]);
                } catch (IntegrityException $e) {
                     Yii::$app->getSession()->setFlash('error', 'Error al eliminar el registro porque esta asociado en otro proceso');
                       return $this->redirect(["ingresos-deducciones/view",'id' => $id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]);
                } catch (\Exception $e) {

                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el registro porque esta asociado en otro proceso');
                      return $this->redirect(["ingresos-deducciones/view",'id' => $id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]);
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el registros, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("ingresos-deducciones/index") . "'>";
            }
        } else {
            return $this->redirect(["pago-adicional-fecha/index"]);
        }
    }
    
    //cerrar el proceso de ingresos y deduciones
    public function actionProceso_cerrado($id) {
        $model = $this->findModel($id);
        if(IngresosDeduccionesDetalle::find()->where(['=','id_ingreso', $id])->one()){
            $model->estado_proceso = 1;
            $model->save();
            return $this->redirect(["ingresos-deducciones/index"]);
        }else{
           Yii::$app->getSession()->setFlash('error', 'Debe de ingresar los registros al proceso.');
           return $this->redirect(['ingresos-deducciones/index']);
        }    
    }

    /**
     * Finds the IngresosDeducciones model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IngresosDeducciones the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = IngresosDeducciones::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
   public function actionExportar_registros($id) {      
        
        $detalle = \app\models\IngresosDeduccionesDetalle::find()->where(['=','id_ingreso', $id])->orderBy('id_empleado DESC')->all();
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
       
                               
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'DOCUMENTO')
                    ->setCellValue('C1', 'EMPLEADO')
                    ->setCellValue('D1', 'CONCEPTO SALARIAL')
                    ->setCellValue('E1', 'FECHA INICIO')                    
                    ->setCellValue('F1', 'FECHA CORTE')
                    ->setCellValue('G1', 'FECHA HORA PROCESO')
                    ->setCellValue('H1', 'VALOR PAGADO')
                    ->setCellValue('I1', 'USER NAME')
                    ->setCellValue('J1', 'OBSERVACIONES');
                 
        $i = 2;
        
        foreach ($detalle as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_detalle)
                    ->setCellValue('B' . $i, $val->empleado->identificacion)
                    ->setCellValue('C' . $i, $val->empleado->nombrecorto)
                    ->setCellValue('D' . $i, $val->codigoSalario->nombre_concepto)
                    ->setCellValue('E' . $i, $val->ingreso->fecha_inicio)
                    ->setCellValue('F' . $i, $val->ingreso->fecha_corte)                    
                    ->setCellValue('G' . $i, $val->ingreso->fecha_hora_proceso)
                    ->setCellValue('H' . $i, $val->valor_pagado)
                    ->setCellValue('I' . $i, $val->ingreso->user_name)
                    ->setCellValue('J' . $i, $val->observacion);
                   
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('ingresos_deducciones');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ingresos_deducciones.xlsx"');
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
