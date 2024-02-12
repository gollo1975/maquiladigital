<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\time\TimePicker;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;
?>

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
?>
<div class="panel panel-success">
    <div class="panel-heading">
        Materia prima
    </div>
    
    <div class="panel-body">
        <div class="row">
            <?= $form->field($model, 'idproveedor')->widget(Select2::classname(), [
                       'data' => $proveedores,
                       'options' => ['prompt' => 'Seleccione...','required' => true],
                       'pluginOptions' => [
                           'allowClear' => true
                       ],
                   ]); ?>     
             <?= $form->field($model, 'numero_soporte')->textInput(['maxlength' => true, 'size' => '15']) ?>
        </div>    
        <div class="row">
            <?=  $form->field($model, 'fecha_proceso')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                           'value' => date('Y-m-d', strtotime('+2 days')),
                           'options' => ['placeholder' => 'Seleccione una fecha ...', 'required' => true],
                           'pluginOptions' => [
                               'format' => 'yyyy-m-d',
                               'todayHighlight' => true]])
            ?>
             <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>'])->textarea(['rows' => 2]) ?>
        </div>
 
        <div class="panel-footer text-right">			
            <a href="<?= Url::toRoute("entrada-materia-prima/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>
<?php $form->end() ?>     
