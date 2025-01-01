<?php

namespace app\controllers;

use app\models\Operarios;
use app\models\OperariosSearchSearch;
use app\models\FormOperarios;
use app\models\UsuarioDetalle;
use app\models\FormFiltroOperarios;
use app\models\MaquinaOperario;
use app\models\TiposMaquinas;
use app\models\FormMaquinaBuscar;
//clases yii
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

/**
 * OperariosController implements the CRUD actions for Operarios model.
 */
class OperariosController extends Controller
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
     * Lists all Operarios models.
     * @return mixed
     */
   public function actionIndex($token = 0) {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 97])->all()) {
                $form = new FormFiltroOperarios();
                $id_operario = null;
                $documento = null;
                $estado = null;
                $vinculado = null;
                $planta = null;
                $tipo_operaria = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_operario = Html::encode($form->id_operario);
                        $documento = Html::encode($form->documento);
                        $estado = Html::encode($form->estado);
                         $tipo_operaria = Html::encode($form->tipo_operaria);
                        $vinculado = Html::encode($form->vinculado);
                        $planta = Html::encode($form->planta);
                        $table = Operarios::find()
                                ->andFilterWhere(['=', 'id_operario', $id_operario])
                                ->andFilterWhere(['=', 'documento', $documento])
                                ->andFilterWhere(['=', 'estado', $estado])
                                ->andFilterWhere(['=','vinculado', $vinculado])
                                ->andFilterWhere(['=','idtipo', $tipo_operaria])
                                ->andFilterWhere(['=','id_planta', $planta]); 
                        $table = $table->orderBy('id_operario DESC');
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
                            $check = isset($_REQUEST['id_operario DESC']);
                            $this->actionExcelconsultaOperarios($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = Operarios::find()
                             ->orderBy('id_operario DESC');
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
                        $this->actionExcelconsultaOperarios($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
                            'token' => $token,
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }

    /**
     * Displays a single Operarios model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token)
    {
        $model = Operarios::findOne($id); 
        $maquina_operario = MaquinaOperario::find()->where(['=','id_operario', $id])->orderBy('id desc')->all();
        if (Yii::$app->request->post()) {
            if (isset($_POST["id_detalle"])) {
                foreach ($_POST["id_detalle"] as $intCodigo) {
                    try {
                        $eliminar = PrestacionesSocialesDetalle::findOne($intCodigo);
                        $eliminar->delete();
                        Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                        $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina, 'token' => $token,]);
                    } catch (IntegrityException $e) {
                        Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle de la prestacion, tiene registros asociados en otros procesos de la nómina');
                    } catch (\Exception $e) {
                        Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle de la prestacion, tiene registros asociados en otros procesos');
                    }
                }
            } else {
                Yii::$app->getSession()->setFlash('warning', 'Debe seleccionar al menos un registro.');
            }
        }
        return $this->render('view', [
                'model' => $this->findModel($id),
                'id' => $id,
                'maquina_operario' => $maquina_operario,
                'token' => $token,
                
        ]);

    }

    /**
     * Creates a new Operarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FormOperarios();        
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $table = new Operarios();
                $table->id_tipo_documento = $model->id_tipo_documento;
                $table->documento = $model->documento;
                $table->nombres = $model->nombres;
                $table->apellidos = $model->apellidos;
                $table->nombrecompleto = $model->nombres.' '.$model->apellidos;      
                $table->celular = $model->celular;
                $table->email = $model->email;
                $table->direccion_operario = $model->direccion;
                $table->iddepartamento = $model->iddepartamento;
                $table->polivalente = $model->polivalente;
                $table->fecha_nacimiento = $model->fecha_nacimiento;
                $table->vinculado = $model->vinculado;
                $table->idtipo = $model->tipo_operaria;
                $table->idmunicipio = $model->idmunicipio;
                $table->fecha_ingreso = $model->fecha_ingreso;
                $table->salario_base = $model->salario;
                $table->aplica_nomina_modulo = $model->nomina_alterna;
                $table->id_arl = $model->id_arl;
                $table->id_horario = $model->id_horario;
                $table->id_planta = $model->id_planta;
                $table->id_banco_empleado = $model->banco;
                $table->tipo_cuenta = $model->tipo_cuenta;
                $table->numero_cuenta = $model->numero_cuenta;
                $table->tipo_transacion = $model->tipo_transacion;
                $table->homologar_document = $model->homologar_document;
                $table->documento_pago_banco = $model->documento_pago_banco;
                $table->usuariosistema =  Yii::$app->user->identity->username;
                if($table->save(false)){;
                   return $this->redirect(["operarios/index"]);
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
     * Updates an existing Operarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = new FormOperarios();
       if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
               $table = Operarios::find()->where(['id_operario' => $id])->one();
               if ($table) {
                   $table->id_tipo_documento = $model->id_tipo_documento;
                   $table->documento = $model->documento;
                   $table->nombres = $model->nombres;
                   $table->apellidos = $model->apellidos;
                   $table->nombrecompleto = $table->nombres.' '.$table->apellidos;
                   $table->celular = $model->celular;
                   $table->email = $model->email;
                   $table->iddepartamento = $model->iddepartamento;
                   $table->idmunicipio = $model->idmunicipio;
                   $table->direccion_operario = $model->direccion;
                   $table->estado = $model->estado;
                   $table->polivalente = $model->polivalente;
                   $table->fecha_nacimiento = $model->fecha_nacimiento;
                   $table->vinculado = $model->vinculado;
                   $table->idtipo = $model->tipo_operaria;
                   $table->fecha_ingreso = $model->fecha_ingreso;
                   $table->salario_base = $model->salario;
                   $table->aplica_nomina_modulo = $model->nomina_alterna;
                   $table->id_arl = $model->id_arl;
                   $table->id_horario= $model->id_horario;
                   $table->id_planta= $model->id_planta;
                   $table->id_banco_empleado = $model->banco;
                   $table->tipo_cuenta = $model->tipo_cuenta;
                   $table->numero_cuenta = $model->numero_cuenta;
                   $table->tipo_transacion = $model->tipo_transacion;
                   $table->homologar_document = $model->homologar_document;
                   $table->documento_pago_banco = $model->documento_pago_banco;
                   $table->save(false);
                    return $this->redirect(["operarios/index"]);
               }
            }else{
                $model->getErrors();
            } 
        }
        if (Yii::$app->request->get("id")) {
            $table = Operarios::find()->where(['id_operario' => $id])->one();
            if ($table) {
                $model->id_tipo_documento = $table->id_tipo_documento;
                $model->documento = $table->documento;
                $model->nombres = $table->nombres;
                $model->apellidos = $table->apellidos;
                $model->celular = $table->celular;
                $model->email = $table->email;
                $model->iddepartamento = $table->iddepartamento;
                $model->idmunicipio = $table->idmunicipio;
                $model->direccion = $table->direccion_operario;
                $model->estado = $table->estado;
                $model->polivalente = $table->polivalente;
                $model->fecha_nacimiento = $table->fecha_nacimiento;
                $model->vinculado = $table->vinculado;
                $model->tipo_operaria = $table->idtipo;
                $model->fecha_ingreso = $table->fecha_ingreso;
                $model->salario = $table->salario_base;
                $model->nomina_alterna = $table->aplica_nomina_modulo;
                $model->id_arl = $table->id_arl;
                $model->id_horario = $table->id_horario;                
                $model->id_planta = $table->id_planta;
                $model->banco = $table->id_banco_empleado;
                $model->tipo_cuenta = $table->tipo_cuenta;
                $model->numero_cuenta = $table->numero_cuenta;
                $model->tipo_transacion =  $table->tipo_transacion; 
                $model->homologar_document = $table->homologar_document;
                $model->documento_pago_banco =  $table->documento_pago_banco;
            }else{
                 return $this->redirect(["operarios/index"]);
            }    
        }else{
             return $this->redirect(["operarios/index"]);
        }    
        return $this->render('update', [
            'model' => $model,
        ]);
    }

     public function actionRelacionmaquina($id, $token)
    {
        $maquinas = TiposMaquinas::find()->where(['=','estado', 1])->orderBy('descripcion asc')->all();
        $form = new FormMaquinaBuscar();
        $q = null;
        $mensaje = '';
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                if ($q){
                    $maquinas = TiposMaquinas::find()
                            ->where(['like','descripcion',$q])
                            ->orwhere(['like','id_tipo',$q])
                            ->orderBy('descripcion asc')
                            ->all();
                }               
            } else {
                $form->getErrors();
            }                    
                    
        } else {
            $maquinas = TiposMaquinas::find()->where(['=','estado', 1])->orderBy('descripcion asc')->all();
        }
        if (isset($_POST["id_tipo"])) {
                $intIndice = 0;
                foreach ($_POST["id_tipo"] as $intCodigo) {
                    $table = new MaquinaOperario();
                    $maquina = TiposMaquinas::find()->where(['id_tipo' => $intCodigo])->one();
                    $detalles = MaquinaOperario::find()
                        ->where(['=', 'id_operario', $id])
                        ->andWhere(['=', 'id_tipo', $maquina->id_tipo])
                        ->all();
                    $reg = count($detalles);
                    if ($reg == 0) {
                        $table->id_operario = $id;
                        $table->id_tipo = $maquina->id_tipo;
                        $table->cantidad = 1;
                        $table->usuariosistema = Yii::$app->user->identity->username;
                        $table->insert();                                                
                    }
                }
                $this->redirect(["operarios/view", 'id' => $id, 'token' => $token]);
            }else{
                
            }
        return $this->render('_formnuevamaquina', [
            'maquinas' => $maquinas,            
            'mensaje' => $mensaje,
            'id' => $id,
            'form' => $form,
            'token' => $token,

        ]);
    }
    /**
     * Deletes an existing Operarios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEliminardetalle($token) {
        if (Yii::$app->request->post()) {
            $id_tipo = Html::encode($_POST["id_tipo"]);
            $id_operario = Html::encode($_POST["id"]);
            if ((int) $id_tipo) {
                               
                try {
                    MaquinaOperario::deleteAll("id=:id", [":id" => $id_tipo]);
                    $this->redirect(["operarios/view", 'id' => $id_operario, 'token' => $token]);
                } catch (IntegrityException $e) {
                    $this->redirect(["operarios/view", 'id' => $id_operario, 'token' => $token]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en los modulos de ensamble');
                } catch (\Exception $e) {
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en los modulos de ensamble');
                     $this->redirect(["operarios/view", 'id' => $id_operario, 'token' => $token]);
                }
              
            } else {
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("operarios/index") . "'>";
            }
        } else {
            return $this->redirect(["operarios/index"]);
        }
    }
    //este proceso actualiza los salarios de los trabajadores
     public function actionActualizarsalarios() {
        $model = new \app\models\FormActualizarSalarioOperario();
         if ($model->load(Yii::$app->request->post())) {
               if ($model->validate()){
                    if (isset($_POST["actualizasalario"])) {
                        $operario = Operarios::find()->where(['=','estado', 1])->all(); 
                        if(count($operario) == 0){
                            $this->redirect(["index"]); 
                        }else{
                            foreach ($operario as $operarios){
                                $operarios->salario_base = $model->nuevo_salario;
                                $operarios->update();
                            }
                            $this->redirect(["index"]); 
                        }    
                    }
               } 
         }
        return $this->renderAjax('actualizarsalarios', [
            'model' => $model,       
        ]);    
    }
    
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Operarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Operarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Operarios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
     public function actionMunicipio($id) {
        $rows = Municipio::find()->where(['iddepartamento' => $id])->all();

        echo "<option required>Seleccione...</option>";
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                echo "<option value='$row->idmunicipio' required>$row->municipio</option>";
            }
        }
    }
    
     public function actionExcelconsultaOperarios($tableexcel) {                
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
                    ->setCellValue('A1', 'CODIGO')
                    ->setCellValue('B1', 'TIPO DOCUMENTO')
                    ->setCellValue('C1', 'DOCUMENTO')
                    ->setCellValue('D1', 'NOMBRES')
                    ->setCellValue('E1', 'DEPARTAMENTO')                    
                    ->setCellValue('F1', 'MUNICIPIO')
                    ->setCellValue('G1', 'CELULAR')
                    ->setCellValue('H1', 'EMAIL')
                    ->setCellValue('I1', 'F. NACIMIENTO')
                    ->setCellValue('J1', 'FECHA CREACION')
                    ->setCellValue('K1', 'ACTIVO')
                    ->setCellValue('L1', 'POLIVALENTE')
                    ->setCellValue('M1', 'FECHA INGRESO')
                    ->setCellValue('N1', 'PLANTA')
                    ->setCellValue('O1', 'BANCO')
                    ->setCellValue('P1', 'No CUENTA')
                    ->setCellValue('Q1', 'TIPO DE AREA');
        $i = 2;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_operario)
                    ->setCellValue('B' . $i, $val->tipoDocumento->tipo)
                    ->setCellValue('C' . $i, $val->documento)
                    ->setCellValue('D' . $i, $val->nombrecompleto)                    
                    ->setCellValue('E' . $i, $val->departamento->departamento)
                    ->setCellValue('F' . $i, $val->municipio->municipio)  
                    ->setCellValue('G' . $i, $val->celular)
                    ->setCellValue('H' . $i, $val->email)
                    ->setCellValue('I' . $i, $val->fecha_nacimiento)
                    ->setCellValue('J' . $i, $val->fecha_creacion)
                    ->setCellValue('K' . $i, $val->estadopago)
                    ->setCellValue('L' . $i, $val->polivalenteOperacion)
                    ->setCellValue('M' . $i, $val->fecha_ingreso)
                    ->setCellValue('N' . $i, $val->planta->nombre_planta)
                    ->setCellValue('O' . $i, $val->bancoEmpleado->banco)
                    ->setCellValue('P' . $i, $val->numero_cuenta)
                     ->setCellValue('Q' . $i, $val->tipoOperaria->tipo);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Operarios');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Operarios.xlsx"');
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
