<?php

namespace app\controllers;

use Yii;
use app\models\Empleado;
use app\models\Contrato;
use app\models\ContratoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\FormContratoNuevo;
use app\models\FormContratoNuevoEmpleado;
use app\models\FormFiltroContrato;
use app\models\FormCerrarContrato;
use app\models\TipoContrato;
use app\models\UsuarioDetalle;
use app\models\FormCambioSalario;
use app\models\CambioSalario;
use app\models\FormNuevaProrroga;
use app\models\ProrrogaContrato;
use app\models\PagoAdicionSalario;
use app\models\FormNuevaAdicion;
use app\models\GrupoPago;
use app\models\FormPrestacionesSociales;
use app\models\PrestacionesSociales;
use app\models\ProgramacionNomina;
use app\models\CambioEps;
use app\models\CambioPension;
use app\models\FormParametroContrato;
//clases
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
class ContratoController extends Controller
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
    public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',64])->all()){
                $form = new FormFiltroContrato;
                $identificacion = null;
                $activo = null;
                $id_grupo_pago = null;
                $id_empleado = null;
                $id_tiempo = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $identificacion = Html::encode($form->identificacion);
                        $activo = Html::encode($form->activo);
                        $id_grupo_pago = Html::encode($form->id_grupo_pago);
                        $id_empleado = Html::encode($form->id_empleado);
                        $id_tiempo = Html::encode($form->id_tiempo);
                        $table = Contrato::find()
                                ->andFilterWhere(['like', 'identificacion', $identificacion])
                                ->andFilterWhere(['=', 'contrato_activo', $activo])
                                ->andFilterWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                                ->andFilterWhere(['=', 'id_empleado', $id_empleado])
                                ->andFilterWhere(['=', 'id_tiempo', $id_tiempo])
                                ->orderBy('id_contrato desc');
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 30,
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
                    $table = Contrato::find()
                            ->orderBy('id_contrato desc');
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 30,
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
     * Displays a single Empleado model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
       $adicion_contrato = PagoAdicionSalario::find()->where(['=','id_contrato',$id])->orderBy('id_pago_adicion DESC')->all();  
       $cambio_salario = CambioSalario::find()->where(['=','id_contrato',$id])->orderBy('id_cambio_salario DESC')->all();
       $prorrogas = ProrrogaContrato::find()->where(['=','id_contrato',$id])->orderBy('id_prorroga_contrato DESC')->all();
       $contador_adicion = count($adicion_contrato);
       $cont = count($prorrogas);
       $registros = count($cambio_salario);
       if(Yii::$app->request->post())
        {
            $intIndice = 0;
            if (isset($_POST["seleccion"])) {
                foreach ($_POST["seleccion"] as $intCodigo)
                {
                   // $abono = Credito::findOne($intCodigo);                    
                    //if(CambioSalario::deleteAll("id_cambio_salario=:id_cambio_salario", [":id_cambio_salario" => $intCodigo]))
                    //{                        
                    //} 
                }
                 return $this->redirect(['contrato/view', 'id' => $id]);
            }
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'id' => $id,
            'cambio_salario' => $cambio_salario,
            'registros' => $registros,
            'prorrogas' => $prorrogas,
            'cont' => $cont,
            'contador_adicion' => $contador_adicion,
            'adicion_contrato' => $adicion_contrato,
            
            ]);
    }
    
      public function actionViewparameters($id)
    {
      $cambioeps = CambioEps::find()->where(['=','id_contrato', $id])->orderBy('id_cambio ASC')->all();  
      $cambiopension = CambioPension::find()->where(['=','id_contrato', $id])->orderBy('id_cambio ASC')->all();
       if(Yii::$app->request->post())
        {
            $intIndice = 0;
            if (isset($_POST["seleccion"])) {
                foreach ($_POST["seleccion"] as $intCodigo)
                {
                   // $abono = Credito::findOne($intCodigo);                    
                    //if(CambioSalario::deleteAll("id_cambio_salario=:id_cambio_salario", [":id_cambio_salario" => $intCodigo]))
                    //{                        
                    //} 
                }
                 return $this->redirect(['contrato/viewParameters', 'id' => $id]);
            }
        }

        return $this->render('viewParameters', [
            'model' => $this->findModel($id),
            'id' => $id,
            'cambioeps' => $cambioeps,
            'cambiopension' => $cambiopension,
            ]);
    }
   
    /**
     * Creates a new Empleado model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        if ($id == null){
            $model = new FormContratoNuevo();
        }else{
            $model = new FormContratoNuevoEmpleado();
        }        
        $msg = "";
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {                
                if ($id == null){
                   $empleado = Empleado::findOne($model->id_empleado); 
                   $identificacion = $empleado->identificacion;                   
                   $id_empleado = $model->id_empleado;
                   $tipocontrato = TipoContrato::findone($model->id_tipo_contrato);
                   $genera_prorroga = $tipocontrato->prorroga;
                   $tipo = $tipocontrato->prefijo;
                }else{
                   $empleado = Empleado::findOne($id);
                   $identificacion = $empleado->identificacion;
                   $id_empleado = $id;
                   $tipocontrato = TipoContrato::findone($model->id_tipo_contrato);
                   $genera_prorroga = $tipocontrato->prorroga;
                   $tipo = $tipocontrato->prefijo;
                }
                $table = new Contrato();
                $table->id_tipo_contrato = $model->id_tipo_contrato;
                $table->id_tiempo = $model->id_tiempo;
                $table->id_cargo = $model->id_cargo;
                $table->identificacion = $identificacion;
                $table->id_empleado = $id_empleado;
                $table->descripcion = $model->descripcion;
                $table->fecha_inicio = $model->fecha_inicio;
                $table->usuario_creador = Yii::$app->user->identity->username;
                $grupo_pago = GrupoPago::find()->where(['=','id_grupo_pago', $model->id_grupo_pago])->one();
                if ($model->salario > $grupo_pago->limite_devengado){
                    Yii::$app->getSession()->setFlash('error', 'Error de salario: El salario del empleado es mayor al permitido en este GRUPO DE PAGO, favor consulte con el administrador');
                }else{
                    $fecha_inicio_contrato = strtotime($model->fecha_inicio);
                    $fecha_ultima_nomina = strtotime($grupo_pago->ultimo_pago_nomina);
                    if($fecha_inicio_contrato < $fecha_ultima_nomina){
                        Yii::$app->getSession()->setFlash('error', 'Error de fechas: La fecha de ingreso es menor a la ultima fecha de pago de nomina, favor revisar la fecha de inicio de contrato!');
                    }else{
                        if($tipo != 'CAI' && $model->fecha_final == '' ){
                            Yii::$app->getSession()->setFlash('error', 'Campo vacio: La fecha final no puede ser vacia!');
                        }else{
                            if($tipo == 'CAI'){
                                echo  $table->fecha_final = '2099-12-31';
                            }else{
                                $table->fecha_final = $model->fecha_final;
                            } 
                            $table->tipo_salario = $model->tipo_salario;
                            $table->salario = $model->salario;                
                            $table->auxilio_transporte = $model->auxilio_transporte;
                            $table->horario_trabajo = $model->horario_trabajo;
                            $table->comentarios = $model->comentarios;
                            $table->funciones_especificas = $model->funciones_especificas;
                            $table->id_tipo_cotizante = $model->id_tipo_cotizante;
                            $table->id_subtipo_cotizante = $model->id_subtipo_cotizante;                
                            $table->id_eps = $model->id_eps;
                            $table->id_entidad_salud = $model->id_entidad_salud;
                            $table->id_pension = $model->id_pension;
                            $table->id_entidad_pension = $model->id_entidad_pension;                
                            $table->id_caja_compensacion = $model->id_caja_compensacion;
                            $table->id_cesantia = $model->id_cesantia;
                            $table->id_arl = $model->id_arl;
                            $table->ciudad_laboral = $model->ciudad_laboral;
                            $table->ciudad_contratado = $model->ciudad_contratado;
                            $table->contrato_activo = 1;
                            $table->id_centro_trabajo = $model->id_centro_trabajo;
                            $table->id_grupo_pago = $model->id_grupo_pago;
                            $table->ultimo_pago = $model->fecha_inicio;
                            $table->ultima_prima = $model->fecha_inicio;
                            $table->ultima_cesantia = $model->fecha_inicio;
                            $table->ultima_vacacion = $model->fecha_inicio;
                            if($genera_prorroga == 1){
                                $table->genera_prorroga = 1;  
                                $total_dias = strtotime($model->fecha_final ) - strtotime($model->fecha_inicio);
                                $table->dias_contrato = round($total_dias / 86400)+1; 
                                // formula que resta 31 dias
                                $fecha = date($model->fecha_final);
                                $date_dato = strtotime('-31 day', strtotime($fecha));
                                $date_dato = date('Y-m-d', $date_dato);
                               //termina
                                $table->fecha_preaviso = $date_dato;
                            }
                            $contratado = Contrato::find()
                                    ->where(['id_empleado' => $id])
                                    ->andWhere(['=','contrato_activo',1])
                                    ->all();
                            if (!$contratado){
                              if ($table->save(false)) {
                                    $empleado->fechaingreso = $table->fecha_inicio;
                                    $empleado->fecharetiro = $table->fecha_final;
                                    $empleado->contrato = $table->contrato_activo;
                                    $empleado->save(false);
                                    $this->redirect(["contrato/index"]);
                                } else {
                                    $msg = "error";
                                }
                            }else{
                                $msg = "Ya tiene un contrato activo en el sistema, no se puede crear";
                            }  
                        }    
                    }    
                }
                
            } else {
                $model->getErrors();
            }                       
        }
        
        return $this->render('create', [
            'model' => $model,
            'msg' => $msg,
            'id' => $id,
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
        //$matriculaempresa = Matriculaempresa::findOne(1);
        $model = new FormContratoNuevoEmpleado();
        $msg = null;
        $tipomsg = null;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
            if ($model->validate()) {
                $table = Contrato::find()->where(['id_contrato' => $id])->one();
                if ($table) {
                    $prorroga = ProrrogaContrato::find()->where(['=','id_contrato', $id])->one();
                    if($prorroga)
                    {
                        Yii::$app->getSession()->setFlash('error', 'Error al modificar: No se puede modificar el contrato por que tiene prorrogas generadas');
                    }else{
                        $grupo_pago = GrupoPago::find()->where(['=','id_grupo_pago', $model->id_grupo_pago])->one();
                        if ($grupo_pago->limite_devengado < $model->salario){
                            Yii::$app->getSession()->setFlash('error', 'Error de salario: El salario del empleado es mayor al permitido en este GRUPO DE PAGO, favor consulte con el administrador.');
                        }else{
                            $table->id_tipo_contrato = $model->id_tipo_contrato;
                            $table->id_tiempo = $model->id_tiempo;
                            $table->id_cargo = $model->id_cargo;
                            $table->descripcion = $model->descripcion;
                            $table->fecha_inicio = $model->fecha_inicio;
                            $table->tipo_salario = $model->tipo_salario;
                            $table->salario = $model->salario;                
                            $table->auxilio_transporte = $model->auxilio_transporte;
                            $table->horario_trabajo = $model->horario_trabajo;
                            $table->comentarios = $model->comentarios;
                            $table->funciones_especificas = $model->funciones_especificas;
                            $table->id_tipo_cotizante = $model->id_tipo_cotizante;
                            $table->id_subtipo_cotizante = $model->id_subtipo_cotizante;                
                            $table->id_eps = $model->id_eps;
                            $table->id_entidad_salud = $model->id_entidad_salud;
                            $table->id_pension = $model->id_pension;
                            $table->id_entidad_pension = $model->id_entidad_pension;                
                            $table->id_caja_compensacion = $model->id_caja_compensacion;
                            $table->id_cesantia = $model->id_cesantia;
                            $table->id_arl = $model->id_arl;
                            $table->ciudad_laboral = $model->ciudad_laboral;
                            $table->ciudad_contratado = $model->ciudad_contratado;
                            $table->id_centro_trabajo = $model->id_centro_trabajo;
                            $table->id_grupo_pago = $model->id_grupo_pago;
                            $table->usuario_editor = Yii::$app->user->identity->username;
                            $fechaActual = $fechaActual = date('Y-m-d H:i:s');
                            $table->fecha_editado = $fechaActual;
                            $tipo_contrato = TipoContrato::find()->where(['=','id_tipo_contrato', $model->id_tipo_contrato])->one();
                            if( $tipo_contrato->prorroga == 1){
                               $table->fecha_final = $model->fecha_final;
                               $table->genera_prorroga = 1;  
                               $total_dias = strtotime($model->fecha_final ) - strtotime($model->fecha_inicio);
                               $table->dias_contrato = round($total_dias / 86400)+1; 
                               // formula que resta 31 dias
                               $fecha = date($model->fecha_final);
                               $date_dato = strtotime('-31 day', strtotime($fecha));
                               $date_dato = date('Y-m-d', $date_dato);
                              //termina
                               $table->fecha_preaviso = $date_dato;

                            }
                            if($tipo_contrato->prefijo == 'CAI'){
                                 $table->fecha_final = '2099-12-31';
                                 $table->genera_prorroga = 0;
                                 $table->dias_contrato = 0;
                                 $table->fecha_preaviso = '0000-00-00'; 
                            }

                            if ($table->save(false)) {
                                $msg = "El registro ha sido actualizado correctamente";
                               $this->redirect(["contrato/index"]);
                            } else {
                                $msg = "El registro no sufrio ningun cambio";
                                $tipomsg = "danger";
                            }
                        }    
                    }    
                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }// termina codigo de ingres a la instancia
            } else {
                $model->getErrors();
            }
        }


        if (Yii::$app->request->get("id")) {
            $table = Contrato::find()->where(['id_contrato' => $id])->one();   
            $nomina = ProgramacionNomina::find()->where(['=','id_contrato', $id])->one();
            if($nomina){
                 Yii::$app->getSession()->setFlash('error', 'Este contrato no se puede modificar, porque tiene programaciones de pago asociadas!');  
                 return $this->redirect(["contrato/index"]); 
            }else{     
                if ($table) {                                
                    $model->id_contrato = $table->id_contrato;
                    $model->id_tipo_contrato = $table->id_tipo_contrato;
                    $model->id_tiempo = $table->id_tiempo;
                    $model->id_cargo = $table->id_cargo;
                    $model->descripcion = $table->descripcion;
                    $model->fecha_inicio = $table->fecha_inicio;
                    $model->fecha_final = $table->fecha_final;
                    $model->tipo_salario = $table->tipo_salario;
                    $model->salario = $table->salario;                
                    $model->auxilio_transporte = $table->auxilio_transporte;
                    $model->horario_trabajo = $table->horario_trabajo;
                    $model->comentarios = $table->comentarios;
                    $model->funciones_especificas = $table->funciones_especificas;
                    $model->id_tipo_cotizante = $table->id_tipo_cotizante;
                    $model->id_subtipo_cotizante = $table->id_subtipo_cotizante;                
                    $model->id_eps = $table->id_eps;
                    $model->id_entidad_salud = $table->id_entidad_salud;
                    $model->id_pension = $table->id_pension;
                    $model->id_entidad_pension = $table->id_entidad_pension;                
                    $model->id_caja_compensacion = $table->id_caja_compensacion;
                    $model->id_cesantia = $table->id_cesantia;
                    $model->id_arl = $table->id_arl;
                    $model->ciudad_laboral = $table->ciudad_laboral;
                    $model->ciudad_contratado = $table->ciudad_contratado;
                    $model->id_centro_trabajo = $table->id_centro_trabajo;
                    $model->id_grupo_pago = $table->id_grupo_pago;
                } else {
                    return $this->redirect(["contrato/index"]);
                }
            }    
        } else {
            return $this->redirect(["contrato/index"]);
        }
        return $this->render("update", ["model" => $model, "msg" => $msg, "tipomsg" => $tipomsg]);
    }

    /**
     * Deletes an existing Empleado model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEliminar($id)
    {        
        $contrato = Contrato::findOne($id);
        try {
            $this->findModel($id)->delete();
            $empleado = Empleado::find()->where(['=','id_empleado', $contrato->id_empleado])->one();
            $empleado->contrato = 0;
            $empleado->fechaingreso = '0000-00-00';
            $empleado->fecharetiro = '0000-00-00';
            $empleado->save(false);
            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
            $this->redirect(["contrato/index"]);
        } catch (IntegrityException $e) {
            $this->redirect(["contrato/index"]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el empleado, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el empleado, tiene registros asociados en otros procesos');
            $this->redirect(["contrato/index"]);
        }
    }

    /**
     * Finds the Empleado model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Empleado the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contrato::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    protected function findModelocambio($id_cambio_salario)
    {
    if (($modelocambio = CambioSalario::findOne($id_cambio_salario)) !== null) {
            return $modelocambio;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    protected function findModelootrosi($id_pago_adicion)
    {
    if (($modelootrosi = PagoAdicionSalario::findOne($id_pago_adicion)) !== null) {
            return $modelootrosi;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    protected function findModeloprorroga($id_prorroga_contrato)
    {
    if (($modeloprorroga = ProrrogaContrato::findOne($id_prorroga_contrato)) !== null) {
            return $modeloprorroga;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
       
    public function actionImprimir($id)
    {
        return $this->render('../formatos/contrato', [
            'model' => $this->findModel($id),
            
        ]);
    }
    
    public function actionImprimircambiosalario($id_cambio_salario)
    {
        return $this->render('../formatos/cambioSalario', [
            'modelocambio' => $this->findModelocambio($id_cambio_salario),
            
        ]);
    }
    
    public function actionImprimirotrosi($id_pago_adicion)
    {
        return $this->render('../formatos/contratootrosi', [
            'modelootrosi' => $this->findModelootrosi($id_pago_adicion),
            
        ]);
    }
    
     public function actionImprimirprorroga($id_prorroga_contrato)
    {
        return $this->render('../formatos/contratoprorroga', [
            'modeloprorroga' => $this->findModeloprorroga($id_prorroga_contrato),
            
        ]);
    }
    
    public function actionCerrarcontrato($id) {                
        $model = new FormCerrarContrato();
        $msg = null;
        $tipomsg = null;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
            if ($model->validate()) {
                $contrato = Contrato::findOne($id);
                if (isset($_POST["actualizar"])) {  
                        $contrato->fecha_final = $model->fecha_final;
                        $contrato->id_motivo_terminacion = $model->id_motivo_terminacion;
                        $contrato->contrato_activo = 0;
                        $contrato->observacion = $model->observacion;
                        $contrato->generar_liquidacion = 1;
                        $contrato->usuario_editor = Yii::$app->user->identity->username;
                        $fechaActual = date('Y-m-d H:i:s');
                        $contrato->fecha_editado = $fechaActual;
                        $contrato->save(false);
                        $empleado = Empleado::findOne($contrato->id_empleado);
                        $empleado->contrato = 0;
                        $empleado->fecharetiro = $model->fecha_final;
                        $empleado->save(false);
                        if($contrato->generar_liquidacion == 1){
                            $modelprestacion = new FormPrestacionesSociales();
                            $table = new PrestacionesSociales();  
                            $table->id_empleado = $contrato->id_empleado;
                            $table->id_contrato = $id;
                            $table->documento = $contrato->identificacion;
                            $table->id_grupo_pago = $contrato->id_grupo_pago;
                            $table->fecha_inicio_contrato = $contrato->fecha_inicio;
                            $table->fecha_termino_contrato = $model->fecha_final;
                            $table->ultimo_pago_prima = $contrato->ultima_prima;
                            $table->ultimo_pago_cesantias = $contrato->ultima_cesantia;
                            $table->ultimo_pago_vacaciones = $contrato->ultima_vacacion;
                            $table->observacion = $model->observacion;
                            $table->salario = $contrato->salario;
                            $table->usuariosistema = Yii::$app->user->identity->username;
                            $table->insert(false);
                        }
                        $this->redirect(["contrato/view", 'id' => $id]);                                                     
                }
            }
        }
        if (Yii::$app->request->get("id")) {
            $table = Contrato::find()->where(['id_contrato' => $id])->one();            
            if ($table) {                                
                $model->id_contrato = $table->id_contrato;                
            }
        }
        
        return $this->renderAjax('_cerrarcontrato', ['model' => $model, 'id' => $id]);
    }
    
     public function actionNuevocambiosalario($id)
     { 
        $model = new FormCambioSalario();
        $contrato = Contrato::find()->where(['=','id_contrato',$id])->one();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {           
            if ($model->validate()) {
               if ($contrato){
                   $fecha_contrato = strtotime(date($contrato->ultimo_pago, time()));
                   $fecha_aplica = strtotime($model->fecha_aplicacion);
                   if($fecha_contrato > $fecha_aplica){
                       
                         try{
                            Yii::$app->getSession()->setFlash('error', 'No se puede cambiar el salario, la fecha de aplicacion es menor al ultimo periodo de pago de la nómina!');
                         } catch (Exception $ex) {
                            Yii::$app->getSession()->setFlash('error', 'No se puede cambiar el salario, la fecha de aplicacion es menor al periodo de pago de la nómina!');
                         }
                        
                   }else{      
                        $table = new CambioSalario();
                        $table->nuevo_salario = $model->nuevo_salario;
                        $table->fecha_aplicacion = $model->fecha_aplicacion; 
                        $table->usuariosistema = Yii::$app->user->identity->username; 
                        $table->id_contrato = $id;
                        $table->observacion = $model->observacion;
                        $table->id_formato_contenido = $model->id_formato_contenido;
                        $table->insert();
                        $contrato->salario = $table->nuevo_salario;
                        $contrato->update();
                        $this->redirect(["contrato/view", 'id' => $id]);    
                   }     
                }else{                
                    Yii::$app->getSession()->setFlash('error', 'El Número del contrato no existe!');
                }
            }else{
                 $model->getErrors();
            }    
        }
       return $this->render('_formnuevocambiosalario', [
            'model' => $model,
            'contrato' => $contrato,
            'id' => $id,
         
        ]);
    }
    //Comienzan todos los tabs
    public function actionCambioeps($id)
     { 
        $model = new CambioEps();
        $contrato = Contrato::find()->where(['=','id_contrato',$id])->one();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {    
           
            if ($model->validate()) {
               if ($contrato){
                   if($contrato->id_entidad_salud ==  $model->id_entidad_salud_nueva){
                         try{
                            Yii::$app->getSession()->setFlash('error', 'Debes de seleccionar una nueva eps para el cambio!');
                         } catch (Exception $ex) {
                            Yii::$app->getSession()->setFlash('error', 'Debes de seleccionar una nueva eps para el cambio!');
                         }
                   }else{      
                        $table = new CambioEps();
                        $table->id_contrato = $id;
                        $table->id_entidad_salud_anterior = $contrato->id_entidad_salud; 
                        $table->id_entidad_salud_nueva = $model->id_entidad_salud_nueva;
                        $table->usuariosistema = Yii::$app->user->identity->username; 
                        $table->motivo = $model->motivo;
                        $table->insert();
                        $contrato->id_entidad_salud = $table->id_entidad_salud_nueva;
                        $contrato->update();
                        $this->redirect(["contrato/viewparameters", 'id' => $id]);    
                   }     
                }else{                
                    Yii::$app->getSession()->setFlash('error', 'El Número del contrato no existe!');
                }
            }else{
                 $model->getErrors();
            }    
        }else{
           $model->id_entidad_salud_anterior =  $contrato->id_entidad_salud;
           $model->id_contrato =  $id;
        }
       return $this->render('_formcambioeps', [
            'model' => $model,
            'contrato' => $contrato,
            'id' => $id,
         
        ]);
    }
    
    public function actionCambiopension($id)
     { 
        $model = new CambioPension();
        $contrato = Contrato::find()->where(['=','id_contrato',$id])->one();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {    
           
            if ($model->validate()) {
               if ($contrato){
                   if($contrato->id_entidad_pension ==  $model->id_entidad_pension_nueva){
                         try{
                            Yii::$app->getSession()->setFlash('error', 'Debes de seleccionar una nueva entidad de pension para el cambio!');
                         } catch (Exception $ex) {
                            Yii::$app->getSession()->setFlash('error', 'Debes de seleccionar una nueva entidad de pension para el cambioo!');
                         }
                   }else{      
                        $table = new CambioPension();
                        $table->id_contrato = $id;
                        $table->id_entidad_pension_anterior = $contrato->id_entidad_pension; 
                        $table->id_entidad_pension_nueva = $model->id_entidad_pension_nueva;
                        $table->usuariosistema = Yii::$app->user->identity->username; 
                        $table->motivo = $model->motivo;
                        $table->insert();
                        $contrato->id_entidad_pension = $table->id_entidad_pension_nueva;
                        $contrato->update();
                        $this->redirect(["contrato/viewparameters", 'id' => $id]);    
                   }     
                }else{                
                    Yii::$app->getSession()->setFlash('error', 'El Número del contrato no existe!');
                }
            }else{
                 $model->getErrors();
            }    
        }else{
           $model->id_entidad_pension_anterior =  $contrato->id_entidad_pension;
           $model->id_contrato =  $id;
        }
       return $this->render('_formcambiopension', [
            'model' => $model,
            'contrato' => $contrato,
            'id' => $id,
         
        ]);
    }
    
    public function actionAcumuladodevengado($id) {                
        $model = new FormParametroContrato();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
            if ($model->validate()) {
                $archivo = Contrato::findOne($id);
                if (isset($_POST["actualizar"])) { 
                    $archivo->ibp_prima_inicial = $model->ibp_prima_inicial;
                    $archivo->ibp_cesantia_inicial = $model->ibp_cesantia_inicial;
                    $archivo->ibp_recargo_nocturno = $model->ibp_recargo_nocturno;
                    $archivo->ultima_prima = $model->ultima_prima;
                    $archivo->ultima_cesantia = $model->ultima_cesantia;
                    $archivo->ultima_vacacion = $model->ultima_vacacion;
                    $archivo->ultimo_pago = $model->ultimo_pago;
                     $archivo->fecha_final = $model->fecha_final;
                    $archivo->usuario_editor = Yii::$app->user->identity->username; 
                    $fechaActual = $fechaActual = date('Y-m-d H:i:s');
                    $archivo->fecha_editado = $fechaActual;
                    $archivo->save(false);
                    $this->redirect(["contrato/viewparameters", 'id' => $id]);                                                     
                }
            }
        }
        if (Yii::$app->request->get("id")) {
            $table = Contrato::find()->where(['id_contrato' => $id])->one();            
            if ($table) {                                
                $model->id_contrato = $table->id_contrato;                
                $model->fecha_inicio = $table->fecha_inicio; 
                $model->fecha_final = $table->fecha_final;
                $model->ultima_prima = $table->ultima_prima;
                $model->ultima_cesantia = $table->ultima_cesantia;
                $model->ultima_vacacion = $table->ultima_vacacion;
                $model->ultimo_pago = $table->ultimo_pago;
                $model->ibp_cesantia_inicial = $table->ibp_cesantia_inicial;
                $model->ibp_prima_inicial = $table->ibp_prima_inicial;
                $model->ibp_recargo_nocturno = $table->ibp_recargo_nocturno;
                
            }
            
        }
        
        return $this->renderAjax('_acumulardevengado', ['model' => $model, 'id' => $id]);
    }
    // termina view de los parametros
    public function actionNuevaprorroga($id)
     { 
        $modeloprorroga = new FormNuevaProrroga();

        $contrato = Contrato::find()->where(['=','id_contrato',$id])->one();
        if ($modeloprorroga->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($modeloprorroga);
        }
        if ($modeloprorroga->load(Yii::$app->request->post())) {           
            if ($modeloprorroga->validate()) {
               if ($contrato){
                  
                        $table = new ProrrogaContrato();
                        $table->id_contrato = $id;
                        $table->id_formato_contenido = $modeloprorroga->id_formato_contenido;
                        $table->fecha_desde = $modeloprorroga->fecha_nueva_renovacion; 
                        $dias = $contrato->dias_contrato;
                        //codigo
                        $fecha = date($table->fecha_desde);
                        $date_hasta = strtotime( '+'.($dias).' day', strtotime($fecha)-1);
                        $date_hasta = date('Y-m-d', $date_hasta);
                        //fin
                        $table->fecha_hasta = $date_hasta;
                        $table->fecha_ultima_contrato = $modeloprorroga->fecha_ultima_contrato;
                        //codigi
                        $fecha = date($table->fecha_ultima_contrato);
                        $date_dato = strtotime('+1 day', strtotime($fecha));
                        $date_dato = date('Y-m-d', $date_dato);
                        // fin codigo
                        $table->fecha_nueva_renovacion = $date_dato;
                        //codigo
                        $fecha = date($table->fecha_hasta);
                        $fecha_preaviso = strtotime('-31 day', strtotime($fecha));
                        $fecha_preaviso = date('Y-m-d', $fecha_preaviso);
                        //fin codigo
                        $table->fecha_preaviso = $fecha_preaviso;
                        $table->dias_preaviso = 30;
                        $table->dias_contratados = $dias;
                        $table->usuariosistema = Yii::$app->user->identity->username; 
                        $table->insert(false);
                        $contrato->fecha_final = $table->fecha_hasta;
                        $contrato->fecha_preaviso =  $table->fecha_preaviso;
                        $contrato->update();
                        $this->redirect(["contrato/view", 'id' => $id]);  
                }else{                
                    Yii::$app->getSession()->setFlash('error', 'El Número del contrato no existe!');
                }
            }else{
                 $modeloprorroga->getErrors();
            }    
        }
         if (Yii::$app->request->get("id")) {
            $table = Contrato::find()->where(['id_contrato' => $id])->one();            
            if ($table) {                                
                $modeloprorroga->fecha_desde = $table->fecha_inicio;
                $modeloprorroga->fecha_ultima_contrato = $table->fecha_final;
                // formula que resta 31 dias
                $fecha = date($table->fecha_final);
                $date_dato = strtotime('+1 day', strtotime($fecha));
                $date_dato = date('Y-m-d', $date_dato);
                $modeloprorroga->fecha_nueva_renovacion = $date_dato;
               
            } else {
                return $this->redirect(["contrato/view", 'id' => $id]);
            }
        } else {
            return $this->redirect(["contrato/view", 'id' => $id]);
        }
        $prorroga_contrato = ProrrogaContrato::find()->where(['=','id_contrato', $id])->orderBy('id_prorroga_contrato DESC')->all();
        $tipo = TipoContrato::find()->where(['=','id_tipo_contrato', $table->id_tipo_contrato])->one();
        $contador = count($prorroga_contrato);
        if(($contador < $tipo->nro_prorrogas) && ($tipo->id_tipo_contrato == $table->id_tipo_contrato)){
            return $this->render('_formnuevaprorroga', [
                'modeloprorroga' => $modeloprorroga,
                'contrato' => $contrato,
                'id' => $id,
            ]);
        }else{
            $this->redirect(["contrato/view", 'id' => $id]);  
        }    
    }
    
     public function actionNuevaprorrogaano($id)
     { 
        $modeloprorroga = new FormNuevaProrroga();

        $contrato = Contrato::find()->where(['=','id_contrato',$id])->one();
        if ($modeloprorroga->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($modeloprorroga);
        }
        if ($modeloprorroga->load(Yii::$app->request->post())) {           
            if ($modeloprorroga->validate()) {
               if ($contrato){
                  
                        $table = new ProrrogaContrato();
                        $table->id_contrato = $id;
                        $table->id_formato_contenido = $modeloprorroga->id_formato_contenido;
                        $table->fecha_desde = $modeloprorroga->fecha_nueva_renovacion; 
                        //codigo
                        $fecha = date($table->fecha_desde);
                        $date_hasta = strtotime( '+365 day', strtotime($fecha)-1);
                        $date_hasta = date('Y-m-d', $date_hasta);
                        //fin
                        $table->fecha_hasta = $date_hasta;
                        $table->fecha_ultima_contrato = $modeloprorroga->fecha_ultima_contrato;
                        //codigi
                        $fecha = date($table->fecha_ultima_contrato);
                        $date_dato = strtotime('+1 day', strtotime($fecha));
                        $date_dato = date('Y-m-d', $date_dato);
                        // fin codigo
                        $table->fecha_nueva_renovacion = $date_dato;
                        //codigo
                        $fecha = date($table->fecha_hasta);
                        $fecha_preaviso = strtotime('-31 day', strtotime($fecha));
                        $fecha_preaviso = date('Y-m-d', $fecha_preaviso);
                        //fin codigo
                        $table->fecha_preaviso = $fecha_preaviso;
                        $table->dias_preaviso = 30;
                        $table->dias_contratados = 365;
                        $table->usuariosistema = Yii::$app->user->identity->username; 
                        $table->insert(false);
                        $contrato->fecha_final = $table->fecha_hasta;
                        $contrato->fecha_preaviso =  $table->fecha_preaviso;
                        $contrato->dias_contrato = 365;
                        $contrato->update();
                        $this->redirect(["contrato/view", 'id' => $id]);  
                }else{                
                    Yii::$app->getSession()->setFlash('error', 'El Número del contrato no existe!');
                }
            }else{
                 $modeloprorroga->getErrors();
            }    
        }
         if (Yii::$app->request->get("id")) {
            $table = Contrato::find()->where(['id_contrato' => $id])->one();            
            if ($table) {                                
                $modeloprorroga->fecha_desde = $table->fecha_inicio;
                $modeloprorroga->fecha_ultima_contrato = $table->fecha_final;
                // formula que resta 31 dias
                $fecha = date($table->fecha_final);
                $date_dato = strtotime('+1 day', strtotime($fecha));
                $date_dato = date('Y-m-d', $date_dato);
                $modeloprorroga->fecha_nueva_renovacion = $date_dato;
               
            } else {
                return $this->redirect(["contrato/view", 'id' => $id]);
            }
        } else {
            return $this->redirect(["contrato/view", 'id' => $id]);
        }
       
            return $this->render('_formnuevaprorroga', [
                'modeloprorroga' => $modeloprorroga,
                'contrato' => $contrato,
                'id' => $id,
            ]);
        
    }
    // codigo de adicion al contrato
    
     public function actionNuevaadicioncontrato($id)
     { 
        $modeloadicion = new FormNuevaAdicion();
        $contrato = Contrato::find()->where(['=','id_contrato',$id])->one();

        if ($modeloadicion->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($modeloadicion);
        }
        if ($modeloadicion->load(Yii::$app->request->post())) {           
            if ($modeloadicion->validate()) {
               if ($contrato){
                       $tipo_adicion = PagoAdicionSalario::find()->where(['=','id_contrato', $id])->all();
                        if (!$tipo_adicion){
                            $table = new PagoAdicionSalario();
                            $table->id_contrato = $id;
                            $table->id_formato_contenido = $modeloadicion->id_formato_contenido; 
                            $table->vlr_adicion = $modeloadicion->vlr_adicion; 
                            $table->fecha_aplicacion = $modeloadicion->fecha_aplicacion; 
                            $table->fecha_proceso = $modeloadicion->fecha_proceso; 
                            $table->usuariosistema = Yii::$app->user->identity->username; 
                            $table->codigo_salario = $modeloadicion->codigo_salario; 
                            $table->estado_adicion = 1; 
                            $table->insert(false);
                        }else{
                            $tipo_adicion = PagoAdicionSalario::find()->where(['=','id_contrato', $id])->andWhere(['=','estado_adicion', 1])->one();
                            $table = new PagoAdicionSalario();
                            $table->id_contrato = $id;
                            $table->id_formato_contenido = $modeloadicion->id_formato_contenido; 
                            $table->vlr_adicion = $modeloadicion->vlr_adicion; 
                            $table->fecha_aplicacion = $modeloadicion->fecha_aplicacion; 
                            $table->fecha_proceso = $modeloadicion->fecha_proceso; 
                            $table->usuariosistema = Yii::$app->user->identity->username; 
                            $table->codigo_salario = $modeloadicion->codigo_salario; 
                            $table->estado_adicion = 1; 
                            $table->insert(false);
                            $tipo_adicion->estado_adicion = 0 ;
                            $tipo_adicion->update(FALSE);
                        }   
                        $this->redirect(["contrato/view", 'id' => $id]);  
                }else{                
                    Yii::$app->getSession()->setFlash('error', 'El Número del contrato no existe!');
                }
            }else{
                 $modeloadicion->getErrors();
            }    
        }
            return $this->render('_formnuevaadicionsalario', [
                'modeloadicion' => $modeloadicion,
                'contrato' => $contrato,
                'id' => $id,
            ]);
        
    }
    
    public function actionEditarpagoadicion($id_pago_adicion, $id)
    {
       $modeloadicion = new FormNuevaAdicion();
       if ($modeloadicion->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($modeloadicion);
        }
      
        if ($modeloadicion->load(Yii::$app->request->post())) {            
                $table = PagoAdicionSalario::find()->where(['id_pago_adicion'=>$id_pago_adicion])->one();
                if ($table) {
                    $table->id_formato_contenido = $modeloadicion->id_formato_contenido; 
                    $table->vlr_adicion = $modeloadicion->vlr_adicion; 
                    $table->fecha_aplicacion = $modeloadicion->fecha_aplicacion; 
                    $table->fecha_proceso = $modeloadicion->fecha_proceso; 
                    $table->codigo_salario = $modeloadicion->codigo_salario; 
                    $table->save(false);
                     return $this->redirect(["contrato/view",'id' => $id]);  
                }
        }
       
        if (Yii::$app->request->get('id_pago_adicion')) {
           $table = PagoAdicionSalario::find()->where(['id_pago_adicion'=>$id_pago_adicion])->one();           
            if ($table) {                                
                $modeloadicion->id_formato_contenido = $table->id_formato_contenido;
                $modeloadicion->vlr_adicion = $table->vlr_adicion;
                $modeloadicion->fecha_aplicacion = $table->fecha_aplicacion;
                $modeloadicion->fecha_proceso = $table->fecha_proceso;
                $modeloadicion->codigo_salario = $table->codigo_salario;
            } 
        }
    
        return $this->render('_formnuevaadicionsalario', [
                'modeloadicion' => $modeloadicion,
                'id_pago_adicion' => $id_pago_adicion,
                'id' => $id,
            ]);
    }
    
    public function actionAbrircontrato($id)
    {
      $prestacion = PrestacionesSociales::find()->where(['=','id_contrato', $id])->one();
      if($prestacion){
         Yii::$app->getSession()->setFlash('error', 'Este contrato no se puede abrir porque tiene prestaciones sociales asociadas!');  
         return $this->redirect(["contrato/view",'id' => $id]); 
      }else{
          $contrato = Contrato::findOne($id);
          $contrato->contrato_activo = 1;
          $contrato->id_motivo_terminacion = 0;
          $contrato->generar_liquidacion = 0;
          $contrato->observacion = '';
          $contrato->save(false);
          return $this->redirect(["contrato/view",'id' => $id]); 
      }   
    }        
    
    public function actionParametrocontrato()
    {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',96])->all()){
               $form = new FormFiltroContrato;
                $identificacion = null;
                $id_grupo_pago = null;
                $id_empleado = null;
                $id_tiempo = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $identificacion = Html::encode($form->identificacion);
                        $id_grupo_pago = Html::encode($form->id_grupo_pago);
                        $id_empleado = Html::encode($form->id_empleado);
                        $id_tiempo = Html::encode($form->id_tiempo);
                        $table = Contrato::find()
                                ->andFilterWhere(['like', 'identificacion', $identificacion])
                                ->andFilterWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                                ->andFilterWhere(['=', 'id_empleado', $id_empleado])
                                ->andFilterWhere(['=', 'id_tiempo', $id_tiempo])
                                ->andFilterWhere(['=', 'contrato_activo', 1])
                                ->orderBy('id_contrato desc');
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 30,
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
                    $table = Contrato::find()
                            ->where(['=','contrato_activo', 1])
                            ->orderBy('id_contrato desc');
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 30,
                        'totalCount' => $count->count(),
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                }
                $to = $count->count();
                return $this->render('parametrocontrato', [
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
}