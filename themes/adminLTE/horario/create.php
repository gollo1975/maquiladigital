<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CompraTipo */

$this->title = 'Nuevo Horario';
$this->params['breadcrumbs'][] = ['label' => 'Horarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="horario-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
       
    ]) ?>

</div>
