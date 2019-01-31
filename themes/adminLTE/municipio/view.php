<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Municipio */

$this->title = 'Detalle Municipio';
$this->params['breadcrumbs'][] = ['label' => 'Municipios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->idmunicipio;
?>
<div class="municipio-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->idmunicipio], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->idmunicipio], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->idmunicipio], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Esta seguro de eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Municipio
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr>
                    <th><?= Html::activeLabel($model, 'idmunicipio') ?>:</th>
                    <td><?= Html::encode($model->idmunicipio) ?></td>                    
                    <th><?= Html::activeLabel($model, 'codigomunicipio') ?>:</th>
                    <td><?= Html::encode($model->codigomunicipio) ?></td>
                    <th><?= Html::activeLabel($model, 'municipio') ?>:</th>
                    <td><?= Html::encode($model->municipio) ?></td>                    
                </tr>
                <tr>
                    <th><?= Html::activeLabel($model, 'iddepartamento') ?>:</th>
                    <td><?= Html::encode($model->iddepartamento) ?></td>                    
                    <th><?= Html::activeLabel($model, 'departamento') ?>:</th>
                    <td><?= Html::encode($model->departamento->departamento) ?></td>
                    <th><?= Html::activeLabel($model, 'activo') ?>:</th>
                    <td><?= Html::encode($model->estado) ?></td>                    
                </tr>                
            </table>
        </div>
    </div>

</div>
