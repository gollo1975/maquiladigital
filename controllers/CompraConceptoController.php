<?php

namespace app\controllers;

use Yii;
use app\models\CompraConcepto;
use app\models\CompraConceptoSearch;
use app\models\CompraTipo;
use app\models\CompraConceptoCuenta;
use app\models\CuentaPub;
use app\models\FormCompraConceptoCuentaNuevo;
use app\models\FormCompraConceptoDetalleEditar;
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
use app\models\UsuarioDetalle;

/**
 * CompraConceptoController implements the CRUD actions for CompraConcepto model.
 */
class CompraConceptoController extends Controller
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
     * Lists all CompraConcepto models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',36])->all()){
                $searchModel = new CompraConceptoSearch();
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
     * Displays a single CompraConcepto model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $modeldetalles = CompraConceptoCuenta::find()->Where(['=', 'id_compra_concepto', $id])->all();
        $registros = count($modeldetalles);
        if (Yii::$app->request->post()) {
            if (isset($_POST["eliminar"])) {
                if (isset($_POST["id_compra_concepto_cuenta"])) {
                    foreach ($_POST["id_compra_concepto_cuenta"] as $intCodigo) {
                        try {
                            $eliminar = CompraConceptoCuenta::findOne($intCodigo);
                            $eliminar->delete();
                            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                            $this->redirect(["compra-concepto/view", 'id' => $id]);
                        } catch (IntegrityException $e) {
                            //$this->redirect(["producto/view", 'id' => $id]);
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                        } catch (\Exception $e) {
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                            //$this->redirect(["producto/view", 'id' => $id]);
                        }
                    }
                    //$this->redirect(["producto/view", 'id' => $id]);
                }
            } else {
                    Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro.');
                    $this->redirect(["facturaventatipo/view", 'id' => $id]);
                   }
        }        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modeldetalles' => $modeldetalles,
            'registros' => $registros,
        ]);
    }

    /**
     * Creates a new CompraConcepto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CompraConcepto();
        $tipos = CompraTipo::find()->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'tipos' => ArrayHelper::map($tipos, "id_compra_tipo", "tipo"),
        ]);
    }

    /**
     * Updates an existing CompraConcepto model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $tipos = CompraTipo::find()->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'tipos' => ArrayHelper::map($tipos, "id_compra_tipo", "tipo"),
        ]);
    }

    /**
     * Deletes an existing CompraConcepto model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
            $this->redirect(["compra-concepto/index"]);
        } catch (IntegrityException $e) {
            $this->redirect(["compra-concepto/index"]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar concepto, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar concepto, tiene registros asociados en otros procesos');
            $this->redirect(["compra-concepto/index"]);
        }
    }
    
    public function actionNuevodetalles($id_compra_concepto)
    {
       
        $model = new FormCompraConceptoCuentaNuevo();
        $cuentas = CuentaPub::find()->all();         
        if ($model->load(Yii::$app->request->post())) {
            $cuenta = CuentaPub::find()->where(['=','codigo_cuenta',$model->cuenta])->one();
            
            if ($cuenta){
                $table = new CompraConceptoCuenta;
                $table->id_compra_concepto = $id_compra_concepto;
                $table->cuenta = $model->cuenta;
                $table->tipocuenta = $model->tipocuenta;
                $table->base = $model->base;
                $table->subtotal = $model->subtotal;
                $table->iva = $model->iva;
                $table->rete_fuente = $model->rete_fuente;
                $table->rete_iva = $model->rete_iva;
                $table->total = $model->total;
                $table->base_rete_fuente = $model->base_rete_fuente;
                $table->porcentaje_base = $model->porcentaje_base;
                $table->save(false);
                $this->redirect(["compra-concepto/view", 'id' => $id_compra_concepto]);
            }else{                
                Yii::$app->getSession()->setFlash('error', 'No exite la cuenta que desea ingresar, verificar en las cuentas del PUB!');
            }
            
        }

        return $this->render('_formnuevodetalles', [
            'model' => $model,
            'cuentas' => ArrayHelper::map($cuentas, "codigo_cuenta", "cuentanombre"),
            'id' => $id_compra_concepto
        ]);
    }
    
    public function actionEditardetalle($id_compra_concepto_cuenta) {
        $model = new FormCompraConceptoDetalleEditar;
        $cuentas = CuentaPub::find()->all();        
        $compraconceptocuenta = CompraConceptoCuenta::findOne($id_compra_concepto_cuenta);
        
        if ($model->load(Yii::$app->request->post())) {                        
            $compraconceptocuenta->cuenta = $model->cuenta;
            $compraconceptocuenta->tipocuenta = $model->tipocuenta;
            $compraconceptocuenta->base = $model->base;
            $compraconceptocuenta->subtotal = $model->subtotal;
            $compraconceptocuenta->iva = $model->iva;
            $compraconceptocuenta->rete_fuente = $model->rete_fuente;
            $compraconceptocuenta->rete_iva = $model->rete_iva;
            $compraconceptocuenta->total = $model->total;
            $compraconceptocuenta->base_rete_fuente = $model->base_rete_fuente;
            $compraconceptocuenta->porcentaje_base = $model->porcentaje_base;
            $compraconceptocuenta->save(false);                                      
            return $this->redirect(['compra-concepto/view','id' => $compraconceptocuenta->id_compra_concepto]);
        }
        if (Yii::$app->request->get("id_compra_concepto_cuenta")) {
            $table = CompraConceptoCuenta::find()->where(['id_compra_concepto_cuenta' => $id_compra_concepto_cuenta])->one();
            if ($table) {
                $model->cuenta = $table->cuenta;
                $model->tipocuenta = $table->tipocuenta;
                $model->base = $table->base;
                $model->subtotal = $table->subtotal;
                $model->iva = $table->iva;
                $model->rete_fuente = $table->rete_fuente;
                $model->rete_iva = $table->rete_iva;
                $model->total = $table->total;
                $model->base_rete_fuente = $table->base_rete_fuente;
                $model->porcentaje_base = $table->porcentaje_base;
            }    
        }
        return $this->render('_formeditardetalle', [
            'model' => $model,
            'compraconceptocuenta' => $compraconceptocuenta,
            'cuentas' => ArrayHelper::map($cuentas, "codigo_cuenta", "cuentanombre"),
        ]);        
    }
    
    

    /**
     * Finds the CompraConcepto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CompraConcepto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CompraConcepto::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
