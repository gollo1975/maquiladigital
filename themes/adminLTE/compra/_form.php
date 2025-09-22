<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Proveedor;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\models\Facturaventa */
/* @var $form yii\widgets\ActiveForm */
$plantas = ArrayHelper::map(\app\models\PlantaEmpresa::find()->all(), 'id_planta', 'nombre_planta')
?>

<?php $form = ActiveForm::begin([
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
        ]);?>

 <div class="panel panel-success">
    <div class="panel-heading">
        <h4>Información de Compras</h4>
    </div>
    <div class="panel-body">        
        <div class="row">        
             <?= $form->field($model, 'id_proveedor')->widget(Select2::classname(), [
            'data' => $proveedores,
            'options' => ['placeholder' => 'Seleccione ...'],
            'pluginOptions' => [
                'allowClear' => true ],
             ]); ?>
             <?= $form->field($model, 'id_compra_concepto')->widget(Select2::classname(), [
            'data' => $conceptos,
            'options' => ['placeholder' => 'Seleccione ....'],
            'pluginOptions' => [
                'allowClear' => true ],
             ]); ?>
        </div>
        <div class="row">
             <?= $form->field($model, 'id_tipo_compra')->widget(Select2::classname(), [
            'data' => $tipocompra,
            'options' => ['placeholder' => 'Tipo de compra ....'],
            'pluginOptions' => [
                'allowClear' => true ],
             ]); ?>
            <?= $form->field($model, 'id_planta')->dropdownList($plantas, ['prompt' => 'Seleccione...']) ?>			
        </div>  
        <div class="row">
            <?= $form->field($model, 'factura')->textInput(['maxlength' => true]) ?>  					
            <?= $form->field($model, 'subtotal')->textInput(['maxlength' => true]) ?>  					
        </div>        
        <div class="row">
            <?= $form->field($model,'fechainicio')->widget(DatePicker::className(),['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]]) ?>
      
            <?= $form->field($model,'fechavencimiento')->widget(DatePicker::className(),['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]]) ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'genera_documento_soporte')->dropdownList(['0' => 'NO', '1' => 'SI']) ?>			
            <?= $form->field($model, 'observacion')->textArea(['maxlength' => true]) ?>
        </div>
        <div class="panel-footer text-right">            
            <a href="<?= Url::toRoute("compra/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>		
        </div>
	</div>
</div>



<?php ActiveForm::end(); ?>

