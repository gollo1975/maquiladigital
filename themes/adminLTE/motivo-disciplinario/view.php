<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MotivoDisciplinario */

$this->title = 'MOTIVO DISCIPLINARIO';
$this->params['breadcrumbs'][] = ['label' => 'Motivo disciplinario', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_motivo;
?>
<div class="motivo-disciplinario-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->id_motivo], ['class' => 'btn btn-primary btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_motivo], ['class' => 'btn btn-success btn-sm']) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle del motivo
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
               <tr style ='font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_motivo') ?>:</th>
                    <td><?= Html::encode($model->id_motivo) ?></td>                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'concepto') ?>:</th>
                    <td><?= Html::encode($model->concepto) ?></td>
              </tr>
            </table>
        </div>
    </div>
</div>    