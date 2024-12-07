<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ConceptoDocumentoSoporte */

$this->title = 'Actualizar: ' . $model->concepto;
$this->params['breadcrumbs'][] = ['label' => 'Concepto Documento Soportes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_concepto, 'url' => ['view', 'id' => $model->id_concepto]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="concepto-documento-soporte-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
