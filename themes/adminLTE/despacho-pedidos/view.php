<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\ComprobanteEgreso */

$this->title = 'DESPACHO DE PÈDIDOS';
$this->params['breadcrumbs'][] = ['label' => 'despacho-pedidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_despacho;
?>
<div class="pedido-cliente-view">
    <div class="btn-group btn-sm" role="group">
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']);
        if ($model->autorizado == 0 && $model->numero_despacho == 0) { ?>
            <?= Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->id_despacho], ['class' => 'btn btn-default btn-sm']);
        } else {
            if ($model->autorizado == 1 && $model->numero_despacho == 0) {
                echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->id_despacho], ['class' => 'btn btn-default btn-sm']);
                echo Html::a('<span class="glyphicon glyphicon-remove"></span> Cerrar despacho', ['cerrar_despacho', 'id' => $model->id_despacho],['class' => 'btn btn-info btn-sm',
                     'data' => ['confirm' => 'Esta seguro que desea cerrar el despacho al cliente ('.$model->cliente->nombrecorto.'). Tener presente que se actualiza el inventario.', 'method' => 'post']]);
            }else{    
                echo Html::a('<span class="glyphicon glyphicon-print"></span> Pedido', ['/despacho-pedidos/imprimir_pedido', 'id' => $model->id_despacho],['class' => 'btn btn-default btn-sm']);
                echo Html::a('<span class="glyphicon glyphicon-print"></span> Tallas', ['/pedidos/imprimir_tallas', 'id' => $model->id_despacho],['class' => 'btn btn-default btn-sm']);
            }    
        }?>
    </div>    
    
    <div class="panel panel-success">
        <div class="panel-heading">
            <h5><?= Html::encode($this->title) ?></h5>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Id') ?>:</th>
                    <td><?= Html::encode($model->id_despacho) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'numero_despacho') ?></th>
                    <td align="right"><?= Html::encode($model->numero_despacho) ?></td>
                </tr>  
                 <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idcliente') ?></th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'total_despacho') ?>:</th>
                    <td align="right"><?= Html::encode(''.number_format($model->total_despacho,0)) ?></td>
                   
                </tr>
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_despacho') ?>:</th>
                    <td><?= Html::encode($model->fecha_despacho) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_hora_registro') ?>:</th>
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
            <li role="presentation" class="active"><a href="#listadoreferencia" aria-controls="listadoreferencia" role="tab" data-toggle="tab">Listado de referencias <span class="badge"><?= count($conDetalle) ?></span></a></li>
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
                                        <th scope="col" style='background-color:#B9D5CE;'>Vlr unit.</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>%</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Vlr descuento</th>
                                         <th scope="col" style='background-color:#B9D5CE;'>Cant. vendida</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Cant. despachada</th>
                                        <th scope="col" style='background-color:#B9D5CE;'>Subtotal</th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                        <th scope="col" style='background-color:#B9D5CE;'></th>
                                     
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($conDetalle as $val):?>
                                        <tr style="font-size: 85%;">
                                            <td><?= $val->inventario->codigo_producto ?></td>
                                            <td><?= $val->inventario->nombre_producto ?></td>
                                            <td style="text-align: right"><?=  number_format($val->valor_unitario,0) ?></td>
                                            <td style="text-align: right"><?= $val->porcentaje_valor ?></td>
                                            <td style="text-align: right"><?=  number_format($val->valor_descuento,0) ?></td>
                                            <td style="text-align: right"><?= $val->detalle->cantidad ?></td>
                                            <td style="text-align: right"><?= $val->cantidad_despachada ?></td>
                                             
                                            <td style="text-align: right"><?=  number_format($val->total_pagar,0) ?></td>
                                            <?php if ($model->autorizado == 0){?>
                                                    <td style= 'width: 25px; height: 25px;'>
                                                             <a href="<?= Url::toRoute(["despacho-pedidos/ver_tallas_colores", "id" => $model->id_despacho, 'id_detalle' => $val->id_detalle, 'codigo' => $val->codigo]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                                                    </td>
                                                    <td style="width: 10px; height: 10px">	
                                                        <?= Html::a('', ['eliminar_lineas', 'id' => $model->id_pedido, 'id_detalle' => $val->codigo], [
                                                          'class' => 'glyphicon glyphicon-trash',
                                                          'data' => [
                                                              'confirm' => 'Esta seguro de eliminar el registro?',
                                                              'method' => 'post',
                                                          ],
                                                        ]) ?>
                                                    </td>
                                            <?php     
                                            }else{?>
                                                    <td style= 'width: 25px; height: 25px;'>
                                                        <a href="<?= Url::toRoute(["despacho-pedidos/ver_tallas_colores", "id" => $model->id_despacho, 'id_detalle' => $val->id_detalle,'codigo' => $val->codigo]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                                                    </td>
                                                    <td style= 'width: 25px; height: 25px;'></td>
                                                    
                                            <?php }?>        
                                        </tr>        
                                    <?php endforeach;?>
                                </tbody>                                    

                            </table>
                        </div>
                        <div class="panel-footer text-right"> 
                        <?php 
                        if($model->autorizado == 0){
                        
                                    echo Html::a('<span class="glyphicon glyphicon-import"></span> Descargar referencias', ['despacho-pedidos/descargar_referencias', 'id' => $model->id_despacho, 'id_pedido' =>$model->id_pedido], ['class' => 'btn btn-success btn-xs']);
                            
                        }?>
                   </div>     
                    </div>
                   
                </div>
            </div>
            <!--TERMINA TABS-->
        </div> 
    </div><!--TERMINA DEL DIV-->    
    <?php ActiveForm::end(); ?>
   
</div>    