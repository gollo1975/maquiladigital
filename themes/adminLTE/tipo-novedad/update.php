<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TipoNovedad */

$this->title = 'Editar novedad';
$this->params['breadcrumbs'][] = ['label' => 'Novedades', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_tipo_novedad, 'url' => ['index', 'id' => $model->id_tipo_novedad]];
?>
<div class="tipo-novedad-update">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
