<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Prendatipo */

$this->title = 'Nuevo Prenda';
$this->params['breadcrumbs'][] = ['label' => 'Prendas tipo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prendatipo-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'tallasM' => $tallasM,
        'tallasH' => $tallasH,
    ]) ?>

</div>
