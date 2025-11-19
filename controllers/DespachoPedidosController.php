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

    /**
     * Displays a single DespachoPedidos model.
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
     * Creates a new DespachoPedidos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DespachoPedidos();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_despacho]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    //importar ordenes de produccion
   public function actionImportar_pedidos() {
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
        ]);
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
            return $this->redirect(['view', 'id' => $model->id_despacho]);
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
