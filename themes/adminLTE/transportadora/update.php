<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Transportadora */

$this->title = 'Actualizar: ' . $model->razon_social;
$this->params['breadcrumbs'][] = ['label' => 'Transportadoras', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_transportadora, 'url' => ['update', 'id' => $model->id_transportadora]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="transportadora-update">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form_editar', [
        'model' => $model,
    ]) ?>

</div>
