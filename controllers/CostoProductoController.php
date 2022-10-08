<?php

namespace app\controllers;
//clases
use Codeception\Module\Cli;
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
   public function actionIndex() {
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
    public function actionView($id)
    {
        $costo_producto_detalle = CostoProductoDetalle::find()->Where(['=', 'id_producto', $id])->all();
        $talla_producto = ProductoTalla::find()->where(['=','id_producto', $id])->orderBy('idtalla asc')->all();
        $color_producto = ProductoColor::find()->where(['=','id_producto', $id])->orderBy('id_producto_talla DESC')->all();
        $modeldetalle = new CostoProductoDetalle();
        $mensaje = "";
        return $this->render('view', [
            'model' => $this->findModel($id),
            'costo_producto_detalle' => $costo_producto_detalle,
            'modeldetalle' => $modeldetalle,
            'mensaje' => $mensaje,
            'talla_producto' => $talla_producto,
            'color_producto' => $color_producto,
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
            if($model->aplicar_iva == 0){
                $model->aplicar_iva = 0;
                $model->porcentaje_iva = 0;
                $model->costo_con_iva = $model->costo_sin_iva;
                $model->save();
            }else{
                $matricula = \app\models\Matriculaempresa::findOne(1);
                $model->aplicar_iva = 1;
                $model->porcentaje_iva = $matricula->porcentajeiva;
                $model->save(false);
                $this->ActualizarCosto($id, $model);
            }
           return $this->redirect(['index', 'id' => $model->id_producto]);
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
    
     public function actionNuevodetalle($id)
    {
        $insumos = Insumos::find()->where(['=','estado_insumo', 1])->orderBy('descripcion asc')->all();
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
            $insumos = Insumos::find()->where(['=','estado_insumo', 1])->orderBy('descripcion asc')->all();
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
                $this->redirect(["costo-producto/view", 'id' => $id]);
            }else{
                
            }
        return $this->render('_formnuevodetalle', [
            'insumos' => $insumos,            
            'mensaje' => $mensaje,
            'id' => $id,
            'form' => $form,

        ]);
    }
    
    //PERMITE EDITAR LOS DETALLES
    
     public function actionEditardetalle()
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
                    $this->redirect(["costo-producto/view",'id' => $idproducto]);

                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }
            }
        }
    }
    // ELIMINA LOS DETALLES DE INSUMOS
    public function actionEliminardetalle()
    {
        if(Yii::$app->request->post())
        {
            $iddetalle = Html::encode($_POST["iddetalle"]);
            $idproducto = Html::encode($_POST["idproducto"]);
            if((int) $iddetalle){
                if(CostoProductoDetalle::deleteAll("id=:id", [":id" => $iddetalle])){
                    $this->actualizarCostos($idproducto);
                    $this->redirect(["costo-producto/view",'id' => $idproducto]);
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
     public function actionEliminarcolores($id, $id_color) {
        
        if (Yii::$app->request->post()) {
            $color = ProductoColor::findOne($id_color);
            if ((int) $id_color) {
                try {
                    ProductoColor::deleteAll("id_producto_color=:id_producto_color", [":id_producto_color" => $id_color]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    $this->redirect(["costo-producto/view",'id'=> $id]);
                } catch (IntegrityException $e) {
                   $this->redirect(["costo-producto/view",'id'=> $id]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el color Nro :' .$color->color->color .', tiene registros asociados en otros procesos');
                } catch (\Exception $e) {

                  $this->redirect(["costo-producto/view",'id'=> $id]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el color Nro :' .$color->color->color .', tiene registros asociados en otros procesos');
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el registros, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("costo-producto/index") . "'>";
            }
        } else {
            $this->redirect(["costo-producto/view",'id'=> $id]);
        }
    }
    
    //editar todos los detalles
     public function actionEditartododetalle($id)
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
            $this->redirect(["costo-producto/view",'id' => $id]);
        }
        return $this->render('_formeditartododetalle', [
            'detalles' => $detalles,
            'id' => $id,
        ]);
    }
    
    //EDITAR TODO LOS COLORES
      public function actionEditarcolores($id)
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
            $this->redirect(["costo-producto/view",'id' => $id]);
        }
        return $this->render('_formeditarcoloresgrupal', [
            'colores' => $colores,
            'id' => $id,
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
     public function actionEliminartododetalle($id)
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
                $this->redirect(["costo-producto/view",'id' => $id]);
            }else {
                $mensaje = "Debe seleccionar al menos un registro";
            }
        }
        return $this->render('_formeliminartododetalle', [
            'detalles' => $detalles,
            'id' => $id,
            'mensaje' => $mensaje,
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
    
    public function actionAutorizado($id) {
        $model = $this->findModel($id);
        $mensaje = "";
        $contador = 0; $subtotal = 0; $iva=0; $total = 0;
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
            $this->redirect(["costo-producto/view", 'id' => $id]);            
        } else{
            $model->autorizado = 0;
            $model->update();
             $this->redirect(["costo-producto/view", 'id' => $id]); 
        }
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
     public function actionCreartallas($id){
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
                $this->redirect(["costo-producto/view", 'id' => $id]);
        }
        return $this->render('creartallas', [
            'tallas' => $tallas,            
            'mensaje' => $mensaje,
            'id' => $id,
            'form' => $form,
        ]);
    
    }
    //PROCESO PARA CREAR LOS COLORES
      public function actionCrearcolores($id_talla, $id)
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
                            $table->insert();
                            $intIndice ++;
                        }    
                    endforeach;
                    return $this->redirect(['view','id' => $id]);
                }else{
                     Yii::$app->getSession()->setFlash('success', 'Debe de seleccionar un color para la talla.'); 
                    return $this->redirect(['view','id' => $id]);
                   
                }    
            }
        }
        return $this->renderAjax('_crearcolores', [
            'id' => $id,
            'id_talla' => $id_talla,
            'colores' => $colores,
        ]);      
    }
    
    //buscar asignaciones
    public function actionBuscarasignacion($id)
    {
        //$colores = \app\models\Color::find()->orderBy('color ASC')->all();
        return $this->renderAjax('_buscarasignacion', [
            'id' => $id,
        ]);      
    }
    
    //IMPRESIONES
    public function actionImprimirinsumos($id) {
        return $this->render('../formatos/costoProductoInsumos', [
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
}
