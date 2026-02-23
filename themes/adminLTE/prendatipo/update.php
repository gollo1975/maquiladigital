<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Prendatipo */

$this->title = 'Editar Prenda Tipo: ' . $model->idprendatipo;
$this->params['breadcrumbs'][] = ['label' => 'Prendas tipos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idprendatipo, 'url' => ['view', 'id' => $model->idprendatipo]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="prendatipo-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'tallasM' => $tallasM,
        'tallasH' => $tallasH,
    ]) ?>

</div>
