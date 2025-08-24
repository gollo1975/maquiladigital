<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\horario */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-2 control-label'],
                'options' => []
            ],
        ]);
?>

<div class="panel panel-success">
    <div class="panel-heading">
        Información Horario
    </div>
    <div class="panel-body">        														   		
        <div class="row">
            <?= $form->field($model, 'horario')->textInput(['maxlength' => true]) ?>            
            <?= $form->field($model, 'desde')->input('time') ?>         
        </div>
        <div class="row">            
            <?= $form->field($model, 'hasta')->input('time') ?>         
            <?= $form->field($model, 'total_horas')->textInput(['maxlength' => true]) ?>         
        </div>
        <div class="row">            
           <?= $form->field($model, 'tiempo_desayuno')->textInput(['maxlength' => true]) ?>         
           <?= $form->field($model, 'tiempo_almuerzo')->textInput(['maxlength' => true]) ?>         
        </div>
         <div class="row">
           <?= $form->field($model, 'abreviatura')->dropDownList(['LV'=> 'Lunes-Vierne(LV)', 'LS'=> 'Lunes-Sabado (LS)'], ['prompt' => 'Seleccione una opcion...']) ?>
           <?= $form->field($model, 'aplica_tiempo_desuso')->dropDownList(['0'=> 'NO', '1'=> 'SI'], ['prompt' => 'Seleccione una opcion...']) ?>
         </div>
        <div class="row">            
           <?= $form->field($model, 'minutos_desuso')->textInput(['maxlength' => true]) ?>         
           <?= $form->field($model, 'total_eventos_dia')->textInput(['maxlength' => true]) ?>         
        </div>
        <div class="panel-footer text-right">			
            <a href="<?= Url::toRoute("horario/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success",]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
