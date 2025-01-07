    <?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Periodopago */
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
        Información Periodo Pago
    </div>
    <div class="panel-body">        														   		
        <div class="row">
            <?= $form->field($model, 'nombre_periodo')->textInput(['maxlength' => true]) ?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'dias')->textInput(['maxlength' => true]) ?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'limite_horas')->textInput(['maxlength' => true]) ?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'continua')->textInput(['maxlength' => true]) ?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'periodo_mes')->textInput(['maxlength' => true]) ?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'codigo_api_nomina')->textInput(['maxlength' => true]) ?> 
        </div>
        <div class="panel-footer text-right">                
            <a href="<?= Url::toRoute("periodo-pago/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>