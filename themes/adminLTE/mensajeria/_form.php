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

$form = ActiveForm::begin([
            "method" => "post",
            'id' => 'formulario',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-3 control-label'],
                'options' => []
            ],
        ]);
?>

<?php
$Conproveedor = ArrayHelper::map(app\models\Proveedor::find()->orderBy('nombrecorto ASC')->all(), 'idproveedor', 'nombrecorto');
$Conprecio = ArrayHelper::map(app\models\PrecioMensajeria::find()->orderBy('concepto ASC')->all(), 'id_precio', 'concepto');
?>
<div class="panel panel-success">
    <div class="panel-heading">
        Información de la mensajeria
    </div>
    <div class="panel-body">
      
        <div class="row">
              <?= $form->field($model, 'idproveedor')->widget(Select2::classname(), [
                    'data' => $Conproveedor,
                    'options' => ['placeholder' => 'Seleccione el proveedor'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
                    ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'fecha_proceso')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                    'value' => date('d-M-Y', strtotime('+2 days')),
                    'options' => ['placeholder' => 'Seleccione una fecha ...'],
                    'pluginOptions' => [
                        'format' => 'yyyy-m-d',
                        'todayHighlight' => true]])
                ?>
          
        </div>	
        <div class="row">
           <?= $form->field($model, 'id_precio')->dropdownList($Conprecio, ['prompt' => 'Seleccione...']) ?>
        </div>    
  
        <div class="row">
            <div class="field-tblproveedor-observaciones_proveedor has-success">
                <?= $form->field($model, 'nota', ['template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>'])->textarea(['rows' => 2]) ?>
            </div>
        </div> 	
        <div class="row">
             <?= $form->field($model, 'valor_precio')->textInput(['maxlength' => true, 'readonly' => true]) ?>
        </div>    
    </div>    
    <div class="panel-footer text-right">
        <a href="<?= Url::toRoute("mensajeria/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
        <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success",]) ?>        
    </div>
</div>
<?php $form->end() ?>
