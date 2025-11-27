<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Horario */

$this->title = 'MARCAS';
$this->params['breadcrumbs'][] = ['label' => 'marca', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_marca;
?>
<div class="horario-view">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
       
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Horario
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_marca') ?>:</th>
                    <td><?= Html::encode($model->id_marca) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'marca') ?>:</th>
                    <td><?= Html::encode($model->marca) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'estado') ?>:</th>
                    <td><?= Html::encode($model->estadoMarca) ?></td>
                </tr>           
                
            </table>
        </div>
    </div>

    

</div>
