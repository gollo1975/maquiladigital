<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AgentesComerciales */

$this->title = 'Nuevo';
$this->params['breadcrumbs'][] = ['label' => 'Agentes Comerciales', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="agentes-comerciales-create">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'sw' => $sw,
    ]) ?>

</div>
