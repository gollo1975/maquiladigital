<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ServicioMantenimiento */

$this->title = 'Actualizar';
$this->params['breadcrumbs'][] = ['label' => 'Servicio Mantenimientos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_servicio, 'url' => ['update', 'id' => $model->id_servicio]];
?>
<div class="servicio-mantenimiento-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
