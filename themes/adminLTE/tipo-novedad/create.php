<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TipoNovedad */

$this->title = 'Nueva novedad';
$this->params['breadcrumbs'][] = ['label' => 'Tipo Novedades', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipo-novedad-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
