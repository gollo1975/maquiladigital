<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;
//modes

use app\models\ProcesoDisciplinario;
use app\models\UsuarioDetalle;
use app\models\Empleado;
use app\models\Contrato;

/**
 * ProcesoDisciplinarioController implements the CRUD actions for ProcesoDisciplinario model.
 */
class ProcesoDisciplinarioController extends Controller
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
     * Lists all ProcesoDisciplinario models.
     * @return mixed
     */
    public function actionIndex($token = 0) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',160])->all()){
                $form = new \app\models\FiltroProcesoDisciplinario();
                $proceso = null;
                $empleado = null;
                $motivo = null;
                $grupo_pago = null;
                $desde = null;
                $hasta = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $proceso = Html::encode($form->proceso);
                        $empleado = Html::encode($form->empleado);
                        $motivo = Html::encode($form->motivo);
                        $desde = Html::encode($form->desde);
                        $hasta = Html::encode($form->hasta);
                        $grupo_pago = Html::encode($form->grupo_pago);
                        $table = ProcesoDisciplinario::find()
                                ->andFilterWhere(['=', 'id_tipo_disciplinario', $proceso])
                                ->andFilterWhere(['=', 'id_empleado', $empleado])
                                ->andFilterWhere(['between', 'fecha_registro', $desde, $hasta])
                                ->andFilterWhere(['=', 'id_grupo_pago', $grupo_pago])
                                ->andFilterWhere(['=', 'id_motivo', $motivo]);
                        $table = $table->orderBy('id_proceso DESC');
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
                            $this->actionExcelProcesosDisciplinarios($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                }else{
                  
                    $table = ProcesoDisciplinario::find()->orderBy('id_proceso DESC');
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 15,
                        'totalCount' => $count->count(),
                    ]);
                    $tableexcel = $table->all();
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){                    
                            $this->actionExcelProcesosDisciplinarios($tableexcel);
                    }
                } 
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
    
    //CONSULTA DE PROCESOS DISCIPLINARIOS
     public function actionIndex_search($token = 1) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',161])->all()){
                $form = new \app\models\FiltroProcesoDisciplinario();
                $proceso = null;
                $empleado = null;
                $motivo = null;
                $grupo_pago = null;
                $desde = null;
                $hasta = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $proceso = Html::encode($form->proceso);
                        $empleado = Html::encode($form->empleado);
                        $motivo = Html::encode($form->motivo);
                        $desde = Html::encode($form->desde);
                        $hasta = Html::encode($form->hasta);
                        $grupo_pago = Html::encode($form->grupo_pago);
                        $table = ProcesoDisciplinario::find()
                                ->andFilterWhere(['=', 'id_tipo_disciplinario', $proceso])
                                ->andFilterWhere(['=', 'id_empleado', $empleado])
                                ->andFilterWhere(['between', 'fecha_registro', $desde, $hasta])
                                ->andFilterWhere(['=', 'id_grupo_pago', $grupo_pago])
                                ->andFilterWhere(['=', 'id_motivo', $motivo])
                                ->andWhere(['=','proceso_cerrado', 1]);
                        $table = $table->orderBy('id_proceso DESC');
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
                            $this->actionExcelProcesosDisciplinarios($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                }else{
                  
                    $table = ProcesoDisciplinario::find()->Where(['=','proceso_cerrado', 1])->orderBy('id_proceso DESC');
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 15,
                        'totalCount' => $count->count(),
                    ]);
                    $tableexcel = $table->all();
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){                    
                            $this->actionExcelProcesosDisciplinarios($tableexcel);
                    }
                } 
                return $this->render('index_search', [
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
     * Displays a single ProcesoDisciplinario model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token)
    {
        $model = ProcesoDisciplinario::findOne($id);
        if(isset($_POST["actualizar_llamado"])){
            if(isset($_POST["observacion"])){
                
            }
        }    
        return $this->render('view', [
            'model' => $model,
            'token' => $token,
        ]);
    }
    
    //nuevo documento
    public function actionCrear_documento_nuevo() {
        $model = new \app\models\ModeloNuevoProceso();
        $conproceso = ArrayHelper::map(\app\models\TipoProcesoDisciplinario::find()->where(['<>','id_formato_contenido', 'Null'])->all(), 'id_tipo_disciplinario', 'concepto');
        $conmotivo = ArrayHelper::map(\app\models\MotivoDisciplinario::find()->orderBy('concepto ASC')->all(), 'id_motivo', 'concepto');
        if ($model->load(Yii::$app->request->post())) {
            
            if ($model->validate()) {
               
                if (isset($_POST["crear_proceso"])) {
                    if($model->cedula <> null){
                        $conempleado = Empleado::find()->where(['=','identificacion', $model->cedula])->andWhere(['=','contrato', 1])->one(); 
                        if($conempleado){
                   
                           $concontrato = Contrato::find()->where(['=','id_empleado', $conempleado->id_empleado])->andWhere(['=','contrato_activo', 1])->one();
                           $formato = \app\models\TipoProcesoDisciplinario::findOne($model->proceso);
                           $table = new ProcesoDisciplinario();
                           $table->id_empleado = $conempleado->id_empleado;
                           $table->id_contrato = $concontrato->id_contrato;
                           $table->id_tipo_disciplinario = $model->proceso;
                           $table->id_motivo = $model->motivo;
                           $table->descripcion_proceso = $formato->formatoContenido->contenido;
                           $table->fecha_registro = date('Y-m-d');
                           $table->user_name = Yii::$app->user->identity->username;
                           $table->save(false);
                           $registro = ProcesoDisciplinario::find()->orderBy('id_proceso DESC')->one();
                           return $this->redirect(["proceso-disciplinario/view",'id' => $registro->id_proceso,'token' => 0]);
                        }else{
                            Yii::$app->getSession()->setFlash('warning', 'Este empleado NO existe o se encuentra INACTIVO laboralmente.');
                            return $this->redirect(["proceso-disciplinario/index"]);
                        }
                    }else{
                        Yii::$app->getSession()->setFlash('error', 'El campo CEDULA no puede ser vacio');
                         return $this->redirect(["proceso-disciplinario/index"]);
                    } 
                   
                }
            }else{
                $model->getErrors();
            }
        }
        return $this->renderAjax('_form_proceso_disciplinario', [
            'model' => $model,
            'conproceso' => $conproceso,
            'conmotivo' => $conmotivo,
            
        ]);   
    }
    
    //actualizar llamado de atencion
    public function actionActualizar_texto_llamado($id, $token) {
        $model = new ProcesoDisciplinario();
        
        if ($model->load(Yii::$app->request->post())) {
            if(isset($_POST["enviar_detalle"])){
                $table = ProcesoDisciplinario::findOne($id);
                $table->descripcion_proceso = $model->descripcion_proceso;
                $table->save();
                return $this->redirect(["proceso-disciplinario/view",'id' => $id, 'token' => $token]);
            }
        }    
        if (Yii::$app->request->get()) {
            $table = ProcesoDisciplinario::findOne($id);
            $model->descripcion_proceso = $table->descripcion_proceso;
            
        }
        return $this->renderAjax('_editar_llamado_atencion', [
            'model' => $model,
            'table' => $table,
            
        ]);   
    }

    //PROCESO QUE GUARDA LOS DESCARGOS
    //actualizar llamado de atencion
    public function actionActa_descargo_empleado($id, $token) {
        $model = new ProcesoDisciplinario();
        
        if ($model->load(Yii::$app->request->post())) {
            if(isset($_POST["enviar_acta"])){
                $table = ProcesoDisciplinario::findOne($id);
                $table->proceso_descargo  = $model->proceso_descargo ;
                $table->save();
                return $this->redirect(["proceso-disciplinario/view",'id' => $id, 'token' => $token]);
            }
        }    
        if (Yii::$app->request->get()) {
            $table = ProcesoDisciplinario::findOne($id);
            $model->proceso_descargo = $table->proceso_descargo ;
            
        }
        return $this->renderAjax('_crear_acta_descargo', [
            'model' => $model,
            'table' => $table,
            
        ]);   
    }
   
    /**
     * Updates an existing ProcesoDisciplinario model.
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
        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            $contrato = Contrato::find()->where(['=','id_empleado', $model->id_empleado])->andwhere(['=','contrato_activo', 1])->one();
            $table = ProcesoDisciplinario::findOne($id);
            $formato = \app\models\TipoProcesoDisciplinario::findOne($table->id_tipo_disciplinario);
            $table->id_contrato = $contrato->id_contrato;
            $table->id_motivo = $model->id_motivo;
            $table->id_grupo_pago = $contrato->id_grupo_pago;
            $table->descripcion_proceso = $formato->formatoContenido->contenido;
            if($model->fecha_falta <> null){
                $table->save(false);
            }else{
                Yii::$app->getSession()->setFlash('error', 'La fecha de la falta debe de ser OBLIGATORIA. Favor validar la informacion.');
            return $this->redirect(['index']);
            }    
            return $this->redirect(['view', 'id' => $model->id_proceso,'token' => 0]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    //actualizar llamado de atencion
    public function actionActualizar_llamado($id, $token) {
        $table = $this->findModel($id);
        $table->descripcion_proceso = $model->descripcion_proceso;
        $table->save();
        return $this->redirect(['view', 'id' => $id,'token' => $token]);
    }

    //PROCESO PARA AUTORIZAR EL PROCESO DISCIPLINARIO
    public function actionAutorizado($id, $token) {
        $model = $this->findModel($id);
        if($model->fecha_falta <> null){ 
            if($model->autorizado == 0){
               $model->autorizado = 1;
               $model->save();
            }else{
                $model->autorizado = 0;
                $model->save();
            }
             return $this->redirect(['view', 'id' => $id,'token' => $token]);
        }else{
            Yii::$app->getSession()->setFlash('error', 'La fecha de la falta debe de ser OBLIGATORIA. Favor validar la informacion.');
            return $this->redirect(['view', 'id' => $id,'token' => $token]);
        }
        
        
    }

    //GENERAR CONSECUTIVO Y CERRAR PROCESO
    public function actionGenerar_consecutivo($id, $token) {
        $model = $this->findModel($id);
        $codigo = \app\models\Consecutivo::findOne(25);
        $model->numero_radicado = $codigo->consecutivo + 1;
        $model->proceso_cerrado = 1;
        $model->save(false);
        //actualiza
        $codigo->consecutivo = $model->numero_radicado;
        $codigo->save();
        if($model->aplica_suspension == 1){
            $this->CrearLicenciaNoRemunerada($id);
            return $this->redirect(['view', 'id' => $id,'token' => $token]);
        }else{
            return $this->redirect(['view', 'id' => $id,'token' => $token]);
        }     
        
    }
    
   //proceso que crea la licencia no remunerada
    protected function CrearLicenciaNoRemunerada($id) {
        $model = $this->findModel($id);
        $concepto = \app\models\ConfiguracionLicencia::findOne(2);
        if($concepto){
            $table = new \app\models\Licencia();
            $table->codigo_licencia = $concepto->codigo_licencia;
            $table->id_empleado = $model->id_empleado;
            $table->identificacion = $model->empleado->identificacion;
            $table->id_contrato = $model->id_contrato;
            $table->id_grupo_pago =$model->id_grupo_pago;
            $table->fecha_desde = $model->fecha_inicio_suspension;
            $table->fecha_hasta = $model->fecha_final_suspension;
            $table->fecha_aplicacion = $model->fecha_inicio_suspension;
            $total = strtotime($model->fecha_final_suspension ) - strtotime($model->fecha_inicio_suspension);
            $dias = round($total/ 86400)+1;
            $table->vlr_licencia = (($model->contrato->salario / 30)* $dias);
            $table->dias_licencia = $dias;
            $table->afecta_transporte = 1;
            $table->salario = $model->contrato->salario;
            $table->observacion = 'Proceso de suspension';
            $table->usuariosistema = Yii::$app->user->identity->username;
            $table->save(false);
            
        }
        
    }
    
    //imprimir documentos
    public function actionImprimir_proceso($id) {
        $model = $this->findModel($id);
        return $this->render('../formatos/reporte_llamado_atencion', [
           'model' => $model, 
        ]);
    }
    //imprimir documentos
    public function actionImprimir_suspension($id) {
        $model = $this->findModel($id);
        return $this->render('../formatos/reporte_acta_descargo', [
           'model' => $model, 
        ]);
    }
    
    /**
     * Finds the ProcesoDisciplinario model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProcesoDisciplinario the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProcesoDisciplinario::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    //EXCELES
      public function actionExcelProcesosDisciplinarios($tableexcel) {                
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
                               
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Id')
                    ->setCellValue('B1', 'Documento')
                    ->setCellValue('C1', 'Empleado')
                    ->setCellValue('D1', 'Grupo pago')
                    ->setCellValue('E1', 'No contrato')
                    ->setCellValue('F1', 'F. Inicio')
                    ->setCellValue('G1', 'F. Final')
                    ->setCellValue('H1', 'F. Proceso')
                    ->setCellValue('I1', 'Fecha hora registro')
                    ->setCellValue('J1', 'Fecha de la falta')
                    ->setCellValue('K1', 'Tipo de proceso')
                    ->setCellValue('L1', 'Motivo del proceso')
                    ->setCellValue('M1', 'Aplica suspension')
                    ->setCellValue('N1', 'Descripcion del llamado')
                    ->setCellValue('O1', 'Acta de descargo')
                    ->setCellValue('P1', 'Autorizado')
                    ->setCellValue('Q1', 'Proceso cerrado')
                    ->setCellValue('R1', 'User name');
                   
        $i = 2  ;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_proceso)
                    ->setCellValue('B' . $i, $val->empleado->identificacion)
                    ->setCellValue('C' . $i, $val->empleado->nombrecorto)
                    ->setCellValue('D' . $i, $val->grupoPago->grupo_pago)   
                    ->setCellValue('E' . $i, $val->id_contrato)
                    ->setCellValue('F' . $i, $val->fecha_inicio_suspension)
                    ->setCellValue('G' . $i, $val->fecha_final_suspension)
                    ->setCellValue('H' . $i, $val->fecha_registro)
                    ->setCellValue('I' . $i, $val->fecha_hora_proceso)
                    ->setCellValue('J' . $i, $val->fecha_falta)
                    ->setCellValue('K' . $i, $val->tipoDisciplinario->concepto)
                    ->setCellValue('L' . $i, $val->motivo->concepto)
                    ->setCellValue('M' . $i, $val->aplicaSuspension)
                    ->setCellValue('N' . $i, $val->descripcion_proceso)
                    ->setCellValue('O' . $i, $val->proceso_descargo)
                    ->setCellValue('P' . $i, $val->procesoAutorizado)
                    ->setCellValue('Q' . $i, $val->procesoCerrado)
                    ->setCellValue('R' . $i, $val->user_name);
                 
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listados');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Procesos_Disciplinarios.xlsx"');
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
