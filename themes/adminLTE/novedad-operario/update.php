<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NovedadOperario */

$this->title = 'Editar novedad';
$this->params['breadcrumbs'][] = ['label' => 'Novedades', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_novedad, 'url' => ['update', 'id' => $model->id_novedad]];
?>
<div class="novedad-operario-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
