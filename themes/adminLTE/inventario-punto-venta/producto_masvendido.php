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
$this->title = 'CONSULTA (Productos mas vendido)';
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
    "action" => Url::toRoute("inventario-punto-venta/producto_masvendido"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-4 control-label'],
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
            <?= $formulario->field($form, 'cantidad_mostrar')->dropdownList(['1' => '1', '3' => '3','5' => '5', '10' => '10','15' => '15'], ['prompt' => 'Seleccione...']) ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("inventario-punto-venta/producto_masvendido") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
        
       Informacion de la consulta...
        
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size: 90%;'>         
                <th scope="col" style='background-color:#B9D5CE;'>Codigo producto</th>
                <th scope="col" style='background-color:#B9D5CE;'>Nombre producto</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                <th scope="col" style='background-color:#B9D5CE;'>Punto venta</th>
                <th scope="col" style='background-color:#B9D5CE;'>Proveedor</th>
                
                         
            </tr>
            </thead>
            <tbody>
            <?php
            if($listado){
               $aux = 0;
                foreach ($listado as $key => $producto): 
                    if($aux <> $producto['codigo_producto']){ ?>
                        <tr style ='font-size: 90%;'>  
                            <td><?= $producto['codigo_producto']?></td>
                            <td><?= $producto['nombre_producto']?></td>
                            <td><?= $producto['cantidad']?></td>
                            <td><?= $producto['punto']?></td>
                            <td><?= $producto['proveedor']?></td>
                        </tr> 
                    <?php
                        $aux = $producto['codigo_producto'];
                    }else{
                        $aux = $producto['codigo_producto'];
                    }    
                endforeach;
            }?>
            </tbody>    
        </table> 
     </div>
</div>
<?php $formulario->end() ?>

