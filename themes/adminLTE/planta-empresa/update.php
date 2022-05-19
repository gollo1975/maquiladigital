<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PlantaEmpresa */

$this->title = 'Update Planta Empresa: ' . $model->id_planta;
$this->params['breadcrumbs'][] = ['label' => 'Planta Empresas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_planta, 'url' => ['view', 'id' => $model->id_planta]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="planta-empresa-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
