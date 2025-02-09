<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Ordenproducciondetalle;
use yii\helpers\Url;
use yii\web\Session;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;

/* @var $this yii\web\View */
/* @var $model app\models\Facturaventa */

$this->title = 'Factura de Venta';
$this->params['breadcrumbs'][] = ['label' => 'Facturas de ventas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->idfactura;
$view = 'facturaventa';
 $detalle = app\models\Notacreditodetalle::find()->where(['=','idfactura', $model->idfactura])->one();
?>
<div class="facturaventa-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['index', 'id' => $model->idfactura], ['class' => 'btn btn-primary btn-xs']) ?>
        <?php if ($model->autorizado == 0 && $model->nrofactura == 0) { ?>
            <?= Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizado', 'id' => $model->idfactura, 'token' => $token], ['class' => 'btn btn-default btn-xs']); 
        }else {
            if ($model->autorizado == 1 && $model->nrofactura == 0){ 
                echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizado', 'id' => $model->idfactura, 'token' => $token], ['class' => 'btn btn-default btn-xs']);
                echo Html::a('<span class="glyphicon glyphicon-send"></span>  Generar consecutivo', ['generarnro', 'id' => $model->idfactura, 'token' => $token],['class' => 'btn btn-info btn-xs',
                        'data' => ['confirm' => 'Esta seguro de GENERAR el consecutivo a la factura electronica.', 'method' => 'post']]);
                echo Html::a('<span class="glyphicon glyphicon-print"></span> Visualizar factura', ['imprimir', 'id' => $model->idfactura], ['class' => 'btn btn-default btn-xs']);            
            }else{
                 echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir factura', ['imprimir', 'id' => $model->idfactura], ['class' => 'btn btn-default btn-xs']);            
                if($model->fecha_envio_begranda == ''){
                    echo Html::a('<span class="glyphicon glyphicon-send"></span>  Enviar factura a la Dian', ['enviar_documento_dian', 'id_factura' => $model->idfactura, 'token' => $token],['class' => 'btn btn-success btn-xs',  'id' => 'my_button', 'onclick' => '$("#my_button").attr("disabled", "disabled")' ,
                    'data' => ['confirm' => 'Esta seguro de enviar la Factura de venta No  '. $model->nrofactura. ' a la DIAN', 'method' => 'post']]);
                }else{
                    $disabled = $model->fecha_envio_begranda !== '' ? true : false;?>
                    <?= Html::a('<span class="glyphicon glyphicon-send"></span> Enviar factura a la Dian', ['desactivado','id' => $model->idfactura, 'token' => $token],['class' => 'btn btn-success btn-xs disabled', 'id' => 'my_button']);?>
                    <?php if(!$detalle){?>
                        <?= Html::a('<span class="glyphicon glyphicon-send"></span>  Reenviar factura a la dian', ['reenviar_documento_dian', 'id_factura' => $model->idfactura, 'token' => $token],['class' => 'btn btn-warning btn-xs', 'id' => 'my_button', 'onclick' => '$("#my_button").attr("disabled", "disabled")',
                        'data' => ['confirm' => 'Esta seguro de REENVIAR la Factura de venta No  '. $model->nrofactura. ' a la DIAN', 'method' => 'post']]);
                    }
                   
                }
               
                if(!$detalle){
                    if($model->reenviar_factura == 1){?>
                        <?= Html::a('<span class="glyphicon glyphicon-send"></span>  Reenviar factura a la dian', ['reenviar_documento_dian', 'id_factura' => $model->idfactura, 'token' => $token],['class' => 'btn btn-warning btn-xs', 'id' => 'my_button', 'onclick' => '$("#my_button").attr("disabled", "disabled")',
                        'data' => ['confirm' => 'Esta seguro de REENVIAR la Factura de venta No  '. $model->nrofactura. ' a la DIAN', 'method' => 'post']]);
                    }
                } ?>
                <?= Html::a('<span class="glyphicon glyphicon-folder-open"></span> Archivos', ['archivodir/index','numero' => 1, 'codigo' => $model->idfactura,'view' => $view, 'token' => $token], ['class' => 'btn btn-default btn-xs']);                                                         
            }
        }
        ?>
    </p>
    <?php
    if ($mensaje != ""){
        ?> <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo $mensaje ?>
        </div> <?php
    }
    ?>
    <div class="panel panel-success">
        <div class="panel-heading">
            <h5><?= Html::encode($this->title) ?></h5>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style='font-size: 85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idfactura') ?>:</th>
                    <td><?= Html::encode($model->idfactura) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Cliente') ?>:</th>
                    <td><?= Html::encode($model->cliente->nombrecorto) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'idordenproduccion') ?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                </tr>
                <tr style='font-size: 85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'nrofactura') ?>:</th>
                    <td><?= Html::encode($model->nrofactura) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'porcentajeiva') ?>:</th>
                    <td><?= Html::encode($model->porcentajeiva) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'subtotal') ?>:</th>
                    <td style="text-align: right"><?= Html::encode('$ '.number_format($model->subtotal,0)) ?></td>
                </tr>
                <tr style='font-size: 85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_inicio') ?>:</th>
                    <td><?= Html::encode($model->fecha_inicio) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'porcentajefuente') ?>:</th>
                    <td><?= Html::encode($model->porcentajefuente) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'impuestoiva') ?>: +</th>
                    <td style="text-align: right"><?= Html::encode('$ '.number_format($model->impuestoiva,0)) ?></td>
                </tr>
                <tr style='font-size: 85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'fecha_vencimiento') ?>:</th>
                    <td><?= Html::encode($model->fecha_vencimiento) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'porcentajereteiva') ?>:</th>
                    <td><?= Html::encode($model->porcentajereteiva) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'retencioniva') ?>: -</th>
                    <td style="text-align: right"><?= Html::encode('$ '.number_format($model->retencioniva,0)) ?></td>
                </tr>
                <tr style='font-size: 85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'plazopago') ?>:</th>
                    <td><?= Html::encode($model->plazopago) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'usuariosistema') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'retencionfuente') ?>: -</th>
                    <td style="text-align: right"><?= Html::encode('$ '.number_format($model->retencionfuente,0)) ?></td>
                </tr>
                <tr style='font-size: 85%;'>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'id_forma_pago') ?>:</th>
                    <td><?= Html::encode($model->formaPago->concepto) ?></td>
                    <?php if ($model->libre == 0){ ?>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tipoServicio') ?>:</th>
                        <td><?= Html::encode($model->ordenproduccion->tipo->tipo) ?></td>
                    <?php } else { ?>
                        <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'tipoFactura') ?>:</th>
                        <td><?= Html::encode($model->facturaventatipo->concepto) ?></td>
                    <?php } ?>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'totalpagar') ?>:</th>
                    <td style="text-align: right"><?= Html::encode('$ '.number_format($model->totalpagar,0)) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                      <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'observacion') ?>:</th>
                    <td colspan="8"><?= Html::encode($model->observacion) ?></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="table-responsive">
        <div class="panel panel-success ">
            <div class="panel-heading">
                Items <span class="badge"><?= count($modeldetalles)?></span>
            </div>
            <div class="panel-body">
                 <table class="table table-bordered table-hover">
                    <thead>
                        <tr style="font-size: 85%;">
                        <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Referencia</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Producto</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Cantidad</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Vr. unitario</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Subtotal</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Impuesto</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Retencion</th>
                        <th scope="col" style='background-color:#B9D5CE;'>Total linea</th>
                        <th style='background-color:#B9D5CE;'></th>
                        <th style='background-color:#B9D5CE;'></th>
                        
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($modeldetalles as $val): ?>
                    <tr style='font-size: 85%;'>
                        <td><?= $val->iddetallefactura ?></td>
                        <td><?= $val->codigoproducto ?></td>
                        <?php if($val->idproductodetalle == ''){?>
                            <td><?= $val->conceptoFactura->concepto?></td>
                        <?php  }else{?>    
                            <td><?= $val->productodetalle->prendatipo->prenda.' / '.$val->productodetalle->prendatipo->talla->talla ?></td>
                        <?php  }?>        
                        <td style="text-align:right"><?= $val->cantidad ?></td>
                        <td style="text-align:right"><?= '$'.number_format($val->preciounitario,2) ?></td>
                        <td style="text-align:right"><?= '$'.number_format($val->total,0) ?></td>
                        <td style="text-align:right"><?= '$'.number_format($val->valor_iva,0) ?></td>
                         <td style="text-align:right"><?= '$'.number_format($val->valor_retencion,0) ?></td>
                        <td style="text-align:right"><?= '$'.number_format($val->total_linea,0) ?></td>
                        <?php if ($model->autorizado == 0) { ?>
                        <td style="width: 25px;">
                                <a href="#" data-toggle="modal" data-target="#iddetallefactura2<?= $val->iddetallefactura ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                                <!-- Editar modal detalle -->
                                <div class="modal fade" role="dialog" aria-hidden="true" id="iddetallefactura2<?= $val->iddetallefactura ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                <h4 class="modal-title">Editar cantidad y valor unitario</h4>
                                            </div>
                                            <?= Html::beginForm(Url::toRoute(["facturaventa/editardetalle", 'token' => $token]), "POST") ?>
                                            <div class="modal-body">
                                                <div class="panel panel-success">
                                                    <div class="panel-heading">
                                                        <h4>Registros..</h4>
                                                    </div>
                                                    <div class="panel-body">
                                                        <div class="col-lg-2">
                                                            <label>Cantidad:</label>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <input type="text" name="cantidad" value="<?= $val->cantidad ?>" class="form-control" required>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <label>Valor Unitario:</label>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <input type="text" name="preciounitario" value="<?=  $val->preciounitario ?>" class="form-control" required>
                                                        </div>
                                                        <input type="hidden" name="iddetallefactura" value="<?= $val->iddetallefactura ?>">
                                                        <input type="hidden" name="idfactura" value="<?= $val->idfactura ?>">
                                                        <input type="hidden" name="total" value="<?= $val->total ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                                <button type="submit" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-saved"></span> Guardar</button>
                                            </div>
                                            <?= Html::endForm() ?>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            </td>
                            <td style="width: 25px;">
                                <!-- Eliminar modal detalle -->
                                <a href="#" data-toggle="modal" data-target="#iddetallefactura<?= $val->iddetallefactura ?>"><span class="glyphicon glyphicon-trash"></span></a>
                                <div class="modal fade" role="dialog" aria-hidden="true" id="iddetallefactura<?= $val->iddetallefactura ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                <h4 class="modal-title">Eliminar Detalle</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Realmente desea eliminar el registro Nro: <?= $val->iddetallefactura ?>?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <?= Html::beginForm(Url::toRoute(["facturaventa/eliminardetalle", 'token' => $token]), "POST") ?>
                                                <input type="hidden" name="iddetallefactura" value="<?= $val->iddetallefactura ?>">
                                                <input type="hidden" name="idfactura" value="<?= $model->idfactura ?>">
                                                <button type="button" class="btn btn-warning" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cerrar</button>
                                                <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Eliminar</button>
                                                <?= Html::endForm() ?>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            </td>
                        <?php }else{ ?>
                            <td style='width: 25px;'>
                            </td>
                             <td style='width: 25px;'>
                            </td>
                            
                        <?php } ?>
                    </tr>
                   
                    <?php endforeach; ?>
                     </tbody>
                </table>
            </div>
            <?php if ($model->autorizado == 0) { ?>
                <div class="panel-footer text-right">
                    <?php if ($model->libre == 1){ ?>
                        <!-- Inicio Nuevo Detalle proceso -->
                        <?= Html::a('<span class="glyphicon glyphicon-search"></span> Buscar conceptos',
                            ['/facturaventa/nuevodetallelibre','id' => $model->idfactura, 'token' => $token],
                            [
                                'title' => 'Permite mostrar los conceptos libres',
                                'data-toggle'=>'modal',
                                'data-target'=>'#modaldetallenuevolibre',
                                'class' => 'btn btn-success btn-sm'
                            ])                                    

                        ?>
                        <div class="modal remote fade" id="modaldetallenuevolibre">
                            <div class="modal-dialog modal-lg" style="width: 580px;">
                                <div class="modal-content"></div>
                            </div>
                        </div>
                        <!-- Fin Nuevo Detalle proceso -->
                    <?php  }else{ ?>
                        <?= Html::a('<span class="glyphicon glyphicon-search"></span> Buscar items', ['facturaventa/nuevodetalles', 'idfactura' => $model->idfactura,'idordenproduccion' => $model->idordenproduccion, 'token' => $token], ['class' => 'btn btn-success btn-sm']) ?>
                    <?php } ?>                    
                </div>
            <?php } ?>
        </div>
    </div>
</div>


