<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Color */

$this->title = 'Detalle';
$this->params['breadcrumbs'][] = ['label' => 'Bodegas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_planta;
?>
<div class="planta-empresa-view">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
		<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_planta], ['class' => 'btn btn-success btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->id_planta], [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
                'confirm' => 'Esta seguro de eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Color
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Id') ?>:</th>
                    <td><?= Html::encode($model->id_planta) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Nombre') ?>:</th>
                    <td><?= Html::encode($model->nombre_planta) ?></td>                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Dirección') ?>:</th>
                    <td><?= Html::encode($model->direccion_planta) ?></td>
                </tr>                                                                
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Teléfono') ?>:</th>
                    <td><?= Html::encode($model->telefono_planta) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Celular') ?>:</th>
                    <td><?= Html::encode($model->celular_planta) ?></td>                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_registro') ?>:</th>
                    <td><?= Html::encode($model->fecha_registro) ?></td>
                </tr>       
            </table>
        </div>
    </div>
    <!--<?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_planta',
            'nombre_planta',            
        ],
    ]) ?>-->

</div>
