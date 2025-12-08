<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title = 'Configuracion de inventarios';
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
                'template' => '{label}<div class="col-sm-3 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-2 control-label'],
                'options' => []
            ],
            ]);
    ?>
       
    <div class="panel panel-success">
        <div class="panel-heading">
            Configuracion del modulo
        </div>
        <div class="panel-body">
            <div class="checkbox checkbox-success" align ="left">
                    <?= $form->field($modelo, 'aplica_inventario_talla_color')->checkBox(['label' => 'Aplica inventario talla y color:','1' =>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplica_inventario_talla_color']) ?>
                    <?= $form->field($modelo, 'aplica_inventario_tallas')->checkBox(['label' => 'Aplica inventario talla:',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplica_inventario_tallas']) ?>
                    <?= $form->field($modelo, 'aplica_solo_inventario')->checkBox(['label' => 'Aplica solo inventario:',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplica_solo_inventario']) ?>
                    <?= $form->field($modelo, 'aplica_iva_incluido')->checkBox(['label' => 'Aplica iva incluido:',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplica_iva_incluido']) ?>
            </div>
            <div class="checkbox checkbox-success" align ="left">
                    <?= $form->field($modelo, 'aplica_modulo_inventario')->checkBox(['label' => 'Aplica modulo de inventario:','1' =>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'aplica_modulo_inventario']) ?>
            </div>
        </div>
    </div>
    <div class="panel panel-success">
        <div class="panel-heading">
          Otras configuraciones
        </div>
        <div class="panel-body">
          
        </div>
    </div>   
    <div class="panel-footer text-right">			
        <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
    </div>
    <?php $form->end() ?> 
</div>


