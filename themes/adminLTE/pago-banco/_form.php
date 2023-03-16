<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Banco;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\time\TimePicker;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */
/* @var $form yii\widgets\ActiveForm */
$banco = ArrayHelper::map(Banco::find()->orderBy('idbanco ASC')->all(),'idbanco','entidad');
$tipo_nomina = ArrayHelper::map(\app\models\TipoNomina::find()->orderBy('id_tipo_nomina ASC')->all(),'id_tipo_nomina','tipo_pago');
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
        ]);
?>
<div class="panel panel-success">
    <div class="panel-heading">
        Detalle del registro
    </div>
    <div class="panel-body">
         <div class="row">
            <?= $form->field($model, 'id_banco')->widget(Select2::classname(), [
                    'data' => $banco,
                    'options' => ['placeholder' => 'Seleccione el empleado'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
                    ?>
           <?= $form->field($model, 'tipo_pago')->dropDownList(['220'=> 'PAGO PROVEEDORES', '225'=> 'PAGO NOMINA'], ['prompt' => 'Seleccione una opcion...']) ?>
        </div> 
        <div class="row">
            <?= $form->field($model, 'fecha_creacion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
             <?= $form->field($model, 'fecha_aplicacion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
        </div>
        <div class="row">
           <?= $form->field($model, 'aplicacion')->dropDownList(['I'=> 'INMEDIATO', 'M'=> 'MEDIO DIA','N' => 'NOCHE'], ['prompt' => 'Seleccione una opcion...']) ?> 
           <?= $form->field($model, 'secuencia')->dropDownList(['A1'=> 'A', 'B1'=> 'B','C1' => 'C', 'D1'=> 'D', 'E1'=> 'E','F1' => 'F'], ['prompt' => 'Seleccione una opcion...']) ?>
         </div> 
        <div class="row">
            <?= $form->field($model, 'id_tipo_nomina')->widget(Select2::classname(), [
                    'data' => $tipo_nomina,
                    'options' => ['placeholder' => 'Seleccione el pago'],
                    'pluginOptions' => [
                        'allowClear' => true ]]);
                    ?>
            <?= $form->field($model, 'descripcion')->textArea(['maxlength' => true]) ?>
        </div>
        <div class="panel-footer text-right">			
             <a href="<?= Url::toRoute("pago-banco/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

