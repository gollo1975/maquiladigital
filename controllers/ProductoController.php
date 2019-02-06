<?php

namespace app\controllers;

use Yii;
use app\models\Producto;
use app\models\ProductoSearch;
use app\models\Productodetalle;
use app\models\Cliente;
use app\models\Prendatipo;
use app\models\Ordenproducciontipo;
use app\models\Stockdescargas;
use app\models\FormFiltroProductoStock;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\db\ActiveQuery;
use yii\base\Model;
use yii\web\Response;
use yii\web\Session;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\bootstrap\Modal;
use app\models\UsuarioDetalle;
use app\models\FormProductosDetallesNuevo;

/**
 * ProductoController implements the CRUD actions for Producto model.
 */
class ProductoController extends Controller
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
     * Lists all Producto models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',16])->all()){
            $searchModel = new ProductoSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }else{
            return $this->redirect(['site/sinpermiso']);
        }    
    }

    /**
     * Displays a single Producto model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $modeldetalles = Productodetalle::find()->Where(['=', 'idproducto', $id])->all();
        
        if (Yii::$app->request->post()) {
            if (isset($_POST["eliminar"])) {
                if (isset($_POST["idproductodetalle"])) {
                    foreach ($_POST["idproductodetalle"] as $intCodigo) {
                        try {
                            $eliminar = Productodetalle::findOne($intCodigo);
                            $eliminar->delete();
                            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                            $this->redirect(["producto/view", 'id' => $id]);
                        } catch (IntegrityException $e) {
                            //$this->redirect(["producto/view", 'id' => $id]);
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                        } catch (\Exception $e) {
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                            //$this->redirect(["producto/view", 'id' => $id]);
                        }
                    }
                    //$this->redirect(["producto/view", 'id' => $id]);
                }
            } else {
                    Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro.');
                    $this->redirect(["producto/view", 'id' => $id]);
                   }
        }        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modeldetalles' => $modeldetalles,
        ]);
    }

    /**
     * Creates a new Producto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Producto();
        $clientes = Cliente::find()->all();
        //$prendas = Prendatipo::find()->all();
        //$ordentipos = Ordenproducciontipo::find()->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->usuariosistema = Yii::$app->user->identity->username;
            //$model->stock = $model->cantidad;
            $model->update();
            return $this->redirect(['view', 'id' => $model->idproducto]);
        }

        return $this->render('create', [
            'model' => $model,
            'clientes' => ArrayHelper::map($clientes,'idcliente','nombreClientes'),
            //'prendas' => ArrayHelper::map($prendas,'idprendatipo','nombreProducto'),
            //'ordentipos' => ArrayHelper::map($ordentipos,'idtipo','tipo')
        ]);
    }

    /**
     * Updates an existing Producto model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        /*if($model->cantidad > $model->stock and $model->stock > 0){
            Yii::$app->getSession()->setFlash('warning', 'Hay stock que no fueron descontados en la facturacion, no se generó la orden completa, generar el descargue de las unidades');
        }*/
        $clientes = Cliente::find()->all();
        //$prendas = Prendatipo::find()->all();
        //$ordentipos = Ordenproducciontipo::find()->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            /*if($model->cantidad > $model->stock and $model->stock > 0){
                Yii::$app->getSession()->setFlash('warning', 'Hay stock que no fueron descontados en la facturacion, no se generó la orden completa, generar el descargue de las unidades');
            }else{
                //$model->stock = $model->cantidad;
                //$model->update();
                                
            } */
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'clientes' => ArrayHelper::map($clientes,'idcliente','nombreClientes'),
            //'prendas' => ArrayHelper::map($prendas,'idprendatipo','nombreProducto'),
            //'ordentipos' => ArrayHelper::map($ordentipos,'idtipo','tipo')
        ]);
    }

    /**
     * Deletes an existing Producto model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
            $this->redirect(["producto/index"]);
        } catch (IntegrityException $e) {
            $this->redirect(["producto/index"]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el producto, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el producto, tiene registros asociados en otros procesos');
            $this->redirect(["producto/index"]);
        }
    }
    
    public function actionNuevodetalles($idproducto)
    {
        $prendas = Prendatipo::find()->orderBy('prenda asc')->all();
        $form = new FormProductosDetallesNuevo;
        $q = null;
        $mensaje = '';
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                if ($q){
                    //$model = Notas::find()->where(['identificacion' => $identificacion])->andWhere(['<>','matricula',0])->all();
                    $prendas = Prendatipo::find()
                            ->where(['like','prenda',$q])
                            ->orwhere(['like','idprendatipo',$q])
                            ->orderBy('prenda asc')
                            ->all();
                            
                }               
            } else {
                $form->getErrors();
            }                    
                    
                       
        } else {
            $prendas = Prendatipo::find()->orderBy('prenda asc')->all();
        }
        if (isset($_POST["idprendatipo"])) {
                $intIndice = 0;
                foreach ($_POST["idprendatipo"] as $intCodigo) {
                    $table = new Productodetalle();
                    $prenda = Prendatipo::find()->where(['idprendatipo' => $intCodigo])->one();
                    $detalles = Productodetalle::find()
                        ->where(['=', 'idproducto', $idproducto])
                        ->andWhere(['=', 'idprendatipo', $prenda->idprendatipo])
                        ->all();
                    $reg = count($detalles);
                    if ($reg == 0) {
                        $table->idprendatipo = $prenda->idprendatipo;
                        $table->observacion = ".";
                        $table->idproducto = $idproducto;
                        $table->usuariosistema = Yii::$app->user->identity->username;
                        $table->insert();                                                
                    }
                }
                $this->redirect(["producto/view", 'id' => $idproducto]);
            }else{
                
            }
        return $this->render('_formnuevodetalles', [
            'prendas' => $prendas,            
            'mensaje' => $mensaje,
            'idproducto' => $idproducto,
            'form' => $form,

        ]);
    }
    
    public function actionProductostock() {
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',21])->all()){
            $form = new FormFiltroProductoStock();
            $idcliente = null;
            $idproducto = null;
            $idtipo = null;
            $clientes = Cliente::find()->all();
            $tipos = Ordenproducciontipo::find()->all();
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $idtipo = Html::encode($form->idtipo);
                    $idproducto = Html::encode($form->idproducto);
                    $table = Producto::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['=', 'idtipo', $idtipo])
                            ->andFilterWhere(['=', 'idproducto', $idproducto])
                            //->andWhere("cantidad > stock")
                            //->andWhere(['<>','stock', 0])                        
                            ->orderBy('idproducto desc');
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
                } else {
                    $form->getErrors();
                }
            } else {
                $table = Producto::find()
                    //->where("cantidad > stock")
                    //->andWhere(['<>','stock', 0])    
                    ->orderBy('idcliente desc');
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 200,
                    'totalCount' => $count->count(),
                ]);           
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
            }
        return $this->render('productos_stock', [
                        'model' => $model,
                        'form' => $form,
                        'pagination' => $pages,
                        'clientes' => ArrayHelper::map($clientes, "idcliente", "nombrecorto"),
                        'tipos' => ArrayHelper::map($tipos, "idtipo", "tipo"),
            ]);
         }else{
            return $this->redirect(['site/sinpermiso']);
        }
    }
    
    public function actionDescargarstock() {
        $idproducto = Html::encode($_POST["idproducto"]);
        $stock = Html::encode($_POST["stock"]);        
        if (Yii::$app->request->post()) {

            if ((int) $idproducto) {
                $table = Producto::findOne($idproducto);                
                if ($table) {
                    $table->stock = 0;
                    $table->update();
                    $descargarstock = new Stockdescargas();
                    $descargarstock->stock = Html::encode($_POST["stock"]);
                    $descargarstock->idfactura = Html::encode($_POST["idfactura"]);
                    $descargarstock->idproducto = Html::encode($_POST["idproducto"]);
                    $descargarstock->idordenproduccion = Html::encode($_POST["idordenproduccion"]);
                    $descargarstock->nrofactura = Html::encode($_POST["nrofactura"]);
                    $descargarstock->observacion = Html::encode($_POST["observacion"]);
                    $descargarstock->insert();
                    $this->redirect(["producto/productostock"]);
                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }
            }
        }
        //return $this->render("_formeditardetalle", ["model" => $model,]);
    }

    /**
     * Finds the Producto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Producto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Producto::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
