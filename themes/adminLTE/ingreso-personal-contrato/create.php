<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\IngresoPersonalContrato */

$this->title = 'Nuevo corte';
$this->params['breadcrumbs'][] = ['label' => 'Ingreso Personal Contratos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ingreso-personal-contrato-create">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
