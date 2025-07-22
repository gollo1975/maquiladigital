<?php

use yii\helpers\Html;

/* @var $this yii\web\View */ 
/* @var $model app\models\PagoAdicionalPermanente */

$this->title = 'Ingresos/Deducciones: ' . $model->id_detalle;
$this->params['breadcrumbs'][] = ['label' => 'Ingresos y deducciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_detalle, 'url' => ['view', 'id' =>$id, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>

<div class="pago-adicional-fecha-updatevista">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    
 <?php  if($model->suma_resta == 1) {
          echo $this->render('_formadicion', [
            'model' => $model,
            'fecha_inicio' => $fecha_inicio,
            'id'=>$id, 
            'fecha_corte' => $fecha_corte,
           ]);
        }else{         
           echo $this->render('_formdescuento', [
             'model' => $model,
            'fecha_inicio' => $fecha_inicio,
            'id'=>$id, 
            'fecha_corte' => $fecha_corte,
           ]);  
        }
         ?>

</div>
