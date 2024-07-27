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

$this->title = 'Ventas de clientes';
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
    "action" => Url::toRoute("orden-fabricacion/cargar_pedidos"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
            <?= $formulario->field($form, "codigo")->input("search") ?>
             <?= $formulario->field($form, "referencia")->input("search") ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("orden-fabricacion/cargar_pedidos") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <th scope="col" style='background-color:#B9D5CE;'>Id</th>    
                <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>Numero pedido</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Fecha pedido</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
            </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($modelo as $val):?>
                    <tr style='font-size:85%;'>     
                        <td><?= $val->id_referencia ?></td>
                        <td><?= $val->codigo ?></td>
                        <td><?= $val->referencia ?></td>
                        <td style="text-align: right"><?= ''. number_format($val->cantidad,0) ?></td>
                           <td><?= $val->pedido->cliente->nombrecorto ?></td>
                           <td style="text-align: right"><?= $val->pedido->numero_pedido ?></td>
                        <td><?= $val->pedido->fecha_pedido ?></td>
                        <td style= 'width: 25px; height: 10px;'>
                            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> ', ['orden-fabricacion/enviar_pedido_fabricacion', 'id_referencia' => $val->id_referencia], [
                                          'class' => '',
                                          'title' => 'Proceso que permite cargar las referencias vendidas a clientes.', 
                                          'data' => [
                                              'confirm' => 'Esta seguro de enviar esta referencia al proceso de fabricacion con pedido No ('.$val->pedido->numero_pedido.').',
                                              'method' => 'post',
                                          ],
                            ]);?>
                        </td> 
                    </tr>  
                    <?php
                endforeach; ?>
            </tbody>         
        </table>    
      <?php $form->end() ?>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>


