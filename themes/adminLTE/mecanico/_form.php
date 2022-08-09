<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Municipio;
use app\models\Departamento;
use app\models\TipoDocumento;
use yii\widgets\LinkPager;
use kartik\select2\Select2;
use kartik\date\DatePicker;

$this->title = 'Mecanicos';

?>

<!--<h1>Nuevo Cliente</h1>-->
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
$departamento = ArrayHelper::map(Departamento::find()->all(), 'iddepartamento', 'departamento');
$municipio = ArrayHelper::map(Municipio::find()->all(), 'idmunicipio', 'municipio');    
$tipodocumento = ArrayHelper::map(TipoDocumento::find()->all(), 'id_tipo_documento', 'descripcion');
?>
<div class="panel panel-success">
    <div class="panel-heading">
        Registro
    </div>
    <div class="panel-body">
        <div class="row">
                <?= $form->field($model, 'id_tipo_documento')->widget(Select2::classname(), [
                'data' => $tipodocumento,
                'options' => ['placeholder' => 'Tipo documento ....'],
                'pluginOptions' => [
                    'allowClear' => true ],
                 ]); ?>
                 <?= $form->field($model, 'documento')->input("text", ['maxlength' => '15']) ?>
        </div>														   
    
        <div class="row">
            <?= $form->field($model, 'nombres')->input("text") ?>
            <?= $form->field($model, 'apellidos')->input("text") ?>	
        </div>
        <div class="row">
            <?= $form->field($model, 'direccion_mecanico')->input("text") ?>
            <?= $form->field($model, 'celular')->input("text") ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'email_mecanico')->input("text") ?>
            <?= $form->field($model, 'estado')->dropDownList(['0'=> 'SI', '1'=> 'NO'], ['prompt' => 'Seleccione una opcion...']) ?>
        </div>
         <div class="row">
            <?php if($token == 0){?> 
                <?= $form->field($model, 'iddepartamento')->dropDownList($departamento, [ 'prompt' => 'Seleccione...', 'onchange' => ' $.get( "' . Url::toRoute('clientes/municipio') . '", { id: $(this).val() } ) .done(function( data ) {
                    $( "#' . Html::getInputId($model, 'idmunicipio', ['required', 'class' => 'select-2']) . '" ).html( data ); });']); ?>
                <?= $form->field($model, 'idmunicipio')->dropDownList(['prompt' => 'Seleccione...']) ?>
            <?php }else{?>
                 <?= $form->field($model, 'iddepartamento')->dropDownList($departamento, ['prompt' => 'Seleccione...', 'onchange' => ' $.get( "' . Url::toRoute('clientes/municipio') . '", { id: $(this).val() } ) .done(function( data ) {
            $( "#' . Html::getInputId($model, 'idmunicipio', ['required']) . '" ).html( data ); });']); ?>
                <?= $form->field($model, 'idmunicipio')->dropDownList($municipio, ['prompt' => 'Seleccione...']) ?>
            <?php }?> 
        </div>
        <div class="row">
            <div class="field-tblclientes-observaciones_cliente has-success">
                <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-10 form-group">{input}{error}</div>'])->textarea(['rows' => 3]) ?>
            </div>
        </div> 	
    </div>    
    <div class="panel-footer text-right">
        <a href="<?= Url::toRoute("mecanico/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
        <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>        
    </div>
</div>
<?php $form->end() ?>
