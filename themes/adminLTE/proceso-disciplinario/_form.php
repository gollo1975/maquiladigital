<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\helpers\Url;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\models\ProcesoDisciplinario */
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
                'labelOptions' => ['class' => 'col-sm-4 control-label'],
                'options' => []
            ],
        ]);
$conEmpleado = ArrayHelper::map(\app\models\Empleado::find()->where(['=','contrato', 1])->orderBy('nombrecorto ASC')->all(), 'id_empleado', 'nombrecorto');
$conProceso = ArrayHelper::map(\app\models\TipoProcesoDisciplinario::find()->where(['<>','id_formato_contenido', 'Null'])->all(), 'id_tipo_disciplinario', 'concepto');
$conMotivo = ArrayHelper::map(\app\models\MotivoDisciplinario::find()->all(), 'id_motivo', 'concepto');
$departamento = ArrayHelper::map(app\models\Departamento::find()->orderBy('departamento ASC')->all(), 'iddepartamento', 'departamento');
$municipio = ArrayHelper::map(app\models\Municipio::find()->orderBy('municipio DESC')->all(), 'idmunicipio', 'municipio');
?>
<div class="proceso-disciplinario-form">
    <div class="panel panel-success">
        <div class="panel-heading">
            Proceso disciplinario
        </div>
        <div class="panel-body">
            <div class="row">
                <?= $form->field($model, 'id_empleado')->widget(Select2::classname(), [
                       'data' => $conEmpleado,
                       'options' => ['prompt' => 'Seleccione...'],
                       'pluginOptions' => [
                           'allowClear' => true
                       ],
                ]); ?> 
            </div>
            <div class="row">
                <?= $form->field($model, 'id_tipo_disciplinario')->widget(Select2::classname(), [
                       'data' => $conProceso,
                       'options' => ['prompt' => 'Seleccione...'],
                       'pluginOptions' => [
                           'allowClear' => true
                       ],
                ]); ?> 
            </div>
             <div class="row">
                <?= $form->field($model, 'id_motivo')->widget(Select2::classname(), [
                       'data' => $conMotivo,
                       'options' => ['prompt' => 'Seleccione...'],
                       'pluginOptions' => [
                           'allowClear' => true
                       ],
                ]); ?> 
            </div>
            <div class="row">
                <?= $form->field($model, 'iddepartamento')->widget(Select2::classname(), [
                    'data' => $departamento,
                    'options' => ['placeholder' => 'Seleccione un departamento', 'required' => true],
                    'pluginOptions' => ['allowClear' => true],
                    'pluginEvents' => [
                        "change" => 'function() { $.get( "' . Url::toRoute('empleado/municipio') . '", { id: $(this).val() } )
                                .done(function( data ) {
                                    $( "#' . Html::getInputId($model, 'idmunicipio') . '" ).html( data );
                            });
                        }',
                        ],
                    ]);
                ?>
                  
            </div>
            <div class="row">
                  <?= $form->field($model, 'idmunicipio')->dropDownList($municipio, ['prompt' => 'Seleccione...']) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'fecha_falta')->widget(DatePicker::className(), ['name' => 'check_issue_date', 
                    'value' => date('d-M-Y', strtotime('+2 days')),
                    'options' => ['placeholder' => 'Seleccione una fecha ...'], 
                    'pluginOptions' => [
                        'format' => 'yyyy-m-d',
                        'todayHighlight' => true,
                        'required' => true]])
                ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'fecha_inicio_suspension')->widget(DatePicker::className(), ['name' => 'check_issue_date', 
                    'value' => date('d-M-Y', strtotime('+2 days')),
                    'options' => ['placeholder' => 'Seleccione una fecha ...'], 
                    'pluginOptions' => [
                        'format' => 'yyyy-m-d',
                        'todayHighlight' => true,
                        'required' => true]])
                ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'fecha_final_suspension')->widget(DatePicker::className(), ['name' => 'check_issue_date', 
                    'value' => date('d-M-Y', strtotime('+2 days')),
                    'options' => ['placeholder' => 'Seleccione una fecha ...'], 
                    'pluginOptions' => [
                        'format' => 'yyyy-m-d',
                        'todayHighlight' => true,
                        'required' => true]])
                ?>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-sm-offset-3 text-center"> <div class="checkbox checkbox-success">
                    <?= $form->field($model, 'aplica_suspension')->checkBox([
                        'class' => 'bs_switch',
                        'style' => 'margin-bottom:5px;', // 'align:center' no es una propiedad CSS válida aquí
                        'id' => 'aplica_suspension',
                        'label' => 'Aplica suspensión al contrato',
                    ]) ?>
                    </div>
                </div>
            </div>
        </div>    
    </div>
    
<div class="panel-footer text-right">
        <a href="<?= Url::toRoute("proceso-disciplinario/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
        <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>        
    </div>
</div>   
<?php $form->end() ?>
