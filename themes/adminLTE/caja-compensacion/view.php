<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\CajaCompensacion */

$this->title = 'Detalle Caja Compensacion';
$this->params['breadcrumbs'][] = ['label' => 'Cajas de Compensacion', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_caja_compensacion;
?>
<div class="caja-compensacion-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->id_caja_compensacion], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_caja_compensacion], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->id_caja_compensacion], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Esta seguro de eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Caja de Compensacion
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr>
                    <th><?= Html::activeLabel($model, 'id_caja_compensacion') ?>:</th>
                    <td><?= Html::encode($model->id_caja_compensacion) ?></td>
                    <th><?= Html::activeLabel($model, 'caja') ?>:</th>
                    <td><?= Html::encode($model->caja) ?></td>
                    <th><?= Html::activeLabel($model, 'telefono') ?>:</th>
                    <td><?= Html::encode($model->telefono) ?></td>
                    <th><?= Html::activeLabel($model, 'direccion') ?>:</th>
                    <td><?= Html::encode($model->direccion) ?></td>
                </tr>
                <tr>
                    <th><?= Html::activeLabel($model, 'codigo_caja') ?>:</th>
                    <td><?= Html::encode($model->codigo_caja) ?></td>
                    <th><?= Html::activeLabel($model, 'codigo_interfaz') ?>:</th>
                    <td><?= Html::encode($model->codigo_interfaz) ?></td>
                    <th><?= Html::activeLabel($model, 'idmunicipio') ?>:</th>
                    <td><?= Html::encode($model->municipios) ?></td>
                    <th><?= Html::activeLabel($model, 'activo') ?>:</th>
                    <td><?= Html::encode($model->activo) ?></td>
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