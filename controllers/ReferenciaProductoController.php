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

//MODELSS
use app\models\ReferenciaProducto;
use app\models\UsuarioDetalle;
use app\models\TipoProducto;
/**
 * ReferenciaProductoController implements the CRUD actions for ReferenciaProducto model.
 */
class ReferenciaProductoController extends Controller
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
     * Lists all ReferenciaProducto models.
     * @return mixed
     */
     public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',138])->all()){
                $form = new \app\models\FormFiltroReferencia();
                $codigo = null;
                $referencia = null;
                $tipo_prenda = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $codigo = Html::encode($form->codigo);
                        $referencia = Html::encode($form->referencia);
                        $tipo_prenda = Html::encode($form->tipo_prenda);
                        $table = ReferenciaProducto::find()
                                ->andFilterWhere(['=', 'codigo', $codigo])                                                                                              
                                ->andFilterWhere(['like', 'descripcion_referencia', $referencia])
                                ->andFilterWhere(['=','id_tipo_producto', $tipo_prenda]);
 
                        $table = $table->orderBy('codigo DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 12,
                            'totalCount' => $count->count()
                        ]);
                        $model = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                            if(isset($_POST['excel'])){                            
                                $check = isset($_REQUEST['codigo DESC']);
                                $this->actionExcelconsultaReferencias($tableexcel);
                            }
                } else {
                        $form->getErrors();
                }                    
            } else {
                $table = ReferenciaProducto::find()
                        ->orderBy('codigo DESC');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 12,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                    $this->actionExcelconsultaReferencias($tableexcel);
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
     * Displays a single ReferenciaProducto model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $lista_precio = \app\models\ReferenciaListaPrecio::find()->where(['=','codigo', $id])->all();
        $lista_insumos = \app\models\ReferenciaInsumos::find()->where(['=','codigo', $id])->all();
        if (Yii::$app->request->post()) {
            if (isset($_POST["actualizar_precio_venta"])) {
                if (isset($_POST["listado_precios"])) {
                     $intIndice = 0;
                    foreach ($_POST["listado_precios"] as $intCodigo) {
                        $table = \app\models\ReferenciaListaPrecio::findOne($intCodigo);
                        $table->valor_venta = $_POST["precio_venta_publico"][$intIndice];
                        $table->id_lista  = $_POST["lista_precio"][$intIndice];
                        $table->save();
                       $intIndice++;
                    }
                    return $this->redirect(['view','id' => $id]);
                }
            } 
            ///PERMITE ACTUALIZAR LA INFORMACION DEL INSUMO
            if (isset($_POST["actualizar_insumos"])) {
                if (isset($_POST["listado_insumos"])) {
                     $intIndice = 0;
                    foreach ($_POST["listado_insumos"] as $intCodigo) {
                        $table = \app\models\ReferenciaInsumos::findOne($intCodigo);
                        $conInsumo = \app\models\Insumos::findOne($table->id_insumos);
                        $table->idtipo = $_POST["tipo_orden"][$intIndice];
                         $table->total_unidades = $_POST["unidades"][$intIndice];
                        $table->maneja_unidad = $_POST["maneja_unidad"][$intIndice];
                        $table->cantidad = $_POST["cantidad"][$intIndice];
                        $table->costo_producto  = $table->cantidad * $conInsumo->precio_unitario;
                        $table->save(false);
                       $intIndice++;
                    }
                    $this->ActualizaCostoInsumos($id);
                    return $this->redirect(['view','id' => $id]);
                }
            } 
        }    
                
        return $this->render('view', [
            'model' => $this->findModel($id),
            'lista_precio' => $lista_precio,
            'lista_insumos' => $lista_insumos,
        ]);
    }
    
    //PROCESO QUE TOTALIZA LOS INSUMOS
    protected function ActualizaCostoInsumos($id) {
        $model = ReferenciaProducto::findOne($id);
        $consulta = \app\models\ReferenciaInsumos::find()->where(['=','codigo', $id])->all();
        $dato = 0;
        foreach ($consulta as $valor) {
            $dato += $valor->costo_producto;
        }
        $model->costo_producto = $dato;
        $model->save();
    }
    
       /**
     * Creates a new ReferenciaProducto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ReferenciaProducto();
        
        $sw = 0;
        if ($model->load(Yii::$app->request->post())) {
            $buscar = ReferenciaProducto::findOne($model->codigo);
            if(!$buscar){
                // $empresa = \app\models\Matriculaempresa::findOne(1);
                // $codigo = $this->CrearCodigoReferencia();
                 $model->codigo = $model->codigo;
                 $model->descripcion_referencia = $model->descripcion_referencia;
                 $model->id_tipo_producto = $model->id_tipo_producto;
                 $model->user_name = Yii::$app->user->identity->username;
                 $model->descripcion= $model->descripcion;
                 $model->save();
                 return $this->redirect(['index']);
            }else{
                Yii::$app->getSession()->setFlash('error', 'La referencia ingresada ya esta creada en el sistema.');
            }     
        }
        return $this->render('create', [
            'model' => $model,
            'sw' => $sw,
            
        ]);
    }
    
    ///consecutivo del codigo de la referencia
    protected function CrearCodigoReferencia() {
        $Dato = \app\models\Consecutivo::findOne(17);
        $codigo = $Dato->consecutivo + 1;
        $Dato->consecutivo = $codigo;
        $Dato->save();
        return ($codigo);
    }

    /**
     * Updates an existing ReferenciaProducto model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $sw = 1;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'sw' => $sw,
        ]);
    }

    /**
     * Deletes an existing ReferenciaProducto model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    //PROCESO QUE CREA EL NUEVO PRECIO
    public function actionNuevo_precio_venta($id) {
        $model = new \app\models\FormModeloAsignarPrecioVenta();
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                if (isset($_POST["crear_precio"])) {
                    if($model->nuevo_precio > 0){
                        $table = new \app\models\ReferenciaListaPrecio();
                        $table->codigo = $id;
                        $table->valor_venta = $model->nuevo_precio;
                        $table->user_name = Yii::$app->user->identity->username;
                        $table->save(false);
                        $this->redirect(["referencia-producto/view", 'id' => $id]);
                    }else{
                        Yii::$app->getSession()->setFlash('warning', 'No se asignó ningun precio de venta a público. Ingrese nuevamente.'); 
                        $this->redirect(["referencia-producto/view", 'id' => $id]);
                    }    
                }    
            }else{
                $model->getErrors();
            }    
        }
        return $this->renderAjax('new_precio_venta', [
            'model' => $model,
            'id' => $id,
        ]);
    }    
    
    //PERMITE CREAR EL COSTO DEL PRODUCTO
     public function actionNuevo_costo_producto($id) {
        $model = new \app\models\FormModeloAsignarPrecioVenta();
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                if (isset($_POST["crear_precio"])) {
                    if($model->nuevo_precio > 0){
                        $table = ReferenciaProducto::findOne($id);
                        $table->costo_producto = $model->nuevo_precio;
                        $table->save(false);
                        $this->redirect(["referencia-producto/index"]);
                    }else{
                        Yii::$app->getSession()->setFlash('warning', 'No se asignó ningun valor de costo. Ingrese nuevamente.'); 
                        $this->redirect(["index"]);
                    }    
                }    
            }else{
                $model->getErrors();
            }    
        }
        return $this->renderAjax('new_costo_producto', [
            'model' => $model,
            'id' => $id,
        ]);
    }    
    
     //BUSCA INSUMOS PARA CONFIGURAR LA REFERENCIA
     public function actionSearch_insumos($id)
    {
        $insumos = \app\models\Insumos::find()->orderBy('descripcion ASC')->all();
        $form = new \app\models\FormBuscarInsumos();
        $codigo= null;
        $nombre_producto = null;
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $codigo = Html::encode($form->codigo);    
                $nombre_producto = Html::encode($form->nombre_producto);  
                $table = \app\models\Insumos::find()
                        ->andFilterWhere(['=','codigo_insumo', $codigo])
                        ->andFilterWhere(['like','descripcion',$nombre_producto])
                        ->andWhere(['>','stock_real', 0]);
                $insumos = $table->orderBy('descripcion ASC');                    
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
        }else{
            $insumos = \app\models\Insumos::find()->where(['>','stock_real', 0])->orderBy('descripcion ASC');
            $count = clone $insumos;
            $pages = new Pagination([
                'pageSize' => 15,
                'totalCount' => $count->count(),
            ]);
            $insumos = $insumos
                    ->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();
        }
         if (isset($_POST["enviar_insumos"])) {
            if(isset($_POST["codigo_insumo"])){
                $intIndice = 0;
                foreach ($_POST["codigo_insumo"] as $intCodigo) {
                   $listado = \app\models\ReferenciaInsumos::find()
                            ->where(['=', 'id_insumos', $intCodigo])
                            ->andWhere(['=', 'codigo', $id])
                            ->all();
                    $reg = count($listado);
                    if ($reg == 0) {
                        $table = new \app\models\ReferenciaInsumos();
                        $table->codigo = $id;
                        $table->id_insumos = $intCodigo;
                        $table->cantidad = 1;
                        $table->fecha_registro = date('Y-m-d');
                        $table->user_name= Yii::$app->user->identity->username;
                        $table->save(false);
                    }
                    $intIndice++;
                }
                return $this->redirect(["referencia-producto/view", 'id' => $id]);
            }else{
                 Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro de la lista de insumos.');
                return $this->redirect(["referencia-producto/search_insumos", 'id' => $id]);
            }
        }
        return $this->render('_buscar_insumos', [
            'insumos' => $insumos,            
            'pagination' => $pages,
            'id' => $id,
            'form' => $form,

        ]);
    }
    
    //ELIMINAR INSUMOS
    public function actionEliminar_insumos($id, $id_detalle)
    {
        try {
            $model = \app\models\ReferenciaInsumos::findOne($id_detalle);
            $model->delete();
            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
            return $this->redirect(["referencia-producto/view",'id' => $id]);
        } catch (IntegrityException $e) {
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar la compra, tiene registros asociados en otros procesos');
            return $this->redirect(["referencia-producto/view",'id' => $id]);
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar la compra, tiene registros asociados en otros procesos');
            return $this->redirect(["referencia-producto/view",'id' => $id]);
        }
    }

    /**
     * Finds the ReferenciaProducto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReferenciaProducto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReferenciaProducto::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
