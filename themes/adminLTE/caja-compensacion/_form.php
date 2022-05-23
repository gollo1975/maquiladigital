<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Municipio;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\CajaCompensacion */
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
$municipio = ArrayHelper::map(Municipio::find()->all(), 'idmunicipio', 'municipio');
?>


<div class="panel panel-success">
    <div class="panel-heading">
        Información Caja Compensacion
    </div>
    <div class="panel-body">        														   		
        <div class="row">
            <?= $form->field($model, 'caja')->textInput(['maxlength' => true]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'telefono')->textInput(['maxlength' => true]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'direccion')->textInput(['maxlength' => true]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'codigo_caja')->textInput(['maxlength' => true]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'codigo_interfaz')->textInput(['maxlength' => true]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'porcentaje_caja')->textInput(['maxlength' => true]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'idmunicipio')->dropDownList($municipio, ['prompt' => 'Seleccione una opcion...']) ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'estado')->dropdownList(['1' => 'SI', '0' => 'NO']) ?>
        </div>	        
        <div class="panel-footer text-right">                
            <a href="<?= Url::toRoute("caja-compensacion/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success",]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
