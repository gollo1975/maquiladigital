<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProcesoDisciplinario */

$this->title = 'Actualizar: ' . $model->empleado->nombrecorto;
$this->params['breadcrumbs'][] = ['label' => 'Proceso Disciplinarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_proceso, 'url' => ['update', 'id' => $model->id_proceso]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="proceso-disciplinario-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
