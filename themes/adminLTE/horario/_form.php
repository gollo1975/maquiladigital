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
                'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-3 control-label'],
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
        </div>
        <div class="row">            
            <?= $form->field($model, 'desde')->input('time') ?>         
        </div>
        <div class="row">            
            <?= $form->field($model, 'hasta')->input('time') ?>         
        </div>
         <div class="row">            
           <?= $form->field($model, 'total_horas')->textInput(['maxlength' => true]) ?>         
        </div>
        <div class="panel-footer text-right">			
            <a href="<?= Url::toRoute("horario/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success",]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
