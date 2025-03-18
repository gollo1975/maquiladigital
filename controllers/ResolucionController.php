<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;
use yii\helpers\Html;
//models
use app\models\Resolucion;
use app\models\ResolucionSearch;
use app\models\Matriculaempresa;
use app\models\UsuarioDetalle;


/**
 * ResolucionController implements the CRUD actions for Resolucion model.
 */
class ResolucionController extends Controller
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
     * Lists all Resolucion models.
     * @return mixed
     */
       public function actionIndex( $token = 0) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',7])->all()){
                $form = new \app\models\FormFiltroResolucion();
                $estado = null;
                $tipo_documento = null;
                $numero = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $estado = Html::encode($form->estado);
                        $tipo_documento = Html::encode($form->tipo_documento);
                        $numero = Html::encode($form->numero);
                        $table = Resolucion::find()
                                ->andFilterWhere(['=', 'nroresolucion', $numero])                                                                                              
                                ->andFilterWhere(['=', 'id_documento', $tipo_documento])
                                ->andFilterWhere(['=','activo', $estado]);
                        $table = $table->orderBy('idresolucion DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 10,
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
                $table = Resolucion::find()
                        ->orderBy('idresolucion DESC');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 10,
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
                        'token' => $token,
            ]);
        }else{
             return $this->redirect(['site/sinpermiso']);
        }     
        }else{
           return $this->redirect(['site/login']);
        }
   }

    /**
     * Displays a single Resolucion model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'token' => $token,
        ]);
    }

    /**
     * Creates a new Resolucion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Resolucion();

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Resolucion model.
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
     * Finds the Resolucion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Resolucion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Resolucion::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
