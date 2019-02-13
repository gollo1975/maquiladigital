<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Fichatiempo */
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
<?php
$empleado = ArrayHelper::map(app\models\Empleado::find()->all(), 'id_empleado', 'nombrecorto');
?>
<div class="panel panel-success">
    <div class="panel-heading">
        Información Ficha Tiempo
    </div>
    <div class="panel-body">        														   		
        <div class="row">
            <?= $form->field($model, 'id_empleado')->dropDownList($empleado, ['prompt' => 'Seleccione...']) ?>    
        </div>                
        <div class="panel-footer text-right">			
            <a href="<?= Url::toRoute("fichatiempo/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success",]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
