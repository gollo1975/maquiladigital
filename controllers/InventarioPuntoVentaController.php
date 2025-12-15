<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
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
use yii\db\Command;
use yii\db\ActiveQuery;
use yii\db\Exception; // Necesario para manejar excepciones de la base de datos

//models
//models

use app\models\InventarioPuntoVenta;
use app\models\UsuarioDetalle;
use app\models\FacturaVentaPunto;
use app\models\FacturaVentaPuntoDetalle;
use app\models\Pedidos;


/**
 * InventarioPuntoVentaController implements the CRUD actions for InventarioPuntoVenta model.
 */
class InventarioPuntoVentaController extends Controller
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
     * Lists all InventarioPuntoVenta models.
     * @return mixed
     */
     public function actionIndex($token = 0) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',173])->all()){
                $form = new \app\models\FiltroBusquedaInventarioPunto();
                $codigo = null;
                $categoria = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $punto_venta = null;
                $stock = null;
                $producto = null;
                $marca = null;
                $conPunto = \app\models\PuntoVenta::find()->orderBy('predeterminado DESC')->all();
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $codigo = Html::encode($form->codigo);
                        $stock = Html::encode($form->stock);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $producto = Html::encode($form->producto);
                        $punto_venta = Html::encode($form->punto_venta);
                        $marca = Html::encode($form->marca);
                        $categoria = Html::encode($form->categoria);
                        $table = InventarioPuntoVenta::find()
                                    ->andFilterWhere(['=', 'codigo_producto', $codigo])
                                    ->andFilterWhere(['=', 'id_marca', $marca])
                                    ->andFilterWhere(['=', 'id_categoria', $categoria])
                                    ->andFilterWhere(['between', 'fecha_proceso', $fecha_inicio, $fecha_corte])
                                    ->andFilterWhere(['like', 'nombre_producto', $producto])
                                    ->andFilterWhere(['>', 'stock_inventario', $stock])
                                    ->andFilterWhere(['=', 'id_punto', $punto_venta]);
                        $table = $table->orderBy('id_inventario DESC');
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
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_inventario  DESC']);
                            $this->actionExcelInventarioPuntoVenta($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = InventarioPuntoVenta::find() ->orderBy('id_inventario DESC');
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
                    if (isset($_POST['excel'])) {
                        $this->actionExcelInventarioPuntoVenta($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'model' => $model,
                            'form' => $form,
                            'pagination' => $pages,
                            'token' => $token,
                            'conPunto' => ArrayHelper::map($conPunto, 'id_punto', 'nombre_punto'),
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
    }
    
    
     //CONSULTAR INVENTARIOS
     public function actionSearch_inventario($token = 1) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',174])->all()){
                $form = new \app\models\FiltroBusquedaInventarioPunto();
                $codigo = null;
                $categoria = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $punto_venta = null;
                $stock = null;
                $producto = null;
                $marca = null;
                $conPunto = \app\models\PuntoVenta::find()->orderBy('predeterminado DESC')->all();
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $codigo = Html::encode($form->codigo);
                        $stock = Html::encode($form->stock);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $producto = Html::encode($form->producto);
                        $punto_venta = Html::encode($form->punto_venta);
                        $marca = Html::encode($form->marca);
                        $categoria = Html::encode($form->categoria);
                        $table = InventarioPuntoVenta::find()
                                    ->andFilterWhere(['=', 'codigo_producto', $codigo])
                                    ->andFilterWhere(['=', 'id_marca', $marca])
                                    ->andFilterWhere(['=', 'id_categoria', $categoria])
                                    ->andFilterWhere(['between', 'fecha_proceso', $fecha_inicio, $fecha_corte])
                                    ->andFilterWhere(['like', 'nombre_producto', $producto])
                                    ->andFilterWhere(['>', 'stock_inventario', $stock])
                                    ->andFilterWhere(['=', 'id_punto', $punto_venta]);
                        $table = $table->orderBy('id_inventario DESC');
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
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_inventario  DESC']);
                             $this->actionExcelInventarioPuntoVenta($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = InventarioPuntoVenta::find() ->orderBy('id_inventario DESC');
                    
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
                    if (isset($_POST['excel'])) {
                        $this->actionExcelInventarioPuntoVenta($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('search_inventario', [
                            'model' => $model,
                            'form' => $form,
                            'pagination' => $pages,
                            'token' => $token,
                            'conPunto' => ArrayHelper::map($conPunto, 'id_punto', 'nombre_punto'),
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
    }

    /**
     * Displays a single InventarioPuntoVenta model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token, $codigo)
    {
        
        $talla_color = \app\models\DetalleColorTalla::find()->where(['=','id_inventario', $id])->orderBy('idtalla DESC')->all();
        $talla_color_cerrado= \app\models\DetalleColorTalla::find()->where(['=','id_inventario', $id])->andWhere(['=','cerrado', 1])->all();
        if($codigo == 0){
            $traslado = \app\models\TrasladoReferenciaPunto::find()->where(['=','id_inventario_saliente', $id])->all();
        }else{
           $traslado = \app\models\TrasladoReferenciaPunto::find()->where(['=','id_inventario_entrante', $id])->all(); 
        }    
        if(isset($_POST["actualizarlineas"])){
            if(isset($_POST["entrada_cantidad"])){
                $intIndice = 0;
                foreach ($_POST["entrada_cantidad"] as $intCodigo):
                    $detalle = \app\models\DetalleColorTalla::find()->where(['=','id_detalle', $intCodigo])->andWhere(['=','cerrado', 0])->one();
                    if($detalle){
                        if($_POST["cantidad"][$intIndice] > 0){
                            $table = \app\models\DetalleColorTalla::findOne($intCodigo);
                            if($codigo <> 0){
                                $unidad_entrada = $_POST["cantidad"][$intIndice]; //asigno variable
                                $inventario = InventarioPuntoVenta::findOne($codigo);
                                if($unidad_entrada <= $inventario->stock_inventario){ //si hay stoxk
                                    $detalle->cantidad = $unidad_entrada;
                                    $detalle->stock_punto = $unidad_entrada;
                                    $detalle->save();
                                    $intIndice++;
                                }else{
                                    $intIndice++;
                                }
                            }else{  
                                $detalle->cantidad = $_POST["cantidad"][$intIndice];
                                $detalle->stock_punto = $_POST["cantidad"][$intIndice];
                                $detalle->save();
                                $intIndice++;
                            } 
                        }else{    
                           $intIndice++; 
                        }    
                    }else{
                        $intIndice++;
                    }   
                endforeach;
                    $this->ActualizarLineas($id);
                    $this->ActualizarTotalesProducto($id);
                return $this->redirect(['view','id' =>$id, 'token' => $token, 'codigo' => $codigo]);
            }
            
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
            'token' => $token,
            'talla_color' => $talla_color,
            'talla_color_cerrado' => $talla_color_cerrado,
            'codigo' => $codigo, 
            'traslado' => $traslado,            
        ]);
    }
    
    //LISTAS DE PRECIOS
    public function actionLista_precios($id, $token) {
       $model = $this->findModel($id);
       $lista_precio = \app\models\PrecioVentaInventario::find()->where(['=','id_inventario', $id])->all();
       $descuentos = \app\models\ReglaDescuentoComercial::find()->where(['=','id_inventario', $id])->all();
       if (Yii::$app->request->post()) {
            if (isset($_POST["actualizar_precio_venta"])) {
                if (isset($_POST["listado_precios"])) {
                     $intIndice = 0;
                    foreach ($_POST["listado_precios"] as $intCodigo) {
                        $table = \app\models\PrecioVentaInventario::findOne($intCodigo);
                        $table->valor_venta = $_POST["precio_venta_publico"][$intIndice];
                        $table->id_lista  = $_POST["lista_precio"][$intIndice];
                         $table->predeterminado  = $_POST["predeterminado"][$intIndice];
                        $table->save();
                       $intIndice++;
                    }
                    return $this->redirect(['lista_precios','id' => $id, 'token' => $token]);
                }
            } 
       }    
       return $this->render('vista_lista_precio', [
            'model' => $model,
            'lista_precio' => $lista_precio,   
            'descuentos' => $descuentos,
           'token' => $token,
        ]);
    }
    
    //PROCESO QUE SUMA TODAAS LAS CANTIDAD
    protected function ActualizarLineas($id) {
        $inventario = InventarioPuntoVenta::findOne($id);
        $detalle = \app\models\DetalleColorTalla::find()->where(['=','id_inventario', $id])->all();
        $suma = 0;
        foreach ($detalle as $detalles):
           $suma += $detalles->cantidad; 
        endforeach;
        $inventario->stock_unidades =  $suma;
        $inventario->stock_inventario =  $suma;
        $inventario->save();
    }
    
     //PROCESO QUE ACTUALIZA LOS PRECIOS DEL PRODUCTO
    protected function ActualizarTotalesProducto($id) {
       $inventario = InventarioPuntoVenta::findOne($id);
       $subtotal =0;
       $impuesto = 0;
       $total = 0;
       $poncentaje = ''.round(($inventario->porcentaje_iva / 100),2);
       $subtotal = $inventario->stock_inventario * $inventario->costo_unitario;
       if($inventario->iva_incluido == 1){
          $impuesto = round($subtotal * $poncentaje);    
       }else{
           $impuesto = 0;
       }
       $inventario->subtotal = $subtotal - $impuesto;
       $inventario->valor_iva = $impuesto;
       $inventario->total_inventario = $subtotal;
       $inventario->save();
    }

    /**
     * Creates a new InventarioPuntoVenta model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
     public function actionCreate()
    {
        $model = new InventarioPuntoVenta();
         if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())){
            $conDato = \app\models\InventarioPuntoVenta::find()->where(['=','codigo_producto', $model->codigo_producto])->one();
            if($conDato){
                 Yii::$app->getSession()->setFlash('info', 'El codigo ('. $model->codigo_producto. ') ya esta codificado en el sistema. Valide la informacion.');
            }else{
                $conInventario = \app\models\ConfiguracionInventario::findOne(1);
                $conIva = \app\models\ConfiguracionIva::find()->where(['=','predeterminado', 1])->one();
                if($conInventario->aplica_solo_inventario == 1 ){
                    $model->save() ;
                    $model->user_name = Yii::$app->user->identity->username;
                    $model->codigo_barra = $model->codigo_producto;
                    $model->id_punto = 1;
                    $model->stock_unidades = $model->stock_unidades;
                    $model->stock_inventario = $model->stock_unidades;
                    $model->fecha_creacion = date('Y-m-d H:i:s');
                   
                    if($conInventario->aplica_iva_incluido == 0){
                        $model->iva_incluido = 0;
                        $model->porcentaje_iva = $conIva->valor_iva;
                    }else{
                        $model->iva_incluido = 1;
                        $model->porcentaje_iva = $conIva->valor_iva;
                    }
                    $model->fecha_creacion = date('Y-m-d H:i:s');
                    $model->save();
                    $id = $model->id_inventario;
                    return $this->redirect(['index']);
                   
                } 
                if($conInventario->aplica_inventario_talla_color == 1 ||  $conInventario->aplica_inventario_tallas == 1){
                    $model->save();
                    $id = $model->id_inventario;
                    $model->user_name = Yii::$app->user->identity->username;
                    $model->codigo_barra = $model->codigo_producto;
                    $model->id_punto = 1;
                    $model->stock_unidades = 0;
                    $model->stock_inventario = 0;
                    $model->aplica_talla_color = 1;
                    if($conInventario->aplica_iva_incluido == 0){
                        $model->iva_incluido = 0;
                        $model->porcentaje_iva = $conIva->valor_iva;
                    }else{
                        $model->iva_incluido = 1;
                        $model->porcentaje_iva = $conIva->valor_iva;
                    }
                    $model->fecha_creacion = date('Y-m-d H:i:s');
                    $model->save();
                    return $this->redirect(['index']);
                }    
                   
            }
        }   

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing InventarioPuntoVenta model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
             $conInventario = \app\models\ConfiguracionInventario::findOne(1);
            if($conInventario->aplica_solo_inventario == 1 ){
                if ($model->stock_unidades > 0){
                    if($model->codigo_enlace_bodega > 0){
                        $table = InventarioPuntoVenta::findOne($model->codigo_enlace_bodega);
                        if($model->stock_unidades <= $table->stock_inventario){
                            $model->save() ;
                            $model->user_name = Yii::$app->user->identity->username;
                            $model->codigo_barra = $model->codigo_producto;
                            $model->stock_unidades = $model->stock_unidades;
                            $model->stock_inventario = $model->stock_unidades;
                            $model->aplica_talla_color = 0;
                            $model->save();
                            $table->stock_inventario -= $model->stock_unidades;
                            $table->save();
                             return $this->redirect(['index']); 
                           
                        }else{ 
                             Yii::$app->getSession()->setFlash('error', 'Las cantidades a ingresar son mayores que el inventario actual que hay en bodega.');
                        }
                    }else{
                        $model->save() ;
                        $model->stock_unidades = $model->stock_unidades;
                        $model->stock_inventario = $model->stock_unidades;
                         $model->aplica_talla_color = 0;
                        $model->save();
                        return $this->redirect(['index']); 
                    }
                    
                }else{
                    Yii::$app->getSession()->setFlash('error', 'Debe de ingresar las unidades al inventario.');
                } 
            }
            if($conInventario->aplica_inventario_talla_color == 1 ||  $conInventario->aplica_inventario_tallas == 1){
                    $model->save();
                    $model->codigo_barra = $model->codigo_producto;
                    $model->id_punto = 1;
                    $model->stock_unidades = 0;
                    $model->stock_inventario = 0;
                    $model->save();
                    $id = $model->id_inventario;
                    return $this->redirect(['index']);
            }    
        }

        return $this->render('update', [
            'model' => $model,
            
        ]);
    }
    
    //CREAR DESCUENTO COMERCIAL
    public function actionCrear_regla_comercial($id, $sw = 0, $token) {
        $model = new \app\models\ModeloEditarReglaDescuento();
        $inventario = InventarioPuntoVenta::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                if (isset($_POST["regla_distribuidor"])) {
                    $table = new \app\models\ReglaDescuentoComercial();
                    $table->id_inventario = $id;
                    $table->fecha_inicio =  $model->fecha_inicio;
                    $table->fecha_final = $model->fecha_final;
                    $table->nuevo_valor = $model->nuevo_valor;
                    $table->tipo_descuento = $model->tipo_descuento;
                    $table->user_name = Yii::$app->user->identity->username;
                    $table->fecha_registro = date('Y-m-d H:i:s');
                    $table->save(false);
                    $inventario->aplica_descuento_distribuidor = 1;
                    $inventario->save();
                    $this->redirect(["inventario-punto-venta/lista_precios", 'id' => $id, 'token' => $token]);
                }
            }else{
                $model->getErrors();
            }    
        }
        return $this->renderAjax('_form_editar_descuento', [
            'model' => $model,
            'id' => $id,
            'sw' => $sw,
            'token' => $token,
        ]);
    }
    
    //EDITAR LA REGLA COMERCIAL DE DESCUENTO PARA DISTRIBUIDORES
    public function actionEditar_regla_comercial($id, $sw = 1, $token, $id_detalle) {
        $model = new \app\models\ModeloEditarReglaDescuento();
        $table = InventarioPuntoVenta::findOne($id);
        $regla = \app\models\ReglaDescuentoComercial::find()->where(['=','id_regla', $id_detalle])->one();
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                if (isset($_POST["regla_distribuidor"])) {
                   $regla->fecha_inicio =  $model->fecha_inicio;
                   $regla->fecha_final = $model->fecha_final;
                   $regla->nuevo_valor = $model->nuevo_valor;
                   $regla->tipo_descuento = $model->tipo_descuento;
                   $regla->estado_regla = $model->estado;
                   $regla->save(false);
                   if($model->estado == 1){
                        $table->aplica_descuento_distribuidor = 0;
                        $table->save();
                    }else{
                        $table->aplica_descuento_distribuidor = 1;
                        $table->save();
                    }     
                   $this->redirect(["inventario-punto-venta/lista_precios",'id' => $id,'token' => $token]);
                }
            }else{
                $model->getErrors();
            }    
        }
        if (Yii::$app->request->get()) {
            $model->fecha_inicio = $regla->fecha_inicio;
            $model->fecha_final = $regla->fecha_final;
            $model->nuevo_valor = $regla->nuevo_valor;
            $model->tipo_descuento = $regla->tipo_descuento;
            $model->estado = $regla->estado_regla;
        }
        return $this->renderAjax('_form_editar_descuento', [
            'model' => $model,
            'id' => $id,
            'sw' => $sw,
            'token' => $token,
        ]);
    }
    
    //PRODUCTO MAS VENDID
      public function actionProducto_masvendido() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',178])->all()){
                $form = new \app\models\FiltroBusquedaInventarioPunto();
                $cantidad_mostrar = null;
                $listado = null;
                $conPunto = \app\models\PuntoVenta::find()->orderBy('predeterminado DESC')->all();
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $cantidad_mostrar = Html::encode($form->cantidad_mostrar);
                        if($cantidad_mostrar <> null){
                            $query = (new Query())
                                       ->select('inventario_punto_venta.id_inventario, inventario_punto_venta.codigo_producto, inventario_punto_venta.nombre_producto,
                                                SUM(pedidos_detalle.cantidad) AS cantidad, punto_venta.nombre_punto AS punto,
                                                proveedor.nombrecorto AS proveedor')
                                       ->from(' pedidos_detalle, punto_venta, proveedor')
                                       ->innerJoin('inventario_punto_venta')
                                       ->where('pedidos_detalle.id_inventario = inventario_punto_venta.id_inventario')
                                       ->andWhere('inventario_punto_venta.idproveedor = proveedor.idproveedor')
                                       ->andWhere('inventario_punto_venta.id_punto = punto_venta.id_punto')
                                       ->groupBy('inventario_punto_venta.id_inventario')
                                       ->orderBy('SUM(pedidos_detalle.cantidad) DESC')
                                       ->limit($cantidad_mostrar); 
                                       $command = $query->createCommand();
                                       $rows = $command->queryAll();   
                                       $listado = $rows;
                        }else{
                            Yii::$app->getSession()->setFlash('warning', 'Debe de seleccionar la cantidad de registro a mostrar.');
                           
                        }               
                    } else {
                        $form->getErrors();
                    }
                } 
                return $this->render('producto_masvendido', [
                            'listado' => $listado,
                            'form' => $form,
                            'conPunto' => ArrayHelper::map($conPunto, 'id_punto', 'nombre_punto'),
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
    }  
    

    /**
     * Finds the InventarioPuntoVenta model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InventarioPuntoVenta the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InventarioPuntoVenta::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    //PROCESO QUE GENERA LA COMBINACION DE TALLAS Y COLORES
    public function actionGenerar_combinacion_talla_color($id, $token, $codigo) {
        $form = new \app\models\FiltroBusquedaTallas();
        $codigo_talla = null;
        $conColores = null;
        if ($form->load(Yii::$app->request->get())) {
            $codigo_talla = Html::encode($form->codigo_talla);
            if($codigo_talla > 0){
                $model = \app\models\Talla::find()->where(['=','idtalla', $codigo_talla])->one();
                if($codigo == 0){
                    $conColores = \app\models\Color::find()->orderBy('color ASC')->all();
                }else{
                    $conColores = \app\models\DetalleColorTalla::find()->where(['=','id_inventario', $codigo])->andWhere(['=','idtalla', $codigo_talla ])->orderBy('id ASC')->all();
                }
                
            }else{
                Yii::$app->getSession()->setFlash('warning', 'Debe seleccionar la talla de la lista.');
                return $this->redirect(['generar_combinacion_talla_color','id' =>$id, 'token' =>$token, 'codigo' => $codigo]);
            }
            
        }
        if (isset($_POST["enviarcolores"])) {
            if(isset($_POST["nuevo_color"])){
                foreach ($_POST["nuevo_color"] as $intCodigo) {
                    $ConInventario = InventarioPuntoVenta::findOne($id);
                    $table = new \app\models\DetalleColorTalla();
                    $table->id_inventario = $id;
                    $table->codigo_producto = $ConInventario->codigo_producto;
                    $table->id = $intCodigo;
                    $table->id_punto = $ConInventario->id_punto;
                    $table->idtalla = $model->idtalla;
                    $table->fecha_registro = date('Y-m-d H:i:s');
                    $table->user_name = Yii::$app->user->identity->username;
                    $table->save();
                }
                return $this->redirect(['generar_combinacion_talla_color','id' => $id, 'token' => $token, 'conColores' => $conColores,  'model' =>$model, 'codigo'=> $codigo]);
            }
            
        }
        return $this->render('generar_combinacion', [
            'id' => $id,
            'token' => $token,
            'form' => $form, 
            'conColores' => $conColores,
            'codigo' => $codigo,
        ]);
    }
    
     //CERRAR COMBINACIONES
    public function actionCerrar_combinaciones($id, $token, $codigo){
        
        $detalle = \app\models\DetalleColorTalla::find()->where(['=','id_inventario', $id])->andWhere(['=','cerrado', 0])->all();
        $model = InventarioPuntoVenta::findOne($id);
        if($model->stock_unidades <= 0){
             Yii::$app->getSession()->setFlash('error', 'Debe de actualizar las unidade al inventario. Ingrese nuevamente.'); 
            return $this->redirect(["inventario-punto-venta/view", 'id' => $id, 'token' => $token, 'codigo' => $codigo]);  
         }
        if($detalle){
            foreach ($detalle as $detalles):
                if($detalles->cantidad > 0 && $detalles->id != null){
                   $detalles->cerrado = 1;
                   $detalles->save ();
                }else{
                    Yii::$app->getSession()->setFlash('error', 'Debe de ingresar las cantidades de cada talla y el color. Valide de nuevo la información.');
                    return $this->redirect(["view",'id' => $id, 'token' => $token, 'codigo' => $codigo]);
                }
                
            endforeach;
            return $this->redirect(["view",'id' => $id, 'token' => $token, 'codigo' => $codigo]);
        }else{
            Yii::$app->getSession()->setFlash('warning', 'Este proceso ya esta cerrado para las tallas y colores.');
            return $this->redirect(["view",'id' => $id, 'token' => $token, 'codigo' => $codigo]);
        }    
        
    }
    //ENVIAR INVENTARIO POR PRIMERA VEZ A PUNTO DE VENTA (APLICA TALLA)
    public function actionEnviar_referencia_punto($id, $id_punto) {
        Yii::$app->getSession()->setFlash('warning', 'Este proceso solo aplica para empresas que tengan el servicio de puntos de venta.');
        return $this->redirect(["index"]);
            
        $model = InventarioPuntoVenta::findOne($id);
        $talla_color = \app\models\DetalleColorTalla::find()->where(['=','codigo_producto', $model->codigo_producto])
                                                            ->andWhere(['=','id_inventario', $id])->all();
        if(isset($_POST["enviar_referencia"])){
            if(isset($_POST["nuevo_envio_bodega"])){
                $intIndice = 0;
                $cantidad = 0;
                $auxiliar = 0;
                foreach ($_POST["nuevo_envio_bodega"] as $intCodigo):
                    $cantidad = $_POST["cantidades"][$intIndice];
                    if($cantidad > 0){
                        $talla = \app\models\DetalleColorTalla::findOne($intCodigo);
                        if($cantidad <= $talla->stock_punto){
                            if($auxiliar <> $_POST["id_punto_saliente"][$intIndice]){
                                $table = new InventarioPuntoVenta();
                                $table->codigo_producto = $model->codigo_producto;
                                $table->codigo_barra = $model->codigo_producto;
                                $table->nombre_producto = $model->nombre_producto;
                                $table->costo_unitario = $model->costo_unitario;
                                $table->stock_unidades += $cantidad;
                                $table->stock_inventario += $cantidad;
                                $table->id_proveedor = $model->id_proveedor;
                                $table->id_marca = $model->id_marca;
                                $table->id_categoria = $model->id_categoria;
                                $table->iva_incluido = $model->iva_incluido;
                                $table->inventario_inicial = $model->inventario_inicial;
                                $table->aplica_inventario = $model->aplica_inventario;
                                $table->aplica_talla_color = $model->aplica_talla_color;
                                $table->porcentaje_iva = $model->porcentaje_iva;
                                $table->fecha_proceso = date('Y-m-d');
                                $table->user_name = Yii::$app->user->identity->username;
                                $table->venta_publico = $model->venta_publico;
                                $table->codigo_enlace_bodega = $model->id_inventario;
                                $table->id_punto = $_POST["id_punto_saliente"][$intIndice];
                                $table->inventario_aprobado = 1;
                                $table->save();
                                $conId = InventarioPuntoVenta::find()->orderBy('id_inventario DESC')->one();
                                $model->stock_inventario -= $cantidad;  
                                $model->save(false);
                                //creamos cada talla con la combinacion de tallas
                                $insertarTalla = new \app\models\DetalleColorTalla();
                                $insertarTalla->id_inventario = $conId->id_inventario;
                                $insertarTalla->codigo_producto = $model->codigo_producto;
                                $insertarTalla->id_color = $talla->id_color;
                                $insertarTalla->id_talla = $talla->id_talla;
                                $insertarTalla->id_punto = $_POST["id_punto_saliente"][$intIndice];
                                $insertarTalla->cantidad = $cantidad;
                                $insertarTalla->stock_punto =  $cantidad;
                                $insertarTalla->cerrado = 1;
                                $insertarTalla->fecha_registro = date('Y-m-d H:i:s');
                                $insertarTalla->user_name = Yii::$app->user->identity->username;
                                $insertarTalla->save(false);
                                ////*****////
                                //actualizar talla
                                $talla->stock_punto -= $cantidad;
                                $talla->save(false);
                                $auxiliar = $_POST["id_punto_saliente"][$intIndice];
                                $intIndice++; 
                            }else{
                                $model->stock_inventario -= $cantidad;  
                                $model->save(false);
                                //creamos cada talla con la combinacion de tallas
                                $insertarTalla = new \app\models\DetalleColorTalla();
                                $insertarTalla->id_inventario = $conId->id_inventario;
                                $insertarTalla->codigo_producto = $model->codigo_producto;
                                $insertarTalla->id_color = $talla->id_color;
                                $insertarTalla->id_talla = $talla->id_talla;
                                $insertarTalla->id_punto = $_POST["id_punto_saliente"][$intIndice];
                                $insertarTalla->cantidad = $cantidad;
                                $insertarTalla->stock_punto =  $cantidad;
                                $insertarTalla->cerrado = 1;
                                $insertarTalla->fecha_registro = date('Y-m-d H:i:s');
                                $insertarTalla->user_name = Yii::$app->user->identity->username;
                                $insertarTalla->save(false);
                                ////*****////
                                //actualizar talla
                                $talla->stock_punto -= $cantidad;
                                $talla->save(false);
                                $conTallas = \app\models\DetalleColorTalla::find()->where(['=','id_inventario', $conId->id_inventario])->andWhere(['=','id_punto', $_POST["id_punto_saliente"][$intIndice]])->all();
                                $suma =0;
                                foreach ($conTallas as $sumar):
                                    $suma += $sumar->stock_punto;
                                endforeach;
                                //actualizar cantidades en el inventario del punto de venta
                                $invSumar = InventarioPuntoVenta::findOne($conId->id_inventario);
                                $invSumar->stock_inventario = $suma;
                                $invSumar->stock_unidades = $suma;
                                $invSumar->save();
                                $auxiliar = $_POST["id_punto_saliente"][$intIndice];
                                $intIndice++;   
                            }    
                        }else{
                            Yii::$app->getSession()->setFlash('error', 'La cantidad a trasladar no puede ser mayor que el STOCK de la talla.');
                        }    
                    }else{
                        $intIndice++;  
                    }
                endforeach;
            }
       }        
        return $this->render('submit_referencia_punto', [
            'model' => $model,
            'id' => $id,
            'id_punto'=> $id_punto,
            'talla_color' => $talla_color,

            
        ]); 
    }
    
   //importar ordenes de produccion
   public function actionImportar_ordenes_inventario() {
        $operacion = \app\models\Ordenproduccion::find()->where([
                                              'aplica_inventario' => 1,
                                              'cerrar_orden' => 1,
                                              'inventario_exportado' => 0])->orderBy('idordenproduccion DESC')->all();
        $form = new \app\models\FormModeloBuscar();
        $q = null;
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                    $operacion = \app\models\Ordenproduccion::find()
                            ->where(['like','codigoproducto',$q])
                            ->orwhere(['=','idordenproduccion',$q])
                            ->andWhere(['=','cerrar_orden', 1])
                            ->andWhere(['=','aplica_inventario', 1])
                            ->andWhere(['=','inventario_exportado', 0]);
                    $operacion = $operacion->orderBy('idordenproduccion DESC');                    
                    $count = clone $operacion;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 10,
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
            $table = $operacion = \app\models\Ordenproduccion::find()->where([
                                              'aplica_inventario' => 1,
                                              'cerrar_orden' => 1,
                                              'inventario_exportado' => 0])->orderBy('idordenproduccion DESC');
            $tableexcel = $table->all();
            $count = clone $table;
            $pages = new Pagination([
                        'pageSize' => 10,
                        'totalCount' => $count->count(),
            ]);
             $operacion = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
        }
        //PROCESO DE GUARDAR
         if (isset($_POST["enviar_datos"])) {
            if(isset($_POST["nueva_orden"])){
                $intIndice = 0;
                foreach ($_POST["nueva_orden"] as $intCodigo) {
                    $registro = InventarioPuntoVenta::find()->where(['=','idordenproduccion', $intCodigo])->one();
                    $iva = \app\models\ConfiguracionIva::find()->where(['predeterminado' => 1])->one();
                    $conInventario = \app\models\ConfiguracionInventario::findOne(1);
                    if(!$registro){
                        $orden = \app\models\Ordenproduccion::findOne($intCodigo);
                        $proveedor = \app\models\Proveedor::findOne(1);
                        if(!$proveedor){
                            Yii::$app->getSession()->setFlash('error', 'Para poder descargar el inventario que esta la orden de producción, se debe crear el proveedor de esta produccion en el proceso de PROVEEDORES .');
                            return $this->redirect(['index']); 
                        }
                        $detalle = \app\models\Ordenproducciondetalle::find()->where(['idordenproduccion' => $orden->idordenproduccion])->one();
                        if($orden){
                            $inventario = InventarioPuntoVenta::find()->where(['=','codigo_producto', $orden->codigoproducto])->one();
                            if(!$inventario){
                                $table = new InventarioPuntoVenta();
                                $table->codigo_producto = $orden->codigoproducto;
                                $table->codigo_barra = $orden->codigoproducto;
                                $table->nombre_producto = $detalle->productodetalle->prendatipo->prenda;
                                $table->descripcion_producto= $detalle->productodetalle->prendatipo->prenda;
                                $table->costo_unitario = $detalle->vlrprecio;
                                $table->idproveedor = 1;
                                $table->id_punto = 1;
                                if($conInventario->aplica_iva_incluido == 1){
                                    $table->iva_incluido = 1;
                                }else{
                                    $table->iva_incluido = 0;
                                }
                                if($conInventario->aplica_inventario_talla_color == 1 || $conInventario->aplica_inventario_tallas == 1){
                                    $table->aplica_talla_color = 1;
                                }else{
                                    $table->aplica_talla_color = 0;
                                    $table->stock_inventario = $orden->cantidad;
                                    $table->stock_unidades = $orden->cantidad;
                                }
                                $table->aplica_inventario = 1;
                                $table->porcentaje_iva = $iva->valor_iva;
                                $table->fecha_creacion = date('Y-m-d H:i:s');
                                $table->fecha_proceso = date('Y-m-d');
                                $table->venta_publico = 1;
                                $table->idordenproduccion = $intCodigo;
                                $table->user_name = Yii::$app->user->identity->username;
                                $table->save(false);
                                $intIndice++;
                            }else{
                               Yii::$app->getSession()->setFlash('error', 'El codigo del producto: '.$orden->codigoproducto.' Ya esta esta registrado en el sistema. Debe de cargarlo por el detalle.'); 
                            }   
                        }    
                    }    
                }
                Yii::$app->getSession()->setFlash('success', 'Se guardaron: '.$intIndice.' registros con exito en el sistema');
                return $this->redirect(['index']);
            }
        }
        return $this->render('importar_orden_inventario', [
            'operacion' => $operacion,            
            'pagination' => $pages,
            'form' => $form,
        ]);
    }
    
    ///DESCARAGAR TALLAS
    public function actionDescargar_tallas_op($id, $token, $codigo, $idordeproduccion){
        $orden = \app\models\Ordenproduccion::find()->where([
                                                    'idordenproduccion' => $idordeproduccion,
                                                    'inventario_exportado' => 0])->one();
        if($orden){
            $detalle_orden = \app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $idordeproduccion])->all();
            $intIndice = 0;
            foreach ($detalle_orden as $detalle){
               $talla_color = \app\models\DetalleColorTalla::find()->where([
                                                                'id_inventario' => $id,
                                                                'codigo_producto' => $orden->codigoproducto,
                                                                'idtalla' => $detalle->productodetalle->prendatipo->talla->idtalla])->one(); 
                if(!$talla_color){
                    $table = new \app\models\DetalleColorTalla();
                    $table->id_inventario = $id;
                    $table->codigo_producto = $orden->codigoproducto;
                    $table->idtalla = $detalle->productodetalle->prendatipo->talla->idtalla;
                    $table->id_punto = 1;
                    $table->cantidad = $detalle->cantidad;
                    $table->user_name = Yii::$app->user->identity->username;
                    $table->fecha_registro = date('Y-m-d H:i:s');
                    $table->save();
                    $intIndice++;
                }    
            }
            Yii::$app->getSession()->setFlash('success', 'Se guardaron: '.$intIndice.' registros exitosamente.');
            return $this->redirect(['view','id' => $id, 'token' => $token, 'codigo' => $codigo]);
            
        }else{
          Yii::$app->getSession()->setFlash('error', 'Las tallas de esta referencias ya se exportaron al inventario. Valide la informacion.');
          return $this->redirect(['view','id' => $id, 'token' => $token, 'codigo' => $codigo]);
        } 
        
    }
    
    //ELIMNIAR DETALLES
    public function actionEliminar_linea($id, $token, $codigo, $id_detalle){
        if (Yii::$app->request->post()) {
            $talla = \app\models\DetalleColorTalla::findOne($id_detalle);
            if ((int) $id_detalle) {
                try {
                    \app\models\DetalleColorTalla::deleteAll("id_detalle=:id_detalle", [":id_detalle" => $id_detalle]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                    return $this->redirect(['view','id' => $id, 'token' => $token, 'codigo' => $codigo]);
                } catch (IntegrityException $e) {
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro');
                     return $this->redirect(['view','id' => $id, 'token' => $token, 'codigo' => $codigo]);

                } catch (\Exception $e) {

                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro');
                     return $this->redirect(['view','id' => $id, 'token' => $token, 'codigo' => $codigo]);
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el registros, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("inventario-punto-venta/index") . "'>";
            }
        } else {
                   
            return $this->redirect(['view','id' => $id, 'token' => $token, 'codigo' => $codigo]);
        }
    }
    
    //SELECIONAR EL COLOR
     public function actionSeleccionar_color($id, $token, $codigo, $id_detalle) {
        $model = new \app\models\ModeloCambiarIva();
        $colores = \app\models\Color::find()->all();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()){
                $table = \app\models\DetalleColorTalla::findOne($id_detalle);
                $table->id = $model->color;
                $table->save();
                return $this->redirect(['view','id' => $id, 'token' => $token, 'codigo' => $codigo]);
               } 
         }
        return $this->renderAjax('cambio_color', [
            'model' => $model,    
            'colores' => ArrayHelper::map($colores, 'id', 'color'),
        ]);    
    }
    
    ///CREAR LINEA PARA AGREGAR UN NUEVO COLOR
    public function actionCrear_linea_color($id, $token, $codigo, $id_detalle) {
        $linea = \app\models\DetalleColorTalla::findOne($id_detalle);
        if($linea){
            $table = new \app\models\DetalleColorTalla();
            $table->id_inventario = $id;
            $table->codigo_producto = $linea->codigo_producto;
            $table->idtalla = $linea->idtalla;
            $table->id_punto = $linea->id_punto;
            $table->user_name = Yii::$app->user->identity->username;
            $table->fecha_registro = date('Y-m-d H:i:s');
            $table->save();
        }
        return $this->redirect(['view','id' => $id, 'token' => $token, 'codigo' => $codigo]);
        
    }
    
    ////PERMITE CREAR EL COSTO DEL PRODUCTO
     public function actionNuevo_costo_producto($id) {
        $model = new \app\models\FormModeloAsignarPrecioVenta();
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                if (isset($_POST["crear_precio"])) {
                    if($model->nuevo_precio > 0){
                        $table = InventarioPuntoVenta::findOne($id);
                        $table->costo_unitario = $model->nuevo_precio;
                        $table->save(false);
                        return $this->redirect(["index"]);
                    }else{
                        Yii::$app->getSession()->setFlash('warning', 'No se asignó ningun valor de costo. Ingrese nuevamente.'); 
                        return $this->redirect(["index"]);
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
      
    //PROCESO QUE CREA EL NUEVO PRECIO
    public function actionNuevo_precio_venta($id, $token) {
        $model = new \app\models\FormModeloAsignarPrecioVenta();
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                if (isset($_POST["crear_precio"])) {
                    if($model->nuevo_precio > 0){
                        $table = new \app\models\PrecioVentaInventario();
                        $table->id_inventario = $id;
                        $table->valor_venta = $model->nuevo_precio;
                        $table->user_name = Yii::$app->user->identity->username;
                        $table->save(false);
                        return $this->redirect(["inventario-punto-venta/lista_precios", 'id' => $id, 'token' => $token]);
                    }else{
                        Yii::$app->getSession()->setFlash('warning', 'No se asignó ningun precio de venta a público. Ingrese nuevamente.'); 
                        return $this->redirect(["inventario-punto-venta/lista_precios", 'id' => $id,'token' => $token]);
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
    
    //cerrar orden
    public function actionCerrar_orden_produccion($id,$token, $codigo) {
         $detalle = \app\models\DetalleColorTalla::find()->where(['id_inventario' => $id])->one(); 
         $model = InventarioPuntoVenta::findOne($id);
         $orden = \app\models\Ordenproduccion::findOne($model->idordenproduccion);
         if(!$detalle){
             Yii::$app->getSession()->setFlash('warning', 'Debe de cargar las tallas y colores a la referencia. Ingrese nuevamente.'); 
            return $this->redirect(["inventario-punto-venta/view", 'id' => $id, 'token' => $token, 'codigo' => $codigo]); 
         }
         //preguntamos si hizo la actualizacion de unidades
         if($model->stock_unidades <= 0){
             Yii::$app->getSession()->setFlash('error', 'Debe de actualizar las unidade al inventario. Ingrese nuevamente.'); 
            return $this->redirect(["inventario-punto-venta/view", 'id' => $id, 'token' => $token, 'codigo' => $codigo]);  
         }
         $orden->inventario_exportado = 1;
         $orden->save();
         return $this->redirect(["inventario-punto-venta/view", 'id' => $id, 'token' => $token, 'codigo' => $codigo]); 
         
    }
    
    //EXCELES
    public function actionExcelInventarioPuntoVenta($tableexcel) {                
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
        
                                     
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'CODIGO')
                    ->setCellValue('C1', 'PRODUCTO')
                    ->setCellValue('D1', 'FECHA PROCESO')
                    ->setCellValue('E1', 'FECHA CREACION')
                    ->setCellValue('F1', 'APLICA INVENTARIO')
                    ->setCellValue('G1', 'INVENTARIO INICIAL')
                    ->setCellValue('H1', 'UNIDADES ENTRADAS')
                    ->setCellValue('I1', 'STOCK')
                    ->setCellValue('J1', 'MINIMO STOCK')
                    ->setCellValue('K1', 'VALOR UNITARIO')
                    ->setCellValue('L1', 'SUBTOTAL')
                    ->setCellValue('M1', 'IMPUESTO')
                    ->setCellValue('N1', 'VALOR TOTAL')
                    ->setCellValue('O1', 'USER NAME')
                    ->setCellValue('P1', 'CODIGO EAN')
                    ->setCellValue('Q1', 'MARCA')
                    ->setCellValue('R1', 'CATEGORIA')
                    ->setCellValue('S1', 'PRECIO DEPTAL')
                    ->setCellValue('T1', 'PRECIO MAYORISTA')
                    ->setCellValue('U1', 'APLICA DESCTO PUNTO')
                    ->setCellValue('V1', 'APLICA DESCTO MAYORISTA')
                    ->setCellValue('W1', 'COLOR')
                    ->setCellValue('X1', 'TALLA')
                    ->setCellValue('Y1', 'STOCK X TALLA')
                    ->setCellValue('Z1', 'BODEGA/PUNTO');
            $i = 2;
        
        foreach ($tableexcel as $val) {
            $detalle = \app\models\DetalleColorTalla::find()->where([
                                                            'id_inventario' => $val->id_inventario,     
                                                            ])->all();
            foreach ($detalle as $detalles){                      
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $val->id_inventario)
                        ->setCellValue('B' . $i, $val->codigo_producto)
                        ->setCellValue('C' . $i, $val->nombre_producto)
                        ->setCellValue('D' . $i, $val->fecha_proceso)
                        ->setCellValue('E' . $i, $val->fecha_creacion)
                        ->setCellValue('F' . $i, $val->aplicaInventario)
                        ->setCellValue('G' . $i, $val->inventarioInicial)
                        ->setCellValue('H' . $i, $val->stock_unidades)
                        ->setCellValue('I' . $i, $val->stock_inventario)
                        ->setCellValue('J' . $i, $val->stock_minimo)
                        ->setCellValue('K' . $i, $val->costo_unitario)
                        ->setCellValue('L' . $i, $val->subtotal)
                        ->setCellValue('M' . $i, $val->valor_iva)
                        ->setCellValue('N' . $i, $val->total_inventario)
                        ->setCellValue('O' . $i, $val->user_name)
                        ->setCellValue('P' . $i, $val->codigo_barra)
                        ->setCellValue('Q' . $i, $val->marca->marca)
                        ->setCellValue('R' . $i, $val->categoria->categoria)
                        ->setCellValue('S' . $i, $val->precio_deptal)
                        ->setCellValue('T' . $i, $val->precio_mayorista)
                        ->setCellValue('U' . $i, $val->aplicaDescuentoPunto)
                        ->setCellValue('V' . $i, $val->aplicaDescuentoDistribuidor)
                        ->setCellValue('W' . $i, $detalles->color->color)
                        ->setCellValue('X' . $i, $detalles->talla->talla)
                        ->setCellValue('Y' . $i, $detalles->stock_punto)
                        ->setCellValue('Z' . $i, $detalles->punto->nombre_punto);
                       
                        
                        ;
                $i++;
            }
            $i = $i;
        }

        $objPHPExcel->getActiveSheet()->setTitle('inventario');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Inventario_productos.xlsx"');
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
