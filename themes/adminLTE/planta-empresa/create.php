<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PlantaEmpresa */

$this->title = 'Create Planta Empresa';
$this->params['breadcrumbs'][] = ['label' => 'Planta Empresas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="planta-empresa-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>