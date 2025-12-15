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

$this->title = 'PEDIDOS';
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
    "action" => Url::toRoute("pedidos/search_pedidos"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
$ConVendedor = ArrayHelper::map(app\models\AgentesComerciales::find()->orderBy('nombre_completo ASC')->all(), 'id_agente', 'nombre_completo');
if($TokenAcceso){
    $ConCliente = ArrayHelper::map(app\models\Cliente::find()->where(['id_agente' => $TokenAcceso])->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombrecorto');
}else{
    $ConCliente = ArrayHelper::map(app\models\Cliente::find()->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombrecorto');
}

?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
           <?php if($TokenAcceso){?>
                <?= $formulario->field($form, 'cliente')->widget(Select2::classname(), [
                     'data' => $ConCliente,
                     'options' => ['prompt' => 'Seleccione...'],
                     'pluginOptions' => [
                         'allowClear' => true
                     ],
                 ]); ?>
           <?php }else{?>
                <?= $formulario->field($form, 'cliente')->widget(Select2::classname(), [
                     'data' => $ConCliente,
                     'options' => ['prompt' => 'Seleccione...'],
                     'pluginOptions' => [
                         'allowClear' => true
                     ],
                 ]); ?>
                <?= $formulario->field($form, 'vendedor')->widget(Select2::classname(), [
                    'data' => $ConVendedor,
                    'options' => ['prompt' => 'Seleccione...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                  ]);
                ?>
           <?php }?> 
            
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
             <?= $formulario->field($form, 'entregado')->dropdownList(['0' => 'NO', '1' => 'SI'], ['prompt' => 'Seleccione...']) ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("pedidos/search_pedidos") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <?php if(!$TokenAcceso){ ?>
                    <th scope="col" style='background-color:#B9D5CE;'>Vendedor</th> 
                    <th scope="col" style='background-color:#B9D5CE;'><span class="badge" title="Pedido despachado">PE</span></th>
                    <th scope="col" style='background-color:#B9D5CE;'>F. Envio email</th>
                <?php }?>    
                <th scope="col" style='background-color:#B9D5CE;'>F. pedido</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. entrega</th>
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
                    <?php if(!$TokenAcceso){ ?>
                        <td><?= $val->cliente->agente->nombre_completo ?? 'NOT FOUND'?></td>
                        <?php if($val->pedido_despachado == 0){?>
                            <td><?= $val->pedidoEntregado?></td>
                         <?php }else { ?>
                             
                             <td style="background-color: #B9D5AA"> <?= $val->pedidoEntregado?></td>
                        <?php }  ?>
                             <td><?= $val->fecha_envio_email ?></td>
                    <?php }?>
                    <td><?= $val->fecha_pedido ?></td>
                     <td><?= $val->fecha_entrega ?></td>
                   
                    <td style= 'width: 25px; height: 25px;'>
                            <a href="<?= Url::toRoute(["pedidos/view", "id" => $val->id_pedido,'token' => $token]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                    </td>
                   
             
            </tbody>            
            <?php endforeach; ?>
        </table>    
        <div class="panel-footer text-right" >            
            <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar excel", ['name' => 'excel','class' => 'btn btn-primary btn-xs ']); ?>                
            
        </div>
      <?php $form->end() ?>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>


