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
     public function actionIndexempleado() {
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
                return $this->render('indexempleado', [
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
     * Displays a single Empleado model.
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
                $table->nombrecorto = $model->nombre1.' '.$model->nombre2.' '.$model->apellido1.' '.$model->apellido2;                
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
                $table->id_centro_costo = $model->id_centro_costo;
                $table->usuario_crear =  Yii::$app->user->identity->username;
                if ($table->insert()) {
                    $this->redirect(["empleado/indexempleado"]);
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
                    $table->nombrecorto = $table->nombre1.' '.$table->nombre2.' '.$table->apellido1.' '.$table->apellido2;
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
                    $table->id_centro_costo = $model->id_centro_costo;
                    $table->usuario_editar =  Yii::$app->user->identity->username;
                    if ($table->save(false)) {
                        $msg = "El registro ha sido actualizado correctamente";
                        return $this->redirect(["empleado/indexempleado"]);
                    } else {
                        $msg = "El registro no sufrio ningun cambio";
                        $tipomsg = "danger";
                        return $this->redirect(["empleado/indexempleado"]);
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
                $model->id_centro_costo = $table->id_centro_costo;
            } else {
                return $this->redirect(["empleado/indexempleado"]);
            }
        } else {
            return $this->redirect(["empleado/indexempleado"]);
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
    
    public function actionImportar_operarios()
    {
        $operarios = \app\models\Operarios::find()->where(['=','vinculado', 1])
                                                  ->andWhere(['=','estado', 1])->orderBy('nombrecompleto ASC')->all();
        if (isset($_POST["importar"])) {
            $intIndice = 0;
            foreach ($_POST["importar"] as $intCodigo):
                 $operario = Operarios::find()->where(['=','documento', $intCodigo])->one();
                 if($operario){
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
                    $table->iddepartamento = $operario->iddepartamento;
                    $table->idmunicipio = $operario->idmunicipio;
                    $table->iddepartamento = $operario->iddepartamento;
                    $table->fecha_nacimiento = $operario->fecha_nacimiento;
                    $table->contrato = 0;
                    $table->id_horario = $operario->id_horario;
                    $table->id_sucursal = $operario->id_planta;
                    $table->usuario_crear = Yii::$app->user->identity->username;
                    $table->save(false);
                 }
                 $intIndice++;
            endforeach;
            $this->redirect(["empleado/indexempleado"]);
        }
        return $this->render('_importar_operarios', [
            'operarios' => $operarios,            

        ]);
       
    }
    
}
