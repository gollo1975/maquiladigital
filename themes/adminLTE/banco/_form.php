<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Banco */
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
<body onload= "mostrar()">
<!--<h1>Editar Cliente</h1>-->

<div class="panel panel-success">
    <div class="panel-heading">
        Información Banco
    </div>
    <div class="panel-body">
        <div class="row">
            <?= $form->field($model, 'idbanco')->textInput(['maxlength' => true]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'nitbanco')->textInput(['maxlength' => true]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'entidad')->textInput(['maxlength' => true]) ?>  					
        </div>
        <div class="row">
            <?= $form->field($model, 'telefonobanco')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'direccionbanco')->textInput(['maxlength' => true]) ?>
        </div>        
        <div class="row">
             <?= $form->field($model, 'producto')->dropDownList(['S' => 'AHORRO', 'D' => 'CORIENTE', 'E' => 'EFECTIVO'], ['prompt' => 'Seleccione una opcion...']) ?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'numerocuenta')->textInput(['maxlength' => true]) ?>
        </div>        
        <div class="row">
            <?= $form->field($model, 'activo')->dropdownList(['1' => 'Activo', '0' => 'Inactivo'], ['prompt' => 'Seleccione...']) ?>
        </div>        
        <div class="panel-footer text-right">			
            <a href="<?= Url::toRoute("banco/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success",]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>


