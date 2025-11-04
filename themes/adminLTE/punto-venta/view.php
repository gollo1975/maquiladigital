<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Municipio */

$this->title = 'PUNTOS DE VENTAS (DETALLE)';
$this->params['breadcrumbs'][] = ['label' => 'Puntos de venta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_punto;
?>

<div class="punto-venta-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->id_punto], ['class' => 'btn btn-primary btn-sm']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_punto], ['class' => 'btn btn-success btn-sm']) ?>
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            PUNTO DE VENTA
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
               <tr style ='font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_punto') ?>:</th>
                    <td><?= Html::encode($model->id_punto) ?></td>                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'nombre_punto') ?>:</th>
                    <td><?= Html::encode($model->nombre_punto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'direccion_punto') ?>:</th>
                    <td><?= Html::encode($model->direccion_punto) ?></td>                    
              </tr>
                <tr style ='font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'celular') ?>:</th>
                    <td><?= Html::encode($model->celular) ?></td>                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'telefono') ?>:</th>
                    <td><?= Html::encode($model->telefono) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'email') ?>:</th>
                    <td><?= Html::encode($model->email) ?></td>                    
                </tr>  
                 <tr style ='font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'user_name') ?>:</th>
                    <td><?= Html::encode($model->user_name) ?></td>                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_inicio') ?>:</th>
                    <td><?= Html::encode($model->fecha_inicio) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'administrador') ?>:</th>
                    <td><?= Html::encode($model->administrador) ?></td>                    
                </tr>  
            </table>
        </div>
    </div>

</div>