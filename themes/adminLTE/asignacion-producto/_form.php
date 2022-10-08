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
/* @var $model app\models\Ordenproduccion */
/* @var $form yii\widgets\ActiveForm */

$proveedor = ArrayHelper::map(Proveedor::find()->where(['=','genera_moda', 1])->orderBy ('nombrecorto ASC')->all(), 'idproveedor', 'nombrecorto');
$tipo = ArrayHelper::map(\app\models\Ordenproducciontipo::find()->orderBy ('idtipo ASC')->all(), 'idtipo', 'tipo');
?>

<?php
$form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal condensed ', 'role' => 'form'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
                'labelOptions' => ['class' => 'col-sm-3 control-label'],
                'options' => []
            ],
        ]);
?>
<?php ?>

<div class="panel panel-success">
    <div class="panel-heading">
        Registros
    </div>
    <div class="panel-body">
        <br>
         <div class="row">
            <?= $form->field($model, 'idproveedor')->widget(Select2::classname(), [
                'data' => $proveedor,
                'options' => ['prompt' => 'Seleccione un cliente ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>  
        <div class="row">
            <?=
            $form->field($model, 'fecha_asignacion')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
        </div>
         <div class="row">
            <?= $form->field($model, 'idtipo')->widget(Select2::classname(), [
                'data' => $tipo,
                'options' => ['prompt' => 'Seleccione un cliente ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>  
           <div class="row">
            <?= $form->field($model, 'observacion')->textArea(['maxlength' => true]) ?>
        </div>
        
     
     
        <div class="panel-footer text-right">			
             <a href="<?= Url::toRoute("asignacion-producto/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

