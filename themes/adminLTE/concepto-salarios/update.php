<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ConceptoSalarios */

$this->title = 'Concepto Salarios: ' . $model->codigo_salario;
$this->params['breadcrumbs'][] = ['label' => 'Concepto Salarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codigo_salario, 'url' => ['view', 'id' => $model->codigo_salario]];
$this->params['breadcrumbs'][] = 'Editar conceptos';
?>
<div class="concepto-salarios-update">

     <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'sw' => 1,
    ]) ?>

</div>
