<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\EficienciaModuloDiario */

$this->title = 'Nuevo dia';
$this->params['breadcrumbs'][] = ['label' => 'Eficiencia Modulo Diarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eficiencia-modulo-diario-create">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
