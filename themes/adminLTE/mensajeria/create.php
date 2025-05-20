<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Mensajeria */

$this->title = 'Nuevo';
$this->params['breadcrumbs'][] = ['label' => 'Mensajerias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mensajeria-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
