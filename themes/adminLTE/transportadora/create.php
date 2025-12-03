<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Transportadora */

$this->title = 'Nueva';
$this->params['breadcrumbs'][] = ['label' => 'Transportadoras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transportadora-create">

  <!--  <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
