<?php

namespace app\controllers;
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

//modelos
use app\models\CostoProducto;
use app\models\CostoProductoSearch;
use app\models\UsuarioDetalle;
use app\models\FormFiltroCostoProducto;
use app\models\FormMaquinaBuscar;
use app\models\CostoProductoDetalle;
use app\models\Insumos;
use app\models\Talla;
use app\models\ProductoTalla;
use app\models\ProductoColor;
use app\models\ProductoOperaciones;
/**
 * CostoProductoController implements the CRUD actions for CostoProducto model.
 */
class CostoProductoController extends Controller
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
     * Lists all CostoProducto models.
     * @return mixed
     */
   public function actionIndex($token = 0) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',103])->all()){
                $form = new FormFiltroCostoProducto();
                $codigo_producto = null;
                $tipo_producto = null;
                $fecha_creacion = null;
                $descripcion = null;
                $asignado = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $codigo_producto = Html::encode($form->codigo_producto);
                        $tipo_producto = Html::encode($form->id_tipo_producto);
                        $fecha_creacion = Html::encode($form->fecha_creacion);
                        $descripcion = Html::encode($form->descripcion);
                        $asignado = Html::encode($form->asignado);
                        $table = CostoProducto::find()
                                ->andFilterWhere(['=', 'codigo_producto', $codigo_producto])
                                ->andFilterWhere(['=', 'id_tipo_producto', $tipo_producto])
                                ->andFilterWhere(['>=', 'fecha_creacion', $fecha_creacion])   
                                ->andFilterWhere(['like','descripcion', $descripcion])
                                ->andFilterWhere(['=', 'asignado', $asignado]);
                        $table = $table->orderBy('id_producto DESC');
                        $tableexcel = $table->all();
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
                            if(isset($_POST['excel'])){                            
                                $check = isset($_REQUEST['id_producto DESC']);
                                $this->actionExcelconsultaProducto($tableexcel);
                            }
                } else {
                        $form->getErrors();
                }                    
            } else {
                $table = CostoProducto::find()
                        ->orderBy('id_producto DESC');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 40,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                    $this->actionExcelconsultaProducto($tableexcel);
                }
            }
            $to = $count->count();
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

    /**
     * Displays a single CostoProducto model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token)
    {
        $costo_producto_detalle = CostoProductoDetalle::find()->Where(['=', 'id_producto', $id])->all();
        $talla_producto = ProductoTalla::find()->where(['=','id_producto', $id])->orderBy('idtalla asc')->all();
        $color_producto = ProductoColor::find()->where(['=','id_producto', $id])->orderBy('id_producto_talla DESC')->all();
        $operaciones = ProductoOperaciones::find()->where(['=','id_producto', $id])->orderBy('idtipo asc')->all();
        $modeldetalle = new CostoProductoDetalle();
        $mensaje = "";
        if (Yii::$app->request->post()) {
            if (isset($_POST["eliminaroperacion"])) {
                if (isset($_POST["id_operacion"])) {
                    foreach ($_POST["id_operacion"] as $intCodigo) {
                        try {
                            $eliminar = ProductoOperaciones::findOne($intCodigo);
                            $eliminar->delete();
                            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                            $this->redirect(["costo-producto/view", 'id' => $id, 'token' => $token]);
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
        return $this->render('view', [
            'model' => $this->findModel($id),
            'costo_producto_detalle' => $costo_producto_detalle,
            'modeldetalle' => $modeldetalle,
            'mensaje' => $mensaje,
            'talla_producto' => $talla_producto,
            'color_producto' => $color_producto,
            'operaciones' => $operaciones,
            'token' => $token,
        ]);
    }

    /**
     * Creates a new CostoProducto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionNuevoproducto()
    {
        $model = new CostoProducto();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {           
            if ($model->validate()) {
                $empresa = \app\models\Matriculaempresa::findOne(1);
                $producto = CostoProducto::find()->where(['=','codigo_producto', $model->codigo_producto])->one();
                if($producto){
                    Yii::$app->getSession()->setFlash('error', 'Este codigo ya esta creado.!');
                }else{
                    $table = new CostoProducto();
                    $table->codigo_producto = $model->codigo_producto;
                    $table->descripcion = $model->descripcion;
                    $table->id_tipo_producto = $model->id_tipo_producto;
                    $fechaActual = date('Y-m-d');
                    $table->fecha_creacion = $fechaActual;
                    $table->aplicar_iva = $model->aplicar_iva; 
                    if($model->aplicar_iva == 1){
                       $table->porcentaje_iva = $empresa->porcentajeiva;    
                    }
                    $table->observacion = $model->observacion;
                    $table->tiempo_confeccion = $model->tiempo_confeccion;
                    $table->tiempo_terminacion = $model->tiempo_terminacion;
                    $table->observacion = $model->observacion;
                    $table->usuariosistema = Yii::$app->user->identity->username;
                    if ($table->insert()) {
                       $this->redirect(["costo-producto/index"]);
                    } else {
                        $msg = "error";
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

    /**
     * Updates an existing CostoProducto model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
           
            if($model->aplicar_iva == '0'){
                $model->aplicar_iva = 0;
                $model->porcentaje_iva = 0;
                $model->tiempo_terminacion = $model->tiempo_terminacion;
                $model->tiempo_confeccion = $model->tiempo_confeccion;
                $model->costo_con_iva = $model->costo_sin_iva;
                $model->save(false);
                 $this->ActualizarCosto($id, $model);
            }else{
                $matricula = \app\models\Matriculaempresa::findOne(1);
                $model->aplicar_iva = 1;
                $model->porcentaje_iva = $matricula->porcentajeiva;
                $model->save(false);
                $this->ActualizarCosto($id, $model);
            }
         //  return $this->redirect(['index', 'id' => $model->id_producto]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    protected function ActualizarCosto($id, $model) {
        $detalle = CostoProductoDetalle::find()->where(['=','id_producto', $id])->all();
        $total = 0;
        foreach ($detalle as $valores):
            $total += $valores->total;
        endforeach;
        $model->costo_sin_iva = $total;
        $model->costo_con_iva = $total + round($total * $model->porcentaje_iva)/100;
        $model->save(false);
    }
 // permita buscar los insumos para el costo del producto
    
     public function actionNuevodetalle($id, $token)
    {
        $insumos = Insumos::find()->where(['=','estado_insumo', 0])->orderBy('descripcion asc')->all();
        $form = new FormMaquinaBuscar();
        $q = null;
        $mensaje = '';
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                if ($q){
                    $insumos = insumos::find()
                            ->where(['like','descripcion',$q])
                            ->orwhere(['like','codigo_insumo',$q])
                            ->orderBy('descripcion asc')
                            ->all();
                }               
            } else {
                $form->getErrors();
            }                    
                    
        } else {
            $insumos = Insumos::find()->where(['=','estado_insumo', 0])->orderBy('descripcion asc')->all();
        }
        if (isset($_POST["id_insumos"])) {
                $intIndice = 0;
                foreach ($_POST["id_insumos"] as $intCodigo) {
                    $table = new CostoProductoDetalle();
                    $insumo = Insumos::find()->where(['id_insumos' => $intCodigo])->one();
                    $detalles = CostoProductoDetalle::find()
                        ->where(['=', 'id_producto', $id])
                        ->andWhere(['=', 'id_insumos', $insumo->id_insumos])
                        ->all();
                    $reg = count($detalles);
                    if ($reg == 0) {
                        $idproducto = $id;
                        $table->id_producto = $id;
                        $table->codigo_insumo = $insumo->codigo_insumo;
                        $table->id_insumos = $insumo->id_insumos;
                        $table->cantidad = 1;
                        $table->vlr_unitario = $insumo->precio_unitario;
                        $table->total = round($table->cantidad * $table->vlr_unitario,2);
                        $table->insert(); 
                        $this->ActualizarCostos($idproducto);
                    }
                }
                $this->redirect(["costo-producto/view", 'id' => $id, 'token' => $token]);
            }else{
                
            }
        return $this->render('_formnuevodetalle', [
            'insumos' => $insumos,            
            'mensaje' => $mensaje,
            'id' => $id,
            'form' => $form,
            'token' => $token,

        ]);
    }
    
    //CREAR OPERACIONES AL PRODUCTO
     public function actionNuevaoperacionproducto($id, $token)
    {
        $operacion = \app\models\ProcesoProduccion::find()->where(['=','estado', 0])->orderBy('proceso asc')->all();
        $form = new FormMaquinaBuscar();
        $q = null;
        $mensaje = '';
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
                        'pageSize' => 40,
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
                'pageSize' => 40,
                'totalCount' => $count->count(),
            ]);
            $operacion = $operacion
                    ->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();
        }
         if (isset($_POST["guardarynuevo"])) {
            if(isset($_POST["idproceso"])){
                $intIndice = 0;
                foreach ($_POST["idproceso"] as $intCodigo) {
                   $listado = ProductoOperaciones::find()
                            ->where(['=', 'idproceso', $intCodigo])
                            ->andWhere(['=', 'id_producto', $id])
                            ->all();
                    $reg = count($listado);
                    if ($reg == 0) {
                        $segundo = 0;
                        $minutos = 0;
                        if($_POST["id_tipo"][$intIndice] > 0){
                            $table = new ProductoOperaciones();
                            $table->idproceso = $intCodigo;
                            $table->id_producto = $id;
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
                            $table->usuario = Yii::$app->user->identity->username;
                            $table->id_tipo = $_POST["id_tipo"][$intIndice];
                            $table->save(false);
                        }   
                    }
                    $intIndice++;
                }
                $this->ContadorConfeccionTerminacion($id);
            }
        }
        return $this->render('_formcrearlistadoperacion', [
            'operacion' => $operacion,            
            'mensaje' => $mensaje,
            'pagination' => $pages,
            'id' => $id,
            'form' => $form,
            'token' => $token,

        ]);
    }
    //PROCESO QUE CUENTA el TIEMPO DE CONFECCION Y TERMINACION
    
    protected function ContadorConfeccionTerminacion($id) {
        $producto = CostoProducto::findOne($id);
        $operacion = ProductoOperaciones::find()->where(['=','id_producto', $id])->all();
        $confeccion = 0; $terminacion = 0;
        foreach ($operacion as $operaciones):
            if($operaciones->idtipo == 1){
                $confeccion += $operaciones->minutos;
            }else{
                $terminacion += $operaciones->minutos;
            }
            $producto->tiempo_confeccion = $confeccion;
            $producto->tiempo_terminacion = $terminacion;
            $producto->save(false);
        endforeach;
    }
    
    //EDITAR LAS OPERACIONES PARA CAMBIAR SI EN CONFECCION O TERMINACION
     public function actionEditaroperacionproducto($id, $token) {
        $mds = ProductoOperaciones::find()->where(['=', 'id_producto', $id])->orderBy('idtipo, idproceso desc ')->all();
        $error = 0;
      
        if (isset($_POST["id_operacion"])) {
            $intIndice = 0;
            $aux= 0; $nuevo_minuto = 0;
            foreach ($_POST["id_operacion"] as $intCodigo) {
                $table = ProductoOperaciones::findOne($intCodigo);
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
            $mds = ProductoOperaciones::find()->where(['=', 'id_producto', $id])->orderBy('idtipo, idproceso desc ')->all();
          $this->redirect(["costo-producto/view", 'id' => $id,'mds' =>$mds, 'token' => $token]);            
        }
        return $this->render('_formeditaroperacionesproducto', [
                    'mds' => $mds,
                    'id' => $id,
                    'token' => $token,
        ]);
    }
    
    //PERMITE EDITAR LOS DETALLES
    
     public function actionEditardetalle($token)
    {
        $iddetalleproducto = Html::encode($_POST["iddetalle"]);
        $idproducto = Html::encode($_POST["idproducto"]);

        if(Yii::$app->request->post()){
            if((int) $iddetalleproducto)
            {
                $table = CostoProductoDetalle::findOne($iddetalleproducto);
                if ($table) {
                   $table->cantidad = Html::encode($_POST["cantidad"]);
                    $table->vlr_unitario = Html::encode($_POST["vlrunitario"]);
                    $table->total = Html::encode($_POST["cantidad"]) * Html::encode($_POST["vlrunitario"]);
                    $table->id_producto = Html::encode($_POST["idproducto"]);
                    $table->save(false);
                    $this->actualizarCostos($idproducto);
                    $this->redirect(["costo-producto/view",'id' => $idproducto, 'token' => $token]);

                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }
            }
        }
    }
    // ELIMINA LOS DETALLES DE INSUMOS
    public function actionEliminardetalle($token)
    {
        if(Yii::$app->request->post())
        {
            $iddetalle = Html::encode($_POST["iddetalle"]);
            $idproducto = Html::encode($_POST["idproducto"]);
            if((int) $iddetalle){
                if(CostoProductoDetalle::deleteAll("id=:id", [":id" => $iddetalle])){
                    $this->actualizarCostos($idproducto);
                    $this->redirect(["costo-producto/view",'id' => $idproducto, 'token' => $token]);
                }else{
                    echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("costo-producto/index")."'>";
                }
            }else{
                echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("costo-producto/index")."'>";
            }
        }else{
            return $this->redirect(["costo-producto/index"]);
        }
    }
    
    //ELIMIAR LOS COLORES
     public function actionEliminarcolores($id, $id_color, $token) {
        
        if (Yii::$app->request->post()) {
            $color = ProductoColor::findOne($id_color);
            if ((int) $id_color) {
                try {
                    ProductoColor::deleteAll("id_producto_color=:id_producto_color", [":id_producto_color" => $id_color]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    $this->redirect(["costo-producto/view",'id'=> $id, 'token' => $token]);
                } catch (IntegrityException $e) {
                   $this->redirect(["costo-producto/view",'id'=> $id, 'token' => $token]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el color Nro :' .$color->color->color .', tiene registros asociados en otros procesos');
                } catch (\Exception $e) {

                  $this->redirect(["costo-producto/view",'id'=> $id, 'token' => $token]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el color Nro :' .$color->color->color .', tiene registros asociados en otros procesos');
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el registros, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("costo-producto/index") . "'>";
            }
        } else {
            $this->redirect(["costo-producto/view",'id'=> $id, 'token' => $token]);
        }
    }
    
    //editar todos los detalles
     public function actionEditartododetalle($id, $token)
    {
        $detalles = CostoProductoDetalle::find()->where(['=', 'id_producto', $id])->all();
        $idproducto = $id;
        if (isset($_POST["iddetalle"])) {
            $intIndice = 0;
            foreach ($_POST["iddetalle"] as $intCodigo) {
                if($_POST["cantidad"][$intIndice] > 0 ){
                    $table = CostoProductoDetalle::findOne($intCodigo);
                    $table->cantidad = $_POST["cantidad"][$intIndice];
                    $table->vlr_unitario = $_POST["vlrunitario"][$intIndice];
                    $table->total = $_POST["cantidad"][$intIndice] * $_POST["vlrunitario"][$intIndice];
                    $table->update();
                    $this->actualizarCostos($idproducto);
                }
                $intIndice++;
            }
            $this->redirect(["costo-producto/view",'id' => $id, 'token' => $token]);
        }
        return $this->render('_formeditartododetalle', [
            'detalles' => $detalles,
            'id' => $id,
            'token' => $token,
        ]);
    }
    
    //EDITAR TODO LOS COLORES
      public function actionEditarcolores($id, $token)
    {
        $colores = ProductoColor::find()->where(['=', 'id_producto', $id])->all();
        $idproducto = $id;
        if (isset($_POST["id_color"])) {
            $intIndice = 0;
            $cant = 0;
            foreach ($_POST["id_color"] as $intCodigo) {
                $color = ProductoColor::findOne($intCodigo);
               $color->cantidad_color = $_POST["cantidad_color"][$intIndice];
               $color->save(false);
               $this->ActualizarCantidades($id);
               $intIndice++;
            }
            $this->redirect(["costo-producto/view",'id' => $id, 'token' => $token]);
        }
        return $this->render('_formeditarcoloresgrupal', [
            'colores' => $colores,
            'id' => $id,
            'token' => $token,
        ]);
    }
    
    protected function ActualizarCantidades($id) {
        $colores = ProductoColor::find()->where(['=','id_producto', $id])->all();
        $tallas = ProductoTalla::find()->where(['=','id_producto', $id])->all();
        $cantidad = 0;
        foreach ($tallas as $talla):
            $tallaProducto = $talla->id_producto_talla;
            $talla_editado = ProductoTalla::findOne($talla->id_producto_talla);
            $color = ProductoColor::find()->where(['=','id_producto_talla', $tallaProducto])->all();
            if(count($color) > 0){
                foreach ($color as $colores):
                   $cantidad += $colores->cantidad_color;     
                endforeach;
                $talla_editado->cantidad = $cantidad;
                $talla_editado->save(false);
                $cantidad = 0;
            }else{
                $talla_editado->cantidad = $color->cantidad_color;
                $talla_editado->save(false);
                $cantidad = 0;
            }
        endforeach;
        
    }
    //CODIGO QUE ELIMINA TODOS LOS DETALLES
     public function actionEliminartododetalle($id, $token)
    {
        $detalles = CostoProductoDetalle::find()->where(['=', 'id_producto', $id])->all();
        $mensaje = "";
        if(Yii::$app->request->post())
        {
            $intIndice = 0;
            $idproducto = $id;
            if (isset($_POST["seleccion"])) {
                foreach ($_POST["seleccion"] as $intCodigo)
                {
                    $costodetalle = CostoProductoDetalle::findOne($intCodigo);
                    if(CostoProductoDetalle::deleteAll("id=:id", [":id" => $intCodigo]))
                    {
                       
                    }
                }
                $this->actualizarCostos($idproducto);
                $this->redirect(["costo-producto/view",'id' => $id, 'token' => $token]);
            }else {
                $mensaje = "Debe seleccionar al menos un registro";
            }
        }
        return $this->render('_formeliminartododetalle', [
            'detalles' => $detalles,
            'id' => $id,
            'mensaje' => $mensaje,
            'token' => $token,
        ]);
    }
    
    //PROCESO QUE ACTUALIZA LOS COSTOS DEL PRODUCTO ANTES Y DESPUES DE IVA
    protected function actualizarCostos($idproducto) {
        $costo_producto = CostoProducto::findOne($idproducto);
        $costo_producto_detalle = CostoProductoDetalle::find()->where(['=','id_producto', $idproducto])->all();
        $suma = 0; $totaliva = 0; $totalsiniva = 0;
        foreach ($costo_producto_detalle as $calculo):
            $suma += $calculo->total;
        endforeach;
        $totalsiniva = $suma;
        $totaliva = ($suma * $costo_producto->porcentaje_iva)/100;
        $costo_producto->costo_sin_iva = $totalsiniva;
        $costo_producto->costo_con_iva = round($totaliva + $suma);
        $costo_producto->save(false);
    }
    //PROCESO QUE AUTORIZA
    
    public function actionAutorizado($id, $token) {
        $model = $this->findModel($id);
        $contador = 0; $subtotal = 0; $iva=0; $total = 0;
        $talla = ProductoTalla::find()->where(['=','id_producto', $id])->one();
        $colores = ProductoColor::find()->where(['=','id_producto', $id])->one();
        $detalle = \app\models\AsignacionProductoDetalle::find()->where(['=','id_producto', $id])->one();
        if(!$detalle){
            if($talla){
                if($colores){
                    $talla = ProductoTalla::find()->where(['=','id_producto', $id])->all();
                    if ($model->autorizado == 0) {                        
                       if(count($talla) > 0){
                            foreach ($talla as $tallas):
                                $contador += $tallas->cantidad;
                            endforeach;
                            $subtotal= round($model->costo_sin_iva * $contador); 
                            $iva = round(($subtotal * $model->porcentaje_iva)/100);
                            $total = round($subtotal + $iva);
                            $model->subtotal_producto = $subtotal;
                            $model->total_producto = $total;
                            $model->cantidad = $contador;
                        }
                        $model->autorizado = 1;            
                        $model->update();
                        $this->redirect(["costo-producto/view", 'id' => $id, 'token' => $token]);            
                    } else{
                        $model->autorizado = 0;
                        $model->update();
                        $this->redirect(["costo-producto/view", 'id' => $id, 'token' => $token]); 
                    }
                }else{
                    $this->redirect(["costo-producto/view", 'id' => $id, 'token' => $token]); 
                    Yii::$app->getSession()->setFlash('success', 'El producto no se puede autorizar porque NO tiene colores registrados para las tallas.'); 
                }    
            }else{
                $this->redirect(["costo-producto/view", 'id' => $id, 'token' => $token]); 
                Yii::$app->getSession()->setFlash('warning', 'El producto no se puede autorizar porque NO tiene tallas registradas.');
            }  
        }else{
                $this->redirect(["costo-producto/view", 'id' => $id, 'token' => $token]); 
                Yii::$app->getSession()->setFlash('error', 'El producto no se puede DESAUTORIZAR porque ya esta asignado a un proveedor.');
        }    
             
    }
    //PROCESO QUE ABRE NUAVAMENTE LA ASIGNACION
    public function actionAbriasignacion($id, $token) {
        $model = $this->findModel($id);
        $model->asignado = 0;
        $model->update();
        $this->redirect(["costo-producto/view", 'id' => $id, 'token' => $token]); 
    }
    
    public function actionEliminar($id) {
        if (Yii::$app->request->post()) {
            $costoproducto = CostoProducto::findOne($id);
            if ((int) $id) {
                try {
                    CostoProducto::deleteAll("id_producto=:id_producto", [":id_producto" => $id]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    $this->redirect(["costo-producto/index"]);
                } catch (IntegrityException $e) {
                    $this->redirect(["costo-producto/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, este Código de prenda tiene registros asociados en otros procesos');
                } catch (\Exception $e) {

                    $this->redirect(["costo-producto/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, este Código de prenda tiene registros asociados en otros procesos');
                }
            } else {
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("costo-producto/index") . "'>";
            }
        } else {
            return $this->redirect(["costo-producto/index"]);
        }
    }
    //CREAR TALLAS
     public function actionCreartallas($id, $token){
        $tallas = Talla::find()->orderBy('sexo,talla asc')->all();
        $form = new FormMaquinaBuscar();
        $q = null;
        $mensaje = '';
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                if ($q){
                    $tallas = Talla::find()
                            ->where(['like','talla',$q])
                            ->orwhere(['like','sexo',$q])
                            ->orderBy('sexo asc')
                            ->all();
                }               
            } else {
                $form->getErrors();
            }                    
                    
        } else {
             $tallas = Talla::find()->orderBy('sexo,talla asc')->all();
        }
        if (isset($_POST["idtalla"])) {
                $intIndice = 0;
                foreach ($_POST["idtalla"] as $intCodigo) {
                    $table = new ProductoTalla();
                    $talla = Talla::find()->where(['idtalla' => $intCodigo])->one();
                    $detalles = ProductoTalla::find()
                        ->where(['=', 'id_producto', $id])
                        ->andWhere(['=', 'idtalla', $talla->idtalla])
                        ->all();
                    $reg = count($detalles);
                    if ($reg == 0) {
                        $table->idtalla = $intCodigo;
                        $table->id_producto = $id;
                        $table->cantidad = 0;
                        $table->usuariosistema = Yii::$app->user->identity->username;
                        $table->insert(); 
                    }
                }
                $this->redirect(["costo-producto/view", 'id' => $id, 'token' => $token]);
        }
        return $this->render('creartallas', [
            'tallas' => $tallas,            
            'mensaje' => $mensaje,
            'id' => $id,
            'form' => $form,
            'token' => $token,
        ]);
    
    }
    //PROCESO PARA CREAR LOS COLORES
      public function actionCrearcolores($id_talla, $id, $token)
    {
        $colores = \app\models\Color::find()->orderBy('color ASC')->all();
        if (Yii::$app->request->post()) {
            if (isset($_POST["enviarcolor"])) { 
                if (isset($_POST["id_color"]) != '') { 
                    $intIndice = 0;
                    foreach ($_POST["id_color"] as $intCodigo):
                        $color = ProductoColor::find()->where(['=','id', $intCodigo])->andWhere(['=','id_producto', $id])->andWhere(['=','id_producto_talla', $id_talla])->one();
                     
                        if(!$color){
                            $table = new ProductoColor();
                            $table->id_producto_talla = $id_talla;
                            $table->id = $intCodigo;
                            $table->id_producto = $id;
                            $table->usuariosistema = Yii::$app->user->identity->username;
                            $table->save(false);
                        }  
                        $intIndice++;
                    endforeach;
                   return $this->redirect(['view','id' => $id, 'token' => $token]);
                }else{
                     Yii::$app->getSession()->setFlash('success', 'Debe de seleccionar un color para la talla.'); 
                    return $this->redirect(['view','id' => $id, 'token' => $token]);
                   
                }    
            }
        }
        return $this->renderAjax('_crearcolores', [
            'id' => $id,
            'id_talla' => $id_talla,
            'colores' => $colores,
            'token' => $token,
        ]);      
    }
    
    //buscar asignaciones
    public function actionBuscarasignacion($id, $token)
    {
        $detalle = \app\models\AsignacionProductoDetalle::find()->where(['=','id_producto', $id])->one();
        return $this->renderAjax('_buscarasignacion', [
            'id' => $id,
            'detalle' => $detalle,
            'token' => $token,
        ]);      
    }
    
    //IMPRESIONES
    public function actionImprimirinsumos($id) {
        return $this->render('../formatos/costoProductoInsumos', [
                    'model' => $this->findModel($id),
        ]);
    }
    
    public function actionImprimiroperaciones($id) {
        return $this->render('../formatos/costoProductOperaciones', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the CostoProducto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CostoProducto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CostoProducto::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
     public function actionExcelconsultaProducto($tableexcel) {                
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
                              
        $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'CODIGO')
                    ->setCellValue('C1', 'PRODUCTO')
                    ->setCellValue('D1', 'COSTO SIN IVA')
                    ->setCellValue('E1', 'COSTO CON IVA')
                    ->setCellValue('F1', 'CANTIDAD')
                    ->setCellValue('G1', 'SUBTOTAL')                    
                    ->setCellValue('H1', 'TOTAL PRODUCTO')
                    ->setCellValue('I1', 'APLICA IVA')
                    ->setCellValue('J1', '% IVA ')
                    ->setCellValue('K1', 'SAM CONFECCION')
                    ->setCellValue('L1', 'SAM TERMINACION')
                    ->setCellValue('M1', 'FECHA PROCESO')
                    ->setCellValue('N1', 'USUARIO')
                    ->setCellValue('O1', 'OBSERVACION');
                   
        $i = 2  ;
        
        foreach ($tableexcel as $asignar) {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $asignar->id_producto)
            ->setCellValue('B' . $i, $asignar->codigo_producto)
            ->setCellValue('C' . $i, $asignar->descripcion)
            ->setCellValue('D' . $i, $asignar->costo_sin_iva)
            ->setCellValue('E' . $i, $asignar->costo_con_iva)
            ->setCellValue('F' . $i, $asignar->cantidad)
            ->setCellValue('G' . $i, $asignar->subtotal_producto)                    
            ->setCellValue('H' . $i, $asignar->total_producto)
            ->setCellValue('I' . $i, $asignar->aplicaiva)
            ->setCellValue('J' . $i, $asignar->porcentaje_iva)
            ->setCellValue('K' . $i, $asignar->tiempo_confeccion)
            ->setCellValue('L' . $i, $asignar->tiempo_terminacion)
            ->setCellValue('M' . $i, $asignar->fecha_creacion)
            ->setCellValue('N' . $i, $asignar->usuariosistema)
            ->setCellValue('O' . $i, $asignar->observacion);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Productos');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Listado_productos.xlsx"');
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
