<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Operarios;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Pedidos';
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
    "action" => Url::toRoute("pedido-cliente/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$ConCliente = ArrayHelper::map(app\models\Cliente::find()->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombrecorto');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "numero")->input("search") ?>
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
                               'todayHighlight' => true]])
            ?>
            <?=  $formulario->field($form, 'fecha_corte')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...'],
                           'pluginOptions' => [
                           'format' => 'yyyy-m-d',
                           'todayHighlight' => true]])
            ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("pedido-cliente/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <th scope="col" style='background-color:#B9D5CE;'>Numero pedido</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha pedido</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha entrega</th>
                <th scope="col" style='background-color:#B9D5CE;'>Unidades</th>
                <th scope="col" style='background-color:#B9D5CE;'>Subtotal</th>
                <th scope="col" style='background-color:#B9D5CE;'>Iva</th>
                <th scope="col" style='background-color:#B9D5CE;'>Total pedido</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
              
            </tr>
            </thead>
            <tbody>
            <?php 
             
            foreach ($modelo as $val):?>
                <tr style='font-size:85%;'>                
                    <td><?= $val->numero_pedido ?></td>
                     <td><?= $val->cliente->cedulanit ?></td>
                    <td><?= $val->cliente->nombrecorto ?></td>
                    <td><?= $val->fecha_pedido ?></td>
                     <td><?= $val->fecha_entrega ?></td>
                    <td style="text-align: right"><?= ''. number_format($val->total_unidades,0) ?></td>
                    <td style="text-align: right"><?= '$'. number_format($val->valor_total,0) ?></td>
                    <td style="text-align: right"><?= '$'. number_format($val->impuesto,0) ?></td>
                    <td style="text-align: right"><?= '$'. number_format($val->total_pedido,0) ?></td>
                    <td style= 'width: 25px; height: 25px;'>
                            <a href="<?= Url::toRoute(["pedido-cliente/view", "id" => $val->id_pedido, 'token' => $token,]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                    </td>
                    <?php if(!\app\models\PedidoClienteReferencias::find()->where(['=','id_pedido', $val->id_pedido])->one()){?>
                        <td style= 'width: 25px; height: 25px;'>
                                <a href="<?= Url::toRoute(["pedido-cliente/update", "id" => $val->id_pedido ]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                        </td>
                    <?php }else{?>
                        <td style= 'width: 25px; height: 25px;'></td>
                    <?php }?>    
             
            </tbody>            
            <?php endforeach; ?>
        </table>    
        <div class="panel-footer text-right" >            
            <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
            <a align="right" href="<?= Url::toRoute("pedido-cliente/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a>
        </div>
      <?php $form->end() ?>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>


