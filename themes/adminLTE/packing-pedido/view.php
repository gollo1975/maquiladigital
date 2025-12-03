<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\ComprobanteEgreso */

$this->title = 'PACKING';
$this->params['breadcrumbs'][] = ['label' => 'packing despacho', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_despacho;
?>
<div class="packing-pedido-view">
    <div class="btn-group btn-sm" role="group">
        
         <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']);
                
        if($model->autorizado == 0 && $model->numero_packing == 0){
             echo  Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->id_packing], ['class' => 'btn btn-default btn-xs']);
        }else{
            if($model->autorizado == 1 && $model->numero_packing == 0){
               echo  Html::a('<span class="glyphicon glyphicon-ok"></span> Desautorizar', ['autorizado', 'id' => $model->id_packing], ['class' => 'btn btn-default btn-xs']);?> 
              <?= Html::a('<span class="glyphicon glyphicon-remove"></span> Cerrar packing', ['cerrar_packing_pedido', 'id' => $model->id_packing],['class' => 'btn btn-success btn-xs',
                     'data' => ['confirm' => 'Esta seguro de cerrar el PACKING del cliente  '. $model->pedido->cliente->nombrecorto.'. Debe de subir la guia del proveedor para el despacho.', 'method' => 'post']]);?>
               <?= Html::a('<span class="glyphicon glyphicon-list"></span> Subir guia masivo',
                                 ['packing-pedido/subir_guia_proveedor', 'id' => $model->id_packing],
                                   ['title' => 'Permite subir la guia del proveedor al PACKING',
                                    'data-toggle'=>'modal',
                                    'data-target'=>'#modalsubirguiaproveedor',
                                    'class' => 'btn btn-info btn-xs'
                                   ])    
                ?> 
                 <?= Html::a('<span class="glyphicon glyphicon-home"></span> Transportadora',
                                 ['packing-pedido/adicionar_transportadora', 'id' => $model->id_packing],
                                   ['title' => 'Permite subir la transportadora al packing',
                                    'data-toggle'=>'modal',
                                    'data-target'=>'#modaladicionartransportadora',
                                    'class' => 'btn btn-warning btn-xs',
                                    'data-backdrop' => 'static',
                                    'data-keyboard' => 'false'
                                   ]);?> 
                    <div class="modal remote fade" id="modaladicionartransportadora" data-backdrop="static">
                        <div class="modal-dialog modal-lg" style ="width: 500px;">    
                            <div class="modal-content"></div>
                        </div>
                    </div> 
                    <div class="modal remote fade" id="modalsubirguiaproveedor" data-backdrop="static">
                        <div class="modal-dialog modal-lg" style ="width: 500px;" >    
                            <div class="modal-content"></div>
                        </div>
                    <div class="modal remote fade" id="modaladicionartransportadora" data-backdrop="static">
                        <div class="modal-dialog modal-lg" style ="width: 500px;">    
                            <div class="modal-content"></div>
                        </div>
                    </div> 
                </div>
            <?php }else{
                 echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimir_packing', 'id' => $model->id_packing], ['class' => 'btn btn-default btn-xs']);
                 
            }
        } ?> 
    </div>    
    
    <div class="panel panel-success">
        <div class="panel-heading">
            <h5><?= Html::encode($this->title) ?></h5>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_packing') ?></th>
                    <td><?= Html::encode($model->id_despacho) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero_packing') ?></th>
                    <td align="right"><?= Html::encode($model->numero_packing) ?></td>
                </tr>  
                 <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idcliente') ?></th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cantidad_despachadas') ?></th>
                    <td align="right"><?= Html::encode(''.number_format($model->cantidad_despachadas,0)) ?></td>
                   
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_transportadora') ?></th>
                    <td><?= Html::encode($model->transportadora->razon_social ?? 'NO FOUNT') ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_hora_registro') ?></th>
                    <td><?= Html::encode($model->fecha_hora_registro) ?></td>
                </tr>
              
            </table>
        </div>
    </div>
    <!--COMIENZA EL FORMULARIO-->
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
        'fieldConfig' => [
            'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
            'labelOptions' => ['class' => 'col-sm-3 control-label'],
            'options' => []
        ],
        ]);
    ?>
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#listadoreferencia" aria-controls="listadoreferencia" role="tab" data-toggle="tab">Listado de referencias <span class="badge"><?= count($vectorDetalle) ?></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="listadoproducto">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style="font-size: 85%;">
                                        <th scope="col" style='background-color:#B9D5CE;'>Código</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Talla</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Color</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>No caja</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cantidades</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Guia</th>
                                     
                                     
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($vectorDetalle as $val):?>
                                        <tr style="font-size: 85%;">
                                            <td><?= $val->inventario->codigo_producto ?? 'NOT FOUND'?></td>
                                            <td><?= $val->inventario->nombre_producto ?? 'NOT FOUND' ?></td>
                                            <td><?= $val->talla->talla ?? 'NOT FOUND' ?></td>
                                            <td><?= $val->colores->color ?? 'NOT FOUND' ?></td>
                                            <td><?= $val->numero_caja ?></td>
                                            <td style="text-align: right"><?= $val->cantidad_despachada ?></td>
                                            <td><?= $val->numero_guia ?></td> 
                                        </tr>        
                                    <?php endforeach;?>
                                </tbody>                                    

                            </table>
                        </div>
                   </div>     
                </div>
            </div>
            <!--TERMINA TABS-->
        </div> 
    </div><!--TERMINA DEL DIV-->    
    <?php ActiveForm::end(); ?>
   
</div>    