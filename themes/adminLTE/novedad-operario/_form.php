<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Operarios;
use app\models\TipoNovedad;
use app\models\Arl;
use kartik\date\DatePicker;
use kartik\select2\Select2;

?>

<?php
$form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
            'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-2 control-label'],
            'options' => []
        ],
        ]);
?>

<?php
$operario = ArrayHelper::map(Operarios::find()->where(['=','estado', 1])->orderBy('nombrecompleto ASC')->all(), 'id_operario', 'nombrecompleto');
$novedad = ArrayHelper::map(TipoNovedad::find()->orderBy('novedad ASC')->all(), 'id_tipo_novedad', 'novedad');
?>
<div class="panel panel-success">
    <div class="panel-heading">
        Novedades
    </div>
    
    <div class="panel-body">
        <div class="row">
            <?= $form->field($model, 'id_tipo_novedad')->widget(Select2::classname(), [
             'data' => $novedad,
             'options' => ['placeholder' => 'Seleccione.... '],
             'pluginOptions' => [
             'allowClear' => true ]]);
            ?>
            <?= $form->field($model, 'id_operario')->widget(Select2::classname(), [
             'data' => $operario,
             'options' => ['placeholder' => 'Seleccione.... '],
             'pluginOptions' => [
             'allowClear' => true ]]);
            ?>
        </div>
        <div class="row">
            <?=  $form->field($model, 'fecha_inicio_permiso')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...'],
                           'pluginOptions' => [
                               'format' => 'yyyy-m-d',
                               'todayHighlight' => true]])
            ?>
             <?=  $form->field($model, 'fecha_final_permiso')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...'],
                           'pluginOptions' => [
                               'format' => 'yyyy-m-d',
                               'todayHighlight' => true]])
            ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'hora_inicio_permiso')->input('time'); ?>
            <?= $form->field($model, 'hora_final_permiso')->input('time'); ?>
        </div>
         <div class="row" col>
            <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-10 form-group">{input}{error}</div>'])->textarea(['rows' => 3]) ?>
        </div>
        
        <div class="panel-footer text-right">			
            <a href="<?= Url::toRoute("novedad-operario/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>
<?php $form->end() ?>     

</div>
