<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Horario */

$this->title = 'PULPOS DE ESTAMPACION';
$this->params['breadcrumbs'][] = ['label' => 'pulpos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_pulpo;
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
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_pulpo') ?>:</th>
                    <td><?= Html::encode($model->id_pulpo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'descripcion') ?>:</th>
                    <td><?= Html::encode($model->descripcion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cantidad_brazos') ?>:</th>
                    <td><?= Html::encode($model->cantidad_brazos) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_registro') ?>:</th>
                    <td><?= Html::encode($model->fecha_registro) ?></td>
                </tr>           
                
            </table>
        </div>
    </div>

    

</div>

