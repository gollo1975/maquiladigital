<?php

namespace app\controllers;

use Yii;
use app\models\Matriculaempresa;
use app\models\Departamento;
use app\models\Municipio;
use app\models\MatriculaempresaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UsuarioDetalle;

/**
 * ArlController implements the CRUD actions for Arl model.
 */
class EmpresaController extends Controller
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
    public function actionEmpresa($id)
    {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',30])->all()){
                $model = $this->findModel($id);
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    //return $this->redirect(['view', 'id' => $model->id_parametros]);
                }
                return $this->render('empresa', [
                    'model' => $model,
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }
    }
    
    public function actionConfiguracion_inventarios($id) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',187])->all()){
               $modelo = \app\models\ConfiguracionInventario::findOne($id);
               if ($modelo->load(Yii::$app->request->post())){
                   $table = \app\models\ConfiguracionInventario::findOne($id);
                   $table->aplica_iva_incluido =  $modelo->aplica_iva_incluido;
                   $table->aplica_modulo_inventario = $modelo->aplica_modulo_inventario;
                   $table->aplica_solo_inventario = $modelo->aplica_solo_inventario;
                   $table->aplica_inventario_tallas = $modelo->aplica_inventario_tallas;
                   $table->aplica_inventario_talla_color = $modelo->aplica_inventario_talla_color;
                   $table->save();
                   return $this->redirect(['empresa/configuracion_inventarios','id' => $id]);
               }
               return $this->render('configuracion-inventarios', [
                    'modelo' => $modelo,
                    'id' => $id,
                ]);
               
            }else{
                 return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
        
    }
    
    //CONFIGURACION DE DOCUMENTOS ELECTRONICOS
     public function actionConfiguracion_documentos_electronicos($id) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',189])->all()){
               $modelo = \app\models\ConfiguracionDocumentoElectronico::findOne($id);
               if ($modelo->load(Yii::$app->request->post())){
                   $table = \app\models\ConfiguracionDocumentoElectronico::findOne($id);
                   $table->aplica_factura_electronica =  $modelo->aplica_factura_electronica;
                   $table->aplica_documento_soporte = $modelo->aplica_documento_soporte;
                   $table->aplica_nomina_electronica = $modelo->aplica_nomina_electronica;
                   $table->llave_api_token = $modelo->llave_api_token;
                   $table->save();
                   return $this->redirect(['empresa/configuracion_documentos_electronicos','id' => $id]);
               }
               return $this->render('configuracion_documento_electronico', [
                    'modelo' => $modelo,
                    'id' => $id,
                ]);
               
            }else{
                 return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
        
    }
    
    //estado de cartera
    public function actionEstado_cartera($id) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',188])->all()){
               $modelo = \app\models\CarteraEmpresa::findOne($id);
               if ($modelo->load(Yii::$app->request->post())){
                   $table = \app\models\CarteraEmpresa::findOne($id);
                   $table->fecha_vencimiento =  $modelo->fecha_vencimiento;
                   $table->fecha_suspension = $modelo->fecha_suspension;
                   $table->numero_factura = $modelo->numero_factura;
                   $table->dias_adicionales = $modelo->dias_adicionales;
                   $table->estado_registro = $modelo->estado_registro;
                   $table->save();
                   return $this->redirect(['empresa/estado_cartera','id' => $id]);
               }
               return $this->render('estado_cartera', [
                    'modelo' => $modelo,
                    'id' => $id,
                ]);
               
            }else{
                 return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
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
        if (($model = Matriculaempresa::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionMunicipio($id) {
        $rows = Municipio::find()->where(['iddepartamento' => $id])->all();

        echo "<option required>Seleccione...</option>";
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                echo "<option value='$row->idmunicipio' required>$row->municipio</option>";
            }
        }
    }
}
