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
            'labelOptions' => ['class' => 'col-sm-4 control-label'],
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
                  REGLA DESCUENTOS COMERCIALES
                </div>
                <div class="panel-body">
                    <div class="row">
                        <?= $form->field($model, 'tipo_descuento')->dropdownList(['1' => 'PORCENTAJE', '2' => 'VALORES'], ['prompt' => 'Seleccione...']) ?>
                    </div>
                    <div class="row">
                        <?= $form->field($model, 'nuevo_valor')->textInput(['maxlength' => true, 'required' => true]) ?>
                    </div>
                    <div class="row">
                        <?=  $form->field($model, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...'],
                           'pluginOptions' => [
                               'format' => 'yyyy-m-d',
                               'todayHighlight' => true,
                               'orientation' => 'bottom']])
                       ?>
                    </div>
                    <div class="row">
                        <?=  $form->field($model, 'fecha_final')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...'],
                           'pluginOptions' => [
                               'format' => 'yyyy-m-d',
                               'todayHighlight' => true,
                               'orientation' => 'bottom']])
                       ?>
                    </div>
                    <?php if($sw == 1){?>
                        <div class="row">
                            <?= $form->field($model, 'estado')->dropdownList(['0' => 'ACTIVO', '1' => 'INACTIVO'], ['prompt' => 'Seleccione...']) ?>
                        </div>
                    <?php }?>
                </div>  
                    <div class="panel-footer text-right">
                       <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Actualizar", ["class" => "btn btn-primary", 'name' => 'regla_distribuidor']) ?>                    
                   </div>
                
            </div>
           
        </div>
    </div>
<?php $form->end() ?> 

