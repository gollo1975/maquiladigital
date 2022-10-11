<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\TipoProducto;
use kartik\select2\Select2;
use kartik\date\DatePicker;

$this->params['breadcrumbs'][] = ['label' => 'Costo producto', 'url' => ['index']];
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
$tipo_producto = ArrayHelper::map(TipoProducto::find()->where(['=','estado',1])->orderBy('concepto ASC')->all(), 'id_tipo_producto', 'concepto');
?>
        <div class="panel panel-success">
            <div class="panel-heading">
                Costo de producto
            </div>
            <div class="panel-body">
                <div class="row">
                    <?= $form->field($model, 'codigo_producto')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>
                  
                </div>
                <div class="row">
                    <?= $form->field($model, 'tiempo_confeccion')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'tiempo_terminacion')->textInput(['maxlength' => true]) ?>
                  
                </div>
                <div class="row">
                     <?= $form->field($model, 'id_tipo_producto')->widget(Select2::classname(), [
                    'data' => $tipo_producto,
                    'options' => ['placeholder' => 'Seleccione...'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
                    ?>
                     <?= $form->field($model, 'observacion')->textarea(['maxlength' => true]) ?>
               
                </div>
               
                <div class="panel panel-success">
                    <div class="panel-heading">
                        Impuestos:
                    </div>
                    <div class="panel-body">
                        <div class="checkbox checkbox-success" align ="right">
                                <?= $form->field($model, 'aplicar_iva')->checkBox(['label' => 'Aplicar iva','1' =>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplicar_iva']) ?>
                     </div>
                </div>   
                <div class="panel-footer text-right">			
                    <a href="<?= Url::toRoute("costo-producto/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
                </div>
            </div>
        </div>
<?php $form->end() ?>     
