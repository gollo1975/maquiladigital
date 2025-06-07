<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MotivoDisciplinario */

$this->title = 'Nuevo';
$this->params['breadcrumbs'][] = ['label' => 'Motivo Disciplinarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="motivo-disciplinario-create">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
