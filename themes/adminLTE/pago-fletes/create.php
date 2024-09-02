<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PagoFletes */

$this->title = 'Pago de fletes';
$this->params['breadcrumbs'][] = ['label' => 'Pago Fletes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pago-fletes-create">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
