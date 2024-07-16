  <?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;
//Modelos...
use app\models\TipoProducto;



$this->title = 'Consulta (salida de bodega)';
$this->params['breadcrumbs'][] = $this->title;

?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtro");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<!--<h1>Lista Facturas</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("salida-bodega/search_detalle_insumos"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$tipo_producto = ArrayHelper::map(TipoProducto::find()->orderBy ('concepto ASC')->all(), 'id_tipo_producto', 'concepto');

?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
             <?= $formulario->field($form, "codigo_producto")->input("search") ?>
           <?= $formulario->field($form, 'referencia')->widget(Select2::classname(), [
                'data' => $ConReferencia,
                'options' => ['prompt' => 'Seleccione la referencia ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            <?= $formulario->field($form, 'fecha_corte')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
        
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("salida-bodega/search_detalle_insumos") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>
<?php
$form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
          Registros <span class="badge"><?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size: 90%;'>         
                
                <th scope="col" style='background-color:#B9D5CE;'>Numero</th>
                <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Total insumo</th> 
                <th scope="col" style='background-color:#B9D5CE;'>Fecha salida</th>
                <th scope="col" style='background-color:#B9D5CE;'>Responsable</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Costo Autorizado">Aut.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Proceso cerrado">Cerr.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
            </tr>
            </thead>
            <tbody>
                <?php
                foreach ($model as $val):
                    $detalle = \app\models\SalidaBodegaDetalle::find()->where(['=','id_salida_bodega', $val->id_salida_bodega])->one();
                    ?>
                    <tr style ='font-size: 85%;'>                
                        <td><?= $val->numero_salida?></td>
                        <td><?= $val->codigo_producto?></td>
                        <td><?= $val->producto->descripcion?></td>
                        <td style="text-align: right"><?= ''.number_format($val->unidades,0)?></td>
                        <td><?= $val->fecha_salida?></td>
                         <td><?= $val->responsable?></td>
                        <td><?= $val->autorizadoSalida?></td>
                        <td><?= $val->cerradoSalida?></td>
                        <td style="width: 25px;">				
                            <a href="<?= Url::toRoute(["salida-bodega/view", "id" => $val->id_salida_bodega, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>                
                        </td>
                        <?php if($detalle){?>
                            <td style="width: 25px;"></td>
                        <?php }else{?>
                            <td style="width: 25px;">				
                                <a href="<?= Url::toRoute(["salida-bodega/update", "id" => $val->id_salida_bodega, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                
                             </td>
                        <?php } ?>     
                    </tr>            
                <?php endforeach; ?>
            </tbody>    
        </table> 
     </div>
    <div class="panel-footer text-right" >            
           <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>                
        <?php $form->end() ?>
        </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>


