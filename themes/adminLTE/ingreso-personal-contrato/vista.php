<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\Session;
use yii\db\ActiveQuery;


/* @var $this yii\web\View */
/* @var $model app\models\Licencia */

$this->title = 'Personal al contrato';
$this->params['breadcrumbs'][] = ['label' => 'Ingresos y deducciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id;
?>

 <div class="adicional-pago-fecha-vista">
              <!--<h1><?= Html::encode($this->title) ?></h1>-->
            <p>
                 <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view','id'=>$id, 'fecha_corte' => $fecha_corte, 'fecha_inicio' => $fecha_inicio], ['class' => 'btn btn-primary btn-sm']) ?>
                
            </p>    
    
   
    <div class="panel panel-success">
        <div class="panel-heading">
            Información: Detalle de ingresos
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                  
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'operacion') ?></th>
                        <td><?= Html::encode($model->operacion) ?></td>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_pagar') ?></th>
                        <td><?= Html::encode('$'. number_format($model->total_pagar,0)) ?></td>
                   </tr> 
                  <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'documento') ?></th>
                    <td><?= Html::encode($model->documento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_empleado') ?></th>
                    <td><?= Html::encode($model->empleado->identificacion .'--'. $model->empleado->nombrecorto) ?></td>
                    
                    
                  </tr>   
                  

            </table>
        </div>
    </div>
</div>    
   
