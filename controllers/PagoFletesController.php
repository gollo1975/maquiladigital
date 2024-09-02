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
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;

use app\models\PagoFletes;
use app\models\PagoFletesSearch;
use app\models\UsuarioDetalle;


/**
 * PagoFletesController implements the CRUD actions for PagoFletes model.
 */
class PagoFletesController extends Controller
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
     * Lists all PagoFletes models.
     * @return mixed
     */
     public function actionIndex() {
         if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',144])->all()){
            $form = new \app\models\FormFiltroDescargueFlete();
            $proveedor = null;
            $desde = null;
            $hasta = null;
            $numero = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $proveedor = Html::encode($form->proveedor);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $numero = Html::encode($form->numero);
                    $table = PagoFletes::find()
                            ->andFilterWhere(['=', 'idproveedor', $proveedor])
                            ->andFilterWhere(['between', 'fecha_pago', $desde, $hasta])
                            ->andFilterWhere(['=', 'numero_pago', $numero]);
                    $table = $table->orderBy('id_pago desc');
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
                   
                } else {
                    $form->getErrors();
                }
            } else {
                $table = PagoFletes::find()
                        ->orderBy('id_pago desc');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 15,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
               
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
     * Displays a single PagoFletes model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
       $listado_fletes = \app\models\PagoFleteDetalle::find()->where(['=','id_pago', $id])->all();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'listado_fletes' => $listado_fletes,
        ]);
    }

    /**
     * Creates a new PagoFletes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PagoFletes();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->user_name = Yii::$app->user->identity->username;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id_pago]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PagoFletes model.
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
     * Deletes an existing PagoFletes model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    
    
    //PROCESO QUE LOS DESPACHOS POR PROVEEDOR
    public function actionListar_fletes($id, $id_proveedor) {
        $listado = \app\models\Despachos::find()->where(['=','pagado', 0])
                                                ->andWhere(['=','idproveedor', $id_proveedor])
                                                ->orderBy('id_despacho DESC')->all();
        $form = new \app\models\FormMaquinaBuscar();
        $q = null;
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                if ($q){
                    $listado = \app\models\Despachos::find()
                            ->where(['=','numero_despacho', $q])
                            ->andWhere(['=','pagado', 0])
                            ->andWhere(['=','idproveedor', $id_proveedor])
                            ->orderBy('id_despacho DESC')->all();
                }               
            } else {
                $form->getErrors();
            }                    

        } else {
             $listado = \app\models\Despachos::find()->where(['=','pagado', 0])
                                                     ->andWhere(['=','idproveedor', $id_proveedor])
                                                     ->orderBy('id_despacho DESC')->all();
        }
        if (isset($_POST["listado_despachos"])) {
            $intIndice = 0;
            foreach ($_POST["listado_despachos"] as $intCodigo) {
                $despacho = \app\models\Despachos::find()->where(['id_despacho' => $intCodigo])->one();
                $detalle = \app\models\PagoFleteDetalle::find()
                    ->where(['=', 'id_pago', $id])
                    ->andWhere(['=', 'id_despacho', $despacho->id_despacho])
                    ->all();
                $reg = count($detalle);
                if ($reg == 0) {
                    $table = new \app\models\PagoFleteDetalle();
                    $table->id_pago = $id;
                    $table->id_despacho= $intCodigo;
                    $table->valor_flete = $despacho->valor_flete;
                    $table->save(false); 
                }
             $intIndice++;   
            }
            $this->TotalizarDespachos($id);
           $this->redirect(["pago-fletes/view", 'id' => $id]);
        }
        return $this->render('_listar_despachos', [
            'listado' => $listado,            
            'id' => $id,
            'id_proveedor' => $id_proveedor,
            'form' => $form,

        ]);
    }
    
    //PROCESO QUE TOTALIZA 
    protected function TotalizarDespachos($id) {
        $model = PagoFletes::findOne($id);
        $detalle = \app\models\PagoFleteDetalle::find()->where(['=','id_pago', $id])->all();
        $suma = 0;
        foreach ($detalle as $pagos):
            $suma += $pagos->valor_flete;
        endforeach;
        $model->total_pagado = $suma;
        $model->save();
        
    }

    public function actionEliminar($id, $id_detalle)
    {
        try {
            $model = \app\models\PagoFleteDetalle::findOne($id_detalle);
            $model->delete();
            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
            $this->TotalizarDespachos($id);
           $this->redirect(["pago-fletes/view",'id' => $id]);
        } catch (IntegrityException $e) {
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el registro, tiene registros asociados en otros procesos');
            $this->redirect(["pago-fletes/view",'id' => $id]);
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el registro, tiene registros asociados en otros procesos');
            $this->redirect(["pago-fletes/view",'id' => $id]);
        }
    }
    //PROCESO QUE AUTORIZADO
    public function actionAutorizado($id) {
        $despacho = PagoFletes::findOne($id);
        $detalle = \app\models\PagoFleteDetalle::find()->where(['=','id_pago', $id])->all();
        if(count($detalle) > 0){
            if($despacho->autorizado == 0){
                $despacho->autorizado = 1;
                $despacho->save();
            }else{
                $despacho->autorizado = 0;
                $despacho->save();
            } 
            return $this->redirect(['pago-fletes/view', 'id' => $id]);
        }else{
             Yii::$app->getSession()->setFlash('error', 'Debe de agregar los pagos a este proveedor para autorizar el proceso.');
             return $this->redirect(['pago-fletes/view', 'id' => $id]);
        }    
    }
    
    //CIERRA EL DESPACHO AL PROVEEDOR
     public function actionCerrar_pago($id) {
        $model = PagoFletes::findOne($id);
         //generar consecutivo
        $registro = \app\models\Consecutivo::findOne(21);
        $valor = $registro->consecutivo + 1;
        $model->numero_pago = $valor;
        $model->proceso_cerrado = 1;
        $model->save();
        //actualiza consecutivo
        $registro->consecutivo = $valor;
        $registro->save();
       
        return $this->redirect(['pago-fletes/view', 'id' => $id]); 
    }
     
    
    /**
     * Finds the PagoFletes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PagoFletes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PagoFletes::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
