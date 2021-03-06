<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\TipoContrato */

$this->title = 'Detalle contrato';
$this->params['breadcrumbs'][] = ['label' => 'Tipo de contrato', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_tipo_contrato;
?>
<div class="tipo-contrato-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'] , ['class' => 'btn btn-primary btn-sm']) ?>
		<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_tipo_contrato], ['class' => 'btn btn-success btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->id_tipo_contrato], [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
                'confirm' => 'Esta seguro de eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Tipo de recibo
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr>
                    <th><?= Html::activeLabel($model, 'id_tipo_contrato') ?>:</th>
                    <td><?= Html::encode($model->id_tipo_contrato) ?></td>
                    <th><?= Html::activeLabel($model, 'contrato') ?></th>
                    <td><?= Html::encode($model->contrato) ?></td>
                    <th><?= Html::activeLabel($model, 'estado') ?></th>
                    <td><?= Html::encode($model->activo) ?></td>   
                     <th><?= Html::activeLabel($model, 'prorroga') ?></th>
                    <td><?= Html::encode($model->prorrogaContrato) ?></td>       
                    <th><?= Html::activeLabel($model, 'nro_prorrogas') ?></th>
                    <td><?= Html::encode($model->nro_prorrogas) ?></td>       
                </tr>                
            </table>
        </div>
    </div>
    <?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
    ]); ?>        
    <?php ActiveForm::end(); ?>
</div>