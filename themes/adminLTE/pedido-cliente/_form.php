<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Cliente;
use kartik\select2\Select2;
use kartik\date\DatePicker;
?>
<?php
$conCliente = ArrayHelper::map(Cliente::find()->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombrecorto');
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
        <div class="panel panel-success">
            <div class="panel-heading">
                Información del pedido
            </div>
            <div class="panel-body">
                 <div class="row">      
                        <?=  $form->field($model, 'fecha_pedido')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...'],
                           'pluginOptions' => [
                               'format' => 'yyyy-m-d',
                               'todayHighlight' => true]])
                       ?>
                       <?=  $form->field($model, 'fecha_entrega')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...'],
                           'pluginOptions' => [
                               'format' => 'yyyy-m-d',
                               'todayHighlight' => true]])
                       ?>
                 </div>   
                
                <div class="row">
                    <?= $form->field($model, 'idcliente')->widget(Select2::classname(), [
                    'data' => $conCliente,
                    'options' => ['placeholder' => 'Seleccione el empleado'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
                    ?>
            
                     <?= $form->field($model, 'observacion')->textarea(['maxlength' => true]) ?>
                </div>
                <div class="panel-footer text-right">			
                    <a href="<?= Url::toRoute("pedido-cliente/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
                </div>
            </div>
        </div>
<?php $form->end() ?>     
