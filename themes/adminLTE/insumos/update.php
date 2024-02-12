<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Ordenproducciontipo */

$this->title = 'Editar insumo ';
$this->params['breadcrumbs'][] = ['label' => 'Insumos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_insumos, 'url' => ['view', 'id' => $model->id_insumos]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ordenproducciontipo-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_formeditar', [
        'model' => $model,
    ]) ?>

</div>