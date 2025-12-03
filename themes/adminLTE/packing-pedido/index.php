<?php

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

$this->title = 'PACKING DESPACHOS';
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
    "action" => Url::toRoute("packing-pedido/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$ConCliente = ArrayHelper::map(app\models\Cliente::find()->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombrecorto');
$ConTransportadora = ArrayHelper::map(app\models\Transportadora::find()->orderBy('razon_social ASC')->all(), 'id_transportadora', 'razon_social');

?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
         
            <?= $formulario->field($form, 'cliente')->widget(Select2::classname(), [
                 'data' => $ConCliente,
                 'options' => ['prompt' => 'Seleccione...'],
                 'pluginOptions' => [
                     'allowClear' => true
                 ],
             ]); ?>
                     
            <?=  $formulario->field($form, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...'],
                           'pluginOptions' => [
                               'format' => 'yyyy-m-d',
                               'todayHighlight' => true,
                               'orientation' => 'bottom']])
            ?>
            <?=  $formulario->field($form, 'fecha_corte')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...'],
                           'pluginOptions' => [
                           'format' => 'yyyy-m-d',
                           'todayHighlight' => true,
                           'orientation' => 'bottom']])
            ?>
             <?= $formulario->field($form, "numero")->input("search") ?>
            <?= $formulario->field($form, 'transportadora')->widget(Select2::classname(), [
                 'data' => $ConTransportadora,
                 'options' => ['prompt' => 'Seleccione...'],
                 'pluginOptions' => [
                     'allowClear' => true
                 ],
             ]); ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("packing-pedido/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
        Registros: <span class="badge"> <?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:85%;'>                
                <th scope="col" style='background-color:#B9D5CE;'>Numero</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha y hora</th>
                <th scope="col" style='background-color:#B9D5CE;'>Transportadora</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                              
            </tr>
            </thead>
            <tbody>
            <?php 
             
            foreach ($modelo as $val):?>
                <tr style='font-size:85%;'>                
                    <td><?= $val->numero_packing?></td>
                     <td><?= $val->cliente->cedulanit ?></td>
                    <td><?= $val->cliente->nombrecorto ?></td>
                    <td><?= $val->fecha_hora_registro ?></td>
                     <td><?= $val->transportadora->razon_social ?? 'NO FOUNT'?></td>
                   
                    <td style= 'width: 25px; height: 25px;'>
                            <a href="<?= Url::toRoute(["packing-pedido/view", "id" => $val->id_packing]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                    </td>
                  
             
            </tbody>            
            <?php endforeach; ?>
        </table>    
        <div class="panel-footer text-right" >     
            <?php  if(count($modelo) > 0){
              echo Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar a excel", ['name' => 'excel','class' => 'btn btn-primary btn-xs ']); 
            }?>
        </div>
      <?php $form->end() ?>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>


