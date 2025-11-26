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
use app\models\DespachoPedidos;
use app\models\UsuarioDetalle;


/**
 * DespachoPedidosController implements the CRUD actions for DespachoPedidos model.
 */
class DespachoPedidosController extends Controller
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
     * Lists all DespachoPedidos models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 179])->all()) {
                $form = new \app\models\FormFiltroDespacho();
                $pedido = null;
                $cliente = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $numero_despacho = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $pedido = Html::encode($form->pedido);
                        $cliente = Html::encode($form->cliente);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $numero_despacho = Html::encode($form->numero_despacho);
                         
                        $table = DespachoPedidos::find()
                            ->andFilterWhere(['=', 'numero_pedido', $pedido])
                            ->andFilterWhere(['=', 'idcliente', $cliente])
                            ->andFilterWhere(['=', 'numero_despacho', $numero_despacho])
                            ->andFilterWhere(['between', 'fecha_pedido', $fecha_inicio, $fecha_corte]);
                               
                        
                        $table = $table->orderBy('id_despacho DESC');
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
                    $table = DespachoPedidos::find()->orderBy('id_despacho DESC');
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
                           
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }

    //CONSULTA DE DESPACHOS
     public function actionSearch_index() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 181])->all()) {
                $form = new \app\models\FormFiltroDespacho();
                $pedido = null;
                $cliente = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $numero_despacho = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $pedido = Html::encode($form->pedido);
                        $cliente = Html::encode($form->cliente);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $numero_despacho = Html::encode($form->numero_despacho);
                         
                        $table = DespachoPedidos::find()
                            ->andFilterWhere(['=', 'numero_pedido', $pedido])
                            ->andFilterWhere(['=', 'idcliente', $cliente])
                            ->andFilterWhere(['=', 'numero_despacho', $numero_despacho])
                            ->andFilterWhere(['between', 'fecha_pedido', $fecha_inicio, $fecha_corte]);
                               
                        
                        $table = $table->orderBy('id_despacho DESC');
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
                    $table = DespachoPedidos::find()->orderBy('id_despacho DESC');
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
                return $this->render('search_index', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
                           
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }
    /**
     * Displays a single DespachoPedidos model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id,$token)
    {
        $conDetalle = \app\models\DespachoPedidoDetalles::find()->where(['id_despacho' => $id])->all();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'conDetalle' => $conDetalle,
            'token' => $token,
        ]);
    }

    /**
     * Creates a new DespachoPedidos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DespachoPedidos();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_despacho, 'token' => 0]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    //importar ordenes de produccion
   public function actionImportar_pedidos($token) {
        $operacion = \app\models\Pedidos::find()->where([
                                              'pedido_cerrado' => 1,
                                              'pedido_despachado' => 0])->orderBy('id_pedido ASC')->all();
        $form = new \app\models\FormModeloBuscar();
        $q = null;
        $cliente = null;
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $numero = Html::encode($form->numero);  
                $cliente = Html::encode($form->cliente);  
                    $operacion = \app\models\Pedidos::find()
                            ->andFilterWhere(['=','numero_pedido', $numero])
                            ->andFilterWhere(['=','idcliente', $cliente])
                            ->andWhere(['=','pedido_cerrado', 1])
                            ->andWhere(['=','pedido_despachado', 0]);
                    $operacion = $operacion->orderBy('id_pedido ASC');                    
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
            $table = \app\models\Pedidos::find()->where([
                                              'pedido_cerrado' => 1,
                                              'pedido_despachado' => 0])->orderBy('id_pedido ASC');
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
            if(isset($_POST["listado_pedidos"])){
                $intIndice = 0;
                foreach ($_POST["listado_pedidos"] as $intCodigo) {
                    $registro = \app\models\Pedidos::find()->where(['=','id_pedido', $intCodigo])->one();
                    if($registro){
                        $table = new DespachoPedidos();
                        $table->id_pedido = $intCodigo;
                        $table->numero_pedido = $registro->numero_pedido;
                        $table->idcliente = $registro->idcliente;
                        $table->fecha_despacho = date('Y-m-d');
                        $table->fecha_hora_registro = date('Y-m-d H:i:s');
                        $table->user_name = Yii::$app->user->identity->username;
                        $table->save(false);
                        $intIndice++;
                    }else{
                       Yii::$app->getSession()->setFlash('error', 'El pedido con Numero: '.$registro->numero_pedido.' no se encuentra listo para despachar.'); 
                    }   
                }
                Yii::$app->getSession()->setFlash('success', 'Se guardaron: '.$intIndice.' registros con exito en el sistema');
                return $this->redirect(['index']);
            }
        }
        return $this->render('importar_pedidos', [
            'operacion' => $operacion,            
            'pagination' => $pages,
            'form' => $form,
            'token' => $token,
        ]);
    }
    
   //DESCARGA REFERENCIAS
    public function actionDescargar_referencias($id, $id_pedido, $token) {
        $detallePedido = \app\models\PedidosDetalle::find()->where(['=','id_pedido',  $id_pedido])->andWhere(['>','unidades_faltantes', 0])->all();
        if($detallePedido){
            $contador = 0;
            foreach ($detallePedido as $detalle) {
                $buscarDato = \app\models\DespachoPedidoDetalles::find()->where(['id_detalle' => $detalle->id_detalle])->one();
                if(!$buscarDato){
                    $table = new \app\models\DespachoPedidoDetalles();
                    $table->id_inventario = $detalle->id_inventario;
                    $table->id_detalle = $detalle->id_detalle;
                    $table->id_despacho = $id;
                    $table->valor_unitario = $detalle->valor_unitario;
                    $table->porcentaje_valor = $detalle->porcentaje_descuento;
                    $table->valor_descuento = $detalle->valor_descuento;
                    $table->save();
                    $contador++;
                }    
            }
            Yii::$app->getSession()->setFlash('success', 'Se grabaron: ' .$contador . ' registros exitosamente.');
            return $this->redirect(['view','id' => $id,'token' => $token]);
            
        }else{
             Yii::$app->getSession()->setFlash('warning', 'NO hay referencias para este despacho.'); 
             return $this->redirect(['view','id' => $id, 'token' => $token ]);
        }
    }
    
    //vista que muestra tallas y colores
    
    public function actionVer_tallas_colores($id, $id_detalle, $codigo, $id_inventario, $token) {
        
        $despacho_detalle = \app\models\DespachoPedidoDetalles::find()->where($id_detalle)->one();
        $model = \app\models\DespachoPedidoDetalles::findOne($codigo);
        $tallas = \app\models\PedidoTallas::find()->where(['=','id_detalle', $id_detalle])->all();
        
        $ConColores = \app\models\PedidoColores::find()->where(['=','id_detalle', $id_detalle])->orderBy('idtalla ASC')->all();
        
        
        
        //PROCESO QUE ACTUALIZA LAS TALLAS
        if (isset($_POST["actualizar_cantidades"])) {
             $intIndice = 0;
            foreach ($_POST["listado_tallas"] as $intCodigo) {
                
                //CONSULTA QUE BUSCA LA TALLA DEL PEDIDO
                $tallas_inventario = \app\models\PedidoTallas::find()->where(['id_detalle' => $id_detalle,
                                                             'codigo' => $intCodigo])->one();
                $unidades = $_POST["cantidad_despachar"][$intIndice];
                $table = \app\models\PedidoTallas::findOne($intCodigo);
                if($unidades <= $table->cantidad){
                                      
                    $talla_color = \app\models\DetalleColorTalla::find()->where(['idtalla' => $tallas_inventario->idtalla,
                                                                                'id_inventario' => $id_inventario])->one();
                    if ($talla_color !== null) {
                        if($talla_color->stock_punto >= $unidades){
                            $table->unidades_despachadas = $unidades;
                            $table->save();
                            $this->TotalizarLineaDespacho($id_detalle, $codigo);
                            $this->TotalizarCantidades($id_detalle, $codigo);
                            $this->TotalizarCantidadesDespacho($id);
                        }else{
                            Yii::$app->getSession()->setFlash('warning', 'Stock en punto de venta insuficiente para la talla seleccionada.');
                        }    
                    }else{
                       Yii::$app->getSession()->setFlash('error', 'No se encontró la combinación de Talla/Color/Inventario para actualizar.'); 
                    }
                    
                }else{
                    Yii::$app->getSession()->setFlash('warning', 'La cantidad a despachar es mayor que las unidades vendidas.');
                }  
                $intIndice++;
            }  
            return $this->redirect(['despacho-pedidos/ver_tallas_colores', 'id' => $id, 'id_detalle' => $id_detalle,'codigo' => $codigo, 'id_inventario' => $id_inventario, 'token' => $token]);
        }
        //PROCESO QUE ACTUALIZA LOS COLORES
        if (isset($_POST["actualizar_colores"])) {
             $intIndice = 0;
            foreach ($_POST["listado_colores"] as $intCodigo) {
               $unidades = $_POST["cantidad_color"][$intIndice];
                $table = \app\models\PedidoColores::findOne($intCodigo);
                $talla = \app\models\PedidoTallas::findOne($table->codigo);
                if($unidades <= $talla->cantidad){
                    $table->cantidad = $unidades;
                    $table->save();
                }else{
                    Yii::$app->getSession()->setFlash('warning', 'La cantidad a despachar de colores es mayor que las unidades vendidas.');
                }  
                $intIndice++;
            }  
            return $this->redirect(['despacho-pedidos/ver_tallas_colores', 'id' => $id, 'id_detalle' => $id_detalle,'codigo' => $codigo, 'token' => $token, 'id_inventario' => $id_inventario]);
        }
        return $this->render('ver_talla', [
            'model' => $model,
            'id_detalle' => $id_detalle,
            'tallas' => $tallas,
            'id_inventario' => $id_inventario,
            'id' => $id,
            'ConColores' => $ConColores,
            'token' => $token,
        ]);
        
    }
    
    //PROCESO QUE TOTALIZA CANTIDADES DE LAS TALLAS DEL PEDIDO
    protected function TotalizarLineaDespacho($id_detalle, $codigo) {
        $tallas = \app\models\PedidoTallas::find()->where(['=','id_detalle', $id_detalle])->all();
        $buscar = \app\models\DespachoPedidoDetalles::find()->where(['=','codigo', $codigo])->one();
        $total = 0;
        foreach ($tallas as $val) {
            $total += $val->unidades_despachadas;
                    
        }
        $buscar->cantidad_despachada = $total;
        $buscar->save();
    }
    
    //actualizar la linea
    //PROCESO QUE TOTALIZA CANTIDADES DEL DETALLA PEDIDO
    protected function TotalizarCantidades($id_detalle, $codigo) {
    
        $table = \app\models\PedidosDetalle::findOne($id_detalle);
        $despacho = \app\models\DespachoPedidoDetalles::findOne($codigo);
        if($table->tipo_descuento != null){
            $total_descuento = 0;
            $saldo = 0;
            if($table->tipo_descuento == 1){
                $total_descuento = round($despacho->valor_unitario * $despacho->porcentaje_valor)/100;
                $despacho->valor_descuento = $total_descuento * $despacho->cantidad_despachada;
                $saldo = $despacho->valor_unitario - $total_descuento; 
                $despacho->total_pagar = round($saldo * $despacho->cantidad_despachada);
            }else{
                $total_descuento = $despacho->porcentaje_valor;
                $despacho->valor_descuento = $total_descuento * $despacho->cantidad_despachada;
                $saldo = $despacho->valor_unitario - $total_descuento; 
                $table->total_pagar = round($saldo * $despacho->cantidad_despachada);
            }
        }else{
            $despacho->total_pagar = round($despacho->cantidad_despachada * $despacho->valor_unitario);
        }
        
        $despacho->save(false);
    }
    
    //PROCESO QUE TOTALIZA CANTIDADES DE LAS TALLAS DEL DESPACHO
     //PROCESO QUE TOTALIZA CANTIDADES TODAS LAS CANTIDADES DEL PEDIDO
    protected function TotalizarCantidadesDespacho($id) {
        $buscar = \app\models\DespachoPedidoDetalles::find()->where(['=','id_despacho', $id])->all();
        $table = \app\models\DespachoPedidos::findOne($id);
        $total = 0; $cantidades = 0;
        foreach ($buscar as $val) {
            $total += $val->total_pagar;
            $cantidades += $val->cantidad_despachada;
        }
        $table->cantidad_despachada = $cantidades;
        $table->total_despacho = $total;
        $table->subtotal = $total;
        $table->save();
    }
    
    /**
     * Updates an existing DespachoPedidos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_despacho, 'token' => 0]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DespachoPedidos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEliminar_registro($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    //ELIMIAR LAS TALLAS DEL PEDIDO
    public function actionEliminar_lineas($id, $id_detalle, $token) {
        try {
           $dato = \app\models\DespachoPedidoDetalles::findOne($id_detalle);
           $dato->delete();
           Yii::$app->getSession()->setFlash('success', 'Registro Eliminado exitosamente.');
           return $this->redirect(Yii::$app->request->referrer);
        } catch (IntegrityException $e) {
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el registro, tiene tallas y colores asociados'); 
           return $this->redirect(Yii::$app->request->referrer);
            
        } catch (\Exception $e) {
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el registro, tiene tallas y colores asociados'); 
            return $this->redirect(Yii::$app->request->referrer);
        }
       
       return $this->redirect(Yii::$app->request->referrer);
    }
    
     //PROCESO QUE SE AUTORIZA
    public function actionAutorizado($id, $token) {
        $detalle_pedido = \app\models\DespachoPedidos::find()->where(['=','id_despacho', $id])->andWhere(['>','cantidad_despachada', 0])->one();
        if(!$detalle_pedido){
            Yii::$app->getSession()->setFlash('error','Debe de ingresar las referencias y las cantidades por talla para poder autorizar el pedido. ');
           return $this->redirect(Yii::$app->request->referrer);
        }
        $model = DespachoPedidos::findOne($id);
        if($model->autorizado == 0){
            $model->autorizado = 1;
            $model->save();
            return $this->redirect(['despacho-pedidos/view', 'id' => $id, 'token' => $token]);
        }else{
            $model->autorizado = 0;
            $model->save();
            return $this->redirect(['despacho-pedidos/view', 'id' => $id, 'token' => $token]);
        }
    }
    
    //PROCESO QUE CIERRA EL PEDIDO
    public function actionCerrar_despacho($id, $token) {
        $model = DespachoPedidos::findOne($id);
         //generar consecutivo
        $registro = \app\models\Consecutivo::findOne(28);
        $valor = $registro->consecutivo + 1;
        $model->numero_despacho = $valor;
        $model->despacho_cerrado = 1;
        $model->save();
        //actualiza consecutivo
        $registro->consecutivo = $valor;
        $registro->save();
        $this->CalcularImpuestoTotalPedido($id);
        $this->ActualizarInventario($id);
        return $this->redirect(['despacho-pedidos/view', 'id' => $id, 'token' => $token]); 
    }
    
    //CERRAR EL PEDIDO
    public function actionCerrar_pedido($id, $id_pedido, $token) {
        $pedido = \app\models\Pedidos::findOne($id_pedido);
        $pedido->pedido_despachado = 1;
        $pedido->save();
        return $this->redirect(['despacho-pedidos/view', 'id' => $id, 'token' => $token]); 
    }
    
    protected function CalcularImpuestoTotalPedido($id) {
   
        $conPedido = \app\models\DespachoPedidos::findOne($id); 
        $conInventario = \app\models\ConfiguracionInventario::findOne(1);
        $conIva = \app\models\ConfiguracionIva::find()->where(['=','predeterminado', 1])->one();
    
        $valor_pedido = $conPedido->subtotal;
        $subtotal = 0;
        $total = 0;
        $iva = 0;
        $porcentaje = 0;
    
        
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
        $conPedido->subtotal = $subtotal; // Base imponible o Subtotal (sin IVA)
        $conPedido->impuesto = $iva;
        $conPedido->total_despacho =  $total; // Total con IVA
        $conPedido->save();
    }
    
    //PROCESO QUE ACTUALIZA LAS UNIDADES
    protected function ActualizarInventario($id) {
        
        $despacho = DespachoPedidos::findOne($id);
        $detalleDespacho = \app\models\DespachoPedidoDetalles::find()->where(['id_despacho' => $id])->all();
        //recorremos el vector
        foreach ($detalleDespacho as $detalle) {
           $inventario = \app\models\InventarioPuntoVenta::findOne($detalle->id_inventario);
           $pedido_colores = \app\models\PedidoColores::find()->where(['id_detalle' => $detalle->id_detalle,
                                                              'id_pedido' => $despacho->id_pedido])->all();
            foreach ($pedido_colores as $colores) {
               $registro = \app\models\DetalleColorTalla::find()->where(['id_inventario' =>$inventario->id_inventario,
                                                                'id' => $colores->id,
                                                                'idtalla' => $colores->idtalla])->one();
                if($registro){
                    $registro->stock_punto -= $colores->cantidad;
                    $registro->save();
                }                                                
            }
            $inventario->stock_inventario -= $detalle->cantidad_despachada;
            $inventario->stock_salida += $detalle->cantidad_despachada;
            $inventario->save();
            
        }
    }
    
    //IMPRESIONES
    public function actionImprimir_despachos($id)
    {
        return $this->render('../formatos/reporte_despacho_pedido', [
            'model' => $this->findModel($id),
            
        ]);
    }

    /**
     * Finds the DespachoPedidos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DespachoPedidos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DespachoPedidos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
