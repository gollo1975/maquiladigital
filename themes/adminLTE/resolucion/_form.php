<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\date\DatePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Resolucion */
/* @var $form yii\widgets\ActiveForm */
?>



<?php
$documento = yii\helpers\ArrayHelper::map(app\models\DocumentoElectronico::find()->all(), 'id_documento', 'nombre_documento');
$form = ActiveForm::begin([
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
        <h4>Información Resolución</h4>
    </div>
    <div class="panel-body">
        <div class="row">            
            <?= $form->field($model, 'nroresolucion')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'vigencia')->textInput(['maxlength' => true]) ?>  					
        </div>
        <div class="row">
            <?=
            $form->field($model, 'fechacreacion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            <?=
            $form->field($model, 'fechavencimiento')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'inicio_rango')->textInput(['maxlength' => true]) ?> 
            <?= $form->field($model, 'final_rango')->textInput(['maxlength' => true]) ?> 
        </div>
        
        <div class="row">
            <?= $form->field($model, 'codigoactividad')->textInput(['maxlength' => true]) ?>  					
            <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>  					
        </div>
        
        <div class="row">
             <?= $form->field($model, 'id_documento')->dropdownList($documento, ['prompt' => 'Seleccione...']) ?>
            <?= $form->field($model, 'activo')->dropdownList(['0' => 'Activo', '1' => 'Inactivo'], ['prompt' => 'Seleccione...']) ?>
        </div>	
         <div class="row">
              <?= $form->field($model, 'consecutivo')->textInput(['maxlength' => true]) ?> 
              <?= $form->field($model, 'codigo_interfaz')->textInput(['maxlength' => true]) ?>  
        </div>	
        <div class="panel-footer text-right">                    
            <a href="<?= Url::toRoute("resolucion/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success",]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
