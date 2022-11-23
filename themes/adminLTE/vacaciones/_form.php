<?php

use app\models\Empleado;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use kartik\date\DatePicker;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;

$this->title = 'Vacaciones';
$this->params['breadcrumbs'][] = ['label' => 'Vacaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => false,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
            'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-2 control-label'],
            'options' => []
        ],
        ]);
?>
<?php
$empleado = ArrayHelper::map(Empleado::find()->where(['=','contrato',1])->orderBy('nombrecorto ASC')->all(), 'id_empleado', 'nombrecorto');
?>
<div class="panel panel-success">
    <div class="panel-heading">
        Empleados
    </div>
    <div class="panel-body">
        <div class="row">
             <?= $form->field($model, 'id_empleado')->widget(Select2::classname(), [
            'data' => $empleado,
            'options' => ['placeholder' => 'Seleccione el empleado'],
            'pluginOptions' => [
                'allowClear' => true ]]);
            ?>
       </div>
    </div>
    <div class="panel-heading">
           Informacion de vacaciones
    </div>    
    <div class="panel-body">
       <div class="row">
             <?= $form->field($model, 'dias_disfrutados')->textInput(['maxlength' => true]) ?> 
             <?= $form->field($model, 'dias_pagados')->textInput(['maxlength' => true]) ?> 
        </div>	
        <div class="row">      
             <?=  $form->field($model, 'fecha_desde_disfrute')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('Y-m-d', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            <?=  $form->field($model, 'fecha_final_disfrute')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('Y-m-d', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            
        </div>
        <div class="row">     
             <?=  $form->field($model, 'fecha_ingreso')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('Y-m-d', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
          
        </div>
          
        <div class="row" col>
            <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-10 form-group">{input}{error}</div>'])->textarea(['rows' => 3]) ?>
        </div>
        <div class="panel-footer text-right">
            <a href="<?= Url::toRoute("vacaciones/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>        
        </div>
  </div>
</div>    
<?php $form->end() ?>
