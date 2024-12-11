<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\helpers\Html;
//models
use app\models\DocumentoSoporte;
use app\models\DocumentoSoporteSearch;
use app\models\UsuarioDetalle;
use app\models\Proveedor;
use app\models\Compra;


/**
 * DocumentoSoporteController implements the CRUD actions for DocumentoSoporte model.
 */
class DocumentoSoporteController extends Controller
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
     * Lists all DocumentoSoporte models.
     * @return mixed
     */
     public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',148])->all()){
                $form = new \app\models\FormFiltroDocumentoSoporte();
                $proveedor = null;
                $numero_compra = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $numero_soporte = null;
                $conProveedor = Proveedor::find()->orderBy('nombrecorto ASC')->all();
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $proveedor = Html::encode($form->proveedor);
                        $numero_compra = Html::encode($form->numero_compra);
                        $numero_soporte = Html::encode($form->numero_soporte);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $table = DocumentoSoporte::find()
                                ->andFilterWhere(['=', 'idproveedor', $proveedor])
                                ->andFilterWhere(['like', 'documento_compra', $numero_compra])
                                ->andFilterWhere(['=', 'numero_soporte', $numero_soporte])
                                ->andFilterWhere(['between', 'fecha_elaboracion', $fecha_inicio, $fecha_corte]);
                        $table = $table->orderBy('id_documento_soporte desc');
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
                            $this->actionExcelconsultaDocumentos($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = DocumentoSoporte::find()
                            ->orderBy('id_documento_soporte desc');
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 15,
                        'totalCount' => $count->count(),
                    ]);
                    $tableexcel = $table->all();
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){                    
                            $this->actionExcelconsultaDocumentos($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'model' => $model,
                            'form' => $form,
                            'pagination' => $pages,
                            'conProveedor' => \yii\helpers\ArrayHelper::map($conProveedor, 'idproveedor', 'nombrecorto'),
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }
    }

    /**
     * Displays a single DocumentoSoporte model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $detalles = \app\models\DocumentoSoporteDetalle::find()->where(['=','id_documento_soporte', $id])->all();
        //ACTUALIZA LINEAS DEL DOCUMENTO SOPORTE
        if(isset($_POST["ActualizarLineas"])){
            if(isset($_POST["listado"])){
                $intIndice = 0;
                foreach ($_POST["listado"] as $intCodigo):
                    $documento = DocumentoSoporte::findOne($id);
                    $codigo = $_POST["id_concepto"][$intIndice];
                    $porcentaje = $_POST["id_retencion"][$intIndice];
                    if($porcentaje > 0){
                        $retenciones = \app\models\RetencionFuente::findOne($porcentaje);
                    }    
                    $concepto = \app\models\ConceptoDocumentoSoporte::findOne($codigo);
                    $table = \app\models\DocumentoSoporteDetalle::findOne($intCodigo);
                    $table->id_concepto = $codigo;
                    $table->descripcion = $concepto->concepto;
                    $table->cantidad = $_POST["cantidad"][$intIndice];
                    $table->valor_unitario = $_POST["valor_unitario"][$intIndice];
                    $table->id_retencion = $_POST["id_retencion"][$intIndice];
                    $suma = ($table->cantidad *  $table->valor_unitario);
                    if($porcentaje > 0){
                       $table->porcentaje_retencion = $retenciones->porcentaje; 
                       $subtotal = round(($suma * $table->porcentaje_retencion)/100);
                       $table->valor_retencion = $subtotal;
                       $table->total_pagar = $suma - $subtotal;
                    }else{
                        $table->porcentaje_retencion = 0;
                         $table->valor_retencion = 0;
                         $table->total_pagar = $suma;
                    }
                    $table->save(false);
                    $documento->valor_pagar = $table->total_pagar;
                    $documento->save();
                    $intIndice++;
                endforeach;
                
               return $this->redirect(['view','id' =>$id]);
            }
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
            'detalles' => $detalles,
        ]);
    }

    /**
     * Creates a new DocumentoSoporte model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($sw)
    {
        $model = new DocumentoSoporte();
        
        $conCompra = Compra::find()->orderBy('id_compra DESC')->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if($sw == 1){
                $compra = Compra::findOne($model->id_compra);
                $model->documento_compra = $compra->factura;
            }
            $model->user_name = Yii::$app->user->identity->username;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id_documento_soporte]);
        }

        return $this->render('create', [
            'model' => $model,
            'sw' => $sw,
            'conCompra' => \yii\helpers\ArrayHelper::map($conCompra, 'id_compra', 'Compras'),
        ]);
    }

    //proceso que llena el combo de cta de cobro
     
    public function actionCargarcompras($id){
        $rows = Compra::find()->where(['=','id_proveedor', $id])->andWhere(['=','genera_documento_soporte', 1])->andWhere(['=','documento_generado', 0])
                                              ->orderBy('fechainicio ASC')->all();

        echo "<option value='' required>Seleccione el documento...</option>";
        if(count($rows)>0){
            foreach($rows as $row){
                echo "<option value='$row->id_compra' required>$row->Compras</option>";
            }
        }
    }
     
    /**
     * Updates an existing DocumentoSoporte model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $sw)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_documento_soporte]);
        }

        return $this->render('update', [
            'model' => $model,
            'sw' => $sw,
        ]);
    }

    //CREA UNA NUEVA LINEA
    public function actionNueva_linea($id) {
        $table = new \app\models\DocumentoSoporteDetalle();
        $table->id_documento_soporte = $id;
        $table->cantidad = 1;
        $table->save(false);
        return $this->redirect(['view', 'id' => $id]);
    }
    
    /**
     * Deletes an existing DocumentoSoporte model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $id_detalle)
    {
        $dato = \app\models\DocumentoSoporteDetalle::findOne($id_detalle);
        $dato->delete();
        return $this->redirect(['view', 'id' => $id]);
    }
    
    //PROCESO QUE AUTORIZA
    public function actionAutorizado($id) {
         $model = $this->findModel($id);
        if(\app\models\DocumentoSoporteDetalle::find()->where(['=','id_documento_soporte', $id])->one()){
            if($model->autorizado == 0){
                $model->autorizado = 1;
                $model->save();
                return $this->redirect(['view', 'id' => $id]);
            }else{
                $model->autorizado = 0;
                $model->save();
                return $this->redirect(['view', 'id' => $id]);
            }
        }else{
            Yii::$app->getSession()->setFlash('error', 'Debe de creaun una nueva linea para subir el registro completo'); 
            return $this->redirect(['view', 'id' => $id]);
        }
    }
    
    //proceso que genera el consecutivo
    public function actionGenerar_documento($id) {
        $model = $this->findModel($id);
        $codigo = \app\models\Consecutivo::findOne(22);
        $numero = $codigo->consecutivo + 1;
        $codigo->consecutivo = $numero;
        $codigo->save();
        //model
        $model->numero_soporte = $numero;
        $model->save();
        if($model->id_compra != ''){
            $compra = Compra::findOne($model->id_compra);
            $compra->documento_generado = 1;
            $compra->save();
        }
        return $this->redirect(['view', 'id' => $id]);
    }
    
    /**
     * Finds the DocumentoSoporte model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DocumentoSoporte the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DocumentoSoporte::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
