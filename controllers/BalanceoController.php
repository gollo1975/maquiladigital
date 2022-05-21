<?php

namespace app\controllers;
//modelos
use app\models\Ordenproduccion;
use app\models\Balanceo;
use app\models\BalanceoDetalle;
use app\models\BalanceoSearch;
use app\models\UsuarioDetalle;
use app\models\FormFiltroModulos;
use app\models\FlujoOperaciones;
//clases
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
use yii\db\Expression;
use yii\db\Query;


/**
 * BalanceoController implements the CRUD actions for Balanceo model.
 */
class BalanceoController extends Controller
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
     * Lists all Balanceo models.
     * @return mixed
     */
    //INDEZ INICIAL
    public function actionIndex() {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',99])->all()){
            $form = new FormFiltroModulos();
            $idcliente = null;
            $fecha_inicio = null;
            $idordenproduccion = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $fecha_inicio = Html::encode($form->fecha_inicio);
                    $idordenproduccion = Html::encode($form->idordenproduccion);
                    $table = Balanceo::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['>=', 'fecha_inicio', $fecha_inicio])
                            ->andFilterWhere(['=', 'idordenproduccion', $idordenproduccion]);
                    $table = $table->orderBy('id_balanceo desc');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 50,
                        'totalCount' => $count->count()
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){
                        //$table = $table->all();
                        $this->actionExcelconsulta($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = Balanceo::find()
                        ->orderBy('id_balanceo desc');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 50,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                    $this->actionExcelconsulta($tableexcel);
                }
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
    // INDEX QUE CONSULTA LOS BALANCEOS POR OPERARIOS
    
    public function actionIndexbalanceoperario() {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',114])->all()){
            $form = new \app\models\FormFiltroBalanceoOperario();
            $id_balanceo = null;
            $id_proceso = null;
            $id_operario = null;
            $id_tipo = null;
            $operaciones = \app\models\ProcesoProduccion::find()->orderBy('proceso ASC')->all();
            $maquinas = \app\models\TiposMaquinas::find()->orderBy('descripcion ASC')->all();
            $operarios = \app\models\Operarios::find()->orderBy('nombrecompleto ASC')->all();
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $id_balanceo = Html::encode($form->id_balanceo);
                    $id_proceso = Html::encode($form->id_proceso);
                    $id_operario = Html::encode($form->id_operario);
                    $id_tipo = Html::encode($form->id_tipo);
                    $table = BalanceoDetalle::find()
                            ->andFilterWhere(['=', 'id_operario', $id_operario])
                            ->andFilterWhere(['=', 'id_tipo', $id_tipo])
                            ->andFilterWhere(['=', 'id_proceso', $id_proceso])
                            ->andFilterWhere(['=', 'id_balanceo', $id_balanceo]);
                    $table = $table->orderBy('id_balanceo desc');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 50,
                        'totalCount' => $count->count()
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){
                        //$table = $table->all();
                        $this->actionExcelconsultaOperarios($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = BalanceoDetalle::find()
                        ->orderBy('id_balanceo desc');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 50,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                    $this->actionExcelconsultaOperarios($tableexcel);
                }
            }
            $to = $count->count();
            return $this->render('indexbalanceoperario', [
                        'model' => $model,
                        'form' => $form,
                        'pagination' => $pages,
                        'operaciones' => ArrayHelper::map($operaciones, "idproceso", "proceso"),
                        'maquinas' => ArrayHelper::map($maquinas, "id_tipo", "descripcion"),
                        'operarios' => ArrayHelper::map($operarios, "id_operario", "nombrecompleto"),
                        'indicador' => 1,
            ]);
        }else{
            return $this->redirect(['site/sinpermiso']);
        }
        }else{
            return $this->redirect(['site/login']);
        }
    }
    /**
     * Displays a single Balanceo model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $idordenproduccion, $id_proceso_confeccion)
    {
       if ($id_proceso_confeccion == 1){
          $flujo_operaciones = FlujoOperaciones::find()->where(['=', 'idordenproduccion', $idordenproduccion])->andWhere(['=','operacion', 0])->orderBy('pieza, operacion, orden_aleatorio asc')->all();
       }else{
          $flujo_operaciones = FlujoOperaciones::find()->where(['=', 'idordenproduccion', $idordenproduccion])->andWhere(['=','operacion', 1])->orderBy('pieza, operacion, orden_aleatorio asc')->all(); 
       }   
        $balanceo_detalle = BalanceoDetalle::find()->where(['=', 'id_balanceo', $id])->orderBy('id_operario asc')->all();
        $operario = \app\models\Operarios::find()->where(['=','estado', 1])->orderBy('nombrecompleto ASC')->all();
        $balanceo = Balanceo::findOne($id);
        //Proceso que guarda el balanceo manual
        if (isset($_POST["guardar"])) {
            if ($_POST["id_operario"] > 0) {  
                if (isset($_POST["idproceso"])) {
                     $intIndice = 0;
                    foreach ($_POST["idproceso"] as $intCodigo) {
                        $proceso = FlujoOperaciones::find()->where(['=','idproceso', $intCodigo])->andWhere(['=','idordenproduccion', $idordenproduccion])->one();
                        $table = new BalanceoDetalle();
                        $table->id_proceso = $intCodigo;
                        $table->id_balanceo = $id;
                        $table->id_tipo = $proceso->id_tipo;
                        $table->id_operario = $_POST["id_operario"];
                        $table->segundos = $proceso->segundos;
                        $table->minutos = $proceso->minutos;
                        $table->total_minutos = $proceso->minutos;
                        $table->total_segundos = $proceso->segundos;
                        $table->usuariosistema = Yii::$app->user->identity->username;
                        $table->ordenamiento = $proceso->orden_aleatorio;
                        $table->insert();
                     } 
                    if($id_proceso_confeccion == 1){
                        $this->ActualizarSegundos($id);
                    }   
                    return $this->redirect(["balanceo/view",
                      'id'=> $id,
                      'flujo_operaciones' => $flujo_operaciones,   
                      'idordenproduccion' => $idordenproduccion,
                      'balanceo_detalle' => $balanceo_detalle,
                      'operario'=> $operario,
                      'id_proceso_confeccion' => $id_proceso_confeccion,
                    ]); 
                }else{
                   Yii::$app->getSession()->setFlash('warning', 'Debe seleccionar las operaciones para el operario.');
                    return $this->redirect(["balanceo/view",
                      'id'=> $id,
                       'flujo_operaciones' => $flujo_operaciones,
                      'idordenproduccion' => $idordenproduccion,
                      'balanceo_detalle' => $balanceo_detalle,
                      'operario'=> $operario,
                       'id_proceso_confeccion' => $id_proceso_confeccion, 
                    ]); 
                }
            }else{
                  Yii::$app->getSession()->setFlash('error', 'Debe de seleccionar un operario.');
                  return $this->redirect(["balanceo/view",
                      'id'=> $id,
                      'flujo_operaciones' => $flujo_operaciones,
                      'idordenproduccion' => $idordenproduccion,
                      'balanceo_detalle' => $balanceo_detalle,
                      'operario'=> $operario,
                      'id_proceso_confeccion' => $id_proceso_confeccion,
                    ]); 
            }
        } //Fin de proceso..
        //Proceso que guarda automatico el balanceo
        if (isset($_POST["generar"])){
            if ($_POST["id_operario"] > 0) {
                $total = $balanceo->tiempo_operario + 0.5;
                if(count($_POST["id_operario"]) == $balanceo->cantidad_empleados){
                    foreach ($_POST["id_operario"] as $variable):
                        //carga las operaciones
                        $suma =0;
                        $operaciones = FlujoOperaciones::find()->where(['=','idordenproduccion', $idordenproduccion])->andWhere(['=','operacion', 0])->orderBy('minutos DESC')->all();
                        if (count($operaciones) >= $balanceo->cantidad_empleados){
                            foreach ($operaciones as $operacion):
                                if(!BalanceoDetalle::find()->where(['=','id_proceso', $operacion->idproceso])->andWhere(['=','id_balanceo', $id])->one()){
                                    $tabla = new BalanceoDetalle();
                                    $tabla->id_proceso = $operacion->idproceso;
                                    $tabla->id_balanceo = $id;
                                    $tabla->id_tipo = $operacion->id_tipo;
                                    $tabla->id_operario = $variable;
                                    $tabla->segundos = $operacion->segundos;
                                    $tabla->minutos = $operacion->minutos;
                                    $tabla->total_segundos = $operacion->segundos;
                                    $tabla->usuariosistema = Yii::$app->user->identity->username;
                                    $tabla->ordenamiento = $operacion->orden_aleatorio;
                                    $tabla->insert();
                                }   
                            endforeach; 
                            //listar el detalle balanceo
                            $detalle = BalanceoDetalle::find()->where(['=','id_balanceo', $id])->andWhere(['=','aplicado', 0])->all();
                            foreach ($detalle as $detalles):
                               $detalles->id_operario = (NULL);
                               $detalles->save(false);
                               if($detalles->id_operario == (NULL)){
                                    $suma += $detalles->minutos;
                                    if($suma <= $total){
                                        $detalles->id_operario = $variable;
                                        $detalles->aplicado = 1;
                                        $detalles->save(false);
                                        $this->ActualizarSegundos($id);
                                    }else{
                                        break;
                                    }    
                               }
                            endforeach;
                        }else{
                            Yii::$app->getSession()->setFlash('warning', 'Este balanceo no se puede hacer automatico, debe ser manual.');
                            return $this->redirect(["balanceo/view",
                                 'id'=> $id,
                                 'flujo_operaciones' => $flujo_operaciones,
                                 'idordenproduccion' => $idordenproduccion,
                                 'balanceo_detalle' => $balanceo_detalle,
                                 'operario'=> $operario,
                                'id_proceso_confeccion' => $id_proceso_confeccion,
                               ]); 
                        }      
                      endforeach;  //termina el ciclo
                      $final_archivo = BalanceoDetalle::find()->where(['=','id_balanceo', $id])->andWhere(['=','aplicado', 0])->one();
                      if($final_archivo){
                        $detalle_balanceo = BalanceoDetalle::find()->where(['=','id_balanceo', $id])->orderBy('total_minutos ASC')->one();
                        $final_archivo->id_operario = $detalle_balanceo->id_operario;
                        $final_archivo->save(false);
                        $this->ActualizarSegundos($id);
                      }  
                       return $this->redirect(["balanceo/view",
                                 'id'=> $id,
                                 'flujo_operaciones' => $flujo_operaciones,
                                 'idordenproduccion' => $idordenproduccion,
                                 'balanceo_detalle' => $balanceo_detalle,
                                 'operario'=> $operario,
                                'id_proceso_confeccion' => $id_proceso_confeccion,
                               ]); 
                      
                }else{  
                    Yii::$app->getSession()->setFlash('warning', 'La cantidad seleccionada de operarios ('.count($_POST["id_operario"]).'), es mayor a la cantidad del balanceo ('.$balanceo->cantidad_empleados.')');
                    return $this->redirect(["balanceo/view",
                         'id'=> $id,
                         'flujo_operaciones' => $flujo_operaciones,
                         'idordenproduccion' => $idordenproduccion,
                         'balanceo_detalle' => $balanceo_detalle,
                         'operario'=> $operario,
                         'id_proceso_confeccion' => $id_proceso_confeccion,
                       ]); 
                }            
           
            }else{
                 Yii::$app->getSession()->setFlash('error', 'Debe de seleccionar los ('.$balanceo->cantidad_empleados.') operarios para el balanceo.');
                 return $this->redirect(["balanceo/view",
                      'id'=> $id,
                      'flujo_operaciones' => $flujo_operaciones,
                      'idordenproduccion' => $idordenproduccion,
                      'balanceo_detalle' => $balanceo_detalle,
                      'operario'=> $operario,
                      'id_proceso_confeccion' => $id_proceso_confeccion,
                    ]); 
            }
        }
        //PROCESO QUE ACTIVA O DESACTIVA EL ESTADO DE LA OPERACION
        if(isset($_POST['aplicarestado'])){
            if(isset($_REQUEST['id_detalle_balanceo'])){
                $intIndice = 0;
                foreach ($_POST['id_detalle_balanceo'] as $intCodigo):
                    if($_POST['id_detalle_balanceo'][$intIndice]){
                        $id_detalle = $_POST["id_detalle_balanceo"][$intIndice];
                        $detalle = BalanceoDetalle::findOne($id_detalle);
                        if($detalle->estado_operacion == 1){
                            $detalle->estado_operacion = 0;
                             $detalle->save(false);
                        }else{
                            $detalle->estado_operacion = 1;
                            $detalle->save(false);
                        }            
                    }
                    $intIndice++;
                endforeach;
            }
             return $this->redirect(['view',
                    'id' => $id,
                    'idordenproduccion' => $idordenproduccion,
                    'id_proceso_confeccion' => $id_proceso_confeccion,
                    'operario'=> $operario,
                    'balanceo_detalle' => $balanceo_detalle,
                    'flujo_operaciones' => $flujo_operaciones]);
                    
        }
        
            return $this->render('view', [
                 'model' => $this->findModel($id),
                 'flujo_operaciones' => $flujo_operaciones,
                'balanceo_detalle' => $balanceo_detalle,
                'idordenproduccion' => $idordenproduccion,
                'operario'=> $operario,
                'id_proceso_confeccion' => $id_proceso_confeccion,
             ]);
      
    }
    
  //vista de la consulta de balanceo y operario
  public function actionViewconsultabalanceo($id, $idordenproduccion, $indicador)
    {
        $flujo_operaciones = FlujoOperaciones::find()->where(['=', 'idordenproduccion', $idordenproduccion])->orderBy('operacion, orden_aleatorio asc')->all();
        $balanceo_detalle = BalanceoDetalle::find()->where(['=', 'id_balanceo', $id])->orderBy('id_operario asc')->all();
        $operarios = \app\models\Operarios::find()->where(['=','estado', 1])->orderBy('nombrecompleto ASC');
        return $this->render('viewconsultabalanceo', [
                 'model' => $this->findModel($id),
                 'flujo_operaciones' => $flujo_operaciones,
                'balanceo_detalle' => $balanceo_detalle,
                'idordenproduccion' => $idordenproduccion,
                'operarios'=> $operarios,
                'indicador' => $indicador,
        ]);
    }
    
 // codigo que actualiza los minutos y segundos de los operarios
    
 protected function ActualizarSegundos($id)
    { 
    $vector_balanceo = Balanceo::findOne($id);
    $operarios = \app\models\Operarios::find()->where(['=','estado', 1])->all();
    $total_s = 0;
    $total_m = 0;
    foreach ($operarios as $operario):
          $query =new Query();
          $table = BalanceoDetalle::find()->select([new Expression('SUM(minutos) as total_minutos'), 'id_operario'])
                      ->where(['=','id_operario', $operario->id_operario])
                      ->andWhere(['=','id_balanceo', $id])
                      ->andWhere (['=','estado_operacion', 0])
                      ->groupBy('id_operario')
                      ->all();       
        foreach ($table as $valor):
             $total_m = $valor->total_minutos;
             $balanceo2 = BalanceoDetalle::find()->where(['=','id_operario', $operario->id_operario])->andWhere(['=','id_balanceo', $id])->all();
             foreach ($balanceo2 as $act):
                 $act->total_minutos = $total_m;
                 $act->total_segundos = $act->total_minutos * 60;
                 $act->sobrante_faltante = ''.number_format($vector_balanceo->tiempo_operario - $total_m, 2);
                 $act->save();
             endforeach;
        endforeach;   
    endforeach;  
    }

    public function actionCreate($idordenproduccion)
    {
        $model = new Balanceo();
        $orden = Ordenproduccion::findOne($idordenproduccion);
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {           
            if ($model->validate()) {
                $balanceo = Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])->andWhere(['=','modulo', $model->modulo])->one();
                if($balanceo){
                    Yii::$app->getSession()->setFlash('error', 'Error en el modulo, ya existe este numero de modulo para esta OP');
                }else{
                    $orden = Ordenproduccion::findOne($idordenproduccion);
                    $table = new Balanceo();
                    $table->idordenproduccion = $idordenproduccion;
                    $table->idcliente = $orden->idcliente;
                    $table->cantidad_empleados = $model->cantidad_empleados;
                    $table->fecha_inicio = $model->fecha_inicio;
                    $table->cantidad_empleados = $model->cantidad_empleados;
                    $table->modulo = $model->modulo;
                    $table->id_planta = $model->id_planta  ;
                    $table->total_minutos = $orden->sam_operativo;
                    $table->tiempo_balanceo = $orden->sam_balanceo;
                    $table->total_segundos = $orden->segundosficha;
                    $table->tiempo_operario = ''.number_format($table->tiempo_balanceo /$table->cantidad_empleados,2); 
                    $table->observacion = $model->observacion;
                    $table->id_proceso_confeccion = $model->id_proceso_confeccion;
                    $table->hora_inicio = $model->hora_inicio;
                    $table->porcentaje = 100;
                    $table->usuariosistema = Yii::$app->user->identity->username;
                    $table->save(false);
                    $this->actionActualizarfechaterminacion($idordenproduccion);
                    return $this->redirect(["balanceo/index"]); 
                }     
            }else{
                $model->getErrors();
            }
        }    
        return $this->render('create', [
            'model' => $model,
            'orden' => $orden,
        ]);
    }
    //suproceso para validar la fecha de terminacion
    protected function actionActualizarfechaterminacion($idordenproduccion)
    {
        $minutos = 0; $cantidad = 0; $totales = 0; $total_dias = 0;
        $unidades = 0;
        $horario = \app\models\Horario::findOne(1);
        $orden = Ordenproduccion::findOne($idordenproduccion);
        $balaceo = Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
        $minutos = $orden->segundosficha / 60;
        $unidades = (60/$minutos);
        foreach ($balaceo as $val):
            $cantidad += $val->cantidad_empleados;
        endforeach;
        $totales = round(($unidades * $cantidad) * $horario->total_horas);
        $total_dias = round($orden->cantidad / $totales);
        $fecha = date($val->fecha_inicio);
        $date_dato = strtotime('+'.($total_dias).' day', strtotime($fecha)-1);
        $date_dato = date('Y-m-d', $date_dato);
        $val->fecha_terminacion = $date_dato;
        $val->save(false);
        $orden->fechaentrega = $date_dato;
        $orden->save(false);
    }
    
   public function actionUpdate($id, $idordenproduccion)
    {
        $model = new Balanceo();
        $orden = Ordenproduccion::findOne($idordenproduccion);
        $balanceo = Balanceo::findOne($id);
       if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
      
        if ($model->load(Yii::$app->request->post())) {            
                $balanceo_detalle = BalanceoDetalle::find()->where(['=','id_balanceo', $id])->one();
                if($balanceo_detalle){
                    Yii::$app->getSession()->setFlash('error', 'Error al modificar: este modulo no se puede modificar la informacion porque presenta proceso de operaciones de prendas, favor consulte con el administrador.!'); 
                }else{
                     $table = balanceo::find()->where(['id_balanceo'=>$id])->one();
                    if ($table){
                        $table->fecha_inicio = $model->fecha_inicio;
                        $table->cantidad_empleados = $model->cantidad_empleados;
                        $table->modulo = $model->modulo;
                        $table->observacion = $model->observacion;
                        $table->total_segundos = $orden->segundosficha;
                        $table->total_minutos = ''.number_format($table->total_segundos /60,2);
                        $table->tiempo_operario = ''.number_format($balanceo->tiempo_balanceo / $table->cantidad_empleados ,3);
                        $table->id_proceso_confeccion = $model->id_proceso_confeccion;
                        $table->id_planta = $model->id_planta;
                        $table->hora_inicio = $model->hora_inicio;
                        $table->save(false);
                        $this->actionActualizarfechaterminacion($idordenproduccion);
                        return $this->redirect(["balanceo/index"]);  
                    }
                }
        }
        if (Yii::$app->request->get("id")) {
            $table = balanceo::find()->where(['id_balanceo' => $id])->one();
            if ($table) {     

               $model->fecha_inicio = $table->fecha_inicio;
               $model->cantidad_empleados = $table->cantidad_empleados;
               $model->modulo = $table->modulo;
               $model->id_proceso_confeccion = $table->id_proceso_confeccion;
               $model->id_planta = $table->id_planta;
                $model->hora_inicio = $table->hora_inicio;
               $model->observacion = $table->observacion;
           }else{
                return $this->redirect(['index']);
           }
        } else {
                return $this->redirect(['index']);    
        }
        return $this->render('update', [
            'model' => $model,
            'id'=>$id, 
            'orden' => $orden,
        ]);
    }

    //Actualizar cantidad de operarios
    
     public function actionNuevacantidad($id, $id_proceso_confeccion) {  
        $model = new \app\models\FormParametroCantidadOperario();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {            
            if ($model->validate()) {
                $archivo = Balanceo::findOne($id);
                if (isset($_POST["actualizaroperario"])) { 
                    $archivo->cantidad_empleados = $model->cantidad_empleados;
                    $archivo->save(false);
                    $this->actionActualizarfechaterminacion($model->idordenproduccion);
                    return $this->redirect(["balanceo/view", 'id' => $id, 'idordenproduccion' => $model->idordenproduccion, 'id_proceso_confeccion' => $id_proceso_confeccion]);                                                     
                }
            }
        }
        if (Yii::$app->request->get("id")) {
            $table = Balanceo::findOne($id);            
            if ($table) {                                
                $model->tiempo_balanceo = $table->tiempo_balanceo;                
                $model->fecha_inicio = $table->fecha_inicio; 
                $model->fecha_final = $table->fecha_terminacion; 
                $model->idordenproduccion = $table->idordenproduccion;
            }
            
        }
        return $this->renderAjax('_nuevacantidadoperario', ['model' => $model, 'id' => $id, 'id_proceso_confeccion' => $id_proceso_confeccion]);
    }
    
   public function actionEliminar($id) {
        if (Yii::$app->request->post()) {
            $balanceo = Balanceo::findOne($id);
            if ((int) $id) {
                try {
                    Balanceo::deleteAll("id_balanceo=:id_balanceo", [":id_balanceo" => $id]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    $this->redirect(["balanceo/index"]);
                } catch (IntegrityException $e) {
                    $this->redirect(["balanceo/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el modulo Nro: ' . $balanceo->modulo . ', tiene registros asociados en otros procesos');
                } catch (\Exception $e) {

                    $this->redirect(["balanceo/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el modulo Nro:  ' . $balanceo->modulo . ', tiene registros asociados en otros procesos');
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el registros, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("balanceo/index") . "'>";
            }
        } else {
            return $this->redirect(["balanceo/index"]);
        }
    }
   
   public function actionEliminardetalle($id_detalle, $id, $idordenproduccion, $id_proceso_confeccion) {
        if (Yii::$app->request->post()) {
            $balanceo_detalle = BalanceoDetalle::findOne($id_detalle);
            if ((int) $id_detalle) {
                try {
                    BalanceoDetalle::deleteAll("id_detalle=:id_detalle", [":id_detalle" => $id_detalle]);
                    if($id_proceso_confeccion == 2){
                        
                    }else{
                       $this->ActualizarSegundos($id);    
                    }
                    
                    $this->redirect(["balanceo/view",'id'=>$id, 'idordenproduccion'=>$idordenproduccion, 'id_proceso_confeccion' => $id_proceso_confeccion]);
                } catch (IntegrityException $e) {
                    $this->redirect(["balanceo/view",'id'=>$id, 'idordenproduccion'=>$idordenproduccion, 'id_proceso_confeccion' => $id_proceso_confeccion]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar al eliminar el registro.!');
                } catch (\Exception $e) {

                    $this->redirect(["balanceo/view",'id'=>$id, 'idordenproduccion'=>$idordenproduccion, 'id_proceso_confeccion' => $id_proceso_confeccion]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar al eliminar el registro.!');
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el registros, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute(["balanceo/view", 'id'=>$id, 'idordenproduccion'=>$idordenproduccion, 'id_proceso_confeccion' => $id_proceso_confeccion]) . "'>";
            }
        } else {
            return $this->redirect(["balanceo/view",'id'=>$id, 'idordenproduccion'=>$idordenproduccion, 'id_proceso_confeccion' => $id_proceso_confeccion]);
        }
    }
    
    public function actionEditaroperacionasignada($id_detalle, $id, $idordenproduccion, $id_proceso_confeccion) {
       
        $model = new BalanceoDetalle;
        $balanceo = Balanceo::findOne($id);   
        
        $tabla_detalle = BalanceoDetalle::findOne($id_detalle);
       if ($model->load(Yii::$app->request->post())) {                        
            $tabla_detalle->id_tipo = $model->id_tipo;
            $tabla_detalle->id_operario = $model->id_operario;
            $tabla_detalle->segundos = $model->segundos;
            $tabla_detalle->minutos = $model->minutos;    
            $tabla_detalle->ordenamiento = $model->ordenamiento;    
            $tabla_detalle->estado_operacion = $model->estado_operacion;
            $tabla_detalle->save(false);   
            if($id_proceso_confeccion == 1){
                $this->ActualizarSegundos($id);
                $this->actionActualizarSobranteRestante($id);
            }    
            return $this->redirect(['balanceo/view','id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_proceso_confeccion' => $id_proceso_confeccion]);
        }
        if (Yii::$app->request->get("id_detalle")) {
            $table = BalanceoDetalle::find()->where(['id_detalle' => $id_detalle])->one();
            if ($table) {
                $model->id_tipo = $table->id_tipo;
                $model->id_operario= $table->id_operario;
                $model->segundos = $table->segundos;
                $model->minutos = $table->minutos;
                $model->ordenamiento = $table->ordenamiento;
                $model->estado_operacion = $table->estado_operacion;
                      
            }    
        }
        return $this->render('_formeditardetallebalanceo', [
            'model' => $model,
            'balanceo' => $balanceo,
            'idordenproduccion' => $idordenproduccion,
            'id_proceso_confeccion' => $id_proceso_confeccion,
           ]);         
    } 

    protected function actionActualizarSobranteRestante($id) 
    {
        $total = 0;
        $balanceo = Balanceo::findOne($id);
        $tabla_detalle = BalanceoDetalle::findAll($id);
        foreach ($tabla_detalle as $dato):
            $total =  $balanceo->tiempo_operario - $dato->total_minutos;
            $dato->save();
        endforeach;
    }
    
    protected function findModel($id)
    {
        if (($model = Balanceo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionCerrarmodulo($id, $idordenproduccion)
    {
        $balanceo = Balanceo::findOne($id);
        $balanceo->estado_modulo = 1;
        $balanceo->save(false);
        $orden = Ordenproduccion::findOne($idordenproduccion);
        $orden->cerrar_orden = 1;
        $orden->save(false);
        return $this->redirect(["balanceo/index"]);
        
    }
    
     public function actionExcelconsulta($tableexcel) {                
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
                    ->setCellValue('B1', 'NRO MODULO')
                    ->setCellValue('C1', 'ORDEN PRODUCCION')
                    ->setCellValue('D1', 'OPERARIOS')
                    ->setCellValue('E1', 'FECHA INICIO')
                    ->setCellValue('F1', 'FECHA TERMINACION')
                    ->setCellValue('G1', 'CLIENTE')                    
                    ->setCellValue('H1', 'ESTADO MODULO')
                    ->setCellValue('I1', 'OBSERVACION')
                    ->setCellValue('J1', 'FECHA CREACION')
                    ->setCellValue('K1', 'USUARIO');
                   
        $i = 2  ;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_balanceo)
                    ->setCellValue('B' . $i, $val->modulo)
                    ->setCellValue('C' . $i, $val->idordenproduccion)
                    ->setCellValue('D' . $i, $val->cantidad_empleados)
                    ->setCellValue('E' . $i, $val->fecha_inicio)
                    ->setCellValue('F' . $i, $val->fecha_terminacion)
                    ->setCellValue('G' . $i, $val->cliente->nombrecorto)                    
                    ->setCellValue('H' . $i, $val->estadomodulo)
                    ->setCellValue('I' . $i, $val->observacion)
                    ->setCellValue('J' . $i, $val->fecha_creacion)
                    ->setCellValue('K' . $i, $val->usuariosistema);
                   
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Modulos');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Modulos.xlsx"');
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
    
     public function actionExcelbalanceo($id_balanceo, $idordenproduccion) {
        $orden = Ordenproduccion::findOne($idordenproduccion);         
        $balanceo = balanceo::find()->where(['=','id_balanceo', $id_balanceo])->one(); 
        $detalle = BalanceoDetalle::find()->where(['=','id_balanceo', $balanceo->id_balanceo])->orderBy('id_operario ASC')->all();
        
       // $ordendetalleproceso = Ordenproducciondetalleproceso::find()->where(['=','iddetalleorden',$iddetalleorden])->all();
        $items = count($detalle);
        $totalprenda = 0;
        $totalminutos = 0;  
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
        $objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('3')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('4')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('6')->getFont()->setBold(true);
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
                               
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('D1', 'DETALE DEL MODULO')
                    ->setCellValue('B2', 'NIT:')
                    ->setCellValue('C2', $orden->cliente->cedulanit . '-' . $orden->cliente->dv)
                    ->setCellValue('D2', 'FECHA LLEGADA:')
                    ->setCellValue('E2', $orden->fechallegada)
                    ->setCellValue('B3', 'CLIENTE:')
                    ->setCellValue('C3', $orden->cliente->nombrecorto)
                    ->setCellValue('D3', 'FECHA ENTREGA:')
                    ->setCellValue('E3', $orden->fechaentrega)
                    ->setCellValue('B4', 'COD PRODUCTO:')
                    ->setCellValue('C4', $orden->codigoproducto)
                    ->setCellValue('D4', 'ORDEN PRODUCCION:')
                    ->setCellValue('E4', $orden->idordenproduccion)
                    ->setCellValue('F4', 'TIPO ORDEN:')
                    ->setCellValue('G4', $orden->tipo->tipo)
                    ->setCellValue('A7', 'Id')
                    ->setCellValue('B7', 'Proceso')
                    ->setCellValue('C7', 'Maquina')
                    ->setCellValue('D7', 'Operario)')
                    ->setCellValue('E7', 'S.Operacion')
                    ->setCellValue('F7', 'M_Operacion')
                    ->setCellValue('G7', 'T. Seg.')
                    ->setCellValue('H7', 'T. Mit.')
                    ->setCellValue('I7', 'S/F')
                    ->setCellValue('J7', 'U. X HORA');
        $i = 10;
        
        foreach ($detalle as $val) {
            $totalprenda += ((60/$val->minutos)/60)*$balanceo->cantidad_empleados;                      
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_detalle)
                    ->setCellValue('B' . $i, $val->proceso->proceso)
                    ->setCellValue('C' . $i, $val->tipo->descripcion)
                    ->setCellValue('D' . $i, $val->operario->nombrecompleto)                    
                    ->setCellValue('E' . $i, $val->segundos)                    
                    ->setCellValue('F' . $i, $val->minutos)
                    ->setCellValue('G' . $i, $val->total_segundos)
                    ->setCellValue('H' . $i, $val->total_minutos)
                    ->setCellValue('I' . $i, $val->sobrante_faltante)
                    ->setCellValue('J' . $i, ''. number_format(60 / $val->minutos,2));
            $i++;
        }
        $j = $i + 1;
        $objPHPExcel->getActiveSheet()->getStyle($j)->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('C' . $j, 'Items: '.$items)
                ->setCellValue('D' . $j, 'Total Minutos: '. $balanceo->total_minutos)
                ->setCellValue('E' . $j, 'Un. x Hora: '. ''. number_format($totalprenda),2);
        
        $objPHPExcel->getActiveSheet()->setTitle('Balanceo modular');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Balanceo_modular.xlsx"');
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
