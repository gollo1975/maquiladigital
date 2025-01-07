<?php

namespace app\controllers;


use app\models\Empleado;
use app\models\EmpleadoSearch;
use app\models\FormEmpleado;
use app\models\UsuarioDetalle;
use app\models\FormFiltroEmpleado;
use app\models\Operarios;
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

/**
 * EmpleadoController implements the CRUD actions for Empleado model.
 */
class EmpleadoController extends Controller
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
     * Lists all Empleado models.
     * @return mixed
     */
    // INDEX DE CREACION DE EMPLEADPS
    public function actionIndex($token = 0) {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 31])->all()) {
                $form = new FormFiltroEmpleado();
                $id_empleado = null;
                $identificacion = null;
                $fecha_desde = null;
                $fecha_hasta = null;
                $contrato = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_empleado = Html::encode($form->id_empleado);
                        $identificacion = Html::encode($form->identificacion);
                        $fecha_desde = Html::encode($form->fechaingreso);
                        $fecha_hasta = Html::encode($form->fecharetiro);
                        $contrato = Html::encode($form->contrato);
                        $table = Empleado::find()
                                ->andFilterWhere(['=', 'id_empleado', $id_empleado])
                                ->andFilterWhere(['=', 'identificacion', $identificacion])
                                ->andFilterWhere(['between', 'fechaingreso', $fecha_desde, $fecha_hasta])
                                ->andFilterWhere(['=', 'contrato', $contrato]);
                        $table = $table->orderBy('id_empleado DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 20,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_empleado DESC']);
                            $this->actionExcelconsultaEmpleado($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = Empleado::find()
                             ->orderBy('id_empleado DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 20,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelconsultaEmpleado($tableexcel);
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
    
   // INDEX DE CONSULTA DE EMPLEADO
    
     public function actionIndexconsulta($token = 1) {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 132])->all()) {
                $form = new FormFiltroEmpleado();
                $id_empleado = null;
                $identificacion = null;
                $fecha_desde = null;
                $fecha_hasta = null;
                $contrato = null;
                $modelo = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_empleado = Html::encode($form->id_empleado);
                        $identificacion = Html::encode($form->identificacion);
                        $fecha_desde = Html::encode($form->fechaingreso);
                        $fecha_hasta = Html::encode($form->fecharetiro);
                        $contrato = Html::encode($form->contrato);
                        $table = Empleado::find()
                                ->andFilterWhere(['=', 'id_empleado', $id_empleado])
                                ->andFilterWhere(['=', 'identificacion', $identificacion])
                                ->andFilterWhere(['>=', 'fechaingreso', $fecha_desde])
                                ->andFilterWhere(['<=', 'fecharetiro', $fecha_hasta])
                               ->andFilterWhere(['=', 'contrato', $contrato]);
                        $table = $table->orderBy('id_empleado DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 20,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_empleado DESC']);
                            $this->actionExcelconsultaEmpleado($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                   $table = 0;
                    $pages = new Pagination([
                        'pageSize' => 20,
                        'totalCount' => 0,
                    ]);
                }
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
     * Displays a single Empleado model.
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
     * Creates a new Empleado model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FormEmpleado();        
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $table = new Empleado();
                $table->id_empleado_tipo = $model->id_empleado_tipo;
                $table->identificacion = $model->identificacion;
                $table->dv = $model->dv;
                $table->nombre1 = $model->nombre1;
                $table->nombre2 = $model->nombre2;
                $table->apellido1 = $model->apellido1;
                $table->apellido2 = $model->apellido2;                
                $table->direccion = $model->direccion;
                $table->telefono = $model->telefono;
                $table->celular = $model->celular;
                $table->email = $model->email;
                $table->iddepartamento = $model->iddepartamento;
                $table->idmunicipio = $model->idmunicipio;
                $table->contrato = 0;
                $table->observacion = $model->observacion;
                $table->nombrecorto = utf8_decode($model->nombre1.' '.$model->nombre2.' '.$model->apellido1.' '.$model->apellido2);                
                $table->id_tipo_documento = $model->id_tipo_documento;
                $table->fecha_expedicion = $model->fecha_expedicion;
                $table->ciudad_expedicion = $model->ciudad_expedicion;
                $table->ciudad_nacimiento = $model->ciudad_nacimiento;
                $table->barrio = $model->barrio;
                $table->id_rh = $model->id_rh;
                $table->sexo = $model->sexo;
                $table->id_estado_civil = $model->id_estado_civil;
                $table->estatura = $model->estatura;
                $table->peso = $model->peso;
                $table->libreta_militar = $model->libreta_militar;
                $table->distrito_militar = $model->distrito_militar;
                $table->fecha_nacimiento = $model->fecha_nacimiento;
                $table->padre_familia = $model->padre_familia;
                $table->cabeza_hogar = $model->cabeza_hogar;
                $table->id_horario = $model->id_horario;
                $table->discapacidad = $model->discapacidad;
                $table->id_banco_empleado = $model->id_banco_empleado;
                $table->tipo_cuenta = $model->tipo_cuenta;
                $table->cuenta_bancaria = $model->cuenta_bancaria;
                $table->tipo_transacion = $model->tipo_transacion;
                $table->id_centro_costo = $model->id_centro_costo;
                $table->id_nivel_estudio = $model->id_nivel_estudio;
                $table->documento_pago_banco = $model->documento_pago_banco;
                $table->homologar_document = $model->homologar_document;
                $table->id_sucursal = 1;
                $table->id_forma_pago = $model->id_forma_pago;
                $table->usuario_crear =  Yii::$app->user->identity->username;
                if ($table->insert()) {
                    $this->redirect(["empleado/index"]);
                } else {
                    $msg = "error";
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
     * Updates an existing Empleado model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
      
        $model = new FormEmpleado();
        $msg = null;
        $tipomsg = null;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
            if ($model->validate()) {
                $table = Empleado::find()->where(['id_empleado' => $id])->one();
                if ($table) {
                    $table->id_empleado_tipo = $model->id_empleado_tipo;
                    $table->identificacion = $model->identificacion;
                    $table->dv = $model->dv;
                    $table->nombre1 = $model->nombre1;
                    $table->nombre2 = $model->nombre2;
                    $table->apellido1 = $model->apellido1;
                    $table->apellido2 = $model->apellido2;
                    $table->nombrecorto = utf8_decode($table->nombre1.' '.$table->nombre2.' '.$table->apellido1.' '.$table->apellido2);
                    $table->direccion = $model->direccion;
                    $table->telefono = $model->telefono;
                    $table->celular = $model->celular;
                    $table->email = $model->email;
                    $table->iddepartamento = $model->iddepartamento;
                    $table->idmunicipio = $model->idmunicipio;
                    $table->observacion = $model->observacion;
                    $table->id_tipo_documento = $model->id_tipo_documento;
                    $table->fecha_expedicion = $model->fecha_expedicion;
                    $table->ciudad_expedicion = $model->ciudad_expedicion;
                    $table->ciudad_nacimiento = $model->ciudad_nacimiento;
                    $table->barrio = $model->barrio;
                    $table->id_rh = $model->id_rh;
                    $table->sexo = $model->sexo;
                    $table->id_estado_civil = $model->id_estado_civil;
                    $table->estatura = $model->estatura;
                    $table->peso = $model->peso;
                    $table->libreta_militar = $model->libreta_militar;
                    $table->distrito_militar = $model->distrito_militar;
                    $table->fecha_nacimiento = $model->fecha_nacimiento;
                    $table->padre_familia = $model->padre_familia;
                    $table->cabeza_hogar = $model->cabeza_hogar;
                    $table->id_horario = $model->id_horario;
                    $table->discapacidad = $model->discapacidad;
                    $table->id_banco_empleado = $model->id_banco_empleado;
                    $table->tipo_cuenta = $model->tipo_cuenta;
                    $table->cuenta_bancaria = $model->cuenta_bancaria;
                    $table->tipo_transacion = $model->tipo_transacion;
                    $table->id_centro_costo = $model->id_centro_costo;
                    $table->id_nivel_estudio = $model->id_nivel_estudio;
                    $table->documento_pago_banco = $model->documento_pago_banco;
                    $table->homologar_document = $model->homologar_document;
                    $table->usuario_editar =  Yii::$app->user->identity->username;
                    $table->id_forma_pago = $model->id_forma_pago;
                    if ($table->save(false)) {
                        $msg = "El registro ha sido actualizado correctamente";
                        return $this->redirect(["empleado/index"]);
                    } else {
                        $msg = "El registro no sufrio ningun cambio";
                        $tipomsg = "danger";
                        return $this->redirect(["empleado/index"]);
                    }
                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }
            } else {
                $model->getErrors();
            }
        }


        if (Yii::$app->request->get("id")) {
            $table = Empleado::find()->where(['id_empleado' => $id])->one();
            if ($table) {
                $model->id_empleado = $table->id_empleado;
                $model->id_empleado_tipo = $table->id_empleado_tipo;
                $model->identificacion = $table->identificacion;
                $model->dv = $table->dv;
                $model->nombre1 = $table->nombre1;
                $model->nombre2 = $table->nombre2;
                $model->apellido1 = $table->apellido1;
                $model->apellido2 = $table->apellido2;
                $model->direccion = $table->direccion;
                $model->telefono = $table->telefono;
                $model->celular = $table->celular;
                $model->email = $table->email;
                $model->iddepartamento = $table->iddepartamento;
                $model->idmunicipio = $table->idmunicipio;
                $model->contrato = $table->contrato;                                                
                $model->observacion = $table->observacion;
                $model->fechaingreso = $table->fechaingreso;
                $model->fecharetiro = $table->fecharetiro;                
                $model->id_tipo_documento = $table->id_tipo_documento;
                $model->fecha_expedicion = $table->fecha_expedicion;
                $model->ciudad_expedicion = $table->ciudad_expedicion;
                $model->ciudad_nacimiento = $table->ciudad_nacimiento;
                $model->barrio = $table->barrio;
                $model->id_rh = $table->id_rh;
                $model->sexo = $table->sexo;
                $model->id_estado_civil = $table->id_estado_civil;
                $model->estatura = $table->estatura;
                $model->peso = $table->peso;
                $model->libreta_militar = $table->libreta_militar;
                $model->distrito_militar = $table->distrito_militar;
                $model->fecha_nacimiento = $table->fecha_nacimiento;
                $model->padre_familia = $table->padre_familia;
                $model->cabeza_hogar = $table->cabeza_hogar;
                $model->id_horario = $table->id_horario;
                $model->discapacidad = $table->discapacidad;
                $model->id_banco_empleado = $table->id_banco_empleado;
                $model->tipo_cuenta = $table->tipo_cuenta;
                $model->cuenta_bancaria = $table->cuenta_bancaria;
                $model->tipo_transacion = $table->tipo_transacion;
                $model->id_centro_costo = $table->id_centro_costo;
                $model->id_nivel_estudio = $table->id_nivel_estudio;
                $model->documento_pago_banco = $table->documento_pago_banco;
                $model->homologar_document = $table->homologar_document;
                $model->id_forma_pago =  $table->id_forma_pago;
            } else {
                return $this->redirect(["empleado/index"]);
            }
        } else {
            return $this->redirect(["empleado/index"]);
        }
        return $this->render("update", ["model" => $model, "msg" => $msg, "tipomsg" => $tipomsg]);
    }

   
    protected function findModel($id)
    {
        if (($model = Empleado::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionMunicipio($id) {
        $rows = Municipio::find()->where(['=','iddepartamento', $id])->all();

        echo "<option required>Seleccione...</option>";
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                echo "<option value='$row->idmunicipio' required>$row->municipio</option>";
            }
        }
    }
    
    public function actionImprimir($id)
    {
                                
        return $this->render('../formatos/empleado', [
            'model' => $this->findModel($id),
            
        ]);
    }
   //PROCESO QUE EXPORTA EMPLEADOS
    
    public function actionImportar_operarios($sw)
    {
        if($sw  == 0){
            $operarios = \app\models\Operarios::find()->where(['=','vinculado', 1])
                                                  ->andWhere(['=','estado', 1])->orderBy('nombrecompleto ASC')->all();
        }else{
            $operarios = \app\models\Operarios::find()->Where(['=','estado', 1])->orderBy('nombrecompleto ASC')->all(); 
        }
        
        if (isset($_POST["importar"])) {
            $intIndice = 0;
            foreach ($_POST["importar"] as $intCodigo):
                 $operario = Operarios::find()->where(['=','documento', $intCodigo])->one();
                 if($operario){
                    if($sw == 0){ 
                        $table = new Empleado();
                        $table->id_empleado_tipo = 1;
                        $table->id_tipo_documento = $operario->id_tipo_documento;
                        $table->identificacion = $operario->documento;
                        $table->nombre1 = $operario->nombres;
                        $table->apellido1 = $operario->apellidos;
                        $table->nombrecorto = $operario->nombrecompleto;
                        $table->direccion = 0;
                        $table->telefono = 0;
                        $table->celular = $operario->celular;
                        $table->email = $operario->email;
                        $table->iddepartamento = $operario->iddepartamento;
                        $table->idmunicipio = $operario->idmunicipio;
                        $table->iddepartamento = $operario->iddepartamento;
                        $table->fecha_nacimiento = $operario->fecha_nacimiento;
                        $table->direccion = $operario->direccion_operario;
                        $table->id_banco_empleado = $operario->id_banco_empleado;
                        $table->tipo_cuenta = $operario->tipo_cuenta;
                        $table->cuenta_bancaria = $operario->numero_cuenta;
                        $table->tipo_transacion = $operario->tipo_transacion;
                        $table->contrato = 0;
                        $table->id_horario = $operario->id_horario;
                        $table->id_sucursal = $operario->id_planta;
                        $table->usuario_crear = Yii::$app->user->identity->username;
                        $table->save(false);
                    }else{
                        $empresa = \app\models\Matriculaempresa::findOne(1);
                        $table = new \app\models\Proveedor();
                        $table->id_tipo_documento = $operario->id_tipo_documento;
                        $table->cedulanit = $operario->documento;
                        $table->dv = 1;
                        $table->nombreproveedor = $operario->nombres;
                        $table->apellidoproveedor = $operario->apellidos;
                        $table->nombrecorto = $operario->nombrecompleto;
                        $table->celularproveedor = $operario->celular;
                        $table->emailproveedor = $operario->email;
                        $table->direccionproveedor = $operario->direccion_operario;
                        $table->iddepartamento = $operario->iddepartamento;
                        $table->idmunicipio = $operario->idmunicipio;
                        $table->formapago = 1;
                        $table->nitmatricula = $empresa->nitmatricula;
                        $table->tiporegimen = 2;
                        $table->autoretenedor = 0;
                        $table->naturaleza = 2;
                        $table->sociedad = 1;
                        $table->tipocuenta = $operario->tipo_cuenta;
                        $table->cuentanumero = $operario->numero_cuenta;
                        $table->genera_moda = 0;
                        $table->save();
                    }    
                 }
                 $intIndice++;
            endforeach;
            if($sw == 0){
                $this->redirect(["empleado/index"]);
            }else{
                $this->redirect(["proveedor/index"]);
            }
            
        }
        return $this->render('_importar_operarios', [
            'operarios' => $operarios,    
            'sw' => $sw,

        ]);
       
    }
    
    public function actionMostrarDepartamentos($id) {

        Yii::$app->response->format = Response::FORMAT_JSON;
        $depto = \app\models\Departamento::find()->andWhere(['iddepartamento' => $id])->all();
        $data = [['id' => '', 'text' => '']];
        foreach ($depto as $deptos) {
            $data[] = ['id' => $deptos->iddepartamento, 'text' => $deptos->departamento];
        }
        return ['data' => $data];
    }
  ////EXPORTAR EMPLEADOS
    public function actionExcelconsultaEmpleado($tableexcel) {                
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
                              
        $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'TIPO EMPLEADO')
                    ->setCellValue('C1', 'TIPO DOCUMENTO')
                    ->setCellValue('D1', 'DOCUMENTO')
                    ->setCellValue('E1', 'NOMBRE 1')
                    ->setCellValue('F1', 'NOMBRE 2')
                    ->setCellValue('G1', 'APELLIDO 1')                    
                    ->setCellValue('H1', 'APELLIDO 2')
                    ->setCellValue('I1', 'FECHA EXPEDICION')
                    ->setCellValue('J1', 'CIUDAD EXPEDICION')
                    ->setCellValue('K1', 'DIRECCION')
                    ->setCellValue('L1', 'TELEFONO')
                    ->setCellValue('M1', 'CELULAR')
                    ->setCellValue('N1', 'EMAIL')
                    ->setCellValue('O1', 'DEPARTAMENTO')
                    ->setCellValue('P1', 'MUNICIPIO')
                    ->setCellValue('Q1', 'BARRIO')
                    ->setCellValue('R1', 'GENERO')
                    ->setCellValue('S1', 'ESTADO CIVIL')
                    ->setCellValue('T1', 'FECHA NACIMIENTO')
                    ->setCellValue('U1', 'CIUDAD NACIMIENTO')
                    ->setCellValue('V1', 'CONTRATO ACTIVO')
                    ->setCellValue('W1', 'FECHA INGRESO')
                    ->setCellValue('X1', 'FECHA RETIRO')
                    ->setCellValue('Y1', 'PADRE FAMILIA')
                    ->setCellValue('Z1', 'CABEZA HOGAR')
                    ->setCellValue('AA1', 'NIVEL ESTUDIO')
                    ->setCellValue('AB1', 'DISCAPACITADO')
                    ->setCellValue('AC1', 'HORARIO')
                    ->setCellValue('AD1', 'BANCO')
                    ->setCellValue('AE1', 'TIPO CUENTA')
                    ->setCellValue('AF1', 'No CUENTA')
                    ->setCellValue('AG1', 'SUCURSAL')
                    ->setCellValue('AH1', 'USUARIO CREACION')
                    ->setCellValue('AI1', 'USUARIO EDITADO')
                    ->setCellValue('AJ1', 'OBSERVACION');
                   
        $i = 2  ;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_empleado)
                    ->setCellValue('B' . $i, $val->empleadoTipo->tipo)
                    ->setCellValue('C' . $i, $val->tipoDocumento->tipo)
                    ->setCellValue('D' . $i, $val->identificacion)
                    ->setCellValue('E' . $i, $val->nombre1)
                    ->setCellValue('F' . $i, $val->nombre2)
                    ->setCellValue('G' . $i, $val->apellido1)                    
                    ->setCellValue('H' . $i, $val->apellido2)
                    ->setCellValue('I' . $i, $val->fecha_expedicion)
                    ->setCellValue('J' . $i, $val->ciudadExpedicion->municipio)
                    ->setCellValue('K' . $i, $val->direccion)
                    ->setCellValue('L' . $i, $val->telefono)
                    ->setCellValue('M' . $i, $val->celular)
                    ->setCellValue('N' . $i, $val->email)
                    ->setCellValue('O' . $i, $val->departamento->departamento)
                    ->setCellValue('P' . $i, $val->municipio->municipio)
                    ->setCellValue('Q' . $i, $val->barrio)
                    ->setCellValue('R' . $i, $val->sexo)
                    ->setCellValue('S' . $i, $val->estadoCivil->estado_civil)
                    ->setCellValue('T' . $i, $val->fecha_nacimiento)
                    ->setCellValue('U' . $i, $val->ciudadNacimiento->municipio)
                    ->setCellValue('V' . $i, $val->contratado)
                    ->setCellValue('W' . $i, $val->fechaingreso)
                    ->setCellValue('X' . $i, $val->fecharetiro)
                    ->setCellValue('Y' . $i, $val->padreFamilia)
                    ->setCellValue('Z' . $i, $val->cabezaHogar);
                    if($val->id_nivel_estudio == '(NULL)'){
                         $objPHPExcel->setActiveSheetIndex(0)
                         ->setCellValue('AA' . $i, 'NO HAY INFORMACION');
                    }else{
                        $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('AA' . $i, $val->nivelEstudioempleado->nive_estudio);
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('AB' . $i, $val->discapacitado)
                    ->setCellValue('AC' . $i, $val->horario->horario)
                    ->setCellValue('AD' . $i, $val->bancoEmpleado->banco)
                    ->setCellValue('AE' . $i, $val->tipocuenta)
                    ->setCellValue('AF' . $i, $val->cuenta_bancaria)
                    ->setCellValue('AG' . $i, $val->sucursalempleado->sucursal)
                    ->setCellValue('AH' . $i, $val->usuario_crear)
                    ->setCellValue('AI' . $i, $val->usuario_editar)
                    ->setCellValue('AJ' . $i, $val->observacion);
                   
                   
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Empleados');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Empleados.xlsx"');
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
