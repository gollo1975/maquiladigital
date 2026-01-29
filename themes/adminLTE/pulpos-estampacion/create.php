<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PulposEstampacion */

$this->title = 'Nuevo pulpo';
$this->params['breadcrumbs'][] = ['label' => 'Pulpos Estampacions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pulpos-estampacion-create">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
