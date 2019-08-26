<?php

namespace app\controllers;

use Yii;
use app\models\DocumentoEquivalente;
use app\models\DocumentoEquivalenteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
        $searchModel = new DocumentoEquivalenteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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
            $model->subtotal = $model->valor;
            $model->update();
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
