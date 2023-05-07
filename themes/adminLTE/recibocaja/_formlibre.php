<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Cliente;
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

$this->title = 'Nuevo recibo(Libre)';
$this->params['breadcrumbs'][] = ['label' => 'Recibos de cajas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;?>


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
        Información Recibo Caja
    </div>
    <div class="panel-body">
        <div class="row">            
            <?= $form->field($model, 'idcliente')->widget(Select2::classname(), [
            'data' => $clientes,
            'options' => ['placeholder' => 'Seleccione un cliente'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>
        </div>
        <div class="row">                        
            <?= $form->field($model, 'idtiporecibo')->dropDownList($tiporecibos, ['prompt' => 'Seleccione un tipo...']) ?>
        </div>                                        
        <div class="row">            
            <?= $form->field($model, 'idbanco')->dropDownList($bancos, ['prompt' => 'Seleccione un banco...']) ?>
        </div>
        <div class="row">            
            <?=  $form->field($model, 'fechapago')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'idmunicipio')->widget(Select2::classname(), [
                'data' => $municipios,
                'options' => ['prompt' => 'Seleccione un municipio ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="row">
            <?= $form->field($model, 'observacion')->textarea() ?>
        </div>
        <div class="panel-footer text-right">            
            <a href="<?= Url::toRoute("recibocaja/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success btn-sm",]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>


