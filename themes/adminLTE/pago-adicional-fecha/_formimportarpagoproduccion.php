<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Facturaventadetalle */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Importar dia sabado';
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
    // *** LÍNEA CORREGIDA AQUÍ ***
    "action" => Url::toRoute(['pago-adicional-fecha/importarpagoproduccion', 'id' => $id, 'fecha_corte' => $fecha_corte]),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-2 control-label'],
        'options' => []
    ],
]);?>

<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
    
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row">
           <?= $formulario->field($form, 'fecha_inicio')->widget(DatePicker::className(), [
                'name' => 'check_issue_date', // Este 'name' no es el que usa ActiveForm para el modelo
                'value' => date('d-M-Y', strtotime('+2 days')), // Esto establecerá un valor inicial
                'options' => ['placeholder' => 'Seleccione una fecha'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true
                ]
            ]) ?>
           <?= $formulario->field($form, 'fecha_final')->widget(DatePicker::className(), [
                'name' => 'check_issue_date', // Este 'name' tampoco es el que usa ActiveForm para el modelo
                'value' => date('d-M-Y', strtotime('+2 days')), // Esto establecerá un valor inicial
                'options' => ['placeholder' => 'Seleccione una fecha'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true
                ]
            ]) ?>
        </div>
         <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute(["pago-adicional-fecha/importarpagoproduccion", 'id' => $id, 'fecha_corte' => $fecha_corte]) ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>  

<?php $formulario->end() ?>

<?= Html::beginForm(Url::toRoute(['pago-adicional-fecha/importarpagoproduccion', 'id' => $id, 'fecha_corte' => $fecha_corte]), 'post') ?>
 <!-- These fields will carry the dates used for filtering to the POST request -->
    <?= Html::hiddenInput('fecha_inicio_filter', $form->fecha_inicio ?? '') ?>
    <?= Html::hiddenInput('fecha_final_filter', $form->fecha_final ?? '') ?>

<div class="table table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
          <?php if($valores){?>
            Registros <span class="badge"><?= count($valores) ?></span>
          <?php }?>  
           
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr style="font-size: 85%">
                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Operario</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Area de proceso</th> 
                     <th scope="col" style='background-color:#B9D5CE;'>Valor generado</th> 
                    <th scope="col" style='background-color:#B9D5CE; width: 20px;'><input type="checkbox" onclick="marcar(this);"/></th>
                </tr>
                </thead>
                <tbody>
                    <?php 
                    if(!empty($valores)){
                        foreach ($valores as $val):  ?>
                            <tr style="font-size: 85%;">
                                <td><?= Html::encode($val['documento']) ?></td>
                                <td><?= Html::encode($val['nombrecompleto'])?></td>
                                <td><?= Html::encode($val['tipo'])?></td>
                                <td style="text-align: right;"><?= Html::encode(''. number_format($val['total_vlr_pago'],0))?></td>
                                <td style="width: 20px; height: 20px;">
                                        <input type="checkbox" name="id_operario_grouped[]" value="<?= Html::encode($val['id_operario']) ?>">
                                </td>
                             </tr>
                        <?php endforeach;
                    }else{ ?>
                        <div class="alert alert-info">No hay registros para mostrar o no se han aplicado filtros.</div>
                    <?php }?>
                 </tbody>
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['pago-adicional-fecha/view', 'id' => $id, 'fecha_corte' => $fecha_corte], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar Pagos Seleccionados", ["class" => "btn btn-success btn-sm", 'name' => 'enviar_valores', 'value' => '1']) ?>
        </div>

    </div>
</div>
<?= Html::endForm() ?>
 
<script type="text/javascript">
	// Función para marcar/desmarcar todos los checkboxes
    function marcar(source) {
        checkboxes = document.getElementsByName('id_operario_grouped[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>