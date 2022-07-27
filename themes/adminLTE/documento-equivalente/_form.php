<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Municipio;
use app\models\Departamento;

/* @var $this yii\web\View */
/* @var $model app\models\DocumentoEquivalente */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-3 control-label'],
                'options' => []
            ],
        ]);
?>
<?php
$departamento = ArrayHelper::map(Departamento::find()->all(), 'iddepartamento', 'departamento');
$municipio = ArrayHelper::map(Municipio::find()->all(), 'idmunicipio', 'municipio');
?>
<div class="panel panel-success">
    <div class="panel-heading">
        Información Documento Equivalente
    </div>
    <div class="panel-body">        														   		
        <div class="row">
            <?= $form->field($model, 'identificacion')->textInput(['maxlength' => true]) ?>    
        </div>
        <div class="row">
            <?= $form->field($model, 'nombre_completo')->textInput(['maxlength' => true]) ?>  					
        </div>
        <div class="row">
            <?= $form->field($model, 'iddepartamento')->dropDownList($departamento, [ 'prompt' => 'Seleccione...', 'onchange' => ' $.get( "' . Url::toRoute('empresa/municipio') . '", { id: $(this).val() } ) .done(function( data ) {
                $( "#' . Html::getInputId($model, 'idmunicipio', ['required', 'class' => 'select-2']) . '" ).html( data ); });']); ?>            
        </div>
        <div class="row">
            <?= $form->field($model, 'idmunicipio')->dropDownList($municipio, ['prompt' => 'Seleccione...']) ?>
        </div>
        <div class="row">
            <?= $form->field($model,'fecha')->widget(DatePicker::className(),['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]]) ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true])?>
        </div>
        <div class="row">
            <?= $form->field($model, 'valor')->textInput(['maxlength' => true]) ?>
        </div>        
        <div class="row">
            <?= $form->field($model, 'porcentaje')->textInput(['maxlength' => true]) ?>
        </div>  
         <div class="row" style="text-align: right">  
             <?= $form->field($model, 'generar_comprobante')->checkBox(['label' => 'Generar comprobante',''=>'small', 'class'=>'bs_switch','style'=>'margin-bottom:5px;', 'id'=>'generar_comprobante']) ?> 
       </div>    
        <div class="panel-footer text-right">			
            <a href="<?= Url::toRoute("documento-equivalente/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>


