<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ProcesoProduccion */

$this->title = 'Nuevo operación';
$this->params['breadcrumbs'][] = ['label' => 'Operacion prendas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="proceso-produccion-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
