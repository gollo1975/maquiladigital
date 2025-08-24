<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ConceptoSalarios */

$this->title = 'Nuevo Concepto Salarios';
$this->params['breadcrumbs'][] = ['label' => 'Concepto Salarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="concepto-salarios-create">

   <?= $this->render('_form', [
        'model' => $model,
        'sw' => 0,
    ]) ?>

</div>
