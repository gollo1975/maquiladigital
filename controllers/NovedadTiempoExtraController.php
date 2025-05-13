<?php

namespace app\controllers;


use app\models\NovedadTiempoExtra;
use app\models\NovedadTiempoExtraSearch;
use app\models\ProgramacionNomina;
use app\models\FormCrearTiempoExtra;
use app\models\ConceptoSalarios;
use app\models\UsuarioDetalle;
use app\models\Contrato;

//clases
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


/**
 * NovedadTiempoExtraController implements the CRUD actions for NovedadTiempoExtra model.
 */
class NovedadTiempoExtraController extends Controller
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

    public function actionNovedades($id){
       $detalle = ProgramacionNomina::find()->where(['=','id_periodo_pago_nomina', $id])->andWhere(['=','tipo_salario', 'VARIABLE'])->orderBy('cedula_empleado DESC')->all();
       $model = ProgramacionNomina::find()->where(['=','id_periodo_pago_nomina', $id])->one();
       $novedades = NovedadTiempoExtra::find()->where(['=','id_periodo_pago_nomina', $id])->orderBy('id_empleado ASC')->all();
       if($detalle == 0 ){
           
       }else{
            return $this->render('vistanovedades', [
                        'model' => $model,
                        'detalle' => $detalle,
                        'id' => $id,
                        'novedades' => $novedades,
            ]);    
       }
    }

    public function actionCreartiempoextra($id,$id_programacion,$tipo_salario) {
        $datos_programacion = ProgramacionNomina::findOne($id_programacion);
        $empresa = \app\models\Matriculaempresa::findOne(1);
        $concepto_salario= ConceptoSalarios::find()->where(['=','hora_extra', 1])->all();
           if (Yii::$app->request->post()) {  
                if (isset($_POST["codigo_salario"])) {
                    $intIndice = 0;
                    foreach ($_POST["codigo_salario"] as $intCodigo) {
                        if ($_POST["horas"][$intIndice] > 0) {
                            if (!NovedadTiempoExtra::find()->where(['=','id_empleado', $datos_programacion->id_empleado])
                                                           ->andWhere(['=','fecha_inicio',$datos_programacion->fecha_desde])
                                                           ->andWhere(['=','fecha_corte',$datos_programacion->fecha_hasta])
                                                           ->andWhere(['=','codigo_salario',$_POST["codigo_salario"][$intIndice]])->one()){
                               $concepto = ConceptoSalarios::findOne($_POST["codigo_salario"][$intIndice]); 
                               $table = new NovedadTiempoExtra();
                               $table->id_empleado = $datos_programacion->id_empleado;
                               $table->id_programacion = $datos_programacion->id_programacion;
                               $table->codigo_salario = $_POST["codigo_salario"][$intIndice];
                               $table->concepto = $_POST["concepto"][$intIndice];
                               $table->porcentaje = $_POST["porcentaje"][$intIndice];
                               $table->id_periodo_pago_nomina = $id;
                               $table->id_grupo_pago = $datos_programacion->id_grupo_pago;
                               $table->fecha_inicio = $datos_programacion->fecha_desde;
                               $table->fecha_corte = $datos_programacion->fecha_hasta;
                               $table->vlr_hora = (($datos_programacion->salario_contrato / $empresa->horas_mensuales) * ($concepto->porcentaje_tiempo_extra)) / 100;
                               $table->nro_horas = $_POST["horas"][$intIndice];
                                $table->salario_contrato = $datos_programacion->salario_contrato;
                               $table->total_novedad = $table->vlr_hora * $table->nro_horas;
                               $table->usuariosistema = Yii::$app->user->identity->username;  
                               $table->save();     
                            }    
                        }
                        $intIndice++;
                    } 
                    $this->redirect(["novedad-tiempo-extra/novedades", 'id' => $id]);
                }
            }
            return $this->renderAjax('_creartiempoextra', ['id' => $id, 'concepto_salario' => $concepto_salario, 'datos_programacion' => $datos_programacion]);
    }
    
    
    public function actionEditartiempoextra($id_empleado, $id){
        
        $datos_novedades= NovedadTiempoExtra::find()->where(['=','id_empleado', $id_empleado])
                                                   ->andWhere(['=','id_periodo_pago_nomina', $id])->all();
        if (Yii::$app->request->post()) { 
            if (isset($_POST["codigo_salario"])) {
                $intIndice = 0;
                foreach ($_POST["codigo_salario"] as $intCodigo) {
                    $table = NovedadTiempoExtra::find()->where(['=','id_empleado', $id_empleado])
                                                   ->andWhere(['=','codigo_salario', $_POST["codigo_salario"][$intIndice]])->one();
                    if($table){                    
                       $table->nro_horas = $_POST["horas"][$intIndice];
                       $vlr_hora = $_POST["vlr_hora"][$intIndice];
                       $table->total_novedad = $vlr_hora * $table->nro_horas;
                       $table->save();     
                    }    
                    $intIndice++;
                } 
                $this->redirect(["novedad-tiempo-extra/novedades", 'id' => $id]);
            }
        }
        return $this->renderAjax('_editartiempoextra', [
            'id_empleado' => $id_empleado,
            'datos_novedad' => $datos_novedades]);
    }
    
    //ELIMINAR NOVEDADES DE NOMINA
    public function actionEliminar_detalle($id, $id_detalle) {
        try {
            $this->findModel($id_detalle)->delete();
            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
            return $this->redirect(["novedad-tiempo-extra/novedades",'id' => $id]);
        } catch (IntegrityException $e) {
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el registro, tiene registros asociados en otros procesos');
           return $this->redirect(["novedad-tiempo-extra/novedades",'id' => $id]);
           
        } catch (\Exception $e) {
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el registro, tiene registros asociados en otros procesos');
           return $this->redirect(["novedad-tiempo-extra/novedades",'id' => $id]);
        }
    }

        protected function findModel($id)
    {
        if (($model = NovedadTiempoExtra::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
