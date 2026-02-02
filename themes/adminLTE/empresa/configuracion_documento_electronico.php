<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title = 'Configuracion documentos electronicos';
$this->params['breadcrumbs'][] = ['label' => 'Parametros', 'url' => ['parametros']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="matricula-empresa-vista">
    <?php
    $form = ActiveForm::begin([
                "method" => "post",
                'id' => 'formulario',
                'enableClientValidation' => false,
                'enableAjaxValidation' => false,
                'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
                'fieldConfig' => [
                'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-1 control-label'],
                'options' => []
            ],
            ]);
    ?>
       
    <div class="panel panel-success">
        <div class="panel-heading">
            Configuracion de documentos
        </div>
        <div class="panel-body">
            <div class="checkbox checkbox-success" align ="left">
                    <?= $form->field($modelo, 'aplica_factura_electronica')->checkBox(['label' => 'Aplica factura electronica:','1' =>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplica_factura_electronica']) ?>
                    <?= $form->field($modelo, 'aplica_documento_soporte')->checkBox(['label' => 'Aplica documento soporte:',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplica_documento_soporte']) ?>
                    <?= $form->field($modelo, 'aplica_nomina_electronica')->checkBox(['label' => 'Aplica nomina electronica:',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplica_nomina_electronica']) ?>
                    
            </div>
          
        </div>
    </div>
    <div class="panel panel-success">
        <div class="panel-heading">
          Otras configuraciones
        </div>
        <div class="panel-body">
            <div class="row">
                <?= $form->field($modelo, 'llave_api_token')->textInput(['maxlength' => true]) ?>  
                <?= $form->field($modelo, 'llave_uuid')->textInput(['maxlength' => true]) ?>   
            </div>
        </div>
    </div>   
    <div class="panel-footer text-right">			
        <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
    </div>
    <?php $form->end() ?> 
</div>


