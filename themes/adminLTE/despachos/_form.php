<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Cliente;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */
/* @var $form yii\widgets\ActiveForm */
$conProveedor = ArrayHelper::map(app\models\Proveedor::find()->orderBy ('nombrecorto ASC')->all(), 'idproveedor', 'nombrecorto');
$conMunicipio = ArrayHelper::map(app\models\Municipio::find()->orderBy ('municipio ASC')->all(), 'idmunicipio', 'municipio');
$conTipoEntrada = ArrayHelper::map(app\models\TipoEntrada::find()->where(['=','genera_cobro',  1])->orderBy ('id_entrada_tipo ASC')->all(), 'id_entrada_tipo', 'concepto');
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
<?php ?>
<div class="panel panel-success">
    <div class="panel-heading">
        Registro
    </div>
    <div class="panel-body">
        <div class="row">
         <?= $form->field($model, 'id_salida')->widget(Select2::classname(), [
                'data' => $conSalida,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $form->field($model, 'idproveedor')->widget(Select2::classname(), [
                'data' => $conProveedor,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'id_entrada_tipo')->widget(Select2::classname(), [
                'data' => $conTipoEntrada,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $form->field($model, 'ciudad_origen')->widget(Select2::classname(), [
                'data' => $conMunicipio,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'ciudad_destino')->widget(Select2::classname(), [
                'data' => $conMunicipio,
                'options' => ['prompt' => 'Seleccione...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?= $form->field($model, 'total_tulas')->textInput(['maxlength' => true]) ?>
            
        </div>
        
        <div class="row">
            <?= $form->field($model, 'valor_flete')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'fecha_despacho')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
        </div>
         <div class="row">
            <?= $form->field($model, 'observacion', ['template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>'])->textarea(['rows' => 2]) ?>
        </div>
         
         <div class="panel-footer text-right">				
            <a href="<?= Url::toRoute("despachos/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm"]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

