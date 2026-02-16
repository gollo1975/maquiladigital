<?php


use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\PeriodoPago;
use app\models\Departamento;
use app\models\Municipio;
use app\models\Sucursal;
use kartik\date\DatePicker;
use kartik\select2\Select2;

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
?>
<?php
   $periodo = ArrayHelper::map(PeriodoPago::find()->all(), 'id_periodo_pago', 'nombre_periodo');
   $departamento = ArrayHelper::map(Departamento::find()->all(), 'iddepartamento', 'departamento');
   $municipio = ArrayHelper::map(Municipio::find()->all(), 'idmunicipio', 'municipio');
   $sucursalSS = ArrayHelper::map(Sucursal::find()->all(), 'id_sucursal', 'sucursal');
?>
<body>
<!--<h1>Editar Cliente</h1>-->

<div class="panel panel-success">
    <div class="panel-heading">
        Información Grupo Pago
    </div>
    <div class="panel-body">        														   		
        <div class="row">
            <?= $form->field($model, 'grupo_pago')->textInput(['maxlength' => true]) ?>    
            <?= $form->field($model, 'id_periodo_pago')->dropDownList($periodo, ['prompt' => 'Seleccione periodo']) ?>
        </div>
        
        <div class="row">
            <?= $form->field($model, 'iddepartamento')->dropDownList($departamento, [ 'prompt' => 'Seleccione una opcion...', 'onchange' => ' $.get( "' . Url::toRoute('clientes/municipio') . '", { id: $(this).val() } ) .done(function( data ) {
            $( "#' . Html::getInputId($model, 'idmunicipio', ['required', 'class' => 'select-2']) . '" ).html( data ); });']); ?>
            <?= $form->field($model, 'idmunicipio')->dropDownList($municipio, ['prompt' => 'Seleccione municipio']) ?>
        </div>
        <div class="row">
             <?= $form->field($model, 'id_sucursal')->dropDownList($sucursalSS, ['prompt' => 'Seleccione sucursal']) ?>
             <?= $form->field($model, 'limite_devengado')->textInput(['maxlength' => true]) ?> 
        </div>
        <div class="row">
            <?=
            $form->field($model, 'ultimo_pago_nomina')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true]])
            ?>       
            
             <?=
            $form->field($model, 'ultimo_pago_prima')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true]])
            ?>       
        
        </div>
        
        <div class="row">
              <?=
            $form->field($model, 'ultimo_pago_cesantia')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha '],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true]])
           ?>       
            <?= $form->field($model, 'dias_pago')->textInput(['maxlength' => true]) ?>
        </div>		
        <div class="row" col>
             <?= $form->field($model, 'estado')->dropdownList(['1' => 'SI', '0' => 'NO']) ?>
            <?= $form->field($model, 'contrato_especial')->dropdownList(['0' => 'NO', '1' => 'SI']) ?>
        </div>
        <div class="row" col>
            <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>'])->textarea(['rows' => 2]) ?>
        </div>
        <div class="panel-footer text-right">                
            <a href="<?= Url::toRoute("grupo-pago/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>