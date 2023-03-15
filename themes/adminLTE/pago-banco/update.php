<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PagoBanco */

$this->title = 'Editar registro';
$this->params['breadcrumbs'][] = ['label' => 'Pago Bancos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_pago_banco, 'url' => ['update', 'id' => $model->id_pago_banco]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="pago-banco-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
