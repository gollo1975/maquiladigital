<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Cliente;
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
$this->title = 'Orden (Tercero)';
$this->params['breadcrumbs'][] = ['label' => 'Orden', 'url' => ['indextercero']];
$this->params['breadcrumbs'][] = $this->title;
$listar_provedor = ArrayHelper::map(Proveedor::find()->where(['=','genera_moda', 1])->orderBy('nombrecorto ASC')->all(), 'idproveedor', 'nombrecorto');
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
        Nuevo (Tercero)
    </div>
    <div class="panel-body">
        <div class="row">
            <?= $form->field($model, 'idproveedor')->widget(Select2::classname(), [
                'data' => $listar_provedor,
                'options' => ['prompt' => 'Seleccione un proveedor ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
           <?= $form->field($model, 'idcliente')->widget(Select2::classname(), [
                'data' => $clientes,
                'options' => ['placeholder' => 'Seleccione un cliente...'],
                'pluginOptions' => ['allowClear' => true],
                'pluginEvents' => [
                    "change" => 'function() { $.get( "' . Url::toRoute('orden-produccion/productos') . '", { id: $(this).val() } )
                            .done(function( data ) {
                                $( "#' . Html::getInputId($model, 'codigoproducto') . '" ).html( data );
                            });
                    }',
                ],
                ]); ?>

        </div>
        <div class="row">
             <?= $form->field($model, 'codigo_producto')->widget(Select2::classname(), [
            'data' => $codigos,
            'options' => ['placeholder' => 'Seleccione un producto'],
            'pluginOptions' => [
                'allowClear' => true ]]);
            ?>
            <?= $form->field($model, 'idtipo')->dropDownList($ordenproducciontipos, ['prompt' => 'Seleccione un tipo...']) ?>
          
        </div>
        <div class="row">
            <?= $form->field($model, 'vlr_minuto')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'cantidad_minutos')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="row">
             <?=
            $form->field($model, 'fecha_proceso')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            <?= $form->field($model, 'observacion')->textArea(['maxlength' => true]) ?>
        </div>

        <div class="panel-footer text-right">			
            <a href="<?= Url::toRoute("orden-produccion/indextercero") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>		
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

