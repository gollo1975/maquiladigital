<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\web\Session;



/* @var $this yii\web\View */
/* @var $model app\models\Facturaventadetalle */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Personal al contrato';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute(["pago-adicional-fecha/importar_operaciones_contrato", 'id' => $id, 'fecha_corte' => $fecha_corte]),
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
    <div class="panel-heading">
        Parametros de entrada
    </div>
	
    <div class="panel-body" id="buscarmaquina">
        <div class="row" >
            <?= $formulario->field($form, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true]])
            ?> 
            <?= $formulario->field($form, 'fecha_final')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true]])
            ?>    
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary",]) ?>
            <a align="right" href="<?= Url::toRoute(["pago-adicional-fecha/importar_operaciones_contrato", 'id' => $id, 'fecha_corte' => $fecha_corte]) ?>" class="btn btn-primary"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>

<?php
$form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-2 control-label'],
                'options' => []
            ],
        ]);
?>

<?php
?>

<div class="table table-responsive">
    <div class="panel panel-success ">
        <div class="panel-heading">
            Registros  <span class="badge"><?= count($table) ?> </span>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th scope="col" style='background-color:#B9D5CE;'>Documento</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Empleado</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Conceto salarial</th>                    
                    <th scope="col" style='background-color:#B9D5CE;'>F. Inicio</th>
                    <th scope="col" style='background-color:#B9D5CE;'>F. Corte</th>
                     <th scope="col" style='background-color:#B9D5CE;'>Total dias</th>
                    <th scope="col" style='background-color:#B9D5CE;'>Valor pagado</th>
                    <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($table as $key => $val): ?>
                        <tr style="font-size: 85%;">
                            <td><?= Html::encode($val['documento']) ?></td>
                            <td><?= Html::encode($val['nombrecompleto'])?></td>
                            <td><?= Html::encode($val['nombre_concepto']) ?></td>
                            <td><?= Html::encode($val['fecha_inicio_periodo']) ?></td>
                            <td><?= Html::encode($val['fecha_corte_periodo']) ?></td>
                            <td style="text-align: right"><?= Html::encode($val['total_dias']) ?></td>
                            <td style="text-align: right"><?= '$'.number_format(Html::encode($val['total_valor_pagado'],0)) ?></td>
                            <td style="width: 30px;">
                                <input type="checkbox" name="selected_items[<?= $key ?>]" value="1">

                                <?= Html::hiddenInput("items_data[{$key}][id_empleado]", $val['id_empleado']) ?>
                                <?= Html::hiddenInput("items_data[{$key}][documento]", $val['documento']) ?>
                                <?= Html::hiddenInput("items_data[{$key}][nombrecompleto]", $val['nombrecompleto']) ?>
                                <?= Html::hiddenInput("items_data[{$key}][codigo_salario]", $val['codigo_salario']) ?>
                                <?= Html::hiddenInput("items_data[{$key}][nombre_concepto]", $val['nombre_concepto']) ?>
                                 <?= Html::hiddenInput("items_data[{$key}][total_dias]", $val['total_dias']) ?>
                                <?= Html::hiddenInput("items_data[{$key}][fecha_inicio_periodo]", $val['fecha_inicio_periodo']) ?>
                                <?= Html::hiddenInput("items_data[{$key}][fecha_corte_periodo]", $val['fecha_corte_periodo']) ?>
                                <?= Html::hiddenInput("items_data[{$key}][total_valor_pagado]", $val['total_valor_pagado']) ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="panel-footer text-right">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['pago-adicional-fecha/view', 'id' => $id, 'fecha_corte' => $fecha_corte], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Enviar a pago", ["class" => "btn btn-success btn-sm",'name' => 'enviar_datos']) ?>
        </div>

    </div>
</div>

<?php $form->end() ?>    

<script type="text/javascript">
	function marcar(source) 
	{
		checkboxes=document.getElementsByTagName('input'); //obtenemos todos los controles del tipo Input
		for(i=0;i<checkboxes.length;i++) //recoremos todos los controles
		{
			if(checkboxes[i].type == "checkbox") //solo si es un checkbox entramos
			{
				checkboxes[i].checked=source.checked; //si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
			}
		}
	}
</script>