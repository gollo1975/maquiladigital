<?php

namespace app\controllers;

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
//
use app\models\SalidaBodega;
use app\models\SalidaBodegaDetalle;
use app\models\UsuarioDetalle;
use app\models\CostoProducto;
use app\models\Insumos;
/**
 * SalidaBodegaController implements the CRUD actions for SalidaBodega model.
 */
class SalidaBodegaController extends Controller
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
     * Lists all SalidaBodega models.
     * @return mixed
     */
     public function actionIndex($token = 0) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',137])->all()){
                $form = new \app\models\FormFiltroSalidaBodega();
                $codigo_producto = null;
                $orden_fabricacion = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $cliente = null;
                $numero = null;
                $conCliente = \app\models\Cliente::find()->orderBy('nombrecorto DESC')->all();
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $orden_fabricacion = Html::encode($form->orden_fabricacion);
                        $codigo_producto = Html::encode($form->codigo_producto);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $cliente = Html::encode($form->cliente);
                        $numero = Html::encode($form->numero);
                        $table = SalidaBodega::find()
                                ->andFilterWhere(['=', 'codigo_producto', $codigo_producto])
                                ->andFilterWhere(['=', 'id_orden_fabricacion', $orden_fabricacion])
                                ->andFilterWhere(['=', 'idcliente', $cliente])
                                ->andFilterWhere(['=', 'numero_salida', $numero])
                                ->andFilterWhere(['>=', 'fecha_salida', $fecha_inicio]) 
                                ->andFilterWhere(['<=', 'fecha_salida', $fecha_corte]);
                       $table = $table->orderBy('id_salida_bodega DESC');
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
                                $check = isset($_REQUEST['id_salida_bodega DESC']);
                                $this->actionExcelconsultaSalida($tableexcel);
                            }
                } else {
                        $form->getErrors();
                }                    
            } else {
                $table = SalidaBodega::find()
                        ->orderBy('id_salida_bodega DESC');
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
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                     $this->actionExcelconsultaSalida($tableexcel);
                }
            }
            $to = $count->count();
            return $this->render('index', [
                        'model' => $model,
                        'form' => $form,
                        'pagination' => $pages,
                        'token' => $token,
                        'conCliente' => ArrayHelper::map($conCliente, 'idcliente', 'nombrecorto'),
            ]);
        }else{
             return $this->redirect(['site/sinpermiso']);
        }     
        }else{
           return $this->redirect(['site/login']);
        }
   }
   
    //CONSULTA DE DETALLES DEL INSUMO
    public function actionSearch_detalle_insumos($token = 1) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',138])->all()){
                $form = new \app\models\FormFiltroSalidaBodega();
                $codigo_producto = null;
                $orden_fabricacion = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $cliente = null;
                $conCliente = \app\models\Cliente::find()->orderBy('nombrecorto DESC')->all();
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $orden_fabricacion = Html::encode($form->orden_fabricacion);
                        $codigo_producto = Html::encode($form->codigo_producto);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $cliente = Html::encode($form->cliente);
                        $table = SalidaBodega::find()
                                ->andFilterWhere(['=', 'codigo_producto', $codigo_producto])
                                ->andFilterWhere(['=', 'id_orden_fabricacion', $orden_fabricacion])
                                ->andFilterWhere(['>=', 'fecha_salida', $fecha_inicio]) 
                                ->andFilterWhere(['<=', 'fecha_salida', $fecha_corte])
                                ->andFilterWhere(['=', 'idcliente', $cliente])
                                ->andWhere(['=', 'proceso_cerrado', 1]);
                       $table = $table->orderBy('id_salida_bodega DESC');
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
                                $check = isset($_REQUEST['id_salida_bodega DESC']);
                                $this->actionExcelconsultaSalida($tableexcel);
                            }
                } else {
                        $form->getErrors();
                }                    
            } else {
                $table = SalidaBodega::find()->Where(['=', 'proceso_cerrado', 1])
                        ->orderBy('id_salida_bodega DESC');
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
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                     $this->actionExcelconsultaSalida($tableexcel);
                }
            }
            $to = $count->count();
            return $this->render('search_salidas', [
                        'model' => $model,
                        'form' => $form,
                        'pagination' => $pages,
                        'token' => $token,
                        'conCliente' => ArrayHelper::map($conCliente, 'idcliente', 'nombrecorto'),
            ]);
        }else{
             return $this->redirect(['site/sinpermiso']);
        }     
        }else{
           return $this->redirect(['site/login']);
        }
   }

    /**
     * Displays a single SalidaBodega model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token)
    {
        $listado_insumos = \app\models\SalidaBodegaDetalle::find()->where(['=','id_salida_bodega', $id])->all();
        $listado_operaciones = \app\models\SalidaBodegaOperaciones::find()->where(['=','id_salida_bodega', $id])->all();
        //PROCESO QUE ELIMINA LOS REGISTROS DE INSUMOS
        if (Yii::$app->request->post()) {
          if (isset($_POST["eliminar_todo"])) {
              if (isset($_POST["listado_eliminar"])) {
                  foreach ($_POST["listado_eliminar"] as $intCodigo) {
                      try {
                          $eliminar = \app\models\SalidaBodegaDetalle::findOne($intCodigo);
                          $eliminar->delete();
                          Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                           $this->Actualizar_unidades($id);
                          $this->redirect(["salida-bodega/view", 'id' => $id, 'token' => $token]);
                      } catch (IntegrityException $e) {

                          Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                      } catch (\Exception $e) {
                          Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');

                      }
                  }
              } else {
                  Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro.');
              }    
           }
        }    
        
        //PROCESO QUE ELIMINA LOS REGISTROS DE OPERACIONES
        if (Yii::$app->request->post()) {
          if (isset($_POST["eliminar_operacion"])) {
              if (isset($_POST["listado_operaciones"])) {
                  foreach ($_POST["listado_operaciones"] as $intCodigo) {
                      try {
                          $eliminar = \app\models\SalidaBodegaOperaciones::findOne($intCodigo);
                          $eliminar->delete();
                          Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                          $this->ContadorConfeccionTerminacion($id);
                          $this->redirect(["salida-bodega/view", 'id' => $id, 'token' => $token]);
                      } catch (IntegrityException $e) {

                          Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                      } catch (\Exception $e) {
                          Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');

                      }
                  }
              } else {
                  Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro.');
              }    
           }
        }    
        
        //PROCESO QUE ACTUALIZAR EL INVENTARIO
        if(isset($_POST["actualizar_inventario"])){
           if(isset($_POST["materia_prima"])){
                $intIndice = 0;
                $cantidad = 0; $subtotal = 0; $iva = 0; $total = 0;
                foreach ($_POST["materia_prima"] as $intCodigo):
                    //buscamos la existencia del insumo
                    $buscar = \app\models\SalidaBodegaDetalle::findOne($intCodigo);
                    $insumo = \app\models\Insumos::findOne($buscar->id_insumo); 
                    //validamos la existencia;
                    $cantidad = $_POST["cantidad_despachar"]["$intIndice"];
                    $subtotal = round($insumo->precio_unitario * $cantidad);
                    if($insumo->aplica_iva == 0){
                        $iva = 0;
                    }else{
                        $iva = round(($subtotal * $insumo->porcentaje_iva)/100);
                    }
                    $total = $subtotal + $iva;
                    if($insumo->aplica_inventario == 1){
                        if($cantidad <= $insumo->stock_real){
                            $buscar->cantidad_despachar = $_POST["cantidad_despachar"]["$intIndice"];
                            $buscar->nota = $_POST["observacion"]["$intIndice"];
                            $buscar->subtotal = $subtotal;
                            $buscar->iva = $iva;
                            $buscar->total_linea = $total;
                            $buscar->save (false);
                            $intIndice++;
                        }else{
                            Yii::$app->getSession()->setFlash('warning', 'No hay existencia del insumo ('.$insumo->descripcion.')');
                            $intIndice++;  
                        }
                    }else{
                        $buscar->cantidad_despachar = $_POST["cantidad_despachar"]["$intIndice"];
                        $buscar->nota = $_POST["observacion"]["$intIndice"];
                        $buscar->iva = $iva;
                        $buscar->total_linea = $total;
                        $buscar->save (false);
                        $buscar->save (false);
                        $intIndice++;
                    }    
               endforeach;
               $this->Actualizar_unidades($id);
              return $this->redirect(['view','id' =>$id, 'token' => $token]);
           }

        }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'token' => $token,
            'listado_insumos' => $listado_insumos,
            'listado_operaciones' => $listado_operaciones,
        ]);
    }
    //PROCESO QUE ACTUALIZAR LAS UNIDADES
    protected function Actualizar_unidades($id) {
        $model = $this->findModel($id);
        $modelo = SalidaBodegaDetalle::find()->where(['=','id_salida_bodega', $id])->all();
        $contar = 0;
        foreach ($modelo as $val):
            $contar += $val->cantidad_despachar;
        endforeach;
        $model->unidades = $contar;
        $model->save();
    }

    /**
     * Creates a new SalidaBodega model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SalidaBodega();
        $Consulta = \app\models\OrdenFabricacion::find()->where(['=','salida_insumo', 0])->orderBy('id_orden_fabricacion ASC')->all();
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                $salida = SalidaBodega::find()->all();
                $sw = 0;
                foreach ($salida as $bodega){

                    if($model->id_orden_fabricacion == $bodega->id_orden_fabricacion){
                        $sw = 1;
                    } 
                }
                if($sw == 0){
                    $model->save();
                    $producto = \app\models\OrdenFabricacion::findOne($model->id_orden_fabricacion);
                    $model->id_orden_fabricacion = $model->id_orden_fabricacion;
                    $model->codigo_producto = $producto->codigo_producto;
                    $model->idcliente = $producto->idcliente;
                    $model->unidades_vendidas = $producto->cantidades;
                    $model->user_name = Yii::$app->user->identity->username;
                    $model->save(false);
                    return $this->redirect(['view', 'id' => $model->id_salida_bodega,'token' => 0]);
                }else{
                    Yii::$app->getSession()->setFlash('error', 'La orden de fabricacion seleccionada se encuentra en un proceso de salida de insumos o ya esta despachada. Valide la informacion.');
                }    
            }else{
                $model->getErrors();
            }    
        }

        return $this->render('create', [
            'model' => $model,
            'Consulta' => ArrayHelper::map($Consulta, 'id_orden_fabricacion', 'ordenFabricacion'),
        ]);
    }
    
     /**
     * Updates an existing SalidaBodega model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
         $model = $this->findModel($id);
         $Consulta = \app\models\OrdenFabricacion::find()->where(['=','salida_insumo', 0])->orderBy('id_orden_fabricacion ASC')->all();
        if ($model->load(Yii::$app->request->post())) {
            $producto = \app\models\OrdenFabricacion::findOne($model->id_orden_fabricacion);
            $model->id_orden_fabricacion = $model->id_orden_fabricacion;
            $model->codigo_producto = $producto->codigo_producto;
            $model->idcliente = $producto->idcliente;
            $model->responsable = $model->responsable;
            $model->unidades_vendidas = $producto->cantidades;
            $model->save(false);
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'Consulta' => ArrayHelper::map($Consulta, 'id_orden_fabricacion', 'ordenFabricacion'),
        ]);
    }
    
   //PERMITE CARGAR LOS INSUMOS DE LA ORDEN DE COSTO
     public function actionCargar_nuevo_insumo($id, $token)
    {
        $insumos = \app\models\Insumos::find()->where(['>','stock_real', 0])->orderBy('descripcion asc')->all();
        $form = new \app\models\FormMaquinaBuscar();
        $q = null;
        $grupo = null;
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);  
               $grupo = Html::encode($form->grupo);
                $insumos = \app\models\Insumos::find()
                        ->andFilterWhere(['=','id_grupo', $grupo])
                        ->andFilterWhere(['=','codigo_insumo', $q])
                        ->andwhere(['>','stock_real', 0]);
                $insumos = $insumos->orderBy('descripcion DESC');                    
                $count = clone $insumos;
                $to = $count->count();
                $pages = new Pagination([
                    'pageSize' => 15,
                    'totalCount' => $count->count()
                ]);
                $insumos = $insumos
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();   
            } else {
                $form->getErrors();
            }                    
                    
        } else {
            $table = Insumos::find()->where(['>','stock_real', 0])->orderBy('descripcion asc');
            $tableexcel = $table->all();
            $count = clone $table;
            $pages = new Pagination([
                        'pageSize' => 15,
                        'totalCount' => $count->count(),
            ]);
             $insumos = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
        }
        if (isset($_POST["id_insumos"])) {
                $intIndice = 0;
                foreach ($_POST["id_insumos"] as $intCodigo) {
                    $insumo = Insumos::find()->where(['id_insumos' => $intCodigo])->one();
                    $detalles = SalidaBodegaDetalle::find()
                        ->where(['=', 'id_salida_bodega', $id])
                        ->andWhere(['=', 'id_insumo', $insumo->id_insumos])
                        ->all();
                    $reg = count($detalles);
                    if ($reg == 0) {
                        $bodega = SalidaBodega::findOne($id);
                        $table = new SalidaBodegaDetalle();
                        $table->id_salida_bodega = $id;
                        $table->id_insumo = $intCodigo;
                        $table->codigo_insumo = $insumo->codigo_insumo;
                        $table->nombre_insumo = $insumo->descripcion;
                        $table->cantidad_despachar = $bodega->unidades_vendidas;
                        $table->valor_unitario = $insumo->precio_unitario;
                        $table->insert(); 
                    }
                }
                $this->Actualizar_unidades($id);
                return $this->redirect(["salida-bodega/view", 'id' => $id, 'token' => $token]);
            }else{
                
            }
        return $this->render('_listado_insumos', [
            'insumos' => $insumos,            
            'id' => $id,
            'form' => $form,
            'token' => $token,
            'pagination' => $pages,

        ]);
    }
   
    //CREAR OPERACIONES AL PRODUCTO
     public function actionCargar_operaciones($id, $token)
    {
        $operacion = \app\models\ProcesoProduccion::find()->where(['=','estado', 0])->orderBy('proceso asc')->all();
        $form = new \app\models\FormMaquinaBuscar();
        $q = null;
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                    $operacion = \app\models\ProcesoProduccion::find()
                            ->where(['like','proceso',$q])
                            ->orwhere(['=','idproceso',$q]);
                    $operacion = $operacion->orderBy('proceso asc');                    
                    $count = clone $operacion;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 20,
                        'totalCount' => $count->count()
                    ]);
                    $operacion = $operacion
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();         
                           
            } else {
                $form->getErrors();
            }                    
        }else{
            $operacion = \app\models\ProcesoProduccion::find()->where(['=','estado', 0])->orderBy('proceso asc');
            $count = clone $operacion;
            $pages = new Pagination([
                'pageSize' => 20,
                'totalCount' => $count->count(),
            ]);
            $operacion = $operacion
                    ->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();
        }
         if (isset($_POST["guardar_operacion"])) {
            if(isset($_POST["idproceso"])){
                $intIndice = 0;
                foreach ($_POST["idproceso"] as $intCodigo) {
                   $listado = \app\models\SalidaBodegaOperaciones::find()
                            ->where(['=', 'idproceso', $intCodigo])
                            ->andWhere(['=', 'id_salida_bodega', $id])
                            ->all();
                    $reg = count($listado);
                    if ($reg == 0) {
                        $segundo = 0;
                        $minutos = 0;
                        if($_POST["id_tipo"][$intIndice] > 0){
                            $table = new \app\models\SalidaBodegaOperaciones();
                            $table->idproceso = $intCodigo;
                            $table->id_salida_bodega = $id;
                            $segundo = $_POST["segundos"][$intIndice];
                            $minutos = $_POST["minutos"][$intIndice];
                            if($segundo == 0){
                               $table->minutos = $minutos;
                               $table->segundos = $table->minutos * 60;
                            }else{
                                $table->segundos = $segundo;
                                $table->minutos = number_format($segundo / 60, 2);
                            }   
                            $table->idtipo = 1;
                            $table->fecha_creacion = date('Y-m-d');
                            $table->user_name = Yii::$app->user->identity->username;
                            $table->id_tipo = $_POST["id_tipo"][$intIndice];
                            $table->save(false);
                        }   
                    }
                    $intIndice++;
                }
                $this->ContadorConfeccionTerminacion($id);
            }
        }
        return $this->render('crear_operaciones_salida', [
            'operacion' => $operacion,            
            'pagination' => $pages,
            'id' => $id,
            'form' => $form,
            'token' => $token,

        ]);
    }
    
     //EDITAR LAS OPERACIONES PARA CAMBIAR SI EN CONFECCION O TERMINACION
     public function actionEditar_operaciones($id, $token) {
        $mds = \app\models\SalidaBodegaOperaciones::find()->where(['=', 'id_salida_bodega', $id])->orderBy('idtipo, idproceso desc ')->all();
        $error = 0;
      
        if (isset($_POST["id_operacion"])) {
            $intIndice = 0;
            $aux= 0; $nuevo_minuto = 0;
            foreach ($_POST["id_operacion"] as $intCodigo) {
                $table = \app\models\SalidaBodegaOperaciones::findOne($intCodigo);
                $table->segundos = $_POST["segundos"][$intIndice];
                $aux = $table->segundos;
                $nuevo_minuto = number_format($aux/60,2);
                $table->minutos = $nuevo_minuto;
                $table->idtipo = $_POST["proceso"][$intIndice];
                $table->id_tipo = $_POST["id_tipo"][$intIndice];
                $table->save(false);                    
                $intIndice++;
            }
            $this->ContadorConfeccionTerminacion($id);
            $mds = \app\models\SalidaBodegaOperaciones::find()->where(['=', 'id_salida_bodega', $id])->orderBy('idtipo, idproceso desc ')->all();
           $this->redirect(["salida-bodega/view", 'id' => $id,'mds' =>$mds, 'token' => $token]);            
        }
        return $this->render('_formeditar_operaciones', [
                    'mds' => $mds,
                    'id' => $id,
                    'token' => $token,
        ]);
    }
    
     //PROCESO QUE CUENTA el TIEMPO DE CONFECCION Y TERMINACION
    
    protected function ContadorConfeccionTerminacion($id) {
        $producto = SalidaBodega::findOne($id);
        $operacion = \app\models\SalidaBodegaOperaciones::find()->where(['=','id_salida_bodega', $id])->all();
        $confeccion = 0; $terminacion = 0; 
        $config = \app\models\Matriculaempresa::findOne(1);
        foreach ($operacion as $operaciones):
            if($operaciones->idtipo == 1){
                $confeccion += $operaciones->minutos;
            }else{
                $terminacion += $operaciones->minutos;
            }
            $producto->tiempo_confeccion = $confeccion;
            $producto->tiempo_terminacion = $terminacion;
            $producto->costo_confeccion = $confeccion * $config->valor_minuto_confeccion;
            $producto->costo_terminacion = $terminacion * $config->valor_minuto_terminacion;
            $producto->save(false);
        endforeach;
    }
    
    //AUTORIZAR EL PROCESO
    public function actionAutorizado($id, $token) {
        $model = $this->findModel($id);
        if($model->unidades > 0){
            if($model->autorizado == 0){
                 $model->autorizado = 1;
                 $model->save();
            }else{
                 $model->autorizado = 0;
                 $model->save();
            }
        }else{
            Yii::$app->getSession()->setFlash('error', 'Debe de ingresar las cantidades a despachar para el proceso de confeccion.');
            return $this->redirect(['view', 'id' => $id,'token' => $token]);
        }    
        return $this->redirect(['view', 'id' => $id,'token' => $token]);
    }
    
    //CERRA EL PROCESO DE SALIDA Y CREA CONSECUTIVO
    public function actionCerrar_despacho($id, $token) {
        $model = $this->findModel($id);
        $producto = \app\models\OrdenFabricacion::findOne($model->id_orden_fabricacion);
        $numero = \app\models\Consecutivo::findOne(16);
        $consecutivo = $numero->consecutivo + 1;
        //actualiza el model
        $model->numero_salida = $consecutivo;
        $model->proceso_cerrado = 1;
        $model->save();
        //actualiza el consecutivo
        $numero->consecutivo = $consecutivo;
        $numero->save();
        //cierrar la referencia
        $producto->salida_insumo = 1;
        $producto->save(false);
        $this->CalcularCostoInsumo($id);
       return $this->redirect(['view', 'id' => $id,'token' => $token]);
    }
    
    //PROCESO QUE CANCULA EL COSTO DEL INSUMOS
    protected function CalcularCostoInsumo($id) {
        $model = SalidaBodega::findOne($id);
        $salidas = SalidaBodegaDetalle::find()->where(['=','id_salida_bodega', $id])->all();
        $total = 0;
        $referencia = $model->orden->codigo_producto;
        foreach ($salidas as $salida):
                $total += $salida->total_linea;
        endforeach;
        $model->costo_insumos = $total ;
        $model->costo_total = $model->costo_insumos + $model->costo_terminacion + $model->costo_confeccion + $model->otros_costos;
        $model->save();
    }

    //ENVIA EL INVENTARIO PARA SER DESCARGADO DEL  MODULO DE INSUMOS
    public function actionEnviar_inventario($id, $token) {
        $model = $this->findModel($id);
        $detalle = SalidaBodegaDetalle::find()->where(['=','id_salida_bodega', $id])->all();
        $contar = 0;
        foreach ($detalle as $val):
            $inventario = \app\models\Insumos::findOne($val->id_insumo);
            if($inventario){
                if($inventario->aplica_inventario == 1){
                   $contar += 1;
                   $inventario->stock_real -= $val->cantidad_despachar;
                   $inventario->save();
                }
            }
        endforeach;
        $model->exportar_inventario = 1;
        $model->save();
        Yii::$app->getSession()->setFlash('info', 'Se exportaron ('.$contar.') referencias de materias primas con Exito al modulo de insumos.');
        return $this->redirect(['view', 'id' => $id,'token' => $token]);
    }
    
    //informes
       //IMPRIME LA REMISION DE SEGUNDAS
     public function actionImprimir_insumos($id) {

        return $this->render('../formatos/reporte_salida_insumos', [
            'model' => SalidaBodega::findOne($id),
        ]);
    }
    
     public function actionImprimir_operaciones($id) {

        return $this->render('../formatos/costoProductOperaciones', [
            'model' => SalidaBodega::findOne($id),
        ]);
    }
       /**
     * Finds the SalidaBodega model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalidaBodega the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalidaBodega::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    //exportar a excel
     public function actionExcelconsultaSalida($tableexcel) {                
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
                    ->setCellValue('B1', 'CODIGO')
                    ->setCellValue('C1', 'REFERENCIA')
                    ->setCellValue('D1', 'UNIDADES')
                    ->setCellValue('E1', 'FECHA SALIDA')
                    ->setCellValue('F1', 'RESPONSABLE')
                    ->setCellValue('G1', 'AUTORIZADO')
                    ->setCellValue('H1', 'CERRADO')
                    ->setCellValue('I1', 'USER NAME')
                    ->setCellValue('J1', 'NUMERO SALIDA')
                    ->setCellValue('K1', 'INV. EXPORTADO')
                    ->setCellValue('L1', 'OBSERVACION');
        $i = 2;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_salida_bodega)
                    ->setCellValue('B' . $i, $val->codigo_producto)
                    ->setCellValue('C' . $i, $val->orden->referencia->referencia)
                    ->setCellValue('D' . $i, $val->unidades)
                    ->setCellValue('E' . $i, $val->fecha_salida)
                    ->setCellValue('F' . $i, $val->responsable)
                    ->setCellValue('G' . $i, $val->autorizadoSalida)
                    ->setCellValue('H' . $i, $val->cerradoSalida)
                    ->setCellValue('I' . $i, $val->user_name)
                    ->setCellValue('J' . $i, $val->numero_salida)
                    ->setCellValue('K' . $i, $val->insumosExportado)
                    ->setCellValue('L' . $i, $val->observacion);
                   
           $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Salida_insumos.xlsx"');
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
    
    //EXPORTAR A EXCEL DETALL DEL INSUMO
    
    //exportar a excel
     public function actionExportar_detalle($id) {    
        $detalle = SalidaBodegaDetalle::find()->where(['=','id_salida_bodega', $id])->all();
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
                               
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID SALIDA')
                    ->setCellValue('B1', 'CODIGO')
                    ->setCellValue('C1', 'REFERENCIA')
                    ->setCellValue('D1', 'TOTAL INSUMOS')
                    ->setCellValue('E1', 'FECHA SALIDA')
                    ->setCellValue('F1', 'RESPONSABLE')
                    ->setCellValue('G1', 'AUTORIZADO')
                    ->setCellValue('H1', 'CERRADO')
                    ->setCellValue('I1', 'USER NAME')
                    ->setCellValue('J1', 'NUMERO SALIDA')
                    ->setCellValue('K1', 'INV. EXPORTADO')
                    ->setCellValue('L1', 'OBSERVACION')
                    ->setCellValue('M1', 'CODIGO INSUMO')
                    ->setCellValue('N1', 'DESCRIPCION INSUMO')
                    ->setCellValue('O1', 'CANTIDAD DESPACHADA')
                    ->setCellValue('P1', 'NOTA DE ENTREGA');
                    
        $i = 2;
        
        foreach ($detalle as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->salidaBodega->id_salida_bodega)
                    ->setCellValue('B' . $i, $val->salidaBodega->codigo_producto)
                    ->setCellValue('C' . $i, $val->salidaBodega->orden->referencia->referencia)
                    ->setCellValue('D' . $i, $val->salidaBodega->unidades)
                    ->setCellValue('E' . $i, $val->salidaBodega->fecha_salida)
                    ->setCellValue('F' . $i, $val->salidaBodega->responsable)
                    ->setCellValue('G' . $i, $val->salidaBodega->autorizadoSalida)
                    ->setCellValue('H' . $i, $val->salidaBodega->cerradoSalida)
                    ->setCellValue('I' . $i, $val->salidaBodega->user_name)
                    ->setCellValue('J' . $i, $val->salidaBodega->numero_salida)
                    ->setCellValue('K' . $i, $val->salidaBodega->insumosExportado)
                    ->setCellValue('L' . $i, $val->salidaBodega->observacion)
                    ->setCellValue('M' . $i, $val->codigo_insumo)
                    ->setCellValue('N' . $i, $val->nombre_insumo)
                    ->setCellValue('O' . $i, $val->cantidad_despachar)
                    ->setCellValue('P' . $i, $val->nota);
           $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Detalla_insumos.xlsx"');
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
