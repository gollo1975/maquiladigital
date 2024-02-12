<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\EntradaMateriaPrima */

$this->title = 'Nueva entrada';
$this->params['breadcrumbs'][] = ['label' => 'Entrada Materia Primas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="entrada-materia-prima-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'proveedores' => $proveedores,
    ]) ?>

</div>
