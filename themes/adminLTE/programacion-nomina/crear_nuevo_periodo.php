<?php
//modelos

//clases
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
?>
<?php

$form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
            'template' => '{label}<div class="col-sm-8 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-3 control-label'],
            'options' => []
        ],
        ]);
?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
    </div>
    <div class="modal-body">        
        <div class="table table-responsive">
            <div class="panel panel-success ">
                <div class="panel-heading" style="text-align: left ">
                   Crear periodo nomina electronica
                </div>
                <div class="panel-body">
                   <div class="row">
                        <?= $form->field($model, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                              'value' => date('d-M-Y', strtotime('+2 days')),
                              'options' => ['placeholder' => 'Seleccione una fecha ...','required' => true],
                              'pluginOptions' => [
                                  'format' => 'yyyy-m-d',
                                  'todayHighlight' => true]])
                        ?>
                    </div>    
                    <div class="row">
                        <?= $form->field($model, 'fecha_corte')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                              'value' => date('d-M-Y', strtotime('+2 days')),
                              'options' => ['placeholder' => 'Seleccione una fecha ...','required' => true],
                              'pluginOptions' => [
                                  'format' => 'yyyy-m-d',
                                  'todayHighlight' => true]])
                        ?>
                    </div>  
                    <div class="row">
                         <?= $form->field($model, 'tipo_nomina')->dropDownList(['9'=> 'Nomina individual', '10'=> 'Nomina de ajuste'], ['prompt' => 'Seleccione una opcion...','required' => true]) ?>
                    </div>
                       
                </div>  
                    <div class="panel-footer text-right">
                       <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Crear periodo", ["class" => "btn btn-primary", 'name' => 'crear_periodo']) ?>                    
                   </div>
                
            </div>
           
        </div>
    </div>
<?php $form->end() ?> 

