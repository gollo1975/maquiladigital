<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Horario */

$this->title = 'Mensajeria';
$this->params['breadcrumbs'][] = ['label' => 'Mensajeria', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_codigo;
?>
<div class="horario-view">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle del registro
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_codigo') ?>:</th>
                    <td><?= Html::encode($model->id_codigo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idproveedor') ?>:</th>
                    <td><?= Html::encode($model->proveedor->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'valor_precio') ?>:</th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->valor_precio,0)) ?></td>
                </tr>           
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_proceso') ?>:</th>
                    <td><?= Html::encode($model->fecha_proceso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_precio') ?>:</th>
                    <td><?= Html::encode($model->precio->concepto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_registro') ?>:</th>
                    <td colspan="5"><?= Html::encode($model->fecha_registro) ?></td>
                </tr>   
                 <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'nota') ?>:</th>
                    <td colspan="8"><?= Html::encode($model->nota) ?></td>
                   
                </tr>     
            </table>
        </div>
    </div>
</div>    