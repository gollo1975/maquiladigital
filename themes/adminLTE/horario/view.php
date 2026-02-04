<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Horario */

$this->title = 'Detalle Horario';
$this->params['breadcrumbs'][] = ['label' => 'Horarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_horario;
?>
<div class="horario-view">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
		<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_horario], ['class' => 'btn btn-success btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['delete', 'id' => $model->id_horario], [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
                'confirm' => 'Esta seguro de eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Horario
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_horario') ?>:</th>
                    <td><?= Html::encode($model->id_horario) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'horario') ?>:</th>
                    <td><?= Html::encode($model->horario) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'desde') ?>:</th>
                    <td><?= Html::encode($model->desde) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'hasta') ?>:</th>
                    <td><?= Html::encode($model->hasta) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_horas') ?>:</th>
                    <td><?= Html::encode($model->total_horas) ?></td>
                </tr>           
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Minutos_desayuno') ?>:</th>
                    <td><?= Html::encode($model->tiempo_desayuno) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Minutos_almuerzo') ?>:</th>
                    <td><?= Html::encode($model->tiempo_almuerzo) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Abreviatura') ?>:</th>
                    <td ><?= Html::encode($model->abreviatura) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'aplica_tiempo_desuso') ?>:</th>
                    <td ><?= Html::encode($model->aplicaTiempoDesuso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'minutos_desuso') ?>:</th>
                    <td ><?= Html::encode($model->minutos_desuso) ?></td>
                </tr>    
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'aplica_sam_maquina') ?>:</th>
                    <td><?= Html::encode($model->aplicaTiempoMaquina) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'minutos_sam_maquina') ?>:</th>
                    <td><?= Html::encode($model->minutos_sam_maquina) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_evento_maquinas') ?>:</th>
                    <td ><?= Html::encode($model->total_evento_maquinas) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'aplica_sam_salud_ocupacional') ?>:</th>
                    <td ><?= Html::encode($model->aplicaTiempoSalud) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'minutos_sam_salud') ?>:</th>
                    <td ><?= Html::encode($model->minutos_sam_salud) ?></td>
                </tr>   
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_evento_salud') ?>:</th>
                    <td><?= Html::encode($model->total_evento_salud) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'aplica_tiempo_adicional') ?>:</th>
                    <td ><?= Html::encode($model->aplicaTiempoAdicional) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_porcentaje_autorizado') ?>:</th>
                    <td><?= Html::encode($model->total_porcentaje_autorizado) ?>%</td>
                     <th style='background-color:#F0F3EF;'></th>
                    <td></td>
                      <th style='background-color:#F0F3EF;'></th>
                    <td></td>
                </tr>    
                
            </table>
        </div>
    </div>

    <!--<?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_horario',
            'horario',
            //'porcentaje',
            //'cuenta',
        ],
    ]) ?>-->

</div>
