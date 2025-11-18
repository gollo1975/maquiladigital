<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AgentesComerciales */

$this->title = 'Actualizar a: ' . $model->nombre_completo;
$this->params['breadcrumbs'][] = ['label' => 'Agentes Comerciales', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_agente, 'url' => ['update', 'id' => $model->id_agente]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="agentes-comerciales-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
         'sw' => 1,
    ]) ?>

</div>
