<?php

namespace app\controllers;

use Yii;
use app\models\CostoFijo;
use app\models\Parametros;
use app\models\CostoFijoDetalle;
use app\models\CostoFijoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use app\models\UsuarioDetalle;

/**
 * CostoFijoController implements the CRUD actions for CostoFijo model.
 */
class CostoFijoController extends Controller
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

    public function actionNuevodetalle()
    {
        
        if(Yii::$app->request->post()){
            $id = Html::encode($_POST["id_costo_fijo"]);
            $table = new CostoFijoDetalle();
            $table->descripcion = Html::encode($_POST["descripcion"]);
            $table->valor = Html::encode($_POST["valor"]);  
            $table->id_planta = Html::encode($_POST["id_planta"]); 
            $table->id_costo_fijo = $id;
            $table->aplica_concepto = isset($_POST["aplica_concepto"]) ? 1 : 0;
            if($table->save()){
                $this->Totales($id);
                $this->redirect(["costo-fijo/costofijodetalle",'id' => $id]);   
            }
            
                                      
        }
        
    }
    
    public function actionEditardetalle()
    {
        
        if(Yii::$app->request->post()){
            $id = Html::encode($_POST["id_costo_fijo"]);
            $iddetalle = Html::encode($_POST["id_detalle_costo_fijo"]);
            $table = CostoFijoDetalle::findOne($iddetalle);
            $table->descripcion = Html::encode($_POST["descripcion"]);
            $table->valor = Html::encode($_POST["valor"]);    
            $table->id_planta = Html::encode($_POST["id_planta"]);
           $table->aplica_concepto = isset($_POST["aplica_concepto"]) ? 1 : 0;
            if($table->save()){;
                $this->Totales($id);
                $this->redirect(["costo-fijo/costofijodetalle",'id' => $id]);
            }    
        }
        
    }
    
    public function actionEliminar($iddetalle)
    {                                
        $costofijodetalle = CostoFijoDetalle::findOne($iddetalle);
        $costofijodetalle->delete();
        $this->Totales(1);
        $this->redirect(["costo-fijo/costofijodetalle",'id' => 1]);        
    }
    
    public function actionCostofijodetalle($id) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',19])->all()){        
                $costofijo = CostoFijo::findOne($id);
                $costofijodetalle = CostoFijoDetalle::find()->where(['=', 'id_costo_fijo', $id])->orderBy('id_planta ASC')->all();
                return $this->render('costofijodetalle', [
                            'costofijo' => $costofijo,
                            'costofijodetalle' => $costofijodetalle,
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }
    }
          
    protected function Totales($id)
    {        
        $detalles = CostoFijoDetalle::find()->where(['=','id_costo_fijo',$id])->all();        
        $total = 0;
            foreach ($detalles as $val){                                                
                $total = $total + $val->valor;
        }        
        $costofijo = CostoFijo::findOne($id);        
        $costofijo->valor = $total;
        $costofijo->update();
}
}