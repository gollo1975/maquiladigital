<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Recibocaja */

$this->title = 'Nuevo recibo';
$this->params['breadcrumbs'][] = ['label' => 'Recibos de cajas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recibocaja-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'municipios' => $municipios,
        'tiporecibos' => $tiporecibos,
        'clientes' => $clientes,
        'bancos' => $bancos,
    ]) ?>

</div>
