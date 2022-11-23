<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Talla */

$this->title = 'Detalle novedad';
$this->params['breadcrumbs'][] = ['label' => 'Novedades', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_tipo_novedad;
?>
<div class="tipo-novedad-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_tipo_novedad], ['class' => 'btn btn-success btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->id_tipo_novedad], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Esta seguro de eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Talla
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr>
                    <th><?= Html::activeLabel($model, 'Código') ?>:</th>
                    <td><?= Html::encode($model->id_tipo_novedad) ?></td>                    
                    <th><?= Html::activeLabel($model, 'Descripción') ?>:</th>
                    <td><?= Html::encode($model->novedad) ?></td>
                    <th><?= Html::activeLabel($model, 'fecha_hora') ?>:</th>
                    <td><?= Html::encode($model->fecha_hora) ?></td>                    
                </tr>                                
            </table>
        </div>
    </div>    
</div>
