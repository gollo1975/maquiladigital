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
use app\models\PedidoCliente;
use app\models\PedidoClientePuntoSearch;
use app\models\UsuarioDetalle;
use app\models\PedidoClienteReferencias;


/**
 * PedidoClienteController implements the CRUD actions for PedidoCliente model.
 */
class PedidoClienteController extends Controller
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
     * Lists all PedidoCliente models.
     * @return mixed
     */
   public function actionIndex($token = 0) {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 139])->all()) {
                $form = new \app\models\FormFiltroPedido();
                $numero = null;
                $cliente = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $numero = Html::encode($form->numero);
                        $cliente = Html::encode($form->cliente);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $table = PedidoCliente::find()
                                ->andFilterWhere(['=', 'numero_pedido', $numero])
                                ->andFilterWhere(['=', 'idcliente', $cliente])
                                ->andFilterWhere(['between', 'fecha_pedido', $fecha_inicio, $fecha_corte]);
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
                            $this->actionExcelconsultaPedidos($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = PedidoCliente::find()
                             ->orderBy('id_pedido DESC');
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
                        $this->actionExcelconsultaPedidos($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
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
     * Displays a single PedidoCliente model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token)
    {
        $model = PedidoCliente::findOne($id);
        $referencias = PedidoClienteReferencias::find()->where(['=','id_pedido', $id])->all();
        //actualiza los regisgtros de las referencias
        if (isset($_POST["actualizar_linea"])) {
            $intIndice = 0;
            $variable = 0;
            foreach ($_POST["listado_referencia"] as $intCodigo) {
                $variable = $_POST["tipo_lista"][$intIndice];
                $BuscarLista = \app\models\ReferenciaListaPrecio::findOne($variable);
                $table = PedidoClienteReferencias::findOne($intCodigo);
                $table->id_detalle = $_POST["tipo_lista"][$intIndice];
                $table->valor_unitario = $BuscarLista->valor_venta;
                $table->save();
                $intIndice++;
            } 
             return $this->redirect(['pedido-cliente/view', 'id' => $id,'token' => $token]);
        }    
        //ELIMINA LAS REFERENCIAS CREADAS EN LAS VISTA
        if (Yii::$app->request->post()) {
            if (isset($_POST["eliminar_referencia"])) {
                if (isset($_POST["listado_eliminar"])) {
                    foreach ($_POST["listado_eliminar"] as $intCodigo) {
                        try {
                            $eliminar = PedidoClienteReferencias::findOne($intCodigo);
                            $eliminar->delete();
                            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                            $this->redirect(["pedido-cliente/view", 'id' => $id, 'token' => $token]);
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
            'model' => $model,
            'token' => $token,
            '$id' => $id,
            'referencias' => $referencias,
        ]);
    }

    /**
     * Creates a new PedidoCliente model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($token = 0)
    {
        $model = new PedidoCliente();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->user_name = Yii::$app->user->identity->username;
            $model->save();
            $this->redirect(['pedido-cliente/view', 'id' => $model->id_pedido, 'token' => $token]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PedidoCliente model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    //CREAR OPERACIONES AL PRODUCTO
     public function actionNueva_referencia_pedido($id, $token)
    {
        $referencia = \app\models\ReferenciaProducto::find()->orderBy('descripcion_referencia ASC')->all();
        $form = new \app\models\FormMaquinaBuscar();
        $q = null;
        $mensaje = '';
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                    $referencia = \app\models\ReferenciaProducto::find()
                            ->where(['like','descripcion_referencia',$q])
                            ->orwhere(['=','codigo',$q]);
                    $referencia = $referencia->orderBy(' descripcion_referencia ASC');                    
                    $count = clone $referencia;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 15,
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
            $referencia = \app\models\ReferenciaProducto::find()->orderBy('descripcion_referencia ASC');
            $count = clone $referencia;
            $pages = new Pagination([
                'pageSize' => 15,
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
                   $listado = PedidoClienteReferencias::find()
                            ->where(['=', 'codigo', $intCodigo])
                            ->andWhere(['=', 'id_pedido', $id])
                            ->all();
                    $reg = count($listado);
                    if ($reg == 0) {
                        $table = new PedidoClienteReferencias();
                        $matricula = \app\models\Matriculaempresa::findOne(1);
                        $conref = \app\models\ReferenciaProducto::findOne($intCodigo);
                        $table->id_pedido = $id;
                        $table->codigo = $intCodigo;
                        $table->referencia = $conref->descripcion_referencia;
                        $table->porcentaje = $matricula->porcentajeiva;
                        $table->user_name= Yii::$app->user->identity->username;
                        $table->save(false);
                    }
                    $intIndice++;
                }
                return $this->redirect(["pedido-cliente/view", 'id' => $id, 'token' => $token]);
            }else{
                 Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro de las referencias.');
                return $this->redirect(["nueva_referencia_pedido", 'id' => $id, 'token' => $token]);
            }
        }
        return $this->render('_form_nueva_referencia', [
            'referencia' => $referencia,            
            'pagination' => $pages,
            'id' => $id,
            'form' => $form,
            'token' => $token,

        ]);
    }

    //CREAR TALLAS
     public function actionCrear_tallas_referencia($id, $token, $id_referencia){
        $tallas = \app\models\Talla::find()->orderBy('sexo,talla asc')->all();
        $form = new \app\models\FormMaquinaBuscar();
        $q = null;
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                if ($q){
                    $tallas = \app\models\TallaSearch::find()
                            ->where(['like','talla',$q])
                            ->orwhere(['like','sexo',$q])
                            ->orderBy('sexo asc')
                            ->all();
                }               
            } else {
                $form->getErrors();
            }                    
                    
        } else {
             $tallas = \app\models\TallaSearch::find()->orderBy('sexo,talla asc')->all();
        }
        if (isset($_POST["idtalla"])) {
                $intIndice = 0;
                foreach ($_POST["idtalla"] as $intCodigo) {
                    $table = new \app\models\PedidoClienteTalla();
                    $talla = \app\models\Talla::find()->where(['idtalla' => $intCodigo])->one();
                    $detalles = \app\models\PedidoClienteTalla::find()
                        ->where(['=', 'id_referencia', $id])
                        ->andWhere(['=', 'idtalla', $talla->idtalla])
                        ->all();
                    $reg = count($detalles);
                    if ($reg == 0) {
                        $table->idtalla = $intCodigo;
                        $table->id_referencia = $id_referencia;
                        $table->id_pedido = $id;
                        $table->cantidad = 0;
                        $table->user_name = Yii::$app->user->identity->username;
                        $table->save(false); 
                    }
                }
                $this->redirect(["pedido-cliente/view", 'id' => $id, 'token' => $token]);
        }
        return $this->render('crear_tallas', [
            'tallas' => $tallas,            
            'id' => $id,
            'form' => $form,
            'token' => $token,
            'id_referencia' => $id_referencia,
        ]);
    
    }
    
    //PERMITE VER LAS TALLAS DE LA REFERENCIA
    public function actionVer_tallas($id, $token, $id_referencia) {
        $tallas_referencia = \app\models\PedidoClienteTalla::find()->where(['=','id_referencia', $id_referencia])->all();
        $model = PedidoClienteReferencias::findOne($id_referencia);
        if (isset($_POST["actualizar_cantidades"])) {
            $intIndice = 0;
            $contar = 0;
            foreach ($_POST["listado_tallas"] as $intCodigo) {
                $contar = $_POST["cantidad"][$intIndice];    
                if($contar > 0){
                    $table = \app\models\PedidoClienteTalla::findOne($intCodigo);
                    $table->cantidad = $_POST["cantidad"][$intIndice];
                    $table->save();
                    $this->ContarCantidadTalla($id_referencia);
                    $intIndice++;
                }else{
                    $intIndice++;
                }    
            } 
             return $this->redirect(['pedido-cliente/ver_tallas', 'id' => $id,'token' => $token,'id_referencia' => $id_referencia]);
        }    
         return $this->render('ver_tallas', [
            'id' => $id,
            'tallas_referencia' => $tallas_referencia,
            'model' => $model,
            'token' => $token,
            'id_referencia' => $id_referencia,
        ]);
    }
    
    // PROCESO QUE CUENTA LAS CANTIDADES VENDIDAS POR TALLAS
    protected function ContarCantidadTalla($id_referencia) {
        $referencia = PedidoClienteReferencias::findOne($id_referencia);
        $tallas = \app\models\PedidoClienteTalla::find()->where(['=','id_referencia', $id_referencia])->all();
        $sumar = 0;
        foreach ($tallas as $talla):
            $sumar += $talla->cantidad;
        endforeach;
        $referencia->cantidad = $sumar;
        $referencia->save();
        $this->CalcularValoresReferencia($id_referencia);
        
    }
    
    //PROCESO QUE CALCULA EL TOTAL POR REFEENCIA
    protected function CalcularValoresReferencia($id_referencia) {
       $referencia = PedidoClienteReferencias::findOne($id_referencia);
       $iva = 0; $subtotal = 0;
       $subtotal = $referencia->valor_unitario * $referencia->cantidad;
       $iva = round(($subtotal * $referencia->porcentaje)/100);
       $total = $subtotal + $iva;
       $referencia->subtotal = $subtotal;
       $referencia->iva = $iva;
       $referencia->total_linea = $total;
       $referencia->save();
    }
    
    /**
     * Deletes an existing PedidoCliente model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEliminar_lineas($id_talla, $id_referencia, $token, $id)
    {
        $dato = \app\models\PedidoClienteTalla::findOne($id_talla);
        $dato->delete();
        $this->ContarCantidadTalla($id_referencia);
       return $this->redirect(['pedido-cliente/ver_tallas', 'id' => $id,'token' => $token,'id_referencia' => $id_referencia]);
    }
    
    //PROCESO QUE AUTORIZA EL PRODUCTO
    public function actionAutorizado($id, $token) {
        $pedido = PedidoCliente::findOne($id);
        $referencias = PedidoClienteReferencias::find()->where(['=','id_pedido', $id])->one();
        $sw = 0;
        $sw1 = 0;
        if($referencias){
           $referencia = PedidoClienteReferencias::find()->where(['=','id_pedido', $id])->all();
           foreach ($referencia as $refe):
               if($refe->valor_unitario == 0 ){
                   $sw = 1;
               }
           endforeach;
           if($sw == 0){
               $tallas = \app\models\PedidoClienteTalla::find()->where(['=','id_pedido', $id])->all();
               if(count($tallas) > 0){
                   foreach ($tallas as $talla):
                       if($talla->cantidad  == 0){
                           $sw1 = 1;
                       }
                   endforeach;
                   if($sw1 == 0){
                        if($pedido->autorizado == 0){
                            $pedido->autorizado = 1;
                            $pedido->save();
                        }else{
                            $pedido->autorizado = 0;
                            $pedido->save();
                        } 
                        return $this->redirect(['pedido-cliente/view', 'id' => $id,'token' => $token]);
                   }else{
                       Yii::$app->getSession()->setFlash('error','Favor ingresar las tallas y las cantidades de cada referencia para autorizar el pedido. ');
                         return $this->redirect(['pedido-cliente/view', 'id' => $id,'token' => $token]);
                   }
               }else{
                  Yii::$app->getSession()->setFlash('warning','Favor ingresar las tallas a cada referencia para autorizar el pedido. ');
                  return $this->redirect(['pedido-cliente/view', 'id' => $id,'token' => $token]);  
               }
               
           }else{
                Yii::$app->getSession()->setFlash('warning','Selecciona las litas de precio y persiona ACTUALIZAR. Luego debe de ingresar las tallas de cada referencias. ');
                return $this->redirect(['pedido-cliente/view', 'id' => $id,'token' => $token]);   
           }
        }else{
            Yii::$app->getSession()->setFlash('error', 'No hay REFERENCIAS asignadas al pedido del cliente ('.$pedido->cliente->nombrecorto. ').');
            return $this->redirect(['pedido-cliente/view', 'id' => $id,'token' => $token]); 
        }
    }
    
    //PROCESO QUE CIERRA EL PEDIDO
    public function actionCerrar_pedido($id, $token) {
        $model = PedidoCliente::findOne($id);
         //generar consecutivo
        $registro = \app\models\Consecutivo::findOne(18);
        $valor = $registro->consecutivo + 1;
        $model->numero_pedido = $valor;
        $model->pedido_cerrado = 1;
        $model->save();
        //actualiza consecutivo
        $registro->consecutivo = $valor;
        $registro->save();
        $this->CalcularTotalPedido($id);
        return $this->redirect(['pedido-cliente/view', 'id' => $id,'token' => $token]); 
    }
    
    //PROCESO QUE TOTALIZA Y CALCULA LOS VALORES DE CADA REFERENCIA
    protected function CalcularTotalPedido($id) {
        $model = PedidoCliente::findOne($id);
        $referencia = PedidoClienteReferencias::find()->where(['=','id_pedido', $id])->all();
        $cantidad= 0; $subtotal = 0; $iva = 0; $total = 0;
        foreach ($referencia as $referencias):
            $cantidad += $referencias->cantidad;
            $subtotal += $referencias->subtotal;
            $iva += $referencias->iva;
            $total += $referencias->total_linea;
        endforeach;
        $model->total_unidades = $cantidad;
        $model->valor_total = $subtotal;
        $model->impuesto = $iva;
        $model->total_pedido = $total;
        $model->save();
    }
    
    public function actionImprimir_pedido($id)
    {
        return $this->render('../formatos/reporte_pedido_cliente', [
            'model' => $this->findModel($id),
            
        ]);
    }
    
    public function actionImprimir_tallas($id)
    {
        return $this->render('../formatos/reporte_pedido_tallas', [
            'model' => $this->findModel($id),
            
        ]);
    }

    /**
     * Finds the PedidoCliente model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PedidoCliente the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PedidoCliente::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    //EXCELES
    //PERMITE EXPORTAR A EXCEL EL PRESUPUESTO DE CADA PEDIDO 
    public function actionExcelconsultaPedidos($tableexcel) {   
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

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'No PEDIDO')
                    ->setCellValue('C1', 'DOCUMENTO')
                    ->setCellValue('D1', 'CLIENTE')
                    ->setCellValue('E1', 'FECHA PEDIDO')
                    ->setCellValue('F1', 'FECHA ENTREGA')
                    ->setCellValue('G1', 'TOTAL UNIDADES')
                    ->setCellValue('H1', 'SUBTOTAL')
                    ->setCellValue('I1', 'IMPUESTO')
                    ->setCellValue('J1', 'TOTAL')
                    ->setCellValue('K1', 'CERRADO')
                    ->setCellValue('L1', 'USER NAME')
                    ->setCellValue('M1', 'CODIGO')
                    ->setCellValue('N1', 'REFERENCIA')
                    ->setCellValue('O1', 'VR UNITARIO')
                    ->setCellValue('P1', 'CANTIDAD')
                    ->setCellValue('Q1', 'SUBTOTA')
                    ->setCellValue('R1', 'IVA')
                    ->setCellValue('S1', 'TOTAL LINEA');
        $i = 2;
        
        foreach ($tableexcel as $val) {
            $referencias  = PedidoClienteReferencias::find()->where(['=','id_pedido', $val->id_pedido])->all();
            foreach ($referencias as $referencia){
                                  
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $val->id_pedido)
                        ->setCellValue('B' . $i, $val->numero_pedido)
                        ->setCellValue('C' . $i, $val->cliente->cedulanit)
                        ->setCellValue('D' . $i, $val->cliente->nombrecorto)
                        ->setCellValue('E' . $i, $val->fecha_pedido)
                        ->setCellValue('F' . $i, $val->fecha_entrega)
                        ->setCellValue('G' . $i, $val->total_unidades)
                        ->setCellValue('H' . $i, $val->valor_total)
                        ->setCellValue('I' . $i, $val->impuesto)
                        ->setCellValue('J' . $i, $val->total_pedido)
                        ->setCellValue('K' . $i, $val->pedidoCerrado)
                        ->setCellValue('L' . $i, $val->user_name)
                        ->setCellValue('M' . $i, $referencia->codigo)
                        ->setCellValue('N' . $i, $referencia->referencia)
                        ->setCellValue('O' . $i, $referencia->valor_unitario)
                        ->setCellValue('P' . $i, $referencia->cantidad)
                        ->setCellValue('Q' . $i, $referencia->subtotal)
                        ->setCellValue('R' . $i, $referencia->iva)
                        ->setCellValue('S' . $i, $referencia->total_linea);
                $i++;
            }
            $i = $i;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Pedido_cliente.xlsx"');
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
    
    //PERMITE EXPORTAR LAS TALLAS
      //PERMITE EXPORTAR A EXCEL EL PRESUPUESTO DE CADA PEDIDO 
    public function actionExcel_tallas_pedido($id, $token, $id_referencia) {   
        $objPHPExcel = new \PHPExcel();
        $detalle_tallas = \app\models\PedidoClienteTalla::find()->where(['=','id_referencia', $id_referencia])->all();
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
                    ->setCellValue('B1', 'No PEDIDO')
                    ->setCellValue('C1', 'TALLA')
                    ->setCellValue('D1', 'REFERENCIA')
                    ->setCellValue('E1', 'FECHA PEDIDO')
                    ->setCellValue('F1', 'FECHA ENTREGA')
                    ->setCellValue('G1', 'CLIENTE')
                    ->setCellValue('H1', 'TOTAL UNIDADES')
                    ->setCellValue('I1', 'VR.UNITARIO');
                  
        $i = 2;
        
        foreach ($detalle_tallas as $val) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->codigo_talla)
                    ->setCellValue('B' . $i, $val->pedido->numero_pedido)
                    ->setCellValue('C' . $i, $val->talla->talla)
                    ->setCellValue('D' . $i, $val->referencia->referencia)
                    ->setCellValue('E' . $i, $val->pedido->fecha_pedido)
                    ->setCellValue('F' . $i, $val->pedido->fecha_entrega)
                    ->setCellValue('G' . $i, $val->pedido->cliente->nombrecorto)
                    ->setCellValue('H' . $i, $val->cantidad)
                    ->setCellValue('I' . $i, $val->referencia->valor_unitario);
                  
            $i++;
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
}
