<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use kartik\date\DatePicker;

$this->title = 'Configuracion de cartera';
$this->params['breadcrumbs'][] = ['label' => 'estado_cartera', 'url' => ['estado_cartera','id' => $id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="empresa-estado_cartera">
    <?php
    $form = ActiveForm::begin([
                "method" => "post",
                'id' => 'formulario',
                'enableClientValidation' => false,
                'enableAjaxValidation' => false,
                'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
                'fieldConfig' => [
                'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-3 control-label'],
                'options' => []
            ],
            ]);
    ?>
       
    <div class="panel panel-success">
        <div class="panel-heading">
            Configuracion de la cartera
        </div>
        <div class="panel-body">
            <div class="row">
                <?= $form->field($modelo, 'numero_factura')->textInput(['maxlength' => true]) ?>    
            </div>
            <div class="row">      
                <?=  $form->field($modelo, 'fecha_vencimiento')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                   'value' => date('Y-m-d', strtotime('+2 days')),
                   'options' => ['placeholder' => 'Seleccione una fecha ...'],
                   'pluginOptions' => [
                       'format' => 'yyyy-m-d',
                       'todayHighlight' => true,
                       'orientation' => 'bottom']
                    ])
               ?>
            </div>
            <div class="row">    
                <?=  $form->field($modelo, 'fecha_suspension')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                    'value' => date('Y-m-d', strtotime('+2 days')),
                    'options' => ['placeholder' => 'Seleccione una fecha ...'],
                    'pluginOptions' => [
                        'format' => 'yyyy-m-d',
                        'todayHighlight' => true,
                        'orientation' => 'bottom']
                    ])
                ?>
            
            </div>
            <div class="row">
                <?= $form->field($modelo, 'dias_adicionales')->textInput(['maxlength' => true]) ?>    
            </div>
            <div class="row">
                <?= $form->field($modelo, 'estado_registro')->dropdownList(['1' => 'NO', '0' => 'SI']) ?>
            </div>	
           
        </div>
    </div>
   
    <div class="panel-footer text-right">			
        <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
    </div>
    <?php $form->end() ?> 
</div>


