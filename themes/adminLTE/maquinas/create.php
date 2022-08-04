<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Maquinas */

$this->title = 'Nueva Maquina';
$this->params['breadcrumbs'][] = ['label' => 'Maquinas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="maquinas-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
