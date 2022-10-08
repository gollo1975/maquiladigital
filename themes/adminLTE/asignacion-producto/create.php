<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AsignacionProducto */

$this->title = 'Nueva Asignacion';
$this->params['breadcrumbs'][] = ['label' => 'Asignacion Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="asignacion-producto-create">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
