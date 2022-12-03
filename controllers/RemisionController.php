<?php

namespace app\controllers;

use Yii;
use app\models\Remision;
use app\models\Remisiondetalle;
use app\models\Ordenproduccion;
use app\models\Ordenproducciondetalle;
use app\models\Consecutivo;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UsuarioDetalle;

/**
 * FichatiempoController implements the CRUD actions for Fichatiempo model.
 */
class RemisionController extends Controller
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
     * Lists all Fichatiempo models.
     * @return mixed
     */
    public function actionIndex()
    {
        /*if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',32])->all()){
            $searchModel = new FichatiempoSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }else{
            return $this->redirect(['site/sinpermiso']);
        }*/
    }

    /**
     * Displays a single Fichatiempo model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRemision($id)
    {
        if (Yii::$app->request->post()) {
            $remision = Remision::find()->where(['=', 'idordenproduccion', $id])->one();
            $count = 0;
            if ($remision){
                $model = $remision;
                $remisiondetalle = Remisiondetalle::find()->where(['=','id_remision',$remision->id_remision])->all();
                $count = count($remisiondetalle);
                $detalleorden = Ordenproducciondetalle::find()->where(['=','idordenproduccion',$id])->all();
                $cxs = 0; $cs = 0; $cm = 0; $cl = 0; $cxl = 0; $cxxl = 0; $ct = 0; $c2 = 0; $c4 = 0; $c6 = 0; $c8 = 0; $c10 = 0; $c12 = 0; $c14 = 0; $c16 = 0; $c18 = 0;
                $c20 = 0; $c22 = 0; $c28 = 0; $c30 = 0; $c32 = 0; $c34 = 0; $c36 = 0; $c38 = 0; $c42 = 0;
                $ct = $cxs + $cs + $cm + $cl + $cxl + $cxxl + $c2 + $c4 + $c6 + $c8 + $c10 + $c12 + $c14 + $c16 + $c18 + $c20 + $c22 + $c28 + $c30 + $c32 + $c34 + $c36 + $c38 + $c42;
                $datostallas = null;
            }else{
                $cxs = 0; $cs = 0; $cm = 0; $cl = 0; $cxl = 0; $cxxl = 0; $ct = 0; $c2 = 0; $c4 = 0; $c6 = 0; $c8 = 0; $c10 = 0; $c12 = 0; $c14 = 0; $c16 = 0; $c18 = 0;
                $c20 = 0; $c22 = 0; $c28 = 0; $c30 = 0; $c32 = 0; $c34 = 0; $c36 = 0; $c38 = 0; $c42 = 0; 
                $table = new Remision();
                $table->idordenproduccion = $id;
                $table->total_tulas = 0;
                $table->total_exportacion = 0;
                $table->totalsegundas = 0;
                $table->total_colombia = 0;
                $table->total_confeccion = 0;
                $table->total_despachadas = 0;
                $table->fechacreacion = date('Y-m-d');
                $table->id_color = $_POST['color'];
                $color = \app\models\Color::find()->where(['=','id',$table->id_color])->one();
                $table->color = $color->color;
                $table->insert();
                $model = Remision::findOne($table->id_remision);
                $remisiondetalle = Remisiondetalle::find()->where(['=','id_remision',$id])->all();
                $count = count($remisiondetalle);
                $ct = $cxs + $cs + $cm + $cl + $cxl + $cxxl + $c2 + $c4 + $c6 + $c8 + $c10 + $c12 + $c14 + $c16 + $c18 + $c20 + $c22 + $c28 + $c30 + $c32 + $c34 + $c36 + $c38 + $c42;
                $datostallas = null;
            }
            if (isset($_POST["actualizar"])) {
                $intIndice = 0;
                foreach ($_POST["id_remision_detalle"] as $intCodigo) {                
                    $table = Remisiondetalle::findOne($intCodigo);
                    $table->id_color = $_POST["color"][$intIndice];
                    $color = \app\models\Color::find()->where(['=','id',$_POST["color"][$intIndice]])->one();
                    $table->color = $color->color;
                    $table->oc = $_POST["oc"][$intIndice];
                    $table->tula = $_POST["tula"][$intIndice];
                    if ($table->txs == 1){
                        $table->xs = $_POST["xs"][$intIndice];
                    }
                    if ($table->ts == 1){
                        $table->s = $_POST["s"][$intIndice];
                    }
                    if ($table->tm == 1){
                        $table->m = $_POST["m"][$intIndice];
                    }
                    if ($table->tl == 1){
                        $table->l = $_POST["l"][$intIndice];
                    }
                    if ($table->txl == 1){
                        $table->xl = $_POST["xl"][$intIndice];
                    }
                    if ($table->txxl == 1){
                        $table->xxl = $_POST["xxl"][$intIndice];
                    }
                    if ($table['t2'] == 1){
                        $table['2'] = $_POST["2"][$intIndice];
                    }
                    if ($table['t4'] == 1){
                        $table['4'] = $_POST["4"][$intIndice];
                    }
                    if ($table['t6'] == 1){
                        $table['6'] = $_POST["6"][$intIndice];
                    }
                    if ($table['t8'] == 1){
                        $table['8'] = $_POST["8"][$intIndice];
                    }
                    if ($table['t10'] == 1){
                        $table['10'] = $_POST["10"][$intIndice];
                    }
                    if ($table['t12'] == 1){
                        $table['12'] = $_POST["12"][$intIndice];
                    }
                    if ($table['t14'] == 1){
                        $table['14'] = $_POST["14"][$intIndice];
                    }
                    if ($table['t16'] == 1){
                        $table['16'] = $_POST["16"][$intIndice];
                    }
                    if ($table['t18'] == 1){
                        $table['18'] = $_POST["18"][$intIndice];
                    }
                    if ($table['t20'] == 1){
                        $table['20'] = $_POST["20"][$intIndice];
                    }
                    if ($table['t22'] == 1){
                        $table['22'] = $_POST["22"][$intIndice];
                    }                    
                    if ($table['t28'] == 1){
                        $table['28'] = $_POST["28"][$intIndice];
                    }
                    if ($table['t30'] == 1){
                        $table['30'] = $_POST["30"][$intIndice];
                    }
                    if ($table['t32'] == 1){
                        $table['32'] = $_POST["32"][$intIndice];
                    }
                    if ($table['t34'] == 1){
                        $table['34'] = $_POST["34"][$intIndice];
                    }
                    if ($table['t36'] == 1){
                        $table['36'] = $_POST["36"][$intIndice];
                    }
                    if ($table['t38'] == 1){
                        $table['38'] = $_POST["38"][$intIndice];
                    }
                    if ($table['t42'] == 1){
                        $table['42'] = $_POST["42"][$intIndice];
                    }
                    $table->estado = $_POST["estado"][$intIndice];
                    $table->save(false);
                    $this->Calculos($table);
                    $intIndice++;
                }
                $this->totales($id);
                $datostallas = null;                
                return $this->redirect(['remision', 'id' => $id]);
            }
            if (isset($_POST["actualizarynuevo"])) {
                $intIndice = 0;
                foreach ($_POST["id_remision_detalle"] as $intCodigo) {                
                    $table = Remisiondetalle::findOne($intCodigo);
                    $table->id_color = $_POST["color"][$intIndice];
                    $color = \app\models\Color::find()->where(['=','id',$_POST["color"][$intIndice]])->one();
                    $table->color = $color->color;
                    $table->oc = $_POST["oc"][$intIndice];
                    $table->tula = $_POST["tula"][$intIndice];
                    if ($table->txs == 1){
                        $table->xs = $_POST["xs"][$intIndice];
                    }
                    if ($table->ts == 1){
                        $table->s = $_POST["s"][$intIndice];
                    }
                    if ($table->tm == 1){
                        $table->m = $_POST["m"][$intIndice];
                    }
                    if ($table->tl == 1){
                        $table->l = $_POST["l"][$intIndice];
                    }
                    if ($table->txl == 1){
                        $table->xl = $_POST["xl"][$intIndice];
                    }
                    if ($table->txxl == 1){
                        $table->xxl = $_POST["xxl"][$intIndice];
                    }
                    if ($table['t2'] == 1){
                        $table['2'] = $_POST["2"][$intIndice];
                    }
                    if ($table['t4'] == 1){
                        $table['4'] = $_POST["4"][$intIndice];
                    }
                    if ($table['t6'] == 1){
                        $table['6'] = $_POST["6"][$intIndice];
                    }
                    if ($table['t8'] == 1){
                        $table['8'] = $_POST["8"][$intIndice];
                    }
                    if ($table['t10'] == 1){
                        $table['10'] = $_POST["10"][$intIndice];
                    }
                    if ($table['t12'] == 1){
                        $table['12'] = $_POST["12"][$intIndice];
                    }
                    if ($table['t14'] == 1){
                        $table['14'] = $_POST["14"][$intIndice];
                    }
                    if ($table['t16'] == 1){
                        $table['16'] = $_POST["16"][$intIndice];
                    }
                    if ($table['t18'] == 1){
                        $table['18'] = $_POST["18"][$intIndice];
                    }
                    if ($table['t20'] == 1){
                        $table['20'] = $_POST["20"][$intIndice];
                    }
                    if ($table['t22'] == 1){
                        $table['22'] = $_POST["22"][$intIndice];
                    }                    
                    if ($table['t28'] == 1){
                        $table['28'] = $_POST["28"][$intIndice];
                    }
                    if ($table['t30'] == 1){
                        $table['30'] = $_POST["30"][$intIndice];
                    }
                    if ($table['t32'] == 1){
                        $table['32'] = $_POST["32"][$intIndice];
                    }
                    if ($table['t34'] == 1){
                        $table['34'] = $_POST["34"][$intIndice];
                    }
                    if ($table['t36'] == 1){
                        $table['36'] = $_POST["36"][$intIndice];
                    }
                    if ($table['t38'] == 1){
                        $table['38'] = $_POST["38"][$intIndice];
                    }
                    if ($table['t42'] == 1){
                        $table['42'] = $_POST["42"][$intIndice];
                    }
                    $table->estado = $_POST["estado"][$intIndice]; 
                    $table->save(false);
                    $this->Calculos($table);
                    $intIndice++;
                }
                $this->totales($id);
                $this->actionNuevodetalle($remision->id_remision,$remision->idordenproduccion);
                $datostallas = null;
                return $this->redirect(['remision', 'id' => $id]);
            }
            
        }else{
            $datostallas = null;
            $remision = Remision::find()->where(['=', 'idordenproduccion', $id])->one();
            $count = 0;
            if ($remision){
                $model = $remision;
                $remisiondetalle = Remisiondetalle::find()->where(['=','id_remision',$remision->id_remision])->all();
                $count = count($remisiondetalle);
            }            
            $cxs = 0; $cs = 0; $cm = 0; $cl = 0; $cxl = 0; $cxxl = 0; $ct = 0; $c2 = 0; $c4 = 0; $c6 = 0; $c8 = 0; $c10 = 0; $c12 = 0; $c14 = 0; $c16 = 0; $c18 = 0;
            $c20 = 0; $c22 = 0; $c28 = 0; $c30 = 0; $c32 = 0; $c34 = 0; $c36 = 0; $c38 = 0; $c42 = 0;
            $tallasremision = Remisiondetalle::find()->where(['=','id_remision',$remision->id_remision])->one();
            $cantidadesremision = Remisiondetalle::find()->where(['=','id_remision',$remision->id_remision])->all();
            if ($cantidadesremision){
                foreach ($cantidadesremision as $val){
                    if ($val->txs == 1){
                        $cxs = $cxs+ $val->xs;
                    }
                    if ($val->ts == 1){
                        $cs = $cs + $val->s;
                    }
                    if ($val->tm == 1){
                        $cm = $cm + $val->m;
                    }
                    if ($val->tl == 1){
                        $cl = $cl + $val->l;
                    }
                    if ($val->txl == 1){
                        $cxl = $cxl + $val->xl;
                    }
                    if ($val->txxl == 1){
                        $cxxl = $cxxl + $val->xxl;
                    }
                    if ($val->t2 == 1){
                        $c2 = $c2 + $val['2'];
                    }
                    if ($val->t4 == 1){
                        $c4 = $c4 + $val['4'];
                    }
                    if ($val->t6 == 1){
                        $c6 = $c6 + $val['6'];
                    }
                    if ($val->t8 == 1){
                        $c8 = $c8 + $val['8'];
                    }
                    if ($val->t10 == 1){
                        $c10 = $c10 + $val['10'];
                    }
                    if ($val->t12 == 1){
                        $c12 = $c12 + $val['12'];
                    }
                    if ($val->t14 == 1){
                        $c14 = $c14 + $val['14'];
                    }
                    if ($val->t16 == 1){
                        $c16 = $c16 + $val['16'];
                    }
                    if ($val->t18 == 1){
                        $c18 = $c18 + $val['18'];
                    }
                    if ($val->t20 == 1){
                        $c20 = $c20 + $val['20'];
                    }
                    if ($val->t22 == 1){
                        $c22 = $c22 + $val['22'];
                    }
                    if ($val->t28 == 1){
                        $c28 = $c28 + $val['28'];
                    }
                    if ($val->t30 == 1){
                        $c30 = $c30 + $val['30'];
                    }
                    if ($val->t32 == 1){
                        $c32 = $c32 +$val['32'];
                    }
                    if ($val->t34 == 1){
                        $c34 = $c34 + $val['34'];
                    }
                    if ($val->t36 == 1){
                        $c36 = $c36 + $val['36'];
                    }
                    if ($val->t38 == 1){
                        $c38 = $c38 + $val['38'];
                    }
                    if ($val->t42 == 1){
                        $c42 = $c42 + $val['42'];
                    }
                }
            }
            if ($tallasremision){
                if ($tallasremision->txs == 1){
                    $datostallas[] = 'XS';
                }
                if ($tallasremision->ts == 1){
                    $datostallas[] = 'S';
                }
                if ($tallasremision->tm == 1){
                    $datostallas[] = 'M';
                }
                if ($tallasremision->tl == 1){
                    $datostallas[] = 'L';
                }
                if ($tallasremision->txl == 1){
                    $datostallas[] = 'XL';
                }
                 if ($tallasremision->txxl == 1){
                    $datostallas[] = 'XXL';
                }
                if ($tallasremision->t2 == 1){
                    $datostallas[] = '2';
                }
                if ($tallasremision->t4 == 1){
                    $datostallas[] = '4';
                }
                if ($tallasremision->t6 == 1){
                    $datostallas[] = '6';
                }
                if ($tallasremision->t8 == 1){
                    $datostallas[] = '8';
                }
                if ($tallasremision->t10 == 1){
                    $datostallas[] = '10';
                }
                if ($tallasremision->t12 == 1){
                    $datostallas[] = '12';
                }
                if ($tallasremision->t14 == 1){
                    $datostallas[] = '14';
                }
                if ($tallasremision->t16 == 1){
                    $datostallas[] = '16';
                }
                if ($tallasremision->t18 == 1){
                    $datostallas[] = '18';
                }
                if ($tallasremision->t20 == 1){
                    $datostallas[] = '20';
                }
                if ($tallasremision->t22 == 1){
                    $datostallas[] = '22';
                }
                if ($tallasremision->t28 == 1){
                    $datostallas[] = '28';
                }
                if ($tallasremision->t30 == 1){
                    $datostallas[] = '30';
                }
                if ($tallasremision->t32 == 1){
                    $datostallas[] = '32';
                }
                if ($tallasremision->t34 == 1){
                    $datostallas[] = '34';
                }
                if ($tallasremision->t36 == 1){
                    $datostallas[] = '36';
                }
                if ($tallasremision->t38 == 1){
                    $datostallas[] = '38';
                }
                if ($tallasremision->t42 == 1){
                    $datostallas[] = '42';
                }
            }
            
            $ct = $cxs + $cs + $cm + $cl + $cxl + $cxxl + $c2; $c4 + $c6 + $c8 + $c10 + $c12 + $c14 + $c16 + $c18 + $c20 + $c22 + $c28 + $c30 + $c32 + $c34 + $c36 + $c38 + $c42;
        }    
        
        return $this->render('remision', [
            'model' => $model,
            'remisiondetalle' => $remisiondetalle,
            'idordenproduccion' => $id,
            //'detalleorden' =>$detalleorden,
            'datostallas' => $datostallas,
            //'cantidades' => $cantidades,
            'count' => $count,
            'cxs' => $cxs, 'cs' => $cs, 'cm' => $cm, 'cl' => $cl, 'cxl' => $cxl, 'cxxl' => $cxxl, 'c2' => $c2,'c4' => $c4, 'c6' => $c6, 'c8' => $c8, 'c10' => $c10, 'c12' => $c12, 'c14' => $c14, 'c16' => $c16, 'c18' => $c18, 'c20' => $c20, 'c22' => $c22, 'c28' => $c28,'c30' => $c30,'c32' => $c32,'c34' => $c34, 'c36' => $c36,'c38' => $c38, 'c42' => $c42,
            'ct' => $ct,
        ]);
    }

        
    public function actionNuevodetalle($id,$idordenproduccion)
    {        
        $remision = Remision::findOne($id);
        $model = new Remisiondetalle();
        $model->id_remision = $id;
        $model->tula = 1;
        $model->id_color = $remision->id_color;
        $model->color = $remision->color;
        $detalleorden = Ordenproducciondetalle::find()->where(['=','idordenproduccion',$idordenproduccion])->all();
        foreach ($detalleorden as $val){
            $talla = 't'.strtolower($val->productodetalle->prendatipo->talla->talla);
            $model->$talla = 1;
        }
        $model->save(false);
        return $this->redirect(['remision', 'id' => $idordenproduccion]);
    }
    
    public function actionEliminar($id,$iddetalle)
    {                                
        $detalle = Remisiondetalle::findOne($iddetalle);
        $detalle->delete();        
        $this->totales($id);
        $this->redirect(["remision",'id' => $id]);
      
    }
    
    protected function Calculos($table)
    {                
        $table->unidades = $table->xs + $table->s + $table->m + $table->l + $table->xl + $table->xxl + $table['2'] + $table['4'] + $table['6'] + $table['8'] + $table['10'] + $table['12'] + $table['14'] + $table['16'] + $table['18'] + $table['20'] + $table['22'] + $table['28'] + $table['30'] + $table['32'] + $table['34'] + $table['36'] + $table['38'] + $table['42'];
        $table->save(false);
    }
    
    protected function Totales($id)
    {        
        $remision = Remision::find()->where(['=','idordenproduccion',$id])->one();
        $detalles = Remisiondetalle::find()->where(['=','id_remision',$remision->id_remision])->all();
        $totaltulas = 0;
        $totalexportacion = 0;
        $totalsegundas = 0;
        $totalcolombia = 0;
        $totalconfeccion = 0;
        $totaldespachadas = 0;
        foreach ($detalles as $val){
            $totaltulas = $totaltulas + $val->tula;
            if ($val->oc == 0){ //colombia
                $totalcolombia = $totalcolombia + $val->unidades;
            }
            if ($val->oc == 1){ //exportacion
                $totalexportacion = $totalexportacion + $val->unidades;
            }
            if ($val->estado == 1){ //segundas
                $totalsegundas = $totalsegundas + $val->unidades;
            }
            $totalconfeccion = $totalconfeccion + $val->unidades;
        }                
        $remision->total_tulas = $totaltulas;
        $remision->total_colombia = $totalcolombia;
        $remision->total_exportacion = $totalexportacion;
        $remision->totalsegundas = $totalsegundas;
        $remision->total_confeccion = $totalconfeccion;
        $remision->total_despachadas = $totalconfeccion;
        $remision->update();
    }
    
    public function actionGenerarnro($id)
    {
        $model = Remision::findOne($id);
        $mensaje = "";
        
        $detalle = Remisiondetalle::find()->where(['=','id_remision',$id])->all();
        $count = count($detalle);
        if ($count > 0) {                    
            if ($model->numero == 0){
                $consecutivo = Consecutivo::findOne(4);// 4 Remision de entrega
                $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
                $model->numero = $consecutivo->consecutivo;
                $model->save(false);
                $consecutivo->update();                                
                //$this->afectarcantidadfacturada($id);//se resta o descuenta las cantidades facturadas en los productos por cliente
                $this->redirect(["remision/remision",'id' => $model->idordenproduccion]);
            }else{
                Yii::$app->getSession()->setFlash('error', 'El registro ya fue generado.');
                $this->redirect(["remision/remision",'id' => $model->idordenproduccion]);
            }
        }else{
            Yii::$app->getSession()->setFlash('error', 'El registro no tiene detalles, no se puede generar numero');
            $this->redirect(["remision/remision",'id' => $model->idordenproduccion]);
        }    
        
    }
    
    public function actionFechamodificar($id){
        
        $remision = Remision::find()->where(['=', 'idordenproduccion', $id])->one();
        $remision->fechacreacion = $_POST['fecha'];
        $remision->save(false);
        $this->redirect(["remision/remision",'id' => $id]);
      
    }

    public function actionImprimir($id) {

        return $this->render('../formatos/remision', [
            'model' => Remision::findOne($id),
        ]);
    }
     //PROCESO QUE CIERRA LA REMISION
     public function actionCerrarremision($id) {
         $cerrar = Remision::findOne($id);
         $cerrar->cerrar_remision = 1;
         $cerrar->save(false);
         $this->redirect(["remision/remision",'id' => $cerrar->idordenproduccion]);
     }
}
