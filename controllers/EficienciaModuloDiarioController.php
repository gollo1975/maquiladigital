<?php

namespace app\controllers;

use Yii;
use app\models\EficienciaModuloDiario;
use app\models\EficienciaModuloDiarioSearch;
use app\models\UsuarioDetalle;
use app\models\FormFiltroEficienciaDiarioModular;
use app\models\Ordenproduccion;
use app\models\Ordenproducciondetalle;
use app\models\Ordenproducciondetalleproceso;
use app\models\CantidadPrendaTerminadas;

//clases
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
 * EficienciaModuloDiarioController implements the CRUD actions for EficienciaModuloDiario model.
 */
class EficienciaModuloDiarioController extends Controller
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
     * Lists all EficienciaModuloDiario models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso', 131])->all()){
            $form = new FormFiltroEficienciaDiarioModular();
            $id_planta = null;
            $fecha_actual = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $id_planta = Html::encode($form->id_planta);
                    $fecha_actual = Html::encode($form->fecha_actual);
                    $table = EficienciaModuloDiario::find()
                            ->andFilterWhere(['=', 'id_planta', $id_planta])
                            ->andFilterWhere(['>=', 'fecha_actual', $fecha_actual]);
                    $table = $table->orderBy('id_eficiencia desc');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 25,
                        'totalCount' => $count->count()
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){
                        //$table = $table->all();
                        $this->actionExcelconsultaEficiencia($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = EficienciaModuloDiario::find()
                        ->orderBy('id_eficiencia desc');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 25,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                    $this->actionExcelconsultaEficiencia($tableexcel);
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

    /**
     * Displays a single EficienciaModuloDiario model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        //vectores
        $modulos = \app\models\EficienciaModuloDetalle::find()->where(['=','id_eficiencia', $id])->orderBy('id_balanceo DESC')->all();
        $EntradaDia = \app\models\EficienciaModuloDiarioDetalle::find()->where(['=','id_eficiencia', $id])->orderBy('id_entrada DESC')->all();
       
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modulos' => $modulos,
            'EntradaDia' => $EntradaDia,
        ]);
    }

    /**
     * Creates a new EficienciaModuloDiario model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EficienciaModuloDiario();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {           
            if ($model->validate()) {
                $table = new EficienciaModuloDiario();
                $table->id_planta = $model->id_planta;
                $table->fecha_actual = $model->fecha_actual;
                $table->usuario_creador = Yii::$app->user->identity->username;
                $table->usuario_editor = Yii::$app->user->identity->username;
                $table->save(false);
                $registro = EficienciaModuloDiario::find()->orderBy('id_eficiencia DESC')->one();
                return $this->redirect(['view', 'id' => $registro->id_eficiencia]);

            } else {
               $model->getErrors();    
            }
        }    
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EficienciaModuloDiario model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $plantas = \app\models\PlantaEmpresa::find()->all();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())){
            if ($model->validate()) {
                $table = $this->findModel($id);
                $table->id_planta = $model->id_planta;
                $table->fecha_actual = $model->fecha_actual;
                $table->save(false);        
                return $this->redirect(['index']);
                
            }
        } 
        if (Yii::$app->request->get("id")) {
            $table = $this->findModel($id);
            $model->id_planta = $table->id_planta;
            $model->fecha_actual = $table->fecha_actual;
        }
       return $this->render('update', [
            'model' => $model,
           'plantas' => ArrayHelper::map($plantas, "id_planta", "nombre_planta"),
        ]);
    }

    /**
     * Deletes an existing EficienciaModuloDiario model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    

    //PROCESO QUE BUSCA LOS MODULOS
    public function actionListar_modulos($id, $id_planta) {
        $listado = \app\models\Balanceo::find()->where(['=','estado_modulo', 0])
                                                ->andWhere(['=','id_proceso_confeccion', 1])
                                                ->andWhere(['=','id_planta', $id_planta])
                                                ->orderBy('id_balanceo DESC')->all();
        $form = new \app\models\FormMaquinaBuscar();
        $q = null;
        $mensaje = '';
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                if ($q){
                    $listado = \app\models\Balanceo::find()
                            ->where(['like','id_balanceo', $q])
                            ->andWhere(['=','estado_modulo', 0])
                            ->andWhere(['=','id_proceso_confeccion', 1])
                            ->andWhere(['=','id_planta', $id_planta])
                            ->orderBy('id_balanceo DESC')->all();
                }               
            } else {
                $form->getErrors();
            }                    

        } else {
             $listado = \app\models\Balanceo::find()->where(['=','estado_modulo', 0])
                                            ->andWhere(['=','id_proceso_confeccion', 1])
                                            ->andWhere(['=','id_planta', $id_planta])
                                            ->orderBy('id_balanceo DESC')->all();
        }
        if (isset($_POST["modulo_activo"])) {
            $intIndice = 0;
            foreach ($_POST["modulo_activo"] as $intCodigo) {
                $eficiencia = EficienciaModuloDiario::findOne($id);
                $balanceo = \app\models\Balanceo::find()->where(['id_balanceo' => $intCodigo])->one();
                $horario = \app\models\Horario::findOne($balanceo->id_horario);
                $detalle = \app\models\EficienciaModuloDetalle::find()
                    ->where(['=', 'id_eficiencia', $id])
                    ->andWhere(['=', 'id_balanceo', $balanceo->id_balanceo])
                    ->all();
                $reg = count($detalle);
                if ($reg == 0) {
                    $table = new \app\models\EficienciaModuloDetalle();
                    $table->id_eficiencia = $id;
                    $table->id_balanceo= $intCodigo;
                    $table->idordenproduccion = $balanceo->idordenproduccion;
                    $table->fecha_carga = $eficiencia->fecha_actual;
                    $table->usuario = Yii::$app->user->identity->username;
                    if($balanceo->fecha_inicio === $table->fecha_carga){
                        $table->hora_inicio_modulo = $balanceo->hora_inicio;
                    }else{
                        $table->hora_inicio_modulo = $horario->desde;
                    }
                    $table->save(false); 
                }
             $intIndice++;   
            }
           $this->redirect(["eficiencia-modulo-diario/view", 'id' => $id, 'id_planta' => $id_planta]);
        }
        return $this->render('_listar_modulos', [
            'listado' => $listado,            
            'mensaje' => $mensaje,
            'id' => $id,
            'form' => $form,
            'id_planta' => $id_planta,

        ]);
    }
    
    //MODAL PARA SUBIR OPERACION
    
    public function actionEficiencia_modulo_diario($id, $orden_produccion, $id_balanceo, $id_carga) {
       $model = new \app\models\EficienciaModuloDiarioDetalle();
       $orden = \app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $orden_produccion])->all();
       $modulo = \app\models\EficienciaModuloDetalle::find()->where(['=','id_eficiencia', $id])->one();       
       $balanceo = \app\models\Balanceo::findOne($id_balanceo);
       if (Yii::$app->request->post()) {
         if ($model->validate()) {
            if (isset($_POST["entrada_diaria"])) {
                $intIndice = 0;
                $registro = 0;
                $id_detalle_talla = 0;
                foreach ($_POST["entrada_diaria"] as $intCodigo):
                    $carga_modulo = \app\models\Ordenproducciondetalle::findOne($intCodigo);
                    $detalle = \app\models\EficienciaModuloDetalle::findOne($id_carga);
                    $cantidad = 0;
                    if($_POST["nueva_entrada"][$intIndice] > 0){
                        $registro += 1;
                        $BuscarHora = \app\models\EficienciaModuloDiarioDetalle::find()->where(['=','id_carga', $id_carga])
                                                                                      ->andWhere(['=','id_balanceo', $id_balanceo])
                                                                                      ->orderBy('id_entrada DESC')->one(); 
                        $model = new \app\models\EficienciaModuloDiarioDetalle();
                        $model->id_eficiencia = $id;
                        $model->id_carga = $id_carga;
                        $model->id_balanceo = $id_balanceo;
                        $model->idordenproduccion = $orden_produccion;
                        $cantidad = $_POST["nueva_entrada"][$intIndice];;
                        $model->unidades_confeccionadas = $_POST["nueva_entrada"][$intIndice];
                        $model->iddetalleorden = $intCodigo;
                        $model->id_proceso_confeccion = $balanceo->id_proceso_confeccion;
                        $model->numero_operarios = $_POST["operarios"][$intIndice];
                        $model->fecha_dia_confeccion = date('Y-m-d');
                        $model->hora_corte = $_POST["hora_corte"][$intIndice];
                        if($BuscarHora){
                            if($registro > 1){
                                if($id_detalle_talla <> $intCodigo){
                                    $model->hora_inicio_dia = $BuscarHora->hora_inicio_dia; 
                                }
                            }else{
                                 $model->hora_inicio_dia = $BuscarHora->hora_corte;
                            }
                        }else{
                           $model->hora_inicio_dia = $detalle->hora_inicio_modulo;
                        }   
                        $model->usuario= Yii::$app->user->identity->username;
                        $model->observacion = $_POST["observacion"][$intIndice];
                        $model->aplica_alimento = $_POST["aplica_alimento"][$intIndice];
                        $model->save(false);
                       ///PROCESO QUE GUARDA EN CANTIDADPRENDASTERMINADAS
                        $table = new \app\models\CantidadPrendaTerminadas();
                        $table->id_balanceo = $id_balanceo;
                        $table->idordenproduccion = $orden_produccion;
                        $table->cantidad_terminada = $_POST["nueva_entrada"][$intIndice];
                        $table->fecha_entrada = date('Y-m-d');
                        $table->nro_operarios = $_POST["operarios"][$intIndice];
                        $table->hora_corte_entrada = $_POST["hora_corte"][$intIndice];
                        $table->usuariosistema = Yii::$app->user->identity->username;
                        $table->observacion = $_POST["observacion"][$intIndice];
                        $table->iddetalleorden = $intCodigo;
                        $table->id_proceso_confeccion = $balanceo->id_proceso_confeccion;
                        $table->insert();
                        $this->GuardarCantidadPrendasTerminadas($intCodigo, $cantidad, $orden_produccion);
                        $this->ActualizaPorcentajeCantidad($intCodigo, $orden_produccion);
                        $this->CalculaEficienciaHora($id_carga, $id_balanceo);
                        $this->TotalizarEficiencia($id_carga, $id_balanceo,$id);
                       
                    }
                    $intIndice++;
                endforeach;
                $this->redirect(["eficiencia-modulo-diario/view", 'id' => $id]);
            }
         }else{
             $model->getErrors();
         }    
       }
       return $this->renderAjax('_formeficienciamodulodiario', [
            'orden' => $orden,
            'modulo' => $modulo,
            'balanceo' => $balanceo,
        ]);
    }
    //totalizar eficiencia
    protected function TotalizarEficiencia($id_carga, $id_balanceo, $id) {
        
        $eficiencia = EficienciaModuloDiario::findOne($id);
        $conLinea = \app\models\EficienciaModuloDetalle::findOne($id_carga);
        $cargar = \app\models\EficienciaModuloDiarioDetalle::find()->where(['=','id_carga', $id_carga])
                                                                                      ->andWhere(['=','id_balanceo', $id_balanceo])
                                                                                      ->orderBy('id_entrada DESC')->all(); 
        $suma = 0; $con = 0; $total = 0; $unidades = 0;
        $hora_corte = ''; $resta = 0; $total_registro = 0;
        foreach ($cargar as $carga):
            if($hora_corte <> $carga->hora_corte){
                $hora_corte = $carga->hora_corte;
            }else{
                $resta += 1;                
            }
            $suma += $carga->porcentaje_hora_corte;
            $unidades += $carga->unidades_confeccionadas;
            $con +=1; 
        endforeach;
        $total_registro = $con - $resta;
        $total = round($suma / $total_registro);
        $conLinea->total_eficiencia_diario = $total;
        $conLinea->total_unidades = $unidades;
        $conLinea->save(false);
        //codigo para totalizzar la eficiencia
        $buscarEficiencia = \app\models\EficienciaModuloDetalle::find()->where(['=','id_eficiencia', $id])->all();
        $totalE = 0; $porcentaje = 0; $contE = 0; $total_unidades = 0;
        foreach ($buscarEficiencia as $buscar):
            $totalE += $buscar->total_eficiencia_diario;
            $contE += 1;
            $total_unidades += $buscar->total_unidades;
        endforeach;
        $porcentaje = round($totalE / $contE);
        $eficiencia->total_eficiencia_planta = $porcentaje;
        $eficiencia->total_unidades = $total_unidades;
        $eficiencia->save(false);
    }
    //CALCULA LA EFICIENCIA
    protected function CalculaEficienciaHora($id_carga, $id_balanceo) {
        
        $balanceo = \app\models\Balanceo::findOne($id_balanceo);      
        $horario = \app\models\Horario::findOne($balanceo->id_horario);
        $Buscar =  \app\models\EficienciaModuloDiarioDetalle::find()->where(['=','id_carga', $id_carga])
                                                                                      ->andWhere(['=','id_balanceo', $id_balanceo])
                                                                                      ->orderBy('id_entrada DESC')->one(); 
        $sumarh = 0; 
        $totalTiempo = 0;
        $sumarm = 0;
        $total_unidades = 0; $unidades = 0;
        $eficiencia = 0;
        $unidades_reales =0;
        //tiempo
        $horad = explode(":", $Buscar->hora_inicio_dia);
        $horah = explode(":", $Buscar->hora_corte); 
        $sumarh = $horah[0] - $horad[0];
        $sumarm = $horah[1] + $horad[1];
        if($Buscar->aplica_alimento == 0){
            var_dump($totalTiempo = ($sumarh * 60) + $sumarm);
        }else{
            if($horario->abreviatura == 'LV'){
                if ($Buscar->hora_corte > '12:00'){
                    $totalTiempo = ($sumarh * 60) - $sumarm - $horario->tiempo_almuerzo; 
                }else{
                    $totalTiempo = ($sumarh * 60) - $sumarm - $horario->tiempo_desayuno; 
                }
            }else{
                var_dump($totalTiempo = ($sumarh * 60) - $sumarm - $horario->tiempo->desayuno); 
            }
        }   
        //unidades po hora
        $unidades = ((60 / $balanceo->tiempo_balanceo) * $Buscar->numero_operarios);
        $total_unidades = ($unidades * $totalTiempo)/60;
        $Buscar->real_confeccion = $total_unidades;
        $eficiencia = round(($Buscar->unidades_confeccionadas / $total_unidades)* 100);
        $Buscar->porcentaje_hora_corte = $eficiencia;
        $Buscar->save(false);
        
    }
    ///actualiza las porcentajes
    
      protected function ActualizaPorcentajeCantidad($intCodigo, $orden_produccion) {
        //actualiza detale
        $canti_operada = 0; $cantidad = 0; $porcentaje = 0;
        $orden_detalle_actualizada = \app\models\Ordenproducciondetalle::find()->where(['=','iddetalleorden', $intCodigo])->one();
        $canti_operada = $orden_detalle_actualizada->faltante;
        $cantidad = $orden_detalle_actualizada->cantidad;
        $porcentaje = number_format(($canti_operada * 100)/ $cantidad,4);
        $orden_detalle_actualizada->porcentaje_cantidad = $porcentaje;
        $orden_detalle_actualizada->cantidad_operada = $canti_operada;
        $orden_detalle_actualizada->save(false);
        //actualiza orden produccion
        $orden = \app\models\Ordenproduccion::findOne($orden_produccion);
        $sumadetalle = \app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $orden_produccion])->all();
        $contador = 0;
        $total_porcentaje = 0;
        foreach ($sumadetalle as $sumar):
            $contador += $sumar->cantidad_operada;
        endforeach;
        $total_porcentaje = number_format(($contador * 100)/ $orden->cantidad,4);
        $orden->porcentaje_cantidad = $total_porcentaje;
        $orden->save(false);
    }
    
    //PROCESO QUE GUARDA EN LA ENTIDAD_PRENDAS_TERMINADAS
    
    protected function GuardarCantidadPrendasTerminadas($intCodigo, $cantidad, $orden_produccion) {
        $detalle_proceso = \app\models\Ordenproducciondetalleproceso::find()->where(['=','iddetalleorden', $intCodigo])->all();
        $contar = 0;
        foreach ($detalle_proceso as $entrarcantidad):
           $contar = $entrarcantidad->cantidad_operada; 
           $entrarcantidad->cantidad_operada = $cantidad + $contar;
           $entrarcantidad->save(false);
           $contar = 0;
        endforeach;
        $orden = \app\models\Ordenproduccion::findOne($orden_produccion);
        $unidades = 0;
        $orden_detalle = \app\models\Ordenproducciondetalle::find()->where(['=','iddetalleorden', $intCodigo])->one();
        $cantidad = \app\models\CantidadPrendaTerminadas::find()->where(['=','iddetalleorden', $intCodigo])->all();
        $ordenunidad = \app\models\CantidadPrendaTerminadas::find()->where(['=','idordenproduccion', $orden_produccion])->all();
        $suma = 0;
        $cantidad_real = 0;
        foreach ($cantidad as $detalle):
            $suma +=$detalle->cantidad_terminada; 
        endforeach;
        $orden_detalle->faltante = $suma;
        $orden_detalle->save(false);
        foreach ($ordenunidad as $cant):
            $unidades += $cant->cantidad_terminada; 
        endforeach;
        $cantidad_real= $orden->cantidad;
        $orden->faltante = $cantidad_real - $unidades;
        $orden->save(false);
    }
    
    ///eliminar registro
     public function actionEliminar($id, $id_carga, $id_planta) {
        if (Yii::$app->request->post()) {
            $detalle = \app\models\EficienciaModuloDetalle::findOne($id_carga);
            if ((int) $id_carga) {
                try {
                    \app\models\EficienciaModuloDetalle::deleteAll("id_carga=:id_carga", [":id_carga" => $id_carga]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    $this->redirect(["eficiencia-modulo-diario/view",'id' => $id, 'id_planta' => $id_planta]);
                } catch (IntegrityException $e) {
                    $this->redirect(["eficiencia-modulo-diario/view",'id' => $id, 'id_planta' => $id_planta]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el modulo Nro: ' . $detalle->id_balanceo . ', tiene registros asociados en otros procesos');
                } catch (\Exception $e) {

                    $this->redirect(["eficiencia-modulo-diario/view",'id' => $id, 'id_planta' => $id_planta]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el modulo Nro:  ' . $detalle->id_balanceo . ', tiene registros asociados en otros procesos');
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el registros, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("eficiencia-modulo-diario/index") . "'>";
            }
        } else {
            return $this->redirect(["eficiencia-modulo-diario/view",'id' => $id, 'id_planta' => $id_planta]);
        }
    }
    
    //cerrar el proceso
    
    public function actionCerrar_proceso_eficiencia($id) {
        $model = EficienciaModuloDiario::findOne($id);
        $model->proceso_cerrado = 1;
        $model->save(false);
        return $this->redirect(["eficiencia-modulo-diario/view",'id' => $id]);      
    }
    
    //MODIFICAR FECHA
    public function actionModificarhorainicio($id, $id_planta, $id_detalle) {
        $detalle = \app\models\EficienciaModuloDetalle::findOne($id_detalle);
        $detalle->hora_inicio_modulo = $_POST['hora_inicio'];
        $detalle->save(false);
        $this->redirect(["eficiencia-modulo-diario/view",'id' => $id, 'id_planta' => $id_planta]);
        
    }
    /**
     * Finds the EficienciaModuloDiario model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EficienciaModuloDiario the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EficienciaModuloDiario::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    //ARCHIVOS DE EXCEL
    
    public function actionExcelconsultaEficiencia($tableexcel) {                
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
                                     
        $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'PLANTA')
                    ->setCellValue('C1', 'FECHA DIA')
                    ->setCellValue('D1', 'FECHA PROCESO')
                    ->setCellValue('E1', 'TOTAL UNIDADES')
                    ->setCellValue('F1', 'TOTAL EFICIENCIA')
                    ->setCellValue('G1', 'USUARIO CREADOR')
                    ->setCellValue('H1', 'USUARIO EDITOR')                    
                    ->setCellValue('I1', 'PROCESO CERRADO');
                   
        $i = 2  ;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_eficiencia)
                    ->setCellValue('B' . $i, $val->planta->nombre_planta)
                    ->setCellValue('C' . $i, $val->fecha_actual)
                    ->setCellValue('D' . $i, $val->fecha_proceso)
                    ->setCellValue('E' . $i, $val->total_unidades)
                    ->setCellValue('F' . $i, $val->total_eficiencia_planta)
                    ->setCellValue('G' . $i, $val->usuario_creador)
                    ->setCellValue('H' . $i, $val->usuario_editor)                    
                    ->setCellValue('I' . $i, $val->procesoCerrado);
                    
                   
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Eficiencia');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Eficiencia_diaria.xlsx"');
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
    
    //ARCHIVO DE EXCEL QUE DESCARGA MODULOS
    
    public function actionExportar_modulos($id) {
        $eficiencia = \app\models\EficienciaModuloDetalle::find()->where(['=','id_eficiencia', $id])->all();         
      
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
                               
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'No BALANCEO')
                    ->setCellValue('C1', 'OP')
                    ->setCellValue('D1', 'MODULO:')
                    ->setCellValue('E1', 'CLIENTE')
                    ->setCellValue('F1', 'TOTAL EFICIENCIA:')
                    ->setCellValue('G1', 'FECHA ENTRADA')
                    ->setCellValue('H1', 'HORA INICIO MODULO:')
                    ->setCellValue('I1', 'TOTAL UNIDADES')
                    ->setCellValue('J1', 'USUARIO')
                    ->setCellValue('K1', 'PLANTA');
                    
        $i = 2;
        
        foreach ($eficiencia as $val) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_carga)
                    ->setCellValue('B' . $i, $val->id_balanceo)
                    ->setCellValue('C' . $i, $val->idordenproduccion)
                    ->setCellValue('D' . $i, $val->balanceo->modulo)                    
                    ->setCellValue('E' . $i, $val->ordenproduccion->cliente->nombrecorto)                    
                    ->setCellValue('F' . $i, $val->total_eficiencia_diario)
                    ->setCellValue('G' . $i, $val->fecha_carga)
                    ->setCellValue('H' . $i, $val->hora_inicio_modulo)
                    ->setCellValue('I' . $i, $val->total_unidades)
                    ->setCellValue('J' . $i, $val->usuario)
                    ->setCellValue('K' . $i, $val->eficiencia->planta->nombre_planta);
            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('listado_modulos');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Listado_Modulos.xlsx"');
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
    
    //ARCHIVO DE EXCEL QUE DESCARGA TODAS LAS ENTRADAS
    
     public function actionExportar_entradas($id) {
        $eficiencia = \app\models\EficienciaModuloDiarioDetalle::find()->where(['=','id_eficiencia', $id])->all();     
     //   $orden_detalle = Ordenproducciondetalle::find()->where([])->one();
      
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
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'No BALANCEO')
                    ->setCellValue('C1', 'OP')
                    ->setCellValue('D1', 'MODULO')
                    ->setCellValue('E1', 'CLIENTE')
                    ->setCellValue('F1', 'UNIDADES CONFECCIONADAS')
                    ->setCellValue('G1', 'UNIDADES SISTEMA')
                    ->setCellValue('H1', 'TALLA')
                    ->setCellValue('I1', 'TIPO PROCESO')
                    ->setCellValue('J1', 'No OPERARIOS')
                    ->setCellValue('K1', 'APLICA ALIMENTO')
                    ->setCellValue('L1', 'FECHA CONFECCION')
                    ->setCellValue('M1', 'FECHA PROCESO')
                    ->setCellValue('N1', 'EFICIENCIA')
                    ->setCellValue('O1', 'HORA CORTE')
                    ->setCellValue('P1', 'HORA INICIO')
                    ->setCellValue('Q1', 'USUARIO')
                    ->setCellValue('R1', 'OBSERVACION');

                    
        $i = 2;
        
        foreach ($eficiencia as $val) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_entrada)
                    ->setCellValue('B' . $i, $val->id_balanceo)
                    ->setCellValue('C' . $i, $val->idordenproduccion)
                    ->setCellValue('D' . $i, $val->balanceo->modulo)                    
                    ->setCellValue('E' . $i, $val->ordenproduccion->cliente->nombrecorto)                    
                    ->setCellValue('F' . $i, $val->unidades_confeccionadas)
                    ->setCellValue('G' . $i, $val->real_confeccion)
                    ->setCellValue('H' . $i, $val->detalleorden->productodetalle->prendatipo->prenda.' / '.$val->detalleorden->productodetalle->prendatipo->talla->talla)
                    ->setCellValue('I' . $i, $val->procesoConfeccion->descripcion_proceso)
                    ->setCellValue('J' . $i, $val->numero_operarios)
                    ->setCellValue('K' . $i, $val->aplicaAlimento)
                    ->setCellValue('L' . $i, $val->fecha_dia_confeccion)
                    ->setCellValue('M' . $i, $val->fecha_registro)
                    ->setCellValue('N' . $i, $val->porcentaje_hora_corte)
                    ->setCellValue('O' . $i, $val->hora_corte)
                    ->setCellValue('P' . $i, $val->hora_inicio_dia)
                    ->setCellValue('Q' . $i, $val->usuario)
                    ->setCellValue('R' . $i, $val->observacion);
            
            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('Entradaproduccion');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Entrada_Produccion.xlsx"');
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
