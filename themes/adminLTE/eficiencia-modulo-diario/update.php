<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\EficienciaModuloDiario */

$this->title = 'Actualizar';
$this->params['breadcrumbs'][] = ['label' => 'Eficiencia Modulo Diarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_eficiencia, 'url' => ['update', 'id' => $model->id_eficiencia]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="eficiencia-modulo-diario-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
