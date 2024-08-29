<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Talla */

$this->title = 'Detalle';
$this->params['breadcrumbs'][] = ['label' => 'Tipo de producto', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_tipo_producto;
?>
<div class="tipo-novedad-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
        
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Tipo de productos
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr>
                    <th><?= Html::activeLabel($model, 'Código') ?>:</th>
                    <td><?= Html::encode($model->id_tipo_producto) ?></td>                    
                    <th><?= Html::activeLabel($model, 'concepto') ?>:</th>
                    <td><?= Html::encode($model->concepto) ?></td>
                    <th><?= Html::activeLabel($model, 'estado') ?>:</th>
                    <td><?= Html::encode($model->estadoRegistro) ?></td>                    
                </tr>                                
            </table>
        </div>
    </div>    
</div>

