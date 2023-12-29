
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Cliente;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\time\TimePicker;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */
/* @var $form yii\widgets\ActiveForm */
$proceso_confeccion = ArrayHelper::map(\app\models\ProcesoConfeccionPrenda::find()->orderBy('id_proceso_confeccion ASC')->all(),'id_proceso_confeccion','descripcion_proceso');
$plantas = ArrayHelper::map(\app\models\PlantaEmpresa::find()->orderBy('id_planta ASC')->all(),'id_planta','nombre_planta');
$horario = ArrayHelper::map(\app\models\Horario::find()->orderBy('id_horario ASC')->all(),'id_horario','horario');
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
        Nuevo balanceo
    </div>
    <div class="panel-body">
        <div class="row">
            <?= $form->field($orden, 'idordenproduccion')->input('text', ['idordenproduccion', 'readonly' => TRUE, ['style' => 'width:15%']])?>
        </div>
        
        <div class="row">
            <?=
            $form->field($model, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
        </div>
        
        <div class="row">
            <?= $form->field($model, 'cantidad_empleados')->textInput(['maxlength' => true, 'required' => true]) ?>
        </div>
        <div class="row">
           <?= $form->field($model, 'modulo')->dropDownList(['1'=> 1, '2'=> 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, '10' => 10], ['prompt' => 'Seleccione una opcion...']) ?>
         </div>  
        <div class="row">
            <?= $form->field($model, 'id_proceso_confeccion')->widget(Select2::classname(), [
             'data' => $proceso_confeccion,
             'options' => ['placeholder' => 'Seleccione.... '],
             'pluginOptions' => [
             'allowClear' => true ]]);
            ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'id_planta')->widget(Select2::classname(), [
             'data' => $plantas,
             'options' => ['placeholder' => 'Seleccione.... '],
             'pluginOptions' => [
             'allowClear' => true ]]);
            ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'id_horario')->widget(Select2::classname(), [
             'data' => $horario,
             'options' => ['placeholder' => 'Seleccione.... '],
             'pluginOptions' => [
             'allowClear' => true ]]);
            ?>
        </div>
        <div class="row">
        <?= $form->field($model, 'hora_inicio')->input ('time'); ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'observacion')->textArea(['maxlength' => true]) ?>
        </div>
        <div class="panel-footer text-right">			
             <a href="<?= Url::toRoute("balanceo/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

