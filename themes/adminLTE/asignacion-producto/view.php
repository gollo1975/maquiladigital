<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AsignacionProducto */

$this->title = $model->id_asignacion;
$this->params['breadcrumbs'][] = ['label' => 'Asignacion Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="asignacion-producto-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id_asignacion], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id_asignacion], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_asignacion',
            'idcliente',
            'documento',
            'razonzocial',
            'fecha_asignacion',
            'fecha_registro',
            'unidades',
            'idtipo',
            'orden_produccion',
            'autorizado',
            'usuario',
            'total_orden',
        ],
    ]) ?>

</div>
