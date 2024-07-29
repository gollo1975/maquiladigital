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
                
        return $this->render('view', [
            'model' => $this->findModel($id),
            'lista_precio' => $lista_precio,
        ]);
    }

    /**
     * Creates a new ReferenciaProducto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ReferenciaProducto();
        

        if ($model->load(Yii::$app->request->post())) {
            $empresa = \app\models\Matriculaempresa::findOne(1);
            $codigo = $this->CrearCodigoReferencia();
            $model->codigo = $codigo;
            $model->descripcion_referencia = $model->descripcion_referencia;
            $model->id_tipo_producto = $model->id_tipo_producto;
            $model->user_name = Yii::$app->user->identity->username;
            $model->save();
            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
            
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
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
