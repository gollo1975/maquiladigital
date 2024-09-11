<?php

namespace app\controllers;
use Yii;
//models
use app\models\Vacaciones;
use app\models\VacacionesSearch;
use app\models\UsuarioDetalle;
use app\models\FormFiltroVacaciones;
use app\models\Contrato;
use app\models\FormVacacion;
use app\models\FormAdicionVacaciones;
use app\models\VacacionesAdicion;
//clases
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\ActiveQuery;
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
use yii\db\ActiveRecord;
use yii\base\Model;

/**
 * VacacionesController implements the CRUD actions for Vacaciones model.
 */
class VacacionesController extends Controller
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
     * Lists all Vacaciones models.
     * @return mixed
     */
   public function actionIndex($token = 0) {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 100])->all()) {
                $form = new FormFiltroVacaciones();
                $id_grupo_pago = null;
                $id_empleado = null;
                $documento = null;
                $fecha_desde = null;
                $fecha_hasta = null;
                $estado_cerrado = null;
                $pagina = 1;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_grupo_pago = Html::encode($form->id_grupo_pago);
                        $id_empleado = Html::encode($form->id_empleado);
                        $documento = Html::encode($form->documento);
                        $fecha_desde = Html::encode($form->fecha_desde);
                        $fecha_hasta = Html::encode($form->fecha_hasta);
                        $estado_cerrado = Html::encode($form->estado_cerrado);
                        $table = Vacaciones::find()
                                ->andFilterWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                                ->andFilterWhere(['=', 'id_empleado', $id_empleado])
                                ->andFilterWhere(['=', 'documento', $documento])
                                ->andFilterWhere(['>=', 'fecha_desde', $fecha_desde])
                                ->andFilterWhere(['<=', 'fecha_hasta', $fecha_hasta])
                                ->andFilterWhere(['=', 'estado_cerrado', $estado_cerrado]);
                        $table = $table->orderBy('id_vacacion DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 40,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_vacacion DESC']);
                            $this->actionExcelfiltrovacacion($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = Vacaciones::find()
                             ->orderBy('id_vacacion DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 40,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelfiltrovacacion($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
                            'pagina' => $pagina,
                            'token' => $token
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }
    
  //inde para busqueda
     public function actionSearchindex() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 101])->all()) {
                $form = new FormFiltroVacaciones();
                $id_grupo_pago = null;
                $id_empleado = null;
                $documento = null;
                $fecha_desde = null;
                $fecha_hasta = null;
                $estado_cerrado = null;
                $pagina = 1;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_grupo_pago = Html::encode($form->id_grupo_pago);
                        $id_empleado = Html::encode($form->id_empleado);
                        $documento = Html::encode($form->documento);
                        $fecha_desde = Html::encode($form->fecha_desde);
                        $fecha_hasta = Html::encode($form->fecha_hasta);
                        $estado_cerrado = Html::encode($form->estado_cerrado);
                        $table = Vacaciones::find()
                                ->andFilterWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                                ->andFilterWhere(['=', 'id_empleado', $id_empleado])
                                ->andFilterWhere(['=', 'documento', $documento])
                                ->andFilterWhere(['>=', 'fecha_desde', $fecha_desde])
                                ->andFilterWhere(['<=', 'fecha_hasta', $fecha_hasta])
                                ->andFilterWhere(['=', 'estado_cerrado', 1]);
                        $table = $table->orderBy('id_vacacion DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 40,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_vacacion DESC']);
                            $this->actionExcelfiltrovacacion($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = Vacaciones::find()
                             ->andWhere(['=','estado_cerrado', 1])
                             ->orderBy('id_vacacion DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 40,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelfiltrovacacion($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('searchindex', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
                            'pagina' => $pagina,
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }

    /**
     * Displays a single Vacaciones model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token)
    {
        $model = Vacaciones::findOne($id); 
       $vacacion_adicion = VacacionesAdicion::find()->where(['=','id_vacacion', $id])->orderBy('id_adicion desc')->all();
        if (Yii::$app->request->post()) {
            if (isset($_POST["id_detalle"])) {
                foreach ($_POST["id_detalle"] as $intCodigo) {
                    try {
                        $eliminar = PrestacionesSocialesDetalle::findOne($intCodigo);
                        $eliminar->delete();
                        Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                        $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina, 'token' => $token]);
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
                'vacacion_adicion' => $vacacion_adicion,
                'token' => $token,
        ]);
    }
    
     public function actionViewsearch($id)
    {
        $model = Vacaciones::findOne($id); 
        $vacacion_adicion = VacacionesAdicion::find()->where(['=','id_vacacion', $id])->orderBy('id_adicion desc')->all();
        if (Yii::$app->request->post()) {
            
        }
        return $this->render('viewsearch', [
                'model' => $this->findModel($id),
                'id' => $id,
                'vacacion_adicion' => $vacacion_adicion,
        ]);
    }
    
     public function actionAdicionsalario($id, $token)
    {
        $model = new FormAdicionVacaciones();        
        $tipo_adicion = 1;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
           if ($model->validate()) {
                $table = new VacacionesAdicion();
                $table->id_vacacion = $id;
                $table->codigo_salario = $model->codigo_salario;
                $table->tipo_adicion = $tipo_adicion;
                $table->valor_adicion = $model->valor_adicion;
                $table->observacion = $model->observacion;
                $table->usuariosistema = Yii::$app->user->identity->username;
                $table->insert();
                $this->redirect(["vacaciones/view", 'id' => $id, 'token' => $token]);
            } else {
                $model->getErrors();
            }
        }
        return $this->render('_adicion', ['model' => $model, 'id' => $id, 'tipo_adicion' => $tipo_adicion, 'token' => $token] );
    }
    
    public function actionDescuento($id, $token)
    {
        $model = new FormAdicionVacaciones();        
        $tipo_adicion = 2;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
           if ($model->validate()) {
                $table = new VacacionesAdicion();
                $table->id_vacacion = $id;
                $table->codigo_salario = $model->codigo_salario;
                $table->tipo_adicion = $tipo_adicion;
                $table->valor_adicion = $model->valor_adicion;
                $table->observacion = $model->observacion;
                $table->usuariosistema = Yii::$app->user->identity->username;
                $table->insert();
                $this->redirect(["vacaciones/view", 'id' => $id, 'token' => $token]);
            } else {
                $model->getErrors();
            }
        }
        return $this->render('_adicion', ['model' => $model, 'id' => $id, 'tipo_adicion' => $tipo_adicion, 'token' => $token]);
        
    }

    /**
     * Creates a new Vacaciones model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FormVacacion();

        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
         $contrato = Contrato::find()->where(['=','id_empleado', $model->id_empleado])->andWhere(['=','contrato_activo', 1])->one();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $suma = 0; $totaldias = 0; $diasRealesDisfrutados =0;
                $suma = $model->dias_disfrutados + $model->dias_pagados;
                $fecha_inicio = $model->fecha_desde_disfrute;
                $fecha_final = $model->fecha_final_disfrute;
                if($suma <= 15){
                    if($suma <> 15){
                        Yii::$app->getSession()->setFlash('error', 'La suma de los dias disfrutados y pagados en dinero no puede ser mayor a Quince(15) dias. Ver Codigo Sustantivo de Trabajo.');    
                    }else{
                        $cedula = $model->id_empleado;
                        $table= new Vacaciones();
                        $table->id_empleado = $model->id_empleado;
                        $table->dias_disfrutados = $model->dias_disfrutados;
                        $table->dias_pagados = $model->dias_pagados;
                        if($model->fecha_desde_disfrute == '' &&  $model->fecha_final_disfrute == ''){
                            $table->observacion = $model->observacion;
                            $table->usuariosistema =  Yii::$app->user->identity->username;
                            $table->id_contrato = $contrato->id_contrato;
                            $table->id_grupo_pago = $contrato->id_grupo_pago;
                            $table->documento = $contrato->identificacion;
                            $table->salario_contrato = $contrato->salario;
                            $table->vlr_recargo_nocturno = $contrato->ibp_recargo_nocturno;
                            $table->dias_total_vacacion = $suma;
                            $table->fecha_inicio_periodo = $contrato->ultima_vacacion;
                            $table->dias_totales_periodo = 360;
                            $table->dias_total_vacacion_pagados = $model->dias_pagados;
                            //suma dias para la fecha proxima a vacaciones.
                            $fecha = date($contrato->ultima_vacacion);
                            $nuevafecha = $this->Diasvacaciones($fecha);
                            $table->fecha_final_periodo = $nuevafecha;
                            $table->dias_real_disfrutados = 0; 
                            $table->save(false);
                            return $this->redirect(["vacaciones/index"]);
                        }else{
                            $this->ValidarIncapacidadLicencia($contrato, $fecha_inicio, $fecha_final, $cedula);
                            $table->fecha_desde_disfrute = $model->fecha_desde_disfrute;
                            $table->fecha_hasta_disfrute = $model->fecha_final_disfrute;
                            $table->fecha_ingreso = $model->fecha_ingreso; 
                            $table->observacion = $model->observacion;
                            $table->usuariosistema =  Yii::$app->user->identity->username;
                            $table->id_contrato = $contrato->id_contrato;
                            $table->id_grupo_pago = $contrato->id_grupo_pago;
                            $table->documento = $contrato->identificacion;
                            $table->salario_contrato = $contrato->salario;
                            $table->dias_total_vacacion_pagados = $model->dias_pagados;
                            $table->vlr_recargo_nocturno = $contrato->ibp_recargo_nocturno;
                            $total = strtotime($model->fecha_final_disfrute ) - strtotime($model->fecha_desde_disfrute);
                            if($model->dias_disfrutados == 0){
                               $table->dias_total_vacacion = 0; 
                            }else{
                                  $table->dias_total_vacacion = round($total / 86400)+1;
                            }
                          
                            $table->fecha_inicio_periodo = $contrato->ultima_vacacion;
                            $table->dias_totales_periodo = 360;
                            //suma dias para la fecha proxima a vacaciones.
                            $fecha = date($contrato->ultima_vacacion);
                            $nuevafecha = $this->Diasvacaciones($fecha);
                            $table->fecha_final_periodo = $nuevafecha;
                            if($model->dias_disfrutados >  $model->dias_pagados){
                                 $table->dias_real_disfrutados = $table->dias_total_vacacion - $model->dias_pagados; 
                            }else{
                                if($model->dias_disfrutados  == 0){
                                   $table->dias_real_disfrutados = 0; 
                                }else{
                                   $table->dias_real_disfrutados = $model->dias_pagados - $table->dias_total_vacacion;   
                                }
                            }     
                          $table->save(false);
                          return $this->redirect(["vacaciones/index"]);
                        }
                    }
                }
            }else{
                $model->getErrors();
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    //PROCESO QUE VALIDE SI HAY INCAPACIDADES
    protected function ValidarIncapacidadLicencia($contrato, $fecha_inicio, $fecha_final, $cedula) {
       
        $incapacidad = \app\models\Incapacidad::find()->where(['=','id_empleado', $cedula])
                ->andWhere(['>=','fecha_inicio', $fecha_inicio])
                ->andWhere(['<=', 'fecha_final', $fecha_final])->orderBy('id_incapacidad DESC')->one();
        $licencia = \app\models\Licencia::find()->where(['=','id_empleado', $cedula])
                ->andWhere(['>=','fecha_desde', $fecha_inicio])
                ->andWhere(['<=', 'fecha_hasta', $fecha_final])->orderBy('id_licencia_pk DESC')->one();
        if ($incapacidad){
            Yii::$app->getSession()->setFlash('warning', 'No es posible generar las vacaciones en este rango de fechas, la incapacidad Nro  '. $incapacidad->id_incapacidad. ' termina el dia '.$incapacidad->fecha_final.', favor corregir la fecha de inicio de vacaciones.');     
        }
       if ($licencia){
            Yii::$app->getSession()->setFlash('warning', 'No es posible generar las vacaciones en este rango de fechas, la licencia Nro  '. $licencia->id_licencia_pk. ' termina el dia '.$licencia->fecha_hasta.', favor corregir la fecha de inicio de vacaciones.');     
        }
    }
    
    
    public function actionEditarvacaciones($id) {
        $model = new FormVacacion();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
         $contrato = Contrato::find()->where(['=','id_empleado', $model->id_empleado])->andWhere(['=','contrato_activo', 1])->one();
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                $suma = 0; $totaldias = 0; $diasRealesDisfrutados =0;
                $suma = $model->dias_disfrutados + $model->dias_pagados;
                $table = Vacaciones::find()->where(['id_vacacion' => $id])->one();
                if($suma > 15){
                    Yii::$app->getSession()->setFlash('error', 'La suma de los dias disfrutados y pagados en dinero no puede ser mayor a Quince(15) dias. Ver Codigo Sustantivo de Trabajo.');    
                }else{
                        $table->id_empleado = $model->id_empleado;
                        $table->dias_disfrutados = $model->dias_disfrutados;
                        $table->dias_pagados = $model->dias_pagados;
                        if($model->fecha_desde_disfrute == '' &&  $model->fecha_final_disfrute == ''){
                            $table->observacion = $model->observacion;
                            $table->usuariosistema =  Yii::$app->user->identity->username;
                            $table->id_contrato = $contrato->id_contrato;
                            $table->id_grupo_pago = $contrato->id_grupo_pago;
                            $table->documento = $contrato->identificacion;
                            $table->salario_contrato = $contrato->salario;
                            $table->vlr_recargo_nocturno = $contrato->ibp_recargo_nocturno;
                            $table->dias_total_vacacion = $suma;
                            $table->fecha_inicio_periodo = $contrato->ultima_vacacion;
                            $table->dias_totales_periodo = 360;
                            $table->dias_total_vacacion_pagados = $model->dias_pagados;
                            //suma dias para la fecha proxima a vacaciones.
                            $fecha = date($contrato->ultima_vacacion);
                            $nuevafecha = $this->Diasvacaciones($fecha);
                            $table->fecha_final_periodo = $nuevafecha;
                            $table->dias_real_disfrutados = $model->dias_disfrutados;
                            $table->save(false);
                            return $this->redirect(["vacaciones/index"]);
                        }else{
                            $table->fecha_desde_disfrute = $model->fecha_desde_disfrute;
                            $table->fecha_hasta_disfrute = $model->fecha_final_disfrute;
                            $table->fecha_ingreso = $model->fecha_ingreso; 
                            $table->observacion = $model->observacion;
                            $table->usuariosistema =  Yii::$app->user->identity->username;
                            $table->id_contrato = $contrato->id_contrato;
                            $table->id_grupo_pago = $contrato->id_grupo_pago;
                            $table->documento = $contrato->identificacion;
                            $table->salario_contrato = $contrato->salario;
                            $table->vlr_recargo_nocturno = $contrato->ibp_recargo_nocturno;
                            $total = strtotime($model->fecha_final_disfrute ) - strtotime($model->fecha_desde_disfrute);
                            if($model->dias_disfrutados == 0){
                                $table->dias_total_vacacion = 0;
                            }else{
                                 $table->dias_total_vacacion = round($total / 86400)+1;
                            }
                            $table->fecha_inicio_periodo = $contrato->ultima_vacacion;
                            $table->dias_totales_periodo = 360;
                            $table->dias_total_vacacion_pagados = $model->dias_pagados;
                            //suma dias para la fecha proxima a vacaciones.
                            $fecha = date($contrato->ultima_vacacion);
                            $nuevafecha = $this->Diasvacaciones($fecha);
                            $table->fecha_final_periodo = $nuevafecha;
                            $table->dias_real_disfrutados = $model->dias_disfrutados;
                            $table->save(false);
                            return $this->redirect(["vacaciones/index"]);
                        }    
                }    
                
            } else {
              $model->getErrors();    
            }
             
        }
        if (Yii::$app->request->get("id")) {
             $table = Vacaciones::find()->where(['id_vacacion' => $id])->one();
            if($table){  
                $model->id_empleado =   $table->id_empleado;
                $model->dias_disfrutados = $table->dias_disfrutados;
                $model->dias_pagados = $table->dias_pagados;
                $model->fecha_desde_disfrute = $table->fecha_desde_disfrute;
                $model->fecha_final_disfrute = $table->fecha_hasta_disfrute;
                $model->fecha_ingreso = $table->fecha_ingreso;
                $model->observacion = $table->observacion;
            }else{
                return $this->redirect(['index']);
            }
        } else {
            return $this->redirect(['index']);
        }    
         return $this->render('_form', [
            'model' => $model,
            'id' =>$id, 
        ]);
    }

    protected function Diasvacaciones($fecha) {
       $anionuevo = 0; $mesnuevo = 0; $dianuevo = 0; $nuevafecha = 0;
       $anioInicio = substr($fecha, 0, 4);
       $mesInicio = substr($fecha, 5, 2);
       $diaInicio = substr($fecha, 8, 8);
       $anionuevo = $anioInicio + 1;
       $mesnuevo =  $mesInicio;
       $dianuevo = $diaInicio;    
       $nuevafecha = $anionuevo.'-'.$mesnuevo.'-'.$dianuevo;
       return ($nuevafecha);
    }
    /**
     * permite modificar las adiciones.
     */
    public function actionUpdate($id, $id_adicion, $tipo_adicion, $token)
    {
        $model = new FormAdicionVacaciones();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
            $table = VacacionesAdicion::find()->where(['id_adicion'=>$id_adicion])->one();
            if ($table) {
                $table->codigo_salario = $model->codigo_salario;
                $table->valor_adicion = $model->valor_adicion;
                $table->observacion = $model->observacion;
                $table->save(false);
               return $this->redirect(["vacaciones/view", 'id' => $id, 'token' => $token]);
            }
        }
        if (Yii::$app->request->get("id_adicion")) {
                 $table = VacacionesAdicion::find()->where(['id_adicion' => $id_adicion])->one();            
                if ($table) {     
                    $model->codigo_salario = $table->codigo_salario;
                    $model->tipo_adicion = $table->tipo_adicion;
                    $model->valor_adicion = $table->valor_adicion;
                    $model->observacion =  $table->observacion;
                }else{
                     return $this->redirect(["vacaciones/view", 'id' => $id, 'token' => $token]);
                }
        } else {
                 return $this->redirect(["vacaciones/view", 'id' => $id, 'token' => $token]); 
        }
        return $this->render('update', [
            'model' => $model, 'id' => $id, 'tipo_adicion'=>$tipo_adicion,'token' => $token 
        ]);
    }

    //Eliminar adiciones 
    
    public function actionEliminaradicion($id, $id_adicion, $token) {
        if (Yii::$app->request->post()) {
            $adicion = VacacionesAdicion::findOne($id_adicion);
            if ((int) $id_adicion) {
                try {
                    VacacionesAdicion::deleteAll("id_adicion=:id_adicion", [":id_adicion" => $id_adicion]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    $this->redirect(["vacaciones/view", 'id' => $id, 'token' => $token]);
                } catch (IntegrityException $e) {
                    $this->redirect(["vacaciones/view", 'id' => $id, 'token' => $token]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, esta asociado a otro proceso');
                } catch (\Exception $e) {

                   $this->redirect(["vacaciones/view", 'id' => $id, 'token' => $token]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, esta asociado a otro proceso');
                }
            } else {
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute(["vacaciones/view,'id' => $id"]) . "'>";
            }
        } else {
            return $this->redirect(["vacaciones/view",'id'=>$id, 'token' => $token]);
        }
    }
  
  //PROCESO QUE AUTORIZA O GENERA LAS VACACIONES
    
    public function actionAutorizado($id, $token) {
        //se inicializan las variables.
        $ibp_vacacion = 0; $total_ibp = 0; $vlr_vacacion =0;
        $salario_promedio = 0; $dias_ausentismo = 0; $dias_reales = 0;
        $Vlr_vacacion_disfrute = 0; $vlr_vacacion_bruto = 0;
        $vlr_vacacion_pagado = 0;
        $conf_eps = \app\models\ConfiguracionEps::find()->all();
        $conf_pension= \app\models\ConfiguracionPension::find()->all();
        $modelo = Vacaciones::findOne($id);
        $contrato = Contrato::findOne($modelo->id_contrato);
        $confi_vacacion = \app\models\ConfiguracionPrestaciones::findOne(4);
        $concepto_salario = \app\models\ConceptoSalarios::find()->where(['=','provisiona_vacacion', 1])->all();
        foreach ($concepto_salario as $concepto):
            $nomina = \app\models\ProgramacionNomina::find()->where(['=','id_contrato', $modelo->id_contrato])
                                                          ->andWhere(['>=','fecha_inicio_contrato', $modelo->fecha_inicio_periodo])
                                                           ->andWhere(['<=','fecha_desde', $modelo->fecha_final_periodo])->all();
            foreach ($nomina as $nomina_programacion):
                $detalle_nomina = \app\models\ProgramacionNominaDetalle::find()->where(['=','codigo_salario', $concepto->codigo_salario])
                                                                               ->andWhere(['=','id_programacion', $nomina_programacion->id_programacion])
                                                                               ->all();
                foreach ($detalle_nomina as $detalle){
                     $ibp_vacacion += $detalle->vlr_devengado + $detalle->vlr_licencia + $detalle->vlr_ajuste_incapacidad;
                     $dias_ausentismo += $detalle->dias_licencia_descontar;
                     
                }
            endforeach;
        endforeach;
        //codigo que busca el % de eps
        $porcentaje_eps = 0;
        foreach ($conf_eps as $eps):
            if($contrato->id_eps == $eps->id_eps ){
                $porcentaje_eps = $eps->porcentaje_empleado_eps;
            }
        endforeach;
        $porcentaje_pension= 0;
        foreach ($conf_pension as $pension):
            if($contrato->id_pension == $pension->id_pension ){
                $porcentaje_pension = $pension->porcentaje_empleado;
            }
        endforeach;
       
        $total_ibp = $ibp_vacacion + $contrato->ibp_recargo_nocturno;
        if($contrato->tipo_salario == 'FIJO'){
           $salario_promedio = $contrato->salario; 
        }else{
            $salario_promedio = round(($total_ibp / $modelo->dias_totales_periodo) * 30);
        }    
        if($contrato->id_tiempo == 1){
            if($salario_promedio < $modelo->salario_contrato){
               $salario_promedio = $modelo->salario_contrato;
            }    
        } else {
            $salario_promedio =  $salario_promedio;
        }
        //pago disfrute
       $dias_reales = $modelo->dias_total_vacacion;
       $Vlr_vacacion_disfrute = round($salario_promedio / 30 * $dias_reales);   
       $vlr_vacacion_bruto = round($salario_promedio / 30 * $modelo->dias_total_vacacion);
        if($confi_vacacion->aplicar_ausentismo == 1){
            $dias_reales = $dias_reales - $dias_ausentismo;
            $Vlr_vacacion_disfrute = round($salario_promedio / 30 * $dias_reales);
        }
        if($modelo->dias_disfrutados <> 0){
            $modelo->dias_total_vacacion_pagados = $dias_reales;
            $modelo->salario_promedio = $salario_promedio;
            $modelo->vlr_vacacion_bruto = $vlr_vacacion_bruto;
            $modelo->dias_ausentismo = $dias_ausentismo;
            $modelo->estado_autorizado = 1;
            $modelo->vlr_vacacion_disfrute = $Vlr_vacacion_disfrute;
            $modelo->vlr_dia_vacacion =  $salario_promedio / 30;
            $modelo->vlr_vacacion_dinero = round($modelo->vlr_dia_vacacion * $modelo->dias_pagados);
            $modelo->descuento_eps = round(($Vlr_vacacion_disfrute * $porcentaje_eps)/100); 
            $modelo->descuento_pension = round(($Vlr_vacacion_disfrute * $porcentaje_pension)/100);
            $modelo->total_pago_vacacion = $modelo->vlr_vacacion_disfrute +  $modelo->vlr_vacacion_dinero ;
        }else{
            $modelo->salario_promedio = $salario_promedio;
            $modelo->vlr_vacacion_bruto = $vlr_vacacion_bruto;
            $modelo->dias_ausentismo = 0;
            $modelo->estado_autorizado = 1;
            $modelo->vlr_vacacion_disfrute = 0;
            $modelo->vlr_dia_vacacion =  $salario_promedio / 30;
            $modelo->vlr_vacacion_dinero = round($modelo->vlr_dia_vacacion * $modelo->dias_pagados);
            $modelo->descuento_eps = 0;
            $modelo->descuento_pension = 0;
            $modelo->total_compensado = 1;
            $modelo->total_pago_vacacion = $modelo->vlr_vacacion_dinero;
        }    
        //proceso que acumula las bonificaciones y descuentos
        $vacacion_adicion = VacacionesAdicion::find()->where(['=','id_vacacion', $modelo->id_vacacion])->all();
        $vlr_suma = 0; $vlr_resta = 0;
        $total_dcto = 0; 
        foreach ($vacacion_adicion as $adicion):
            if($adicion->tipo_adicion == 1){
              $vlr_suma += $adicion->valor_adicion;                
            }else{
                 $vlr_resta += $adicion->valor_adicion;
            }
        endforeach;
        $total_dcto = $vlr_resta + $modelo->descuento_eps + $modelo->descuento_pension;
        $modelo->total_descuentos = $total_dcto;
        $modelo->total_bonificaciones = $vlr_suma;
        $modelo->total_pagar =  ($modelo->total_pago_vacacion + $vlr_suma) - $total_dcto;
        $modelo->save(false);
        //RECARGA LA VISTA NUEVAMENTE
        $model = Vacaciones::findOne($id);
        $vacacion_adicion = VacacionesAdicion::find()->where(['=','id_vacacion', $id])->orderBy('id_adicion desc')->all();
       return $this->render('view', ['model' => $model, 'id' => $id, 'vacacion_adicion' => $vacacion_adicion,'token' => $token]);
    }  
    public function actionDesautorizado($id, $token) {
        $model = Vacaciones::findOne($id);
        $vacacion_adicion = VacacionesAdicion::find()->where(['=','id_vacacion', $id])->orderBy('id_adicion desc')->all();
        $model->estado_autorizado = 0;
        $model->save(false);
        return $this->render('view', [
            'model' => $model,
            'id' => $id, 
            'vacacion_adicion' => $vacacion_adicion, 
            'token' => $token,
        ]);
    }
// proceso que cierra las vacaciones
    
    public function actionCerrarvacacion($id, $token) {
        
        $model = Vacaciones::findOne($id);
        $vacacion_adicion = VacacionesAdicion::find()->where(['=','id_vacacion', $id])->orderBy('id_adicion desc')->all();
        $consecutivo = \app\models\Consecutivo::findOne(11);
        $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
        $consecutivo->save(false);
        $model->nro_pago = $consecutivo->consecutivo;
        $model->estado_cerrado = 1;
        $model->save(false);
        $contrato = Contrato::findOne($model->id_contrato);
        $contrato->ultima_vacacion = $model->fecha_final_periodo;
        $contrato->save(false);
        return $this->render('view', [
            'model' => $model,
            'id' => $id, 
            'vacacion_adicion' => $vacacion_adicion, 
            'token' => $token,
        ]);
    }
    
    public function actionAnularvacacion($id, $token) {
        $model = Vacaciones::findOne($id);
        $model->estado_anulado = 1;
        $model->total_pagar = 0;
        $model->save(false);
        $vacacion_adicion = VacacionesAdicion::find()->where(['=','id_vacacion', $id])->orderBy('id_adicion desc')->all();
        return $this->render('view', [
            'model'=>$model,
            'id' => $id,
            'vacacion_adicion' => $vacacion_adicion,
            'token' => $token,
        ]);
        
    }


    public function actionImprimirvacacion($id)
    {
                                
        return $this->render('../formatos/vacaciones', [
            'model' => $this->findModel($id),
            
        ]);
    }
    /**
     * Deletes an existing Vacaciones model.
    */
    // PERMITE ELIMINAR LAS VACACIONES
   public function actionEliminarvacacion($id) {
        if (Yii::$app->request->post()) {
            $vacacion = Vacaciones::findOne($id);
            if ((int) $id) {
                try {
                    Vacaciones::deleteAll("id_vacacion=:id_vacacion", [":id_vacacion" => $id]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    $this->redirect(["vacaciones/index"]);
                } catch (IntegrityException $e) {
                    $this->redirect(["vacaciones/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar las vacaciones, tiene detalles asociados en otros procesos');
                } catch (\Exception $e) {

                    $this->redirect(["vacaciones/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar las vacaciones, tiene detalles asociados en otros procesos');
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el registros, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("vacaciones/index") . "'>";
            }
        } else {
            return $this->redirect(["vacaciones/index"]);
        }
    }

    /**
     * Finds the Vacaciones model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Vacaciones the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Vacaciones::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
     public function actionExcelfiltrovacacion($tableexcel) {                
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setAutoSize(true);
        
        
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'CODIGO')
                    ->setCellValue('B1', 'NRO_PAGO')
                    ->setCellValue('C1', 'DOCUMENTO')
                    ->setCellValue('D1', 'EMPLEADO')
                    ->setCellValue('E1', 'NRO_CONTRATO')
                    ->setCellValue('F1', 'GRUPO_PAGO')
                    ->setCellValue('G1', 'F._INICIO_DISFRUTE')
                    ->setCellValue('H1', 'F._FINAL_DISFRUTE')
                    ->setCellValue('I1', 'F._INGRESO')
                    ->setCellValue('J1', 'INICIO_PERIODO')
                    ->setCellValue('K1', 'FINAL_PERIODO')
                    ->setCellValue('L1', 'DIAS_DISFRUTADOS')
                    ->setCellValue('M1', 'DIAS_PAGADOS')
                    ->setCellValue('N1', 'DIAS_VACACION')
                    ->setCellValue('O1', 'DIAS_TOTAL_VACACION')
                    ->setCellValue('P1', 'DIAS_REAL_DISFRUTADOS')
                    ->setCellValue('Q1', 'SALARIO')
                    ->setCellValue('R1', 'S._PROMEDIO')
                    ->setCellValue('S1', 'VLR_VACACION_BRUTO')
                    ->setCellValue('T1', 'TOTAL_VACACION')        
                    ->setCellValue('U1', 'VLR_VAC_DISFURTE')
                    ->setCellValue('V1', 'VLR_EN_DINERO')
                    ->setCellValue('W1', 'RECARGO_NOCTURNO')
                    ->setCellValue('X1', 'D_AUSENTISMO')
                    ->setCellValue('Y1', 'D._EPS')
                    ->setCellValue('Z1', 'D._PENSION')
                    ->setCellValue('AA1', 'T._DESCUENTO')
                    ->setCellValue('AB1', 'T._BONIFICACION')
                    ->setCellValue('AC1', 'TOTAL_PAGAR')
                    ->setCellValue('AD1', 'ESTADO_CERRADO')
                    ->setCellValue('AE1', 'ESTADO_ANULADO')
                    ->setCellValue('AF1', 'USUARIO')
                    ->setCellValue('AG1', 'FECHA_PROCESO')
                    ->setCellValue('AH1', 'OBSERVACION');
        
                   
        $i = 2  ;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_vacacion)
                    ->setCellValue('B' . $i, $val->nro_pago)
                    ->setCellValue('C' . $i, $val->documento)
                    ->setCellValue('D' . $i, $val->empleado->nombrecorto)   
                    ->setCellValue('E' . $i, $val->id_contrato)
                    ->setCellValue('F' . $i, $val->grupoPago->grupo_pago)
                    ->setCellValue('G' . $i, $val->fecha_desde_disfrute)
                    ->setCellValue('H' . $i, $val->fecha_hasta_disfrute)
                    ->setCellValue('I' . $i, $val->fecha_ingreso)
                    ->setCellValue('J' . $i, $val->fecha_inicio_periodo)
                    ->setCellValue('K' . $i, $val->fecha_final_periodo)
                    ->setCellValue('L' . $i, $val->dias_disfrutados)
                    ->setCellValue('M' . $i, $val->dias_pagados)
                    ->setCellValue('N' . $i, $val->dias_total_vacacion)
                    ->setCellValue('O' . $i, $val->dias_total_vacacion_pagados)
                    ->setCellValue('P' . $i, $val->dias_real_disfrutados)
                    ->setCellValue('Q' . $i, $val->salario_contrato)
                    ->setCellValue('R' . $i, $val->salario_promedio)
                    ->setCellValue('S' . $i, $val->vlr_vacacion_bruto)
                    ->setCellValue('T' . $i, $val->total_pago_vacacion)
                    ->setCellValue('U' . $i, $val->vlr_vacacion_disfrute)
                    ->setCellValue('V' . $i, $val->vlr_vacacion_dinero)
                    ->setCellValue('W' . $i, $val->vlr_recargo_nocturno)
                    ->setCellValue('X' . $i, $val->dias_ausentismo)
                    ->setCellValue('Y' . $i, $val->descuento_eps)
                    ->setCellValue('Z' . $i, $val->descuento_pension)
                    ->setCellValue('AA' . $i, $val->total_descuentos)
                    ->setCellValue('AB' . $i, $val->total_bonificaciones)
                    ->setCellValue('AC' . $i, $val->total_pagar)
                    ->setCellValue('AD' . $i, $val->estadocerrado)
                    ->setCellValue('AE' . $i, $val->procesoanulado)
                    ->setCellValue('AF' . $i, $val->usuariosistema)
                    ->setCellValue('AG' . $i, $val->fecha_proceso)
                    ->setCellValue('AH' . $i, $val->observacion);
                                     
            $i++;        }

        $objPHPExcel->getActiveSheet()->setTitle('Vacaciones');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="vacaciones.xlsx"');
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
