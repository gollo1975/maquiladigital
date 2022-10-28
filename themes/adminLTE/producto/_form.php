<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use app\models\Cliente;
use app\models\Prendatipo;

/* @var $this yii\web\View */
/* @var $model app\models\Producto */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
]); ?>

<div class="panel panel-success">
    <div class="panel-heading">
        Información Prductos
    </div>
    <div class="panel-body">        
        <div class="row">
            <?= $form->field($model, 'idcliente')->widget(Select2::classname(), [
                'data' => $clientes,
                'options' => ['prompt' => 'Seleccione un cliente ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?>
        </div>        
        <div class="row">
            <?= $form->field($model, 'observacion')->textarea(['maxlength' => true]) ?>
        </div>
        <div class="panel-footer text-right">            
            <a href="<?= Url::toRoute("producto/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
