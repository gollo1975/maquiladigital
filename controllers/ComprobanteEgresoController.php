<?php

namespace app\controllers;

use Yii;
use app\models\ComprobanteEgreso;
use app\models\ComprobanteEgresoSearch;
use app\models\Proveedor;
use app\models\ComprobanteEgresoTipo;
use app\models\ComprobanteEgresoDetalle;
use app\models\Municipio;
use app\models\Consecutivo;
use app\models\UsuarioDetalle;
use app\models\Banco;
use app\models\Compra;
use app\models\FormComprobanteegresolibre;
use app\models\FormComprobanteegresonuevodetallelibre;
use app\models\FormFiltroConsultaComprobanteegreso;
use app\models\ModelCrearComprobantes;

//clases
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
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


/**
 * ComprobanteEgresoController implements the CRUD actions for ComprobanteEgreso model.
 */
class ComprobanteEgresoController extends Controller
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
     * Lists all ComprobanteEgreso models.
     * @return mixed
     */
     public function actionIndex($token = 0) {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',38])->all()){
            $form = new FormFiltroConsultaComprobanteegreso();
            $idproveedor = null;
            $desde = null;
            $hasta = null;
            $numero = null;
            $tipo = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idproveedor = Html::encode($form->idproveedor);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $numero = Html::encode($form->numero);
                    $tipo = Html::encode($form->tipo);
                    $table = ComprobanteEgreso::find()
                            ->andFilterWhere(['=', 'id_proveedor', $idproveedor])
                            ->andFilterWhere(['>=', 'fecha_comprobante', $desde])
                            ->andFilterWhere(['<=', 'fecha_comprobante', $hasta])
                            ->andFilterWhere(['=', 'numero', $numero])
                            ->andFilterWhere(['=', 'id_comprobante_egreso_tipo', $tipo]);
                    $table = $table->orderBy('id_comprobante_egreso desc');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 20,
                        'totalCount' => $count->count()
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){
                        //$table = $table->all();
                        $this->actionExcelconsulta($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = ComprobanteEgreso::find()
                        ->orderBy('id_comprobante_egreso desc');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 20,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                    $this->actionExcelconsulta($tableexcel);
                }
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
    
    
    //PROCESO QUE IMPORTAR DOCUMENTOS CONCTABLES
     public function actionImportardocumento() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso', 155])->all()){
                $form = new ModelCrearComprobantes();
                $tipo_proceso = null;
                $fecha_inicio = null;
                $fecha_final = null;
                $tipo_comprobante = null;
                $fecha_pago = null;
                $banco = null;
                $model = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $tipo_proceso = Html::encode($form->tipo_proceso);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_final = Html::encode($form->fecha_final);
                        $fecha_pago = Html::encode($form->fecha_pago);
                        $tipo_comprobante = Html::encode($form->tipo_comprobante);
                        $banco = Html::encode($form->banco);
                        if($tipo_proceso == 1){
                            $nomina = \app\models\ProgramacionNomina::find()->where(['>=','fecha_desde', $fecha_inicio])
                                                                            ->andWhere(['<=', 'fecha_hasta', $fecha_final])->all();
                            $model = $nomina;
                        }else{
                            if($tipo_proceso == 2){
                                $servicio = \app\models\PagoNominaServicios::find()->where(['>=','fecha_inicio', $fecha_inicio])
                                                                                   ->andWhere(['<=','fecha_corte', $fecha_final])->orderBy('operario ASC')->all();   
                                $model = $servicio;
                            }else{
                                if($tipo_proceso == 3){
                                    $cesantia = \app\models\ProgramacionNomina::find()->where(['=','fecha_desde', $fecha_inicio])
                                                                                ->andWhere(['=', 'fecha_hasta', $fecha_final])->all();
                                    $model = $cesantia;
                                }else{
                                    $primas = \app\models\ProgramacionNomina::find()->where(['=','fecha_desde', $fecha_inicio])
                                                                                ->andWhere(['=', 'fecha_hasta', $fecha_final])->all();
                                    $model = $primas;
                                }    
                            }    
                        }    
                        
                    } else {
                        $form->getErrors();
                    }
                                
                } 
                if (isset($_POST["creardocumento"])) {
                    if (isset($_POST["documento_identidad"])) {
                        $intIndice = 0;
                        $observacion = '';
                        $cont = 0;
                        $valorPagado = 0;
                        foreach ($_POST["documento_identidad"] as $intCodigo) {
                            $empresa = \app\models\Matriculaempresa::findOne(1);
                            if($tipo_proceso == 1 || $tipo_proceso == 3 || $tipo_proceso == 4){
                                $nomina = \app\models\ProgramacionNomina::findOne($intCodigo);
                                $proveedor = Proveedor::find()->where(['=','cedulanit', $nomina->cedula_empleado])->one();
                                $valorPagado = $nomina->total_pagar;
                            }else{
                                $nomina = \app\models\PagoNominaServicios::findOne($intCodigo);
                                $proveedor = Proveedor::find()->where(['=','cedulanit', $nomina->documento])->one();
                                $valorPagado = $nomina->total_pagar;
                            }
                            if($proveedor){
                                $table = new ComprobanteEgreso();
                                $table->id_municipio = $empresa->idmunicipio;
                                $table->fecha_comprobante = $fecha_pago ;
                                $table->id_comprobante_egreso_tipo = $tipo_comprobante;
                                $table->id_proveedor = $proveedor->idproveedor;
                                $table->id_banco = $banco;
                                if($tipo_proceso == 1){
                                     $observacion = 'Nomina del  '. $fecha_inicio. '  al  ' .$fecha_final. ''; 
                                } else {
                                    if($tipo_proceso == 2){
                                      $observacion = 'Confeccion de prendas';
                                    }else{
                                        if($tipo_proceso == 3){
                                            $observacion = 'Pago de cesantias';
                                        }else{
                                            $observacion = 'Primas del  '. $fecha_inicio. '  al  ' .$fecha_final. '';
                                        }    
                                    }  
                                }
                                $table->observacion = $observacion;
                                $table->libre = 1;
                                $table->usuariosistema = Yii::$app->user->identity->username;
                                $table->save(false);   
                                //proceso de inserccion en la tabla detalle
                                $comprobante = ComprobanteEgreso::find()->orderBy('id_comprobante_egreso DESC')->one();
                                $table2 = new ComprobanteEgresoDetalle();
                                $table2->id_comprobante_egreso = $comprobante->id_comprobante_egreso;
                                $table2->vlr_abono = $valorPagado;
                                $table2->subtotal = $valorPagado;
                                $table2->save(false);
                                $cont += 1;
                            }    
                            $valorPagado = 0;
                            $intIndice++;    
                        }
                        Yii::$app->getSession()->setFlash('info', 'Se importaron '.  $cont. ' registros al modulo contable de forma exitosa. ');
                         return $this->render('importardocumentocontable', [
                           'form' => $form,
                           'model' => $model,
                           'tipo_proceso' => $tipo_proceso,
                        ]);
                    }
                }    
                return $this->render('importardocumentocontable', [
                       'form' => $form,
                       'model' => $model,
                       'tipo_proceso' => $tipo_proceso,
               ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
    }

    /**
     * Displays a single ComprobanteEgreso model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $token)
    {
        $modeldetalles = ComprobanteEgresoDetalle::find()->Where(['=', 'id_comprobante_egreso', $id])->all();
        $modeldetalle = new ComprobanteEgresoDetalle();
        $mensaje = "";
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modeldetalle' => $modeldetalle,
            'modeldetalles' => $modeldetalles,
            'mensaje' => $mensaje,
            'token' => $token,
        ]);
    }

    /**
     * Creates a new ComprobanteEgreso model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ComprobanteEgreso();
        $proveedores = Proveedor::find()->orderBy('nombrecorto ASC')->all();
        $municipios = Municipio::find()->all();
        $bancos = Banco::find()->all();
        $tipos = ComprobanteEgresoTipo::find()->orderBy('concepto ASC')->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {            
            $model->usuariosistema = Yii::$app->user->identity->username;
            if($model->save()){
              
                return $this->redirect(['view','id' => $model->id_comprobante_egreso,'token' => 0]);
            }else{
                
            }    
        }

        return $this->render('create', [
            'model' => $model,
            'proveedores' => ArrayHelper::map($proveedores, "idproveedor", "nombreproveedores"),
            'tipos' => ArrayHelper::map($tipos, "id_comprobante_egreso_tipo", "concepto"),
            'municipios' => ArrayHelper::map($municipios, "idmunicipio", "municipioCompleto"),
            'bancos' => ArrayHelper::map($bancos, "idbanco", "entidad"),
        ]);
    }
    
    public function actionCreatelibre()
    {
        $model = new FormComprobanteegresolibre();
        $proveedores = Proveedor::find()->orderBy('nombrecorto ASC')->all();
        $municipios = Municipio::find()->all();
        $bancos = Banco::find()->all();
       $tipos = ComprobanteEgresoTipo::find()->orderBy('concepto ASC')->all();
        if ($model->load(Yii::$app->request->post())) {
            $table = new ComprobanteEgreso;
            $table->id_proveedor = $model->id_proveedor;
            $table->id_banco = $model->id_banco;            
            $table->observacion = $model->observacion;
            $table->fecha_comprobante = $model->fecha_comprobante;
            $table->id_comprobante_egreso_tipo = $model->id_comprobante_egreso_tipo;
            $table->id_municipio = $model->id_municipio;            
            $table->usuariosistema = Yii::$app->user->identity->username;
            $table->libre = 1;
            $table->save();
            $registro = ComprobanteEgreso::find()->orderBy('id_comprobante_egreso DESC')->one();
            return $this->redirect(['view','id' => $registro->id_comprobante_egreso, 'token' => 0]);
        }

        return $this->render('_formlibre', [
            'model' => $model,
            'proveedores' => ArrayHelper::map($proveedores, "idproveedor", "nombreproveedores"),
            'tipo' => ArrayHelper::map($tipos, "id_comprobante_egreso_tipo", "concepto"),
            'municipios' => ArrayHelper::map($municipios, "idmunicipio", "municipioCompleto"),
            'bancos' => ArrayHelper::map($bancos, "idbanco", "entidad"),
        ]);
    }

    /**
     * Updates an existing ComprobanteEgreso model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $proveedores = Proveedor::find()->all();
        $municipios = Municipio::find()->all();
        $bancos = Banco::find()->all();
        $tipos = ComprobanteEgresoTipo::find()->all();
        if($model->libre == 1){
                return $this->redirect(['updatelibre', 'id' => $id]);
        }
        if(ComprobanteEgresoDetalle::find()->where(['=', 'id_comprobante_egreso', $id])->all() or $model->estado <> 0){
           Yii::$app->getSession()->setFlash('warning', 'No se puede modificar la información, tiene detalles asociados');
        }
        else             
            if($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view','id' => $model->id_comprobante_egreso,'token' => 0]);
        }

        return $this->render('update', [
            'model' => $model,
            'proveedores' => ArrayHelper::map($proveedores, "idproveedor", "nombreproveedores"),
            'tipos' => ArrayHelper::map($tipos, "id_comprobante_egreso_tipo", "concepto"),
            'municipios' => ArrayHelper::map($municipios, "idmunicipio", "municipioCompleto"),
            'bancos' => ArrayHelper::map($bancos, "idbanco", "entidad"),
        ]);
    }
    
    public function actionUpdatelibre($id) {
        $model = new FormComprobanteegresolibre;
        $proveedores = Proveedor::find()->all();
        $municipios = Municipio::find()->all();
        $bancos = Banco::find()->all();
        $tipos = ComprobanteEgresoTipo::find()->all();
        $table = $this->findModel($id);
        if(ComprobanteEgresoDetalle::find()->where(['=', 'id_comprobante_egreso', $id])->all() && $table->numero > 0){
           return $this->redirect(['index']);
        }else{
            if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate()) {
                    if ($table) {                                        
                        $table->id_proveedor = $model->id_proveedor;
                        $table->id_banco = $model->id_banco;            
                        $table->observacion = $model->observacion;
                        $table->fecha_comprobante = $model->fecha_comprobante;
                        $table->id_comprobante_egreso_tipo = $model->id_comprobante_egreso_tipo;
                        $table->id_municipio = $model->id_municipio;                                            
                        if ($table->save()) {
                            $msg = "El registro ha sido actualizado correctamente";
                            return $this->redirect(["view",'id' => $id,'token' => 0]);
                        } else {
                            $msg = "El registro no sufrio ningun cambio";
                            return $this->redirect(["index"]);
                        }
                    } else {
                        $msg = "El registro seleccionado no ha sido encontrado";
                    }
                } else {
                    $model->getErrors();
                }
            }

            if (Yii::$app->request->get("id")) {
                $table = $this->findModel($id);
                if ($table) {
                    $model->id_proveedor = $table->id_proveedor;
                    $model->id_banco = $table->id_banco;
                    $model->fecha_comprobante = $table->fecha_comprobante;                
                    $model->observacion = $table->observacion;
                    $model->id_municipio = $table->id_municipio;
                    $model->id_comprobante_egreso_tipo = $table->id_comprobante_egreso_tipo;
                } else {
                    return $this->redirect(["index"]);
                }
            } else {
                return $this->redirect(["index"]);
            }
        }    
        return $this->render('_formlibre', [
            'model' => $model,
            'proveedores' => ArrayHelper::map($proveedores, "idproveedor", "nombreproveedores"),
            'tipo' => ArrayHelper::map($tipos, "id_comprobante_egreso_tipo", "concepto"),
            'municipios' => ArrayHelper::map($municipios, "idmunicipio", "municipioCompleto"),
            'bancos' => ArrayHelper::map($bancos, "idbanco", "entidad"),
        ]);
    }

    /**
     * Deletes an existing ComprobanteEgreso model.
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
            $this->redirect(["comprobante-egreso/index"]);
        } catch (IntegrityException $e) {
            $this->redirect(["comprobante-egreso/index"]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el comprobante, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el comprobante, tiene registros asociados en otros procesos');
            $this->redirect(["comprobante-egreso/index"]);
        }
    }
    
    //proceso que crear los comprobantes de egreso
    public function actionNuevoimportado($fecha_inicio, $fecha_final, $tipo_proceso) {
        $form = $form = new ModelCrearComprobantes();
        if (isset($_POST["documento_identidad"])) {
                $intIndice = 0;
                foreach ($_POST["documento_identidad"] as $intCodigo) {
                    $table = new Productodetalle();
                    $prenda = Prendatipo::find()->where(['idprendatipo' => $intCodigo])->one();
                    $detalles = Productodetalle::find()
                        ->where(['=', 'idproducto', $idproducto])
                        ->andWhere(['=', 'idprendatipo', $prenda->idprendatipo])
                        ->all();
                    $reg = count($detalles);
                    if ($reg == 0) {
                        $table->idprendatipo = $prenda->idprendatipo;
                        $table->observacion = ".";
                        $table->idproducto = $idproducto;
                        $table->usuariosistema = Yii::$app->user->identity->username;
                        $table->insert();                                                
                    }
                }
                 $this->redirect(["comprobante-egreso/importardocumento"]);
        }else{
            Yii::$app->getSession()->setFlash('warning', 'Debe de seleccionar un registro para crear el documento.');   
            $nomina = \app\models\ProgramacionNomina::find()->where(['>=','fecha_desde', $fecha_inicio])
                                                                            ->andWhere(['<=', 'fecha_hasta', $fecha_final])->all();
            $model = $nomina;
             return $this->render('importardocumentocontable', [
            'model' => $model,
            'form' => $form, 
            'tipo_proceso' => $tipo_proceso,    

        ]);
        }
    }
    
    //nuevso detalles
    public function actionNuevodetalles($id_proveedor,$id_comprobante_egreso, $token)
    {
        $compraegreso = Compra::find()
            ->where(['=', 'id_proveedor', $id_proveedor])
            ->andWhere(['=', 'autorizado', 1])->andWhere(['<>', 'numero', 0])
            ->andWhere(['>', 'saldo', 0])->orderBy('id_compra DESC')
            ->all();
        $mensaje = "";
        if(Yii::$app->request->post()) {
            if (isset($_POST["id_compra"])) {
                $intIndice = 0;
                foreach ($_POST["id_compra"] as $intCodigo) {
                    $table = new ComprobanteEgresoDetalle();
                    $compra = Compra::find()->where(['id_compra' => $intCodigo])->one();
                    $detalles = ComprobanteEgresoDetalle::find()
                        ->where(['=', 'id_compra', $compra->id_compra])
                        ->andWhere(['=', 'id_comprobante_egreso', $id_comprobante_egreso])
                        ->all();
                    $reg = count($detalles);
                    if ($reg == 0) {
                        $table->id_compra = $compra->id_compra;
                        $table->vlr_abono = round($compra->saldo, 0);
                        $table->vlr_saldo = round($compra->saldo, 0);
                        $table->subtotal = $compra->subtotal;
                        $table->iva = $compra->impuestoiva;
                        $table->retefuente = $compra->retencionfuente;
                        $table->reteiva = $compra->retencioniva;
                        $table->base_aiu = $compra->base_aiu;
                        $table->id_comprobante_egreso = $id_comprobante_egreso;
                        $table->insert();
                    }
                }
                $this->redirect(["comprobante-egreso/view", 'id' => $id_comprobante_egreso, 'token' => $token]);
            } else {
                $mensaje = "Debe seleccionar al menos un registro";
            }
        }
        return $this->render('_formnuevodetalles', [
            'compraegreso' => $compraegreso,
            'id_comprobante_egreso' => $id_comprobante_egreso,
            'mensaje' => $mensaje,
            'token' => $token,

        ]);
    }
    
    public function actionNuevodetallelibre($id, $token) {
        $model = new FormComprobanteegresonuevodetallelibre();        
        if ($model->load(Yii::$app->request->post())) {
            $table = new ComprobanteEgresoDetalle();
            $table->vlr_abono = $model->vlr_abono;
            $table->id_comprobante_egreso = $id;
            $table->vlr_saldo = 0;
            $table->subtotal = $model->subtotal;
            $table->retefuente = $model->retefuente;
            $table->iva = $model->iva;
            $table->reteiva = $model->reteiva;
            $table->reteica = 0;
            $table->base_aiu = $model->base_aiu;
            $table->save(false);
            return $this->redirect(['view','id' => $id, 'token' => $token]);
        }
        return $this->renderAjax('_formnuevodetallelibre', [
            'model' => $model,            
            'token' => $token,
        ]);        
    }
    
    public function actionEditardetalle($token)
    {
        $id_comprobante_egreso_detalle = Html::encode($_POST["id_comprobante_egreso_detalle"]);
        $id_comprobante_egreso = Html::encode($_POST["id_comprobante_egreso"]);
        if(Yii::$app->request->post()){

            if((int) $id_comprobante_egreso_detalle)
            {
                $table = ComprobanteEgresoDetalle::findOne($id_comprobante_egreso_detalle);
                if ($table) {

                    $table->vlr_abono = Html::encode($_POST["vlr_abono"]);
                    $table->id_comprobante_egreso = Html::encode($_POST["id_comprobante_egreso"]);
                    $table->update();
                    $this->redirect(["comprobante-egreso/view",'id' => $id_comprobante_egreso, 'token' => $token]);

                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }
            }
        }
    }
    
    public function actionEditardetalles($id_comprobante_egreso, $token)
    {
        $mds = ComprobanteEgresoDetalle::find()->where(['=', 'id_comprobante_egreso', $id_comprobante_egreso])->all();

        if (isset($_POST["id_comprobante_egreso_detalle"])) {
            $intIndice = 0;
            foreach ($_POST["id_comprobante_egreso_detalle"] as $intCodigo) {
                if($_POST["vlr_abono"][$intIndice] > 0 ){
                    $table = ComprobanteEgresoDetalle::findOne($intCodigo);
                    $total = $table->vlr_abono;
                    $table->vlr_abono = $_POST["vlr_abono"][$intIndice];
                    $table->update();
                }
                $intIndice++;
            }
            $this->redirect(["comprobante-egreso/view",'id' => $id_comprobante_egreso, 'token' => $token]);
        }
        return $this->render('_formeditardetalles', [
            'mds' => $mds,
            'id_comprobante_egreso' => $id_comprobante_egreso,
            'token' => $token,
        ]);
    }
    
    public function actionEliminardetalle($token)
    {
        if(Yii::$app->request->post())
        {
            $id_comprobante_egreso_detalle = Html::encode($_POST["id_comprobante_egreso_detalle"]);
            $id_comprobante_egreso = Html::encode($_POST["id_comprobante_egreso"]);
            if((int) $id_comprobante_egreso_detalle)
            {
                $comprobanteDetalle = ComprobanteEgresoDetalle::findOne($id_comprobante_egreso_detalle);
                $total = $comprobanteDetalle->vlr_abono;
                if(ComprobanteEgresoDetalle::deleteAll("id_comprobante_egreso_detalle=:id_comprobante_egreso_detalle", [":id_comprobante_egreso_detalle" => $id_comprobante_egreso_detalle]))
                {                   
                    $this->redirect(["comprobante-egreso/view",'id' => $id_comprobante_egreso, 'token' => $token]);
                }
                else
                {
                    echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("comprobante-egreso/index")."'>";
                }
            }
            else
            {
                echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("comprobante-egreso/index")."'>";
            }
        }
        else
        {
            return $this->redirect(["comprobante-egreso/index"]);
        }
    }
    
    public function actionEliminardetalles($id_comprobante_egreso, $token)
    {
        $mds = ComprobanteEgresoDetalle::find()->where(['=', 'id_comprobante_egreso', $id_comprobante_egreso])->all();
        $mensaje = "";
        if(Yii::$app->request->post())
        {
            $intIndice = 0;

            if (isset($_POST["seleccion"])) {
                foreach ($_POST["seleccion"] as $intCodigo)
                {
                    $comprobanteDetalle = ComprobanteEgresoDetalle::findOne($intCodigo);
                    $total = $comprobanteDetalle->vlr_abono;
                    if(ComprobanteEgresoDetalle::deleteAll("id_comprobante_egreso_detalle=:id_comprobante_egreso_detalle", [":id_comprobante_egreso_detalle" => $intCodigo]))
                    {
                        /*$recibo = Recibocaja::findOne($idrecibo);
                        $recibo->valorpagado = $recibo->valorpagado - $total;
                        $recibo->update();*/
                    }
                }
                $this->redirect(["comprobante-egreso/view",'id' => $id_comprobante_egreso, 'token' => $token]);
            }else {
                $mensaje = "Debe seleccionar al menos un registro";
            }
        }
        return $this->render('_formeliminardetalles', [
            'mds' => $mds,
            'id_comprobante_egreso' => $id_comprobante_egreso,
            'mensaje' => $mensaje,
            'token' => $token,
        ]);
    }
    
    public function actionAutorizado($id, $token)
    {
        $model = $this->findModel($id);
        if ($model->autorizado == 0){
            $detalles = ComprobanteEgresoDetalle::find()
                ->where(['=', 'id_comprobante_egreso', $id])
                ->all();
            $reg = count($detalles);
            if ($reg <> 0) {
                $error = 0;
                if($model->libre == 1){
                    $error = 0;
                }else{
                }               
                if ($error == 0){
                    $model->autorizado = 1;
                    $model->save(false);
                    return $this->redirect(["comprobante-egreso/view",'id' => $id, 'token' => $token]);
                }else{
                    Yii::$app->getSession()->setFlash('error', 'Los abonos no pueden ser mayores a los saldos.');
                    return $this->redirect(["comprobante-egreso/view",'id' => $id, 'token' => $token]);
                }
            }else{
                Yii::$app->getSession()->setFlash('error', 'Para autorizar el registro debe tener compras relacionados en el comprobante.');
                return $this->redirect(["comprobante-egreso/view",'id' => $id, 'token' => $token]);
            }
        } else {
            if ($model->valor <> 0) {
                Yii::$app->getSession()->setFlash('error', 'No se puede desautorizar el registro, ya fue pagado.');
                return $this->redirect(["comprobante-egreso/view",'id' => $id, 'token' => $token]);
            } else {
                $model->autorizado = 0;
                $model->update();
                return $this->redirect(["comprobante-egreso/view",'id' => $id, 'token' => $token]);
            }
        }
    }
    
    public function actionPagar($id, $token)
    {
        $model = $this->findModel($id);
        $mensaje = "";
        if ($model->autorizado == 1){
            if ($model->valor == 0){
                $comprobantedetalles = ComprobanteEgresoDetalle::find()
                    ->where(['id_comprobante_egreso' => $id])
                    ->all();
                $total = 0;
                $subtotal = 0;
                $iva = 0;
                $retefuente = 0;
                $reteiva = 0;
                $baseaiu = 0;                
                $error = 0;
                if ($model->libre == 0){
                }else{
                    $error = 0;
                }                
                if ($error == 0){
                    if ($model->libre == 0){
                        foreach ($comprobantedetalles as $val) {
                            $comprobantedetalle = ComprobanteEgresoDetalle::findOne($val->id_comprobante_egreso_detalle);
                            $comprobantedetalle->vlr_saldo = ($comprobantedetalle->vlr_saldo) - ($comprobantedetalle->vlr_abono);
                            $total = $total + $val->vlr_abono;
                            $subtotal = $subtotal + $val->subtotal;
                            $iva = $iva + $val->iva;
                            $retefuente = $retefuente + $val->retefuente;
                            $reteiva = $reteiva + $val->reteiva;
                            $baseaiu = $baseaiu + $val->base_aiu;
                            $comprobantedetalle->update();
                            $compra = Compra::findOne($val->id_compra);
                            $compra->saldo = $comprobantedetalle->vlr_saldo;
                            if($compra->saldo <= 0){
                                $compra->estado = 2; //estado 0 = abieto, estado 1 = abono, estado 2 = pagada, estado 3 = anulada por otro procesos (saldo 0 en la compra), estado 4 = descuento por otro proceso
                            }elseif ($compra->saldo >= 0){
                                $compra->estado = 1; //estado 0 = abieto, estado 1 = abono, estado 2 = pagada, estado 3 = anulada por otro procesos (saldo 0 en la compra), estado 4 = descuento por otro proceso
                            }
                            $compra->save(false);
                        }
                        $model->valor = $total;
                        $model->subtotal = $subtotal;
                        $model->iva = $iva;
                        $model->retefuente = $retefuente;
                        $model->reteiva = $reteiva;
                        $model->base_aiu = $baseaiu;
                        //$model->fechapago = date('Y-m-d');
                        //generar consecutivo numero de la nota credito
                        $consecutivo = Consecutivo::findOne(6);//6 comprobante de egreso
                        $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
                        $model->numero = $consecutivo->consecutivo;
                        $model->save(false);
                        $consecutivo->update();
                        //fin generar consecutivo
                        $this->redirect(["comprobante-egreso/view",'id' => $id, 'token' => $token]);
                    }else{
                        foreach ($comprobantedetalles as $val) {                            
                            $total = $total + $val->vlr_abono;
                            $subtotal = $subtotal + $val->subtotal;
                            $iva = $iva + $val->iva;
                            $retefuente = $retefuente + $val->retefuente;
                            $reteiva = $reteiva + $val->reteiva;
                            $baseaiu = $baseaiu + $val->base_aiu;                           
                        }
                        $model->valor = $total;
                        $model->subtotal = $subtotal;
                        $model->iva = $iva;
                        $model->retefuente = $retefuente;
                        $model->reteiva = $reteiva;
                        $model->base_aiu = $baseaiu;                        
                        $consecutivo = Consecutivo::findOne(6);//6 comprobante de egreso
                        $consecutivo->consecutivo = $consecutivo->consecutivo + 1;
                        $model->numero = $consecutivo->consecutivo;
                        $model->save(false);
                        $consecutivo->update();
                        //fin generar consecutivo
                        $this->redirect(["comprobante-egreso/view",'id' => $id, 'token' => $token]);
                    }
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Los abonos no pueden ser mayores a los saldos.');
                    $this->redirect(["comprobante-egreso/view",'id' => $id, 'token' => $token]);
                }
            }else{
                Yii::$app->getSession()->setFlash('error', 'Ya se realizo el pago del comprobante.');
                $this->redirect(["comprobante-egreso/view",'id' => $id, 'token' => $token]);
            }
        } else {
            Yii::$app->getSession()->setFlash('error', 'Para pagar el comprobante debe estar autorizado');
            $this->redirect(["comprobante-egreso/view",'id' => $id, 'token' => $token]);
        }
    }
    
    //ANULAR DOCUMENTO CONTABLE
    public function actionAnular_documento($id, $token) {
        $comprobante = ComprobanteEgreso::findOne($id);
        $detalle = ComprobanteEgresoDetalle::find()->where(['=','id_comprobante_egreso', $id])->all();
        if(count($detalle) > 0){
            foreach ($detalle as $detalles):
                $detalles->vlr_abono = 0;
                $detalles->subtotal = 0;
                $detalles->vlr_saldo = 0;
                $detalles->save();
            endforeach;
        }
        $comprobante->estado = 1;
        $comprobante->subtotal = 0;
        $comprobante->valor = 0;
        $comprobante->retefuente = 0;
        $comprobante->iva = 0;
        $comprobante->save();
        $this->redirect(["comprobante-egreso/view",'id' => $id, 'token' => $token]);
    }

    /**
     * Finds the ComprobanteEgreso model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ComprobanteEgreso the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ComprobanteEgreso::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionImprimir($id)
    {                                
        return $this->render('../formatos/comprobanteEgreso', [
            'model' => $this->findModel($id),
            
        ]);
    }
    
    public function actionIndexconsulta() {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',46])->all()){
            $form = new FormFiltroConsultaComprobanteegreso();
            $idproveedor = null;
            $desde = null;
            $hasta = null;
            $numero = null;
            $tipo = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idproveedor = Html::encode($form->idproveedor);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $numero = Html::encode($form->numero);
                    $tipo = Html::encode($form->tipo);
                    $table = ComprobanteEgreso::find()
                            ->andFilterWhere(['=', 'id_proveedor', $idproveedor])
                            ->andFilterWhere(['>=', 'fecha_comprobante', $desde])
                            ->andFilterWhere(['<=', 'fecha_comprobante', $hasta])
                            ->andFilterWhere(['=', 'numero', $numero])
                            ->andFilterWhere(['=', 'id_comprobante_egreso_tipo', $tipo]);
                    $table = $table->orderBy('id_comprobante_egreso desc');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 20,
                        'totalCount' => $count->count()
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){
                        //$table = $table->all();
                        $this->actionExcelconsulta($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = ComprobanteEgreso::find()
                        ->orderBy('id_comprobante_egreso desc');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 20,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                    $this->actionExcelconsulta($tableexcel);
                }
            }
            $to = $count->count();
            return $this->render('index_consulta', [
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
    
    public function actionViewconsulta($id)
    {
        $modeldetalles = ComprobanteEgresoDetalle::find()->Where(['=', 'id_comprobante_egreso', $id])->all();
        $modeldetalle = new ComprobanteEgresoDetalle();
        $mensaje = "";
        return $this->render('view_consulta', [
            'model' => $this->findModel($id),
            'modeldetalle' => $modeldetalle,
            'modeldetalles' => $modeldetalles,
            'mensaje' => $mensaje,
        ]);
    }
    
    //documento soporte des de comprobante
     public function actionCrear_documento_soporte ($id) {
        $model = new \app\models\ModeloCambiarIva();
         if ($model->load(Yii::$app->request->post())) {
            if (isset($_POST["actualizar_concepto"])) {
                if($model->tipocomprobante <> ''){
                    $comprobante = ComprobanteEgreso::findOne($id);
                    if($comprobante){
                        if($comprobante->id_comprobante_egreso = \app\models\DocumentoSoporte::find()->where(['=','id_comprobante_egreso', $id])->one()){
                           Yii::$app->getSession()->setFlash('info', 'Este comprobante de egreso ya tiene el documento soporte generado. Valide la informacion.');
                           return $this->redirect(["comprobante-egreso/view",'id' => $id, 'token' => 0]);
                        }else{
                            $resolucion = \app\models\Resolucion::find()->where(['=','activo', 0])->andWhere(['=','id_documento', 2])->one();
                            if($resolucion){
                                $documento = \app\models\ConceptoDocumentoSoporte::findOne($model->tipocomprobante);
                                $table = new \app\models\DocumentoSoporte();
                                $table->idproveedor = $comprobante->id_proveedor;
                                $table->id_comprobante_egreso = $id;
                                $table->idresolucion = $resolucion->idresolucion;
                                $table->documento_compra = $comprobante->numero;
                                $table->fecha_elaboracion = date('Y-m-d');
                                $table->observacion =  $documento->concepto;
                                $table->id_forma_pago = 4;
                                $table->user_name = Yii::$app->user->identity->username;
                                $table->consecutivo = $resolucion->consecutivo;
                                $table->save(false);
                                //cargue el detalle
                               
                                $detalle = new \app\models\DocumentoSoporteDetalle();
                                $detalle->id_documento_soporte = $table->id_documento_soporte;
                                $detalle->id_concepto = $model->tipocomprobante;
                                $detalle->descripcion = $documento->concepto;
                                $detalle->cantidad = 1;
                                $detalle->valor_unitario = $comprobante->valor;
                                $detalle->save(false);
                                return $this->redirect(["documento-soporte/view",'id' => $table->id_documento_soporte]);
                                  
                            }else{
                                Yii::$app->getSession()->setFlash('error', 'No existe la resolucion para documento soporte. Valide la informacion');
                                return $this->redirect(["comprobante-egreso/view",'id' => $id, 'token' => 0]);
                            }
                        }
                    }
                }else{
                    Yii::$app->getSession()->setFlash('warning', 'Debe de seleccion al menos un registro.');
                    return $this->redirect(["comprobante-egreso/view",'id' => $id, 'token' => 0]);
                }
            }
         }
        return $this->renderAjax('documento_soporte', [
            'model' => $model,
            
        ]);   
    }
    
    public function actionExcelconsulta($tableexcel) {                
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setLastModifiedBy("EMPRESA")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);                       
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Id')
                    ->setCellValue('B1', 'N° Comprobante')
                    ->setCellValue('C1', 'Proveedor')
                    ->setCellValue('D1', 'Fecha Comprobante')
                    ->setCellValue('E1', 'Tipo')
                    ->setCellValue('F1', 'Municipio')
                    ->setCellValue('G1', 'Valor')
                    ->setCellValue('H1', 'Iva')
                    ->setCellValue('I1', 'Retencion fuente')
                    ->setCellValue('J1', 'Retencion iva')
                    ->setCellValue('K1', 'Banco')
                    ->setCellValue('L1', 'Autorizado')
                    ->setCellValue('M1', 'Observacion');
        $i = 2;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_comprobante_egreso)
                    ->setCellValue('B' . $i, $val->numero)
                    ->setCellValue('C' . $i, $val->proveedor->nombreProveedores)
                    ->setCellValue('D' . $i, $val->fecha_comprobante)
                    ->setCellValue('E' . $i, $val->comprobanteEgresoTipo->concepto)
                    ->setCellValue('F' . $i, $val->municipio->municipio.' - '.$val->municipio->departamento->departamento)
                    ->setCellValue('G' . $i, round($val->valor,0))
                    ->setCellValue('H' . $i, $val->iva)
                    ->setCellValue('I' . $i, $val->retefuente)
                    ->setCellValue('J' . $i, $val->reteiva)
                    ->setCellValue('K' . $i, $val->banco->entidad)
                    ->setCellValue('L' . $i, $val->autorizar)
                    ->setCellValue('M' . $i, $val->observacion);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('comprobante_egreso');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="comprobante_egreso.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
        exit;
    }
}
