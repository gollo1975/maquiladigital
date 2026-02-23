<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Prendatipo */

$this->title = 'Detalle Prenda Tipo';
$this->params['breadcrumbs'][] = ['label' => 'Prenda Tipos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->idprendatipo;
?>
<div class="prendatipo-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->idprendatipo], ['class' => 'btn btn-primary btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->idprendatipo], [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
                'confirm' => 'Esta seguro de eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Prenda tipo
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr>
                    <th><?= Html::activeLabel($model, 'idprendatipo') ?>:</th>
                    <td><?= Html::encode($model->idprendatipo) ?></td>                    
                    <th><?= Html::activeLabel($model, 'prenda') ?>:</th>
                    <td><?= Html::encode($model->prenda) ?></td>
                    <th><?= Html::activeLabel($model, 'Talla') ?>:</th>
                    <td><?= Html::encode($model->talla->talla.'-'.$model->talla->sexo) ?></td>                    
                </tr>                                
            </table>
        </div>
    </div>    
</div>
