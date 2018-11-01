<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Banco */
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

 <div class="panel panel-success">
    <div class="panel-heading">
        <h4>Información Banco</h4>
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
			<?= $form->field($model, 'telefonobanco')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="row">
			<?= $form->field($model, 'producto')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="row">
			<?= $form->field($model, 'numerocuenta')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="row">
			<?= $form->field($model, 'nitmatricula')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="row">
			<?= $form->field($model, 'activo')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="row">
			<?= $form->field($model, 'nitmatricula')->textInput() ?>
		</div>
		<div class="panel-footer text-right">
			<?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success",]) ?>
			<a href="<?= Url::toRoute("banco/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>


