<?php

use yii\helpers\Html;

/* @var $this yii\web\View */ 
/* @var $model app\models\PagoAdicionalPermanente */

$this->title = 'Personal al contrato: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Ingresos y deducciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' =>$id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>

<div class="ingreso-personal-contrato-updatevista">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
 
         <?= $this->render('_formadicion', [
            'model' => $model,
            'fecha_inicio' => $fecha_inicio,
            'id'=>$id, 
            'fecha_corte' => $fecha_corte,
           ]);
       ?>

</div>
