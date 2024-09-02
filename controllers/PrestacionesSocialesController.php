<?php

namespace app\controllers;
use Yii;
use app\models\PrestacionesSociales;
use app\models\PrestacionesSocialesSearch;
use app\models\FormFiltroPrestaciones;
use app\models\UsuarioDetalle;
use app\models\Contrato;
use app\models\GrupoPago;
use app\models\ConfiguracionPrestaciones;
use app\models\ProgramacionNomina;
use app\models\PrestacionesSocialesDetalle;
use app\models\ConceptoSalarios;
use app\models\ConfiguracionLicencia;
use app\models\ProgramacionNominaDetalle;
use app\models\ConfiguracionSalario;
use app\models\FormAdicionPrestaciones;
use app\models\PrestacionesSocialesAdicion;
use app\models\Credito;
use app\models\PrestacionesSocialesCreditos;
use app\models\FormParametroPrestaciones;
use app\models\Consecutivo;
use app\models\FormFiltroComprobantePagoPrestacion;
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
 * PrestacionesSocialesController implements the CRUD actions for PrestacionesSociales model.
 */
class PrestacionesSocialesController extends Controller
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
     * Lists all PrestacionesSociales models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',87])->all()){
                $form = new FormFiltroPrestaciones();
                $documento = null;
                $id_grupo_pago = null;
                $id_empleado = null;
                $pagina = 2;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $documento = Html::encode($form->documento);
                        $id_grupo_pago = Html::encode($form->id_grupo_pago);
                        $id_empleado = Html::encode($form->id_empleado);
                        $table = PrestacionesSociales::find()
                                ->andFilterWhere(['like', 'documento', $documento])
                                ->andFilterWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                                ->andFilterWhere(['=', 'id_empleado', $id_empleado])
                                ->orderBy('id_prestacion desc');
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 40,
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
                    $table = PrestacionesSociales::find()
                            ->orderBy('id_prestacion desc');
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 40,
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
                            'pagina' =>$pagina,
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
    }
    
    public function actionComprobantepagoprestaciones() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 89])->all()) {
                $form = new FormFiltroComprobantePagoPrestacion();
                $id_grupo_pago = null;
                $id_empleado = null;
                $documento = null;
                $fecha_desde = null;
                $fecha_hasta = null;
                $pagina = 1;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_grupo_pago = Html::encode($form->id_grupo_pago);
                        $id_empleado = Html::encode($form->id_empleado);
                        $documento = Html::encode($form->documento);
                        $fecha_desde = Html::encode($form->fecha_inicio_contrato);
                        $fecha_hasta = Html::encode($form->fecha_termino_contrato);
                        $table = PrestacionesSociales::find()
                                ->andFilterWhere(['=', 'id_grupo_pago', $id_grupo_pago])
                                ->andFilterWhere(['=', 'id_empleado', $id_empleado])
                                ->andFilterWhere(['=', 'documento', $documento])
                                ->andFilterWhere(['>=', 'fecha_inicio_contrato', $fecha_desde])
                                ->andFilterWhere(['<=', 'fecha_termino_contrato', $fecha_hasta]); 
                        $table = $table->orderBy('nro_pago DESC');
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
                            $check = isset($_REQUEST['nro_pago DESC']);
                            $this->actionExcelconsultaPago($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = PrestacionesSociales::find()
                             ->orderBy('nro_pago DESC');
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
                        $this->actionExcelconsultaPago($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('comprobantepagoprestaciones', [
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
    
    public function actionView($id, $pagina)
    {
        $model = PrestacionesSociales::findOne($id);
        $detalle_prestacion = PrestacionesSocialesDetalle::find()->where(['=','id_prestacion', $id])->all();
        $adicion_prestacion = PrestacionesSocialesAdicion::find()->where(['=','id_prestacion', $id])->andWhere(['=','tipo_adicion', 1])->all();
        $descuento_prestacion = PrestacionesSocialesAdicion::find()->where(['=','id_prestacion', $id])->andWhere(['=','tipo_adicion', 2])->all();
        $descuento_credito = PrestacionesSocialesCreditos::find()->where(['=','id_prestacion', $id])->all();
        if (Yii::$app->request->post()) {
            if (isset($_POST["id_detalle"])) {
                foreach ($_POST["id_detalle"] as $intCodigo) {
                    try {
                        $eliminar = PrestacionesSocialesDetalle::findOne($intCodigo);
                        $eliminar->delete();
                        Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                        $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina]);
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
               'pagina' =>$pagina,
                'detalle_prestacion' => $detalle_prestacion,
                'adicion_prestacion' => $adicion_prestacion,
                'descuento_prestacion' => $descuento_prestacion,
                'descuento_credito' => $descuento_credito,
                
    ]);
    }
    
    public function actionUpdate($id, $id_adicion, $tipo_adicion, $pagina)
    {
        $model = new FormAdicionPrestaciones();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
            $table = PrestacionesSocialesAdicion::find()->where(['id_adicion'=>$id_adicion])->one();
            if ($table) {
                $table->codigo_salario = $model->codigo_salario;
                $table->valor_adicion = $model->valor_adicion;
                $table->observacion = $model->observacion;
                $table->save(false);
               return $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina]);
            }
        }
        if (Yii::$app->request->get("id_adicion")) {
              
                 $table = PrestacionesSocialesAdicion::find()->where(['id_adicion' => $id_adicion])->one();            
                if ($table) {     
                    $model->codigo_salario = $table->codigo_salario;
                    $model->tipo_adicion = $table->tipo_adicion;
                    $model->valor_adicion = $table->valor_adicion;
                    $model->observacion =  $table->observacion;
                }else{
                     return $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina,]);
                }
        } else {
                 return $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina,]); 
        }
        return $this->render('update', [
            'model' => $model, 'id' => $id, 'tipo_adicion'=>$tipo_adicion, 'pagina' => $pagina, 
        ]);
    }
    
    public function actionEditarconcepto($id, $id_adicion, $codigo, $pagina)
    {
        $model = new FormParametroPrestaciones();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
            $table = PrestacionesSocialesDetalle::find()->where(['id_prestacion'=>$id])->andWhere(['=','codigo_salario', $codigo])->one();
            if ($table) {
                $valor_pagar = 0;
                $interes = 0;
                $prestacion = ConfiguracionPrestaciones::find()->where(['=','codigo_salario', $codigo])->one();
             
                if($prestacion->id_prestacion == 1){
                    $valor_pagar = round((($model->salario_promedio_prima + $model->auxilio_transporte)* $model->total_dias)/360);
                }
                if($prestacion->id_prestacion == 2){
                    $valor_pagar = round((($model->salario_promedio_prima + $model->auxilio_transporte)* $model->total_dias)/360);
                }
                if($prestacion->id_prestacion == 3){
                   $porcentaje = (12 * $model->nro_dias)/360;
                   $valor_pagar = round(($model->salario_promedio_prima * $porcentaje)/100);
                }
                if($prestacion->id_prestacion == 4){
                   $valor_pagar = round(($model->salario_promedio_prima * $model->total_dias)/720);
                }
                $table->nro_dias = $model->nro_dias;
                $table->dias_ausentes = $model->dias_ausentes;
                $table->salario_promedio_prima = $model->salario_promedio_prima;
                $table->total_dias = $model->total_dias;
                $table->auxilio_transporte = $model->auxilio_transporte;
                $table->valor_pagar = $valor_pagar;
                $table->save(false);
                return $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina]);
            }
        }
        if (Yii::$app->request->get("id_adicion")) {
            $table = PrestacionesSocialesDetalle::find()->where(['id' => $id_adicion])->one();            
            if ($table) {     
                $model->nro_dias = $table->nro_dias;
                $model->dias_ausentes = $table->dias_ausentes;
                $model->salario_promedio_prima = $table->salario_promedio_prima;
                $model->total_dias =  $table->total_dias;
                $model->auxilio_transporte =  $table->auxilio_transporte;
            }
        }
       return $this->render('_editarprestaciones', [
            'model' => $model, 'id' => $id, 'pagina' => $pagina,  
        ]);
    }
    
    public function actionAdicionsalario($id, $pagina)
    {
        $model = new FormAdicionPrestaciones();        
        $tipo_adicion = 1;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
           if ($model->validate()) {
                $table = new PrestacionesSocialesAdicion();
                $table->id_prestacion = $id;
                $table->codigo_salario = $model->codigo_salario;
                $table->tipo_adicion = $tipo_adicion;
                $table->valor_adicion = $model->valor_adicion;
                $table->observacion = $model->observacion;
                $table->usuariosistema = Yii::$app->user->identity->username;
                $table->insert();
                $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina]);
            } else {
                $model->getErrors();
            }
        }
        return $this->render('_adicion', ['model' => $model, 'id' => $id, 'tipo_adicion' => $tipo_adicion, 'pagina' => $pagina]);
        
    }
    public function actionDescuento($id, $pagina)
    {
        $model = new FormAdicionPrestaciones();        
        $tipo_adicion = 2;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
           if ($model->validate()) {
                $table = new PrestacionesSocialesAdicion();
                $table->id_prestacion = $id;
                $table->codigo_salario = $model->codigo_salario;
                $table->tipo_adicion = $tipo_adicion;
                $table->valor_adicion = $model->valor_adicion;
                $table->observacion = $model->observacion;
                $table->usuariosistema = Yii::$app->user->identity->username;
                $table->insert();
                $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina]);
            } else {
                $model->getErrors();
            }
        }
        return $this->render('_adicion', ['model' => $model, 'id' => $id, 'tipo_adicion' => $tipo_adicion, 'pagina' => $pagina]);
        
    }
    
     public function actionDescuentocredito($id_empleado, $id, $pagina)
    {
        $credito = Credito::find()->where(['=','id_empleado', $id_empleado])->andWhere(['>','saldo_credito', 0])->all();
        
        if (isset($_POST["idcredito"])) {
                $intIndice = 0;
                foreach ($_POST["idcredito"] as $intCodigo) {
                    $table = new PrestacionesSocialesCreditos();
                    $credito_consulta = credito::find()->where(['id_credito' => $intCodigo])->one();
                    $detalle_credito = PrestacionesSocialesCreditos::find()
                        ->where(['=', 'id_prestacion', $id])
                        ->andWhere(['=', 'id_credito', $credito_consulta->id_credito])
                        ->all();
                   
                    if (!$detalle_credito) {
                        $table->id_credito = $credito_consulta->id_credito;
                        $table->id_prestacion = $id;
                        $concepto = \app\models\ConfiguracionCredito::find()->where(['=','codigo_credito', $credito_consulta->codigo_credito])->one();
                        $table->codigo_salario = $concepto->codigo_salario;
                        $table->valor_credito = $credito_consulta->vlr_credito;
                        $table->saldo_credito = $credito_consulta->saldo_credito;
                        $table->deduccion = $credito_consulta->saldo_credito;
                        $table->estado_cerrado = 0;
                        $table->fecha_inicio = $credito_consulta->fecha_inicio;
                        $table->usuariosistema = Yii::$app->user->identity->username;
                        $table->insert();                                                
                    }
                }
                $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina]);
        }
        return $this->render('_consultacredito', [
            'credito' => $credito, 
            'id_empleado' => $id_empleado,
            'id' => $id,
            'pagina' => $pagina,
        ]);
    }
    
    public function actionEliminar($id) {
        if (Yii::$app->request->post()) {
            $prestacion = PrestacionesSociales::findOne($id);
            if ((int) $id) {
                try {
                    PrestacionesSociales::deleteAll("id_prestacion=:id_prestacion", [":id_prestacion" => $id]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    $this->redirect(["prestaciones-sociales/index"]);
                } catch (IntegrityException $e) {
                    $this->redirect(["prestaciones-sociales/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, las prestación Nro ' . $prestacion->id_prestacion . ' tiene registros asociados en otros procesos');
                } catch (\Exception $e) {

                    $this->redirect(["prestaciones-sociales/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, las prestación Nro  ' . $prestacion->id_prestacion . ' tiene registros asociados en otros procesos');
                }
            } else {
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("prestaciones-sociales/index") . "'>";
            }
        } else {
            return $this->redirect(["prestaciones-sociales/index"]);
        }
    }
    
    public function actionEliminaradicion($id, $id_adicion, $pagina) {
        if (Yii::$app->request->post()) {
            $adicion = PrestacionesSocialesAdicion::findOne($id_adicion);
            if ((int) $id_adicion) {
                try {
                    PrestacionesSocialesAdicion::deleteAll("id_adicion=:id_adicion", [":id_adicion" => $id_adicion]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina]);
                } catch (IntegrityException $e) {
                    $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, esta asociado a otro proceso');
                } catch (\Exception $e) {

                   $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, esta asociado a otro proceso');
                }
            } else {
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute(["prestaciones-sociales/view,'id' => $id, 'pagina' => $pagina"]) . "'>";
            }
        } else {
            return $this->redirect(["prestaciones-sociales/view",'id'=>$id, 'pagina' => $pagina]);
        }
    }
    
    public function actionEditarcredito($id_credito, $id, $pagina)
    {
        $model = new \app\models\FormDeduccionCredito();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
            if ($model->validate()) {
                $credito = PrestacionesSocialesCreditos::findOne($id_credito);
                if (isset($_POST["actualizar"])) {  
                        $credito->deduccion = $model->deduccion;
                        $credito->save(false);
                        $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina]);                                                     
                }
            }
        }
        if (Yii::$app->request->get("id")) {
            $table = PrestacionesSocialesCreditos::find()->where(['id' => $id_credito])->one();            
            if ($table) {                                
                $model->id_credito = $table->id_credito;                
            }
        }
        
        return $this->renderAjax('_editarcredito', ['model' => $model, 'id' => $id, 'pagina' => $pagina]);
    }
    
   
    public function actionGenerarconceptos($id, $year=NULL, $pagina)
    {
        $model = PrestacionesSociales::find()->where(['=','id_prestacion', $id])->one();
        $id_prestacion = $model->id_prestacion;
        $configuracion_p = ConfiguracionPrestaciones::findOne(1);
        $concepto_p = ConceptoSalarios::find()->where(['=','concepto_prima', 1])->one();
        $configuracion_c = ConfiguracionPrestaciones::findOne(2);
        $concepto_c = ConceptoSalarios::find()->where(['=','concepto_cesantias', 1])->one();
        $configuracion_v = ConfiguracionPrestaciones::findOne(4);
        $concepto_v = ConceptoSalarios::find()->where(['=','concepto_vacacion', 1])->one();
        $contrato_trabajo = Contrato::find()->where(['=','id_contrato', $model->id_contrato])->one();
        $fecha_inicio_contrato = strtotime($model->fecha_inicio_contrato);
        $fecha_final = date($model->fecha_termino_contrato);
        $fecha_inicio = date($model->fecha_inicio_contrato);
        $fecha_final_contrato = strtotime($model->fecha_termino_contrato);
        $ultimo_pago_prima = strtotime($model->ultimo_pago_prima);
        $ultimo_pago_cesantia = strtotime($model->ultimo_pago_cesantias);
        $ultimo_pago_vacacion = strtotime($model->ultimo_pago_vacaciones);
        $mes_ano_final = substr($fecha_final, 5, 2);
        $ultimo_dia_febrero = substr($fecha_final, 5, 7);
        $mes_ano_inicio = substr($fecha_inicio, 5, 2);
        $year = ($year==NULL)? date('Y'):$year;
        if (($year%4 == 0 && $year%100 != 0) || $year%400 == 0 ){
           $ano = 1;
        }else{
            $ano = 2;
        }  
        //PROCESO PARA PRIMA
        $sw = 0;
        if($configuracion_p->codigo_salario ==  $concepto_p->codigo_salario){
            if (strtotime($model->ultimo_pago_prima) > strtotime($model->fecha_inicio_contrato)){ 
                 $sw = 1;
                $this->CrearPrima($model, $sw, $ano);
            }
            if (strtotime($model->ultimo_pago_prima)< strtotime($model->fecha_inicio_contrato )){
                $sw = 2;
                $this->CrearPrima($model, $sw, $ano);
            }
            if (strtotime($model->fecha_termino_contrato)< strtotime($model->ultimo_pago_prima  )){
             $sw = 3; 
                $this->CrearPrima($model, $sw, $ano);
            }
            
            if (strtotime($model->fecha_inicio_contrato) == strtotime($model->ultimo_pago_prima)){
               $sw = 4; 
                $this->CrearPrima($model, $sw, $ano);
            }
            
            
        }
        //PROCESO PARA CESANTIAS
        $sw = 0;
        if($configuracion_c->codigo_salario ==  $concepto_c->codigo_salario){
            if(strtotime($model->fecha_inicio_contrato) == strtotime($model->ultimo_pago_cesantias)){   
                $sw = 1;
            }else{
                if(strtotime($model->fecha_termino_contrato) == strtotime($model->ultimo_pago_cesantias)){
                   $sw = 2;
                }else{
                    $sw = 3;
                }    
            }    
                $this->CrearCesantia($ano, $model, $sw);
   
        }
        //PROCESO PARA VACACIONES
        $sw = 0;
        if($configuracion_v->codigo_salario ==  $concepto_v->codigo_salario){
            if($ultimo_pago_vacacion == $fecha_inicio_contrato){    
                $sw =1;
                $this->CrearVacion($model, $sw, $ano);
            }else{
                 $sw = 2;
                $this->CrearVacion($model, $sw, $ano);
            }    
        }
        $model->estado_generado = 1;
        $model->save(false);
         
    $this->redirect(["prestaciones-sociales/view", 'id' => $id,
          'model' => $model,
           'pagina' => $pagina,
          ]);
       
       
    }
     protected function CrearVacion($model, $sw, $ano)
    {
        $concepto = ConceptoSalarios::find()->where(['=','concepto_vacacion', 1])->one();
        $configuracion_v = ConfiguracionPrestaciones::findOne(4);
        $contrato = Contrato::find()->where(['=','id_contrato', $model->id_contrato])->one();
        $total_dias = 0;
        if($sw == 1){
            $fecha_inicio = $model->fecha_inicio_contrato;
            $fecha_termino = $model->fecha_termino_contrato;
            $diaTerminacion = substr($fecha_termino, 8, 8);
            $mesTerminacion = substr($fecha_termino, 5, 2);
            $anioTerminacion = substr($fecha_termino, 0, 4);
            $diaInicio = substr($fecha_inicio, 8, 8);
            $mesInicio = substr($fecha_inicio, 5, 2);
            $anioInicio = substr($fecha_inicio, 0, 4);
        }else{
            $fecha_inicio = $model->ultimo_pago_vacaciones;
            $fecha_termino = $model->fecha_termino_contrato;
            $diaTerminacion = substr($fecha_termino, 8, 8);
            $mesTerminacion = substr($fecha_termino, 5, 2);
            $anioTerminacion = substr($fecha_termino, 0, 4);
            $diaInicio = substr($fecha_inicio, 8, 8);
            $mesInicio = substr($fecha_inicio, 5, 2);
            $anioInicio = substr($fecha_inicio, 0, 4); 
        }    
        $febrero = 0;
        $mes = $mesInicio-1;
        if($mes == 2){
            if($ano == 1){
              $febrero = 29;
            }else{
              $febrero = 28;
            }
        }else if($mes <= 7){
            if($mes==0){
             $febrero = 31;
            }else if($mes%2==0){
                 $febrero = 30;
                }else{
                   $febrero = 31;
                }
        }else if($mes > 7){
              if($mes%2==0){
                  $febrero = 31;
              }else{
                  $febrero = 30;
              }
       }
        if(($anioInicio > $anioTerminacion) || ($anioInicio == $anioTerminacion && $mesInicio > $mesTerminacion) || 
            ($anioInicio == $anioTerminacion && $mesInicio == $mesTerminacion && $diaInicio > $diaTerminacion)){
                //mensaje
        }else{
            if($mesInicio <= $mesTerminacion){
                $anios = $anioTerminacion - $anioInicio;
                if($diaInicio <= $diaTerminacion){
                    $meses = $mesTerminacion - $mesInicio;
                    $dies = $diaTerminacion - $diaInicio;
                }else{
                    if($mesTerminacion == $mesInicio){
                       $anios = $anios - 1;
                    }
                    $meses = ($mesTerminacion - $mesInicio - 1 + 12) % 12;
                    $dies = $febrero-($diaInicio - $diaTerminacion);
                }
            }else{
                $anios = $anioTerminacion - $anioInicio - 1;
                if($diaInicio > $diaTerminacion){
                    $meses = $mesTerminacion - $mesInicio -1 +12;
                    $dies = $febrero - ($diaInicio-$diaTerminacion);
                }else{
                    $meses = $mesTerminacion - $mesInicio + 12;
                    $dies = $diaTerminacion - $diaInicio;
                }
            }
            $total_dias = (($anios * 360) + ($meses * 30)+ ($dies))+1;
        }
        //TERMINA EL CODIGO DE LOS DIAS
        $suma_vacacion = 0;
        $suma_vacacion1 = 0;
        $total_devengado = 0;
        $ibc_vacacion = 0;
        $ibc_vacacion = $contrato->ibp_recargo_nocturno;
        if($sw == 1){
             $nomina = ProgramacionNomina::find()->where(['=','id_contrato',$model->id_contrato])->andWhere(['=','fecha_inicio_contrato',$model->fecha_inicio_contrato])->all();
             foreach ($nomina as $valor_nomina):
                 $detalle_nomina = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $valor_nomina->id_programacion])->all();
                     foreach ($detalle_nomina as $valor_detalle ):
                           $concepto_salario = ConceptoSalarios::find()->where(['=','provisiona_vacacion', 1])->andWhere(['=','codigo_salario', $valor_detalle->codigo_salario])->one();
                           if($concepto_salario){
                                $suma_vacacion += $valor_detalle->vlr_devengado + $valor_detalle->vlr_ajuste_incapacidad;   
                                $suma_vacacion1 += $valor_detalle->vlr_licencia_no_pagada;
                           }
                     endforeach;
             endforeach;
        }else{
            $nomina = ProgramacionNomina::find()->where(['=','id_contrato',$model->id_contrato])->andWhere(['=','fecha_ultima_vacacion',$model->ultimo_pago_vacaciones])->all();
            foreach ($nomina as $valor_nomina):
                 $detalle_nomina = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $valor_nomina->id_programacion])->all();
                     foreach ($detalle_nomina as $valor_detalle ):
                           $concepto_salario = ConceptoSalarios::find()->where(['=','provisiona_vacacion', 1])->andWhere(['=','codigo_salario', $valor_detalle->codigo_salario])->one();
                           if($concepto_salario){
                                $suma_vacacion += $valor_detalle->vlr_devengado + $valor_detalle->vlr_ajuste_incapacidad;   
                                $suma_vacacion1 += $valor_detalle->vlr_licencia_no_pagada;
                           }
                     endforeach;
            endforeach;
        }
        $total_devengado = $suma_vacacion + $suma_vacacion1 + $ibc_vacacion;
        $auxiliar = 0;
        $vacacion = 0;
        $salario_promedio = 0;
        $dias_reales = 0;
        if ($configuracion_v->aplicar_ausentismo == 1){
            $ausentismo = ConfiguracionLicencia::find()->where(['=','ausentismo', 1])->all();
            foreach ($ausentismo  as $dato):
                 $nomina = ProgramacionNomina::find()->where(['=','id_contrato', $model->id_contrato])->all();
                 foreach ($nomina as $nomina):
                     $detalle_no = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $nomina->id_programacion])->andWhere(['=','codigo_salario', $dato->codigo_salario])->one();
                      if($detalle_no){   
                           $auxiliar = $auxiliar + $detalle_no->dias_licencia_descontar; 
                      }     
                 endforeach; 
            endforeach;
        }
       
        $dias_reales = $total_dias - $auxiliar;
        $salario_promedio = (($total_devengado / $total_dias)* 30);
        if($sw == 1){
            $vacacion = round(($salario_promedio * $dias_reales)/720);
        }else{
          $vacacion = round(($salario_promedio * $dias_reales)/720);
        }
       $detalle_prestacion = PrestacionesSocialesDetalle::find()->where(['=','id_prestacion', $model->id_prestacion])->andWhere(['=','codigo_salario', $concepto->codigo_salario])->one();
       if(!$detalle_prestacion){
           $detalle = new PrestacionesSocialesDetalle();
           $detalle->id_prestacion = $model->id_prestacion;
           $detalle->codigo_salario = $concepto->codigo_salario;
           $detalle->fecha_inicio = $model->ultimo_pago_vacaciones;
           $detalle->fecha_final = $model->fecha_termino_contrato;
           $detalle->nro_dias = $total_dias;
           $detalle->dias_ausentes = $auxiliar;
           $detalle->total_dias = $dias_reales;
           $detalle->salario_promedio_prima = $salario_promedio;
           $detalle->auxilio_transporte = 0;
           $detalle->valor_pagar = $vacacion;
           $detalle->insert(false);
           
       }
       
    }
    
    protected function CrearCesantia($ano, $model, $sw)
    {
        $concepto = ConceptoSalarios::find()->where(['=','concepto_cesantias', 1])->one();
        $concepto_i = ConceptoSalarios::find()->where(['=','intereses', 1])->one();
        $configuracion_c = ConfiguracionPrestaciones::findOne(2);
        $configuracion_i = ConfiguracionPrestaciones::findOne(3);
        $total_dias = 0;
        //codigo que captura los dias, meses y años. 
        if($sw == 1){
            $fecha_inicio = $model->fecha_inicio_contrato;      
            $fecha_termino = $model->fecha_termino_contrato;
            $diaTerminacion = substr($fecha_termino, 8, 8);
            $mesTerminacion = substr($fecha_termino, 5, 2);
            $anioTerminacion = substr($fecha_termino, 0, 4);
            $diaInicio = substr($fecha_inicio, 8, 8);
            $mesInicio = substr($fecha_inicio, 5, 2);
            $anioInicio = substr($fecha_inicio, 0, 4);
        }else{
            if ($sw==2){
                $total_dias = 0;
            }else{
                $fecha = date($model->ultimo_pago_cesantias);
                $fecha_inicio_dias = strtotime('2 day', strtotime($fecha));
                $fecha_inicio_dias = date('Y-m-d', $fecha_inicio_dias);
                $fecha_inicio = $fecha_inicio_dias;      
                $fecha_termino = $model->fecha_termino_contrato;
                $diaTerminacion = substr($fecha_termino, 8, 8);
                $mesTerminacion = substr($fecha_termino, 5, 2);
                $anioTerminacion = substr($fecha_termino, 0, 4);
                $diaInicio = substr($fecha_inicio, 8, 8);
                $mesInicio = substr($fecha_inicio, 5, 2);
                $anioInicio = substr($fecha_inicio, 0, 4);
            }
                
        }
       
        //codigo que valide los dias
        
        $febrero = 0;
        $mes = $mesInicio-1;
        if($mes == 2){
            if($ano == 1){
              $febrero = 29;
            }else{
              $febrero = 28;
            }
        }else if($mes <= 7){
            if($mes==0){
             $febrero = 31;
            }else if($mes%2==0){
                 $febrero = 30;
                }else{
                   $febrero = 31;
                }
        }else if($mes > 7){
              if($mes%2==0){
                  $febrero = 31;
              }else{
                  $febrero = 30;
              }
       }
        if(($anioInicio > $anioTerminacion) || ($anioInicio == $anioTerminacion && $mesInicio > $mesTerminacion) || 
            ($anioInicio == $anioTerminacion && $mesInicio == $mesTerminacion && $diaInicio > $diaTerminacion)){
                //mensaje
        }else{
            if($mesInicio <= $mesTerminacion){
                $anios = $anioTerminacion - $anioInicio;
                if($diaInicio <= $diaTerminacion){
                    $meses = $mesTerminacion - $mesInicio;
                    $dies = $diaTerminacion - $diaInicio;
                }else{
                    if($mesTerminacion == $mesInicio){
                       $anios = $anios - 1;
                    }
                    $meses = ($mesTerminacion - $mesInicio - 1 + 12) % 12;
                    $dies = $febrero-($diaInicio - $diaTerminacion);
                }
            }else{
                $anios = $anioTerminacion - $anioInicio - 1;
                if($diaInicio > $diaTerminacion){
                    $meses = $mesTerminacion - $mesInicio -1 +12;
                    $dies = $febrero - ($diaInicio-$diaTerminacion);
                }else{
                    $meses = $mesTerminacion - $mesInicio + 12;
                    $dies = $diaTerminacion - $diaInicio;
                }
            }
            $total_dias = (($anios * 360) + ($meses * 30)+ ($dies))+1;
        }
       
        //TERMINA EL CODIGO DE LOS DIAS
         $suma_cesantia = 0; $ibp_cesantia_anterior = 0;
         $suma_licencia = 0;
         
         $contrato = Contrato::find()->where(['=','id_contrato', $model->id_contrato])->one();
         $ibp_cesantia_anterior = $contrato->ibp_cesantia_inicial;
         if($sw == 1){
             $nomina = ProgramacionNomina::find()->where(['=','id_contrato',$model->id_contrato])->andWhere(['=','fecha_inicio_contrato',$model->fecha_inicio_contrato])->all();
             foreach ($nomina as $valor_nomina):
                 $detalle_nomina = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $valor_nomina->id_programacion])->all();
                     foreach ($detalle_nomina as $valor_detalle ):
                           $concepto_salario = ConceptoSalarios::find()->where(['=','ingreso_base_prestacional', 1])->andWhere(['=','codigo_salario', $valor_detalle->codigo_salario])->one();
                           if($concepto_salario){
                               $suma_cesantia += $valor_detalle->vlr_devengado + $valor_detalle->vlr_ajuste_incapacidad;   
                               $suma_licencia += $valor_detalle->vlr_licencia_no_pagada;
                           }
                     endforeach;
             endforeach;
        }
        if($sw==3){
            $nomina = ProgramacionNomina::find()->where(['=','id_contrato',$model->id_contrato])->andWhere(['>','fecha_ultima_cesantia', $model->ultimo_pago_cesantias])->all();
             foreach ($nomina as $valor_nomina):
                 $detalle_nomina = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $valor_nomina->id_programacion])->all();
                     foreach ($detalle_nomina as $valor_detalle ):
                           $concepto_salario = ConceptoSalarios::find()->where(['=','ingreso_base_prestacional', 1])->andWhere(['=','codigo_salario', $valor_detalle->codigo_salario])->one();
                           if($concepto_salario){
                               $suma_cesantia += $valor_detalle->vlr_devengado + $valor_detalle->vlr_ajuste_incapacidad;   
                               $suma_licencia += $valor_detalle->vlr_licencia_no_pagada;
                           }
                     endforeach;
             endforeach;
        }
         
        $auxiliar = 0;
        $porcentaje_interes = 0;
        $cesantias = 0;
        $salario_promedio = 0;
        $auxilio_transporte = 0;
        $intereses = 0;
        if ($configuracion_c->aplicar_ausentismo == 1){
            $ausentismo = ConfiguracionLicencia::find()->where(['=','ausentismo', 1])->all();
            foreach ($ausentismo  as $dato):
                 $nomina = ProgramacionNomina::find()->where(['=','id_contrato', $model->id_contrato])->all();
                 foreach ($nomina as $nomina):
                     $detalle_no = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $nomina->id_programacion])->andWhere(['=','codigo_salario', $dato->codigo_salario])->one();
                      if($detalle_no){   
                          $auxiliar +=  $detalle_no->dias_licencia_descontar; 
                      }     
                 endforeach; 
            endforeach;
        }
        $total_ibp = 0;
        if($sw==1){
           $total_ibp = round($suma_cesantia + $suma_licencia + $ibp_cesantia_anterior);
        }else{
            if($sw==2){
                $total_ibp =0;
            }else{
                $total_ibp = round($suma_cesantia + $suma_licencia);
            }
        }  
        $dias_reales = 0;
        $dias_reales = $total_dias - $auxiliar;
      
        $transporte = ConfiguracionSalario::find()->where(['=','estado', 1])->one();
        $salario_promedio = (($total_ibp / $total_dias)*30);
        $porcentaje_interes = (($total_dias * 12)/100)/360;
        if($contrato->auxilio_transporte == 1){
            $auxilio_transporte = $transporte->auxilio_transporte_actual;
        }
        if($sw==1 or $sw == 3){    
           $cesantias = round((($salario_promedio + $auxilio_transporte) * $dias_reales)/360);
            $intereses = round($cesantias * $porcentaje_interes);
        }else{
            $cesantias = 0;
            $intereses = 0;
        }    
       
       $detalle_prestacion = PrestacionesSocialesDetalle::find()->where(['=','id_prestacion', $model->id_prestacion])->andWhere(['=','codigo_salario', $concepto->codigo_salario])->one();
       if(!$detalle_prestacion){
           $detalle = new PrestacionesSocialesDetalle();
           $detalle->id_prestacion = $model->id_prestacion;
           $detalle->codigo_salario = $concepto->codigo_salario;
           $detalle->fecha_inicio = $model->ultimo_pago_cesantias;
           $detalle->fecha_final = $model->fecha_termino_contrato;
           $detalle->nro_dias = $total_dias;
           $detalle->dias_ausentes = $auxiliar;
           $detalle->total_dias = $dias_reales;
           $detalle->salario_promedio_prima = $salario_promedio;
           $detalle->auxilio_transporte = $auxilio_transporte;
           $detalle->valor_pagar = $cesantias;
           $detalle->insert(false);
           
       }
       $detalle_interes = PrestacionesSocialesDetalle::find()->where(['=','id_prestacion', $model->id_prestacion])->andWhere(['=','codigo_salario', $concepto_i->codigo_salario])->one();
       if(!$detalle_interes){
           $detalle = new PrestacionesSocialesDetalle();
           $detalle->id_prestacion =  $model->id_prestacion;
           $detalle->codigo_salario = $concepto_i->codigo_salario;
           $detalle->fecha_inicio = $model->ultimo_pago_cesantias;
           $detalle->fecha_final = $model->fecha_termino_contrato;
           $detalle->nro_dias = $total_dias;
           $detalle->dias_ausentes = 0;
           $detalle->total_dias = $dias_reales;
           $detalle->salario_promedio_prima = 0;
           $detalle->valor_pagar = $intereses;
           $detalle->insert(false);
           
       }
    }
    
    protected function CrearPrima($model,$sw, $ano) 
    {
        $contrato = Contrato::find()->where(['=','id_contrato', $model->id_contrato])->one();
        $concepto = ConceptoSalarios::find()->where(['=','concepto_prima', 1])->one();
        $configuracion_p = ConfiguracionPrestaciones::findOne(1);
        $total_dias = 0;
   
        //codigo que captura los dias, meses y años.
        if($sw == 1){
            $fecha = date($model->ultimo_pago_prima);
            $fecha_inicio_dias = strtotime('1 day', strtotime($fecha));
            $fecha_inicio_dias = date('Y-m-d', $fecha_inicio_dias);
            $fecha_inicio = $fecha_inicio_dias;      
            $fecha_termino = $model->fecha_termino_contrato;
            $diaTerminacion = substr($fecha_termino, 8, 8);
            $mesTerminacion = substr($fecha_termino, 5, 2);
            $anioTerminacion = substr($fecha_termino, 0, 4);
            $diaInicio = substr($fecha_inicio, 8, 8);
            $mesInicio = substr($fecha_inicio, 5, 2);
            $anioInicio = substr($fecha_inicio, 0, 4);
        }else{
            if($sw == 2){
                $fecha_inicio = $model->fecha_inicio_contrato;      
                $fecha_termino = $model->fecha_termino_contrato;
                $diaTerminacion = substr($fecha_termino, 8, 8);
                $mesTerminacion = substr($fecha_termino, 5, 2);
                $anioTerminacion = substr($fecha_termino, 0, 4);
                $diaInicio = substr($fecha_inicio, 8, 8);
                $mesInicio = substr($fecha_inicio, 5, 2);
                $anioInicio = substr($fecha_inicio, 0, 4); 
            }else{
                if($sw == 3){
                    $fecha_inicio = $model->fecha_termino_contrato;      
                    $fecha_termino = $model->ultimo_pago_prima;
                    $diaTerminacion = substr($fecha_termino, 8, 8);
                    $mesTerminacion = substr($fecha_termino, 5, 2);
                    $anioTerminacion = substr($fecha_termino, 0, 4);
                    $diaInicio = substr($fecha_inicio, 8, 8);
                    $mesInicio = substr($fecha_inicio, 5, 2);
                    $anioInicio = substr($fecha_inicio, 0, 4); 
                }else{
                    if($sw == 4){
                        $fecha_inicio = $model->fecha_inicio_contrato;      
                        $fecha_termino = $model->fecha_termino_contrato;
                        $diaTerminacion = substr($fecha_termino, 8, 8);
                        $mesTerminacion = substr($fecha_termino, 5, 2);
                        $anioTerminacion = substr($fecha_termino, 0, 4);
                        $diaInicio = substr($fecha_inicio, 8, 8);
                        $mesInicio = substr($fecha_inicio, 5, 2);
                        $anioInicio = substr($fecha_inicio, 0, 4); 
                        $sw = 2;
                    }
                }
            }
            
        }    
        //codigo que valide los dias
        
        $febrero = 0;
        $mes = $mesInicio-1;
        if($mes == 2){
            if($ano == 1){
              $febrero = 29;
            }else{
              $febrero = 28;
            }
        }else if($mes <= 7){
            if($mes==0){
             $febrero = 31;
            }else if($mes%2==0){
                 $febrero = 30;
                }else{
                   $febrero = 31;
                }
        }else if($mes > 7){
              if($mes%2==0){
                  $febrero = 31;
              }else{
                  $febrero = 30;
              }
       }
        if(($anioInicio > $anioTerminacion) || ($anioInicio == $anioTerminacion && $mesInicio > $mesTerminacion) || 
            ($anioInicio == $anioTerminacion && $mesInicio == $mesTerminacion && $diaInicio > $diaTerminacion)){
                //mensaje
        }else{
            if($mesInicio <= $mesTerminacion){
                $anios = $anioTerminacion - $anioInicio;
                if($diaInicio <= $diaTerminacion){
                    $meses = $mesTerminacion - $mesInicio;
                    $dies = $diaTerminacion - $diaInicio;
                }else{
                    if($mesTerminacion == $mesInicio){
                       $anios = $anios - 1;
                    }
                    $meses = ($mesTerminacion - $mesInicio - 1 + 12) % 12;
                    $dies = $febrero-($diaInicio - $diaTerminacion);
                }
            }else{
                $anios = $anioTerminacion - $anioInicio - 1;
                if($diaInicio > $diaTerminacion){
                    $meses = $mesTerminacion - $mesInicio -1 +12;
                    $dies = $febrero - ($diaInicio-$diaTerminacion);
                }else{
                    $meses = $mesTerminacion - $mesInicio + 12;
                    $dies = $diaTerminacion - $diaInicio;
                }
            }
            $total_dias = (($anios * 360) + ($meses * 30)+ ($dies))+1;
        }
        
        //TERMINA EL CODIGO DE LOS DIAS 
        $suma = 0; $auxilio_transporte = 0;
        $suma2 = 0;
        $total_suma = 0;
        $dias_restar = 0;
        $salario_promedio = 0;
       
        if ($sw == 3){
                $diff = abs(strtotime($model->ultimo_pago_prima) - strtotime($model->fecha_termino_contrato));
                $years = floor($diff / (365 * 60 * 60 * 24));
                $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24)); 
                $anos_restar = $years * 360;
                $meses_restar = $months * 30;
                $dias_restar = $days *-1 ;
                $nomina = ProgramacionNomina::find()->where(['=','id_contrato', $model->id_contrato])
                                                    ->andWhere(['=','id_tipo_nomina', 2])->orderBy('id_programacion DESC')->one();
                $total_suma = $nomina->salario_promedio;
                $total_dias = $dias_restar;
               
        }
        if($sw == 1){
            $vector_nomina = ProgramacionNomina::find()->where(['>=', 'fecha_desde', $fecha_inicio_dias])
                                                                       ->andWhere(['=','id_contrato', $model->id_contrato]) 
                                                                       ->all();
            foreach ($vector_nomina as $sumar_ibc_prestacional):
              $suma +=  $sumar_ibc_prestacional->ibc_prestacional;
              $suma2 += $sumar_ibc_prestacional->total_ibc_no_prestacional;
            endforeach;
             $total_suma = $suma + $suma2 + $contrato->ibp_prima_inicial;
        }
        if($sw == 2){
            $vector_nomina = ProgramacionNomina::find()->where(['>=', 'fecha_inicio_contrato', $model->fecha_inicio_contrato])
                                                                       ->andWhere(['=','id_contrato', $model->id_contrato]) 
                                                                       ->all();
            foreach ($vector_nomina as $sumar_ibc_prestacional):
              $suma +=  $sumar_ibc_prestacional->ibc_prestacional;
              $suma2 += $sumar_ibc_prestacional->total_ibc_no_prestacional;
            endforeach;
             $total_suma = $suma + $suma2 + $contrato->ibp_prima_inicial;
        }
        $auxiliar = 0;
        if ($configuracion_p->aplicar_ausentismo == 1){
            $ausentismo = ConfiguracionLicencia::find()->where(['=','ausentismo', 1])->all();
            foreach ($ausentismo  as $dato):
                 $nomina = ProgramacionNomina::find()->where(['=','id_contrato', $model->id_contrato])->all();
                 foreach ($nomina as $nomina):
                     $detalle_no = ProgramacionNominaDetalle::find()->where(['=','id_programacion', $nomina->id_programacion])->andWhere(['=','codigo_salario', $dato->codigo_salario])->one();
                      if($detalle_no){   
                           $auxiliar += $ProgramacionNominaDetalle->dias_licencia_descontar; 
                      }     
                 endforeach; 
            endforeach;
        }
        $transporte = ConfiguracionSalario::find()->where(['=','estado', 1])->one();
        if($total_dias == 0){
            $total_dias_reales = 0;
            $auxiliar = 0;
            $salario_promedio = 0;
            
        }else{
            
            if($dias_restar < 0){
                $salario_promedio = $total_suma;
                $total_dias_reales = $dias_restar;
                $total_dias = $dias_restar;
            }else{
                $total_dias_reales = $total_dias - $auxiliar;
                $salario_promedio =($total_suma / $total_dias)*30;
            }   
        }    
        
        if($contrato->auxilio_transporte == 1){
            $auxilio_transporte = $transporte->auxilio_transporte_actual;
            $prima = round((($salario_promedio + $auxilio_transporte)* $total_dias_reales)/360);
        }else{
             $prima = round((($salario_promedio + $auxilio_transporte)* $total_dias_reales)/360);
        }    
       $detalle_prestacion = PrestacionesSocialesDetalle::find()->where(['=','id_prestacion', $model->id_prestacion])->andWhere(['=','codigo_salario', $concepto->codigo_salario])->one();
       if(!$detalle_prestacion){
           $detalle = new PrestacionesSocialesDetalle();
           $detalle->id_prestacion = $model->id_prestacion;
           $detalle->codigo_salario = $concepto->codigo_salario;
           $detalle->fecha_inicio = $model->ultimo_pago_prima;
           $detalle->fecha_final = $model->fecha_termino_contrato;
           $detalle->nro_dias = $total_dias;
           $detalle->dias_ausentes = $auxiliar;
           $detalle->total_dias = $total_dias_reales;
           $detalle->salario_promedio_prima = $salario_promedio;
           $detalle->auxilio_transporte = $auxilio_transporte;
           $detalle->valor_pagar = $prima;
           $detalle->insert(false);
       }

        
    }
    
    public function actionDesgenerar($id, $pagina)
    {
      $model = PrestacionesSociales::find()->where(['=','id_prestacion', $id])->one();
      $model->estado_generado = 0;
      $model->save(false);
     $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina,
          'model' => $model,
          ]);
    }
     public function actionDesgeneraraplicar($id, $pagina)
    {
      $model = PrestacionesSociales::find()->where(['=','id_prestacion', $id])->one();
      $model->estado_aplicado = 0;
      $model->save(false);
     $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina,
          'model' => $model,
          ]);
    }
        
    // contralador que aplica los pagos, saldos y netos a pagar
     public function actionAplicarpagos($id, $pagina)
     {
        $model_prestacion = PrestacionesSociales::findOne($id);
        $model = PrestacionesSocialesDetalle::find()->where(['=','id_prestacion', $id])->orderBy('codigo_salario ASC')->all();
        $model_credito = PrestacionesSocialesCreditos::find()->where(['=','id_prestacion', $id])->all();
        $model_adicion = PrestacionesSocialesAdicion::find()->where(['=','id_prestacion', $id])->andWhere(['=','tipo_adicion', 1])->all();
        $model_descuento = PrestacionesSocialesAdicion::find()->where(['=','id_prestacion', $id])->andWhere(['=','tipo_adicion', 2])->all();
        $conf_prima = ConfiguracionPrestaciones::findone(1);
        $conf_cesantia = ConfiguracionPrestaciones::findone(2);
        $conf_interes = ConfiguracionPrestaciones::findone(3);
        $conf_vacacion = ConfiguracionPrestaciones::findone(4);
        $total_prestacion = 0;
        $total_deduccion_credito = 0;
        $total_adicion = 0;
        $deduccion_descuento = 0;
        //codigo que actualiza los fechas
        foreach ($model as $actualizar):
            if($conf_prima->codigo_salario == $actualizar->codigo_salario){
                $model_prestacion->dias_primas = $actualizar->total_dias;
                $model_prestacion->ibp_prima = $actualizar->salario_promedio_prima;
                $model_prestacion->dias_ausencia_prima = $actualizar->dias_ausentes;
                $model_prestacion->save(false);
            }
            if($conf_cesantia->codigo_salario == $actualizar->codigo_salario){
                $model_prestacion->dias_cesantias = $actualizar->total_dias;
                $model_prestacion->ibp_cesantias = $actualizar->salario_promedio_prima;
                $model_prestacion->dias_ausencia_cesantias = $actualizar->dias_ausentes;
                $model_prestacion->save(false);
            }
            if($conf_interes->codigo_salario == $actualizar->codigo_salario){
                $model_prestacion->interes_cesantia = $actualizar->valor_pagar;
                $model_prestacion->save(false);
            }
            if($conf_vacacion->codigo_salario == $actualizar->codigo_salario){
                $model_prestacion->dias_vacaciones = $actualizar->total_dias;
                $model_prestacion->ibp_vacaciones = $actualizar->salario_promedio_prima;
                $model_prestacion->dias_ausencia_vacaciones = $actualizar->dias_ausentes;
                $model_prestacion->save(false);
            }
            
        endforeach;
        
         //calcula todas la prestaciones
        foreach ($model as $calcular):
             $total_prestacion +=  $calcular->valor_pagar;
        endforeach;
        
         //calculos los creditos
        foreach ($model_credito as $calcular_credito):
             $total_deduccion_credito +=  $calcular_credito->deduccion;
        endforeach;
        //calculos las adiciones
        foreach ($model_adicion as $calcular_adicion):
            $total_adicion +=  $calcular_adicion->valor_adicion;
        endforeach;
         //calculos descuentos
        foreach ($model_descuento as $calcular_descuento):
            $deduccion_descuento +=  $calcular_descuento->valor_adicion;
        endforeach;
        
        //codigo que actualizada
        $model_prestacion->total_devengado = $total_prestacion + $model_prestacion->total_indemnizacion + $total_adicion;
        $model_prestacion->total_deduccion = $total_deduccion_credito + $deduccion_descuento;
        $model_prestacion->total_pagar = $model_prestacion->total_devengado - $model_prestacion->total_deduccion;
        $model_prestacion->estado_aplicado = 1;
        $model_prestacion->save(false);
        $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina]);  
     }
    //CERRAR EL PROCESO DE LAS PRESTACIONES
    public function actionCerrarprestacion($id, $pagina)
    {
        //este codigo salda los creditos y hace el abono
        $credito = PrestacionesSocialesCreditos::find()->where(['=','id_prestacion', $id])->andWhere(['=','estado_cerrado', 0])->all();
        $total = count($credito);
        if($total > 0){
            foreach ($credito as $creditoprestacion){
                $abono = new \app\models\AbonoCredito();
                $abono->id_credito = $creditoprestacion->id_credito;
                $abono->id_tipo_pago = 4;
                $abono->vlr_abono = $creditoprestacion->deduccion;
                $abono->saldo = $creditoprestacion->saldo_credito - $abono->vlr_abono;
                $abono->cuota_pendiente = 0;
                $abono->observacion = 'Deducción por prestaciones';
                $abono->usuariosistema = Yii::$app->user->identity->username;
                $abono->insert(false);
                $credito_actualizar = Credito::findOne($creditoprestacion->id_credito);
                $credito_actualizar->saldo_credito = $abono->saldo;
                $credito_actualizar->estado_credito = 0;
                $credito_actualizar->observacion = 'Se cancelo por prestaciones';
                $credito_actualizar->save(false);
                $creditoprestacion->estado_cerrado = 1;
                $creditoprestacion->save(false);
            }
        }
        
        // este codigo genera el consecutivo de las prestaciones.   
        $modelo = PrestacionesSociales::findOne($id);
        $consecutivo = Consecutivo::findOne(9);
        $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
        $consecutivo->save(false);
        $modelo->nro_pago = $consecutivo->consecutivo;
        $modelo->estado_cerrado = 1;
        $modelo->save(false);
        
        //codigo que actualiza el contrato
        $contrato = Contrato::find()->where(['=','id_contrato', $modelo->id_contrato])->one();
        $contrato->ultima_cesantia = $modelo->fecha_termino_contrato;
        $contrato->ultima_vacacion = $modelo->fecha_termino_contrato;
        $contrato->ibp_cesantia_inicial = 0;
        $contrato->ibp_prima_inicial = 0;
        $contrato->ibp_recargo_nocturno = 0;
        $contrato->save(false);
        
        $this->redirect(["prestaciones-sociales/view", 'id' => $id, 'pagina' => $pagina]);  
    }              

     protected function findModel($id)
    {
        if (($model = PrestacionesSociales::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionImprimir($id)
    {
                                
        return $this->render('../formatos/prestacionesSociales', [
            'model' => $this->findModel($id),
            
        ]);
    }
}
