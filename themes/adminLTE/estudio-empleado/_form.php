<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Municipio;
use app\models\Empleado;
use app\models\TipoEstudios;
use kartik\date\DatePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Empleado */
/* @var $form yii\widgets\ActiveForm */
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
$empleado = ArrayHelper::map(Empleado::find()->orderBy('nombrecorto ASC')->all(), 'id_empleado', 'nombrecorto');
$municipio = ArrayHelper::map(Municipio::find()->orderBy('municipio ASC')->all(), 'idmunicipio', 'municipio');
$tipoestudio = ArrayHelper::map(TipoEstudios::find()->orderBy('id_tipo_estudio ASC')->all(), 'id_tipo_estudio', 'estudio');
?>
<div class="panel panel-success">
    <div class="panel-heading">
        Estudios empleados
    </div>
    
    <div class="panel-body">
        <div class="row">
            <?= $form->field($model, 'id_empleado')->widget(Select2::classname(), [
            'data' => $empleado,
            'options' => ['placeholder' => 'Seleccioneele empleado'],
            'pluginOptions' => [
                'allowClear' => true ]]);
            ?>
            <?= $form->field($model, 'institucion_educativa')->textInput(['maxlength' => true]) ?>  
        </div>
        <div class="row">
            <?= $form->field($model, 'id_tipo_estudio')->widget(Select2::classname(), [
            'data' => $tipoestudio,
            'options' => ['placeholder' => 'Seleccioneele empleado'],
            'pluginOptions' => [
                'allowClear' => true ]]);
            ?>
           <?= $form->field($model, 'idmunicipio')->widget(Select2::classname(), [
            'data' => $municipio,
            'options' => ['placeholder' => 'Seleccione el municipio'],
            'pluginOptions' => [
                'allowClear' => true ]]);
            ?>
        </div>        
        <div class="row">
            <?= $form->field($model, 'titulo_obtenido')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'anio_cursado')->dropDownList(['1' => '1', '2' => '2','3' => '3', '4' => '4','5' => '5', '6' => '6','7' => '7', '8' => '8','9' => '9', '10' => '10','11' => '11'], ['prompt' => 'Seleccione una opcion...']) ?>
        </div>     
        <div class="row">
            <?=
            $form->field($model, 'fecha_inicio')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true]])
            ?>    
            <?=
            $form->field($model, 'fecha_terminacion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true]])
            ?>    
        </div>        
        <div class="row">
           <?= $form->field($model, 'graduado')->dropDownList(['0' => 'NO', '1' => 'SI'], ['prompt' => 'Seleccione una opcion...']) ?> 					
             <?=
            $form->field($model, 'fecha_vencimiento')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true]])
            ?>    
        </div>        
        <div class="row">
            <?= $form->field($model, 'registro')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'validar_vencimiento')->dropDownList(['0' => 'NO', '1' => 'SI'], ['prompt' => 'Seleccione una opcion...']) ?>
        </div>                
        <div class="row" col>
            <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-10 form-group">{input}{error}</div>'])->textarea(['rows' => 2]) ?>
        </div>
        <div class="panel-footer text-right">			
            <a href="<?= Url::toRoute("estudio-empleado/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>
<?php $form->end() ?>     

</div>
