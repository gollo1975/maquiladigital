<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Cliente;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

$this->title = 'Facturas de venta';
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
    "action" => Url::toRoute("facturaventa/indexconsulta"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$clientes = ArrayHelper::map(Cliente::find()->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombreClientes');
$tipoServicio = ArrayHelper::map(\app\models\Facturaventatipo::find()->all(), 'id_factura_venta_tipo', 'concepto');
?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, 'idcliente')->widget(Select2::classname(), [
                'data' => $clientes,
                'options' => ['prompt' => 'Seleccione un cliente ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, "numero")->input("search") ?>
            <?= $formulario->field($form, 'desde')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            <?= $formulario->field($form, 'hasta')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
             <?= $formulario->field($form, 'ordenProduccion')->widget(Select2::classname(), [
                'data' => $ordenproduccion,
                'options' => ['prompt' => 'Seleccione la referencia ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $formulario->field($form, 'pendiente')->dropDownList(['1' => 'SI'],['prompt' => 'Seleccione una opcion ...']) ?>
            <?= $formulario->field($form, 'tipo_servicio')->widget(Select2::classname(), [
                'data' => $tipoServicio,
                'options' => ['prompt' => 'Seleccione el servicio ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute("facturaventa/indexconsulta") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros:  <?= $pagination->totalCount ?>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style="font-size: 85%;">                
                <th scope="col" style='background-color:#B9D5CE;'>No Factura</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cedula/Nit</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Ref.</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. Inicio</th>
                <th scope="col" style='background-color:#B9D5CE;'>F. Vencto</th>
                <th scope="col" style='background-color:#B9D5CE;'>Subtotal</th>
                <th scope="col" style='background-color:#B9D5CE;'>Total</th>
                <th scope="col" style='background-color:#B9D5CE;'>Saldo</th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Estado de la factura">Estado</span></th>
                <th scope="col" style='background-color:#B9D5CE;'><span title="Dias de mora">DM.</span></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>                               
            </tr>
            </thead>
            <tbody>
                <?php
                
                $saldo=0; $subtotal = 0;  $total = 0; $dias = 0;$dias_faltante = 0;
                foreach ($model as $val):
                    $saldo += $val->saldo;
                    $total += $val->totalpagar;
                    $subtotal += $val->subtotal;
                    //pemrite busca los dias de mora
                    $fecha_actual_str = date('Y-m-d'); // Asegúrate de que $fecha_actual esté definida como una cadena o un objeto DateTime
                    $fecha_vencimiento_str = $val->fecha_vencimiento; // Asumo que esto es una cadena
                    $fecha_actual_obj = new DateTime($fecha_actual_str);
                    $fecha_vencimiento_obj = new DateTime($fecha_vencimiento_str);
                    $diferencia = $fecha_actual_obj->diff($fecha_vencimiento_obj);
                    $dias = $diferencia->days;
                    //permite buscar los dias que faltan para la mora
                     $fecha_actual_inicio = date('Y-m-d');
                    $fecha_vencimiento_str = $val->fecha_vencimiento; // Asumo que esto es una cadena
                    $fecha_actual_obj = new DateTime($fecha_actual_inicio);
                    $fecha_vencimiento_obj = new DateTime($fecha_vencimiento_str);
                    $diferencia = $fecha_actual_obj->diff($fecha_vencimiento_obj);
                    $dias_faltante = $diferencia->days;
                    ?>
                    <tr style="font-size: 85%;">                
                        <td><?= $val->nrofactura ?></td>
                        <td><?= $val->cliente->cedulanit ?></td>
                        <td><?= $val->cliente->nombrecorto ?></td>
                        <?php if($val->idordenproduccion <>  ''){?>
                             <td><?= $val->ordenproduccion->codigoproducto ?></td>
                        <?php }else{?>
                            <td><?= 'NO FOUND' ?></td>
                        <?php }?>    
                        <td><?= $val->fecha_inicio ?></td>
                        <td><?= $val->fecha_vencimiento ?></td>
                        <td align="right"><?= number_format($val->subtotal,0) ?></td>
                        <td align="right"><?= number_format($val->totalpagar,0) ?></td>
                        <td align="right"><?= number_format($val->saldo,0) ?></td>
                        <?php if($val->saldo > 0){
                            if($val->fecha_vencimiento < $fecha_actual_str){?>
                                <td style="background-color:#ffe5ec;"><?= 'MORA' ?></td>
                                <td><?= $dias ?> Dias</td>
                            <?php } else{ ?>
                                <td style="background-color:#95d5b2;"><?= 'AL DIA' ?></td>
                                <td><?= - $dias_faltante ?> Dias</td>
                            <?php } 
                        }else{?>
                            <td style="background-color:#ffe5ec;"><?= 'CANCELADA' ?></td>
                            <td style="background-color:#95d5b2;"><?= '0' ?></td>
                             
                        <?php }?>
                       
                        <td style= 'width: 20px; height: 20px;'>				
                        <a href="<?= Url::toRoute(["facturaventa/viewconsulta", "id" => $val->idfactura]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>                
                        </td>
                    </tr>
                <?php endforeach; ?>
            </body>        
            <tr>
                <td colspan="5"></td>
                <td align="right"><b>Totales:</b></td>
                 <td align="right" ><b><?= '$ '.number_format($subtotal,0); ?></b></td>
                  <td align="right" ><b><?= '$ '.number_format($total,0); ?></b></td>
                <td align="right" ><b><?= '$ '.number_format($saldo,0); ?></b></td>
                <td colspan="2"></td>
            </tr>
        </table>    
        <div class="panel-footer text-right" >            
            <?php
                $form = ActiveForm::begin([
                            "method" => "post",                            
                        ]);
                ?>    
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary ']); ?>
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>







