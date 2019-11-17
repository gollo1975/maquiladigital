<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CentroTrabajo */

$this->title = 'Editar Centro de Trabajo: ' . $model->id_centro_trabajo;
$this->params['breadcrumbs'][] = ['label' => 'Centros de Trabajo', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_centro_trabajo, 'url' => ['view', 'id' => $model->id_centro_trabajo]];
$this->params['breadcrumbs'][] = 'Editar Entidad Salud';
?>
<div class="centro-trabajo-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
