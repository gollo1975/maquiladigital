<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */
/* @var $form yii\widgets\ActiveForm */
$plantas = ArrayHelper::map(\app\models\PlantaEmpresa::find()->orderBy('id_planta ASC')->all(),'id_planta','nombre_planta');
?>
<?php
$form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal condensed ', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-3 control-label'],
                'options' => []
            ],
        ]);
?>
<?php ?>
<div class="panel panel-success">
    <div class="panel-heading">
       Registro
    </div>
    <div class="panel-body">
       
        <div class="row">
            <?= $form->field($model, 'id_planta')->widget(Select2::classname(), [
             'data' => $plantas,
             'options' => ['placeholder' => 'Seleccione.... '],
             'pluginOptions' => [
             'allowClear' => true ]]);
            ?>
        </div>
        <div class="row">
            <?=
            $form->field($model, 'fecha_actual')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
        </div>
        <div class="panel-footer text-right">			
             <a href="<?= Url::toRoute("eficiencia-modulo-diario/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

