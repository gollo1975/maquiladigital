<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Ordenproducciontipo */

$this->title = 'Detalle Tipo orden';
$this->params['breadcrumbs'][] = ['label' => 'Ordenes Producción Tipos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->idtipo;
?>
<div class="ordenproducciontipo-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->idtipo], ['class' => 'btn btn-primary btn-sm']) ?>
		<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->idtipo], ['class' => 'btn btn-success btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->idtipo], [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
                'confirm' => 'Esta seguro de eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
<div class="panel panel-success">
        <div class="panel-heading">
            Orden de Producción tipo
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idtipo') ?>:</th>
                    <td><?= Html::encode($model->idtipo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tipo') ?>:</th>
                    <td><?= Html::encode($model->tipo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'remision') ?>:</th>
                    <td><?= Html::encode($model->rremision) ?></td>
                    
                </tr>    
                 <tr style="font-size: 85%;">
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'activo') ?>:</th>
                    <td><?= Html::encode($model->estado) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Ver_registro') ?>:</th>
                    <td><?= Html::encode($model->verregistro) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'permite_insumos') ?>:</th>
                    <td><?= Html::encode($model->verInsumos) ?></td>
                </tr>                
            </table>
        </div>
    </div>

</div>
