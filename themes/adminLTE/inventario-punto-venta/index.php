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
$this->title = 'INVENTARIO (GENERAL)';
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
    "action" => Url::toRoute("inventario-punto-venta/index"),
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
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "codigo")->input("search") ?>
             <?= $formulario->field($form, "producto")->input("search") ?>
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
             <?= $formulario->field($form, 'punto_venta')->widget(Select2::classname(), [
                'data' => $conPunto,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?> 
            <?= $formulario->field($form, 'inventario_inicial')->dropdownList(['0' => 'NO', '1' => 'SI'], ['prompt' => 'Seleccione...']) ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("inventario-punto-venta/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
                <tr style ='font-size: 85%;'>         
                <th score="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Nombre producto</th>
                <th scope="col" style='background-color:#B9D5CE;'>Bodega</th>
                <th scope="col" style='background-color:#B9D5CE;'>Proveedor</th>
                <th scope="col" style='background-color:#B9D5CE;'>Marca</th>
                <th scope="col" style='background-color:#B9D5CE;'>Categoria</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. proceso</th>
                <th scope="col" style='background-color:#B9D5CE;'>Entradas</th>
                <th scope="col" style='background-color:#B9D5CE;'>Stock</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th score="col" style='background-color:#B9D5CE;'></th>  
                         
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val): ?>
                <tr style ='font-size: 85%;'>  
                    <?php if($val->id_punto == 1){
                            if($val->aplica_talla_color == 0){
                                if($val->stock_inventario > 0){?>
                                    <td style="width: 20px; height: 20px">
                                        <!-- Inicio Nuevo Detalle proceso -->
                                        <?= Html::a('<span class="glyphicon glyphicon-list"></span>',
                                                ['/inventario-punto-venta/trasladar_punto_venta','id' => $val->id_inventario],
                                                [
                                                    'title' => 'Permite enviar productos por primera vez a un PUNTO DE VENTA?',
                                                    'data-toggle'=>'modal',
                                                    'data-target'=>'#modaltrasladareferenciapuntoventa'.$val->id_inventario,
                                                    'class' => ''
                                                ])    
                                           ?>
                                        <div class="modal remote fade" id="modaltrasladareferenciapuntoventa<?= $val->id_inventario ?>">
                                            <div class="modal-dialog modal-lg" style ="width: 500px;">
                                                 <div class="modal-content"></div>
                                            </div>
                                        </div> 
                                    </td>
                                <?php }else{ ?>  
                                    <td style="width: 20px; height: 20px; background-color: #fae1dd"></td>
                                <?php }
                            }else{
                                if($val->stock_inventario > 0){?>
                                    <td style="width: 20px; height: 20px">
                                         <a href="<?= Url::toRoute(["inventario-punto-venta/enviar_referencia_punto", "id" => $val->id_inventario, 'id_punto' => $val->id_punto]) ?>"><span class="glyphicon glyphicon-home" title ="Permite enviar desde BODEGA la referencia por primera vez al puntol.."></span></a>                   
                                    </td>
                                <?php 
                                }else{ ?>
                                     <td style="width: 20px; height: 20px; background-color: #d0f4de"></td>
                                <?php }
                            }    
                    }else{
                        if($val->aplica_talla_color == 1){ ?>
                            <td style="width: 20px; height: 20px">
                                <a href="<?= Url::toRoute(["inventario-punto-venta/importar_inventario_bodega", "id" => $val->id_inventario, 'id_punto' => $val->id_punto]) ?>"><span class="glyphicon glyphicon-import" title ="Permite importar unidades desde la bodega princial.."></span></a>                   
                            </td>
                        <?php }else{ ?>
                            <td style="width: 20px; height: 20px">
                                <!-- Inicio Nuevo Detalle proceso -->
                                <?= Html::a('<span class="glyphicon glyphicon-export"></span>',
                                        ['/inventario-punto-venta/trasladar_referencia_bodega_punto','id' => $val->id_inventario],
                                        [
                                            'title' => 'Permite trasladar productos de la bodega al punto de venda?',
                                            'data-toggle'=>'modal',
                                            'data-target'=>'#modaltrasladarreferenciabodegapunto'.$val->id_inventario,
                                            'class' => ''
                                        ])    
                                   ?>
                                <div class="modal remote fade" id="modaltrasladarreferenciabodegapunto<?= $val->id_inventario ?>">
                                    <div class="modal-dialog modal-lg" style ="width: 500px;">
                                         <div class="modal-content"></div>
                                    </div>
                                </div> 
                            </td>
                       <?php }    
                    }?>   
                    <td><?= $val->codigo_producto?></td>
                    <td><?= $val->nombre_producto?></td>
                    <td><?= $val->punto->nombre_punto?></td>
                    <td><?= $val->proveedor->nombrecorto?></td>
                    <td><?= $val->marca->marca?></td>
                    <td><?= $val->categoria->categoria?></td>
                    <td><?= $val->fecha_proceso?></td>
                    <td style="text-align: right;"><?= ''.number_format($val->stock_unidades,0)?></td>
                    <td style="text-align: right; background-color:#CBDDE3; color: black"><?= ''.number_format($val->stock_inventario,0)?></td>
                    <td style= 'width: 25px; height: 10px;'>
                         <a href="<?= Url::toRoute(["inventario-punto-venta/view", "id" => $val->id_inventario, 'token' => $token,'codigo' => $val->codigo_enlace_bodega]) ?>" ><span class="glyphicon glyphicon-eye-open" title="Permite crear las cantidades del producto, lote y codigos"></span></a>
                    </td> 
                    <?php if($val->stock_unidades == $val->stock_inventario && $val->aplica_talla_color == 0){?>
                        <td style= 'width: 25px; height: 10px;'>
                               <a href="<?= Url::toRoute(["inventario-punto-venta/update", "id" => $val->id_inventario]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>                   
                        </td>
                    <?php }else{?>
                        <td style= 'width: 25px; height: 10px;'></td>
                    <?php }?>    
                </tr>            
            <?php endforeach; ?>
            </tbody>    
        </table> 
        <div class="panel-footer text-right" >            
           <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm']); ?>                
            <a align="right" href="<?= Url::toRoute("inventario-punto-venta/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo sin OP</a>
        <?php $form->end() ?>
        </div>
     </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>
