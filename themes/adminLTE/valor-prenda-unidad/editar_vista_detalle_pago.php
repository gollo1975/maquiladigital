<?php
//modelos
//clases
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Recibocaja */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Editar detalle';
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

<div class="panel panel-success">
    <div class="panel-heading">
        Detalle del registro
    </div>
    <div class="panel-body">        
        
        
        <div class="row" col>
            <?= $form->field($model, 'devengado')->textInput(['maxlength' => true]) ?>
           <?= $form->field($model, 'deduccion')->textInput(['maxlength' => true]) ?>
        </div>
      
        <div class="panel-footer text-right">                        
            <a href="<?= Url::toRoute(["valor-prenda-unidad/vistadetallepago", 'autorizado' => $autorizado, 'id_pago' => $id_pago, 'fecha_inicio' => $fecha_inicio,'fecha_corte' => $fecha_corte, 'bodega' => $bodega]) ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>