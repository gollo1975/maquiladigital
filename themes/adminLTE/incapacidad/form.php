<?php

use app\models\Empleado;
use app\models\ConfiguracionIncapacidad;
use app\models\DiagnosticoIncapacidad;
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

$this->title = 'Incapacidades';
$this->params['breadcrumbs'][] = ['label' => 'Incapacidades', 'url' => ['index']];
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
$diagnostico = ArrayHelper::map(DiagnosticoIncapacidad::find()->orderBy ('codigo_diagnostico ASC')->all(), 'id_codigo', 'codigo_diagnostico');
$configuracionincapacidad = ArrayHelper::map(ConfiguracionIncapacidad::find()->all(), 'codigo_incapacidad', 'nombre');
?>
<div class="panel panel-success">
    <div class="panel-heading">
        Información:Incapacidades
    </div>
    <div class="panel-body">
        <div class="row">
            <?= $form->field($model, 'codigo_incapacidad')->dropDownList($configuracionincapacidad, ['prompt' => 'Seleccione...']) ?>
             <?= $form->field($model, 'id_empleado')->widget(Select2::classname(), [
            'data' => $empleado,
            'options' => ['placeholder' => 'Seleccione el empleado'],
            'pluginOptions' => [
                'allowClear' => true ]]);
            ?>
       </div>
       <div class="row">
            <?= $form->field($model, 'id_codigo')->widget(Select2::classname(), [
            'data' => $diagnostico,
            'options' => ['placeholder' => 'Seleccione el diagnostico'],
            'pluginOptions' => [
                'allowClear' => true ]]);
            ?>
             <?= $form->field($model, 'numero_incapacidad')->textInput(['maxlength' => true]) ?> 
        </div>	
        <div class="row">   
            <?= $form->field($model, 'nombre_medico')->textInput(['maxlength' => true]) ?>
           
        </div>
        <div class="row">      
             <?=  $form->field($model, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('Y-m-d', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']
                 ])
            ?>
            <?=  $form->field($model, 'fecha_final')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('Y-m-d', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']
                ])
            ?>
            
        </div>
        <div class="row">     
             <?=  $form->field($model, 'fecha_documento_fisico')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('Y-m-d', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']
                 ])
            ?>
            <?=  $form->field($model, 'fecha_aplicacion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('Y-m-d', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true,
                    'orientation' => 'bottom']
                ])
            ?>
        </div>
        <div class="row" col>
            <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-10 form-group">{input}{error}</div>'])->textarea(['rows' => 3]) ?>
        </div>
        <div class="panel panel-success">
            <div class="panel-heading">
                Seleccione el estado del incapacidad.
            </div>
            <div class="panel-body">
                <div class="checkbox checkbox-success" align ="left">
                        <?= $form->field($model, 'transcripcion')->checkBox(['label' => 'Transcripción','' =>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'transcripcion']) ?>
                        <?= $form->field($model, 'cobrar_administradora')->checkBox(['label' => 'Cobrar_administradora',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'cobrar_administradora']) ?>
                        <?= $form->field($model, 'aplicar_adicional')->checkBox(['label' => 'Aplicar_adicional',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplicar_adicional']) ?>
                        <?= $form->field($model, 'pagar_empleado')->checkBox(['label' => 'Pagar empleado',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'pagar_empleado']) ?>
                        <?= $form->field($model, 'prorroga')->checkBox(['label' => 'Prorroga',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'prorroga']) ?>
                </div>
            </div>
        </div>    
        <div class="panel-footer text-right">
            <a href="<?= Url::toRoute("incapacidad/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>        
        </div>
  </div>
</div>    
<?php $form->end() ?>
