<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\ComprobanteEgreso */

$this->title = 'Orden de fabricacion';
$this->params['breadcrumbs'][] = ['label' => 'Fabricacion de referencias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_orden_fabricacion;
?>
<div class="pedido-cliente-view">

    <p>
        <div class="btn-group btn-sm" role="group">
            <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']);
            if ($model->autorizada == 0 && $model->numero_orden == 0) { 
                echo Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->id_orden_fabricacion], ['class' => 'btn btn-default btn-sm']); }
            else {
                if ($model->autorizada == 1 && $model->numero_orden == 0) {
                    echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->id_orden_fabricacion], ['class' => 'btn btn-default btn-sm']);
                    echo Html::a('<span class="glyphicon glyphicon-remove"></span> Cerrar orden', ['cerrar_orden', 'id' => $model->id_orden_fabricacion],['class' => 'btn btn-info btn-sm',
                         'data' => ['confirm' => 'Esta seguro que desea cerrar la orden de fabricacion del  cliente ('.$model->cliente->nombrecorto.')', 'method' => 'post']]);
                }else{    
                    echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir orden', ['/orden-fabricacion/imprimir_orden', 'id' => $model->id_orden_fabricacion],['class' => 'btn btn-default btn-sm']);
                }    
            }?>
        </div>    
    </p>
    <div class="panel panel-success">
        <div class="panel-heading">
            <h5><?= Html::encode($this->title) ?></h5>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Id') ?>:</th>
                    <td><?= Html::encode($model->id_orden_fabricacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero_orden') ?></th>
                    <td><?= Html::encode($model->numero_orden) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idcliente') ?></th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cantidades') ?></th>
                    <td align="right"><?= Html::encode(''.number_format($model->cantidades,0)) ?></td>
                   
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_fabricacion') ?></th>
                    <td><?= Html::encode($model->fecha_fabricacion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_hora_registro') ?>:</th>
                    <td><?= Html::encode($model->fecha_hora_registro) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'user_name') ?></th>
                    <td ><?= Html::encode($model->user_name) ?>%</td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_pedido') ?>:</th>
                    <td align="right"><?= Html::encode(''.number_format($model->pedido->numero_pedido,0)) ?></td>
                  
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'codigo_producto') ?>:</th>
                    <td style="text-align: right"><?= Html::encode($model->codigo_producto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Referencia') ?>:</th>
                    <td><?= Html::encode($model->referencia->referencia) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'orden_cerrada') ?>:</th>
                    <td ><?= Html::encode($model->ordenCerrada) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'autorizada') ?>:</th>
                    <td ><?= Html::encode($model->autorizadoOrden) ?></td>
                </tr>
              
            </table>
        </div>
    </div>
     <?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
    ]);?>
   
    <!--INICIOS DE TABS-->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#listadotallas" aria-controls="listadotallas" role="tab" data-toggle="tab">Listado de tallas <span class="badge"><?= count($conTallas) ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="listadotallas">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style="font-size: 90%;">
                                        <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Numero de orden</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad vendidas</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad real</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($conTallas as $val):  ?>
                                       <tr style="font-size: 90%;">
                                            <td><?= $val->codigoTalla->talla->talla ?></td>
                                            <td><?= $val->ordenFabricacion->numero_orden ?></td>
                                            <td style="text-align: right"><?= ''. number_format($val->cantidad_vendida,0)?></td>
                                            <td style="text-align: right"><?= ''. number_format($val->cantidad_real,0)?></td>
                                            <?php if ($model->autorizada == 0){?>
                                               
                                                
                                            <?php }?>        
                                           
                                       </tr>  
                                    <?php endforeach;?>   
                                </<body>
                            </table>
                        </div>
                        <div class="panel-footer text-right"> 
                            <?php 
                            if($model->autorizada == 0){
                                echo Html::a('<span class="glyphicon glyphicon-plus"></span> Cargar tallas', ['orden-fabricacion/cargar_tallas_pedido', 'id' => $model->id_orden_fabricacion, 'id_referencia' => $model->id_referencia], ['class' => 'btn btn-success btn-sm']);
                            }?>
                        </div>     
                    </div>    
                </div>
            </div> 
            <!--TERMINA TABS DE OPERACIONES-->
        </div>
    </div>
     <?php ActiveForm::end(); ?>
</div>

