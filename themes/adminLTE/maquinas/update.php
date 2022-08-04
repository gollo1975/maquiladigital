<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Maquinas */

$this->title = 'Actualizar';
$this->params['breadcrumbs'][] = ['label' => 'Maquinas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_maquina, 'url' => ['update', 'id' => $model->id_maquina]];
?>
<div class="maquinas-update">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
