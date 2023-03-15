<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PagoBanco */

$this->title = 'Nuevo pago';
$this->params['breadcrumbs'][] = ['label' => 'Pago Bancos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pago-banco-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
