<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Resolucion */

$this->title = 'Editar Tipo Documento: ' . $model->id_tipo_documento;
$this->params['breadcrumbs'][] = ['label' => 'Tipos de Documentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_tipo_documento, 'url' => ['view', 'id' => $model->id_tipo_documento]];
$this->params['breadcrumbs'][] = 'Editar Tipo Documento';
?>
<div class="tipo documento-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
