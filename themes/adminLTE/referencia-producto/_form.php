<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\models\TipoProducto;


/* @var $this yii\web\View */
/* @var $model app\models\Municipio */
/* @var $form yii\widgets\ActiveForm */
?>

    <?php $form = ActiveForm::begin([
		'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
	'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-3 control-label'],
                    'options' => []
                ],
	]); ?>
<?php
$tipoPrenda = ArrayHelper::map(TipoProducto::find()->orderBy('concepto ASC')->all(), 'id_tipo_producto', 'concepto');
?>
<div class="panel panel-success">
    <div class="panel-heading">
        <h3>Registros</h3>
    </div>
    <div class="panel-body">        														   		
        <div class="row">
            <?= $form->field($model, 'codigo')->textInput(['maxlength' => true, 'readonly' => true]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'descripcion_referencia')->textInput(['maxlength' => true]) ?>  					
        </div>

        <div class="row">
          <?= $form->field($model, 'id_tipo_producto')->widget(Select2::classname(), [
                'data' => $tipoPrenda,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>   
        <div class="row">
            <?= $form->field($model, 'descripcion', ['template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>'])->textarea(['rows' => 3, 'size' => 150, 'maxlength' => true]) ?>
        </div>
        <div class="panel-footer text-right">            
            <a href="<?= Url::toRoute("referencia-producto/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success",]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
