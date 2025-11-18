<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use kartik\select2\Select2;
//models
use app\models\Municipio;
use app\models\Departamento;
use app\models\TipoDocumento;
?>

<!--<h1>Nuevo proveedor</h1>-->
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
$departamento = ArrayHelper::map(app\models\Departamento::find()->all(), 'iddepartamento', 'departamento');
$municipio = ArrayHelper::map(app\models\Municipio::find()->all(), 'idmunicipio', 'municipio');
$tipodocumento = ArrayHelper::map(TipoDocumento::find()->all(), 'id_tipo_documento', 'descripcion');
?>
<div class="panel panel-success">
    <div class="panel-heading">
        AGENTES COMERCIALES
    </div>
    <div class="panel-body">
        <div class="row">
            <?= $form->field($model, 'id_tipo_documento')->dropDownList($tipodocumento, ['prompt' => 'Seleccione...', 'onchange' => 'mostrar2()', 'id' => 'id_tipo_documento']) ?>
            <?= $form->field($model, 'nit_cedula')->input('text', ['id' => 'cedulanit', 'onchange' => 'calcularDigitoVerificacion()']) ?>
            <?= Html::textInput('dv', $model->dv, ['id' => 'dv', 'aria-required' => true, 'aria-invalid' => 'false', 'maxlength' => 1, 'class' => 'form-control', 'placeholder' => 'dv', 'style' => 'width:50px', 'readonly' => true]) ?>       
        </div>														   
        <div class="row">
            <?= $form->field($model, 'primer_nombre')->input("text", ["maxlength" => 15]) ?>
            <?= $form->field($model, 'segundo_nombre')->input("text", ["maxlength" => 15]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'primer_apellido')->input("text", ["maxlength" => 15]) ?>
            <?= $form->field($model, 'segundo_apellido')->input("text", ["maxlength" => 15]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'direccion')->input("text", ["maxlength" => 40]) ?>
            <?= $form->field($model, 'email_agente')->input("text", ["maxlength" => 50]) ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'celular_agente')->input("text") ?>
            <?= $form->field($model, 'hacer_pedido')->dropdownList(['0' => 'SI', '1' => 'NO'], ['prompt' => 'Seleccione...']) ?>
        </div>
        <div class="row">
             <?= $form->field($model, 'iddepartamento')->widget(Select2::classname(), [
                    'data' => $departamento,
                    'options' => ['placeholder' => 'Seleccione...'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
            ?>
            <?= $form->field($model, 'idmunicipio')->widget(Select2::classname(), [
                    'data' => $municipio,
                    'options' => ['placeholder' => 'Seleccione...'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
            ?>
        </div>
        <?php if($sw == 1){?>
            <div class="row">
                <?= $form->field($model, 'estado')->dropdownList(['0' => 'SI', '1' => 'NO'], ['prompt' => 'Seleccione...']) ?>
            </div>
        <?php }?>
    </div>    
    <div class="panel-footer text-right">
        <a href="<?= Url::toRoute("agentes-comerciales/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
        <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>        
    </div>
</div>
<?php $form->end() ?>
