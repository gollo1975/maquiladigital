<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Color */

$this->title = 'Nuevo Color';
$this->params['breadcrumbs'][] = ['label' => 'Colores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="color-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
