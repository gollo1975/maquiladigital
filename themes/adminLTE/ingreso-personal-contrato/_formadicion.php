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

$this->title = 'Nuevas Bonificaciones';
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
?>
        <div class="panel panel-success">
            <div class="panel-heading">
                Información: Bonificaciones del empleado
            </div>
            <div class="panel-body">
                <div class="row">

                    <?= $form->field($model, 'id_empleado')->widget(Select2::classname(), [
                        'data' => $empleado,
                        'options' => ['placeholder' => 'Seleccione el empleado'],
                        'pluginOptions' => [
                            'allowClear' => true ]]);
                    ?>
                   <?= $form->field($model, 'operacion')->textInput(['maxlength' => true]) ?>
                </div>        
                <div class="row">
                        <?= $form->field($model, 'cantidad')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'valor_unitario')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="row">
                        <?= $form->field($model, 'total_dias')->textInput(['maxlength' => true]) ?>
                        
                </div>
                
                <div class="panel-footer text-right">			
                    <a href="<?= Url::toRoute(["ingreso-personal-contrato/view", 'id'=>$id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
                </div>
            </div>
        </div>
<?php $form->end() ?>     


