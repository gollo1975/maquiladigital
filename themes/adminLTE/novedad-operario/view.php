<?php

use yii\helpers\Html;
use yii\widgets\DetailView;


/* @var $this yii\web\View */
/* @var $model app\models\Empleado */

$this->title = 'Detalle novedad';
$this->params['breadcrumbs'][] = ['label' => 'Novedades', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_novedad;
$view = 'novedad-operario';
?>
<div class="novedad-operario-view">

    <!--<?= Html::encode($this->title) ?>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
        <?php if ($model->autorizado == 0) { ?>
	    <?= Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizar', 'id' => $model->id_novedad], ['class' => 'btn btn-default btn-sm']) ?>
        <?php }else{
            if($model->cerrado == 0){?>
                <?= Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizar', 'id' => $model->id_novedad], ['class' => 'btn btn-default btn-sm']) ?>
                <?= Html::a('<span class="glyphicon glyphicon-eye-close"></span> Cerrar novedad', ['cerrarproceso', 'id' => $model->id_novedad],['class' => 'btn btn-info btn-sm',
                          'data' => ['confirm' => 'Esta seguro de cerrar la novedad del operario.', 'method' => 'post']]) ?>
            <?php }else{ ?>
               <?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimirnovedad', 'id' => $model->id_novedad], ['class' => 'btn btn-default btn-sm'])?>            
               <?= Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 18, 'codigo' => $model->id_novedad,'view' => $view], ['class' => 'btn btn-default btn-sm']) ?>
            <?php }
        }?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_novedad') ?></th>
                    <td><?= Html::encode($model->id_novedad) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_tipo_novedad') ?></th>
                    <td><?= Html::encode($model->tipoNovedad->novedad) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Documento') ?></th>
                    <td><?= Html::encode($model->documento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Operario') ?></th>
                    <td><?= Html::encode($model->operario->nombrecompleto) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_inicio_permiso') ?></th>
                    <td><?= Html::encode($model->fecha_inicio_permiso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_final_permiso') ?></th>
                    <td><?= Html::encode($model->fecha_final_permiso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'hora_inicio_permiso') ?></th>
                    <td><?= Html::encode($model->hora_inicio_permiso) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'hora_final_permiso') ?></th>
                    <td><?= Html::encode($model->hora_final_permiso) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_registro') ?></th>
                    <td><?= Html::encode($model->fecha_registro) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'usuario') ?></th>
                    <td><?= Html::encode($model->usuario) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'autorizado') ?></th>
                    <td><?= Html::encode($model->estadoAutorizado) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cerrado') ?></th>
                    <td colspan="4"><?= Html::encode($model->procesoCerrado) ?></td>
                 
                </tr>
                 <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'nro_novedad') ?>:</th>
                    <td><?= Html::encode($model->nro_novedad) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?>:</th>
                    <td colspan="6"><?= Html::encode($model->observacion) ?></td>
                    
                 
                </tr>
                
            </table>
        </div>
    </div>
</div>    