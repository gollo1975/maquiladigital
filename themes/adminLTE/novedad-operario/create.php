<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NovedadOperario */

$this->title = 'Nueva novedad';
$this->params['breadcrumbs'][] = ['label' => 'Novedad Operarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="novedad-operario-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
