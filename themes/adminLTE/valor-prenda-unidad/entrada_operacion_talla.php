<?php
//clases
use yii\bootstrap\Progress;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\web\Session;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;
use kartik\select2\Select2;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */

$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$Fecha =  $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;
$operario = \app\models\Operarios::findOne($tokenOperario);
$this->title = ''.$operario->nombrecompleto.' - ('. $tallas->listadoTallaIndividual. ') - (Referencia:' . $model->ordenproduccion->codigoproducto.' )';
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view_produccion','id' => $model->id_valor, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tokenOperario' =>$tokenOperario], ['class' => 'btn btn-primary btn-sm'])?>
</p> 

<?php $form = ActiveForm::begin([
            "method" => "post",                            
       ]);
?>
<!--INICIA LOS TABS-->
<div class="table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            <?php if(count($detalle_balanceo) > 0){?>
                Listado de tallas en producción
            <?php }?>    
        </div>                       
         <table class="table table-responsive-lg">
            <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Operaciones</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Sam</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Vlr vinculado</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Vlr contrato</th>
                    <th scope="col" style='background-color:#B9D5CE;'></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $valor_contrato = 0; 
                $total_unidades = 0;
                $valor_vinculado = 0;
                if ($detalle_balanceo){
                    $empresa = app\models\Matriculaempresa::findOne(1);
                    foreach ($detalle_balanceo as $val):
                        $flujo = \app\models\FlujoOperaciones::find()->where(['=','idproceso', $val->id_proceso])->andWhere(['=','idordenproduccion', $idordenproduccion])->one();
                        $total_unidades = $flujo->cantidad_operaciones - $flujo->cantidad_confeccionadas;
                        $valor_contrato = round($val->minutos * $empresa->vlr_minuto_contrato); 
                        $valor_vinculado = round($val->minutos * $empresa->vlr_minuto_vinculado);
                        if($total_unidades != 0){ ?>
                            <tr style="font-size: 85%;">
                                <td><?= $val->id_proceso ?></td>
                                <td><?= $val->proceso->proceso ?></td>
                                <td><?= $val->minutos ?></td> 
                                <td style="text-align: right"><?= $valor_vinculado?></td> 
                                <td style="text-align: right"><?= $valor_contrato?></td> 
                                <td style= 'width: 25px; height: 25px;'>
                                    <?= Html::a('<span class="glyphicon glyphicon-send"></span> Enviar', ['valor-prenda-unidad/enviar_operacion_individual', 'id' => $model->id_valor,'tokenOperario' => $tokenOperario ,'id_detalle' => $id_detalle, 'id_planta' => $id_planta,'id_operacion' => $val->id_proceso,'idordenproduccion' => $idordenproduccion],['class' => 'btn btn-success btn-sm']); ?>  
                                </td>
                            </tr>  
                        <?php }    
                    endforeach;
                }  ?>
            </tbody>  
        </table>
    </div>

</div>
   
 
         
    <?php ActiveForm::end(); ?>
