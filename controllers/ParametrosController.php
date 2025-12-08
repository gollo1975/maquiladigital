<?php

namespace app\controllers;

use Yii;
use app\models\Parametros;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UsuarioDetalle;

/**
 * ArlController implements the CRUD actions for Arl model.
 */
class ParametrosController extends Controller
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
     * Updates an existing Arl model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionParametros($id)
    {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',29])->all()){
                $model = $this->findModel($id);
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    $this->ValorDiaEmpleado($id);
                    return $this->redirect(['parametros','id' => $id]);
                }

                return $this->render('parametros', [
                    'model' => $model,
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }
        
    }
    
    protected function ValorDiaEmpleado($id) {
        $model = $this->findModel($id);
        $empresa = \app\models\Matriculaempresa::findOne(1);
        $total_salario = 0;
        // 1. seguridad social
        
        $pension = round(($model->salario_minimo * $model->pension)/100);
        $arl =    round(($model->salario_minimo * $model->arl->arl)/100);      
        $caja =  round(($model->salario_minimo * $model->caja)/100);
        
        // 2. prestaciones
        $cesantia_prima_interes = round((($model->salario_minimo +  $model->auxilio_transporte) * $model->prestaciones)/100);
        $vacacion =  round(($model->salario_minimo * $model->vacaciones)/100);
        $ajuste =  round(($vacacion *  $model->ajuste)/100);
        
        // 3. totales 
        $total_seguridad =  $pension + $arl + $caja;
        $total_prestaciones = $cesantia_prima_interes + $vacacion +  $ajuste;
        $total_salario =  $total_seguridad +  $total_prestaciones + $model->salario_minimo + $model->auxilio_transporte;     
        $valor_dia = round($total_salario / $empresa->dias_trabajados);
        $model->valor_dia_empleado = $valor_dia;
        $model->save(false);
    }
    

    /**
     * Finds the Arl model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Arl the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Parametros::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
