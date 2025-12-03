<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\ComprobanteEgreso */

$this->title = 'PACKING';
$this->params['breadcrumbs'][] = ['label' => 'despacho-pedidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id_despacho;
?>
<div class="pedido-cliente-view">
    <div class="btn-group btn-sm" role="group">
        <?php if($token == 0){
            echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index'], ['class' => 'btn btn-primary btn-sm']);
        }else{
            echo Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['search_index'], ['class' => 'btn btn-primary btn-sm']);
        }?>
        
        <?php if(!\app\models\PackingPedidoDetalle::find()->where(['id_despacho' => $model->id_despacho])->one()){?>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear cajas',
                    ['/despacho-pedidos/cantidad_cajas','id' => $model->id_despacho,'id_pedido' => $model->id_pedido ,'token' => $token],
                    [
                        'title' => 'Crear la cantidad de cajas que necesita',
                        'data-toggle'=>'modal',
                        'data-target'=>'#modalcantidadcajas'.$model->id_despacho,
                        'class' => 'btn btn-success btn-sm'

                    ])    
                ?>
                <div class="modal remote fade" id="modalcantidadcajas<?= $model->id_despacho ?>" data-backdrop="static">
                  <div class="modal-dialog modal-lg" style ="width: 500px;" >
                       <div class="modal-content"></div>
                  </div>
                </div>
        <?php }?>
        
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
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'cantidad_despachada') ?></th>
                    <td align="right"><?= Html::encode($model->cantidad_despachada) ?></td>
                </tr>  
                 <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idcliente') ?></th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
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
            <li role="presentation" class="active"><a href="#listadoreferencia" aria-controls="listadoreferencia" role="tab" data-toggle="tab">Listado de referencias <span class="badge"><?= count($conDetalleVista) ?></span></a></li>
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
                                    foreach ($conDetalleVista as $val):?>
                                        <tr style="font-size: 85%;">
                                            <td><?= $val->inventario->codigo_producto ?></td>
                                            <td><?= $val->inventario->nombre_producto ?></td>
                                            <td style="text-align: right"><?=  number_format($val->valor_unitario,0) ?></td>
                                            <td style="text-align: right"><?= $val->porcentaje_valor ?></td>
                                            <td style="text-align: right"><?=  number_format($val->valor_descuento,0) ?></td>
                                            <td style="text-align: right"><?= $val->detalle->cantidad ?></td>
                                            <td style="text-align: right"><?= $val->cantidad_despachada ?></td>
                                            <td style="text-align: right"><?=  number_format($val->total_pagar,0) ?></td>
                                            
                                            <td style= 'width: 25px; height: 25px;'>
                                                <a href="<?= Url::toRoute(["despacho-pedidos/ver_tallas_colores_vista", "id" => $model->id_despacho, 'id_detalle' => $val->id_detalle,'codigo' => $val->codigo, 'id_inventario' => $val->id_inventario, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                                            </td>
                                            <?php if($model->proceso_packing == 1){?>
                                                <td style= 'width: 25px; height: 25px;'>
                                                    <a href="<?= Url::toRoute(["despacho-pedidos/ingresar_cantidad_cajas", "id" => $model->id_despacho, 'id_detalle' => $val->id_detalle,'codigo' => $val->codigo, 'id_inventario' => $val->id_inventario, 'token' => $token]) ?>" ><span class="glyphicon glyphicon-home"></span></a>
                                                </td>
                                            <?php }else{?>
                                                <td style= 'width: 25px; height: 25px;'></td>
                                            <?php }?>
                                              
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