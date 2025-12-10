<?php

namespace app\controllers;



use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\Html;
use yii\data\Pagination;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

//models
use app\models\Pedidos;
use app\models\UsuarioDetalle;

/**
 * PedidosController implements the CRUD actions for Pedidos model.
 */
class PedidosController extends Controller
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
     * Lists all Pedidos models.
     * @return mixed
     */
     public function actionIndex($token = 0) {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 176])->all()) {
                $form = new \app\models\FormFiltroPedido();
                $numero = null;
                $cliente = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $vendedor = null;
                $entregado = null;
                $TokenAcceso = Yii::$app->user->identity->id_agente;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $numero = Html::encode($form->numero);
                        $cliente = Html::encode($form->cliente);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $vendedor = Html::encode($form->vendedor);
                        $entregado = Html::encode($form->entregado);
                         if($TokenAcceso){
                            $table = Pedidos::find()
                                ->andFilterWhere(['=', 'numero_pedido', $numero])
                                ->andFilterWhere(['=', 'idcliente', $cliente])
                                ->andFilterWhere(['=', 'pedido_despachado', $entregado])    
                                ->andFilterWhere(['between', 'fecha_pedido', $fecha_inicio, $fecha_corte])
                                ->andWhere(['=', 'id_agente', $TokenAcceso]); 
                         }else{
                             $table = Pedidos::find()
                                ->andFilterWhere(['=', 'numero_pedido', $numero])
                                ->andFilterWhere(['=', 'idcliente', $cliente])
                                ->andFilterWhere(['=', 'id_agente', $vendedor])   
                                ->andFilterWhere(['=', 'pedido_despachado', $entregado])     
                                ->andFilterWhere(['between', 'fecha_pedido', $fecha_inicio, $fecha_corte]);
                         }
                        
                        $table = $table->orderBy('id_pedido DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 15,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_pedido DESC']);
                            $this->actionExcelConsultaPedidos($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                   
                    if($TokenAcceso){
                        $table = Pedidos::find()->where(['id_agente' => $TokenAcceso])
                                 ->orderBy('id_pedido DESC');
                    }else{
                        $table = Pedidos::find()->orderBy('id_pedido DESC');
                    }    
                        
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 15,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelConsultaPedidos($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
                            'TokenAcceso' => $TokenAcceso,
                            'token' => $token,  
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }
    
    //CONSULTA DE PEDIDOS
     public function actionSearch_pedidos($token = 1) {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 180])->all()) {
                $form = new \app\models\FormFiltroPedido();
                $numero = null;
                $cliente = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $vendedor = null;
                $entregado = null;
                $TokenAcceso = Yii::$app->user->identity->id_agente;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $numero = Html::encode($form->numero);
                        $cliente = Html::encode($form->cliente);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $vendedor = Html::encode($form->vendedor);
                        $entregado = Html::encode($form->entregado);
                         if($TokenAcceso){
                            $table = Pedidos::find()
                                ->andFilterWhere(['=', 'numero_pedido', $numero])
                                ->andFilterWhere(['=', 'idcliente', $cliente])
                                ->andFilterWhere(['=', 'pedido_despachado', $entregado])     
                                ->andFilterWhere(['between', 'fecha_pedido', $fecha_inicio, $fecha_corte])
                                ->andWhere(['=', 'id_agente', $TokenAcceso]); 
                         }else{
                             $table = Pedidos::find()
                                ->andFilterWhere(['=', 'numero_pedido', $numero])
                                ->andFilterWhere(['=', 'idcliente', $cliente])
                                 ->andFilterWhere(['=', 'pedido_despachado', $entregado])     
                                 ->andFilterWhere(['=', 'id_agente', $vendedor])     
                                ->andFilterWhere(['between', 'fecha_pedido', $fecha_inicio, $fecha_corte]);
                         }
                        
                        $table = $table->orderBy('id_pedido DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 15,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_pedido DESC']);
                            $this->actionExcelConsultaPedidos($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                   
                    if($TokenAcceso){
                        $table = Pedidos::find()->where(['id_agente' => $TokenAcceso])
                                 ->orderBy('id_pedido DESC');
                    }else{
                        $table = Pedidos::find()->where(['autorizado' => 1])->orderBy('id_pedido DESC');
                    }    
                        
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 15,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelConsultaPedidos($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index_search', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
                            'TokenAcceso' => $TokenAcceso,
                            'token' => $token,
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }

    /**
     * Displays a single Pedidos model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token,)
    {
        $referencias = \app\models\PedidosDetalle::find()->where(['=','id_pedido', $id])->orderBy('id_detalle DESC')->all();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'referencias' => $referencias,
            'token' => $token,
        ]);
    }
    
    //PERMITE VER LAS TALLAS DE LA REFERENCIA
    public function actionVer_tallas_colores($id, $id_detalle, $token) {
        
        $tallas = \app\models\PedidoTallas::find()->where(['=','id_detalle', $id_detalle])->all();
        $ConColores = \app\models\PedidoColores::find()->where(['=','id_detalle', $id_detalle])->orderBy('idtalla ASC')->all();
        $model = \app\models\PedidosDetalle::findOne($id_detalle);
        $detalleColores = \app\models\DetalleColorTalla::find()->where(['=','id_inventario', $model->id_inventario])->orderBy('id ASC')->all();
        
        $coloresTallasFormateado = [];
        foreach ($detalleColores as $detalle) {
            $idTalla = $detalle->idtalla; // Asume que el modelo tiene el campo id_talla
            $idColor = $detalle->id; // Asume que el modelo tiene el campo id_color

            // Si el ID de talla no existe como clave, lo inicializa como un array.
            if (!isset($coloresTallasFormateado[$idTalla])) {
                $coloresTallasFormateado[$idTalla] = [];
            }
            // Agrega el ID del color al array de esa talla.
            $coloresTallasFormateado[$idTalla][] = (string)$idColor; 
            // Se convierte a string porque los valores de los select múltiples suelen ser strings.
        }
    
        // Asignamos el array formateado a la variable que se pasa a la vista
        $coloresTallas = $coloresTallasFormateado;
        
        if (isset($_POST["actualizar_cantidades"])) {
         // 3. Procesar los colores seleccionados del Select2
            $coloresPorTalla = \Yii::$app->request->post('colores_por_talla');
            $intIndice = 0;
            foreach ($_POST["listado_tallas"] as $intCodigo) {
                $contar = $_POST["cantidad"][$intIndice];
            
                // Suponiendo que $intCodigo es el ID del registro en PedidoTallas
                $table = \app\models\PedidoTallas::findOne($intCodigo); 
                $idTallaActual = $table->idtalla; // Obtener el ID de la Talla

                if ($contar > 0) {
                    // Actualizar cantidad (como lo tenías)
                    $table->cantidad = $contar;
                    $table->save();
                    // Totalizaciones
                    $this->TotalizarCantidadesTallas($id_detalle);
                    $this->TotalizarCantidades($id_detalle);
                    $this->TotalizarCantidadesPedidos($id);
                    // --- Lógica para guardar los colores ---
                    if (isset($coloresPorTalla[$idTallaActual])) {
                        $coloresSeleccionados = $coloresPorTalla[$idTallaActual];
                        // 1. Eliminar los registros de colores anteriores para esta talla e inventario
                     /*   \app\models\PedidoColores::deleteAll([
                            'id_detalle' => $id_detalle, 
                            'id_pedido' => $id
                        ]);*/

                        // 2. Insertar los nuevos colores seleccionados
                        foreach ($coloresSeleccionados as $idColor) {
                            $registro = \app\models\PedidoColores::find()->where([
                                                                    'id_detalle' => $id_detalle,
                                                                    'id_pedido' => $id,
                                                                    'id' => $idColor, 
                                                                    'idtalla' => $idTallaActual, 
                            ])->one();
                            if(!$registro){
                                $newColorTalla = new \app\models\PedidoColores();
                                $newColorTalla->id_detalle = $model->id_detalle;
                                $newColorTalla->idtalla = $idTallaActual;
                                $newColorTalla->id = $idColor;
                                $newColorTalla->id_pedido = $id;
                                $newColorTalla->codigo = $intCodigo;
                                $newColorTalla->save(); 
                            }    
                            // Aquí podrías agregar lógica para manejar la cantidad por color si fuera necesario.
                        }
                    }

                    
                }
            $intIndice++;
        }
        return $this->redirect(['pedidos/ver_tallas_colores', 'id' => $id, 'id_detalle' => $id_detalle, 'token' => $token]);
    }
         return $this->render('ver_talla_color', [
            'id' => $id,
            'tallas' => $tallas,
            'model' => $model,
            'id_detalle' => $id_detalle,
            'coloresTallas' => $coloresTallas,
            'ConColores' => $ConColores,
             'token' => $token,
        ]);
    }
            

    /**
     * Creates a new Pedidos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Pedidos();
        $TokenAcceso = Yii::$app->user->identity->id_agente;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->user_name = Yii::$app->user->identity->username;
            $model->fecha_pedido = date('Y-m-d');
            $model->fecha_proceso = date('Y-m-d H:i:s');
            if($TokenAcceso){
                $model->id_agente = $TokenAcceso;
            }
            $model->save();
            return $this->redirect(['view', 'id' => $model->id_pedido,'token' => 0]);
        }
        
        return $this->render('create', [
            'model' => $model,
            'TokenAcceso' => $TokenAcceso,
        ]);
    }

    /**
     * Updates an existing Pedidos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $TokenAcceso = Yii::$app->user->identity->id_agente;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_pedido, 'token' => 0]);
        }

        return $this->render('update', [
            'model' => $model,
            'TokenAcceso' => $TokenAcceso,
        ]);
    }

   
    
    //AGREGAR REFERENCIAS AL PEDIDO
     public function actionNueva_referencia_pedido($id,$token,)
    {
        $referencia = \app\models\InventarioPuntoVenta::find()->orderBy('nombre_producto ASC')->all();
        $form = new \app\models\FormMaquinaBuscar();
        $q = null;
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                    $referencia = \app\models\InventarioPuntoVenta::find()
                            ->where(['like','nombre_producto',$q])
                            ->orwhere(['=','codigo_producto',$q]);
                    $referencia = $referencia->orderBy('nombre_producto ASC');                    
                    $count = clone $referencia;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 10,
                        'totalCount' => $count->count()
                    ]);
                    $referencia = $referencia
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();         
                           
            } else {
                $form->getErrors();
            }                    
        }else{
            $referencia = \app\models\InventarioPuntoVenta::find()->orderBy('nombre_producto ASC');
            $count = clone $referencia;
            $pages = new Pagination([
                'pageSize' => 10,
                'totalCount' => $count->count(),
            ]);
            $referencia = $referencia
                    ->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();
        }
         if (isset($_POST["enviar_referencias"])) {
            if(isset($_POST["codigo_referencia"])){
                $intIndice = 0;
                foreach ($_POST["codigo_referencia"] as $intCodigo) {
                    $inventario = \app\models\InventarioPuntoVenta::findOne($intCodigo);
                    if($inventario){
                        $precio_venta = \app\models\PrecioVentaInventario::find()->where(['=','id_inventario', $inventario->id_inventario])->andWhere(['=','predeterminado', 1])->one();
                        if ($precio_venta){
                            $registro = \app\models\PedidosDetalle::find()->where(['=','id_pedido', $id])->andWhere(['=','id_inventario', $intCodigo])->one();
                            if(!$registro){
                                $regla_descuento = \app\models\ReglaDescuentoComercial::find()->where(['=','id_inventario', $intCodigo])->andWhere(['=','estado_regla', 0])->one();
                                if($regla_descuento){
                                    if($regla_descuento->fecha_inicio >= date('Y-m-d') && $regla_descuento->fecha_final <= date('Y-m-d')){
                                        $table = new \app\models\PedidosDetalle();
                                        $table->id_pedido = $id;
                                        $table->id_inventario = $intCodigo;
                                        $table->valor_unitario = $precio_venta->valor_venta;
                                        $table->porcentaje_descuento = $regla_descuento->nuevo_valor;
                                        $table->tipo_descuento = $regla_descuento->tipo_descuento;
                                        $table->user_name= Yii::$app->user->identity->username;
                                        $table->save(false);
                                    }else{
                                        $table = new \app\models\PedidosDetalle();
                                        $table->id_pedido = $id;
                                        $table->id_inventario = $intCodigo;
                                        $table->valor_unitario = $precio_venta->valor_venta;
                                        $table->porcentaje_descuento = $regla_descuento->nuevo_valor;
                                        $table->tipo_descuento = $regla_descuento->tipo_descuento;
                                        $table->user_name= Yii::$app->user->identity->username;
                                        $table->save(false);
                                    }
                                    
                                }else{
                                    $table = new \app\models\PedidosDetalle();
                                    $table->id_pedido = $id;
                                    $table->id_inventario = $intCodigo;
                                    $table->valor_unitario = $precio_venta->valor_venta;
                                    $table->user_name= Yii::$app->user->identity->username;
                                    $table->save(false);
                                }
                            }    
                        }    
                    }    
                    $intIndice++;
                }
                return $this->redirect(["pedidos/view", 'id' => $id, 'token' => $token]);
            }else{
                 Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro de las referencias.');
                return $this->redirect(["nueva_referencia_pedido", 'id' => $id, 'token' => $token]);
            }
        }
        return $this->render('_form_nueva_referencia_producto', [
            'referencia' => $referencia,            
            'pagination' => $pages,
            'id' => $id,
            'form' => $form,
            'token' => $token,
        ]);
    }
    
    //ADICIONAR LAS TALLAS Y LOS COLORES
    public function actionCrear_tallas_referencia($id, $id_detalle, $token,) {
        
        $detalle_pedido = \app\models\PedidosDetalle::findOne($id_detalle);
        $tallas = \app\models\DetalleColorTalla::find()->where(['=','id_inventario', $detalle_pedido->id_inventario])->orderBy('idtalla ASC')->all();
       
       if (isset($_POST["enviar_referencias"])) {
            if(isset($_POST["tallas_seleccionadas"])){
                $intIndice = 0;
                foreach ($_POST["tallas_seleccionadas"] as $intCodigo) {
                    $tallas = \app\models\PedidoTallas::find()->where(['=','id_pedido', $id])->andWhere(['=','idtalla', $intCodigo])->one();
                    if (!$tallas){
                        $table = new \app\models\PedidoTallas();
                        $table->id_detalle = $id_detalle;
                        $table->idtalla = $intCodigo;
                        $table->id_pedido = $id;
                        $table->save(false);
                        $intIndice++;
                    }    
                }
                Yii::$app->getSession()->setFlash('success', 'Se guardaron: ' . $intIndice .' registros exitosamente.');
                return $this->redirect(["pedidos/view", 'id' => $id, 'token' => $token]);
            }else{
                 Yii::$app->getSession()->setFlash('warning', 'Debe seleccionar al menos un registro de las referencias.');
                return $this->redirect(["nueva_referencia_pedido", 'id' => $id, 'token' => $token]);
            }
        }
        
        return $this->render('_form_crear_tallas_referencias', [
                'tallas' => $tallas, 
                'id' => $id,
              
                'id_detalle' => $id_detalle,
                'token' => $token,
              
            ]);
    }
    
    //PROCESO QUE TOTALIZA CANTIDADES DEL DETALLA PEDIDO
    protected function TotalizarCantidades($id_detalle) {
    
        $table = \app\models\PedidosDetalle::findOne($id_detalle);
        if($table->tipo_descuento != null){
            $total_descuento = 0;
            $saldo = 0;
            if($table->tipo_descuento == 1){
                $total_descuento = round($table->valor_unitario * $table->porcentaje_descuento)/100;
                $table->valor_descuento = $total_descuento * $table->cantidad;
                $saldo = $table->valor_unitario - $total_descuento; 
                $table->total_linea = round($saldo * $table->cantidad);
            }else{
                $total_descuento = $table->porcentaje_descuento;
                $table->valor_descuento = $total_descuento * $table->cantidad;
                $saldo = $table->valor_unitario - $total_descuento; 
                $table->total_linea = round($saldo * $table->cantidad);
            }
        }else{
            $table->total_linea = round($table->cantidad * $table->valor_unitario);
        }
        
        $table->save(false);
    }
    
    //PROCESO QUE TOTALIZA CANTIDADES DE LAS TALLAS DEL PEDIDO
    protected function TotalizarCantidadesTallas($id_detalle) {
        $buscar = \app\models\PedidoTallas::find()->where(['=','id_detalle', $id_detalle])->all();
        $table = \app\models\PedidosDetalle::findOne($id_detalle);
        $total = 0;
        foreach ($buscar as $val) {
            $total += $val->cantidad;
        }
        $table->cantidad = $total;
        $table->unidades_faltantes = $total;
        $table->save();
    }
    
     //PROCESO QUE TOTALIZA CANTIDADES TODAS LAS CANTIDADES DEL PEDIDO
    protected function TotalizarCantidadesPedidos($id) {
        $buscar = \app\models\PedidosDetalle::find()->where(['=','id_pedido', $id])->all();
        $table = \app\models\Pedidos::findOne($id);
        $total = 0; $cantidades = 0;
        foreach ($buscar as $val) {
            $total += $val->total_linea;
            $cantidades += $val->cantidad;
        }
        $table->total_unidades = $cantidades;
        $table->total_pedido = $total;
        $table->save();
    }
    
    //SUBPROCESO QUE CARGA LOS COLORES DE CADA TALLA
    public function actionSeleccion_talla_color($idTalla, $idInventario)
    {
        // 1. Filtrar los colores usando AMBOS IDs
        $rows = \app\models\DetalleColorTalla::find()
            ->where(['idtalla' => $idTalla])
            ->andWhere(['id_inventario' => $idInventario]) // <-- NUEVO FILTRO
            ->all();

        // 2. Imprimir la opción por defecto (prompt)
        echo "<option value=''>Seleccione el color...</option>"; 

        // 3. Generar las opciones de color
        if (count($rows) > 0) {
            foreach ($rows as $row) {

                // Usar el ID del color (asumiendo que es idcolor)
                $colorId = $row->id; 

                // Usar el nombre del color
                $colorNombre = $row->color->color; 

                // Devolver el ID del color en el value y el nombre del color como texto visible
                echo "<option value='{$colorId}' required>{$colorNombre}</option>";
            }
        }
    }
    
    //ELIMINAR COLORES
    public function actionEliminar_colores($id, $id_detalle, $dato_eliminar, $token)
    {
        $dato = \app\models\PedidoColores::findOne($dato_eliminar);
        $dato->delete();
       return $this->redirect(['pedidos/ver_tallas_colores', 'id' => $id, 'id_detalle' => $id_detalle, 'token' => $token]);
    }
    
    //ELIMIAR LAS TALLAS DEL PEDIDO
    public function actionEliminar_tallas($id, $id_detalle, $dato_eliminar, $token) {
        try {
           $dato = \app\models\PedidoTallas::findOne($dato_eliminar);
           $dato->delete();
           Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
           return $this->redirect(['pedidos/ver_tallas_colores', 'id' => $id, 'id_detalle' => $id_detalle, 'token' => $token]);
        } catch (IntegrityException $e) {
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el registro, tiene colores asociados al proceso'); 
            return $this->redirect(['pedidos/ver_tallas_colores', 'id' => $id, 'id_detalle' => $id_detalle, 'token' => $token]);
            
        } catch (\Exception $e) {
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el registro, tiene colores asociados al proceso'); 
            return $this->redirect(['pedidos/ver_tallas_colores', 'id' => $id, 'id_detalle' => $id_detalle, 'token' => $token]);
        }
       
       return $this->redirect(['pedidos/ver_tallas_colores', 'id' => $id, 'id_detalle' => $id_detalle, 'token' => $token]);
    }
    
    //ELIMIAR LAS TALLAS DEL PEDIDO
    public function actionEliminar_referencias($id, $id_detalle, $token) {
        try {
           $dato = \app\models\PedidosDetalle::findOne($id_detalle);
           $dato->delete();
           Yii::$app->getSession()->setFlash('success', 'Registro Eliminado exitosamente.');
           return $this->redirect(['pedidos/view', 'id' => $id, 'id_detalle' => $id_detalle,'token' => $token]);
        } catch (IntegrityException $e) {
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el registro, tiene tallas y colores asociados'); 
            return $this->redirect(['pedidos/view', 'id' => $id, 'id_detalle' => $id_detalle, 'token' => $token]);
            
        } catch (\Exception $e) {
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el registro, tiene tallas y colores asociados'); 
            return $this->redirect(['pedidos/view', 'id' => $id, 'id_detalle' => $id_detalle, 'token' => $token]);
        }
       
       return $this->redirect(['pedidos/view', 'id' => $id, 'id_detalle' => $id_detalle, 'token' => $token]);
    }
    
    //PROCESO QYE SE AUTORIZA
    public function actionAutorizado($id, $token,) {
        $detalle_pedido = \app\models\PedidosDetalle::find()->where(['=','id_pedido', $id])->andWhere(['>','cantidad', 0])->one();
        if(!$detalle_pedido){
            Yii::$app->getSession()->setFlash('error','Debe de ingresar las referencias y las cantidades por talla para poder autorizar el pedido. ');
            return $this->redirect(['pedidos/view', 'id' => $id, 'token' => $token]);
        }
        $model = Pedidos::findOne($id);
        if($model->autorizado == 0){
            $model->autorizado = 1;
            $model->save();
            return $this->redirect(['pedidos/view', 'id' => $id, 'token' => $token]);
        }else{
            $model->autorizado = 0;
            $model->save();
            return $this->redirect(['pedidos/view', 'id' => $id, 'token' => $token]);
        }
    }
    
    //PROCESO QUE CIERRA EL PEDIDO
    public function actionCerrar_pedido($id, $token) {
        $model = Pedidos::findOne($id);
         //generar consecutivo
        $registro = \app\models\Consecutivo::findOne(26);
        $valor = $registro->consecutivo + 1;
        $model->numero_pedido = $valor;
        $model->pedido_cerrado = 1;
        $model->save();
        //actualiza consecutivo
        $registro->consecutivo = $valor;
        $registro->save();
        $this->CalcularImpuestoTotalPedido($id);
        return $this->redirect(['pedidos/view', 'id' => $id, 'token' => $token]); 
    }
    
    protected function CalcularImpuestoTotalPedido($id) {
   
        $detallePedido = \app\models\PedidosDetalle::find()->where(['=','id_pedido', $id])->all();
        $conPedido = \app\models\Pedidos::findOne($id); 
        $conInventario = \app\models\ConfiguracionInventario::findOne(1);
        $conIva = \app\models\ConfiguracionIva::find()->where(['=','predeterminado', 1])->one();
    
        $valor_pedido = 0;
        $subtotal = 0;
        $total = 0;
        $iva = 0;
        $porcentaje = 0;
    
        // 4. Calcular subtotal
        foreach ($detallePedido as $detalle){
            $valor_pedido += $detalle->total_linea;
        }
    
        // 5. Aplicar una verificación de seguridad
        if ($conPedido === null) {
            // Manejar el caso donde el pedido no se encuentra (opcional pero recomendado)
            \Yii::error("Pedido con ID {$id} no encontrado para actualización de totales.");
            return false; 
        }
    
        // 6. Calcular IVA y Total (Asegúrate de que $conIva no sea null antes de acceder a sus propiedades)
        if ($conIva !== null) {
            $porcentaje = ($conIva->valor_iva / 100);
        } else {
            // Manejar el caso donde la configuración de IVA predeterminada no se encuentra
            \Yii::warning("Configuración de IVA predeterminada no encontrada. Usando 0% de IVA.");
            $porcentaje = 0;
        }
        if ($conInventario->aplica_iva_incluido == 1){
           $iva = round($valor_pedido * $porcentaje); 
           $subtotal = $valor_pedido - $iva ;
           $total = $valor_pedido;
        }else{
            $iva = round($valor_pedido * $conIva->valor_iva)/100;
            $subtotal = $valor_pedido;
            $total = $subtotal + $iva;
        }
    
        // 7. Actualizar campos
        $conPedido->valor_total = $subtotal; // Base imponible o Subtotal (sin IVA)
        $conPedido->impuesto = $iva;
        $conPedido->total_pedido =  $total; // Total con IVA
        $conPedido->save();
    }
    
    //pedidos
    public function actionImprimir_pedido($id)
    {
        return $this->render('../formatos/reporte_pedido_cliente_inventario', [
            'model' => $this->findModel($id),
            
        ]);
    }
    //TALLAS Y COLORES
    public function actionImprimir_tallas($id)
    {
        return $this->render('../formatos/reporte_pedido_tallas_inventario', [
            'model' => $this->findModel($id),
            
        ]);
    }

    /**
     * Finds the Pedidos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pedidos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pedidos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    //EXCELES
     
    //PERMITE EXPORTAR LAS TALLAS
    public function actionExcel_tallas_pedido($id, $id_detalle) {   
        $objPHPExcel = new \PHPExcel();
        $detalle_tallas = \app\models\PedidoTallas::find()->where(['=','id_detalle', $id_detalle])->all();
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

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'CODIGO')
                    ->setCellValue('B1', 'No PEDIDO')
                    ->setCellValue('C1', 'TALLA')
                    ->setCellValue('D1', 'REFERENCIA')
                    ->setCellValue('E1', 'FECHA PEDIDO')
                    ->setCellValue('F1', 'FECHA ENTREGA')
                    ->setCellValue('G1', 'CLIENTE')
                    ->setCellValue('H1', 'TOTAL UNIDADES')
                    ->setCellValue('I1', 'VR.UNITARIO')
                    ->setCellValue('J1', 'COLOR');
                  
        $i = 2;
        
        foreach ($detalle_tallas as $val) {
            $colores = \app\models\PedidoColores::find()->where(['=','id_detalle', $val->id_detalle])->andWhere(['=','codigo', $val->codigo])->all();
            foreach ($colores as $color){
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $val->detalle->inventario->codigo_producto)
                        ->setCellValue('B' . $i, $val->pedido->numero_pedido)
                        ->setCellValue('C' . $i, $val->talla->talla)
                        ->setCellValue('D' . $i, $val->detalle->inventario->nombre_producto)
                        ->setCellValue('E' . $i, $val->pedido->fecha_pedido)
                        ->setCellValue('F' . $i, $val->pedido->fecha_entrega)
                        ->setCellValue('G' . $i, $val->pedido->cliente->nombrecorto)
                        ->setCellValue('H' . $i, $val->cantidad)
                        ->setCellValue('I' . $i, $val->detalle->valor_unitario)
                        ->setCellValue('J' . $i, $color->colores->color);

                $i++;
            }
            $i = $i;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Listado_tallas.xlsx"');
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
    
    //PERMITE EXPORTAR A EXCEL LOS PEDIDOS
    public function actionExcelConsultaPedidos($tableexcel) {                
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

            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', 'ID')
                        ->setCellValue('B1', 'No PEDIDO')
                        ->setCellValue('C1', 'DOCUMENTO')
                        ->setCellValue('D1', 'CLIENTE')
                        ->setCellValue('E1', 'FECHA PEDIDO')
                        ->setCellValue('F1', 'FECHA ENTREGA')
                        ->setCellValue('G1', 'CANTIDAD')
                        ->setCellValue('H1', 'SUBTOTAL')
                        ->setCellValue('I1', 'IVA')
                        ->setCellValue('J1', 'TOTAL')
                        ->setCellValue('K1', 'VENDEDOR')    
                        ->setCellValue('L1', 'USER NAME')
                        ->setCellValue('M1', 'AUTORIZADO')
                        ->setCellValue('N1', 'CERRADO')
                        ->setCellValue('O1', 'FACTURADO');
                       
            $i = 2;

            foreach ($tableexcel as $val) {

                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $val->id_pedido)
                        ->setCellValue('B' . $i, $val->numero_pedido)
                        ->setCellValue('C' . $i, $val->cliente->cedulanit)
                        ->setCellValue('D' . $i, $val->cliente->nombrecorto)
                        ->setCellValue('E' . $i, $val->fecha_proceso)
                        ->setCellValue('F' . $i, $val->fecha_entrega)
                        ->setCellValue('G' . $i, $val->total_unidades)
                        ->setCellValue('H' . $i, $val->valor_total)
                        ->setCellValue('I' . $i, $val->impuesto)
                        ->setCellValue('J' . $i, $val->total_pedido)
                        ->setCellValue('K' . $i, $val->cliente->agente->nombre_completo ?? 'NOT FOUND')
                        ->setCellValue('L' . $i, $val->user_name)
                        ->setCellValue('M' . $i, $val->autorizadoPedido)
                        ->setCellValue('N' . $i, $val->pedidoCerrado)
                        ->setCellValue('O' . $i, $val->pedidoFacturado);
                $i++;
            }

            $objPHPExcel->getActiveSheet()->setTitle('Listado');
            $objPHPExcel->setActiveSheetIndex(0);

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Pedidos.xlsx"');
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
