<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\Session;
use yii\db\ActiveQuery;

/* @var $this yii\web\View */
/* @var $model app\models\Producto */
$this->title = 'Despachos / Fletes';
$this->params['breadcrumbs'][] = ['label' => 'Despachos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_despacho;
?>
<div class="credito-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
        <p>
            <div class="btn-group btn-sm" role="group">
                <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']);
                if ($model->autorizado == 0 && $model->numero_despacho == 0) { 
                    echo Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->id_despacho], ['class' => 'btn btn-default btn-sm']); 
                }else {
                    if ($model->autorizado == 1 && $model->numero_despacho == 0) {
                        echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->id_despacho], ['class' => 'btn btn-default btn-sm']);
                        echo Html::a('<span class="glyphicon glyphicon-remove"></span> Cerrar despacho', ['cerrar_despacho', 'id' => $model->id_despacho, 'id_salida' =>$model->id_salida],['class' => 'btn btn-info btn-sm',
                             'data' => ['confirm' => 'Esta seguro que desea cerrar el despacho al proveedor ('.$model->nombre_proveedor.')', 'method' => 'post']]);
                    }else{    
                        echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir despacho', ['/despachos/imprimir_despacho', 'id' => $model->id_despacho],['class' => 'btn btn-default btn-sm']);
                    }    
                }?>
            </div>    
        </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            Detalle del despacho
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style= 'font-size:85%;'>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_despacho') ?></th>
                    <td><?= Html::encode($model->id_despacho) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_salida') ?></th>
                    <td><?= Html::encode($model->id_salida) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idproveedor') ?></th>
                    <td><?= Html::encode($model->nombre_proveedor) ?></td>
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo_producto') ?></th>
                   <td style="text-align: right"><?= Html::encode($model->codigo_producto) ?></td>  
                </tr>   
                 <tr style= 'font-size:85%;'>
                    
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'municipio_origen') ?></th>
                     <td><?= Html::encode($model->municipio_origen) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'municipio_destino') ?></th>
                    <td><?= Html::encode($model->municipio_destino) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_entrada_tipo') ?></th>
                     <td><?= Html::encode($model->tipoEntrada->concepto) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'valor_flete') ?></th>
                     <td tyle="text-align: right"><?= Html::encode(''.number_format($model->valor_flete,0)) ?></td>
                 </tr>
                 <tr style= 'font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_tulas')?>:</th>
                    <td tyle="text-align: right"><?= Html::encode($model->total_tulas) ?></td> 
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tulas_reales') ?></th>
                    <td tyle="text-align: right"><?= Html::encode($model->tulas_reales)?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_despacho') ?></th>
                    <td><?= Html::encode($model->fecha_despacho) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_registro') ?></th>
                     <td><?= Html::encode($model->fecha_registro) ?></td>
                </tr>    
                <tr style= 'font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'user_name') ?></th>
                     <td><?= Html::encode($model->user_name) ?></td> 
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'autorizado') ?></th>
                     <td><?= Html::encode($model->autorizadoRegistro) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'proceso_cerrado') ?></th>
                     <td><?= Html::encode($model->procesoCerrado) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero_despacho') ?>:</th>
                     <td style="text-align: right"><?= Html::encode($model->numero_despacho) ?></td>
                     
                </tr>
                
                <tr style= 'font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?></th>
                    <td colspan="8"><?= Html::encode($model->observacion) ?></td>
                </tr>
                          
            </table>
        </div>
    </div>
    
</div>

