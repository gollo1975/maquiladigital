
<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\Session;
use yii\db\ActiveQuery;

/* @var $this yii\web\View */
/* @var $model app\models\Producto */
$this->title = 'Pago de fletes';
$this->params['breadcrumbs'][] = ['label' => 'Pago fletes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_pago;
?>
<div class="pago-fletes-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
        <p>
            <div class="btn-group btn-sm" role="group">
                <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']);
                if ($model->autorizado == 0 && $model->numero_pago == 0) { 
                    echo Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->id_pago], ['class' => 'btn btn-default btn-sm']); 
                }else {
                    if ($model->autorizado == 1 && $model->numero_pago == 0) {
                        echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->id_pago], ['class' => 'btn btn-default btn-sm']);
                        echo Html::a('<span class="glyphicon glyphicon-remove"></span> Cerrar pago', ['cerrar_pago', 'id' => $model->id_pago],['class' => 'btn btn-info btn-sm',
                             'data' => ['confirm' => 'Esta seguro que desea cerrar el pago al proveedor ('.$model->proveedor->nombrecorto.')', 'method' => 'post']]);
                    }else{
                        
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
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_pago') ?></th>
                    <td><?= Html::encode($model->id_pago) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idproveedor') ?></th>
                    <td><?= Html::encode($model->proveedor->nombrecorto) ?></td>
                   <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero_pago') ?></th>
                   <td style="text-align: right"><?= Html::encode($model->numero_pago) ?></td>  
                </tr>   
                 <tr style= 'font-size:85%;'>
                    
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_pago') ?></th>
                    <td><?= Html::encode($model->fecha_pago) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_registro') ?></th>
                    <td><?= Html::encode($model->fecha_registro) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_pagado') ?></th>
                    <td style="text-align: right"><?= Html::encode(''.number_format($model->total_pagado,0)) ?></td>
                 </tr>
                 <tr style= 'font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'autorizado')?>:</th>
                    <td tyle="text-align: right"><?= Html::encode($model->registroAutorizado) ?></td> 
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'proceso_cerrado') ?></th>
                    <td tyle="text-align: right"><?= Html::encode($model->procesoCerrado)?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'user_name') ?></th>
                    <td><?= Html::encode($model->user_name) ?></td>
                   
                </tr>    
               
                <tr style= 'font-size:85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?></th>
                    <td colspan="8"><?= Html::encode($model->observacion) ?></td>
                </tr>
                          
            </table>
        </div>
    </div>
      <!-- INICIO DEL TABAS*-->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#listado_fletes" aria-controls="listado_fletes" role="tab" data-toggle="tab">Listado de fletes <span class="badge"><?= count($listado_fletes) ?></span></a></li>
            
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="modulos">
                <div class="table-responsive">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr style='font-size:85%;'>
                                        <th scope="col" style='background-color:#B9D5CE;'>Id</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Numero despacho</th>                        
                                        <th scope="col" style='background-color:#B9D5CE;'>Fecha despacho</th> 
                                        <th scope="col" style='background-color:#B9D5CE;'>Municipio origen</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Municipio destino</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Valor flete</th>  
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($listado_fletes as $modulo):?>
                                        <tr style='font-size:95%;'>
                                            <td><?= $modulo->id_despacho ?></td>
                                            <td><?= $modulo->despacho->numero_despacho ?></td>
                                            <td><?= $modulo->despacho->fecha_despacho ?></td>
                                            <td><?= $modulo->despacho->municipio_origen ?></td>
                                            <td><?= $modulo->despacho->municipio_destino ?></td>
                                             <td style="text-align: right"><?= ''.number_format($modulo->valor_flete,0) ?></td>
                                            <?php if($model->autorizado == 0){?>
                                                <td style="width: 25px; height: 25px;">
                                                 <?=
                                                    Html::a('<span class="glyphicon glyphicon-trash"></span> ', ['eliminar', 'id_detalle' => $modulo->id, 'id' => $model->id_pago], [
                                                        'class' => '',
                                                        'data' => [
                                                            'confirm' => 'Esta seguro de eliminar el registro?',
                                                            'method' => 'post',
                                                        ],
                                                    ])
                                                    ?>
                                                </td>  
                                             <?php }else{?>
                                                <td style="width: 25px; height: 25px;">
                                             <?php }?>   
                                        </tr>
                                   <?php endforeach; ?>    
                                </tbody>      
                            </table>
                            <?php if($model->autorizado == 0){?>
                                <div class="panel-footer text-right"> 
                                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Cargar fletes', ['pago-fletes/listar_fletes', 'id' => $model->id_pago, 'id_proveedor' => $model->idproveedor], ['class' => 'btn btn-success btn-sm']) ?>
                                </div> 
                            <?php }?>
                        </div>
                    </div>   
                </div>
            </div>
            <!-- INICIO DE TABAS-->
        </div>
    </div>        
    
</div>

