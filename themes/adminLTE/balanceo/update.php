<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Balanceo */

$this->title = 'Editar Modulo ';
$this->params['breadcrumbs'][] = ['label' => 'Balanceos', 'url' => ['index', 'id' => $id]];
$this->params['breadcrumbs'][] = ['label' => $id, 'url' => ['view', 'id' => $model->id_balanceo]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="balanceo-update">

   <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'orden' => $orden,
        'token' => $token,
    ]) ?>

</div>
