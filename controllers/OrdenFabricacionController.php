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
//models;
use app\models\OrdenFabricacion;
use app\models\OrdenFabricacionSearch;
use app\models\UsuarioDetalle;
use app\models\Cliente;
use app\models\PedidoCliente;

/**
 * OrdenFabricacionController implements the CRUD actions for OrdenFabricacion model.
 */
class OrdenFabricacionController extends Controller
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
     * Lists all OrdenFabricacion models.
     * @return mixed
     */
   public function actionIndex() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 140])->all()) {
                $form = new \app\models\FormFiltroPedido();
                $numero = null;
                $cliente = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $codigo = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $numero = Html::encode($form->numero);
                        $cliente = Html::encode($form->cliente);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $codigo = Html::encode($form->codigo);
                        $table = OrdenFabricacion::find()
                                ->andFilterWhere(['=', 'numero_orden', $numero])
                                ->andFilterWhere(['=', 'idcliente', $cliente])
                                 ->andFilterWhere(['=', 'codigo_producto', $codigo])
                                ->andFilterWhere(['between', 'fecha_fabricacion', $fecha_inicio, $fecha_corte]);
                        $table = $table->orderBy('id_orden_fabricacion DESC');
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
                            $check = isset($_REQUEST['id_orden_fabricacion DESC']);
                            $this->actionExcelconsultaOrdenes($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = OrdenFabricacion::find()
                             ->orderBy('id_orden_fabricacion DESC');
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
                        $this->actionExcelconsultaOrdenes($tableexcel);
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
   //PERMITE CARGA LOS PEDIDOS
     public function actionCargar_pedidos() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 141])->all()) {
                $form = new \app\models\FormFiltroPedido();
                $codigo = null;
                $referencia = null;
                $pedido = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $codigo = Html::encode($form->codigo);
                        $referencia = Html::encode($form->referencia);
                        $table = \app\models\PedidoClienteReferencias::find()
                                ->andFilterWhere(['=', 'codigo', $codigo])
                                ->andFilterWhere(['like', 'referencia', $referencia])
                                ->andWhere(['=', 'proceso_fabricacion', 0]);
                        
                        $table = $table->orderBy('id_referencia DESC');
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
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = \app\models\PedidoClienteReferencias::find()
                             ->Where(['=', 'proceso_fabricacion', 0])->orderBy('id_referencia DESC');
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
                    
                }
                $to = $count->count();
                return $this->render('cargar_pedidos_cliente', [
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
     * Displays a single OrdenFabricacion model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = OrdenFabricacion::findOne($id);
        $conTallas = \app\models\OrdenFabricacionTallas::find()->where(['=','id_orden_fabricacion', $id])->all();
        return $this->render('view', [
            'model' => $model,
            'id' => $id,
            'conTallas' => $conTallas,
        ]);
    }

    //ENVIAR PEDIDO A FABRICACION
    public function actionEnviar_pedido_fabricacion($id_referencia){
        $conReferencia = \app\models\PedidoClienteReferencias::findOne($id_referencia);
        $conBusqueda = OrdenFabricacion::find()->where(['=','id_referencia', $id_referencia])->one();
        if(!$conBusqueda){
            if($conReferencia){
                $table = new OrdenFabricacion();
                $table->id_pedido = $conReferencia->id_pedido;
                $table->idcliente = $conReferencia->pedido->idcliente;
                $table->codigo_producto = $conReferencia->codigo;
                $table->id_referencia = $id_referencia;
                $table->fecha_fabricacion = date('Y-m-d');
                $table->cantidades = $conReferencia->cantidad;
                $table->user_name= Yii::$app->user->identity->username;
                $table->save(); 
                $variable = OrdenFabricacion::find()->orderBy('id_orden_fabricacion DESC')->limit(1)->one();
                $variable->id_orden_fabricacion;
                return $this->redirect(["index"]);
            }    
        }else{
            Yii::$app->getSession()->setFlash('warning', 'Esta referencia se encuentra en un proceso de fabricacion. Intentelo nuevamente!');
             return $this->redirect(["cargar_pedidos"]);
        }    
    }
    
    //PERMITE CARGAR LAS TALLAS DE LA REFERENCIA QUE ESTA EN EL PEDIDO
    public function actionCargar_tallas_pedido($id_referencia, $id) {
        $tallas = \app\models\PedidoClienteTalla::find()->where(['=','id_referencia', $id_referencia])->all();
        foreach ($tallas as $talla):
            if(!\app\models\OrdenFabricacionTallas::find()->where(['=','codigo_talla', $talla->codigo_talla])->andWhere(['=','id_orden_fabricacion', $id])->one()){; 
                $table = new \app\models\OrdenFabricacionTallas ();
                $table->codigo_talla = $talla->codigo_talla;
                $table->id_orden_fabricacion = $id;
                $table->cantidad_vendida = $talla->cantidad;
                $table->cantidad_real = $talla->cantidad;
                $table->idtalla = $talla->idtalla;
                $table->save ();   
            }    
        endforeach;
        return $this->redirect(["view",'id' => $id]);
    }
    
    //PROCESO QUE AUTORIZA LA ORDEN DE FABRICACION
    public function actionAutorizado($id) {
        $model = OrdenFabricacion::findOne($id);
        if(\app\models\OrdenFabricacionTallas::find()->where(['=','id_orden_fabricacion', $id])->one()){
            if($model->autorizada == 0){
                $model->autorizada = 1;
                $model->save();
            }else{
                $model->autorizada = 0;
                $model->save();
            }
            return $this->redirect(["view",'id' => $id]);
        }else{
             Yii::$app->getSession()->setFlash('error', 'No se puede autorizar la orden de fabricacion porque NO se ha descargado las tallas.');
              return $this->redirect(["view",'id' => $id]);
        }    
    }
    
    //PROCESO QUE CIERRA LA ORDEN DE FABRICACION
    public function actionCerrar_orden($id) {
        $model = OrdenFabricacion::findOne($id);
        $referencia = \app\models\PedidoClienteReferencias::findOne($model->id_referencia);
         //generar consecutivo
        $registro = \app\models\Consecutivo::findOne(19);
        $valor = $registro->consecutivo + 1;
        $model->numero_orden = $valor;
        $model->orden_cerrada = 1;
        $model->save();
        //actualiza consecutivo
        $registro->consecutivo = $valor;
        $registro->save();
        $referencia->proceso_fabricacion = 1;
        $referencia->save();
        return $this->redirect(['orden-fabricacion/view', 'id' => $id]); 
    }

     public function actionImprimir_orden($id)
    {
        return $this->render('../formatos/reporte_orden_fabricacion', [
            'model' => $this->findModel($id),
            
        ]);
    }
    
    /**
     * Finds the OrdenFabricacion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrdenFabricacion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrdenFabricacion::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
     //EXCELES
    //PERMITE EXPORTAR A EXCEL EL PRESUPUESTO DE CADA PEDIDO 
    public function actionExcelconsultaOrdenes($tableexcel) {   
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
                    ->setCellValue('B1', 'NRO ORDEN')
                    ->setCellValue('C1', 'DOCUMENTO')
                    ->setCellValue('D1', 'CLIENTE')
                    ->setCellValue('E1', 'FECHA FABRICACION')
                    ->setCellValue('F1', 'FECHA HORA REGISTRO ')
                    ->setCellValue('G1', 'TOTAL UNIDADES')
                    ->setCellValue('H1', 'CERRADO')
                    ->setCellValue('I1', 'USER NAME')
                    ->setCellValue('J1', 'CODIGO')
                    ->setCellValue('K1', 'REFERENCIA')
                    ->setCellValue('L1', 'NUMERO PEDIDO')
                    ->setCellValue('M1', 'TALLA')
                    ->setCellValue('N1', 'CANTIDAD VENDIDA')
                    ->setCellValue('O1', 'CANTIDAD REAL');
        $i = 2;
        
        foreach ($tableexcel as $val) {
            $tallas  = \app\models\OrdenFabricacionTallas::find()->where(['=','id_orden_fabricacion', $val->id_orden_fabricacion])->all();
            foreach ($tallas as $talla){
                                  
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $val->id_orden_fabricacion)
                        ->setCellValue('B' . $i, $val->numero_orden)
                        ->setCellValue('C' . $i, $val->cliente->cedulanit)
                        ->setCellValue('D' . $i, $val->cliente->nombrecorto)
                        ->setCellValue('E' . $i, $val->fecha_fabricacion)
                        ->setCellValue('F' . $i, $val->fecha_hora_registro)
                        ->setCellValue('G' . $i, $val->cantidades)
                        ->setCellValue('H' . $i, $val->ordenCerrada)
                        ->setCellValue('I' . $i, $val->user_name)
                        ->setCellValue('J' . $i, $val->codigo_producto)
                        ->setCellValue('K' . $i, $val->referencia->referencia)
                        ->setCellValue('L' . $i, $val->pedido->numero_pedido)
                        ->setCellValue('M' . $i, $talla->talla->talla)
                        ->setCellValue('N' . $i, $talla->cantidad_vendida)
                        ->setCellValue('O' . $i, $talla->cantidad_real);
                       
                $i++;
            }
            $i = $i;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Orden_fabricacion.xlsx"');
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
