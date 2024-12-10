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
    public function actionCreate()
    {
        $model = new DocumentoSoporte();
        
        $conCompra = Compra::find()->orderBy('id_compra DESC')->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $compra = Compra::findOne($model->id_compra);
            $model->user_name = Yii::$app->user->identity->username;
            $model->documento_compra = $compra->factura;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id_documento_soporte]);
        }

        return $this->render('create', [
            'model' => $model,
            'conCompra' => \yii\helpers\ArrayHelper::map($conCompra, 'id_compra', 'Compras'),
        ]);
    }

    //proceso que llena el combo de cta de cobro
     
    public function actionCargarcompras($id){
        $rows = Compra::find()->where(['=','id_proveedor', $id])->andWhere(['=','genera_documento_soporte', 1])
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
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_documento_soporte]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DocumentoSoporte model.
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
