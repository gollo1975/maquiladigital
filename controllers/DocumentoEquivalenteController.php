<?php

namespace app\controllers;

use Yii;
use app\models\DocumentoEquivalente;
use app\models\DocumentoEquivalenteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UsuarioDetalle;

/**
 * DocumentoEquivalenteController implements the CRUD actions for DocumentoEquivalente model.
 */
class DocumentoEquivalenteController extends Controller
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
     * Lists all DocumentoEquivalente models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',54])->all()){
                $searchModel = new DocumentoEquivalenteSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
             }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
    }

    /**
     * Displays a single DocumentoEquivalente model.
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
     * Creates a new DocumentoEquivalente model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DocumentoEquivalente();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->porcentaje == 0 or $model->porcentaje == ''){                
                $model->retencion_fuente = 0;
            }else{                
                $model->retencion_fuente = $model->valor * $model->porcentaje / 100;                
            }
            $model->generar_comprobante = $model->generar_comprobante;
            $model->subtotal = $model->valor;
            $model->update();
            if( $model->generar_comprobante == 1){
                $proveedor = \app\models\Proveedor::find()->where(['=','cedulanit', $model->identificacion])->one();
                if($proveedor){
                    $matricula = \app\models\Matriculaempresa::findOne(1);
                    $tabla = new \app\models\ComprobanteEgreso();
                    $tabla->id_municipio = $model->idmunicipio;
                    $tabla->fecha_comprobante = $model->fecha;
                    $tabla->id_comprobante_egreso_tipo = $matricula->codigo_concepto_compra;
                    $tabla->id_proveedor = $proveedor->idproveedor;
                    $tabla->usuariosistema = Yii::$app->user->identity->username;
                    $tabla->id_banco = $matricula->id_banco_factura;
                    $tabla->libre = 1;
                    $tabla->observacion = $model->descripcion;
                    $tabla->save(false);
                    return $this->redirect(['view', 'id' => $model->consecutivo]);
                }else{
                   Yii::$app->getSession()->setFlash('warning', 'No se puede generar el comprobante de egreso porque el documento no existe como proveedor.' ); 
                }    
            }
            return $this->redirect(['view', 'id' => $model->consecutivo]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DocumentoEquivalente model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->porcentaje == 0 or $model->porcentaje == ''){                
                $model->retencion_fuente = 0;
                
            }else{                
                $model->retencion_fuente = $model->valor * $model->porcentaje / 100;                
            }
            $model->subtotal = $model->valor;
            $model->update();
           return $this->redirect(['view', 'id' => $model->consecutivo]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DocumentoEquivalente model.
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
     * Finds the DocumentoEquivalente model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DocumentoEquivalente the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DocumentoEquivalente::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionImprimir($id)
    {
                                
        return $this->render('../formatos/documentoEquivalente', [
            'model' => $this->findModel($id),
            
        ]);
    }
}
