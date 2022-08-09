<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Mecanico */

$this->title = 'Actualizar';
$this->params['breadcrumbs'][] = ['label' => 'Mecanicos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_mecanico, 'url' => ['update', 'id' => $model->id_mecanico]];
?>
<div class="mecanico-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'token' => $token,
    ]) ?>

</div>
