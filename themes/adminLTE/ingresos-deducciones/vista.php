<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\Session;
use yii\db\ActiveQuery;


/* @var $this yii\web\View */
/* @var $model app\models\Licencia */

$this->title = 'Ingresos / deducciones';
$this->params['breadcrumbs'][] = ['label' => 'Ingresos y deducciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_detalle;
?>

 <div class="adicional-pago-fecha-vista">
              <!--<h1><?= Html::encode($this->title) ?></h1>-->
            <p>
                 <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view','id'=>$id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio], ['class' => 'btn btn-primary btn-sm']) ?>
                
            </p>    
    
   
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle de ingresos / deducciones
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                  
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo_salario') ?></th>
                        <td><?= Html::encode($model->codigoSalario->nombre_concepto) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'valor_pagado') ?></th>
                        <td><?= Html::encode('$'. number_format($model->valor_pagado,0)) ?></td>
                   </tr> 
                  <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_empleado') ?></th>
                    <td><?= Html::encode($model->empleado->identificacion .'--'. $model->empleado->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?></th>
                    <td><?= Html::encode($model->observacion) ?></td>
                    
                  </tr>   
                  

            </table>
        </div>
    </div>
</div>    
   
