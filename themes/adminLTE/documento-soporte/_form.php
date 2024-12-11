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
   $formaPago = ArrayHelper::map(\app\models\FormaPago::find()->all(), 'id_forma_pago', 'concepto');
   $conProveedor = ArrayHelper::map(app\models\Proveedor::find()->orderBy('nombrecorto ASC')->all(), 'idproveedor', 'nombrecorto');
   
?>
<body>
<!--<h1>Editar Cliente</h1>-->

<div class="panel panel-success">
    <div class="panel-heading">
        Información Grupo Pago
    </div>
    <div class="panel-body">  
        <?php if ($sw == 1){?>        
            <div class="row">
                  <?= $form->field($model, 'idproveedor')->dropDownList($conProveedor,['prompt'=>'Seleccione...', 'onchange'=>' $.get( "'.Url::toRoute('documento-soporte/cargarcompras').'", { id: $(this).val() } ) .done(function( data ) {
                    $( "#'.Html::getInputId($model, 'id_compra',['required', 'class' => 'select-2']).'" ).html( data ); });']); ?>
                    <?= $form->field($model, 'id_compra')->dropDownList(['prompt' => 'Seleccione...']) ?>
            </div>    
            <div class="row">
                <?=$form->field($model, 'fecha_elaboracion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                    'value' => date('d-M-Y', strtotime('+2 days')),
                    'options' => ['placeholder' => 'Seleccione una fecha'],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true]])
                ?> 
                <?= $form->field($model, 'id_forma_pago')->dropDownList($formaPago, ['prompt' => 'Seleccione...']) ?>

            </div>

            <div class="row" col>
                <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>'])->textarea(['rows' => 2]) ?>
            </div>
        <?php }else{?>
            <div class="row">
                <?= $form->field($model, 'idproveedor')->widget(Select2::classname(), [
                              'data' => $conProveedor,
                              'options' => ['placeholder' => 'Seleccione el proveedor'],
                              'pluginOptions' => [
                                  'allowClear' => true ]]);
                ?>
                <?=$form->field($model, 'fecha_elaboracion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                        'value' => date('d-M-Y', strtotime('+2 days')),
                        'options' => ['placeholder' => 'Seleccione una fecha'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true]])
                ?> 
            </div> 
            <div class="row" col>
                <?= $form->field($model, 'id_forma_pago')->dropDownList($formaPago, ['prompt' => 'Seleccione...']) ?>
                <?= $form->field($model, 'documento_compra')->textInput(['maxlength' => true]) ?>
                  
            </div>
            <div class="row">
                <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>'])->textarea(['rows' => 2]) ?>
            </div>   
        <?php }?>

        <div class="panel-footer text-right">                
            <a href="<?= Url::toRoute("documento-soporte/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>