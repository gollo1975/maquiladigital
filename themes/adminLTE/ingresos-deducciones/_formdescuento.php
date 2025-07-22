<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\PagoAdicionalPermanente;
use app\models\ConceptoSalarios;
use app\models\Empleado;
use kartik\select2\Select2;
use kartik\date\DatePicker;

$this->title = 'Descuentos';
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
$empleado = ArrayHelper::map(Empleado::find()->where(['=','contrato',1])->orderBy('nombrecorto ASC')->all(), 'id_empleado', 'nombrecorto');
$conceptosalario = ArrayHelper::map(ConceptoSalarios::find()->where(['compone_salario'=> 1])
                                                            ->andWhere(['=','tipo_adicion', 2])
                                                            ->orderBy('nombre_concepto ASC')->all(), 'codigo_salario', 'nombre_concepto');
?>
        <div class="panel panel-success">
            <div class="panel-heading">
                Información: Descuentos a empleados
            </div>
            <div class="panel-body">
                <div class="row">

                     <?= $form->field($model, 'id_empleado')->widget(Select2::classname(), [
                    'data' => $empleado,
                    'options' => ['placeholder' => 'Seleccione el empleado'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
                    ?>
                       <?= $form->field($model, 'codigo_salario')->widget(Select2::classname(), [
                    'data' => $conceptosalario,
                    'options' => ['placeholder' => 'Seleccione el concepto'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
                    ?>
                </div>        
                <div class="row">
                         <?= $form->field($model, 'valor_pagado')->textInput(['maxlength' => true]) ?>
                         <?= $form->field($model, 'observacion')->textarea(['maxlength' => true]) ?>
                </div>
                <div class="panel-footer text-right">			
                    <a href="<?= Url::toRoute(["ingresos-deducciones/view" , 'id'=>$id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
                </div>
            </div>
        </div>
<?php $form->end() ?>     


