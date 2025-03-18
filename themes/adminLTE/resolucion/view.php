<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Resolucion */

$this->title = 'Detalle Resolución';
$this->params['breadcrumbs'][] = ['label' => 'Resoluciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->idresolucion;
$view = 'resolucion';
?>
<div class="resolucion-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->idresolucion], ['class' => 'btn btn-primary btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 21, 'codigo' => $model->idresolucion,'view' => $view, 'token' => $token], ['class' => 'btn btn-default btn-sm']) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Resolución
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size:85%; ">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idresolucion') ?>:</th>
                    <td><?= Html::encode($model->idresolucion) ?></td>                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'nroresolucion') ?>:</th>
                    <td><?= Html::encode($model->nroresolucion) ?></td>  
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'activo') ?>:</th>
                    <td><?= Html::encode($model->estado) ?></td>   
                </tr>
                <tr  style="font-size:85%; ">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'inicio_rango') ?>:</th>
                    <td><?= Html::encode($model->inicio_rango) ?></td>                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'final_rango') ?>:</th>
                    <td><?= Html::encode($model->final_rango) ?></td>  
                     <th><?= Html::activeLabel($model, 'id_documento') ?>:</th>
                    <td><?= Html::encode($model->documentoelectronico->nombre_documento) ?></td>  
                </tr>
                <tr  style="font-size:85%; ">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechacreacion') ?>:</th>
                    <td><?= Html::encode($model->fechacreacion) ?></td>                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fechavencimiento') ?>:</th>
                    <td><?= Html::encode($model->fechavencimiento) ?></td>      
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_notificacion') ?>:</th>
                    <td><?= Html::encode($model->fecha_notificacion) ?></td> 
                </tr>
                <tr  style="font-size:85%; ">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigoactividad') ?>:</th>
                    <td><?= Html::encode($model->codigoactividad) ?></td>                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'descripcion') ?>:</th>
                    <td colspan="3"><?= Html::encode($model->descripcion) ?></td>                    
                </tr>
                
            </table>
        </div>
    </div>     
</div>
