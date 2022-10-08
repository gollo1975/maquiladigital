<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AsignacionProducto */

$this->title = 'Actualizar Asignacion';
$this->params['breadcrumbs'][] = ['label' => 'Asignacion Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_asignacion, 'url' => ['update', 'id' => $model->id_asignacion]];
?>
<div class="asignacion-producto-update">

  <!--  <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
