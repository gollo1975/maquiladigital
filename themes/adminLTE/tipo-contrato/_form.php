<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\ConfiguracionFormatoPrefijo;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\TipoRecibo */
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
$configuracionformato = ArrayHelper::map(ConfiguracionFormatoPrefijo::find()->all(), 'id_configuracion_prefijo', 'formato');
?>


<div class="panel panel-success">
    <div class="panel-heading">
        Información Tipo Contrato
    </div>
    <div class="panel-body">        														   		
        <div class="row">
            <?= $form->field($model, 'contrato')->textInput(['maxlength' => true]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'prorroga')->dropdownList(['1' => 'SI', '0' => 'NO']) ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'nro_prorrogas')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'estado')->dropdownList(['1' => 'SI', '0' => 'NO']) ?>
        </div>	
        <div class="row">
            <?= $form->field($model, 'prefijo')->dropdownList(['CAI' => 'CAI', 'CFI' => 'CFI','COL' => 'COL','CAS' => 'CAS','CPE' => 'CPE', 'CPD' => 'CPD']) ?>
        </div>
        <div class="row">
          <?= $form->field($model, 'id_configuracion_prefijo')->dropDownList($configuracionformato, ['prompt' => 'Seleccione...']) ?>                      
        </div> 
         <div class="row">
            <?= $form->field($model, 'codigo_api_enlace')->textInput(['maxlength' => true]) ?>
        </div>	
        <div class="panel-footer text-right">                
            <a href="<?= Url::toRoute("tipo-contrato/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
