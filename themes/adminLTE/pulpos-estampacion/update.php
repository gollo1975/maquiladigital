<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PulposEstampacion */

$this->title = 'Actualización: ' . $model->descripcion;
$this->params['breadcrumbs'][] = ['label' => 'Pulpos Estampacions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_pulpo, 'url' => ['update', 'id' => $model->id_pulpo]];
$this->params['breadcrumbs'][] = 'Actualización';
?>
<div class="pulpos-estampacion-update">

  <!--  <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
