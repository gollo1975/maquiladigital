<?php

namespace app\controllers;

use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\helpers\Html;

//models
use app\models\PackingPedido;
use app\models\PackingPedidoSearch;
use app\models\UsuarioDetalle;

/**
 * PackingPedidoController implements the CRUD actions for PackingPedido model.
 */
class PackingPedidoController extends Controller
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
     * Lists all PackingPedido models.
     * @return mixed
     */
     public function actionIndex($token = 0) {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 185])->all()) {
                $form = new \app\models\FormFiltroPacking();
                $numero = null;
                $cliente = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                 $transportadora = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $numero = Html::encode($form->numero);
                        $cliente = Html::encode($form->cliente);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $transportadora = Html::encode($form->transportadora);
                        $table = PackingPedido::find()
                                ->andFilterWhere(['=', 'numero_pedido', $numero])
                                ->andFilterWhere(['=', 'idcliente', $cliente])
                                ->andFilterWhere(['=', 'id_transportadora', $transportadora])
                                ->andFilterWhere(['between', 'fecha_proceso', $fecha_inicio, $fecha_corte]);
                        $table = $table->orderBy('id_packing DESC');
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
                            $check = isset($_REQUEST['id_packing DESC']);
                            $this->actionExcelPacking($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                  
                    $table = PackingPedido::find()->orderBy('id_packing DESC');
                      
                        
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
                        $this->actionExcelPacking($tableexcel);
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

    /**
     * Displays a single PackingPedido model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $vectorDetalle = \app\models\PackingPedidoDetalle::find()->where(['id_packing' => $id])->all();
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'vectorDetalle' => $vectorDetalle,
        ]);
    }

    public function actionAutorizado($id)
    {
         $model = $this->findModel($id);
        if($model->autorizado == 0){
            $this->TotalizarUnidades($id);
            $model->autorizado = 1;
            $model->save();
        }else{
            $model->autorizado = 0;
            $model->save();
        }
        return $this->redirect(['packing-pedido/view','id' => $id]);
    }

     //proceso que totaliza
    protected function TotalizarUnidades($id) {
        $detalle = \app\models\PackingPedidoDetalle::find()->where(['=','id_packing', $id])->orderBy('numero_caja ASC')->all();
        $model = $this->findModel($id);
        $contar_unidades = 0; $contar_caja = 0; $auxiliar = 0;
        foreach ($detalle as $key => $detalles) {
            $contar_unidades += $detalles->cantidad_despachada;
            if($auxiliar <> $detalles->numero_caja){
                $contar_caja += 1;
                $auxiliar = $detalles->numero_caja;
            }else{
                $auxiliar = $detalles->numero_caja;
            }    
        }
        $model->total_cajas = $contar_caja;
        $model->cantidad_despachadas = $contar_unidades;
        $model->save();
    }
    
    //PERMITE SUBIR LA GUIA DEL PROVEEDOR AL PACKING
    public function actionSubir_guia_proveedor($id) {
        $model = new \app\models\ModeloDocumento(); 
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if (isset($_POST["subir_guia"])) {
                    if($model->numero_guia !== ''){
                        $packin = PackingPedido::findOne($id);
                        $table = \app\models\PackingPedidoDetalle::find()->where(['=','id_packing', $id])->all() ;
                        foreach ($table as $key => $val) {
                            $val->numero_guia = $model->numero_guia;
                            $val->save();
                        }
                        $packin->numero_guia = $model->numero_guia;
                        $packin->save();
                         return $this->redirect(['packing-pedido/view', 'id' => $id]);
                    }else{
                        Yii::$app->getSession()->setFlash('error', 'Este campo no puede ser vacion, Favor ingresar al menos un caracter.');
                        return $this->redirect(['packing-pedido/view', 'id' => $id]);
                    }    
                }
            }else{
              $model->getErrors();  
            }
        }
        return $this->renderAjax('form_subir_guia_provider', [
                    'model' => $model]);
    }
    
    //CERRAR EL EL PACKING
    public function actionCerrar_packing_pedido($id) {
        $model = $this->findModel($id);
        $detalle = \app\models\PackingPedidoDetalle::find()->where(['=','id_packing', $id])->orderBy('numero_caja DESC')->all();
        if(!$detalle){
            Yii::$app->getSession()->setFlash('error', 'Debe de CREAR las cajas para el despacho');
            return $this->redirect(['packing-pedido/view','id' => $id]);
        }
        if($model->numero_guia == null || $model->id_transportadora == null){
            Yii::$app->getSession()->setFlash('error', 'Debe de ingresar GUIA / TRANSPORTADORA para poder cerrar el PACKING.');
            return $this->redirect(['packing-pedido/view','id' => $id]);
        }
        //generar consecutivo
        $dato = \app\models\Consecutivo::findOne(29);
        $codigo = $dato->consecutivo + 1;
        $model->numero_packing = $codigo;
        $model->cerrado_packing = 1;
        $model->save();
        $dato->consecutivo = $codigo;
        $dato->save();
        return $this->redirect(['packing-pedido/view','id' => $id]);
        
         
    }
    
    //SUBIR TRANSPORTADORA
  
    public function actionAdicionar_transportadora($id)
    {
        $model = new \app\models\ModeloDocumento();
        if ($model->load(Yii::$app->request->post())){
            if (isset($_POST["adicionar_transportadora"])){
                if($model->transportadora !== ''){
                    $table = PackingPedido::findOne($id);
                    $table->id_transportadora = $model->transportadora;
                    $table->save(false);
                    return $this->redirect(["view",'id' => $id]); 
                }else{
                    Yii::$app->getSession()->setFlash('warning', 'Debe de seleccionar una transportadora de la lista.');
                   return $this->redirect(["view",'id' => $id]);  
                }    
            }  
        }
        return $this->renderAjax('form_adicionar_transportadora', [
            'model' => $model,
        ]);
    }   
    
    
    public function actionImprimir_packing($id) {
        $model = PackingPedido::findOne($id);
            return $this->render('../formatos/reporte_packing_pedido', [
                'model' => $model,
            ]);
        
            
    }

    /**
     * Finds the PackingPedido model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PackingPedido the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PackingPedido::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionExcelPacking($tableexcel) {
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
       

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'No PEDIDO')
                    ->setCellValue('C1', 'No PACKIN')
                    ->setCellValue('D1', 'CLIENTE')
                    ->setCellValue('E1', 'F. PROCESO')
                    ->setCellValue('F1', 'FECHA HORA')
                    ->setCellValue('G1', 'UNIDADES')
                    ->setCellValue('H1', 'T. CAJAS')
                    ->setCellValue('I1', 'No GUIA')
                    ->setCellValue('J1', 'TRANSPORTADORA');
        $i = 2;
        
        foreach ($tableexcel as $val) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $val->id_packing)
                ->setCellValue('B' . $i, $val->pedido->numero_pedido)
                ->setCellValue('C' . $i, $val->numero_packing)
                ->setCellValue('D' . $i, $val->cliente->nombrecorto)
                ->setCellValue('E' . $i, $val->fecha_proceso)
                ->setCellValue('F' . $i, $val->fecha_hora_registro)
                ->setCellValue('G' . $i, $val->cantidad_despachadas)
                ->setCellValue('H' . $i, $val->total_cajas)
                ->setCellValue('I' . $i, $val->numero_guia)
                ->setCellValue('J' . $i, $val->transportadora->razon_social);
              
        $i++;
             
        }

        $objPHPExcel->getActiveSheet()->setTitle('Packing');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Packing.xlsx"');
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
