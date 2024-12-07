<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\TipoRecibo */

$this->title = 'Conceptos DS';
$this->params['breadcrumbs'][] = ['label' => 'Concepto documento', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_concepto;
?>
<div class="concepto-documento-soporte-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->id_concepto], ['class' => 'btn btn-primary btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->id_concepto], [
            'class' => 'btn btn-danger',
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
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_concepto') ?>:</th>
                    <td><?= Html::encode($model->id_concepto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'concepto') ?>:</th>
                    <td><?= Html::encode($model->concepto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo_interface') ?>:</th>
                    <td ><?= Html::encode($model->codigo_interface) ?></td>                    
                </tr>                
            </table>
        </div>
    </div>
</div>    