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
//MODELS
use app\models\AsignacionProducto;
use app\models\AsignacionProductoSearch;
use app\models\UsuarioDetalle;
use app\models\FormFiltroAsignacionProducto;
use app\models\CostoProducto;

/**
 * AsignacionProductoController implements the CRUD actions for AsignacionProducto model.
 */
class AsignacionProductoController extends Controller
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
     * Lists all AsignacionProducto models.
     * @return mixed
     */
     public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',123])->all()){
                $form = new FormFiltroAsignacionProducto();
                $fecha_asignacion = null;
                $fecha_corte = null;
                $proveedor = null;
                $documento = null;
                $tipoOrden = null;
                $orden_produccion = null;
                $autorizado = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $proveedor = Html::encode($form->proveedor);
                        $fecha_asignacion = Html::encode($form->fecha_asignacion);
                        $documento = Html::encode($form->documento);
                        $tipoOrden = Html::encode($form->tipoOrden);
                        $orden_produccion = Html::encode($form->orden_produccion);
                        $autorizado = Html::encode($form->autorizado);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $table = AsignacionProducto::find()
                                ->andFilterWhere(['=', 'idproveedor', $proveedor])
                                ->andFilterWhere(['>=', 'fecha_asignacion', $fecha_asignacion])
                                ->andFilterWhere(['<=', 'fecha_asignacion', $fecha_corte])   
                                ->andFilterWhere(['=', 'documento', $documento])  
                                ->andFilterWhere(['=', 'orden_produccion', $orden_produccion])
                                ->andFilterWhere(['=', 'idtipo', $tipoOrden])
                                ->andFilterWhere(['=', 'autorizado', $autorizado]);  
                       $table = $table->orderBy('id_asignacion DESC');
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
                                $check = isset($_REQUEST['id_asignacion DESC']);
                                $this->actionExcelconsultaAsignacion($tableexcel);
                            }
                } else {
                        $form->getErrors();
                }                    
            } else {
                $table = AsignacionProducto::find()
                        ->orderBy('id_asignacion DESC');
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
                    $this->actionExcelconsultaAsignacion($tableexcel);
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
     * Displays a single AsignacionProducto model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AsignacionProducto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AsignacionProducto();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $provedor = \app\models\Proveedor::findOne($model->idproveedor);
            $model->documento = $provedor->cedulanit;
            $model->razon_social = $provedor->nombrecorto;
            $model->usuario = Yii::$app->user->identity->username;
            $model->save(false);
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AsignacionProducto model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $provedor = \app\models\Proveedor::findOne($model->idproveedor);
            $model->documento = $provedor->cedulanit;
            $model->razon_social = $provedor->nombrecorto;
            $model->usuario_editado = Yii::$app->user->identity->username;
            $model->fecha_editado = date('Y-m-d');
            $model->save(false);
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    //PERMITE BUSCAR LOS PRODUCTOS PARA ASIGNAR
    
       public function actionBuscarproducto($id)
    {
        $productos = CostoProducto::find()->where(['=','asignado', 0])->orderBy('descripcion ASC')->all();
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
        return $this->renderAjax('_crearasignacionproducto', [
            'id' => $id,
            'productos' => $productos,
        ]);      
    }
    /**
     * Deletes an existing AsignacionProducto model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AsignacionProducto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AsignacionProducto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AsignacionProducto::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
