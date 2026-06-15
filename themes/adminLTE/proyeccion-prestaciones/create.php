<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProyeccionPrestaciones */

$this->title = 'Create Proyeccion Prestaciones';
$this->params['breadcrumbs'][] = ['label' => 'Proyeccion Prestaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="proyeccion-prestaciones-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
