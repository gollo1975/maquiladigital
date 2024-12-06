<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Notacredito */

$this->title = 'Actualizar';
$this->params['breadcrumbs'][] = ['label' => 'Notacreditos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idnotacredito, 'url' => ['view', 'id' => $model->idnotacredito]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="notacredito-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'clientes' => $clientes,
        'conceptonotacredito' => $conceptonotacredito,
    ]) ?>

</div>
