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
//
use app\models\SalidaBodega;
use app\models\SalidaBodegaDetalle;
use app\models\UsuarioDetalle;
use app\models\CostoProducto;
/**
 * SalidaBodegaController implements the CRUD actions for SalidaBodega model.
 */
class SalidaBodegaController extends Controller
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
     * Lists all SalidaBodega models.
     * @return mixed
     */
     public function actionIndex($token = 0) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',137])->all()){
                $form = new \app\models\FormFiltroSalidaBodega();
                $codigo_producto = null;
                $referencia = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $ConReferencia = CostoProducto::find()->where(['=','entregado', 0])->orderBy('id_producto DESC')->all();
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {                        
                        $referencia = Html::encode($form->referencia);
                        $codigo_producto = Html::encode($form->codigo_producto);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $table = SalidaBodega::find()
                                ->andFilterWhere(['=', 'codigo_producto', $codigo_producto])
                                ->andFilterWhere(['=', 'id_producto', $referencia])
                                ->andFilterWhere(['>=', 'fecha_salida', $fecha_inicio]) 
                                ->andFilterWhere(['<=', 'fecha_salida', $fecha_corte]);
                       $table = $table->orderBy('id_salida_bodega DESC');
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
                                $check = isset($_REQUEST['id_salida_bodega DESC']);
                                $this->actionExcelconsultaSalidaBodega($tableexcel);
                            }
                } else {
                        $form->getErrors();
                }                    
            } else {
                $table = SalidaBodega::find()
                        ->orderBy('id_salida_bodega DESC');
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
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                     $this->actionExcelconsultaSalidaBodega($tableexcel);
                }
            }
            $to = $count->count();
            return $this->render('index', [
                        'model' => $model,
                        'form' => $form,
                        'pagination' => $pages,
                        'token' => $token,
                        'ConReferencia' => ArrayHelper::map($ConReferencia, 'id_producto', 'productos'),
            ]);
        }else{
             return $this->redirect(['site/sinpermiso']);
        }     
        }else{
           return $this->redirect(['site/login']);
        }
   }

    /**
     * Displays a single SalidaBodega model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token)
    {
        $listado_insumos = \app\models\SalidaBodegaDetalle::find()->where(['=','id_salida_bodega', $id])->all();
        //PROCESO QUE ELIMA LOS REGISTROS
        if (Yii::$app->request->post()) {
          if (isset($_POST["eliminar_todo"])) {
              if (isset($_POST["listado_eliminar"])) {
                  foreach ($_POST["listado_eliminar"] as $intCodigo) {
                      try {
                          $eliminar = \app\models\SalidaBodegaDetalle::findOne($intCodigo);
                          $eliminar->delete();
                          Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con exito.');
                          $this->redirect(["salida-bodega/view", 'id' => $id, 'token' => $token]);
                      } catch (IntegrityException $e) {

                          Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                      } catch (\Exception $e) {
                          Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');

                      }
                  }
              } else {
                  Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro.');
              }    
           }
        }    
        
        //PROCESO QUE ACTUALIZAR EL INVENTARIO
        if(isset($_POST["actualizar_inventario"])){
           if(isset($_POST["materia_prima"])){
                $intIndice = 0;
                $cantidad = 0;
                foreach ($_POST["materia_prima"] as $intCodigo):
                    //buscamos la existencia del insumo
                    $buscar = \app\models\SalidaBodegaDetalle::findOne($intCodigo);
                    $insumo = \app\models\Insumos::findOne($buscar->id_insumo); 
                    //validamos la existencia;
                    $cantidad = $_POST["cantidad_despachar"]["$intIndice"];
                    if($cantidad <= $insumo->stock_real){
                        $buscar->cantidad_despachar = $_POST["cantidad_despachar"]["$intIndice"];
                        $buscar->nota = $_POST["observacion"]["$intIndice"];
                        $buscar->save (false);
                        $intIndice++;
                    }else{
                        Yii::$app->getSession()->setFlash('warning', 'No hay existencia del insumo ('.$insumo->descripcion.')');
                        $intIndice++;  
                    }
                   
               endforeach;
               $this->Actualizar_unidades($id);
              return $this->redirect(['view','id' =>$id, 'token' => $token]);
           }

        }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'token' => $token,
            'listado_insumos' => $listado_insumos,
        ]);
    }
    //PROCESO QUE ACTUALIZAR LAS UNIDADES
    protected function Actualizar_unidades($id) {
        $model = $this->findModel($id);
        $modelo = SalidaBodegaDetalle::find()->where(['=','id_salida_bodega', $id])->all();
        $contar = 0;
        foreach ($modelo as $val):
            $contar += $val->cantidad_despachar;
        endforeach;
        $model->unidades = $contar;
        $model->save();
    }

    /**
     * Creates a new SalidaBodega model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SalidaBodega();
        $ConReferencia = CostoProducto::find()->where(['=','entregado', 0])->orderBy('id_producto DESC')->all();
        if ($model->load(Yii::$app->request->post())) {
            $salida = SalidaBodega::find()->all();
            $sw = 0;
            foreach ($salida as $bodega){
              
                if($model->id_producto == $bodega->id_producto){
                    $sw = 1;
                } 
            }
            if($sw == 0){
                $model->save();
                $producto = CostoProducto::findOne($model->id_producto);
                $model->codigo_producto = $producto->codigo_producto;
                $model->user_name = Yii::$app->user->identity->username;
                $model->save();
                return $this->redirect(['view', 'id' => $model->id_salida_bodega,'token' => 0]);
            }else{
                Yii::$app->getSession()->setFlash('error', 'La referencia seleccionada se encuentra en un proceso de salida de insumos o ya esta despachada. Valide la informacion.');
            }    
        }

        return $this->render('create', [
            'model' => $model,
            'ConReferencia' => ArrayHelper::map($ConReferencia, 'id_producto', 'productos'),
        ]);
    }
    
   //PERMITE CARGAR LOS INSUMOS DE LA ORDEN DE COSTO
   public function actionCargar_insumos($id, $token, $id_producto) {
        $cargar = \app\models\CostoProductoDetalle::find()->where(['=','id_producto', $id_producto])->all();  
        if(count($cargar) > 0){
            foreach ($cargar as $insumo):
                $ingreso = \app\models\SalidaBodegaDetalle::find()->where(['=','id_salida_bodega', $id])->andWhere(['=','id_insumo', $insumo->id_insumos])->one();
                if(!$ingreso){         
                    $table = new \app\models\SalidaBodegaDetalle();
                    $table->id_salida_bodega = $id;
                    $table->id_insumo = $insumo->id_insumos;
                    $table->codigo_insumo = $insumo->codigo_insumo;
                    $table->nombre_insumo = $insumo->insumos->descripcion;
                    $table->save (false);
                }    
            endforeach;
            return $this->redirect(['view', 'id' => $id,'token' => 0]);
        }else{
            Yii::$app->getSession()->setFlash('error', 'Esta orden de costo No tiene relacionado los insumos para la confeccion. Validar con el administrador.');
            return $this->redirect(['view', 'id' => $id,'token' => 0]);
        }
       
   }
    /**
     * Updates an existing SalidaBodega model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $ConReferencia = CostoProducto::find()->where(['=','entregado', 0])->orderBy('id_producto DESC')->all();
        if ($model->load(Yii::$app->request->post())) {
            $producto = CostoProducto::findOne($model->id_producto);
            $model->id_producto = $model->id_producto;
            $model->codigo_producto = $producto->codigo_producto;
            $model->responsable = $model->responsable;
            $model->fecha_salida = $model->fecha_salida;
            $model->save();
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'ConReferencia' => ArrayHelper::map($ConReferencia, 'id_producto', 'productos'),
        ]);
    }
    
    //AUTORIZAR EL PROCESO
    public function actionAutorizado($id, $token) {
        $model = $this->findModel($id);
        if($model->unidades > 0){
            if($model->autorizado == 0){
                 $model->autorizado = 1;
                 $model->save();
            }else{
                 $model->autorizado = 0;
                 $model->save();
            }
        }else{
            Yii::$app->getSession()->setFlash('error', 'Debe de ingresar las cantidades a despachar para el proceso de confeccion.');
            return $this->redirect(['view', 'id' => $id,'token' => $token]);
        }    
        return $this->redirect(['view', 'id' => $id,'token' => $token]);
    }
    
    //CERRA EL PROCESO DE SALIDA Y CREA CONSECUTIVO
    public function actionCerrar_despacho($id, $token) {
        $model = $this->findModel($id);
        $producto = CostoProducto::findOne($model->id_producto);
        $numero = \app\models\Consecutivo::findOne(16);
        $consecutivo = $numero->consecutivo + 1;
        //actualiza el model
        $model->numero_salida = $consecutivo;
        $model->proceso_cerrado = 1;
        $model->save();
        //actualiza el consecutivo
        $numero->consecutivo = $consecutivo;
        $numero->save();
        //cierrar la referencia
        $producto->entregado = 1;
        $producto->save(false);
        return $this->redirect(['view', 'id' => $id,'token' => $token]);
    }

    //ENVIA EL INVENTARIO PARA SER DESCARGADO DEL  MODULO DE INSUMOS
    public function actionEnviar_inventario($id, $token) {
        $model = $this->findModel($id);
        $detalle = SalidaBodegaDetalle::find()->where(['=','id_salida_bodega', $id])->all();
        $contar = 0;
        foreach ($detalle as $val):
            $inventario = \app\models\Insumos::findOne($val->id_insumo);
            if($inventario){
                if($inventario->aplica_inventario == 1){
                   $contar += 1;
                   $inventario->stock_real -= $val->cantidad_despachar;
                   $inventario->save();
                }
            }
        endforeach;
        $model->exportar_inventario = 1;
        $model->save();
        Yii::$app->getSession()->setFlash('info', 'Se exportaron ('.$contar.') referencias de materias primas con Exito al modulo de insumos.');
    }
       /**
     * Finds the SalidaBodega model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalidaBodega the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalidaBodega::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
