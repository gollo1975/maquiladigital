<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\Session;
use yii\db\ActiveQuery;


/* @var $this yii\web\View */
/* @var $model app\models\Licencia */

$this->title = 'Detalle licencia';
$this->params['breadcrumbs'][] = ['label' => 'Licencias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_licencia_pk;
?>
<div class="licencia-view">

      <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
       <?php if($token == 0){?>
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
             <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id_licencia_pk], ['class' => 'btn btn-success btn-sm']);
       }else{
            echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index_search'], ['class' => 'btn btn-primary btn-sm']);
       }?>       
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle incapacidad
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                  <tr style="font-size: 85%;">
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_licencia_pk') ?>:</th>
                    <td><?= Html::encode($model->id_licencia_pk) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo_licencia') ?>:</th>
                    <td><?= Html::encode($model->codigoLicencia->concepto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_desde') ?>:</th>
                    <td><?= Html::encode($model->fecha_desde) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_hasta') ?>:</th>
                    <td><?= Html::encode($model->fecha_hasta) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'dias_licencia') ?>:</th>
                    <td><?= Html::encode($model->dias_licencia) ?></td>
                   </tr>   
                   <tr style="font-size: 85%;">
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'identificacion') ?>:</th>
                        <td><?= Html::encode($model->identificacion) ?></td>
                           <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_empleado') ?>:</th>
                        <td><?= Html::encode($model->empleado->nombrecorto) ?></td>  
                         <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_grupo_pago') ?>:</th>
                        <td><?= Html::encode($model->grupoPago->grupo_pago) ?></td>
                         <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_aplicacion') ?>:</th>
                        <td><?= Html::encode($model->fecha_aplicacion) ?></td>
                         <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_contrato') ?>:</th>
                        <td><?= Html::encode($model->contrato->id_contrato) ?></td>
                    </tr>  
                    <tr style="font-size: 85%;">
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'afecta_transporte') ?>:</th>
                        <td><?= Html::encode($model->afectatransporte) ?></td>
                           <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cobrar_administradora') ?>:</th>
                        <td><?= Html::encode($model->cobraradministradora) ?></td>  
                         <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'aplicar_adicional') ?>:</th>
                        <td><?= Html::encode($model->aplicaradicional) ?></td>
                         <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'pagar_empleado') ?>:</th>
                        <td><?= Html::encode($model->pagarempleado) ?></td>
                         <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_proceso') ?>:</th>
                        <td><?= Html::encode($model->fecha_proceso) ?></td>
                    </tr>   
                    <tr style="font-size: 85%;">
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'usuariosistema') ?>:</th>
                        <td><?= Html::encode($model->usuariosistema) ?></td>
                           <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?>:</th>
                           <td colspan="8"><?= Html::encode($model->observacion) ?></td>  
                        
                    </tr>   
            </table>
        </div>
    </div>
</div>    
   
