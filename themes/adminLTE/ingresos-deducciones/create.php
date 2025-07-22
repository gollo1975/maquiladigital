<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\IngresosDeducciones */

$this->title = 'Nuevo';
$this->params['breadcrumbs'][] = ['label' => 'Ingresos Deducciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ingresos-deducciones-create">

    <!--<<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
