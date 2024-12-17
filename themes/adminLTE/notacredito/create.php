<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Notacredito */

$this->title = 'Nueva';
$this->params['breadcrumbs'][] = ['label' => 'Notacreditos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notacredito-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'clientes' => $clientes,
        'documentos' => $documentos
    ]) ?>

</div>
