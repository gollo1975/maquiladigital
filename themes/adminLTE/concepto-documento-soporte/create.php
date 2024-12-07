<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ConceptoDocumentoSoporte */

$this->title = 'Nuevo documento';
$this->params['breadcrumbs'][] = ['label' => 'Concepto Documento Soportes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="concepto-documento-soporte-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
